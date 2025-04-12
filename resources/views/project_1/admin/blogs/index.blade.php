@extends('project_1.admin.layouts.layout')  
 
@section('content')

<div class="main-content">
    <div class="m-3 bg-white py-3">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show mx-4" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @elseif (session('error'))
            <div class="alert alert-danger alert-dismissible fade show mx-4" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif


     <div class="mx-3 p-2">
         <a href="{{ route('blogs.create') }}">
            <button class="btn btn-danger ">Thêm tin tức</button>
         </a>
     </div>
     <div class="table-responsive" style="overflow-x: auto;">
        <table class="table table-striped table-hover ">
            <thead>
               <tr>
                   <th>#</th>
                   <th style="min-width: 120px;">Tiêu đề</th>
                   <th style="min-width: 120px;">Hình ảnh</th>
                   <th style="min-width: 300px;" class="text-center">Nội dung</th>
                   <th style="min-width: 150px;">Tên tác giả</th>
                   <th>Hành động</th>
               </tr>
            </thead>
            <tbody>
               @foreach ($blogs as $key => $blog)
                   <tr>
                       <th scope="row">{{$key}}</th>
                       <td>{{$blog->title}}</td>
                       <td>
                           <img src="{{ asset('storage/' . $blog->image_path) }}" alt="" style="width: 100px">
                       </td>
                       <td>{{$blog->content}}</td>
                       <td>{{$blog->author}}</td>
                       <td>
                            <div class="d-flex">
                                <form action="{{route('blogs.destroy', $blog)}}" method="post" onclick="return confirm('Bạn chắc muốn xóa chứ?')">
                                    @csrf
                                    @method('delete')
                                    <input type="submit"  class="btn btn-danger " value="Xóa" >
                                </form>
                                
                                <a class="btn btn-info mx-1" href="{{route('blogs.edit', $blog)}}">Sửa</a>
                            </div>
                            
                        </td>
                   </tr>
               @endforeach
            </tbody>
        </table>
     </div>
         
        <div class="my-5 border-top p-3">
            {{$blogs->links()}}
        </div>
        
    </div>
 </div>


@endsection
