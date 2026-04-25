@extends('backend.layouts.master')

@section('main-content')
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary float-left">Packaging Stock Inventory</h6>
        <div class="float-right">
            <a href="{{route('packaging.create')}}" class="btn btn-primary btn-sm shadow-sm"><i class="fas fa-plus"></i> Add New Item</a>
        </div>
    </div>
    <div class="card-body">
        <!-- Filter/Search -->
        <form method="GET" action="{{route('packaging.index')}}" class="mb-4">
            <div class="row">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Search by name..." value="{{request('search')}}">
                </div>
                <div class="col-md-3">
                    <select name="type" class="form-control">
                        <option value="">All Types</option>
                        <option value="sticker" {{request('type') == 'sticker' ? 'selected' : ''}}>Sticker</option>
                        <option value="box" {{request('type') == 'box' ? 'selected' : ''}}>Box</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                </div>
                <div class="col-md-2">
                    <a href="{{route('packaging.index')}}" class="btn btn-secondary w-100">Reset</a>
                </div>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="packaging-table" width="100%" cellspacing="0">
                <thead class="thead-light">
                    <tr>
                        <th>#</th>
                        <th>Type</th>
                        <th>Name</th>
                        <th>Size</th>
                        <th>Supplier</th>
                        <th>Base Cost</th>
                        <th>Current Stock</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $item)
                    <tr>
                        <td>{{$item->id}}</td>
                        <td>
                            <span class="badge badge-{{$item->type == 'sticker' ? 'info' : 'warning'}}">
                                {{strtoupper($item->type)}}
                            </span>
                        </td>
                        <td>{{$item->name}}</td>
                        <td>{{$item->size ?? 'N/A'}}</td>
                        <td>{{$item->supplier->name ?? 'N/A'}}</td>
                        <td>Rs. {{number_format($item->cost, 2)}}</td>
                        <td>
                            <span class="font-weight-bold {{$item->stock < 10 ? 'text-danger' : 'text-success'}}">
                                {{number_format($item->stock, 2)}}
                            </span>
                        </td>
                        <td>
                            <a href="{{route('packaging.edit', $item->id)}}" class="btn btn-primary btn-sm float-left mr-1" data-toggle="tooltip" title="edit" data-placement="bottom"><i class="fas fa-edit"></i></a>
                            <form method="POST" action="{{route('packaging.destroy', [$item->id])}}">
                                @csrf
                                @method('delete')
                                <button class="btn btn-danger btn-sm dltBtn" data-id={{$item->id}} data-toggle="tooltip" data-placement="bottom" title="Delete"><i class="fas fa-trash-alt"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center">No materials found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .dltBtn { border-radius: 50%; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; }
</style>
@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $('.dltBtn').click(function(e) {
            var form = $(this).closest('form');
            var dataID = $(this).data('id');
            e.preventDefault();
            swal({
                title: "Are you sure?",
                text: "Once deleted, you will not be able to recover this data!",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            })
            .then((willDelete) => {
                if (willDelete) {
                    form.submit();
                } else {
                    swal("Your data is safe!");
                }
            });
        })
    })
</script>
@endpush
