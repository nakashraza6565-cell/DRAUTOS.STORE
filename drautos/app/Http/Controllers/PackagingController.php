<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PackagingItem;
use App\Models\OrderPackaging;
use App\Models\Supplier;

class PackagingController extends Controller
{
    public function index(Request $request)
    {
        $items = PackagingItem::with('supplier');

        if ($request->has('type') && $request->type != '') {
            $items->where('type', $request->type);
        }

        if ($request->has('search') && $request->search != '') {
            $items->where('name', 'like', '%' . $request->search . '%');
        }

        $items = $items->orderBy('id', 'DESC')->get();
        return view('backend.packaging.index')->with('items', $items);
    }

    public function create()
    {
        $suppliers = Supplier::where('status', 'active')->get();
        return view('backend.packaging.create')->with('suppliers', $suppliers);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'type' => 'required|in:sticker,box',
            'name' => 'required|string',
            'size' => 'nullable|string',
            'cost' => 'required|numeric',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'stock' => 'nullable|numeric'
        ]);

        $data = $request->all();
        $status = PackagingItem::create($data);

        if ($status) {
            request()->session()->flash('success', 'Packaging item successfully added');
        } else {
            request()->session()->flash('error', 'Error occurred, Please try again!');
        }
        return redirect()->route('packaging.index');
    }

    public function edit($id)
    {
        $item = PackagingItem::findOrFail($id);
        $suppliers = Supplier::where('status', 'active')->get();
        return view('backend.packaging.edit', compact('item', 'suppliers'));
    }

    public function update(Request $request, $id)
    {
        $item = PackagingItem::findOrFail($id);
        
        $this->validate($request, [
            'type' => 'required|in:sticker,box',
            'name' => 'required|string',
            'size' => 'nullable|string',
            'cost' => 'required|numeric',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'stock' => 'nullable|numeric'
        ]);

        $data = $request->all();
        $status = $item->fill($data)->save();

        if ($status) {
            request()->session()->flash('success', 'Packaging item successfully updated');
        } else {
            request()->session()->flash('error', 'Error occurred, Please try again!');
        }
        return redirect()->route('packaging.index');
    }

    public function destroy($id)
    {
        $item = PackagingItem::findOrFail($id);
        $status = $item->delete();

        if ($status) {
            request()->session()->flash('success', 'Packaging item successfully deleted');
        } else {
            request()->session()->flash('error', 'Error occurred, Please try again!');
        }
        return redirect()->route('packaging.index');
    }

    public function usageIndex(Request $request)
    {
        $orderUsageQuery = OrderPackaging::with(['order', 'packagingItem']);
        $incomingUsageQuery = \App\Models\InventoryIncomingItem::whereNotNull('packaging_item_id')->with(['inventoryIncoming', 'packagingItem']);

        if ($request->has('type') && $request->type != '') {
            $type = $request->type;
            $orderUsageQuery->whereHas('packagingItem', function($q) use ($type) {
                $q->where('type', $type);
            });
            $incomingUsageQuery->whereHas('packagingItem', function($q) use ($type) {
                $q->where('type', $type);
            });
        }

        if ($request->has('order_number') && $request->order_number != '') {
            $search = $request->order_number;
            $orderUsageQuery->whereHas('order', function($q) use ($search) {
                $q->where('order_number', 'like', '%' . $search . '%');
            });
            $incomingUsageQuery->whereHas('inventoryIncoming', function($q) use ($search) {
                $q->where('reference_number', 'like', '%' . $search . '%');
            });
        }

        $orderUsage = $orderUsageQuery->get()->map(function($item) {
            return (object) [
                'date' => $item->created_at,
                'ref_no' => $item->order->order_number ?? 'N/A',
                'material' => $item->packagingItem->name ?? 'N/A',
                'size' => $item->packagingItem->size ?? '',
                'type' => $item->packagingItem->type ?? 'N/A',
                'quantity' => $item->quantity,
                'source' => 'Order',
                'url' => route('order.show', $item->order_id ?? 0)
            ];
        });

        $incomingUsage = $incomingUsageQuery->get()->map(function($item) {
            return (object) [
                'date' => $item->created_at,
                'ref_no' => $item->inventoryIncoming->reference_number ?? 'N/A',
                'material' => $item->packagingItem->name ?? 'N/A',
                'size' => $item->packagingItem->size ?? '',
                'type' => $item->packagingItem->type ?? 'N/A',
                'quantity' => $item->packaging_quantity,
                'source' => 'Incoming',
                'url' => route('inventory-incoming.show', $item->inventory_incoming_id ?? 0)
            ];
        });

        $merged = $orderUsage->merge($incomingUsage)->sortByDesc('date');

        // Simple Pagination for merged collection
        $perPage = 20;
        $page = $request->get('page', 1);
        $pagedData = new \Illuminate\Pagination\LengthAwarePaginator(
            $merged->forPage($page, $perPage),
            $merged->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('backend.packaging.usage')->with('usage', $pagedData);
    }
}
