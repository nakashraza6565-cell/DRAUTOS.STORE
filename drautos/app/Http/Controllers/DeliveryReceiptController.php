<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DeliveryReceipt;
use App\User;

class DeliveryReceiptController extends Controller
{
    public function index()
    {
        $receipts = DeliveryReceipt::orderBy('id', 'DESC')->paginate(20);
        return view('backend.delivery_receipts.index', compact('receipts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'receiver_name' => 'required|string',
            'date' => 'required|date'
        ]);

        $data = $request->all();
        
        // Auto-generate sequential receipt number
        $lastReceipt = DeliveryReceipt::orderBy('id', 'DESC')->first();
        if ($lastReceipt) {
            $num = (int)str_replace('DR-', '', $lastReceipt->receipt_number);
            $data['receipt_number'] = 'DR-' . str_pad($num + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $data['receipt_number'] = 'DR-0001';
        }

        // Determine if customer exists
        $user = null;
        if (!empty($data['customer_id'])) {
            $user = User::find($data['customer_id']);
        } else {
            // Check by name if no ID passed but name matches
            $user = User::where('name', $data['receiver_name'])->where('role', 'user')->first();
        }

        if ($user) {
            $data['customer_id'] = $user->id;
            // Update customer profile if they provided new address/city info
            if (!empty($data['address'])) {
                $user->address = $data['address'];
            }
            if (!empty($data['city'])) {
                $user->city = $data['city'];
            }
            if (!empty($data['courier_company'])) {
                $user->courier_company = $data['courier_company'];
            }
            $user->save();
        }

        $data['total_parcels'] = (int)($data['no_of_cartons'] ?? 0) + (int)($data['no_of_bags'] ?? 0);

        $receipt = DeliveryReceipt::create($data);

        return redirect()->route('delivery-receipts.print', $receipt->id);
    }

    public function print($id)
    {
        $receipt = DeliveryReceipt::findOrFail($id);
        return view('backend.delivery_receipts.print', compact('receipt'));
    }

    public function getCustomer($id)
    {
        $user = User::find($id);
        if ($user) {
            return response()->json([
                'status' => true,
                'address' => $user->address,
                'city' => $user->city,
                'courier_company' => $user->courier_company
            ]);
        }
        return response()->json(['status' => false]);
    }
}
