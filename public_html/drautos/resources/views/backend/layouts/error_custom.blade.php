@extends('backend.layouts.master')

@section('main-content')
<div class="container-fluid">
    <div class="row justify-content-center" style="margin-top: 50px;">
        <div class="col-md-6 text-center">
            <div class="card shadow mb-4">
                <div class="card-body py-5">
                    <div class="mb-4">
                        <i class="fas fa-exclamation-triangle text-warning" style="font-size: 60px;"></i>
                    </div>
                    <h2 class="h3 mb-2 text-gray-800">{{ $title ?? 'Resource Not Found' }}</h2>
                    <p class="lead text-gray-500 mb-5">{{ $message ?? 'The requested record could not be found in our database.' }}</p>
                    <div class="d-flex justify-content-center gap-3">
                        <a href="javascript:window.close();" class="btn btn-secondary mx-2">
                            <i class="fas fa-times"></i> Close Window
                        </a>
                        <a href="{{ url()->previous() }}" class="btn btn-primary mx-2">
                            <i class="fas fa-arrow-left"></i> Go Back
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
