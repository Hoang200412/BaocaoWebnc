@extends('project_1.customer.layouts.layout')

@section('content')
    <link rel="stylesheet" href="{{ asset('css/payment-method.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
    <style>
        .select2-container--default .select2-selection--single {
            height: 52px;
            padding: 10px 12px;
            border: 1px solid #dee2e6;
            border-radius: 10px;
            display: flex;
            align-items: center;
            background: #fff;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #212529;
            line-height: 28px;
            padding-left: 0;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 52px;
            right: 10px;
        }

        .select2-container--default .select2-selection--single .select2-selection__placeholder {
            color: #6c757d;
        }

        .select2-container--default .select2-dropdown {
            border-radius: 12px;
            border: 1px solid #dee2e6;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
        }

        .select2-container--default .select2-search--dropdown .select2-search__field {
            border-radius: 8px;
            border: 1px solid #ced4da;
            padding: 8px 10px;
        }

        .select2-container--default .select2-results__option--highlighted.select2-results__option--selectable {
            background: #0d6efd;
            color: #fff;
        }

        .select2-container--default .select2-results__option--selected {
            background: #e9f2ff;
            color: #0d6efd;
        }
    </style>
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

                            <div class="mb-3">
                                <label class="form-label">Địa chỉ nhận hàng</label>
                                <div class="row g-2">
                                    <div class="col-12 mb-2">
                                        <select id="province-select" name="province_id" class="form-select" required>
                                            <option value="">Chọn tỉnh / thành phố</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <select id="district-select" name="district_id" class="form-select" required>
                                            <option value="">Chọn quận / huyện</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <select id="ward-select" name="ward_code" class="form-select" required>
                                            <option value="">Chọn phường / xã</option>
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <input type="text" id="street-address" name="street_address" class="form-control" placeholder="Số nhà, tên đường" value="{{ Auth::user()->address }}">
                                    </div>
                                    <input type="hidden" id="province-name" name="province_name" value="">
                                    <input type="hidden" id="district-name" name="district_name" value="">
                                    <input type="hidden" id="ward-name" name="ward_name" value="">
                                </div>
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
                                <div class="small text-muted mt-1" id="shipping-note">Phí ship sẽ được tính khi chọn đầy đủ địa chỉ.</div>
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

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        (function(){
            const provincesUrl = "{{ route('locations.provinces') }}";
            const districtsUrlTemplate = "{{ url('home/locations') }}"; // will append /{province}/districts
            const wardsUrlTemplate = "{{ url('home/locations') }}"; // will append /{province}/{district}/wards
            const calcShippingUrl = "{{ route('checkout.calculate_shipping') }}";
            const subtotal = parseFloat({{ $total_price }});
            const csrfToken = document.querySelector('form input[name="_token"]').value;

            function formatVnd(n) {
                return new Intl.NumberFormat('vi-VN').format(n) + ' đ';
            }

            async function fetchJson(url, opts = {}){
                const res = await fetch(url, opts);
                if (!res.ok) throw new Error('Network error');
                return res.json();
            }

            async function loadProvinces(){
                const sel = document.getElementById('province-select');
                sel.innerHTML = '<option value="">Chọn tỉnh / thành phố</option>';
                try{
                    const data = await fetchJson(provincesUrl);
                    data.forEach(p => {
                        const opt = document.createElement('option');
                        opt.value = p.ProvinceID;
                        opt.textContent = p.ProvinceName;
                        sel.appendChild(opt);
                    });
                    if (window.jQuery) {
                        $('#province-select').trigger('change.select2');
                    }
                }catch(e){
                    console.error(e);
                }
            }

            async function loadDistricts(provinceCode){
                const sel = document.getElementById('district-select');
                sel.innerHTML = '<option value="">Chọn quận / huyện</option>';
                if(!provinceCode) return;
                try{
                    const url = `${districtsUrlTemplate}/${provinceCode}/districts`;
                    const data = await fetchJson(url);
                    data.forEach(d => {
                        const opt = document.createElement('option');
                        opt.value = d.DistrictID;
                        opt.textContent = d.DistrictName;
                        sel.appendChild(opt);
                    });
                    if (window.jQuery) {
                        $('#district-select').trigger('change.select2');
                    }
                }catch(e){console.error(e)}
            }

            async function loadWards(provinceCode, districtCode){
                const sel = document.getElementById('ward-select');
                sel.innerHTML = '<option value="">Chọn phường / xã</option>';
                if(!provinceCode || !districtCode) return;
                try{
                    const url = `${wardsUrlTemplate}/${provinceCode}/${districtCode}/wards`;
                    const data = await fetchJson(url);
                    data.forEach(w => {
                        const opt = document.createElement('option');
                        opt.value = w.WardCode;
                        opt.textContent = w.WardName;
                        sel.appendChild(opt);
                    });
                    if (window.jQuery) {
                        $('#ward-select').trigger('change.select2');
                    }
                }catch(e){console.error(e)}
            }

            async function calculateShipping(){
                const district = document.getElementById('district-select').value;
                const ward = document.getElementById('ward-select').value;
                const street = document.getElementById('street-address').value.trim();
                const note = document.getElementById('shipping-note');
                if(!district || !ward || !street) {
                    if (note) note.textContent = 'Phí ship sẽ được tính khi chọn đầy đủ địa chỉ.';
                    return;
                }
                try{
                    const res = await fetch(calcShippingUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({
                            district_id: district,
                            ward_code: ward,
                            street_address: street
                        })
                    });
                    const data = await res.json();
                    const fee = Number(data.fee || 0);
                    const shippingDisplay = document.getElementById('shipping-display');
                    const grandTotalDisplay = document.getElementById('grandtotal-display');
                    const shippingFeeInput = document.getElementById('shipping_fee_input');
                    if (note) note.textContent = data.message || '';

                    if (shippingDisplay) shippingDisplay.textContent = formatVnd(fee);
                    if (shippingFeeInput) shippingFeeInput.value = fee;
                    const grand = subtotal + fee;
                    if (grandTotalDisplay) grandTotalDisplay.textContent = formatVnd(grand);
                }catch(e){ console.error(e); }
            }

            document.addEventListener('DOMContentLoaded', function(){
                if (window.jQuery) {
                    $('#province-select').select2({
                        width: '100%',
                        placeholder: 'Chọn tỉnh / thành phố',
                        allowClear: true
                    });
                    $('#district-select').select2({
                        width: '100%',
                        placeholder: 'Chọn quận / huyện',
                        allowClear: true
                    });
                    $('#ward-select').select2({
                        width: '100%',
                        placeholder: 'Chọn phường / xã',
                        allowClear: true
                    });
                }

                loadProvinces();

                const onProvinceChange = function(){
                    const province = this.value;
                    const provinceName = this.options[this.selectedIndex]?.textContent || '';
                    document.getElementById('province-name').value = provinceName;
                    loadDistricts(province);
                    // clear wards
                    document.getElementById('ward-select').innerHTML = '<option value="">Chọn phường / xã</option>';
                    if (window.jQuery) {
                        $('#ward-select').val(null).trigger('change');
                    }
                    document.getElementById('district-name').value = '';
                    document.getElementById('ward-name').value = '';
                    calculateShipping();
                };

                const onDistrictChange = function(){
                    const district = this.value;
                    const districtName = this.options[this.selectedIndex]?.textContent || '';
                    document.getElementById('district-name').value = districtName;
                    const province = document.getElementById('province-select').value;
                    loadWards(province, district);
                    document.getElementById('ward-name').value = '';
                    calculateShipping();
                };

                const onWardChange = function(){
                    const wardName = this.options[this.selectedIndex]?.textContent || '';
                    document.getElementById('ward-name').value = wardName;
                    calculateShipping();
                };

                if (window.jQuery) {
                    $('#province-select').on('change', onProvinceChange);
                    $('#district-select').on('change', onDistrictChange);
                    $('#ward-select').on('change', onWardChange);
                } else {
                    document.getElementById('province-select').addEventListener('change', onProvinceChange);
                    document.getElementById('district-select').addEventListener('change', onDistrictChange);
                    document.getElementById('ward-select').addEventListener('change', onWardChange);
                }

                document.getElementById('street-address').addEventListener('blur', function(){
                    calculateShipping();
                });

                // If user already has address, keep subtotal displayed
                const shippingDisplay = document.getElementById('shipping-display');
                const grandTotalDisplay = document.getElementById('grandtotal-display');
                const note = document.getElementById('shipping-note');
                if (shippingDisplay) shippingDisplay.textContent = '0 đ';
                if (grandTotalDisplay) grandTotalDisplay.textContent = formatVnd(subtotal);
                if (note) note.textContent = 'Phí ship sẽ được tính khi chọn đầy đủ địa chỉ.';
            });
        })();
    </script>
@endsection