@extends('project_1.admin.layouts.layout')

@section('content')
<div class="main-content">
    <div class="admin-page-shell m-3">

        <div class="admin-page-header">
            <h3><i class="fas fa-sliders me-2"></i>Quản lý Banner</h3>
            <p>Quản lý hình ảnh banner hiển thị trên trang chủ.</p>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="admin-card">
            <form action="{{route('banners.store')}}" method="post" enctype="multipart/form-data">
                @csrf
                <label for="image" class="form-label">Thêm banner mới</label>
                <input type="file" name="image" class="form-control mb-3">
                <input type="submit" value="Thêm banner" class="btn btn-success">
            </form>
        </div>

        <div class="admin-card">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th class="text-center" style="min-width: 300px;">Ảnh</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($banners as $key => $banner)
                            <tr>
                                <td>{{$key}}</td>
                                <td class="text-center">
                                    <img src="{{ asset('storage/' . $banner->image_path) }}" alt="" style="width: 200px">
                                </td>
                                <td>
                                    <form action="{{route('banners.destroy', $banner)}}" method="post" class="d-inline" onclick="return confirm('Bạn chắc muốn xóa chứ?')">
                                        @csrf
                                        @method('delete')
                                        <button type="submit" class="btn-icon btn-delete" data-bs-toggle="tooltip" data-bs-title="Xóa">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
@endsection