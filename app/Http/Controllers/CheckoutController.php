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
use Illuminate\Support\Facades\Http;



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
                $payment_method = $request->payment_method ?? 'cod';
                
                $order = Order::create([
                    'user_id'           => Auth::user()->id,
                    'name'              => $request->name,
                    'phone'             => $request->phone,
                    'email'             => $request->email,
                    'address'           => $request->address,
                    'status'            => 'Chờ duyệt',
                    'payment_method'    => $payment_method,
                    'payment_status'    => 'Chưa thanh toán',
                    'total_price'       => $request->total_price,
                    'expired_at'        => Carbon::now()->addMinutes(15) 
                ]);
                if ($request->has('cart_id')) {
                    $cart_ids = $request->cart_id;
                    $cart_items = Cartitem::whereIn('id', $cart_ids)->get();

                    foreach ($cart_items as $item) {
                        // Lock the product row for this transaction to avoid race conditions
                        $product = Product::where('id', $item->product_id)->lockForUpdate()->first();
                        if (!$product) {
                            throw new \Exception('Sản phẩm không tồn tại');
                        }

                        if ($product->quantity < $item->quantity) {
                            throw new \Exception("Sản phẩm '{" . $product->name . "}' chỉ còn {$product->quantity} nhưng bạn đặt {$item->quantity}.");
                        }

                        // Decrement stock
                        $product->decrement('quantity', $item->quantity);

                        OrderItem::create([
                            'order_id'      => $order->id,
                            'product_id'    => $item->product_id,
                            'product_name'  => $product->name,
                            'price'         => $product->price,
                            'quantity'      => $item->quantity,
                            'total_price'   => $product->price * $item->quantity
                        ]);
                    }

                    Cartitem::destroy($cart_ids);

                } elseif ($request->has('product_id')) {
                        // Single product purchase: lock and validate quantity
                        $product = Product::where('id', $request->product_id)->lockForUpdate()->first();
                        if (!$product) {
                            throw new \Exception('Sản phẩm không tồn tại');
                        }

                        $qty = (int) $request->quantity;
                        if ($product->quantity < $qty) {
                            throw new \Exception("Sản phẩm '{" . $product->name . "}' chỉ còn {$product->quantity} nhưng bạn đặt {$qty}.");
                        }

                        // Decrement stock
                        $product->decrement('quantity', $qty);

                        OrderItem::create([
                            'order_id'      => $order->id,
                            'product_id'    => $product->id,
                            'product_name'  => $product->name,
                            'price'         => $product->price,
                            'quantity'      => $qty,
                            'total_price'   => $product->price * $qty,
                        ]);
                }
            });

            // Nếu là thanh toán khi nhận hàng thì trả về trang thành công
            if ($request->payment_method === 'cod') {
                return view('project_1.customer.checkout.success', compact('order'));
            } else {
                // Nếu là thanh toán qua VNPay thì chuyển hướng
                return $this->vnpayShow($order);
            }
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
        

        
    }


    public function vnpayShow(Order $order)
    {
        
        $vnp_Url        = config('services.vnpay.url');
        $vnp_ReturnUrl  = config('services.vnpay.return_url');
        $vnp_TmnCode    = config('services.vnpay.tmn_code');
        $vnp_HashSecret = config('services.vnpay.hash_secret');

        $vnp_TxnRef     = $order->id . '-' . time(); 
        $vnp_OrderInfo  = "Thanh toán đơn hàng #" . $order->id;
        $vnp_OrderType  = 'billpayment';
        $vnp_Amount     = $order->total_price * 100; 
        $vnp_Locale     = 'vn';
        $vnp_IpAddr     = request()->ip();  

        $inputData = [
            "vnp_Version"    => "2.1.0",
            "vnp_TmnCode"    => $vnp_TmnCode,
            "vnp_Amount"     => $vnp_Amount,
            "vnp_Command"    => "pay",
            "vnp_CreateDate" => now()->format('YmdHis'),
            "vnp_CurrCode"   => "VND",
            "vnp_IpAddr"     => $vnp_IpAddr,
            "vnp_Locale"     => $vnp_Locale,
            "vnp_OrderInfo"  => $vnp_OrderInfo,
            "vnp_OrderType"  => $vnp_OrderType,
            "vnp_ReturnUrl"  => $vnp_ReturnUrl,
            "vnp_TxnRef"     => $vnp_TxnRef,
            "vnp_BankCode"   => 'NCB',
        ];

        $vnp_BankCode = request()->input('bank_code'); // ví dụ: "NCB", "VNPAYQR", "VISA"
        if (!empty($vnp_BankCode)) {
            $inputData['vnp_BankCode'] = $vnp_BankCode;
        }

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
            $order->payment_status = 'Thanh toán thành công';
            $order->save();

            return view('project_1.customer.checkout.success',compact('order'));
        } else {
            $orderId = explode('-', $request->vnp_TxnRef)[0];
            $order = Order::find($orderId);
            $order->payment_status = 'Thanh toán thất bại';
            $order->save();
            return view('project_1.customer.checkout.error');
        }
    }

    /**
     * Calculate shipping fee based on distance between shop and customer address.
     * Expects `address` in the POST body.
     */
    public function shippingFee(Request $request)
    {
        $request->validate([
            'address' => 'required|string',
        ]);

        $shopAddress = '234 Hoang Quoc Viet, Hanoi, Vietnam';
        $dest = $request->input('address');

        $apiKey = config('services.google_maps.key');
        if (empty($apiKey)) {
            return response()->json([
                'configured' => false,
                'fee' => 0,
                'message' => 'Google Maps API key chưa được cấu hình. Phí ship tạm thời để 0 đ.',
            ]);
        }

        $params = [
            'origins' => $shopAddress,
            'destinations' => $dest,
            'key' => $apiKey,
            'units' => 'metric',
            'mode' => 'driving',
            'language' => 'vi',
            'region' => 'vn',
        ];

        $url = 'https://maps.googleapis.com/maps/api/distancematrix/json';

        try {
            $response = Http::get($url, $params);
            if (!$response->ok()) {
                return response()->json([
                    'configured' => true,
                    'fee' => 0,
                    'message' => 'Không lấy được dữ liệu khoảng cách từ Google Maps.',
                    'google_status' => 'HTTP_' . $response->status(),
                ], 200);
            }

            $data = $response->json();
            $googleStatus = $data['status'] ?? 'UNKNOWN';
            if (!isset($data['rows'][0]['elements'][0]) || $data['rows'][0]['elements'][0]['status'] !== 'OK') {
                $elementStatus = $data['rows'][0]['elements'][0]['status'] ?? 'UNKNOWN';
                return response()->json([
                    'configured' => true,
                    'fee' => 0,
                    'message' => 'Không xác định được khoảng cách cho địa chỉ đã nhập.',
                    'google_status' => $googleStatus,
                    'element_status' => $elementStatus,
                    'google_error_message' => $data['error_message'] ?? null,
                ], 200);
            }

            $element = $data['rows'][0]['elements'][0];
            $distanceMeters = $element['distance']['value'];
            $distanceText = $element['distance']['text'];

            // Shipping fee calculation example:
            // base 15,000 VND for up to 5 km, then +3,000 VND per km thereafter (rounded up)
            $km = $distanceMeters / 1000;
            $baseKm = 5;
            $baseFee = 15000;
            $perKmFee = 3000;

            $extraKm = max(0, ceil(max(0, $km - $baseKm)));
            $fee = $baseFee + ($extraKm * $perKmFee);

            return response()->json([
                'configured' => true,
                'distance_text' => $distanceText,
                'distance_meters' => $distanceMeters,
                'fee' => $fee,
                'google_status' => $googleStatus,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'configured' => true,
                'fee' => 0,
                'message' => 'Không thể tính phí ship lúc này.',
                'exception' => class_basename($e),
            ], 200);
        }
    }
}