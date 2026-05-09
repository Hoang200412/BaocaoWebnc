@extends('project_1.admin.layouts.layout')

@section('content')
<div class="main-content">
    <div class="admin-page-shell m-3">

        <div class="admin-page-header">
            <h3><i class="fas fa-tags me-2"></i>Quản lý thẻ</h3>
            <p>Thêm, chỉnh sửa và quản lý các thẻ sản phẩm.</p>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="admin-card">
            <div class="admin-toolbar">
                <a href="{{route('tags.create')}}" class="btn-icon btn-add" data-bs-toggle="tooltip" data-bs-title="Thêm thẻ mới">
                   <i class="fas fa-plus"></i>
                </a>
            </div>

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th scope="col" class="text-center">#</th>
                            <th scope="col">Tên thẻ</th>
                            <th scope="col" class="text-end pe-4">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($tags as $key => $tag)
                            <tr>
                                <td class="text-center">{{$key}}</td>
                                <td>{{$tag->name}}</td>
                                <td class="d-flex justify-content-end">
                                    <a class="btn-icon btn-view" href="{{route('tags.edit', $tag->id)}}" data-bs-toggle="tooltip" data-bs-title="Chỉnh sửa">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{route('tags.destroy', $tag->id)}}" method="post" class="d-inline" onclick="return confirm('Bạn chắc muốn xóa chứ?')">
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

            <div class="mt-3 border-top pt-3">
                {{$tags->links()}}
            </div>
        </div>

    </div>
</div>
@endsection