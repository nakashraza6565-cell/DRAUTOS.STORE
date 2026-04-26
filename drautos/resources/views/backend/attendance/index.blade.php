@extends('backend.layouts.master')

@section('main-content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            @include('backend.layouts.notification')
        </div>
    </div>
    
    <div class="card shadow mb-4" style="border-radius: 15px; overflow: hidden; border: none;">
        <div class="card-header py-3 d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #1e293b 0%, #334155 100%);">
            <h6 class="m-0 font-weight-bold text-white"><i class="fas fa-users-gear mr-2"></i>Employee Profiles & Attendance</h6>
            <a href="{{route('staff.create')}}" class="btn btn-primary btn-sm rounded-pill px-4 shadow-sm border-0" style="background: #f59e0b; color: #1e293b; font-weight: 700;">
                <i class="fas fa-plus-circle mr-1"></i> Add New Employee Profile
            </a>
        </div>
        <div class="card-body bg-light">
            <div class="table-responsive">
                <table class="table table-hover" id="employee-dataTable" width="100%" cellspacing="0" style="background: white; border-radius: 10px;">
                    <thead style="background: #f8fafc;">
                        <tr>
                            <th class="border-0">ID</th>
                            <th class="border-0">Employee Name</th>
                            <th class="border-0">Position</th>
                            <th class="border-0">Base Salary</th>
                            <th class="border-0">OT Rate (/hr)</th>
                            <th class="border-0">Status</th>
                            <th class="border-0 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($staff as $emp)
                        <tr>
                            <td class="align-middle">#{{$emp->id}}</td>
                            <td class="align-middle">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle mr-3" style="background: #e2e8f0; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; color: #475569;">
                                        {{ substr($emp->name, 0, 1) }}
                                    </div>
                                    <a href="{{route('attendance.show', $emp->id)}}" class="font-weight-bold text-dark hover-primary" style="text-decoration: none;">
                                        {{$emp->name}}
                                    </a>
                                </div>
                            </td>
                            <td class="align-middle"><span class="badge badge-light px-3 py-2" style="color: #64748b; font-weight: 600;">{{strtoupper($emp->role)}}</span></td>
                            <td class="align-middle font-weight-bold text-dark">Rs. {{number_format($emp->base_salary, 2)}}</td>
                            <td class="align-middle text-muted">Rs. {{number_format($emp->overtime_rate, 2)}}</td>
                            <td class="align-middle">
                                <span class="badge badge-{{$emp->status == 'active' ? 'success' : 'danger'}} rounded-pill px-3">
                                    {{ucfirst($emp->status)}}
                                </span>
                            </td>
                            <td class="align-middle text-center">
                                <div class="dropdown no-arrow">
                                    <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink{{$emp->id}}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink{{$emp->id}}">
                                        <a class="dropdown-item" href="{{route('attendance.show', $emp->id)}}"><i class="fas fa-calendar-alt mr-2 text-info"></i> View Attendance</a>
                                        <a class="dropdown-item" href="{{route('staff.edit', $emp->id)}}"><i class="fas fa-edit mr-2 text-warning"></i> Edit Profile</a>
                                        <div class="dropdown-divider"></div>
                                        <form method="POST" action="{{route('staff.destroy', [$emp->id])}}">
                                            @csrf
                                            @method('delete')
                                            <button type="submit" class="dropdown-item text-danger dltBtn" data-id="{{$emp->id}}"><i class="fas fa-trash-alt mr-2"></i> Delete Profile</button>
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link href="{{asset('backend/vendor/datatables/dataTables.bootstrap4.min.css')}}" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.css" />
<style>
    .hover-primary:hover { color: #f59e0b !important; }
    .table td, .table th { vertical-align: middle; border-top: 1px solid #f1f5f9; }
    #employee-dataTable_wrapper .dataTables_filter input {
        border-radius: 20px;
        padding: 5px 15px;
        border: 1px solid #e2e8f0;
    }
</style>
@endpush

@push('scripts')
<script src="{{asset('backend/vendor/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('backend/vendor/datatables/dataTables.bootstrap4.min.js')}}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
<script>
    $('#employee-dataTable').DataTable({
        "order": [[ 0, "desc" ]],
        "pageLength": 25
    });

    $(document).ready(function(){
        $('.dltBtn').click(function(e){
            var form=$(this).closest('form');
            e.preventDefault();
            swal({
                title: "Are you sure?",
                text: "Deleting this employee will also affect their attendance and records!",
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
