<header>
    <div class="p-3 bg-warning">
        <span class="fs-6">Can we help you?</span>
        <span class="fs-6 ">{{getConfigValueFromSettingTable('phone')}}</span>
    </div>
    <div class="container">
        <div class="d-flex flex-wrap align-items-start justify-content-between py-3 my-3">
            <!-- Logo -->
            <div class="col-xl-3 col-lg-3 col-md-3 col-sm-4 col-4 d-flex align-items-center">
                <div class="logo">
                    <a href>
                        <img class="logo img-fluid"
                            src="https://demo3leotheme.b-cdn.net/prestashop/leo_bookery_demo/img/bookery-logo-1611909896.jpg"
                            alt="Leo Bookery logo">
                    </a>
                </div>
            </div>

            <!-- Search (order để nó xuống dưới khi màn nhỏ) -->
            <div class="col-12 col-xl-6 col-lg-5 col-md-6 col-sm-12 order-3 order-md-2 mt-md-0 mt-3">
                <div class="search">
                    <form action method="get">
                        <div class="d-flex form-control-sm bg-secondary-subtle rounded px-2">
                            <button type="submit" class="border-0 bg-transparent">
                                <i class="fa-solid fa-magnifying-glass"></i>
                            </button>
                            <input type="text" placeholder="search" class="form-control border-0 bg-transparent">
                        </div>
                    </form>
                </div>
            </div>

            <!-- Icons -->
            <div
                class="col-xl-3 col-lg-3 col-md-3 col-sm-4 col-4 d-flex justify-content-around align-items-center order-2">
                <div class="user_icon font-icon">
                    @guest
                        <div>
                            <a class="text-decoration-none fs-6 text-danger" href="{{route('login')}}" >Đăng Nhâp</a>
                        </div>
                    @endguest
                    @auth
                        <div class="noti__item dropdown">
                            <i class="fa-solid fa-user" data-bs-toggle="dropdown" aria-expanded="false" role="button"></i>
                    
                            <ul class="dropdown-menu dropdown-menu-end p-3" style="width: 350px;">
                                <!-- Thông tin người dùng -->
                                <form action="">
                                    <li>
                                        @php
                                            $avatar = Auth::user()->avatar;
                                            $avatarUrl = '';

                                            if ($avatar && Storage::disk('public')->exists($avatar)) {
                                                $avatarUrl = asset(Storage::url($avatar));
                                            } else {
                                                $avatarUrl = $avatar; // hoặc thay thế bằng ảnh mặc định nếu muốn
                                            }
                                        @endphp

                                        <div class="text-center">
                                            <img src="{{ $avatarUrl }}" alt="Avatar" class="rounded-circle mb-2" width="100" height="100">
                                            <h5 class="mb-0">{{ Auth::user()->username }}</h5>

                                            @php
                                                $avatar = Auth::user()->avatar;
                                                $avatarUrl = '';

                                                if (!empty($avatar) && Storage::disk('public')->exists($avatar)) {
                                                    $avatarUrl = asset(Storage::url($avatar));
                                                } elseif (!empty($avatar)) {
                                                    $avatarUrl = $avatar;
                                                } else {
                                                    $avatarUrl = asset('images/default-avatar.png'); // fallback ảnh mặc định
                                                }
                                            @endphp

                                            <img src="{{ $avatarUrl }}" alt="Avatar" class="rounded-circle mb-2" width="100" height="100">


                                            <h5 class="mb-0">{{Auth::user()->username}}</h5>
                                        </div>

                                        <hr>
                                        <div class="mb-2">
                                            <strong>Full Name:</strong> {{Auth::user()->username}}
                                        </div>
                                        <div class="mb-2">
                                            <strong>Email:</strong> {{Auth::user()->email}}
                                        </div>
                                        
                                        <div class="mb-3">
                                            <strong>Address:</strong> {{Auth::user()->address}}
                                        </div>
                        
                                        <div class="text-center">
                                            
                                            <a href="{{route('editInformation')}}" class="btn btn-default">Sửa thông tin </a>
                                        </div>
                                    </li>
                                </form>
                                <li><hr class="dropdown-divider"></li>
                                <!-- Đơn hàng -->
                                <li>
                                <a class="dropdown-item" href="{{ route('order') }}">Đơn hàng</a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <!-- Đăng xuất -->
                                <li class="d-flex">
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button class="dropdown-item text-danger" type="submit">Đăng xuất</button>
                                    </form>
                                    <form action="{{route('editPassword')}}">
                                        @csrf
                                        <button class="dropdown-item " type="submit">Đổi mật khẩu</button>
                                    </form>
                                </li>
                
                            </ul>
                        </div>
                    @endauth
                </div>
                
                <div class="cart_icon font-icon">
                    <a class="text-dark" href="{{route('showCart')}}">
                        <i class="fa-solid fa-cart-shopping fs-5"></i>
                    </a>
                </div>
                <div class="setting_icon font-icon">
                    <i class="fa-solid fa-gear fs-5"></i>
                </div>
            </div>

            <div class="col-xl-6 col-lg-6 order-2 order-lg-3 mx-md-auto mx-0 mt-md-2 mt-0">
                <nav class="navbar navbar-expand-md bg-body-tertiary">
                    <div class="container-fluid">

                        <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                            data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false"
                            aria-label="Toggle navigation">
                            <span class="navbar-toggler-icon"></span>
                        </button>
                        <div class="collapse navbar-collapse  justify-content-center" id="navbarNav">
                            <ul class="navbar-nav">
                                <li class="nav-item">
                                    <a class="nav-link fw-bolder" aria-current="page" href="{{route('home')}}">Trang Chủ</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link fw-bolder" href="{{route('custom.category.index')}}">Sách</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link fw-bolder" href="{{route('about.index')}}">Giới thiệu</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link fw-bolder" href="#">Tin tức</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link fw-bolder" href="{{route('helppage.index')}}">Liên hệ</a>
                                </li>

                            </ul>
                        </div>
                    </div>
                </nav>
            </div>
        </div>

    </div>

</header>