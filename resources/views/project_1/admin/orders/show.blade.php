@extends('project_1.admin.layouts.layout')

@section('content')
<div class="main-content">
    <div class="admin-page-shell m-3">

        <div class="admin-page-header">
            <h3><i class="fas fa-file-invoice me-2"></i>Chi tiết đơn hàng #{{ $order->id }}</h3>
            <p>Xem thông tin chi tiết và quản lý trạng thái đơn hàng.</p>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="admin-card">
            <h5 class="mb-3" style="font-weight: 700; color: #102a43;">Thông tin khách hàng</h5>
            <div class="row mb-3">
                <div class="col-md-6">
                    <p><strong>Tên khách:</strong> {{ $order->name }}</p>
                    <p><strong>Email:</strong> {{ $order->email }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Điện thoại:</strong> {{ $order->phone }}</p>
                    <p><strong>Địa chỉ:</strong> {{ $order->address }}</p>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <p><strong>Trạng thái đơn:</strong>
                        @if ($order->status === \App\Models\Order::STATUS_APPROVED)
                            <span class="badge bg-success">{{ $order->status }}</span>
                        @elseif ($order->status === \App\Models\Order::STATUS_PENDING)
                            <span class="badge bg-warning text-dark">{{ $order->status }}</span>
                        @elseif ($order->status === \App\Models\Order::STATUS_SHIPPING)
                            <span class="badge bg-info text-dark">{{ $order->status }}</span>
                        @elseif ($order->status === \App\Models\Order::STATUS_DELIVERED)
                            <span class="badge bg-primary">{{ $order->status }}</span>
                        @elseif ($order->status === \App\Models\Order::STATUS_CANCELED)
                            <span class="badge bg-danger">{{ $order->status }}</span>
                        @else
                            <span class="badge bg-secondary">{{ $order->status }}</span>
                        @endif
                    </p>
                </div>
                <div class="col-md-4">
                    <p><strong>Thanh toán:</strong>
                        @if ($order->payment_status === \App\Models\Order::PAYMENT_STATUS_SUCCESS)
                            <span class="badge bg-success">{{ $order->payment_status }}</span>
                        @elseif ($order->payment_status === \App\Models\Order::PAYMENT_STATUS_FAILED)
                            <span class="badge bg-danger">{{ $order->payment_status }}</span>
                        @else
                            <span class="badge bg-warning text-dark">{{ $order->payment_status ?? \App\Models\Order::PAYMENT_STATUS_PENDING }}</span>
                        @endif
                    </p>
                </div>
                <div class="col-md-4">
                    <p><strong>Phương thức:</strong>
                        {{ \App\Models\Order::PAYMENT_METHOD_LABELS[$order->payment_method] ?? $order->payment_method }}
                    </p>
                </div>
            </div>

            {{-- Action buttons --}}
            <div class="d-flex gap-2 mb-3">
                @if($order->status === \App\Models\Order::STATUS_PENDING)
                <form action="{{ route('orders.approve', $order->id) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn-icon btn-add" data-bs-toggle="tooltip" data-bs-title="Duyệt đơn hàng">
                        <i class="fas fa-check"></i>
                    </button>
                </form>
                @endif

                @if($order->status === \App\Models\Order::STATUS_APPROVED)
                <form action="{{ route('orders.ship', $order->id) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn-icon btn-download" data-bs-toggle="tooltip" data-bs-title="Chuyển giao vận chuyển">
                        <i class="fas fa-truck"></i>
                    </button>
                </form>
                @endif

                <a href="{{ route('orders.print', $order->id) }}" class="btn-icon btn-download" data-bs-toggle="tooltip" data-bs-title="In hóa đơn">
                    <i class="fas fa-print"></i>
                </a>
            </div>
        </div>

        <div class="admin-card">
            <h5 class="mb-3" style="font-weight: 700; color: #102a43;">Danh sách sản phẩm</h5>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
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

            <div class="text-end mt-3 pt-3 border-top">
                <div class="mb-1" style="color: #627d98;">Phí vận chuyển: <strong>{{ number_format($order->shipping_fee ?? 0) }} đ</strong></div>
                <span class="fs-5 fw-bold" style="color: #0f766e;">Tổng đơn hàng: {{ number_format($order->total_price) }} đ</span>
            </div>
        </div>

        <div class="mt-2">
            <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>Quay lại danh sách
            </a>
        </div>

    </div>
</div>
@endsection