<?php

namespace App\Http\Controllers;

use App\Models\Cheque;
use App\User;
use App\Models\Supplier;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChequeController extends Controller
{
    /**
     * Display cheques list
     */
    public function index(Request $request)
    {
        // Auto-migration for transferred_to_id
        if (!\Illuminate\Support\Facades\Schema::hasColumn('cheques', 'transferred_to_id')) {
            \Illuminate\Support\Facades\Schema::table('cheques', function (\Illuminate\Database\Schema\Blueprint $table) {
                $table->unsignedBigInteger('transferred_to_id')->nullable()->after('created_by');
            });
        }

        // Always ensure the status enum includes 'transferred' (can be run multiple times safely in MySQL)
        try {
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE cheques MODIFY COLUMN status ENUM('pending', 'cleared', 'bounced', 'cancelled', 'transferred') DEFAULT 'pending'");
        } catch (\Exception $e) {
            // Silently fail if already updated or not supported
        }

        $query = Cheque::with(['party', 'creator', 'transferredTo']);

        // Filter by type
        if ($request->has('type') && in_array($request->type, ['received', 'paid'])) {
            $query->where('type', $request->type);
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->has('from_date')) {
            $query->whereDate('cheque_date', '>=', $request->from_date);
        }
        if ($request->has('to_date')) {
            $query->whereDate('cheque_date', '<=', $request->to_date);
        }

        // Filter by overdue
        if ($request->filter == 'overdue') {
            $query->overdue();
        }

        // Filter by clearing today
        if ($request->filter == 'clearing_today') {
            $query->whereDate('clearing_date', today())->where('status', 'pending');
        }

        // Filter by pending received
        if ($request->filter == 'pending_received') {
            $query->where('type', 'received')->where('status', 'pending');
        }

        // Filter by pending paid
        if ($request->filter == 'pending_paid') {
            $query->where('type', 'paid')->where('status', 'pending');
        }

        $cheques = $query->orderBy('cheque_date', 'desc')->paginate(5000);

        // Get summary statistics
        $stats = [
            'pending_received' => Cheque::where('type', 'received')->where('status', 'pending')->sum('amount'),
            'pending_paid' => Cheque::where('type', 'paid')->where('status', 'pending')->sum('amount'),
            'cleared_today' => Cheque::whereDate('clearing_date', today())->where('status', 'pending')->count(),
            'overdue' => Cheque::overdue()->count(),
        ];

        try {
            // Try with status filter first (column may not exist on older DBs)
            if (\Illuminate\Support\Facades\Schema::hasColumn('financial_accounts', 'status')) {
                $financialAccounts = \App\Models\FinancialAccount::where('status', 'active')->get();
            } else {
                $financialAccounts = \App\Models\FinancialAccount::all();
            }
        } catch (\Throwable $e) {
            $financialAccounts = collect();
        }

        return view('backend.cheques.index', compact('cheques', 'stats', 'financialAccounts'));
    }

    /**
     * Show form for creating new cheque
     */
    public function create()
    {
        $customers = User::where('role', 'user')->get();
        $suppliers = Supplier::where('status', 'active')->get();

        return view('backend.cheques.create', compact('customers', 'suppliers'));
    }

    /**
     * Store new cheque
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:received,paid',
            'cheque_number' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'cheque_date' => 'required|date',
            'clearing_date' => 'required|date|after_or_equal:cheque_date',
            'party_type' => 'required|string',
            'party_id' => 'required|integer',
            'bank_name' => 'nullable|string',
            'bank_branch' => 'nullable|string',
            'reference_number' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $validated['created_by'] = Auth::id();
        $validated['status'] = 'pending';

        $cheque = Cheque::create($validated);

        // Record in Ledger immediately (as Pending Cheque)
        if ($cheque->type === 'paid' && $cheque->party_type === 'App\\Models\\Supplier') {
            \App\Models\SupplierLedger::record(
                $cheque->party_id,
                $cheque->cheque_date,
                'credit',
                'payment',
                "Cheque Issued #{$cheque->cheque_number} (Pending) - {$cheque->bank_name}",
                $cheque->amount,
                $cheque->id,
                'cheque',
                ['cheque_no' => $cheque->cheque_number, 'bank_name' => $cheque->bank_name, 'status' => 'pending']
            );
        } elseif ($cheque->type === 'received' && $cheque->party_type === 'App\\User') {
            \App\Models\CustomerLedger::record(
                $cheque->party_id,
                $cheque->cheque_date,
                'credit',
                'payment',
                "Cheque Received #{$cheque->cheque_number} (Pending) - {$cheque->bank_name}",
                $cheque->amount,
                $cheque->id
            );
        }

        // Create calendar task for clearing date
        Task::create([
            'title' => ($cheque->type === 'received' ? 'Cheque to Clear' : 'Cheque Payment Due'),
            'description' => "Cheque #{$cheque->cheque_number} - " . number_format($cheque->amount, 2) . " PKR",
            'start_date' => $cheque->clearing_date,
            'end_date' => $cheque->clearing_date,
            'task_type' => 'cheque',
            'related_type' => 'App\\Models\\Cheque',
            'related_id' => $cheque->id,
            'created_by' => Auth::id(),
            'status' => 'pending',
            'color' => $cheque->type === 'received' ? '#28a745' : '#dc3545',
        ]);

        session()->flash('success', 'Cheque added successfully');
        return redirect()->route('cheques.index');
    }

    /**
     * Display cheque details
     */
    public function show(Cheque $cheque)
    {
        $cheque->load(['party', 'creator']);
        return view('backend.cheques.show', compact('cheque'));
    }

    /**
     * Show form for editing cheque
     */
    public function edit(Cheque $cheque)
    {
        $customers = User::where('role', 'user')->get();
        $suppliers = Supplier::where('status', 'active')->get();

        return view('backend.cheques.edit', compact('cheque', 'customers', 'suppliers'));
    }

    /**
     * Update cheque
     */
    public function update(Request $request, Cheque $cheque)
    {
        $validated = $request->validate([
            'cheque_number' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'cheque_date' => 'required|date',
            'clearing_date' => 'required|date|after_or_equal:cheque_date',
            'bank_name' => 'nullable|string',
            'bank_branch' => 'nullable|string',
            'reference_number' => 'nullable|string',
            'notes' => 'nullable|string',
            'status' => 'nullable|in:pending,cleared,bounced,cancelled,transferred',
        ]);

        $cheque->update($validated);

        session()->flash('success', 'Cheque updated successfully');
        return redirect()->route('cheques.index');
    }

    /**
     * Mark cheque as cleared
     */
    public function markCleared(Request $request, Cheque $cheque)
    {
        $actual_date        = $request->actual_clearing_date ?: date('Y-m-d');
        $financialAccountId = $request->financial_account_id ?: null;

        $cheque->update([
            'status'               => 'cleared',
            'actual_clearing_date' => $actual_date,
        ]);

        // Calculate delay
        $cheque->calculateDelay();

        // Update related task
        Task::where('related_type', 'App\\Models\\Cheque')
            ->where('related_id', $cheque->id)
            ->update(['status' => 'completed']);

        // ── RECEIVED cheque (Customer → You) ────────────────────────────────
        if ($cheque->type === 'received' && $cheque->party_type === 'App\\User') {
            // Update ledger description & date
            \App\Models\CustomerLedger::where('category', 'payment')
                ->where('reference_id', $cheque->id)
                ->update([
                    'description'      => "Cheque #{$cheque->cheque_number} Cleared - {$cheque->bank_name}",
                    'transaction_date' => $actual_date,
                    'financial_account_id' => $financialAccountId,
                ]);
            \App\Models\CustomerLedger::updateBalance($cheque->party_id);

            // Record Cash IN on the selected account
            if ($financialAccountId) {
                $partyName = $cheque->party ? $cheque->party->name : '';
                \App\Models\AccountTransaction::record(
                    $financialAccountId,
                    $cheque->amount,
                    'in',
                    'Cheque',
                    $cheque->id,
                    "Cheque Cleared #{$cheque->cheque_number} - {$partyName} ({$cheque->bank_name})",
                    $actual_date
                );
            }
        }

        // ── PAID cheque (You → Supplier) ────────────────────────────────────
        if ($cheque->type === 'paid' && $cheque->party_type === 'App\\Models\\Supplier') {
            // Update ledger description & date
            \App\Models\SupplierLedger::where('category', 'payment')
                ->where('reference_id', $cheque->id)
                ->update([
                    'description'      => "Cheque #{$cheque->cheque_number} Cleared - {$cheque->bank_name}",
                    'transaction_date' => $actual_date,
                    'payment_details'  => json_encode([
                        'cheque_no'  => $cheque->cheque_number,
                        'bank_name'  => $cheque->bank_name,
                        'status'     => 'cleared',
                    ]),
                ]);
            \App\Models\SupplierLedger::updateBalance($cheque->party_id);

            // Record Cash OUT from the selected account
            if ($financialAccountId) {
                $partyName = $cheque->party ? $cheque->party->name : '';
                \App\Models\AccountTransaction::record(
                    $financialAccountId,
                    $cheque->amount,
                    'out',
                    'Cheque',
                    $cheque->id,
                    "Cheque Paid #{$cheque->cheque_number} - {$partyName} ({$cheque->bank_name})",
                    $actual_date
                );
            }
        }

        // ── TRANSFERRED cheque (Customer cheque → Supplier) ─────────────────
        // A customer's cheque was handed directly to a supplier.
        // On clearing: record both a Cash IN (customer paid) and Cash OUT (supplier paid)
        // through the same account so the net is zero but both legs are visible.
        if ($cheque->status === 'cleared' && $cheque->transferred_to_id && $financialAccountId) {
            // Cash IN – customer's payment received
            $partyName = $cheque->party ? $cheque->party->name : '';
            \App\Models\AccountTransaction::record(
                $financialAccountId,
                $cheque->amount,
                'in',
                'Cheque',
                $cheque->id,
                "Transferred Cheque Cleared #{$cheque->cheque_number} - Received from {$partyName}",
                $actual_date
            );

            // Cash OUT – supplier paid via transferred cheque
            \App\Models\AccountTransaction::record(
                $financialAccountId,
                $cheque->amount,
                'out',
                'Cheque',
                $cheque->id,
                "Transferred Cheque Cleared #{$cheque->cheque_number} - Paid to supplier via endorsement",
                $actual_date
            );
        }

        session()->flash('success', 'Cheque marked as cleared and cash flow updated.');
        return back();
    }

    /**
     * Mark cheque as bounced
     */
    public function markBounced(Request $request, Cheque $cheque)
    {
        $validated = $request->validate([
            'notes' => 'nullable|string',
        ]);

        $cheque->update([
            'status' => 'bounced',
            'notes' => $validated['notes'] ?? null,
        ]);

        // Update related task
        Task::where('related_type', 'App\\Models\\Cheque')
            ->where('related_id', $cheque->id)
            ->update([
                'status' => 'cancelled',
                'description' => $cheque->notes . "\n[BOUNCED] " . ($validated['notes'] ?? '')
            ]);

        session()->flash('warning', 'Cheque marked as bounced');
        return back();
    }

    /**
     * Mark cheque as cancelled
     */
    public function markCancelled(Request $request, Cheque $cheque)
    {
        $cheque->update([
            'status' => 'cancelled',
            'notes' => $request->notes,
        ]);

        // Update related task
        Task::where('related_type', 'App\\Models\\Cheque')
            ->where('related_id', $cheque->id)
            ->update(['status' => 'cancelled']);

        session()->flash('info', 'Cheque cancelled');
        return back();
    }

    /**
     * Get cheques for calendar view
     */
    public function getCalendarCheques(Request $request)
    {
        $start = $request->get('start');
        $end = $request->get('end');

        $cheques = Cheque::whereBetween('clearing_date', [$start, $end])
            ->where('status', 'pending')
            ->with('party')
            ->get();

        $events = [];
        foreach ($cheques as $cheque) {
            $events[] = [
                'id' => $cheque->id,
                'title' => ($cheque->type === 'received' ? '💰' : '💸') . ' ' . $cheque->cheque_number . ' - PKR ' . number_format($cheque->amount, 2),
                'start' => $cheque->clearing_date->format('Y-m-d'),
                'color' => $cheque->type === 'received' ? '#28a745' : '#dc3545',
                'extendedProps' => [
                    'cheque_id' => $cheque->id,
                    'type' => $cheque->type,
                    'party' => $cheque->party->name ?? 'N/A',
                ],
            ];
        }

        return response()->json($events);
    }

    /**
     * Get pending received cheques for selection in ledger
     */
    public function getPendingCustomerCheques()
    {
        // Auto-migration for transferred_to_id (backup check)
        if (!\Illuminate\Support\Facades\Schema::hasColumn('cheques', 'transferred_to_id')) {
            \Illuminate\Support\Facades\Schema::table('cheques', function (\Illuminate\Database\Schema\Blueprint $table) {
                $table->unsignedBigInteger('transferred_to_id')->nullable()->after('created_by');
            });
        }

        // Ensure status enum includes 'transferred'
        try {
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE cheques MODIFY COLUMN status ENUM('pending', 'cleared', 'bounced', 'cancelled', 'transferred') DEFAULT 'pending'");
        } catch (\Exception $e) {}

        $cheques = Cheque::with('party')
            ->where('type', 'received')
            ->where('status', 'pending')
            ->orderBy('cheque_date', 'asc')
            ->get();
            
        return response()->json($cheques);
    }

    /**
     * Delete cheque
     */
    public function destroy(Cheque $cheque)
    {
        // Delete related task
        Task::where('related_type', 'App\\Models\\Cheque')
            ->where('related_id', $cheque->id)
            ->delete();

        $cheque->delete();

        session()->flash('success', 'Cheque deleted successfully');
        return redirect()->route('cheques.index');
    }
}
