@extends('project_1.admin.layouts.layout')

@section('content')
<div class="main-content">
    <div class="admin-page-shell m-3">

        <div class="admin-page-header">
            <h3><i class="fas fa-bag-shopping me-2"></i>Quản lý đơn hàng</h3>
            <p>Xem, duyệt và quản lý tất cả đơn hàng trên hệ thống.</p>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="admin-filter-card">
            <form class="row g-2 align-items-end" method="GET" action="{{ route('orders.index') }}">
                <div class="col-md-3">
                    <label class="form-label">Trạng thái đơn hàng</label>
                    <select name="status" class="form-select">
                        <option value="">Tất cả</option>
                        @foreach ($statusOptions as $status)
                            <option value="{{ $status }}" {{ ($filters['status'] ?? '') === $status ? 'selected' : '' }}>
                                {{ $status }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Phương thức thanh toán</label>
                    <select name="payment_method" class="form-select">
                        <option value="">Tất cả</option>
                        @foreach ($paymentMethodOptions as $methodValue => $methodLabel)
                            <option value="{{ $methodValue }}" {{ ($filters['payment_method'] ?? '') === $methodValue ? 'selected' : '' }}>
                                {{ $methodLabel }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Trạng thái thanh toán</label>
                    <select name="payment_status" class="form-select">
                        <option value="">Tất cả</option>
                        @foreach ($paymentStatusOptions as $paymentStatus)
                            <option value="{{ $paymentStatus }}" {{ ($filters['payment_status'] ?? '') === $paymentStatus ? 'selected' : '' }}>
                                {{ $paymentStatus }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Từ ngày</label>
                    <input type="date" name="created_from" value="{{ $filters['created_from'] ?? '' }}" class="form-control">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Đến ngày</label>
                    <input type="date" name="created_to" value="{{ $filters['created_to'] ?? '' }}" class="form-control">
                </div>
                <div class="col-md-6 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Lọc</button>
                    <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary">Xóa lọc</a>
                </div>
            </form>
        </div>

        <div class="admin-card">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Khách hàng</th>
                            <th>Trạng thái đơn</th>
                            <th>Thanh toán</th>
                            <th>Phương thức</th>
                            <th>Ngày tạo</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                        <tr>
                            <td>{{ $order->id }}</td>
                            <td>{{ $order->name }}</td>
                            <td>
                                @if($order->status === \App\Models\Order::STATUS_APPROVED)
                                    <span class="badge bg-success">{{ $order->status }}</span>
                                @elseif($order->status === \App\Models\Order::STATUS_PENDING)
                                    <span class="badge bg-warning text-dark">{{ $order->status }}</span>
                                @elseif($order->status === \App\Models\Order::STATUS_SHIPPING)
                                    <span class="badge bg-info text-dark">{{ $order->status }}</span>
                                @elseif($order->status === \App\Models\Order::STATUS_DELIVERED)
                                    <span class="badge bg-primary">{{ $order->status }}</span>
                                @elseif($order->status === \App\Models\Order::STATUS_CANCELED)
                                    <span class="badge bg-danger">{{ $order->status }}</span>
                                @else
                                    <span class="badge bg-secondary">{{ $order->status }}</span>
                                @endif
                            </td>
                            <td>
                                @if($order->payment_status === \App\Models\Order::PAYMENT_STATUS_SUCCESS)
                                    <span class="badge bg-success">{{ $order->payment_status }}</span>
                                @elseif($order->payment_status === \App\Models\Order::PAYMENT_STATUS_FAILED)
                                    <span class="badge bg-danger">{{ $order->payment_status }}</span>
                                @else
                                    <span class="badge bg-warning text-dark">{{ $order->payment_status ?? \App\Models\Order::PAYMENT_STATUS_PENDING }}</span>
                                @endif
                            </td>
                            <td>
                                {{ $paymentMethodOptions[$order->payment_method] ?? $order->payment_method }}
                            </td>
                            <td>{{ $order->created_at?->format('d/m/Y H:i') }}</td>
                            <td>
                                <div class="admin-actions">
                                    <a href="{{ route('orders.show', $order->id) }}" class="btn-icon btn-view" data-bs-toggle="tooltip" data-bs-title="Xem chi tiết">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    @if($order->status === \App\Models\Order::STATUS_PENDING)
                                    <form action="{{ route('orders.approve', $order->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn-icon btn-add" data-bs-toggle="tooltip" data-bs-title="Duyệt đơn">
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
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">Không có đơn hàng nào.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3 border-top pt-3">
                {{ $orders->links() }}
            </div>
        </div>

    </div>
</div>
@endsection
