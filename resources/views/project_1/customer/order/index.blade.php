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
            <div class="mb-4">
                <ul class="nav nav-tabs" id="orderStatusTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" data-status="{{ \App\Models\Order::STATUS_PENDING }}" type="button">Chờ duyệt</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" data-status="{{ \App\Models\Order::STATUS_APPROVED }}" type="button">Đã duyệt</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" data-status="{{ \App\Models\Order::STATUS_SHIPPING }}" type="button">Chờ giao hàng</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" data-status="{{ \App\Models\Order::STATUS_DELIVERED }}" type="button">Đã nhận hàng</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" data-status="{{ \App\Models\Order::STATUS_CANCELED }}" type="button">Đã hủy</button>
                    </li>
                    <li class="nav-item ms-auto" role="presentation">
                        <button class="nav-link" data-status="all" type="button">Tất cả</button>
                    </li>
                </ul>
            </div>

            @if (!$orders->isEmpty())
                @foreach ($orders as $order)
                    <div class="order-item bg-white mb-2 shadow" data-status="{{ $order->status }}">
                        <div class="order_title p-3 border-bottom d-flex justify-content-between align-items-start">
                            <div>
                                <span class="fw-bold fs-5 d-block">Mã đơn hàng: {{ $order->id }}</span>
                                <div class="text-muted small mt-1">
                                    Thời gian đặt: {{ $order->created_at?->format('d/m/Y H:i') }}
                                </div>
                            </div>
                            <div class="text-end">
                                <div class="text-danger fw-medium">{{ $order->status }}</div>
                                <div>
                                    <span class="badge {{ $order->payment_status === 'Thanh toán thành công' ? 'bg-success' : ($order->payment_status === 'Thanh toán thất bại' ? 'bg-danger' : 'bg-warning') }}">
                                        {{ $order->payment_status ?? 'Chưa thanh toán' }}
                                    </span>
                                </div>
                            </div>
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
                                    <div class="text-muted">Phí ship: {{ number_format($order->shipping_fee ?? 0) }} đ</div>
                                </div>
                        </div>  
                        @endforeach
                        

                        <div class="p-3">
                            <div class="d-flex flex-wrap justify-content-between align-items-center mb-2">
                                <div class="text-muted">
                                    Phương thức thanh toán: {{ \App\Models\Order::PAYMENT_METHOD_LABELS[$order->payment_method] ?? $order->payment_method }}
                                </div>
                            </div>
                            <div class="text-end">
                                <span class="fs-5 fw-medium">Thành tiền: </span>
                                <span class="fs-5 fw-medium text-danger">{{number_format($order->total_price)}} đ</span>
                            </div>
                            <div class="text-end my-3">
                                <a href="{{ route('order.show', $order->id) }}" class="btn btn-outline-dark">Xem chi tiết</a>
                                @if ($order->payment_method === \App\Models\Order::PAYMENT_METHOD_VNPAY && in_array($order->payment_status, [\App\Models\Order::PAYMENT_STATUS_PENDING, \App\Models\Order::PAYMENT_STATUS_FAILED], true) && $order->status !== \App\Models\Order::STATUS_CANCELED)
                                    <form action="{{ route('order.repay', $order->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-danger">Thanh toán</button>
                                    </form>
                                @endif
                                @if ($order->payment_status === \App\Models\Order::PAYMENT_STATUS_FAILED || $order->payment_status === \App\Models\Order::PAYMENT_STATUS_PENDING && $order->status !== \App\Models\Order::STATUS_CANCELED)
                                    <form action="{{ route('order.cancel', $order->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-danger">Hủy đơn</button>
                                    </form>
                                @endif
                            </div>
                        </div>

                    </div>
                @endforeach
            @else
                <div class="text-center">
                    <h1 class="fs-3">Không có đơn hàng nào</h1>
                </div>
            @endif
            
           
        </div>

        <script>
            (function() {
                const tabs = document.querySelectorAll('#orderStatusTabs .nav-link');
                const orders = document.querySelectorAll('.order-item');

                function normalizeStatus(s) {
                    return String(s).trim();
                }

                function showStatus(status) {
                    orders.forEach(o => {
                        const s = normalizeStatus(o.getAttribute('data-status'));
                        if (status === 'all' || s === status) {
                            o.style.display = '';
                        } else {
                            o.style.display = 'none';
                        }
                    });
                }

                tabs.forEach(tab => {
                    tab.addEventListener('click', function() {
                        tabs.forEach(t => t.classList.remove('active'));
                        this.classList.add('active');
                        const status = this.getAttribute('data-status');
                        showStatus(status);
                    });
                });

                // Initialize view: show active tab's status
                const active = document.querySelector('#orderStatusTabs .nav-link.active');
                if (active) {
                    showStatus(active.getAttribute('data-status'));
                }
            })();
        </script>
    </main>
@endsection