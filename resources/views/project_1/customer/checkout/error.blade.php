@extends('project_1.customer.layouts.layout')

@section('content')
<div class="text-center">
    <h1 class="text-danger-600 text-3xl font-bold">❌ Đặt hàng thất bại!</h1>
    <a href="{{ route('home') }}" class="btn btn-primary mt-4">Quay về trang chủ</a>
</div>
@endsection
