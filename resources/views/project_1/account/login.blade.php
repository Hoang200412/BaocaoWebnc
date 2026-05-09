<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
    <title>Đăng nhập</title>
</head>
<body>
    <section class="vh-100">
        <div class="container py-5 h-100">
          <div class="row d-flex align-items-center justify-content-center h-100">
            <div class="col-md-8 mb-4 mb-md-0 col-lg-7 col-xl-6">
              <img src="https://mdbcdn.b-cdn.net/img/Photos/new-templates/bootstrap-login-form/draw2.svg"
                class="img-fluid" alt="Phone image">
            </div>
            <div class="col-md-7 col-lg-5 col-xl-5 offset-xl-1">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show mx-4" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @elseif (session('error'))
                    <div class="alert alert-error alert-dismissible fade show mx-4" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <form action="{{route('login')}}" method="POST">
                <!-- Email input -->
                    @csrf
                    <div data-mdb-input-init class="form-outline mb-4">
                    <input type="text" name="username" id="form1Example13" class="form-control form-control-lg" value ="{{old('username')}}"/>
                    <label class="form-label"  for="form1Example13">Tên đăng nhập</label>
                    </div>
        
                    <!-- Password input -->
                    <div data-mdb-input-init class="form-outline mb-4">
                    <input type="password" name="password" id="form1Example23" class="form-control form-control-lg" />
                    <label class="form-label" for="form1Example23">Mật Khẩu</label>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="" id="form1Example3" checked />
                        <label class="form-check-label" for="form1Example3">Lưu mật khẩu</label>
                    </div>
                    </div>
                    <div class="pt-1 mb-4 d-flex flex-column ">
                        <button data-mdb-button-init data-mdb-ripple-init class="btn btn-dark btn-lg" type="submit">Đăng nhập</button>
                    </div>

                    <a href="{{route('showForgotForm')}}">Quên mật khẩu?</a>
                    <div class="d-flex mt-3">
                        <span>Bạn chưa có tài khoản?</span>
                        <a class="mx-2" href="{{route('register')}}">Đăng ký</a>
                    </div>

                </form>
                <a href="{{route('google')}}" class="text-decoration-none">
                    <div class="google_login d-flex border border-dark-subtle p-2 rounded-3 mt-3 align-items-center justify-content-center" style="cursor:pointer; transition: background-color 0.2s ease, box-shadow 0.2s ease;" onmouseover="this.style.backgroundColor='#f8f9fa'; this.style.boxShadow='0 2px 8px rgba(0,0,0,0.1)'" onmouseout="this.style.backgroundColor=''; this.style.boxShadow=''">
                        <div class="gg_img" style="width: 24px; height: 24px; flex-shrink: 0;">
                            <svg viewBox="0 0 48 48" width="24" height="24">
                                <path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/>
                                <path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/>
                                <path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"/>
                                <path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/>
                            </svg>
                        </div>
                        <div class="mx-3">
                            <span class="text-dark fw-medium">Đăng nhập bằng Google</span>
                        </div>
                    </div>
                </a>
            </div>
          </div>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js" integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq" crossorigin="anonymous"></script>

</body>
</html>