<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Supplier;

use Carbon\Carbon;
use App\Models\PurchaseOrder;
use App\Models\PurchaseReturn;
use App\Models\InventoryIncoming;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $suppliers = Supplier::orderBy('id', 'DESC')
            ->when($request->search, function($query) use ($request) {
                return $query->where('name', 'LIKE', "%{$request->search}%")
                            ->orWhere('company_name', 'LIKE', "%{$request->search}%")
                            ->orWhere('phone', 'LIKE', "%{$request->search}%")
                            ->orWhere('email', 'LIKE', "%{$request->search}%");
            })
            ->get();
        return view('backend.supplier.index')->with('suppliers', $suppliers);
    }

    public function show(Request $request, $id)
    {
        $supplier = Supplier::findOrFail($id);
        
        $from = $request->get('from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $to = $request->get('to', Carbon::now()->format('Y-m-d'));

        $purchaseOrders = PurchaseOrder::where('supplier_id', $id)
            ->whereBetween('order_date', [$from, $to])
            ->orderBy('order_date', 'DESC')
            ->get();

        $returns = PurchaseReturn::where('supplier_id', $id)
            ->whereBetween('return_date', [$from, $to])
            ->orderBy('return_date', 'DESC')
            ->get();

        $incoming = InventoryIncoming::where('supplier_id', $id)
            ->whereBetween('received_date', [$from, $to])
            ->orderBy('received_date', 'DESC')
            ->get();

        return view('backend.supplier.show', compact('supplier', 'purchaseOrders', 'returns', 'incoming', 'from', 'to'));
    }

    public function exportCSV(Request $request, $id)
    {
        $supplier = Supplier::findOrFail($id);
        $from = $request->get('from');
        $to = $request->get('to');

        $filename = "supplier_history_" . str_replace(' ', '_', strtolower($supplier->name)) . "_" . date('Y-m-d') . ".csv";
        
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['Date', 'Type', 'Reference', 'Description', 'Amount', 'Status'];

        $callback = function() use ($supplier, $from, $to, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            $id = $supplier->id;

            $purchaseOrders = PurchaseOrder::where('supplier_id', $id)
                ->whereBetween('order_date', [$from, $to])
                ->get();

            foreach ($purchaseOrders as $po) {
                fputcsv($file, [$po->order_date, 'Purchase Order', $po->po_number, 'Items: ' . $po->items->count(), $po->total_amount, $po->status]);
            }

            $returns = PurchaseReturn::where('supplier_id', $id)
                ->whereBetween('return_date', [$from, $to])
                ->get();

            foreach ($returns as $ret) {
                fputcsv($file, [$ret->return_date->format('Y-m-d'), 'Purchase Return', $ret->return_number, $ret->reason, '-' . $ret->total_return_amount, $ret->status]);
            }

            $incoming = InventoryIncoming::where('supplier_id', $id)
                ->whereBetween('received_date', [$from, $to])
                ->get();

            foreach ($incoming as $inc) {
                fputcsv($file, [$inc->received_date->format('Y-m-d'), 'Incoming Goods', $inc->reference_number, 'Invoice: ' . $inc->invoice_number, $inc->items->sum('total_cost'), $inc->status]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function create()
    {
        return view('backend.supplier.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'string|required',
            'email' => 'string|nullable',
            'phone' => 'string|nullable',
            'company_name' => 'string|nullable',
            'status' => 'required|in:active,inactive'
        ]);
        $data = $request->all();
        $status = Supplier::create($data);
        if ($status) {
            request()->session()->flash('success', 'Supplier successfully added');
        } else {
            request()->session()->flash('error', 'Error occurred, Please try again!');
        }
        return redirect()->route('suppliers.index');
    }

    public function edit($id)
    {
        $supplier = Supplier::findOrFail($id);
        return view('backend.supplier.edit')->with('supplier', $supplier);
    }

    public function update(Request $request, $id)
    {
        $supplier = Supplier::findOrFail($id);
        $this->validate($request, [
            'name' => 'string|required',
            'status' => 'required|in:active,inactive'
        ]);
        $data = $request->all();
        $status = $supplier->fill($data)->save();
        if ($status) {
            request()->session()->flash('success', 'Supplier successfully updated');
        } else {
            request()->session()->flash('error', 'Error occurred, Please try again!');
        }
        return redirect()->route('suppliers.index');
    }

    public function sendWhatsApp(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'supplier_id' => 'required|exists:suppliers,id',
            'product_ids' => 'required|array',
            'quantities' => 'required|array',
            'description' => 'nullable|string'
        ]);

        $supplier = Supplier::find($request->supplier_id);
        $products_text = "";
        
        foreach($request->product_ids as $key => $pid) {
            $product = \App\Models\Product::find($pid);
            if($product) {
                $qty = $request->quantities[$key] ?? 1;
                $products_text .= "- {$product->title} (Qty: {$qty})\n";
            }
        }

        $appName = config('app.name', 'Dr Auto Store');
        $message  = "Assalam-o-Alaikum {$supplier->name},\n\n";
        $message .= "Please find below a stock inquiry / order request from {$appName}.\n\n";
        $message .= "Date         : " . now()->format('d M Y') . "\n\n";
        $message .= "Product List:\n";
        $message .= "-----------------------------\n";
        $message .= $products_text;
        $message .= "-----------------------------\n\n";

        if ($request->description) {
            $message .= "Notes:\n{$request->description}\n\n";
        }

        $message .= "Kindly provide availability and pricing for the above items at your earliest convenience.\n\n";
        $message .= "Regards,\n{$appName}";

        $waService = new \App\Services\WhatsAppService();
        $status = $waService->sendMessage($request->phone, $message);

        if($status) {
            return response()->json(['success' => true, 'message' => 'WhatsApp message sent to ' . $supplier->name]);
        } else {
            return response()->json(['success' => false, 'message' => 'Failed to send WhatsApp message. Please check API settings.']);
        }
    }

    public function destroy($id)
    {
        $supplier = Supplier::findOrFail($id);
        $status = $supplier->delete();
        if ($status) {
            request()->session()->flash('success', 'Supplier successfully deleted');
        } else {
            request()->session()->flash('error', 'Error occurred, Please try again!');
        }
        return redirect()->route('suppliers.index');
    }

    public function updateRating(Request $request, $id) {
        $request->validate([
            'loyalty_rating' => 'nullable|integer|min:0|max:5',
            'goodwill_rating' => 'nullable|integer|min:0|max:5',
            'payment_rating' => 'nullable|integer|min:0|max:5',
            'behaviour_rating' => 'nullable|integer|min:0|max:5',
        ]);
        
        $supplier = Supplier::findOrFail($id);
        $supplier->loyalty_rating = $request->loyalty_rating ?? 0;
        $supplier->goodwill_rating = $request->goodwill_rating ?? 0;
        $supplier->payment_rating = $request->payment_rating ?? 0;
        $supplier->behaviour_rating = $request->behaviour_rating ?? 0;
        
        $supplier->save();
        
        if($request->ajax()) {
            return response()->json(['status' => 'success', 'message' => 'Ratings updated successfully']);
        }
        
        return redirect()->back()->with('success', 'Ratings updated successfully');
    }
}
