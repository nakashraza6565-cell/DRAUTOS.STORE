@extends('backend.layouts.master')

@section('main-content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            @include('backend.layouts.notification')
        </div>
    </div>

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Supplier Profile: {{ $supplier->name }}</h1>
        <div>
            <a href="{{ route('admin.supplier-ledger.show', $supplier->id) }}" class="btn btn-info btn-sm shadow-sm">
                <i class="fas fa-file-invoice-dollar fa-sm text-white-50"></i> View Full Ledger
            </a>
            <a href="{{ route('suppliers.index') }}" class="btn btn-primary btn-sm shadow-sm">
                <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to List
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Current Account Balance (Payable)</div>
                            <div class="h5 mb-0 font-weight-bold {{ $supplier->current_balance > 0 ? 'text-danger' : 'text-success' }}">
                                PKR {{ number_format($supplier->current_balance, 2) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-money-check-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Products Supplied</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $supplier->products()->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-box fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Products Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-white border-bottom">
            <h6 class="m-0 font-weight-bold text-dark">Products Linked to {{ $supplier->name }}</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="supplier-products-table">
                    <thead class="bg-light">
                        <tr>
                            <th>Image</th>
                            <th>Title</th>
                            <th>Category</th>
                            <th>Purchase Price</th>
                            <th>Selling Price</th>
                            <th>Stock</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($products as $product)
                        @php
                            $isLowStock = $product->stock <= ($product->low_stock_threshold ?? 5);
                            $isOutOfStock = $product->stock <= 0;
                        @endphp
                        <tr class="{{ $isOutOfStock ? 'table-danger' : ($isLowStock ? 'table-warning' : '') }}">
                            <td>
                                @if($product->photo)
                                    @php 
                                      $photo=explode(',',$product->photo);
                                    @endphp
                                    <img src="{{$photo[0]}}" class="img-fluid zoom" style="max-width:50px" alt="{{$product->photo}}">
                                @else
                                    <img src="{{asset('backend/img/thumbnail-default.jpg')}}" class="img-fluid zoom" style="max-width:50px" alt="avatar.png">
                                @endif
                            </td>
                            <td>
                                <strong>{{ $product->title }}</strong>
                                @if($isOutOfStock)
                                    <br><small class="text-danger font-weight-bold"><i class="fas fa-times-circle"></i> Out of Stock</small>
                                @elseif($isLowStock)
                                    <br><small class="text-warning font-weight-bold" style="color: #d68f00 !important;"><i class="fas fa-exclamation-triangle"></i> Low Stock</small>
                                @endif
                            </td>
                            <td>{{ $product->cat_info['title'] ?? 'N/A' }}</td>
                            <td>PKR {{ number_format($product->purchase_price, 2) }}</td>
                            <td>PKR {{ number_format($product->price, 2) }}</td>
                            <td>
                                @if($isOutOfStock)
                                    <span class="badge badge-danger" style="font-size:14px;">0</span>
                                @elseif($isLowStock)
                                    <span class="badge badge-warning text-dark" style="font-size:14px;">{{$product->stock}}</span>
                                @else
                                    <span class="badge badge-primary" style="font-size:14px;">{{$product->stock}}</span>
                                @endif
                            </td>
                            <td>
                                @if($product->status=='active')
                                    <span class="badge badge-success">{{$product->status}}</span>
                                @else
                                    <span class="badge badge-secondary">{{$product->status}}</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{route('product.edit', $product->id)}}" class="btn btn-primary btn-sm float-left mr-1" style="height:30px; width:30px;border-radius:50%" data-toggle="tooltip" title="edit" data-placement="bottom"><i class="fas fa-edit"></i></a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="mt-3 d-flex justify-content-center">
                    {{ $products->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .zoom {
        transition: transform .2s;
    }
    .zoom:hover {
        transform: scale(3.2);
    }
</style>
@endpush
