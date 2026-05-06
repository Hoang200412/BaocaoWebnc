@extends('project_1.admin.layouts.layout')

@section('content')
    <div class="main-content">
        <div class="m-3 bg-white py-3">

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show mx-4 " role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="mx-3 p-2">
                <a href="{{route('categories.create')}}" class="btn-icon btn-add" data-bs-toggle="tooltip" data-bs-title="Thêm danh mục mới">
                   <i class="fas fa-plus"></i>
                </a>
            </div>
            <div class="px-4">
                <table class="table table-striped table-hover ">
                    <thead>
                        <tr>
                            <th scope="col" class="text-center">#</th>
                            <th scope="col">Tên danh mục</th>
                            <th scope="col" class="text-end pe-4">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($categories as $key => $category)
                            <tr>
                                <td scope="row" class="text-center">{{$key}}</th>
                                <td scope="row">{{$category->name}}</td>
                                <td class="d-flex justify-content-end">
                                    <a class="btn-icon btn-view" href="{{route('categories.edit', $category->id)}}" data-bs-toggle="tooltip" data-bs-title="Chỉnh sửa">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{route('categories.destroy', $category->id)}}" method="post" class="d-inline" onclick="return  confirm('Bạn chắc muốn xóa chứ?')">
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
                
                <div class="my-5 border-top p-3">
                    {{$categories->links()}}
                </div>
            </div>
            

        </div>
    </div>
@endsection