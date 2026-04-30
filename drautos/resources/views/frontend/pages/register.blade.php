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
            
    <!-- Shop Register -->
    <section class="shop login section py-5" style="background: #f8fafc;">
        <div class="container">
            <div class="row"> 
                <div class="col-lg-6 col-md-10 col-12 mx-auto">
                    <div class="login shadow-lg p-5" style="background: #fff; border-radius: 24px; border: 1px solid #f1f5f9;">
                        <div class="text-center mb-4">
                            <h2 class="font-weight-bold display-4 mb-2" style="font-size: 28px; color: #1e293b;">Join DrAutos</h2>
                            <p class="text-muted">Create your account to start shopping auto parts</p>
                        </div>

                        @if(session('pending_approval'))
                            <div class="alert pending-alert mb-4" role="alert" style="background: #fffbeb; border: 2px solid #fde68a; border-radius: 16px;">
                                <div class="d-flex align-items-center p-2">
                                    <div class="bg-warning rounded-circle d-flex align-items-center justify-content-center mr-3" style="width: 40px; height: 40px; flex-shrink: 0;">
                                        <i class="ti-time text-dark" style="font-size:20px;"></i>
                                    </div>
                                    <div>
                                        <strong class="text-dark">Registration Submitted!</strong><br>
                                        <span class="small text-muted">Your account is <strong>pending approval</strong>. You will be notified on WhatsApp once active.</span>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Form -->
                        <form class="form" method="post" action="{{route('register.submit')}}">
                            @csrf
                            <div class="row">
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label class="font-weight-bold text-dark mb-2">Your Name <span>*</span></label>
                                        <input type="text" name="name" placeholder="Full Name" required="required" value="{{old('name')}}" style="height: 50px; border-radius: 12px; border: 2px solid #f1f5f9; background: #f8fafc; padding: 0 20px;">
                                        @error('name')
                                            <span class="text-danger small mt-1 d-block">{{$message}}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label class="font-weight-bold text-dark mb-2">Phone Number <span>*</span></label>
                                        <input type="text" name="phone" placeholder="03XXXXXXXXX" required="required" value="{{old('phone')}}" style="height: 50px; border-radius: 12px; border: 2px solid #f1f5f9; background: #f8fafc; padding: 0 20px;">
                                        @error('phone')
                                            <span class="text-danger small mt-1 d-block">{{$message}}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <label class="font-weight-bold text-dark mb-2">Your Email (Optional)</label>
                                        <input type="email" name="email" placeholder="example@mail.com" value="{{old('email')}}" style="height: 50px; border-radius: 12px; border: 2px solid #f1f5f9; background: #f8fafc; padding: 0 20px;">
                                        @error('email')
                                            <span class="text-danger small mt-1 d-block">{{$message}}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label class="font-weight-bold text-dark mb-2">Your Password <span>*</span></label>
                                        <input type="password" name="password" placeholder="••••••••" required="required" style="height: 50px; border-radius: 12px; border: 2px solid #f1f5f9; background: #f8fafc; padding: 0 20px;">
                                        @error('password')
                                            <span class="text-danger small mt-1 d-block">{{$message}}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label class="font-weight-bold text-dark mb-2">Confirm Password <span>*</span></label>
                                        <input type="password" name="password_confirmation" placeholder="••••••••" required="required" style="height: 50px; border-radius: 12px; border: 2px solid #f1f5f9; background: #f8fafc; padding: 0 20px;">
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group login-btn mt-3 mb-4">
                                        <button class="btn btn-block py-3 font-weight-bold" type="submit" style="background: #1e293b; color: #fff; border-radius: 12px; font-size: 16px; box-shadow: 0 10px 20px rgba(30, 41, 59, 0.2);">CREATE ACCOUNT</button>
                                    </div>
                                    <div class="text-center">
                                        <p class="small text-muted mb-0">Already have an account? <a href="{{route('login')}}" class="text-primary font-weight-bold">Login here</a></p>
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
