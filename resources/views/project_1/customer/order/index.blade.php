@extends('project_1.customer.layouts.layout')

@section('content')
    <main>
        <section class="mb-4">
            <div class="bg-dark-subtle p-3 d-flex justify-content-center">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                      <li class="breadcrumb-item"><a href="#" class="text-decoration-none text-gray">Trang chủ</a></li>
                      <li class="breadcrumb-item"><a href="#" class="text-decoration-none text-gray">Đơn hàng</a></li>
                    </ol>
                </nav>
            </div>
        </section>
        <div class="container">
            @foreach ($orders as $order)
            <div class="order-item bg-white mb-2 shadow">
                <div class="order_title p-3 border-bottom d-flex justify-content-between">
                    <span class="fw-bold fs-5">Mã đơn hàng:{{ $order->id }}</span>
                    <span class="text-danger">{{$order->status}}</span>
                </div>

                @foreach ($order->items as $item)
                    <div class="row p-3 border-bottom">
                        <div class="col-1">
                            <div class="image">
                                <img src="{{asset(Storage::url($item->product->image_path))}}" alt="" class="img-fluid">
                            </div>
                        </div>
                        <div class="col-11">
                            <div class="product-name">
                                <span class="fs-5 fw-medium">{{$item->product_name}}</span>
                            </div>
                            <div class="sl">
                                <span>Số lượng: {{$item->quantity}}</span>
                            </div>
                            <div class="price">
                                <span class="text-danger">Giá: {{number_format($item->price)}}đ</span>
                            </div>
                        </div>
                    </div>
                @endforeach
                

                <div class="p-3">
                    <div class="text-end">
                        <span class="fs-5 fw-medium">Thành tiền: </span>
                        <span class="fs-5 fw-medium text-danger">{{number_format($order->total_price)}} đ</span>
                    </div>
                    <div class="text-end my-3">
                        <a href="#" class="btn btn-danger">Mua lại</a>
                        <a href="" class="btn border">Liên hệ người bán</a>
                    </div>
                </div>

            </div>
            @endforeach
          
           
        </div>
    </main>
@endsection