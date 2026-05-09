@extends('project_1.admin.layouts.layout')

@section('content')
<div class="main-content">
    <div class="admin-page-shell m-3">

        <div class="admin-page-header">
            <h3><i class="fas fa-plus-circle me-2"></i>Thêm danh mục mới</h3>
            <p>Tạo danh mục sản phẩm mới cho hệ thống.</p>
        </div>

        @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="admin-card">
            <form action="{{route('categories.store')}}" method="post">
                @csrf
                <label for="name" class="form-label">Tên danh mục</label>
                <input type="text" class="form-control mb-3" name="name" value="">
                <div class="d-flex gap-2">
                    <input type="submit" class="btn btn-success" value="Thêm mới">
                    <a href="{{route('categories.index')}}" class="btn btn-danger">Xem danh sách</a>
                </div>
            </form>
        </div>

    </div>
</div>
@endsection