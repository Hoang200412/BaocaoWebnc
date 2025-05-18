@extends('project_1.admin.layouts.layout')

@section('content')
<div class="main-content">
    <div class="m-3 bg-white py-3">
       <h1 class="fs-3 px-3 mb-4">Danh sách đơn hàng</h1>

        <table class="table table-bordered table-hover">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Khách hàng</th>
                    <th>Trạng thái</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                <tr>
                    <td>{{ $order->id }}</td>
                    <td>{{ $order->name }}</td>
                    <td>
                        @if($order->status === 'Đã duyệt')
                            <span class="badge bg-success">{{ $order->status }}</span>
                        @elseif($order->status === 'Chưa thanh toán')
                            <span class="badge bg-warning text-dark">{{ $order->status }}</span>
                        @else
                            <span class="badge bg-secondary">{{ $order->status }}</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('orders.show', $order->id) }}" class="btn btn-sm btn-primary">Xem</a>

                        @if($order->status !== 'Đã duyệt')
                        <form action="{{ route('orders.approve', $order->id) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-success">Duyệt</button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center">Không có đơn hàng nào.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

    </div>
</div>
@endsection