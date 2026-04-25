<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PackagingItem;
use App\Models\PackagingPurchase;
use App\Models\Supplier;
use PDF;
use Illuminate\Support\Str;
use App\Services\WhatsAppService;

class PackagingPurchaseController extends Controller
{
    protected $whatsapp;

    public function __construct(WhatsAppService $whatsapp)
    {
        $this->whatsapp = $whatsapp;
    }

    public function index(Request $request)
    {
        $purchases = PackagingPurchase::with(['packagingItem', 'supplier']);

        if ($request->has('search') && $request->search != '') {
            $purchases->where('invoice_no', 'like', '%' . $request->search . '%');
        }

        $purchases = $purchases->orderBy('id', 'DESC')->paginate(5000);
        return view('backend.packaging.purchase.index')->with('purchases', $purchases);
    }

    public function create()
    {
        $items = PackagingItem::all();
        $suppliers = Supplier::where('status', 'active')->get();
        return view('backend.packaging.purchase.create', compact('items', 'suppliers'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'packaging_item_id' => 'required|exists:packaging_items,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'quantity' => 'required|numeric|min:0.01',
            'price' => 'required|numeric|min:0',
            'purchase_date' => 'required|date',
            'invoice_no' => 'nullable|string|unique:packaging_purchases,invoice_no'
        ]);

        $data = $request->all();
        if (empty($data['invoice_no'])) {
            $data['invoice_no'] = 'PKG-' . strtoupper(Str::random(8));
        }
        $data['total_price'] = $data['quantity'] * $data['price'];

        $purchase = PackagingPurchase::create($data);

        if ($purchase) {
            // Update stock of the item
            $item = PackagingItem::find($data['packaging_item_id']);
            $item->stock += $data['quantity'];
            $item->save();

            // Send WhatsApp with PDF
            try {
                $purchase->load('packagingItem', 'supplier');
                $this->whatsapp->sendPackagingPurchaseNotification($purchase);
            } catch (\Exception $e) {
                \Log::error("Packaging WHATSAPP ERROR: " . $e->getMessage());
            }

            request()->session()->flash('success', 'Purchase recorded, stock updated, and WhatsApp sent.');
        } else {
            request()->session()->flash('error', 'Error occurred while recording purchase.');
        }

        return redirect()->route('packaging.purchases.index');
    }

    public function edit($id)
    {
        $purchase = PackagingPurchase::findOrFail($id);
        $items = PackagingItem::all();
        $suppliers = Supplier::where('status', 'active')->get();
        return view('backend.packaging.purchase.edit', compact('purchase', 'items', 'suppliers'));
    }

    public function update(Request $request, $id)
    {
        $purchase = PackagingPurchase::findOrFail($id);
        $this->validate($request, [
            'packaging_item_id' => 'required|exists:packaging_items,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'quantity' => 'required|numeric|min:0.01',
            'price' => 'required|numeric|min:0',
            'purchase_date' => 'required|date',
        ]);

        $data = $request->all();
        $data['total_price'] = $data['quantity'] * $data['price'];

        // Adjust Stock
        $item = PackagingItem::find($purchase->packaging_item_id);
        if ($item) {
            // Revert old quantity
            $item->stock -= $purchase->quantity;
            $item->save();
        }

        $purchase->update($data);

        // Apply new quantity
        $newItem = PackagingItem::find($data['packaging_item_id']);
        if ($newItem) {
            $newItem->stock += $data['quantity'];
            $newItem->save();
        }

        request()->session()->flash('success', 'Purchase record updated.');
        return redirect()->route('packaging.purchases.index');
    }

    public function destroy($id)
    {
        $purchase = PackagingPurchase::findOrFail($id);
        
        // Adjust Stock before deleting
        $item = PackagingItem::find($purchase->packaging_item_id);
        if ($item) {
            $item->stock -= $purchase->quantity;
            $item->save();
        }

        $purchase->delete();
        request()->session()->flash('success', 'Purchase record deleted and stock adjusted.');
        return redirect()->route('packaging.purchases.index');
    }

    public function invoice($id)
    {
        $purchase = PackagingPurchase::with(['packagingItem', 'supplier'])->findOrFail($id);
        $pdf = PDF::loadview('backend.packaging.purchase.invoice', compact('purchase'));
        return $pdf->download('Invoice-' . $purchase->invoice_no . '.pdf');
    }
}
