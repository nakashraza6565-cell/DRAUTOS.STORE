@extends('frontend.layouts.master')

@section('title','Dr Auto Parts || Login Page')

@section('main-content')
    <!-- Breadcrumbs -->
    <div class="breadcrumbs">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="bread-inner">
                        <ul class="bread-list">
                            <li><a href="{{route('home')}}">Home<i class="ti-arrow-right"></i></a></li>
                            <li class="active"><a href="javascript:void(0);">Login</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Breadcrumbs -->
            
    <!-- Shop Login -->
    <section class="shop login section py-5" style="background: #f8fafc;">
        <div class="container">
            <div class="row"> 
                <div class="col-lg-5 col-md-8 col-12 mx-auto">
                    <div class="login shadow-lg p-5" style="background: #fff; border-radius: 24px; border: 1px solid #f1f5f9;">
                        <div class="text-center mb-4">
                            <h2 class="font-weight-bold display-4 mb-2" style="font-size: 28px; color: #1e293b;">Welcome Back</h2>
                            <p class="text-muted">Enter your credentials to access your account</p>
                        </div>
                        <!-- Form -->
                        <form class="form" method="post" action="{{route('login.submit')}}">
                            @csrf
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label class="font-weight-bold text-dark mb-2">Email or Phone Number<span>*</span></label>
                                        <input type="text" name="email" placeholder="Enter Email or Phone" required="required" value="{{old('email')}}" style="height: 50px; border-radius: 12px; border: 2px solid #f1f5f9; background: #f8fafc; padding: 0 20px;">
                                        @error('email')
                                            <span class="text-danger small mt-1 d-block">{{$message}}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <label class="font-weight-bold text-dark mb-0">Your Password<span>*</span></label>
                                        </div>
                                        <input type="password" name="password" placeholder="••••••••" required="required" value="{{old('password')}}" style="height: 50px; border-radius: 12px; border: 2px solid #f1f5f9; background: #f8fafc; padding: 0 20px;">
                                        @error('password')
                                            <span class="text-danger small mt-1 d-block">{{$message}}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group mb-4">
                                        <div class="checkbox">
                                            <label class="checkbox-inline d-flex align-items-center" for="2">
                                                <input name="news" id="2" type="checkbox" class="mr-2" style="width: 18px; height: 18px;">
                                                <span class="small text-muted font-weight-bold">Remember me</span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="form-group login-btn mb-4">
                                        <button class="btn btn-block py-3 font-weight-bold" type="submit" style="background: #1e293b; color: #fff; border-radius: 12px; font-size: 16px; box-shadow: 0 10px 20px rgba(30, 41, 59, 0.2);">SIGN IN</button>
                                    </div>
                                    <div class="text-center">
                                        <p class="small text-muted mb-0">Don't have an account? <a href="{{route('register')}}" class="text-primary font-weight-bold">Register here</a></p>
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
    .btn-facebook{
        background:#39579A;
    }
    .btn-facebook:hover{
        background:#073088 !important;
    }
    .btn-github{
        background:#444444;
        color:white;
    }
    .btn-github:hover{
        background:black !important;
    }
    .btn-google{
        background:#ea4335;
        color:white;
    }
    .btn-google:hover{
        background:rgb(243, 26, 26) !important;
    }
</style>
@endpush
