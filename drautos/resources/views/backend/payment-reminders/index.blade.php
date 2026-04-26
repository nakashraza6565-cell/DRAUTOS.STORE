@extends('backend.layouts.master')

@section('main-content')
<div class="container-fluid">
    
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Payment Reminders</h1>
        <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addReminderModal">
            <i class="fas fa-plus"></i> Add Reminder
        </button>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                To Receive Today</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">PKR {{ number_format($receivablesToday ?? 0, 2) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-download fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                To Pay Today</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">PKR {{ number_format($payablesToday ?? 0, 2) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-upload fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Overdue</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $overdue->count() ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Upcoming</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $upcoming->count() ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs -->
    <ul class="nav nav-tabs mb-3" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" data-toggle="tab" href="#today">Due Today <span class="badge badge-primary">{{ $dueToday->count() ?? 0 }}</span></a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="tab" href="#overdue">Overdue <span class="badge badge-danger">{{ $overdue->count() ?? 0 }}</span></a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="tab" href="#upcoming">Upcoming <span class="badge badge-info">{{ $upcoming->count() ?? 0 }}</span></a>
        </li>
    </ul>

    <!-- Tab content -->
    <div class="tab-content">
        <!-- Due Today Tab -->
        <div id="today" class="tab-pane fade show active">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Payments Due Today</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        @if($dueToday && count($dueToday) > 0)
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Type</th>
                                    <th>Party</th>
                                    <th>Reference</th>
                                    <th>Amount</th>
                                    <th>Paid</th>
                                    <th>Balance</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($dueToday as $reminder)
                                <tr>
                                    <td>
                                        <span class="badge badge-{{ $reminder->type == 'receivable' ? 'success' : 'danger' }}">
                                            {{ ucfirst($reminder->type) }}
                                        </span>
                                    </td>
                                    <td>{{ $reminder->party->name ?? 'N/A' }}</td>
                                    <td>{{ $reminder->reference_number }}</td>
                                    <td>PKR {{ number_format($reminder->amount, 2) }}</td>
                                    <td>PKR {{ number_format($reminder->paid_amount, 2) }}</td>
                                    <td>PKR {{ number_format($reminder->amount - $reminder->paid_amount, 2) }}</td>
                                    <td><span class="badge badge-warning">{{ $reminder->status }}</span></td>
                                    <td>
                                        <div class="btn-group">
                                            <button class="btn btn-sm btn-success" onclick="recordPayment({{ $reminder->id }})" title="Pay">
                                                <i class="fas fa-money-bill"></i>
                                            </button>
                                            <button class="btn btn-sm btn-primary" onclick="editReminder({{ $reminder->id }})" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-danger" onclick="deleteReminder({{ $reminder->id }})" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                            @if($reminder->party && $reminder->party->phone)
                                            <button class="btn btn-sm btn-info" onclick="sendWhatsApp({{ $reminder->id }})" title="WhatsApp">
                                                <i class="fab fa-whatsapp"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @else
                        <p class="text-center">No payments due today</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Overdue Tab -->
        <div id="overdue" class="tab-pane fade">
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-danger text-white">
                    <h6 class="m-0 font-weight-bold">Overdue Payments</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        @if($overdue && count($overdue) > 0)
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Due Date</th>
                                    <th>Type</th>
                                    <th>Party</th>
                                    <th>Reference</th>
                                    <th>Amount</th>
                                    <th>Balance</th>
                                    <th>Days Overdue</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($overdue as $reminder)
                                <tr class="bg-light-danger">
                                    <td>{{ $reminder->due_date->format('d M Y') }}</td>
                                    <td>
                                        <span class="badge badge-{{ $reminder->type == 'receivable' ? 'success' : 'danger' }}">
                                            {{ ucfirst($reminder->type) }}
                                        </span>
                                    </td>
                                    <td>{{ $reminder->party->name ?? 'N/A' }}</td>
                                    <td>{{ $reminder->reference_number }}</td>
                                    <td>PKR {{ number_format($reminder->amount, 2) }}</td>
                                    <td>PKR {{ number_format($reminder->amount - $reminder->paid_amount, 2) }}</td>
                                    <td><span class="badge badge-danger">{{ now()->diffInDays($reminder->due_date) }} days</span></td>
                                    <td>
                                        <div class="btn-group">
                                            <button class="btn btn-sm btn-success" onclick="recordPayment({{ $reminder->id }})" title="Pay">
                                                <i class="fas fa-money-bill"></i>
                                            </button>
                                            <button class="btn btn-sm btn-primary" onclick="editReminder({{ $reminder->id }})" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-danger" onclick="deleteReminder({{ $reminder->id }})" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @else
                        <p class="text-center">No overdue payments</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Upcoming Tab -->
        <div id="upcoming" class="tab-pane fade">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Upcoming Payments</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        @if($upcoming && count($upcoming) > 0)
                        <table class="table table-bordered">
                            <thead>
                                    <tr>
                                        <th>Due Date</th>
                                        <th>Type</th>
                                        <th>Party</th>
                                        <th>Reference</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                            </thead>
                            <tbody>
                                @foreach($upcoming as $reminder)
                                <tr>
                                    <td>{{ $reminder->due_date->format('d M Y') }}</td>
                                    <td>
                                        <span class="badge badge-{{ $reminder->type == 'receivable' ? 'success' : 'danger' }}">
                                            {{ ucfirst($reminder->type) }}
                                        </span>
                                    </td>
                                    <td>{{ $reminder->party->name ?? 'N/A' }}</td>
                                    <td>{{ $reminder->reference_number }}</td>
                                    <td>PKR {{ number_format($reminder->amount, 2) }}</td>
                                    <td><span class="badge badge-info">{{ $reminder->status }}</span></td>
                                    <td>
                                        <div class="btn-group">
                                            <button class="btn btn-sm btn-primary" onclick="editReminder({{ $reminder->id }})" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-danger" onclick="deleteReminder({{ $reminder->id }})" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @else
                        <p class="text-center">No upcoming payments</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Reminder Modal -->
<div class="modal fade" id="addReminderModal" tabindex="-1" role="dialog" aria-labelledby="addReminderModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addReminderModalLabel">Add New Payment Reminder</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="addReminderForm" method="POST" action="{{ route('payment-reminders.store') }}">
                <div class="modal-body">
                    @csrf
                    <input type="hidden" name="party_type" id="hidden_party_type" value="App\\User">
                    <input type="hidden" name="party_id" id="hidden_party_id">

                    <div class="form-group">
                        <label>Type</label>
                        <select name="type" id="reminder_type" class="form-control" required>
                            <option value="receivable">Receivable (To Receive)</option>
                            <option value="payable">Payable (To Pay)</option>
                        </select>
                    </div>

                    <div class="form-group" id="customer_group">
                        <label>Customer</label>
                        <select id="customer_select" class="form-control select2-modal">
                            <option value="">Select Customer</option>
                            @foreach($customers as $customer)
                                <option value="{{$customer->id}}">{{$customer->name}}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group" id="supplier_group" style="display:none;">
                        <label>Supplier</label>
                        <select id="supplier_select" class="form-control select2-modal">
                            <option value="">Select Supplier</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{$supplier->id}}">{{$supplier->name}}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Reference Number (Optional)</label>
                        <input type="text" name="reference_number" class="form-control" placeholder="Invoice #, Bill #, etc.">
                    </div>

                    <div class="form-group">
                        <label>Amount</label>
                        <input type="number" name="amount" class="form-control" step="0.01" min="0" required>
                    </div>

                    <div class="form-group">
                        <label>Due Date</label>
                        <input type="date" name="due_date" class="form-control" required value="{{ date('Y-m-d') }}">
                    </div>

                    <div class="form-group">
                        <label>Notes</label>
                        <textarea name="notes" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Reminder</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Reminder Modal -->
<div class="modal fade" id="editReminderModal" tabindex="-1" role="dialog" aria-labelledby="editReminderModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editReminderModalLabel">Edit Payment Reminder</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editReminderForm">
                <div class="modal-body">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="reminder_id" id="edit_reminder_id">
                    
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" id="edit_status" class="form-control" required>
                            <option value="pending">Pending</option>
                            <option value="partially_paid">Partially Paid</option>
                            <option value="completed">Completed</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Amount (Total)</label>
                        <input type="number" name="amount" id="edit_amount" class="form-control" step="0.01" min="0" required>
                    </div>

                    <div class="form-group">
                        <label>Paid Amount (So far)</label>
                        <input type="number" name="paid_amount" id="edit_paid_amount" class="form-control" step="0.01" min="0">
                    </div>

                    <div class="form-group">
                        <label>Due Date</label>
                        <input type="date" name="due_date" id="edit_due_date" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label>Notes</label>
                        <textarea name="notes" id="edit_notes" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update Reminder</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Toggle Party Type
$(document).on('change', '#reminder_type', function() {
    const type = $(this).val();
    if (type === 'receivable') {
        $('#customer_group').show();
        $('#supplier_group').hide();
        $('#hidden_party_type').val('App\\User');
    } else {
        $('#customer_group').hide();
        $('#supplier_group').show();
        $('#hidden_party_type').val('App\\Models\\Supplier');
    }
});

// Sync selected IDs to hidden fields
$(document).on('change', '#customer_select', function() {
    if ($('#reminder_type').val() === 'receivable') {
        $('#hidden_party_id').val($(this).val());
    }
});

$(document).on('change', '#supplier_select', function() {
    if ($('#reminder_type').val() === 'payable') {
        $('#hidden_party_id').val($(this).val());
    }
});

$(document).ready(function() {
    $('.select2-modal').select2({
        dropdownParent: $('#addReminderModal'),
        width: '100%'
    });

    $(document).on('submit', '#addReminderForm', function(e) {
        e.preventDefault();
        
        const partyId = $('#hidden_party_id').val();
        if (!partyId) {
            Swal.fire('Error!', 'Please select a customer or supplier', 'error');
            return;
        }

        $.ajax({
            url: $(this).attr('action'),
            type: "POST",
            data: $(this).serialize(),
            success: function(response) {
                if(response.success) {
                    $('#addReminderModal').modal('hide');
                    Swal.fire('Success!', response.message, 'success').then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire('Error!', response.message || 'Failed to create reminder', 'error');
                }
            },
            error: function(xhr) {
                let errorMsg = 'Failed to create reminder';
                if (xhr.status === 422) {
                    let errors = xhr.responseJSON.errors;
                    errorMsg = Object.values(errors).flat().join('\n');
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                Swal.fire('Error!', errorMsg, 'error');
            }
        });
    });
});

function recordPayment(reminderId) {
    // Open payment modal
    Swal.fire({
        title: 'Record Payment',
        html: `
            <div class="form-group">
                <label>Payment Amount</label>
                <input type="number" id="payment_amount" class="swal2-input" placeholder="Enter amount">
            </div>
            <div class="form-group">
                <label>Payment Date</label>
                <input type="date" id="payment_date" class="swal2-input" value="${new Date().toISOString().split('T')[0]}">
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'Record',
        preConfirm: () => {
            return {
                payment_amount: document.getElementById('payment_amount').value,
                payment_date: document.getElementById('payment_date').value
            }
        }
    }).then((result) => {
        if (result.isConfirmed) {
            // Submit via AJAX
            $.ajax({
                url: `/admin/payment-reminders/${reminderId}/payment`,
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    payment_amount: result.value.payment_amount,
                    payment_date: result.value.payment_date
                },
                success: function(response) {
                    Swal.fire('Success!', 'Payment recorded successfully', 'success').then(() => {
                        location.reload();
                    });
                },
                error: function() {
                    Swal.fire('Error!', 'Failed to record payment', 'error');
                }
            });
        }
    });
}

function sendWhatsApp(reminderId) {
    Swal.fire({
        title: 'Send WhatsApp Reminder?',
        text: 'This will send a payment reminder via WhatsApp',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, Send'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `/admin/payment-reminders/${reminderId}/whatsapp`,
                method: 'POST',
                data: { _token: '{{ csrf_token() }}' },
                success: function(response) {
                    Swal.fire('Sent!', 'WhatsApp reminder sent successfully', 'success');
                },
                error: function() {
                    Swal.fire('Error!', 'Failed to send WhatsApp', 'error');
                }
            });
        }
    });
}

function editReminder(id) {
    $.ajax({
        url: `/admin/payment-reminders/${id}`,
        type: 'GET',
        success: function(data) {
            $('#edit_reminder_id').val(data.id);
            $('#edit_status').val(data.status);
            $('#edit_amount').val(data.amount);
            $('#edit_paid_amount').val(data.paid_amount);
            $('#edit_due_date').val(data.due_date.split('T')[0]);
            $('#edit_notes').val(data.notes);
            $('#editReminderModal').modal('show');
        }
    });
}

$('#editReminderForm').on('submit', function(e) {
    e.preventDefault();
    let id = $('#edit_reminder_id').val();
    $.ajax({
        url: `/admin/payment-reminders/${id}`,
        type: 'POST',
        data: $(this).serialize(),
        success: function(response) {
            Swal.fire('Updated!', 'Reminder updated successfully', 'success').then(() => {
                location.reload();
            });
        },
        error: function(xhr) {
            Swal.fire('Error!', 'Failed to update reminder', 'error');
        }
    });
});

function deleteReminder(id) {
    Swal.fire({
        title: 'Are you sure?',
        text: "This will also revert the balance added to the customer/supplier account if the payment is pending!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `/admin/payment-reminders/${id}`,
                type: 'DELETE',
                data: { _token: '{{ csrf_token() }}' },
                success: function(response) {
                    Swal.fire('Deleted!', response.message, 'success').then(() => {
                        location.reload();
                    });
                },
                error: function(xhr) {
                    Swal.fire('Error!', 'Failed to delete reminder', 'error');
                }
            });
        }
    });
}
</script>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
.select2-container {
    width: 100% !important;
    z-index: 99999;
}
.select2-container .select2-selection--single {
    height: 38px !important;
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
@endpush
