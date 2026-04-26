@extends('backend.layouts.master')

@section('main-content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Dead Products Report</h1>
        <small class="text-muted">Products with NO sales in the last 30 days</small>
        <button class="btn btn-sm btn-primary shadow-sm" onclick="window.print()"><i class="fas fa-print fa-sm text-white-50"></i> Print</button>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-white">
            <h6 class="m-0 font-weight-bold text-danger"><i class="fas fa-ghost mr-2"></i>Inactive / Dead Stock</h6>
        </div>
        <div class="card-body">
            <div class="alert alert-info border-0 shadow-sm">
                <i class="fas fa-info-circle mr-2"></i> These products are currently in your inventory but haven't been purchased by any customer for over a month. 
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="deadStockTable" width="100%" cellspacing="0">
                    <thead class="bg-light">
                        <tr>
                            <th>Product Name</th>
                            <th>Category</th>
                            <th>Remaining Stock</th>
                            <th>Cost per Item</th>
                            <th>Tied Up Capital</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($deadProducts as $product)
                        <tr>
                            <td><strong>{{$product->title}}</strong></td>
                            <td>{{$product->cat_info->title ?? 'N/A'}}</td>
                            <td class="text-center">{{$product->stock}}</td>
                            <td>Rs. {{number_format($product->purchase_price, 2)}}</td>
                            <td class="font-weight-bold text-danger">Rs. {{number_format($product->stock * $product->purchase_price, 2)}}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-success">
                                <i class="fas fa-check-circle fa-3x mb-3"></i>
                                <h5>Great news! No dead products found.</h5>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                    @if($deadProducts->count() > 0)
                    <tfoot class="bg-gray-100 font-weight-bold">
                        <tr>
                            <td colspan="4" class="text-right">Total Capital Tied Up in Dead Stock:</td>
                            <td class="text-danger h5">Rs. {{number_format($deadProducts->sum(fn($p) => $p->stock * $p->purchase_price), 2)}}</td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
