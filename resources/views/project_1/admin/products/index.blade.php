@extends('project_1.admin.layouts.layout')  
@section('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container--default .select2-selection--multiple {
        min-height: 40px;
        padding: 5px;
        border: 1px solid #ced4da;
        border-radius: 4px;
       width: 400px;
       
    }
    .select2-selection__choice__display {
        background-color: #0ff129;
    }
</style>
@endsection
@section('content')


<div class="main-content">
    <div class="m-3 bg-white py-3">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show mx-4" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @elseif (session('error'))
            <div class="alert alert-error alert-dismissible fade show mx-4" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif


     <div class="mx-3 p-2">
         <a href="{{ route('products.create') }}">
            <button class="btn btn-danger ">Thêm sản phẩm</button>
         </a>
     </div>
     <div class="table-responsive" style="overflow-x: auto;">
        <table class="table table-striped table-hover ">
            <thead>
               <tr>
                   <th>#</th>
                   <th style="min-width: 120px;">Hình ảnh</th>
                   <th style="min-width: 150px;">Tên sản phẩm</th>
                   <th style="min-width: 300px;" class="text-center">Mô tả sản phẩm</th>
                   <th style="min-width: 150px;">Tên danh mục</th>
                   <th style="min-width: 200px;">Tên thẻ</th>
                   <th style="min-width: 100px;">Số lượng</th>
                   <th style="min-width: 100px;">đơn giá</th>
                   <th>Hành động</th>
               </tr>
            </thead>
            <tbody>
               @foreach ($products as $key => $product)
                   <tr>
                       <th scope="row">{{$key}}</th>
                       <td>
                           <img src="{{ asset('storage/' . $product->image_path) }}" alt="" style="width: 100px">
                       </td>
                       <td>{{$product->name}}</td>
                       <td>{{$product->description}}</td>
                       <td>{{$product->category->name}}</td>
                      
                       <td>
                            @foreach ($product->tags as $tagItem)
                                <span class="badge bg-success ">{{$tagItem->name}}</span>
                            @endforeach
                       </td>

                       <td>{{$product->quantity}}</td>
                       <td>{{number_format($product->price)}} đ</td>
                       <td>
                            <div class="d-flex">
                                <form action="{{route('products.destroy', $product)}}" method="post" onclick="return confirm('Bạn chắc muốn xóa chứ?')">
                                    @csrf
                                    @method('delete')
                                    <input type="submit"  class="btn btn-danger " value="Xóa" >
                                </form>
                                
                                <a class="btn btn-info mx-1" href="{{route('products.edit', $product)}}">Sửa</a>
                            </div>
                            
                        </td>
                   </tr>
               @endforeach
            </tbody>
        </table>
     </div>
         
    <div class="my-5 border-top p-3">
        {{$products->links()}}
    </div>
        
    </div>
 </div>


@endsection
