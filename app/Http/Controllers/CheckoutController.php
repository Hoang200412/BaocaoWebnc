<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Http\Requests\CheckoutRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Cartitem;
use Illuminate\Support\Facades\DB;
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
                $address = $this->formatAddressFromRequest($request);
                $shippingFee = $this->resolveShippingFee(
                    (int) $request->input('district_id'),
                    (string) $request->input('ward_code'),
                    (float) $request->input('shipping_fee', 0)
                );
                $itemsTotal = (float) $request->input('total_price', 0);
                $orderTotal = $itemsTotal + $shippingFee;
                $payment_method = $request->payment_method ?? 'cod';
                
                $order = Order::create([
                    'user_id'           => Auth::user()->id,
                    'name'              => $request->name,
                    'phone'             => $request->phone,
                    'email'             => $request->email,
                    'address'           => $address,
                    'status'            => 'Chờ duyệt',
                    'payment_method'    => $payment_method,
                    'payment_status'    => 'Chưa thanh toán',
                    'total_price'       => $orderTotal,
                    'shipping_fee'      => $shippingFee,
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
        return response()->json([
            'configured' => false,
            'fee' => 0,
            'message' => 'Vui long su dung tinh phi ship GHN.',
        ], 200);
    }
    public function getProvinces()
    {
        return $this->proxyGhnList('/master-data/province');
    }

    public function getDistricts($provinceCode)
    {
        return $this->proxyGhnList('/master-data/district', [
            'province_id' => $provinceCode,
        ]);
    }

    public function getWards($provinceCode, $districtCode)
    {
        return $this->proxyGhnList('/master-data/ward', [
            'district_id' => $districtCode,
        ]);
    }

    public function calculateShippingByLocation(Request $request)
    {
        $request->validate([
            'district_id' => 'required|numeric',
            'ward_code' => 'required|string',
            'street_address' => 'required|string',
        ]);

        return response()->json(
            $this->requestGhnFee(
                (int) $request->input('district_id'),
                (string) $request->input('ward_code')
            )
        );
    }

    private function formatAddressFromRequest(Request $request)
    {
        $parts = array_filter([
            $request->input('street_address'),
            $request->input('ward_name'),
            $request->input('district_name'),
            $request->input('province_name'),
        ]);

        return implode(', ', $parts);
    }

    private function resolveShippingFee($districtId, $wardCode, $fallbackFee)
    {
        $response = $this->requestGhnFee($districtId, $wardCode);
        if (!empty($response['configured']) && isset($response['fee'])) {
            return (float) $response['fee'];
        }

        return (float) $fallbackFee;
    }

    private function requestGhnFee($toDistrictId, $toWardCode)
    {
        $token = config('services.ghn.token');
        $shopId = config('services.ghn.shop_id');
        $baseUrl = rtrim((string) config('services.ghn.base_url'), '/');

        if (empty($token) || empty($shopId) || empty($baseUrl)) {
            return [
                'configured' => false,
                'fee' => 0,
                'message' => 'Chua cau hinh GHN sandbox. Vui long cap nhat .env.',
            ];
        }

        $payload = [
            'from_district_id' => (int) config('services.ghn.from_district_id'),
            'from_ward_code' => (string) config('services.ghn.from_ward_code'),
            'service_type_id' => (int) config('services.ghn.service_type_id'),
            'to_district_id' => (int) $toDistrictId,
            'to_ward_code' => (string) $toWardCode,
            'weight' => (int) config('services.ghn.weight'),
            'height' => (int) config('services.ghn.height'),
            'length' => (int) config('services.ghn.length'),
            'width' => (int) config('services.ghn.width'),
            'insurance_value' => (int) config('services.ghn.insurance_value'),
            'coupon' => null,
        ];

        try {
            $response = Http::withOptions([
                'verify' => (bool) config('services.ghn.verify_ssl', true),
            ])->withHeaders([
                'Token' => $token,
                'ShopId' => $shopId,
            ])->post($baseUrl . '/v2/shipping-order/fee', $payload);

            if (!$response->ok()) {
                $payload = [
                    'configured' => true,
                    'fee' => 0,
                    'message' => 'Khong lay duoc phi ship tu GHN.',
                    'ghn_status' => 'HTTP_' . $response->status(),
                ];

                if (config('app.debug')) {
                    $payload['ghn_body'] = $response->json();
                }

                return $payload;
            }

            $data = $response->json();
            if (($data['code'] ?? 0) !== 200) {
                $payload = [
                    'configured' => true,
                    'fee' => 0,
                    'message' => $data['message'] ?? 'GHN tra ve loi.',
                ];

                if (config('app.debug')) {
                    $payload['ghn_body'] = $data;
                }

                return $payload;
            }

            return [
                'configured' => true,
                'fee' => $data['data']['total'] ?? 0,
                'message' => 'Phi van chuyen da duoc tinh toan.',
            ];
        } catch (\Throwable $e) {
            return [
                'configured' => true,
                'fee' => 0,
                'message' => 'Khong the tinh phi ship luc nay.',
                'exception' => class_basename($e),
            ];
        }
    }

    private function proxyGhnList($path, array $query = [])
    {
        $token = config('services.ghn.token');
        $baseUrl = rtrim((string) config('services.ghn.base_url'), '/');

        if (empty($token) || empty($baseUrl)) {
            if (config('app.debug')) {
                return response()->json([
                    'ok' => false,
                    'message' => 'GHN token/base url chua cau hinh.',
                ], 500);
            }

            return response()->json([]);
        }

        try {
            $response = Http::withOptions([
                'verify' => (bool) config('services.ghn.verify_ssl', true),
            ])->withHeaders(['token' => $token])
                ->get($baseUrl . $path, $query);

            if (!$response->ok()) {
                if (config('app.debug')) {
                    return response()->json([
                        'ok' => false,
                        'message' => 'GHN response not OK.',
                        'status' => $response->status(),
                        'body' => $response->json(),
                    ], 500);
                }

                return response()->json([]);
            }

            $data = $response->json();
            if (($data['code'] ?? 0) !== 200) {
                if (config('app.debug')) {
                    return response()->json([
                        'ok' => false,
                        'message' => $data['message'] ?? 'GHN tra ve loi.',
                        'code' => $data['code'] ?? null,
                    ], 500);
                }

                return response()->json([]);
            }

            return response()->json($data['data'] ?? []);
        } catch (\Throwable $e) {
            if (config('app.debug')) {
                return response()->json([
                    'ok' => false,
                    'message' => 'Khong the goi GHN.',
                    'exception' => class_basename($e),
                    'exception_message' => $e->getMessage(),
                    'exception_message' => $e->getMessage(),
                ], 500);
            }

            return response()->json([]);
        }
    }
}