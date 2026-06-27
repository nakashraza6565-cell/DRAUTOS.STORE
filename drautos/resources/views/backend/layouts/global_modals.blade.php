@php
    $accounts = \App\Models\FinancialAccount::where('status', 'active')->get();
    $recent_expense_titles = \App\Models\Expense::select('title')
        ->groupBy('title')
        ->orderByRaw('COUNT(*) DESC')
        ->limit(20)
        ->pluck('title');
    $staffAccId = class_exists('\App\Models\FinancialAccount') ? \App\Models\FinancialAccount::getStaffAccount() : null;
@endphp

<!-- Quick Expense Modal -->
<div class="modal fade" id="quickExpenseModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 15px;">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title font-weight-bold"><i class="fas fa-minus-circle mr-2"></i> Record Quick Expense</h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form action="{{ route('expenses.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="form-group mb-3">
                        <label class="small font-weight-bold">Expense Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" list="expense-titles" class="form-control border-0 bg-light" placeholder="Type or select expense title (e.g. Office tea, Rent)" required autofocus autocomplete="off">
                        <datalist id="expense-titles">
                            @foreach($recent_expense_titles as $title)
                                <option value="{{ $title }}">
                            @endforeach
                        </datalist>
                    </div>

                    <div class="form-group mb-3">
                        <label class="small font-weight-bold">Amount (Rs.) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-light border-0">Rs.</span>
                            </div>
                            <input type="number" step="0.01" name="amount" class="form-control form-control-lg border-0 bg-light" placeholder="0.00" required>
                        </div>
                    </div>
                    
                    <div class="form-group mb-3">
                        <label class="small font-weight-bold">Deduct From <span class="text-danger">*</span></label>
                        <select name="financial_account_id" class="form-control border-0 bg-light" required>
                            <option value="">-- Select Account --</option>
                            @foreach($accounts as $acc)
                                <option value="{{ $acc->id }}" {{ $acc->id == ($staffAccId ?? null) ? 'selected' : '' }}>
                                    {{ $acc->name }} (Bal: Rs. {{ number_format($acc->current_balance ?? 0) }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <label class="small font-weight-bold">Date <span class="text-danger">*</span></label>
                        <input type="date" name="date" class="form-control border-0 bg-light" value="{{ date('Y-m-d') }}" required>
                    </div>

                    <div class="form-group mb-0">
                        <label class="small font-weight-bold">Description</label>
                        <textarea name="description" class="form-control border-0 bg-light" rows="3" placeholder="What was this expense for? (Optional)"></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger rounded-pill px-4 shadow">SAVE EXPENSE</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Quick Bilty (Delivery Receipt) Modal -->
<div class="modal fade" id="quickBiltyModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title font-weight-bold"><i class="fas fa-truck mr-2 text-primary"></i> Create Delivery Receipt (Bilty)</h5>
                <button type="button" class="close text-muted" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('delivery-receipts.store') }}" method="POST" target="_blank" onsubmit="var f = this; setTimeout(function(){ $('#quickBiltyModal').modal('hide'); f.reset(); }, 500);">
                @csrf
                <div class="modal-body px-4 pt-3" style="max-height: 70vh; overflow-y: auto;">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="small font-weight-bold">Date <span class="text-danger">*</span></label>
                            <input type="date" name="date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="small font-weight-bold">Courier Company</label>
                            <input type="text" name="courier_company" id="bilty_courier" class="form-control" placeholder="e.g. TCS, Leopard">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="small font-weight-bold">Sender Name</label>
                            <input type="text" name="sender_name" class="form-control" value="Danyal Autos (Lahore)" readonly>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="small font-weight-bold">Receiver Name (Customer) <span class="text-danger">*</span></label>
                            <input type="hidden" name="customer_id" id="bilty_customer_id">
                            <input type="text" name="receiver_name" list="bilty-customers" id="bilty_receiver" class="form-control" placeholder="Type or select customer" required autocomplete="off">
                            <datalist id="bilty-customers">
                                @php
                                    $customers = \App\User::where('role','user')->get();
                                @endphp
                                @foreach($customers as $cust)
                                    <option value="{{$cust->name}}" data-id="{{$cust->id}}"></option>
                                @endforeach
                            </datalist>
                        </div>
                        <div class="col-12 col-md-6 mb-3">
                            <label class="small font-weight-bold">Address</label>
                            <textarea name="address" id="bilty_address" class="form-control" rows="1"></textarea>
                        </div>
                        <div class="col-12 col-md-6 mb-3">
                            <label class="small font-weight-bold">City</label>
                            <input type="text" name="city" id="bilty_city" class="form-control">
                        </div>
                        <div class="col-6 col-md-4 mb-3">
                            <label class="small font-weight-bold">No. of Cartons</label>
                            <input type="number" name="no_of_cartons" class="form-control" value="0" min="0">
                        </div>
                        <div class="col-6 col-md-4 mb-3">
                            <label class="small font-weight-bold">No. of Bags</label>
                            <input type="number" name="no_of_bags" class="form-control" value="0" min="0">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 pb-4 px-4 justify-content-between">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 shadow">SAVE & PRINT</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const receiverInput = document.getElementById('bilty_receiver');
    if (receiverInput) {
        receiverInput.addEventListener('change', function() {
            const val = this.value;
            const options = document.getElementById('bilty-customers').options;
            let customerId = null;
            for(let i=0; i<options.length; i++) {
                if(options[i].value === val) {
                    customerId = options[i].getAttribute('data-id');
                    break;
                }
            }
            
            document.getElementById('bilty_customer_id').value = customerId || '';

            if(customerId) {
                fetch(`/admin/delivery-receipts/get-customer/${customerId}`)
                .then(res => res.json())
                .then(data => {
                    if(data.status) {
                        if(data.address) document.getElementById('bilty_address').value = data.address;
                        if(data.city) document.getElementById('bilty_city').value = data.city;
                        if(data.courier_company) document.getElementById('bilty_courier').value = data.courier_company;
                    }
                });
            }
        });
    }
});
</script>
