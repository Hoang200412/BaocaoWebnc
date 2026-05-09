@extends('project_1.admin.layouts.layout')

@section('content')
<div class="main-content">
    <div class="admin-page-shell m-3">

        <div class="admin-page-header">
            <h3><i class="fas fa-plus-circle me-2"></i>Thêm bài viết mới</h3>
            <p>Tạo bài viết tin tức mới cho hệ thống.</p>
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
        @elseif (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="admin-card">
            <form action="{{route('blogs.store')}}" method="post" enctype="multipart/form-data">
                @csrf

                <label for="title" class="form-label">Tiêu đề</label>
                <input type="text" name="title" class="form-control mb-3"  value="{{old('title')}}">

                <label for="image" class="form-label">Hình ảnh</label>
                <input type="file" name="image_path" class="form-control mb-3" >

                <label for="content" class="form-label">Nội dung</label>
                <textarea class="form-control mb-3" name="content" placeholder="Viết nội dung ở đây" id="floatingTextarea2" rows="5">{{old('content')}}</textarea>

                <label for="author" class="form-label">Tên tác giả</label>
                <input type="text" name="author" class="form-control mb-3" value="{{old('author')}}">

                <div class="d-flex gap-2 mt-2">
                    <input type="submit" value="Thêm tin tức" class="btn btn-success">
                    <a href="{{route('blogs.index')}}" class="btn btn-danger">Xem danh sách</a>
                </div>
            </form>
        </div>

    </div>
</div>
@endsection
