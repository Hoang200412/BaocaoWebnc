@extends('project_1.admin.layouts.layout')
 
@section('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endsection

@section('content')
<div class="main-content">
    <div class="admin-page-shell m-3">

        <div class="admin-page-header">
            <h3><i class="fas fa-plus-circle me-2"></i>Thêm sản phẩm mới</h3>
            <p>Tạo sản phẩm mới và thêm vào danh sách bán hàng.</p>
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
            <form action="{{route('products.store')}}" method="post" enctype="multipart/form-data">
                @csrf
                <label for="" class="form-label">Danh mục sản phẩm</label>
                <select class="form-select mb-3" name="category_id" id="" >
                    <option selected>Chọn danh mục</option>
                    @foreach ($categories as $category )
                    <option value="{{$category->id}}">{{$category->name}}</option>
                    @endforeach
                </select>

                <div class="form-group">
                    <label>Nhập tags cho sản phẩm </label>
                    <div class="form-control mb-3">
                        <select name="tags[]" class="tags_select_choose" multiple="multiple">
                            @if (old('tags'))
                                @foreach (old('tags') as $tag)
                                    <option value="{{ $tag }}" selected>{{ $tag }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                </div>

                <label for="name" class="form-label">Tên sản phẩm</label>
                <input type="text" name="name" class="form-control mb-3"  value="{{old('name')}}">

                <label for="" class="form-label">Hình ảnh</label>
                <input type="file" name="image_path" class="form-control mb-3" >

                <label for="" class="form-label">Mô tả sản phẩm</label>
                <textarea class="form-control mb-3" name="description" placeholder="Viết mô tả sản phẩm tại đây" id="floatingTextarea2" style="height: 100px"></textarea>

                <label for="" class="form-label">Số lượng</label>
                <input type="number" name="quantity" class="form-control mb-3" value="{{old('quantity')}}">

                <label for="" class="form-label">Giá Tiền</label>
                <input type="number" name="price" class="form-control mb-3" value="{{old('price')}}">

                <label for="" class="form-label">Ảnh chi tiết </label>
                <input type="file" name="galleries[]" class="form-control mb-3" multiple>

                <div class="d-flex gap-2 mt-2">
                    <input type="submit" value="Thêm sản phẩm" class="btn btn-success">
                    <a href="{{route('products.index')}}" class="btn btn-danger">Xem danh sách</a>
                </div>
            </form>
        </div>

    </div>
</div>
@endsection
