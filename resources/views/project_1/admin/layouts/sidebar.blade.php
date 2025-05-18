<aside class="menu-sidebar d-none d-lg-block">
    <div class="logo">
        <a href="{{route('admin')}}">
            <img src="{{asset('images/logo.png')}}" alt="Cool Admin" />
        </a>
    </div>
    <div class="menu-sidebar__content js-scrollbar1">
        <nav class="navbar-sidebar">
            <ul class="list-unstyled navbar__list">

                <li class="nav-link">
                    <a class="nav-link" href="{{route('admin')}}" role="button">
                        <i class="fa-solid fa-house"></i>
                        Trang chủ
                    </a>
                </li>
              
                <li class="dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fa-solid fa-book"></i>
                        Sản phẩm
                    </a>
                    <ul class="dropdown-menu px-2">
                        <li><a class="dropdown-item px-2" href="{{route('products.index')}}">Quản lý sản phẩm</a></li>
                        <li><a class="dropdown-item px-2" href="{{route('categories.index')}}">Quản lý danh mục</a></li>
                        <li><a class="dropdown-item px-2" href="{{route('tags.index')}}">Quản lý thẻ</a></li>
                        
                    </ul>
                </li>
                <li class="nav-link">
                    <a class="nav-link" href="{{route('banners.index')}}" role="button">
                        <i class="fa-solid fa-sliders"></i>
                        Quản lí banner
                    </a>
                </li>
                 <li class="nav-link">
                    <a class="nav-link" href="{{route('settings.index')}}" role="button">
                        <i class="fa-solid fa-gear"></i>
                        Quản lí setting
                    </a>
                </li>

                <li class="nav-link">
                    <a class="nav-link" href="{{route('blogs.index')}}" role="button">
                        <i class="fa-solid fa-newspaper"></i>
                        Quản tin tức
                    </a>
                </li>
                <li class="nav-link">
                    <a class="nav-link" href="{{route('orders.index')}}" role="button">
                        <i class="fa-solid fa-bag-shopping"></i>
                        Quản đơn hàng
                    </a>
                </li>

          
                
            </ul>
        </nav>
    </div>
</aside>