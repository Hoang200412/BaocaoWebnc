<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;    
use App\Models\Order;

class AdminOrderController extends Controller
{

    public function index(Request $request) {
        $query = Order::query();

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->input('payment_method'));
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->input('payment_status'));
        }

        if ($request->filled('created_from')) {
            $from = Carbon::parse($request->input('created_from'))->startOfDay();
            $query->where('created_at', '>=', $from);
        }

        if ($request->filled('created_to')) {
            $to = Carbon::parse($request->input('created_to'))->endOfDay();
            $query->where('created_at', '<=', $to);
        }

        $orders = $query->orderBy('created_at', 'desc')
            ->paginate(10)
            ->appends($request->query());

        $filters = $request->only([
            'status',
            'payment_method',
            'payment_status',
            'created_from',
            'created_to',
        ]);

        $statusOptions = Order::STATUS_OPTIONS;
        $paymentStatusOptions = Order::PAYMENT_STATUS_OPTIONS;
        $paymentMethodOptions = Order::PAYMENT_METHOD_LABELS;

        return view('project_1.admin.orders.index', compact(
            'orders',
            'filters',
            'statusOptions',
            'paymentStatusOptions',
            'paymentMethodOptions'
        ));
    }

    public function approve($id) {
        $order = Order::findOrFail($id);
        $order->status = Order::STATUS_APPROVED;
        $order->save();

        return redirect()->back()->with('success', 'Đã duyệt đơn hàng #' . $order->id);
    }

    public function show($id) {
        $order = Order::with('items.product')->findOrFail($id);
        return view('project_1.admin.orders.show', compact('order'));
    }

    public function print($id)
    {
        $order = Order::with('items.product')->findOrFail($id);
        return view('project_1.admin.orders.print', compact('order'));
    }

    public function ship($id)
    {
        $order = Order::findOrFail($id);

        if ($order->status !== Order::STATUS_APPROVED) {
            return redirect()->back()->with('error', 'Chỉ có thể chuyển giao đơn hàng đã được duyệt.');
        }

        $order->status = Order::STATUS_SHIPPING;
        $order->save();

        return redirect()->back()->with('success', 'Đã chuyển đơn hàng #' . $order->id . ' cho bên vận chuyển.');
    }

}