<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;    
use App\Models\Order;

class AdminOrderController extends Controller
{

    public function index() {
        $orders = Order::orderBy('created_at', 'desc')->get();
        return view('project_1.admin.orders.index', compact('orders'));
    }

    public function approve($id) {
        $order = Order::findOrFail($id);
        $order->status = 'Đã duyệt';
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

}
