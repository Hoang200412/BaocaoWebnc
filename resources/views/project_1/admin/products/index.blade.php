@extends('project_1.admin.layouts.layout')

@section('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endsection

@section('content')
<div class="main-content">
    <div class="admin-page-shell m-3">

        <div class="admin-page-header">
            <h3><i class="fas fa-book me-2"></i>Quản lý sản phẩm</h3>
            <p>Quản lý danh sách sách và sản phẩm trong hệ thống.</p>
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
            <div class="admin-toolbar">
                <a href="{{ route('products.create') }}" class="btn-icon btn-add" data-bs-toggle="tooltip" data-bs-title="Thêm sản phẩm mới">
                    <i class="fas fa-plus"></i>
                </a>
            </div>

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                       <tr>
                           <th>#</th>
                           <th style="min-width: 120px;">Hình ảnh</th>
                           <th style="min-width: 150px;">Tên sản phẩm</th>
                           <th style="min-width: 300px;" class="text-center">Mô tả sản phẩm</th>
                           <th style="min-width: 150px;">Tên danh mục</th>
                           <th style="min-width: 200px;">Tên thẻ</th>
                           <th style="min-width: 100px;">Số lượng</th>
                           <th style="min-width: 100px;">Đơn giá</th>
                           <th>Hành động</th>
                       </tr>
                    </thead>
                    <tbody>
                       @foreach ($products as $key => $product)
                           <tr>
                               <td>{{$key}}</td>
                               <td>
                                   <img src="{{ asset('storage/' . $product->image_path) }}" alt="" style="width: 100px">
                               </td>
                               <td>{{$product->name}}</td>
                               <td>{{$product->description}}</td>
                               <td>{{$product->category->name}}</td>
                               <td>
                                    @foreach ($product->tags as $tagItem)
                                        <span class="badge bg-success">{{$tagItem->name}}</span>
                                    @endforeach
                               </td>
                               <td>{{$product->quantity}}</td>
                               <td>{{number_format($product->price)}} đ</td>
                               <td>
                                    <div class="admin-actions">
                                        <a class="btn-icon btn-view" href="{{route('products.edit', $product)}}" data-bs-toggle="tooltip" data-bs-title="Chỉnh sửa">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{route('products.destroy', $product)}}" method="post" class="d-inline">
                                            @csrf
                                            @method('delete')
                                            <button type="submit" class="btn-icon btn-delete" data-bs-toggle="tooltip" data-bs-title="Xóa" onclick="return confirm('Bạn chắc muốn xóa chứ?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                           </tr>
                       @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-3 border-top pt-3">
                {{$products->links()}}
            </div>
        </div>

    </div>
</div>
@endsection
