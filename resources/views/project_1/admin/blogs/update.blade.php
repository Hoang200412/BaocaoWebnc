@extends('project_1.admin.layouts.layout')

@section('content')
<div class="main-content">
    <div class="admin-page-shell m-3">

        <div class="admin-page-header">
            <h3><i class="fas fa-edit me-2"></i>Cập nhật bài viết</h3>
            <p>Chỉnh sửa nội dung bài viết tin tức.</p>
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
            <form action="{{route('blogs.update', $blog)}}" method="post" enctype="multipart/form-data">
                @csrf
                @method('put')

                <label for="title" class="form-label">Tiêu đề</label>
                <input type="text" name="title" class="form-control mb-3"  value="{{$blog->title}}">

                <label for="image" class="form-label">Hình ảnh</label>
                <input type="file" name="image_path" class="form-control mb-3" >
                <img src="{{asset('storage/'. $blog->image_path)}}" alt="" style="width: 100px; border-radius: 8px;" class="mb-3">

                <label for="content" class="form-label">Nội dung</label>
                <textarea class="form-control mb-3" name="content" placeholder="Viết nội dung ở đây" id="floatingTextarea2" rows="5">{{$blog->content}}</textarea>

                <label for="author" class="form-label">Tên tác giả</label>
                <input type="text" name="author" class="form-control mb-3" value="{{$blog->author}}">

                <input type="submit" value="Cập nhật tin tức" class="btn btn-success">
            </form>
        </div>

    </div>
</div>
@endsection
