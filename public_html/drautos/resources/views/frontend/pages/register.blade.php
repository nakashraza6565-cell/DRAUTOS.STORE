@extends('frontend.layouts.master')

@section('title','Dr Auto Parts || Register Page')

@section('main-content')
	<!-- Breadcrumbs -->
    <div class="breadcrumbs">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="bread-inner">
                        <ul class="bread-list">
                            <li><a href="{{route('home')}}">Home<i class="ti-arrow-right"></i></a></li>
                            <li class="active"><a href="javascript:void(0);">Register</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Breadcrumbs -->
            
    <!-- Shop Login -->
    <section class="shop login section">
        <div class="container">
            <div class="row"> 
                <div class="col-lg-6 offset-lg-3 col-12">
                    <div class="login-form">
                        <h2>Register</h2>
                        <p>Please register in order to checkout more quickly</p>

                        @if(session('pending_approval'))
                            <div class="alert alert-warning pending-alert" role="alert">
                                <div class="d-flex align-items-center">
                                    <i class="ti-time" style="font-size:24px; margin-right:12px; color:#e6a817;"></i>
                                    <div>
                                        <strong>Registration Submitted!</strong><br>
                                        Your registration request is currently <strong>pending approval</strong>. 
                                        You will be notified on your WhatsApp number once your account is approved by admin.
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Form -->
                        <form class="form" method="post" action="{{route('register.submit')}}">
                            @csrf
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label>Your Name <span>*</span></label>
                                        <input type="text" name="name" placeholder="Full Name" required="required" value="{{old('name')}}">
                                        @error('name')
                                            <span class="text-danger">{{$message}}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <label>Phone Number <span>*</span></label>
                                        <input type="text" name="phone" placeholder="e.g. 03001234567" required="required" value="{{old('phone')}}">
                                        @error('phone')
                                            <span class="text-danger">{{$message}}</span>
                                        @enderror
                                        <small class="text-muted">WhatsApp messages will be sent to this number.</small>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <label>Your Email (Optional)</label>
                                        <input type="email" name="email" placeholder="Enter your email" value="{{old('email')}}">
                                        @error('email')
                                            <span class="text-danger">{{$message}}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <label>Your Password <span>*</span></label>
                                        <input type="password" name="password" placeholder="" required="required">
                                        @error('password')
                                            <span class="text-danger">{{$message}}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <label>Confirm Password <span>*</span></label>
                                        <input type="password" name="password_confirmation" placeholder="" required="required">
                                        @error('password_confirmation')
                                            <span class="text-danger">{{$message}}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group login-btn">
                                        <button class="btn" type="submit">Register</button>
                                        <a href="{{route('login.form')}}" class="btn">Login</a>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <!--/ End Form -->
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--/ End Login -->
@endsection

@push('styles')
<style>
    .shop.login .form .btn{
        margin-right:0;
    }
    .opt-text {
        color: #6c757d;
        font-size: 11px;
        font-weight: 500;
        margin-left: 5px;
    }
    .pending-alert {
        background: linear-gradient(135deg, #fff9e6, #fff3cc);
        border: 1.5px solid #ffc107;
        border-radius: 10px;
        padding: 16px 18px;
        margin-bottom: 20px;
        box-shadow: 0 2px 10px rgba(255,193,7,0.2);
    }
</style>
@endpush
