<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;


class OrderController extends Controller
{
    //
    

    public function index() {
        $orders = Auth::user()->orders()
            ->with('items.product')
            ->latest()
            ->get();

        return view('project_1.customer.order.index', compact('orders'));
    }

    public function show($id)
    {
        $order = Auth::user()
            ->orders()
            ->with('items.product')
            ->findOrFail($id);

        return view('project_1.customer.order.show', compact('order'));
    }

    public function cancel($id)
    {
        $order = Auth::user()
            ->orders()
            ->findOrFail($id);

        if ($order->payment_method !== Order::PAYMENT_METHOD_VNPAY) {
            return redirect()
                ->route('order.show', $order->id)
                ->with('error', 'Chỉ có thể hủy đơn VNPAY khi chưa thanh toán hoặc thanh toán thất bại.');
        }

        if (!in_array($order->payment_status, [Order::PAYMENT_STATUS_FAILED, Order::PAYMENT_STATUS_PENDING], true)) {
            return redirect()
                ->route('order.show', $order->id)
                ->with('error', 'Chỉ có thể hủy khi chưa thanh toán hoặc thanh toán thất bại.');
        }

        if ($order->status !== Order::STATUS_PENDING) {
            return redirect()
                ->route('order.show', $order->id)
                ->with('error', 'Chỉ có thể hủy đơn hàng ở trạng thái chờ duyệt.');
        }
        if ($order->status === Order::STATUS_CANCELED) {
            return redirect()
                ->route('order.show', $order->id)
                ->with('success', 'Đơn hàng đã được hủy trước đó.');
        }

        $order->status = Order::STATUS_CANCELED;
        $order->save();

        return redirect()
            ->route('order.show', $order->id)
            ->with('success', 'Đã hủy đơn hàng thành công.');
    }
}