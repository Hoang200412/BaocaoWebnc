@extends('project_1.admin.layouts.layout')

@section('content')
<div class="main-content">
    <div class="m-3 bg-white py-3">
        <div class="container mt-4">
            <h2 class="mb-4">Chi tiết đơn hàng #{{ $order->id }}</h2>

            <div class="mb-3">
                <p><strong>Tên khách:</strong> {{ $order->name }}</p>
                <p><strong>Địa chỉ:</strong> {{ $order->address }}</p>
                <p><strong>Email:</strong> {{ $order->email }}</p>
                <p><strong>Điện thoại:</strong> {{ $order->phone }}</p>
                <p><strong>Trạng thái:</strong>
                    @if ($order->status === 'Đã thanh toán')
                        <span class="badge bg-success">{{ $order->status }}</span>
                    @elseif ($order->status === 'Thanh toán thất bại')
                        <span class="badge bg-danger">{{ $order->status }}</span>
                    @else
                        <span class="badge bg-warning text-dark">{{ $order->status }}</span>
                    @endif
                </p>
            </div>

            <h4>Danh sách sản phẩm</h4>
            <div class="table-responsive">
                <table class="table table-bordered mt-3">
                    <thead class="table-light">
                        <tr>
                            <th>Sản phẩm</th>
                            <th>Giá</th>
                            <th>Số lượng</th>
                            <th>Tổng</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->items as $item)
                        <tr>
                            <td>{{ $item->product_name }}</td>
                            <td>{{ number_format($item->price) }} đ</td>
                            <td>{{ $item->quantity }}</td>
                            <td>{{ number_format($item->total_price) }} đ</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <p class="fw-bold mt-3 fs-5">Tổng đơn hàng: {{ number_format($order->total_price) }} đ</p>

            <div class="mt-3">
                <a href="{{ route('orders.print', $order->id) }}" class="btn-icon btn-download" data-bs-toggle="tooltip" data-bs-title="In hóa đơn">
                    <i class="fas fa-print"></i>
                </a>
            </div>
        </div>

    </div>
</div>
@endsection