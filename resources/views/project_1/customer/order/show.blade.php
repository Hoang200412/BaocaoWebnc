@extends('project_1.customer.layouts.layout')

@section('content')
    <main>
        <section class="mb-4">
            <div class="bg-dark-subtle p-3 d-flex justify-content-center">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none text-gray">Trang chủ</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('order') }}" class="text-decoration-none text-gray">Đơn hàng</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Chi tiết</li>
                    </ol>
                </nav>
            </div>
        </section>

        <div class="container">
            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                <div>
                    <h3 class="mb-1">Chi tiết đơn hàng #{{ $order->id }}</h3>
                    <div class="text-muted">Đặt lúc: {{ $order->created_at?->format('d/m/Y H:i') }}</div>
                </div>
                <div class="text-end">
                    <div class="fw-medium">{{ $order->status }}</div>
                    <span class="badge {{ $order->payment_status === \App\Models\Order::PAYMENT_STATUS_SUCCESS ? 'bg-success' : ($order->payment_status === \App\Models\Order::PAYMENT_STATUS_FAILED ? 'bg-danger' : 'bg-warning') }}">
                        {{ $order->payment_status ?? \App\Models\Order::PAYMENT_STATUS_PENDING }}
                    </span>
                </div>
            </div>

            <div class="row g-3">
                <div class="col-lg-8">
                    <div class="bg-white shadow-sm rounded p-3 mb-3">
                        <h5 class="mb-3">Sản phẩm</h5>
                        <div class="table-responsive">
                            <table class="table align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Sản phẩm</th>
                                        <th class="text-end">Giá</th>
                                        <th class="text-center">Số lượng</th>
                                        <th class="text-end">Tạm tính</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($order->items as $item)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <div style="width: 56px">
                                                        @if ($item->product?->image_path)
                                                            <img src="{{ asset(Storage::url($item->product->image_path)) }}" alt="" class="img-fluid rounded">
                                                        @endif
                                                    </div>
                                                    <div>
                                                        <div class="fw-medium">{{ $item->product_name }}</div>
                                                        <div class="text-muted small">Mã SP: {{ $item->product_id }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-end">{{ number_format($item->price) }} đ</td>
                                            <td class="text-center">{{ $item->quantity }}</td>
                                            <td class="text-end">{{ number_format($item->total_price) }} đ</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="bg-white shadow-sm rounded p-3 mb-3">
                        <h5 class="mb-3">Thông tin giao hàng</h5>
                        <div class="mb-2"><span class="text-muted">Người nhận:</span> {{ $order->name }}</div>
                        <div class="mb-2"><span class="text-muted">Điện thoại:</span> {{ $order->phone }}</div>
                        <div class="mb-2"><span class="text-muted">Email:</span> {{ $order->email }}</div>
                        <div><span class="text-muted">Địa chỉ:</span> {{ $order->address }}</div>
                    </div>

                    @php
                        $itemsTotal = $order->items->sum('total_price');
                    @endphp
                    <div class="bg-white shadow-sm rounded p-3">
                        <h5 class="mb-3">Tổng kết</h5>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Phương thức thanh toán</span>
                            <span>{{ \App\Models\Order::PAYMENT_METHOD_LABELS[$order->payment_method] ?? $order->payment_method }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Tạm tính</span>
                            <span>{{ number_format($itemsTotal) }} đ</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Phí vận chuyển</span>
                            <span>{{ number_format($order->shipping_fee ?? 0) }} đ</span>
                        </div>
                        <div class="d-flex justify-content-between fw-bold border-top pt-2">
                            <span>Tổng thanh toán</span>
                            <span class="text-danger">{{ number_format($order->total_price) }} đ</span>
                        </div>
                        <div class="mt-3 d-flex gap-2">
                            @if ($order->payment_method === \App\Models\Order::PAYMENT_METHOD_VNPAY && in_array($order->payment_status, [\App\Models\Order::PAYMENT_STATUS_PENDING, \App\Models\Order::PAYMENT_STATUS_FAILED], true))
                                <form action="{{ route('order.repay', $order->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-danger">Thanh toán lại</button>
                                </form>
                            @endif
                            @if ($order->payment_method === \App\Models\Order::PAYMENT_METHOD_VNPAY && in_array($order->payment_status, [\App\Models\Order::PAYMENT_STATUS_PENDING, \App\Models\Order::PAYMENT_STATUS_FAILED], true) && $order->status !== \App\Models\Order::STATUS_CANCELED)
                                <form action="{{ route('order.cancel', $order->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-danger">Hủy đơn</button>
                                </form>
                            @endif
                            <a href="{{ route('order') }}" class="btn btn-outline-dark">Quay lại danh sách</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection
