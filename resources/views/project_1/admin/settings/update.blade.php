@extends('project_1.admin.layouts.layout')

@section('content')
<div class="main-content">
    <div class="admin-page-shell m-3">

        <div class="admin-page-header">
            <h3><i class="fas fa-edit me-2"></i>Cập nhật Setting</h3>
            <p>Chỉnh sửa cấu hình hệ thống.</p>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @elseif (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="admin-card">
            <form action="{{ route('setting.update', ['id' => $setting->id]) }}" method="post" enctype="multipart/form-data">
                @csrf

                <label for="name" class="form-label">Tên</label>
                <input type="text" name="name" class="form-control mb-3" value="{{$setting->name}}" >

                <label for="value" class="form-label">Value</label>
                <textarea class="form-control mb-3" name="value" placeholder="Viết nội dung ở đây" id="floatingTextarea2" rows="5">{{$setting->value}}</textarea>

                <input type="submit" value="Cập nhật" class="btn btn-success">
            </form>
        </div>

    </div>
</div>
@endsection
