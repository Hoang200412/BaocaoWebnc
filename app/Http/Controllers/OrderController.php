<?php

namespace App\Http\Controllers;

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
}
