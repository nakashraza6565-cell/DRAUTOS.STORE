@extends('backend.layouts.master')

@section('main-content')
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Create New Role</h6>
    </div>
    <div class="card-body">
        <form action="{{route('roles.store')}}" method="POST">
            @csrf
            <div class="form-group">
                <label for="name">Role Name <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control" placeholder="Enter role name" required value="{{old('name')}}">
                @error('name')
                    <span class="text-danger">{{$message}}</span>
                @enderror
            </div>

            <hr>
            <h5 class="mb-3">Assign Permissions</h5>
            
            <div class="row">
                @foreach($groupedPermissions as $module => $perms)
                    <div class="col-md-4 mb-4">
                        <div class="card bg-light">
                            <div class="card-header font-weight-bold text-uppercase" style="font-size: 0.8rem; letter-spacing: 1px;">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input select-all-module" id="module-{{$module}}" data-module="{{$module}}">
                                    <label class="custom-control-label" for="module-{{$module}}">{{$module}}</label>
                                </div>
                            </div>
                            <div class="card-body p-3">
                                @foreach($perms as $permission)
                                    <div class="custom-control custom-checkbox mb-1">
                                        <input type="checkbox" name="permissions[]" value="{{$permission->id}}" class="custom-control-input perms-{{$module}}" id="perm-{{$permission->id}}">
                                        <label class="custom-control-label" for="perm-{{$permission->id}}">{{str_replace('-'.$module, '', $permission->name)}}</label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="form-group mt-4">
                <button type="submit" class="btn btn-primary">Create Role</button>
                <a href="{{route('roles.index')}}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        $('.select-all-module').click(function() {
            let module = $(this).data('module');
            if ($(this).is(':checked')) {
                $('.perms-' + module).prop('checked', true);
            } else {
                $('.perms-' + module).prop('checked', false);
            }
        });
    });
</script>
@endpush
@endsection
