<?php

namespace App\Http\Controllers;

use App\Models\PaymentReminder;
use App\User;
use App\Models\Supplier;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentReminderController extends Controller
{
    protected $whatsapp;

    public function __construct(WhatsAppService $whatsapp)
    {
        $this->whatsapp = $whatsapp;
    }

    public function index()
    {
        $today = today();
        
        // Reminders due today
        $dueToday = PaymentReminder::with('party')
            ->dueToday()
            ->get();
            
        // Overdue reminders
        $overdue = PaymentReminder::with('party')
            ->overdue()
            ->get();
            
        // Upcoming (All future)
        $upcoming = PaymentReminder::with('party')
            ->where('due_date', '>', $today)
            ->whereIn('status', ['pending', 'partially_paid'])
            ->orderBy('due_date', 'asc')
            ->get();

        // Calculate totals for today
        $receivablesToday = $dueToday->where('type', 'receivable')->sum(function($r) {
            return $r->amount - $r->paid_amount;
        });
        
        $payablesToday = $dueToday->where('type', 'payable')->sum(function($r) {
            return $r->amount - $r->paid_amount;
        });

        // Get customers and suppliers for the modal
        $customers = User::whereIn('role', ['customer', 'user'])->get();
        $suppliers = Supplier::all();

        return view('backend.payment-reminders.index', compact(
            'dueToday', 'overdue', 'upcoming', 'receivablesToday', 'payablesToday',
            'customers', 'suppliers'
        ));
    }

    /**
     * Store a new payment reminder
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:receivable,payable',
            'party_type' => 'required|string',
            'party_id' => 'required|integer',
            'reference_number' => 'nullable|string',
            'amount' => 'required|numeric|min:0',
            'due_date' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        $validated['due_date'] = \Carbon\Carbon::parse($validated['due_date']);

        DB::beginTransaction();
        try {
            $reminder = PaymentReminder::create($validated);

            // Update party balance on creation
            if ($validated['party_type'] === 'App\\User') {
                $customer = User::find($validated['party_id']);
                if ($customer && $validated['type'] === 'receivable') {
                    $customer->current_balance += $validated['amount'];
                    $customer->save();
                }
            } elseif ($validated['party_type'] === 'App\\Models\\Supplier') {
                $supplier = Supplier::find($validated['party_id']);
                if ($supplier && $validated['type'] === 'payable') {
                    $supplier->current_balance += $validated['amount'];
                    $supplier->save();
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Payment reminder created successfully',
                'reminder' => $reminder
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error creating reminder: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update payment reminder
     */
    public function update(Request $request, PaymentReminder $reminder)
    {
        $validated = $request->validate([
            'amount' => 'nullable|numeric|min:0',
            'status' => 'nullable|in:pending,partially_paid,completed,overdue',
            'paid_amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        if (isset($validated['due_date'])) {
            $validated['due_date'] = \Carbon\Carbon::parse($validated['due_date']);
        }

        DB::beginTransaction();
        try {
            $old_remaining = $reminder->amount - $reminder->paid_amount;
            
            $reminder->update($validated);
            
            // Re-calculate status based on paid amount if provided
            if (isset($validated['paid_amount']) || isset($validated['amount'])) {
                if ($reminder->paid_amount >= $reminder->amount) {
                    $reminder->status = 'completed';
                } elseif ($reminder->paid_amount > 0) {
                    $reminder->status = 'partially_paid';
                } else {
                    $reminder->status = 'pending';
                }
                $reminder->save();
            }

            $new_remaining = $reminder->amount - $reminder->paid_amount;
            $diff = $new_remaining - $old_remaining;

            // Sync balance if it's not completed (only adjust pending part)
            if ($diff != 0) {
                if ($reminder->party_type === 'App\\User') {
                    $customer = User::find($reminder->party_id);
                    if ($customer && $reminder->type === 'receivable') {
                        $customer->current_balance += $diff;
                        $customer->save();
                    }
                } elseif ($reminder->party_type === 'App\\Models\\Supplier') {
                    $supplier = Supplier::find($reminder->party_id);
                    if ($supplier && $reminder->type === 'payable') {
                        $supplier->current_balance += $diff;
                        $supplier->save();
                    }
                }
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Payment reminder updated successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error updating reminder: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Record payment against reminder
     */
    public function recordPayment(Request $request, PaymentReminder $reminder)
    {
        $validated = $request->validate([
            'payment_amount' => 'required|numeric|min:0',
            'payment_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $reminder->paid_amount += $validated['payment_amount'];
            
            if ($reminder->paid_amount >= $reminder->amount) {
                $reminder->status = 'completed';
            } else {
                $reminder->status = 'partially_paid';
            }
            
            $reminder->save();

            // Update party balance
            if ($reminder->party_type === 'App\\User') {
                $customer = User::find($reminder->party_id);
                if ($customer && $reminder->type === 'receivable') {
                    $customer->current_balance -= $validated['payment_amount'];
                    $customer->save();
                }
            } elseif ($reminder->party_type === 'App\\Models\\Supplier') {
                $supplier = Supplier::find($reminder->party_id);
                if ($supplier && $reminder->type === 'payable') {
                    $supplier->current_balance -= $validated['payment_amount'];
                    $supplier->save();
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Payment recorded successfully',
                'remaining' => $reminder->remaining_amount
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error recording payment: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send WhatsApp reminder
     */
    public function sendWhatsAppReminder(PaymentReminder $reminder)
    {
        if (!$reminder->party || !$reminder->party->phone) {
            return response()->json([
                'success' => false,
                'message' => 'No phone number available for this party'
            ], 400);
        }

        $sent = $this->whatsapp->sendPaymentReminder(
            $reminder->party->phone,
            $reminder->type,
            $reminder->remaining_amount,
            $reminder->due_date->format('Y-m-d'),
            $reminder->reference_number
        );

        if ($sent) {
            $reminder->update([
                'whatsapp_sent' => true,
                'whatsapp_sent_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'WhatsApp reminder sent successfully'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Failed to send WhatsApp reminder'
        ], 500);
    }

    /**
     * Get today's notifications for dashboard widget
     */
    public function getTodayNotifications()
    {
        $receivables = PaymentReminder::with('party')
            ->where('type', 'receivable')
            ->dueToday()
            ->get();

        $payables = PaymentReminder::with('party')
            ->where('type', 'payable')
            ->dueToday()
            ->get();

        return response()->json([
            'receivables' => $receivables,
            'payables' => $payables,
            'total_receivable_amount' => $receivables->sum('remaining_amount'),
            'total_payable_amount' => $payables->sum('remaining_amount'),
        ]);
    }

    /**
     * Delete payment reminder
     */
    public function destroy(PaymentReminder $reminder)
    {
        DB::beginTransaction();
        try {
            // Revert party balance if it's pending/unpaid
            if ($reminder->status !== 'completed') {
                $balance_to_revert = $reminder->amount - $reminder->paid_amount;
                if ($reminder->party_type === 'App\\User') {
                    $customer = User::find($reminder->party_id);
                    if ($customer && $reminder->type === 'receivable') {
                        $customer->current_balance -= $balance_to_revert;
                        $customer->save();
                    }
                } elseif ($reminder->party_type === 'App\\Models\\Supplier') {
                    $supplier = Supplier::find($reminder->party_id);
                    if ($supplier && $reminder->type === 'payable') {
                        $supplier->current_balance -= $balance_to_revert;
                        $supplier->save();
                    }
                }
            }
            
            $reminder->delete();
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Payment reminder deleted successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error deleting reminder: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show reminder data for editing
     */
    public function show(PaymentReminder $reminder)
    {
        return response()->json($reminder);
    }
}
