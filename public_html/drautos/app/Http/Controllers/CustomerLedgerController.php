<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CustomerLedger;
use App\User;
use Illuminate\Support\Facades\DB;

class CustomerLedgerController extends Controller
{
    public function index(Request $request)
    {
        $query = User::whereIn('role', ['user', 'customer']);
        
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'LIKE', '%' . $request->search . '%')
                  ->orWhere('phone', 'LIKE', '%' . $request->search . '%')
                  ->orWhere('email', 'LIKE', '%' . $request->search . '%');
            });
        }

        if ($request->city) {
            $query->where('city', $request->city);
        }

        $customers = $query->orderBy('name', 'asc')->paginate(5000);
        $cities = User::whereIn('role', ['user', 'customer'])->whereNotNull('city')->distinct()->pluck('city');
        
        return view('backend.customer_ledger.index', compact('customers', 'cities'));
    }

    public function show(User $user, Request $request)
    {
        $query = CustomerLedger::where('user_id', $user->id);

        if ($request->date_from) {
            $query->whereDate('transaction_date', '>=', $request->date_from);
        }
        if ($request->date_to) {
            $query->whereDate('transaction_date', '<=', $request->date_to);
        }

        $ledger = $query->orderBy('transaction_date', 'asc')->get();
        
        // Prepare Graph Data (Professional Performance)
        $graphLabels = [];
        $balanceHistory = [];
        $runningBalance = 0;
        
        foreach ($ledger as $item) {
            $graphLabels[] = date('d M', strtotime($item->transaction_date));
            if ($item->type == 'debit') {
                $runningBalance += $item->amount;
            } else {
                $runningBalance -= $item->amount;
            }
            $balanceHistory[] = $runningBalance;
        }

        $ledger = $query->orderBy('transaction_date', 'desc')->orderBy('id', 'desc')->paginate(5000);
        // The original method signature includes `User $user`, so we should use that.
        // If this method is intended for the authenticated user, the signature should be changed.
        // For now, we keep the passed $user object.
        
        return view('backend.customer_ledger.show', compact('user', 'ledger', 'graphLabels', 'balanceHistory'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'transaction_date' => 'required|date',
            'type' => 'required|in:credit,debit',
            'amount' => 'required|numeric|min:0',
            'description' => 'required|string',
            'category' => 'required|in:manual,payment,order,return',
            'payment_method' => 'nullable|string',
            'payment_details' => 'nullable|array'
        ]);

        DB::beginTransaction();
        try {
            // Auto-fix database schema if columns are missing
            if (!\Illuminate\Support\Facades\Schema::hasColumn('customer_ledgers', 'payment_method')) {
                \Illuminate\Support\Facades\Schema::table('customer_ledgers', function (\Illuminate\Database\Schema\Blueprint $table) {
                    $table->string('payment_method')->nullable()->after('category');
                    $table->text('payment_details')->nullable()->after('payment_method');
                });
            }

            $ledger = CustomerLedger::record(
                $request->user_id,
                $request->transaction_date,
                $request->type,
                $request->category,
                $request->description,
                $request->amount,
                null,
                $request->payment_method,
                $request->payment_details
            );

            // If it's a cheque, create record in Cheque Management and Payment Reminder
            if ($request->payment_method == 'cheque' && isset($request->payment_details['cheque_no'])) {
                $details = $request->payment_details;
                
                // Create Cheque
                $cheque = \App\Models\Cheque::create([
                    'type' => ($request->type == 'credit' ? 'received' : 'issued'), // For customer, credit usually means they paid us (received)
                    'cheque_number' => $details['cheque_no'],
                    'amount' => $request->amount,
                    'cheque_date' => $request->transaction_date,
                    'clearing_date' => $details['clearing_date'] ?? $request->transaction_date,
                    'party_type' => 'App\User',
                    'party_id' => $request->user_id,
                    'bank_name' => $details['bank_name'] ?? 'N/A',
                    'status' => 'pending',
                    'notes' => 'Generated from Customer Ledger. ' . $request->description,
                    'reference_number' => 'CLEDGER-' . $ledger->id,
                    'created_by' => auth()->id()
                ]);

                // Create Task for Calendar
                \App\Models\Task::create([
                    'title' => ($request->type == 'credit' ? '💰 Cheque Received: #' : '💸 Cheque Issued: #') . $details['cheque_no'],
                    'description' => "Customer cheque for amount PKR " . number_format($request->amount, 2) . ". Description: " . $request->description,
                    'start_date' => $details['clearing_date'] ?? $request->transaction_date,
                    'end_date' => $details['clearing_date'] ?? $request->transaction_date,
                    'priority' => 'high',
                    'status' => 'pending',
                    'task_type' => 'cheque',
                    'related_type' => 'App\Models\Cheque',
                    'related_id' => $cheque->id,
                    'created_by' => auth()->id(),
                    'color' => ($request->type == 'credit' ? '#28a745' : '#dc3545') // Green for receipt, Red for payment
                ]);

                // Create Payment Reminder
                \App\Models\PaymentReminder::create([
                    'type' => ($request->type == 'credit' ? 'receivable' : 'payable'),
                    'party_type' => 'App\User',
                    'party_id' => $request->user_id,
                    'reference_number' => 'CHQ-' . $details['cheque_no'],
                    'amount' => $request->amount,
                    'due_date' => $details['clearing_date'] ?? $request->transaction_date,
                    'status' => 'pending',
                    'notes' => 'Customer cheque clearing reminder: ' . $details['cheque_no']
                ]);
            }

            DB::commit();
            return redirect()->back()->with('success', 'Transaction recorded successfully');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'transaction_date' => 'required|date',
            'type' => 'required|in:credit,debit',
            'amount' => 'required|numeric|min:0',
            'description' => 'required|string',
            'category' => 'required|in:manual,payment,order,return'
        ]);

        try {
            $transaction = CustomerLedger::findOrFail($id);
            $userId = $transaction->user_id;
            $transaction->update($validated);
            
            // Recalculate balance
            CustomerLedger::updateBalance($userId);

            return redirect()->back()->with('success', 'Transaction updated successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function generatePDF($userId)
    {
        $user = \App\User::findOrFail($userId);
        $ledger = CustomerLedger::where('user_id', $userId)->orderBy('transaction_date', 'asc')->get();
        $pdf = \PDF::loadView('backend.customer_ledger.pdf', compact('user', 'ledger'));
        return $pdf->download('ledger-' . $user->name . '-' . date('Y-m-d') . '.pdf');
    }

    public function sendWhatsApp(Request $request, $userId)
    {
        $user = \App\User::findOrFail($userId);
        $ledger = CustomerLedger::where('user_id', $userId)->orderBy('transaction_date', 'asc')->get();
        
        // Generate PDF path
        $pdf = \PDF::loadView('backend.customer_ledger.pdf', compact('user', 'ledger'));
        $fileName = 'ledger-' . str_replace(' ', '_', $user->name) . '-' . time() . '.pdf';
        $path = public_path('storage/ledgers/' . $fileName);
        
        if (!file_exists(public_path('storage/ledgers'))) {
            mkdir(public_path('storage/ledgers'), 0777, true);
        }
        
        $pdf->save($path);
        $fileUrl = asset('storage/ledgers/' . $fileName);

        $waService = new \App\Services\WhatsAppService();
        $message = "Hello " . $user->name . ", here is your account ledger statement from Danyal Autos.\n\nCurrent Balance: Rs. " . number_format($user->current_balance, 2);
        
        try {
            $waService->sendMediaMessage($user->phone, $fileUrl, $fileName, $message);
            return redirect()->back()->with('success', 'Ledger sent to customer via WhatsApp');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'WhatsApp Error: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $transaction = CustomerLedger::findOrFail($id);
            $userId = $transaction->user_id;
            $transaction->delete();
            
            // Recalculate balance for this user
            CustomerLedger::updateBalance($userId);

            return redirect()->back()->with('success', 'Transaction deleted and balance reversed successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function thermalPrint($userId)
    {
        $user = \App\User::findOrFail($userId);
        // Get all transactions to calculate balance correctly
        $ledger = CustomerLedger::where('user_id', $userId)->orderBy('transaction_date', 'asc')->get();
        return view('backend.customer_ledger.thermal', compact('user', 'ledger'));
    }
}
