@if($type == 'cash_flow')
    <div class="row">
        <!-- Money In -->
        <div class="col-md-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-success text-white font-weight-bold">
                    <i class="fas fa-arrow-down mr-2"></i> Money In ({{ count($inTransactions) }})
                </div>
                <div class="card-body p-0" style="max-height: 300px; overflow-y: auto;">
                    @if(count($inTransactions) > 0)
                        <ul class="list-group list-group-flush">
                            @foreach($inTransactions as $t)
                                @php
                                    $refName = '';
                                    $refLink = '#';
                                    if ($t->reference_type === 'CustomerLedger' || $t->reference_type === 'App\Models\CustomerLedger' || $t->reference_type === 'App\CustomerLedger') {
                                        $model = \App\Models\CustomerLedger::find($t->reference_id);
                                        if ($model && $model->user) {
                                            $refName = $model->user->name;
                                            $refLink = route('customer_ledger.index', $model->user->id);
                                        }
                                    } elseif ($t->reference_type === 'SupplierLedger' || $t->reference_type === 'App\Models\SupplierLedger' || $t->reference_type === 'App\SupplierLedger') {
                                        $model = \App\Models\SupplierLedger::find($t->reference_id);
                                        if ($model && $model->supplier) {
                                            $refName = $model->supplier->name;
                                            $refLink = route('admin.supplier-ledger.index', $model->supplier->id);
                                        }
                                    }
                                @endphp
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="font-weight-bold">
                                            {{ $t->category ?? 'Payment' }} 
                                            @if($refName)
                                                <small class="text-danger ml-1 text-uppercase">{{ $refName }}</small>
                                            @endif
                                        </div>
                                        <small class="text-muted">
                                            @if($refLink !== '#')
                                                <a href="{{ $refLink }}" class="text-decoration-underline" target="_blank">{{ class_basename($t->reference_type) }} #{{ $t->reference_id }}</a>
                                            @else
                                                {{ class_basename($t->reference_type) }} #{{ $t->reference_id }}
                                            @endif
                                        </small>
                                    </div>
                                    <span class="badge badge-success badge-pill" style="font-size: 14px;">Rs. {{ number_format($t->amount) }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="p-3 text-center text-muted">No money in records for this date.</div>
                    @endif
                </div>
                <div class="card-footer bg-light text-right font-weight-bold text-success">
                    Total: Rs. {{ number_format($inTransactions->sum('amount')) }}
                </div>
            </div>
        </div>

        <!-- Money Out -->
        <div class="col-md-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-danger text-white font-weight-bold">
                    <i class="fas fa-arrow-up mr-2"></i> Money Out ({{ count($outTransactions) }})
                </div>
                <div class="card-body p-0" style="max-height: 300px; overflow-y: auto;">
                    @if(count($outTransactions) > 0)
                        <ul class="list-group list-group-flush">
                            @foreach($outTransactions as $t)
                                @php
                                    $refName = '';
                                    $refLink = '#';
                                    if ($t->reference_type === 'CustomerLedger' || $t->reference_type === 'App\Models\CustomerLedger' || $t->reference_type === 'App\CustomerLedger') {
                                        $model = \App\Models\CustomerLedger::find($t->reference_id);
                                        if ($model && $model->user) {
                                            $refName = $model->user->name;
                                            $refLink = route('customer_ledger.index', $model->user->id);
                                        }
                                    } elseif ($t->reference_type === 'SupplierLedger' || $t->reference_type === 'App\Models\SupplierLedger' || $t->reference_type === 'App\SupplierLedger') {
                                        $model = \App\Models\SupplierLedger::find($t->reference_id);
                                        if ($model && $model->supplier) {
                                            $refName = $model->supplier->name;
                                            $refLink = route('admin.supplier-ledger.index', $model->supplier->id);
                                        }
                                    }
                                @endphp
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="font-weight-bold">
                                            {{ $t->category ?? 'Payment' }}
                                            @if($refName)
                                                <small class="text-danger ml-1 text-uppercase">{{ $refName }}</small>
                                            @endif
                                        </div>
                                        <small class="text-muted">
                                            @if($refLink !== '#')
                                                <a href="{{ $refLink }}" class="text-decoration-underline" target="_blank">{{ class_basename($t->reference_type) }} #{{ $t->reference_id }}</a>
                                            @else
                                                {{ class_basename($t->reference_type) }} #{{ $t->reference_id }}
                                            @endif
                                        </small>
                                    </div>
                                    <span class="badge badge-danger badge-pill" style="font-size: 14px;">Rs. {{ number_format($t->amount) }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="p-3 text-center text-muted">No money out records for this date.</div>
                    @endif
                </div>
                <div class="card-footer bg-light text-right font-weight-bold text-danger">
                    Total: Rs. {{ number_format($outTransactions->sum('amount')) }}
                </div>
            </div>
        </div>
    </div>
@elseif($type == 'incoming_sales')
    <div class="row">
        <!-- Incoming Goods -->
        <div class="col-md-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-secondary text-white font-weight-bold">
                    <i class="fas fa-boxes mr-2"></i> Incoming Goods ({{ count($incoming) }})
                </div>
                <div class="card-body p-0" style="max-height: 300px; overflow-y: auto;">
                    @if(count($incoming) > 0)
                        <ul class="list-group list-group-flush">
                            @foreach($incoming as $in)
                                @php
                                    $amount = $in->items->sum('total_cost') + ($in->shipping_cost ?? 0);
                                @endphp
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="font-weight-bold">Supplier: {{ $in->supplier ? $in->supplier->name : 'N/A' }}</div>
                                        <small class="text-muted">Batch #{{ $in->batch_number ?? $in->id }} ({{ $in->items->count() }} items)</small>
                                    </div>
                                    <span class="badge badge-secondary badge-pill" style="font-size: 14px;">Rs. {{ number_format($amount) }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="p-3 text-center text-muted">No incoming goods for this date.</div>
                    @endif
                </div>
                <div class="card-footer bg-light text-right font-weight-bold text-secondary">
                    Total: Rs. {{ number_format($incoming->sum(function($in) { return $in->items->sum('total_cost') + ($in->shipping_cost ?? 0); })) }}
                </div>
            </div>
        </div>

        <!-- Customer Sales -->
        <div class="col-md-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-warning text-dark font-weight-bold">
                    <i class="fas fa-shopping-cart mr-2"></i> Customer Sales ({{ count($sales) }})
                </div>
                <div class="card-body p-0" style="max-height: 300px; overflow-y: auto;">
                    @if(count($sales) > 0)
                        <ul class="list-group list-group-flush">
                            @foreach($sales as $order)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="font-weight-bold">{{ $order->user ? $order->user->name : ($order->first_name . ' ' . $order->last_name) }}</div>
                                        <small class="text-muted">Order #{{ $order->order_number }}</small>
                                    </div>
                                    <span class="badge badge-warning badge-pill" style="font-size: 14px;">Rs. {{ number_format($order->total_amount) }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="p-3 text-center text-muted">No sales records for this date.</div>
                    @endif
                </div>
                <div class="card-footer bg-light text-right font-weight-bold text-warning" style="color: #b45309 !important;">
                    Total: Rs. {{ number_format($sales->sum('total_amount')) }}
                </div>
            </div>
        </div>
    </div>
@endif
