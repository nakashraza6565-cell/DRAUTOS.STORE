@extends('backend.layouts.master')

@section('main-content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            @include('backend.layouts.notification')
        </div>
    </div>

    <!-- Employee Summary Header -->
    <div class="card shadow mb-4" style="border-radius: 15px; border: none; overflow: hidden;">
        <div class="card-body p-0">
            <div class="row no-gutters">
                <div class="col-md-3 bg-dark p-4 d-flex flex-column align-items-center justify-content-center text-white" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);">
                    <div class="avatar-large mb-3" style="width: 100px; height: 100px; border-radius: 50%; background: #f59e0b; color: #0f172a; display: flex; align-items: center; justify-content: center; font-size: 3rem; font-weight: 900; border: 4px solid rgba(255,255,255,0.2);">
                        {{ substr($user->name, 0, 1) }}
                    </div>
                    <h4 class="font-weight-bold mb-0 text-center">{{$user->name}}</h4>
                    <span class="badge badge-warning mt-2 px-3">{{strtoupper($user->role)}}</span>
                    <div class="mt-4 w-100 text-center">
                        <small class="text-white-50 d-block mb-1">Base Salary</small>
                        <h5 class="font-weight-bold">Rs. {{number_format($user->base_salary, 2)}}</h5>
                    </div>
                </div>
                <div class="col-md-9 p-4 bg-white">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="m-0 font-weight-bold text-primary">Attendance History & Calculations</h5>
                        <div class="btn-group">
                            <button type="button" class="btn btn-outline-primary btn-sm px-3" data-toggle="modal" data-target="#manualEntryModal">
                                <i class="fas fa-plus-circle mr-1"></i> Add Manual Entry
                            </a>
                            <a href="{{ route('attendance.export', ['id' => $user->id, 'start_date' => $start_date, 'end_date' => $end_date]) }}" class="btn btn-success btn-sm px-3 ml-2">
                                <i class="fas fa-file-csv mr-1"></i> Download CSV
                            </a>
                        </div>
                    </div>

                    <!-- Filters -->
                    <form action="{{route('attendance.show', $user->id)}}" method="GET" class="mb-4 bg-light p-3 rounded-lg border">
                        <div class="row align-items-end">
                            <div class="col-md-4">
                                <label class="small font-weight-bold">Start Date</label>
                                <input type="date" name="start_date" class="form-control" value="{{$start_date}}">
                            </div>
                            <div class="col-md-4">
                                <label class="small font-weight-bold">End Date</label>
                                <input type="date" name="end_date" class="form-control" value="{{$end_date}}">
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-primary btn-block"><i class="fas fa-filter mr-1"></i> Filter Records</button>
                            </div>
                        </div>
                    </form>

                    <!-- Stats Cards -->
                    <div class="row">
                        <div class="col-md-3">
                            <div class="p-3 bg-primary text-white rounded-lg shadow-sm">
                                <div class="small opacity-75">Work Period Total Hours</div>
                                <div class="h3 font-weight-bold mb-0">{{$total_worked_hours}} hrs</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="p-3 bg-info text-white rounded-lg shadow-sm">
                                <div class="small opacity-75">Total Overtime Hours</div>
                                <div class="h3 font-weight-bold mb-0 text-white">{{$total_overtime_hours}} hrs</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="p-3 bg-success text-white rounded-lg shadow-sm">
                                <div class="small opacity-75">Calculated Bonus (OT)</div>
                                <div class="h3 font-weight-bold mb-0 text-white">Rs. {{number_format($total_overtime_hours * $user->overtime_rate, 2)}}</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="p-3 bg-dark text-white rounded-lg shadow-sm d-flex flex-column justify-content-center align-items-center">
                                <a href="{{ route('payroll.show', $user->id) }}" class="btn btn-warning btn-sm btn-block mb-1 font-weight-bold">Pay Salary</a>
                                <a href="{{ route('payroll.ledger', $user->id) }}" class="btn btn-outline-light btn-sm btn-block py-0" style="font-size: 0.7rem;">View Ledger</a>
                            </div>
                        </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Attendance Records Table -->
    <div class="card shadow mb-4" style="border-radius: 15px; border: none; overflow: hidden;">
        <div class="card-header py-3 bg-white border-bottom">
            <h6 class="m-0 font-weight-bold text-dark">Records from {{Carbon\Carbon::parse($start_date)->format('d M, Y')}} to {{Carbon\Carbon::parse($end_date)->format('d M, Y')}}</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="attendance-dataTable" width="100%" cellspacing="0">
                    <thead class="bg-light">
                        <tr>
                            <th>Date</th>
                            <th>Day</th>
                            <th>Clock In</th>
                            <th>Clock Out</th>
                            <th>Total Worked</th>
                            <th>Overtime</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($attendances as $att)
                        @php $date = Carbon\Carbon::parse($att->date); @endphp
                        <tr>
                            <td>{{$date->format('Y-m-d')}}</td>
                            <td>{{$date->format('l')}}</td>
                            <td>{{$att->clock_in ?? '--:--'}}</td>
                            <td>{{$att->clock_out ?? '--:--'}}</td>
                            <td><span class="font-weight-bold text-primary">{{$att->total_hours}} hrs</span></td>
                            <td><span class="text-success">{{$att->overtime_hours}} hrs</span></td>
                            <td>
                                @php 
                                    $color = 'success';
                                    if($att->status == 'absent') $color = 'danger';
                                    if($att->status == 'late') $color = 'warning';
                                    if($att->status == 'on_leave') $color = 'info';
                                @endphp
                                <span class="badge badge-{{$color}} px-3">{{ucfirst($att->status)}}</span>
                                @if($att->is_manual) <i class="fas fa-hand-pointer text-muted ml-1" title="Manual Entry"></i> @endif
                            </td>
                            <td class="d-flex">
                                <button class="btn btn-warning btn-sm rounded-circle mr-2 editAttendanceBtn" 
                                    style="height:30px; width:30px;"
                                    data-id="{{$att->id}}"
                                    data-date="{{$att->date}}"
                                    data-in="{{$att->clock_in}}"
                                    data-out="{{$att->clock_out}}"
                                    data-status="{{$att->status}}"
                                    data-notes="{{$att->notes}}"
                                    data-toggle="modal"
                                    data-target="#editAttendanceModal">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form method="POST" action="{{route('attendance.destroy',[$att->id])}}">
                                    @csrf 
                                    @method('delete')
                                    <button class="btn btn-danger btn-sm rounded-circle dltBtn" style="height:30px; width:30px;"><i class="fas fa-trash-alt"></i></button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Edit Attendance Modal -->
<div class="modal fade" id="editAttendanceModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title font-weight-bold text-dark"><i class="fas fa-edit mr-2"></i>Edit Attendance Record</h5>
                <button type="button" class="close text-dark" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editAttendanceForm" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-body">
                    <div class="form-group">
                        <label>Date</label>
                        <input type="date" name="date" id="edit_date" class="form-control" required>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Clock In</label>
                            <input type="time" name="clock_in" id="edit_clock_in" class="form-control">
                        </div>
                        <div class="form-group col-md-6">
                            <label>Clock Out</label>
                            <input type="time" name="clock_out" id="edit_clock_out" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" id="edit_status" class="form-control" required>
                            <option value="present">Present</option>
                            <option value="late">Late</option>
                            <option value="absent">Absent</option>
                            <option value="on_leave">On Leave</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Notes (Optional)</label>
                        <textarea name="notes" id="edit_notes" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-warning shadow-sm border-0 font-weight-bold text-dark">Update Record</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Manual Entry Modal -->
<div class="modal fade" id="manualEntryModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title" id="exampleModalLabel">Add Manual Attendance Record</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{route('attendance.store')}}" method="POST">
                @csrf
                <input type="hidden" name="user_id" value="{{$user->id}}">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Date</label>
                        <input type="date" name="date" class="form-control" required value="{{date('Y-m-d')}}">
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Clock In</label>
                            <input type="time" name="clock_in" class="form-control">
                        </div>
                        <div class="form-group col-md-6">
                            <label>Clock Out</label>
                            <input type="time" name="clock_out" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" class="form-control" required>
                            <option value="present">Present</option>
                            <option value="late">Late</option>
                            <option value="absent">Absent</option>
                            <option value="on_leave">On Leave</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Notes (Optional)</label>
                        <textarea name="notes" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary shadow-sm border-0" style="background: #1e293b;">Save Record</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('styles')
<link href="{{asset('backend/vendor/datatables/dataTables.bootstrap4.min.css')}}" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.css" />
@endpush

@push('scripts')
<script src="{{asset('backend/vendor/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('backend/vendor/datatables/dataTables.bootstrap4.min.js')}}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
<script>
    $('#attendance-dataTable').DataTable({
        "order": [[ 0, "desc" ]]
    });

    $(document).ready(function(){
        $('.editAttendanceBtn').click(function(){
            var id = $(this).data('id');
            var date = $(this).data('date');
            var clock_in = $(this).data('in');
            var clock_out = $(this).data('out');
            var status = $(this).data('status');
            var notes = $(this).data('notes');

            $('#edit_date').val(date);
            $('#edit_clock_in').val(clock_in);
            $('#edit_clock_out').val(clock_out);
            $('#edit_status').val(status);
            $('#edit_notes').val(notes);
            
            var url = "{{ route('attendance.update', ':id') }}";
            url = url.replace(':id', id);
            $('#editAttendanceForm').attr('action', url);
        });

        $('.dltBtn').click(function(e){
            var form=$(this).closest('form');
            e.preventDefault();
            swal({
                title: "Are you sure?",
                text: "This record will be permanently deleted!",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            })
            .then((willDelete) => {
                if (willDelete) {
                   form.submit();
                }
            });
        });
    });
</script>
@endpush
