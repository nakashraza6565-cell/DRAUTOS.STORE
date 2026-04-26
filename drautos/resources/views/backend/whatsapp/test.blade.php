@extends('backend.layouts.master')

@section('main-content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-6 offset-md-3">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">WhatsApp Test Tool</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('whatsapp.test.send') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="phone">Phone Number (with country code, e.g. 923420557758)</label>
                            <input type="text" name="phone" id="phone" class="form-control" placeholder="923420557758" required>
                            <small class="text-muted">The system will automatically try to format common Pakistani numbers if entered as 03...</small>
                        </div>
                        <div class="form-group">
                            <label for="message">Message</label>
                            <textarea name="message" id="message" class="form-control" rows="5" required>Hello! This is a test message from Dr Auto Store WhatsApp API. 🛠️✨</textarea>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fab fa-whatsapp mr-2"></i> Send Test Message
                        </button>
                    </form>
                </div>
                <div class="card-footer">
                    <div class="alert alert-info small mb-0">
                        <strong>Troubleshooting:</strong><br>
                        1. Ensure the WhatsApp account is <strong>Logged In</strong> on wa2.shnaveed.com.<br>
                        2. If it returns success but no message arrives, check the "Instance ID" and "Access Token" in your <code>.env</code>.<br>
                        3. Current Endpoint: <code>https://wa2.shnaveed.com/api/send</code> (POST)
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
