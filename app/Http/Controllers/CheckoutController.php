<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Http\Requests\CheckoutRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;
use App\Models\User;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Cartitem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;


class CheckoutController extends Controller
{
    public function show(Request $request) {
        if ($request->has('cart_id')) {
            $cart_ids = $request->cart_id; // lấy từ request
            $cart_items = Cartitem::whereIn('id', $cart_ids)->get();

            $total_price = 0;
            foreach ($cart_items as $item) {
                $total_price += $item->product->price * $item->quantity;
            }

            return view('project_1.customer.checkout.index', compact('cart_items', 'total_price'));

        }elseif($request->has('product_id')) {
            $product = Product::find($request->product_id);
            $quantity = $request->quantity;
            $total_price = $request->quantity*$product->price;
            return view('project_1.customer.checkout.index', compact('product', 'quantity','total_price'));

        }
        
    }

    public function checkout(CheckoutRequest $request) {

        $order = new Order;

    try {
        DB::transaction(function () use ($request, &$order) {
            $order = Order::create([
                'user_id'       => Auth::user()->id,
                'name'          => $request->name,
                'phone'         => $request->phone,
                'email'         => $request->email,
                'address'       => $request->address,
                'status'        => 'Chưa thanh toán',
                'total_price'   => $request->total_price,
                'expired_at'    => Carbon::now()->addMinutes(15) 

            ]);

            if ($request->has('cart_id')) {
                $cart_ids = $request->cart_id;
                $cart_items = Cartitem::whereIn('id', $cart_ids)->get();

                foreach ($cart_items as $item) {
                    OrderItem::create([
                        'order_id'      => $order->id,
                        'product_id'    => $item->product_id,
                        'product_name'  => $item->product->name,
                        'price'         => $item->product->price,
                        'quantity'      => $item->quantity,
                        'total_price'   => $item->product->price * $item->quantity
                    ]);
                }

                Cartitem::destroy($cart_ids);

            } elseif ($request->has('product_id')) {
                $product = Product::findOrFail($request->product_id);

                OrderItem::create([
                    'order_id'      => $order->id,
                    'product_id'    => $product->id,
                    'product_name'  => $product->name,
                    'price'         => $product->price,
                    'quantity'      => $request->quantity,
                    'total_price'   => $product->price * $request->quantity,
                ]);
            }
        });

            return $this->vnpayShow($order);
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }

        
    }


    public function vnpayShow(Order $order)
    {
        
        $vnp_Url = config('services.vnpay.url');
        $vnp_Returnurl = config('services.vnpay.return_url');
        $vnp_TmnCode = config('services.vnpay.tmn_code');
        $vnp_HashSecret = config('services.vnpay.hash_secret');

        $vnp_TxnRef = $order->id . '-' . time(); // mã đơn hàng
        $vnp_OrderInfo = "Thanh toán đơn hàng #" . $order->id;
        $vnp_OrderType = 'billpayment';
        $vnp_Amount = $order->total_price * 100; // VNPAY yêu cầu đơn vị là VNĐ * 100
        $vnp_Locale = 'vn';
        $vnp_BankCode = '';
        $vnp_IpAddr = request()->ip();

        $inputData = [
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => now()->format('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => $vnp_Locale,
            "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_OrderType" => $vnp_OrderType,
            "vnp_ReturnUrl" => $vnp_Returnurl,
            "vnp_TxnRef" => $vnp_TxnRef,
        ];

        // Sắp xếp mảng theo key
        ksort($inputData);
        $query = "";
        $i = 0;
        $hashdata = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }

        $vnp_Url = $vnp_Url . "?" . $query;
        if (isset($vnp_HashSecret)) {
            $vnpSecureHash =   hash_hmac('sha512', $hashdata, $vnp_HashSecret);//  
            $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
        }
        return redirect($vnp_Url);
    }

    public function vnpayReturn(Request $request)
    {

        $inputData = $request->all();
        $vnp_HashSecret = config('services.vnpay.hash_secret');

        $vnp_SecureHash = $inputData['vnp_SecureHash'];
        unset($inputData['vnp_SecureHash']);
        unset($inputData['vnp_SecureHashType']);

        ksort($inputData);
        $i = 0;
        $hashData = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashData = $hashData . '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashData = $hashData . urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
        }

        $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);

        if ($secureHash === $vnp_SecureHash && $request->vnp_ResponseCode == '00') {
            // Thanh toán thành công
            $orderId = explode('-', $request->vnp_TxnRef)[0];
            $order = Order::find($orderId);
            $order->status = 'Đã thanh toán';
            $order->save();

            return view('project_1.customer.checkout.success',compact('order'));
        } else {
            $orderId = explode('-', $request->vnp_TxnRef)[0];
            $order = Order::find($orderId);
            $order->status = 'Thanh toán thất bại';
            $order->save();
            return view('project_1.customer.checkout.error');
        }
    }
}