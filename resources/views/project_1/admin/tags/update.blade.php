@extends('project_1.admin.layouts.layout')

@section('content')
<div class="main-content">
    <div class="admin-page-shell m-3">

        <div class="admin-page-header">
            <h3><i class="fas fa-edit me-2"></i>Chỉnh sửa thẻ</h3>
            <p>Cập nhật thông tin thẻ sản phẩm.</p>
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
            <form action="{{route('tags.update', $tag->id)}}" method="POST">
                @csrf
                @method('PUT')
                <label for="name" class="form-label">Tên thẻ</label>
                <input type="text" class="form-control mb-3" name="name" value="{{$tag->name}}">
                <input type="submit" class="btn btn-success" value="Cập nhật">
            </form>
        </div>

    </div>
</div>
@endsection