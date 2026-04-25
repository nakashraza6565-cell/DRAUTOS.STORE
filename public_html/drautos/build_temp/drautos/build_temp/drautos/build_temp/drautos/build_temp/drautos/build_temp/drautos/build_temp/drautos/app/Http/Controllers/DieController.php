<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DieModel;

class DieController extends Controller
{
    public function index()
    {
        $dies = DieModel::orderBy('id', 'DESC')->get();
        return view('backend.die.index')->with('dies', $dies);
    }

    public function create()
    {
        return view('backend.die.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'string|required',
            'rack_number' => 'string|nullable',
            'maker' => 'string|nullable',
            'maker_phone' => 'string|nullable',
            'die_type' => 'string|nullable',
            'phone_number' => 'string|nullable',
            'custody_of' => 'string|nullable',
            'custody_phone' => 'string|nullable',
            'quality_status' => 'string|nullable',
            'photo' => 'string|nullable',
            'status' => 'required|in:active,inactive'
        ]);
        $data = $request->all();
        $status = DieModel::create($data);
        if ($status) {
            request()->session()->flash('success', 'Die successfully added');
        } else {
            request()->session()->flash('error', 'Error occurred, Please try again!');
        }
        return redirect()->route('die-management.index');
    }

    public function edit($id)
    {
        $die = DieModel::findOrFail($id);
        return view('backend.die.edit')->with('die', $die);
    }

    public function update(Request $request, $id)
    {
        $die = DieModel::findOrFail($id);
        $this->validate($request, [
            'name' => 'string|required',
            'maker_phone' => 'string|nullable',
            'custody_phone' => 'string|nullable',
            'photo' => 'string|nullable',
            'status' => 'required|in:active,inactive'
        ]);
        $data = $request->all();
        $status = $die->fill($data)->save();
        if ($status) {
            request()->session()->flash('success', 'Die successfully updated');
        } else {
            request()->session()->flash('error', 'Error occurred, Please try again!');
        }
        return redirect()->route('die-management.index');
    }

    public function destroy($id)
    {
        $die = DieModel::findOrFail($id);
        $status = $die->delete();
        if ($status) {
            request()->session()->flash('success', 'Die successfully deleted');
        } else {
            request()->session()->flash('error', 'Error occurred, Please try again!');
        }
        return redirect()->route('die-management.index');
    }
}
