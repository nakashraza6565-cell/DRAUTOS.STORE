<?php

namespace App\Http\Controllers;

use App\Models\EmployeePayment;
use App\Models\EmployeeAdvance;
use App\Models\EmployeeAdvanceRepayment;
use App\Models\EmployeeCommission;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class EmployeePayrollController extends Controller
{
    /**
     * Display payroll dashboard
     */
    public function index()
    {
        $employees = User::whereIn('role', ['staff', 'admin'])->get();

        $stats = [
            'total_paid_this_month' => EmployeePayment::whereMonth('payment_date', now()->month)
                ->whereYear('payment_date', now()->year)
                ->sum('amount'),
            'pending_commissions' => EmployeeCommission::where('status', 'pending')->sum('commission_amount'),
            'active_advances' => EmployeeAdvance::where('status', '!=', 'fully_repaid')->sum('balance'),
            'employees_count' => $employees->count(),
        ];

        return view('backend.payroll.index', compact('employees', 'stats'));
    }

    /**
     * Show employee payroll details
     */
    public function show(User $employee)
    {
        $employee->load(['payments', 'advances', 'commissions']);

        $summary = [
            'total_paid' => $employee->payments->sum('amount'),
            'total_advances' => $employee->advances->sum('amount'),
            'total_repaid' => $employee->advances->sum('repaid_amount'),
            'pending_advances' => $employee->advances->where('status', '!=', 'fully_repaid')->sum('balance'),
            'total_commissions' => $employee->commissions->sum('commission_amount'),
            'paid_commissions' => $employee->commissions->where('status', 'paid')->sum('commission_amount'),
            'pending_commissions' => $employee->commissions->where('status', 'pending')->sum('commission_amount'),
        ];

        return view('backend.payroll.show', compact('employee', 'summary'));
    }

    /**
     * Record salary payment
     */
    public function recordPayment(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:users,id',
            'payment_date' => 'required|date',
            'payment_type' => 'required|in:salary,bonus,commission,overtime,other',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,bank_transfer,cheque,jazzcash,easypaisa',
            'reference_number' => 'nullable|string',
            'month_year' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $validated['paid_by'] = Auth::id();

        $payment = EmployeePayment::create($validated);

        // If payment type is commission, mark related commissions as paid
        if ($validated['payment_type'] === 'commission' && $request->has('commission_ids')) {
            EmployeeCommission::whereIn('id', $request->commission_ids)
                ->update([
                    'status' => 'paid',
                    'payment_id' => $payment->id
                ]);
        }

        session()->flash('success', 'Payment recorded successfully');
        return back();
    }

    /**
     * Record employee advance/loan
     */
    public function recordAdvance(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:0',
            'advance_date' => 'required|date',
            'installments' => 'required|integer|min:1',
            'reason' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $validated['approved_by'] = Auth::id();
        $validated['balance'] = $validated['amount'];
        $validated['installment_amount'] = $validated['amount'] / $validated['installments'];
        $validated['status'] = 'active';

        $advance = EmployeeAdvance::create($validated);

        session()->flash('success', 'Advance recorded successfully');
        return back();
    }

    /**
     * Record advance repayment
     */
    public function recordRepayment(Request $request, EmployeeAdvance $advance)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0|max:' . $advance->balance,
            'repayment_date' => 'required|date',
            'repayment_method' => 'required|in:salary_deduction,cash,other',
            'notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            EmployeeAdvanceRepayment::create([
                'advance_id' => $advance->id,
                'amount' => $validated['amount'],
                'repayment_date' => $validated['repayment_date'],
                'repayment_method' => $validated['repayment_method'],
                'notes' => $validated['notes'] ?? null,
            ]);

            $advance->updateBalance();

            DB::commit();

            session()->flash('success', 'Repay recorded successfully');
            return back();

        } catch (\Exception $e) {
            DB::rollback();
            session()->flash('error', 'Error recording repayment: ' . $e->getMessage());
            return back();
        }
    }

    /**
     * Calculate and record commission for an order
     */
    public function calculateCommission(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:users,id',
            'order_id' => 'required|exists:orders,id',
            'sale_amount' => 'required|numeric|min:0',
            'commission_rate' => 'required|numeric|min:0|max:100',
            'commission_date' => 'required|date',
        ]);

        $commission_amount = ($validated['sale_amount'] * $validated['commission_rate']) / 100;

        EmployeeCommission::create([
            'employee_id' => $validated['employee_id'],
            'order_id' => $validated['order_id'],
            'sale_amount' => $validated['sale_amount'],
            'commission_rate' => $validated['commission_rate'],
            'commission_amount' => $commission_amount,
            'commission_date' => $validated['commission_date'],
            'status' => 'pending',
        ]);

        session()->flash('success', 'Commission calculated and recorded');
        return back();
    }

    /**
     * Get employee ledger
     */
    public function ledger(User $employee, Request $request)
    {
        $from = $request->get('from', now()->startOfMonth()->format('Y-m-d'));
        $to = $request->get('to', now()->format('Y-m-d'));

        $payments = EmployeePayment::where('employee_id', $employee->id)
            ->whereBetween('payment_date', [$from, $to])
            ->orderBy('payment_date', 'desc')
            ->get();

        $advances = EmployeeAdvance::where('employee_id', $employee->id)
            ->whereBetween('advance_date', [$from, $to])
            ->with('repayments')
            ->get();

        $commissions = EmployeeCommission::where('employee_id', $employee->id)
            ->whereBetween('commission_date', [$from, $to])
            ->get();

        return view('backend.payroll.ledger', compact('employee', 'payments', 'advances', 'commissions', 'from', 'to'));
    }

    /**
     * Print payment voucher
     */
    public function printVoucher(EmployeePayment $payment)
    {
        $payment->load(['employee', 'payer']);
        return view('backend.payroll.voucher', compact('payment'));
    }

    /**
     * Get pending commissions for employee
     */
    public function getPendingCommissions(User $employee)
    {
        $commissions = EmployeeCommission::where('employee_id', $employee->id)
            ->where('status', 'pending')
            ->with('order')
            ->get();

        return response()->json($commissions);
    }
}
