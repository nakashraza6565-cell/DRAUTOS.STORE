@extends('backend.layouts.master')

@section('main-content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">WhatsApp API Integration</h1>
    </div>

    <div class="row">
        <div class="col-md-8 mx-auto">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <div class="card shadow mb-4 border-left-success">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-success">Update WhatsApp Credentials</h6>
                    <img src="https://upload.wikimedia.org/wikipedia/commons/6/6b/WhatsApp.svg" width="30" alt="WA">
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.whatsapp-settings.update') }}">
                        @csrf
                        
                        <div class="form-group row">
                            <label for="instance_id" class="col-sm-3 col-form-label font-weight-bold">Instance ID</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="instance_id" name="instance_id" 
                                    value="{{ env('WHATSAPP_INSTANCE_ID') }}" placeholder="e.g. 69A2A751D79C1" required>
                                <small class="form-text text-muted">Obtained from your WhatsApp Provider Dashboard (wa2.shnaveed.com)</small>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="access_token" class="col-sm-3 col-form-label font-weight-bold">Access Token</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="access_token" name="access_token" 
                                    value="{{ env('WHATSAPP_ACCESS_TOKEN') }}" placeholder="e.g. 697e616d6b5ba" required>
                                <small class="form-text text-muted">The secure token linked to your Instance ID</small>
                            </div>
                        </div>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle mr-2"></i>
                            <strong>Note:</strong> These settings will update your <code>.env</code> file directly. 
                            The application cache will be cleared automatically to apply the changes.
                        </div>

                        <hr>

                        <div class="d-flex justify-content-center">
                            <button type="submit" class="btn btn-success btn-lg px-5 shadow-sm">
                                <i class="fas fa-save mr-2"></i> Update Credentials
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">How to Re-connect Instance?</h6>
                </div>
                <div class="card-body">
                    <ol class="mb-0">
                        <li>Visit <a href="https://wa2.shnaveed.com/" target="_blank">wa2.shnaveed.com</a></li>
                        <li>Log in with your existing account.</li>
                        <li>Go to the <strong>Instances</strong> section.</li>
                        <li>Select Instance <strong>{{ env('WHATSAPP_INSTANCE_ID') }}</strong>.</li>
                        <li>Open the **Scanner** tab and scan the QR code from your phone's WhatsApp.</li>
                        <li>Ensure the status says <span class="badge badge-success">CONNECTED</span>.</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
