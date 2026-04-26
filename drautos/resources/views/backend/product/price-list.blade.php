@extends('backend.layouts.master')

@section('title', 'Price List')

@section('main-content')
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex align-items-center justify-content-between">
        <div>
            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-tags mr-2"></i>Price List</h6>
            <small class="text-muted">Double-click any price cell to edit it inline</small>
        </div>
        <div class="d-flex align-items-center" style="gap: 10px;">
            <a href="{{route('product.price-list.pdf', request()->query())}}" class="btn btn-danger btn-sm">
                <i class="fas fa-file-pdf mr-1"></i> Print PDF
            </a>
            <a href="{{route('product.index')}}" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left mr-1"></i> All Products</a>
        </div>
    </div>

    <div class="card-body">
        {{-- Filters --}}
        <form action="{{route('product.price-list')}}" method="GET" class="mb-4">
            <div class="row align-items-end">
                <div class="col-md-3">
                    <label class="small font-weight-bold text-uppercase">Search Product</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-light"><i class="fas fa-search fa-sm"></i></span>
                        </div>
                        <input type="text" name="title" class="form-control" placeholder="Title or SKU..." value="{{request('title')}}">
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="small font-weight-bold text-uppercase">Supplier</label>
                    <select name="supplier_id" class="form-control selectpicker" data-live-search="true" data-style="btn-outline-primary">
                        <option value="">-- All Suppliers --</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{$supplier->id}}" {{request('supplier_id')==$supplier->id ? 'selected' : ''}}>
                                {{$supplier->name}} @if($supplier->company_name) ({{$supplier->company_name}}) @endif
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="small font-weight-bold text-uppercase">Category</label>
                    <select name="cat_id" class="form-control">
                        <option value="">-- All Categories --</option>
                        @foreach($categories as $cat)
                            <option value="{{$cat->id}}" {{request('cat_id')==$cat->id ? 'selected' : ''}}>{{$cat->title}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <div class="btn-group w-100">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-filter mr-1"></i> Filter</button>
                        <a href="{{route('product.price-list')}}" class="btn btn-secondary"><i class="fas fa-undo mr-1"></i> Reset</a>
                    </div>
                </div>
            </div>
        </form>

        <div class="table-responsive">
            @if(count($products) > 0)
            <table class="table table-bordered table-hover" id="price-list-table" width="100%">
                <thead class="thead-dark">
                    <tr>
                        <th width="40">#</th>
                        <th>Product</th>
                        <th>Category</th>
                        <th>SKU</th>
                        <th class="text-center" style="min-width:170px;">
                            <div class="d-flex align-items-center justify-content-center">
                                <i class="fas fa-tags mr-1"></i>
                                <select id="price-type-select" class="form-control form-control-sm ml-1" style="font-size:11px; max-width:160px;">
                                    <option value="price">💰 Selling Price</option>
                                    <option value="wholesale_price">🏭 Wholesale Price</option>
                                    <option value="retail_price">🏪 Retail Price</option>
                                    <option value="walkin_price">🚶 Walk-in Price</option>
                                    <option value="salesman_price">🤝 Salesman Price</option>
                                </select>
                            </div>
                        </th>
                        <th class="text-center" style="min-width:140px;">
                            <i class="fas fa-receipt mr-1"></i> Cost Price
                        </th>
                        <th class="text-center">Stock</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th>#</th>
                        <th>Product</th>
                        <th>Category</th>
                        <th>SKU</th>
                        <th class="text-center"><small id="price-footer-label" class="text-muted">Selling Price</small></th>
                        <th class="text-center"><small class="text-muted">Cost Price</small></th>
                        <th class="text-center">Stock</th>
                    </tr>
                </tfoot>
                <tbody>
                    @foreach($products as $product)
                    <tr>
                        <td>{{$loop->iteration}}</td>
                        <td>
                            <div class="font-weight-bold text-dark">{{$product->title}}</div>
                            <div class="d-flex flex-wrap align-items-center mt-1" style="gap: 4px;">
                                @if($product->is_featured)
                                    <span class="badge badge-warning" style="font-size:9px;">Featured</span>
                                @endif
                                @if($product->suppliers->count() > 0)
                                    @foreach($product->suppliers as $s)
                                        <span class="badge badge-light border text-info" style="font-size:9px;">
                                            <i class="fas fa-truck-field mr-1"></i>{{$s->name}}
                                        </span>
                                    @endforeach
                                @endif
                            </div>
                        </td>
                        <td><small>{{$product->cat_info->title ?? 'N/A'}}</small></td>
                        <td><small class="text-muted">{{$product->sku ?? '—'}}</small></td>

                        {{-- Selling Price (switchable) --}}
                        <td class="price-cell text-center"
                            data-id="{{$product->id}}"
                            data-price="{{$product->price ?? ''}}"
                            data-wholesale_price="{{$product->wholesale_price ?? ''}}"
                            data-retail_price="{{$product->retail_price ?? ''}}"
                            data-walkin_price="{{$product->walkin_price ?? ''}}"
                            data-salesman_price="{{$product->salesman_price ?? ''}}"
                            data-current-type="price"
                        >
                            <span class="price-display font-weight-bold text-primary">
                                PKR {{number_format($product->price ?? 0, 0)}}
                            </span>
                            <small class="text-muted d-block edit-hint" style="font-size:9px;">dbl-click to edit</small>
                        </td>

                        {{-- Cost Price --}}
                        <td class="cost-price-cell text-center"
                            data-id="{{$product->id}}"
                            data-purchase_price="{{$product->purchase_price ?? ''}}"
                        >
                            @if($product->purchase_price)
                                <span class="cost-display font-weight-bold text-danger">PKR {{number_format($product->purchase_price, 0)}}</span>
                                <div class="text-muted" style="font-size:8px;">Updated: {{$product->updated_at->format('d M, Y')}}</div>
                            @else
                                <span class="cost-display text-muted">&mdash;</span>
                            @endif
                            <small class="text-muted d-block edit-hint" style="font-size:9px;">dbl-click to edit</small>
                        </td>

                        <td class="text-center">
                            @if($product->stock > 0)
                                <span class="badge badge-success">{{$product->stock}}</span>
                            @else
                                <span class="badge badge-danger">{{$product->stock}}</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <span style="float:right">{{$products->links()}}</span>
            @else
                <div class="text-center py-5 text-muted">
                    <i class="fas fa-tags fa-3x mb-3 d-block"></i>
                    No products found.
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('styles')
<link href="{{asset('backend/vendor/datatables/dataTables.bootstrap4.min.css')}}" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/css/bootstrap-select.css" />
<style>
    /* Selling price cells */
    .price-cell { cursor: pointer; }
    .price-cell:hover .price-display { text-decoration: underline dotted; }
    .price-not-set { opacity: 0.7; }

    /* Cost price cells */
    .cost-price-cell { cursor: pointer; }
    .cost-price-cell:hover .cost-display { text-decoration: underline dotted; }

    /* Shared input style */
    .inline-edit-input {
        width: 120px; font-size: 13px; font-weight: 600;
        border-radius: 4px; padding: 3px 7px; outline: none;
        text-align: center;
    }
    .inline-edit-input.price-input  { border: 2px solid #4e73df; }
    .inline-edit-input.cost-input   { border: 2px solid #e74a3b; }

    .cell-saving { opacity: 0.45; pointer-events: none; }
    .saved-flash { color: #1cc88a !important; transition: color 0.8s; }
</style>
@endpush

@push('scripts')
<script src="{{asset('backend/vendor/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('backend/vendor/datatables/dataTables.bootstrap4.min.js')}}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/js/bootstrap-select.min.js"></script>
<script>
$(document).ready(function () {
    $('.selectpicker').selectpicker();

    // ── DataTable ──────────────────────────────────────────────
    $('#price-list-table').DataTable({
        scrollX: false,
        pageLength: 50,
        columnDefs: [
            { orderable: false, targets: [4, 5] }
        ]
    });

    // ── Price type labels ──────────────────────────────────────
    var priceLabels = {
        'price':            '💰 Selling Price',
        'wholesale_price':  '🏭 Wholesale Price',
        'retail_price':     '🏪 Retail Price',
        'walkin_price':     '🚶 Walk-in Price',
        'salesman_price':   '🤝 Salesman Price'
    };

    // ── Dropdown: switch displayed price column ────────────────
    $('#price-type-select').on('change', function () {
        var type = $(this).val();
        var label = priceLabels[type].replace(/^\S+\s/, ''); // strip emoji
        $('#price-footer-label').text(label);

        $('.price-cell').each(function () {
            var $cell = $(this);
            var val   = $cell.data(type);
            var hasVal = (val !== '' && val !== null && val !== undefined && val !== 0 && val !== '0');

            if (hasVal) {
                $cell.find('.price-display')
                     .removeClass('price-not-set')
                     .html('<strong class="text-primary">PKR ' + Number(val).toLocaleString('en-PK') + '</strong>');
            } else {
                // Fallback: show selling price in muted style
                var fallback = $cell.data('price');
                $cell.find('.price-display')
                     .addClass('price-not-set')
                     .html('<span class="text-muted" title="Not set — showing selling price">PKR '
                           + Number(fallback).toLocaleString('en-PK')
                           + ' <i class="fas fa-tag fa-xs"></i></span>');
            }
            $cell.attr('data-current-type', type);
        });
    });

    // ── Generic inline edit helper ─────────────────────────────
    function makeEditable($cell, opts) {
        /*
         * opts: {
         *   getValue: fn($cell) -> current numeric value,
         *   inputClass: string,
         *   priceType: string (field name),
         *   renderSuccess: fn($cell, newVal),
         * }
         */
        if ($cell.find('.inline-edit-input').length) return;

        var productId = $cell.data('id');
        var rawVal    = opts.getValue($cell);

        var $display  = $cell.find(opts.displaySelector);
        var $hint     = $cell.find('.edit-hint');
        $display.hide();
        $hint.hide();

        var $input = $('<input type="number" min="0" step="0.01">')
            .addClass('inline-edit-input ' + opts.inputClass)
            .val(rawVal);

        $cell.append($input);
        $input.focus().select();

        function save() {
            var newVal = parseFloat($input.val());
            if (isNaN(newVal) || newVal < 0) { cancel(); return; }

            $cell.addClass('cell-saving');
            $.ajax({
                url: '/admin/product/' + productId + '/update-price',
                type: 'POST',
                data: {
                    _token: '{{csrf_token()}}',
                    price_type: opts.priceType,
                    value: newVal
                },
                success: function () {
                    $cell.data(opts.priceType, newVal);
                    $input.remove();
                    opts.renderSuccess($cell, newVal);
                    $display.show().addClass('saved-flash');
                    setTimeout(function () { $display.removeClass('saved-flash'); }, 1500);
                    $hint.show();
                    $cell.removeClass('cell-saving');
                },
                error: function (xhr) {
                    alert('Error: ' + (xhr.responseJSON ? xhr.responseJSON.message : 'Could not save.'));
                    cancel();
                }
            });
        }

        function cancel() {
            $input.remove();
            $display.show();
            $hint.show();
            $cell.removeClass('cell-saving');
        }

        $input.on('keydown', function (e) {
            if (e.key === 'Enter')  save();
            if (e.key === 'Escape') cancel();
        }).on('blur', function () { save(); });
    }

    // ── Selling price: double-click ────────────────────────────
    $(document).on('dblclick', '.price-cell', function () {
        var $cell = $(this);
        var type  = $cell.attr('data-current-type') || 'price';

        makeEditable($cell, {
            getValue: function ($c) {
                var v = $c.data(type);
                return (v !== '' && v !== null && v !== undefined) ? v : $c.data('price');
            },
            inputClass:      'price-input',
            priceType:       type,
            displaySelector: '.price-display',
            renderSuccess: function ($c, newVal) {
                $c.data(type, newVal);
                $c.find('.price-display')
                  .removeClass('price-not-set')
                  .html('<strong class="text-primary">PKR ' + newVal.toLocaleString('en-PK') + '</strong>');
            }
        });
    });

    // ── Cost price: double-click ───────────────────────────────
    $(document).on('dblclick', '.cost-price-cell', function () {
        var $cell = $(this);

        makeEditable($cell, {
            getValue: function ($c) {
                var v = $c.data('purchase_price');
                return (v !== '' && v !== null && v !== undefined) ? v : 0;
            },
            inputClass:      'cost-input',
            priceType:       'purchase_price',
            displaySelector: '.cost-display',
            renderSuccess: function ($c, newVal) {
                $c.data('purchase_price', newVal);
                $c.find('.cost-display')
                  .html('<span class="font-weight-bold text-danger">PKR ' + newVal.toLocaleString('en-PK') + '</span>');
            }
        });
    });

});
</script>
@endpush
