@extends('project_1.admin.layouts.layout')

@section('content')
<div class="main-content">
    <div class="admin-page-shell m-3">

        <div class="admin-page-header">
            <h3><i class="fas fa-plus-circle me-2"></i>Thêm Setting</h3>
            <p>Tạo cấu hình mới cho hệ thống.</p>
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
            <form action="{{route('settings.store')}}" method="post" enctype="multipart/form-data">
                @csrf

                <label for="name" class="form-label">Tên</label>
                <input type="text" name="name" class="form-control mb-3" >

                <label for="value" class="form-label">Value</label>
                <textarea class="form-control mb-3" name="value" placeholder="Viết nội dung ở đây" id="floatingTextarea2" rows="5">{{old('content')}}</textarea>

                <div class="d-flex gap-2 mt-2">
                    <input type="submit" value="Thêm Setting" class="btn btn-success">
                    <a href="{{route('settings.index')}}" class="btn btn-danger">Xem danh sách</a>
                </div>
            </form>
        </div>

    </div>
</div>
@endsection
