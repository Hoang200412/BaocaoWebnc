<!DOCTYPE html>
<html>
<head>
    <title>Hóa đơn #{{ $order->id }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; }
        table { width: 100%; border-collapse: collapse; }
        td, th { border: 1px solid #ccc; padding: 5px; }
    </style>
</head>
<body onload="window.print()">
    <h1>Hóa đơn #{{ $order->id }}</h1>
    <p>Khách hàng: {{ $order->name }}</p>
    <p>Địa chỉ: {{ $order->address }}</p>
    <p>Số điện thoại: {{ $order->phone }}</p>

    <h3>Sản phẩm</h3>
    <table>
        <tr>
            <th>Tên SP</th><th>SL</th><th>Giá</th><th>Tổng</th>
        </tr>
        @foreach($order->items as $item)
        <tr>
            <td>{{ $item->product_name }}</td>
            <td>{{ $item->quantity }}</td>
            <td>{{ number_format($item->price) }}</td>
            <td>{{ number_format($item->total_price) }}</td>
        </tr>
        @endforeach
    </table>

    <p><strong>Tổng tiền:</strong> {{ number_format($order->total_price) }} đ</p>
</body>
</html>