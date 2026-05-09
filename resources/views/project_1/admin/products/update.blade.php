@extends('project_1.admin.layouts.layout')

@section('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endsection

@section('content')
<div class="main-content">
    <div class="admin-page-shell m-3">

        <div class="admin-page-header">
            <h3><i class="fas fa-edit me-2"></i>Cập nhật sản phẩm</h3>
            <p>Chỉnh sửa thông tin sản phẩm hiện tại.</p>
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
            <form action="{{route('products.update', $product->id)}}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <label for="category_id" class="form-label">Danh mục sản phẩm</label>
                <select class="form-select mb-3" name="category_id" id="" >
                    <option disabled>Chọn danh mục</option>
                    @foreach ($categories as $category )
                    <option @selected($product->category_id == $category->id) value="{{$category->id}}">{{$category->name}}</option>
                    @endforeach
                </select>

                <label for="" class="form-label">Nhập tags cho sản phẩm </label>
                <div class="form-control mb-3">
                    <select name = "tags[]"  class="form-control  tags_select_choose" multiple="multiple">
                        @foreach ($product->tags as $tagItem)
                            <option value="{{$tagItem->name}}" selected>{{$tagItem->name}}</option>
                        @endforeach
                    </select>
                </div>

                <label for="name" class="form-label">Tên sản phẩm</label>
                <input type="text" name="name" class="form-control mb-3"  value="{{$product->name}}">

                <label for="" class="form-label">Hình ảnh</label>
                <input type="file" name="image_path" class="form-control mb-3" >
                <img class="mb-3" src="{{ asset('storage/' . $product->image_path) }}" alt="" style="width: 100px; border-radius: 8px;">

                <label for="" class="form-label">Mô tả sản phẩm</label>
                <textarea class="form-control mb-3" name="description" placeholder="Viết mô tả sản phẩm tại đây" id="floatingTextarea2" style="height: 100px">{{$product->description}}</textarea>

                <label for="" class="form-label">Số lượng</label>
                <input type="number" name="quantity" class="form-control mb-3" value="{{$product->quantity}}">

                <label for="" class="form-label">Giá Tiền</label>
                <input type="number" name="price" class="form-control mb-3" value="{{$product->price}}">

                <label for="" class="form-label">Ảnh khác</label>
                <div class="mb-3 d-flex gap-2">
                    @foreach ($product->galleries as $image)
                        @if ($image->image_path && \Storage::disk('public')->exists($image->image_path))
                            <img src="{{ asset('storage/' . $image->image_path) }}" style="width: 100px; height: auto; border-radius: 8px;" alt="">
                        @endif
                    @endforeach
                </div>

                <input type="file" name="galleries[]" class="form-control mb-3" multiple>

                <input type="submit" value="Cập nhật sản phẩm" class="btn btn-success">
            </form>
        </div>

    </div>
</div>
@endsection
