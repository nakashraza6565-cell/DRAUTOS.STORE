<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Warehouse;

class WarehouseController extends Controller
{
    public function index()
    {
        $warehouses = Warehouse::orderBy('id', 'DESC')->get();
        return view('backend.warehouse.index')->with('warehouses', $warehouses);
    }

    public function create()
    {
        return view('backend.warehouse.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'string|required',
            'location' => 'string|nullable',
            'contact_person' => 'string|nullable',
            'phone' => 'string|nullable',
            'status' => 'required|in:active,inactive'
        ]);
        $data = $request->all();
        $status = Warehouse::create($data);
        if ($status) {
            request()->session()->flash('success', 'Warehouse successfully added');
        } else {
            request()->session()->flash('error', 'Error occurred, Please try again!');
        }
        return redirect()->route('warehouses.index');
    }

    public function edit($id)
    {
        $warehouse = Warehouse::findOrFail($id);
        return view('backend.warehouse.edit')->with('warehouse', $warehouse);
    }

    public function update(Request $request, $id)
    {
        $warehouse = Warehouse::findOrFail($id);
        $this->validate($request, [
            'name' => 'string|required',
            'status' => 'required|in:active,inactive'
        ]);
        $data = $request->all();
        $status = $warehouse->fill($data)->save();
        if ($status) {
            request()->session()->flash('success', 'Warehouse successfully updated');
        } else {
            request()->session()->flash('error', 'Error occurred, Please try again!');
        }
        return redirect()->route('warehouses.index');
    }

    public function destroy($id)
    {
        $warehouse = Warehouse::findOrFail($id);
        $status = $warehouse->delete();
        if ($status) {
            request()->session()->flash('success', 'Warehouse successfully deleted');
        } else {
            request()->session()->flash('error', 'Error occurred, Please try again!');
        }
        return redirect()->route('warehouses.index');
    }
}
