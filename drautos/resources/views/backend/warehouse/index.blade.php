@extends('backend.layouts.master')

@section('main-content')
 <div class="card shadow mb-4">
    <div class="card-header py-3">
      <h6 class="m-0 font-weight-bold text-primary float-left">Warehouse List</h6>
      <a href="{{route('warehouses.create')}}" class="btn btn-primary btn-sm float-right"><i class="fas fa-plus"></i> Add Warehouse</a>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        @if(count($warehouses)>0)
        <table class="table table-bordered" id="banner-dataTable" width="100%" cellspacing="0">
          <thead>
            <tr>
              <th>S.N.</th>
              <th>Name</th>
              <th>Location</th>
              <th>Contact Person</th>
              <th>Phone</th>
              <th>Status</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            @foreach($warehouses as $warehouse)   
                <tr>
                    <td>{{$warehouse->id}}</td>
                    <td>{{$warehouse->name}}</td>
                    <td>{{$warehouse->location}}</td>
                    <td>{{$warehouse->contact_person}}</td>
                    <td>{{$warehouse->phone_number}}</td>
                    <td>
                        <span class="badge badge-{{($warehouse->status=='active') ? 'success' : 'warning'}}">{{$warehouse->status}}</span>
                    </td>
                    <td>
                        <a href="{{route('warehouses.edit',$warehouse->id)}}" class="btn btn-primary btn-sm float-left mr-1" style="height:30px; width:30px;border-radius:50%"><i class="fas fa-edit"></i></a>
                        <form method="POST" action="{{route('warehouses.destroy',[$warehouse->id])}}">
                          @csrf 
                          @method('delete')
                              <button class="btn btn-danger btn-sm dltBtn" style="height:30px; width:30px;border-radius:50%"><i class="fas fa-trash-alt"></i></button>
                        </form>
                    </td>
                </tr>  
            @endforeach
          </tbody>
        </table>
        @else
          <h6 class="text-center">No Warehouses found!!!</h6>
        @endif
      </div>
    </div>
</div>
@endsection
