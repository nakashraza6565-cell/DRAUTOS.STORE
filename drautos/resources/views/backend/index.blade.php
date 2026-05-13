@extends('backend.layouts.master')
@section('title','Danyal Autos || DEBUG DASHBOARD')
@section('main-content')
<div class="container-fluid">
    <h1>Dashboard is Loading!</h1>
    <p>If you see this, the controller is working fine.</p>
    <ul>
        <li>Staff Count: {{ $staff_count }}</li>
        <li>Order Count: {{ $order_count }}</li>
    </ul>
</div>
@endsection
