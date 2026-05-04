@extends('project_1.customer.layouts.layout')

@section('content')
    <link rel="stylesheet" href="{{ asset('css/payment-method.css') }}">
    <main>
        <div class="container">
            @if ($errors->any())
                <div class="alert alert-danger mx-4">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show mx-4" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @elseif (session('error'))
                <div class="alert alert-danger alert-dismissible fade show mx-4" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <form action="{{route('checkout.handle')}}" method="post">
                @csrf
                @if (isset($product))
                    <input type="hidden" name="quantity" value="{{$quantity}}">
                    <input type="hidden" name="total_price" value="{{$total_price}}">
                    <input type="hidden" name="product_id" value="{{$product->id}}">
                @elseif (isset($cart_items))
                    @foreach ($cart_items as $item)
                        <input type="hidden" name="cart_id[]" value="{{$item->id}}">
                    @endforeach
                    <input type="hidden" name="total_price" value="{{$total_price}}">
                @endif
               
                <div class="row g-4">
                    <div class="col-12 col-lg-7">
                        <div class="information p-3 border  shadow">
                            <p class="fs-4 fw-medium text-center">Thông tin khách hàng</p>
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" name="name" id="fullname" placeholder="Họ và tên" value="{{Auth::user()->username}}">
                                <label for="fullname">Họ và tên</label>
                            </div>

                            <div class="form-floating mb-3">
                                <input type="email" class="form-control" name="email" id="floatingInput" placeholder="email" value="{{Auth::user()->email}}">
                                <label for="floatingInput">Email</label>
                            </div>

                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" name="phone" id="phone" placeholder="Số điện thoại" value="{{Auth::user()->phone}}">
                                <label for="phone">Số điện thoại</label>
                            </div>

                            <div class="form-floating mb-3">
                                <textarea class="form-control" placeholder="Địa chỉ" name="address" id="floatingTextarea2" style="height: 100px">{{Auth::user()->address}}</textarea>
                                <label for="floatingTextarea2">Địa chỉ</label>
                            </div>

                            <div class="mb-4">
                                <p class="fs-5 fw-medium mb-3">Phương thức thanh toán</p>
                                
                                <div class="payment-method-container">
                                    <!-- COD Payment -->
                                    <div class="payment-option mb-3">
                                        <input class="form-check-input payment-radio" type="radio" name="payment_method" id="payment_cod" value="cod" checked style="display: none;">
                                        <label for="payment_cod" class="payment-card payment-card-active w-100">
                                            <div class="d-flex align-items-center">
                                                <div class="payment-icon me-3">
                                                    <i class="fas fa-hand-holding-usd" style="font-size: 32px; color: #28a745;"></i>
                                                </div>
                                                <div class="payment-info">
                                                    <h6 class="mb-1">Thanh toán khi nhận hàng</h6>
                                                    <small class="text-muted">COD - Trả tiền khi nhận sản phẩm</small>
                                                </div>
                                            </div>
                                        </label>
                                    </div>

                                    <!-- VNPay Payment -->
                                    <div class="payment-option">
                                        <input class="form-check-input payment-radio" type="radio" name="payment_method" id="payment_vnpay" value="vnpay" style="display: none;">
                                        <label for="payment_vnpay" class="payment-card w-100">
                                            <div class="d-flex align-items-center">
                                                <div class="payment-icon me-3">
                                                    <i class="fas fa-credit-card" style="font-size: 32px; color: #007bff;"></i>
                                                </div>
                                                <div class="payment-info">
                                                    <h6 class="mb-1">Thanh toán qua VNPay</h6>
                                                    <small class="text-muted">Thẻ tín dụng, ví điện tử hoặc ngân hàng</small>
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <script>
                                document.querySelectorAll('.payment-radio').forEach(radio => {
                                    radio.addEventListener('change', function() {
                                        document.querySelectorAll('.payment-card').forEach(card => {
                                            card.classList.remove('payment-card-active');
                                        });
                                        if (this.checked) {
                                            this.nextElementSibling.classList.add('payment-card-active');
                                        }
                                    });
                                });
                            </script>

                            <script>
                                (function(){
                                    const shopShippingUrl = "{{ route('checkout.shipping_fee') }}";
                                    const subtotal = parseFloat({{ $total_price }});
                                    const csrfInput = document.querySelector('form input[name="_token"]');
                                    const csrfToken = csrfInput ? csrfInput.value : '';

                                    function formatVnd(n) {
                                        return new Intl.NumberFormat('vi-VN').format(n) + ' đ';
                                    }

                                    async function updateShipping(addressEl, shippingDisplay, grandTotalDisplay, shippingFeeInput, totalPriceInput) {
                                        const address = addressEl.value || '';
                                        if (!address.trim()) return;

                                        try {
                                            const res = await fetch(shopShippingUrl, {
                                                method: 'POST',
                                                headers: {
                                                    'Content-Type': 'application/json',
                                                    'X-CSRF-TOKEN': csrfToken
                                                },
                                                body: JSON.stringify({ address })
                                            });

                                            const data = await res.json();
                                            const fee = Number(data.fee || 0);

                                            if (shippingDisplay) {
                                                shippingDisplay.textContent = formatVnd(fee);
                                                if (data.message) {
                                                    shippingDisplay.title = data.message;
                                                }
                                            }

                                            if (shippingFeeInput) {
                                                shippingFeeInput.value = fee;
                                            }

                                            const grand = subtotal + fee;
                                            if (grandTotalDisplay) {
                                                grandTotalDisplay.textContent = formatVnd(grand);
                                            }

                                            if (totalPriceInput) {
                                                totalPriceInput.value = grand;
                                            }
                                        } catch (e) {
                                            console.error(e);
                                        }
                                    }

                                    document.addEventListener('DOMContentLoaded', function(){
                                        const addressEl = document.querySelector('#floatingTextarea2');
                                        const shippingDisplay = document.getElementById('shipping-display');
                                        const grandTotalDisplay = document.getElementById('grandtotal-display');
                                        const shippingFeeInput = document.getElementById('shipping_fee_input');
                                        const totalPriceInput = document.getElementById('total_price_input');

                                        if (!addressEl) {
                                            return;
                                        }

                                        let timer = null;
                                        addressEl.addEventListener('input', function(){
                                            clearTimeout(timer);
                                            timer = setTimeout(function() {
                                                updateShipping(addressEl, shippingDisplay, grandTotalDisplay, shippingFeeInput, totalPriceInput);
                                            }, 800);
                                        });

                                        if (addressEl.value && addressEl.value.trim()) {
                                            updateShipping(addressEl, shippingDisplay, grandTotalDisplay, shippingFeeInput, totalPriceInput);
                                        }
                                    });
                                })();
                            </script>

                        </div>
                    </div>
                    <div class="col-12 col-lg-5">
                        <div class="product_infor border border-dark-subtle p-3" >
                            <div class="accordion" id="accordionExample">
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button " type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                            Sản phẩm
                                        </button>
                                    </h2>
                                    <div id="collapseTwo" class="accordion-collapse collapse show" data-bs-parent="#accordionExample">
                                        <div class="accordion-body">
                                            @if (isset($cart_items))
                                                 @foreach($cart_items as $item)
                                                    <div class="row mb-3">
                                                        <div class="col-3">
                                                            <div class="image">
                                                                <img src="{{ asset(Storage::url($item->product->image_path)) }}" alt="" class="img-fluid">
                                                            </div>
                                                        </div>
                                                        <div class="col-9">
                                                            <div>
                                                                <div class="name">
                                                                    <span>{{ $item->product->name }}</span>
                                                                </div>
                                                                <div class="sl">
                                                                    <span>Số lượng: {{ $item->quantity }}</span>
                                                                </div>
                                                                <div class="price">
                                                                    <span>Giá :</span>
                                                                    <span class="text-danger">{{ number_format($item->product->price) }} đ</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @elseif(isset($product))
                                            <div class="row">
                                                <div class="col-3">
                                                    <div class="image">
                                                        <img src="{{asset(Storage::url($product->image_path))}}" alt="" class="img-fluid">
                                                    </div>
                                                </div>
                                                <div class="col-9">
                                                    <div>
                                                        <div class="name">
                                                            <span>{{$product->name}}</span>
                                                        </div>
                                                        <div class="sl">
                                                            <span >Số lượng: {{$quantity}}</span>
                                                        </div>
                                                        <div class="price">
                                                            <span>Giá :</span>
                                                            <span class="text-danger">{{number_format($product->price)}} đ</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            @endif
                                            
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="border-bottom border-2 mb-4 py-4">
                                <div class="d-flex justify-content-between ">
                                    <span>Tạm tính</span>
                                    <span class="text-danger" id="subtotal-display">{{number_format($total_price)}}đ</span>
                                </div>
                                <div class="d-flex justify-content-between "> 
                                    <span>Phí ship</span>
                                    <span class="text-danger" id="shipping-display">0 đ</span>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between my-3">
                                <span class="fs-5">Tổng cộng</span>
                                <span class="fs-5 text-danger" id="grandtotal-display">{{number_format($total_price)}}đ</span>
                            </div>
                            <input type="hidden" name="shipping_fee" id="shipping_fee_input" value="0">
                            <input type="hidden" name="total_price" id="total_price_input" value="{{ $total_price }}">

                            <div>
                                <button type="submit" class="btn btn-warning">Đặt hàng</button>
                            </div>


                        </div>

                    </div>
                </div>
            </form>
            
        </div>
    </main>
@endsection