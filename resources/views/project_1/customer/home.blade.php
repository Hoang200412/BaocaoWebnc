

@extends('project_1.customer.layouts.layout')

@section('content')
<main>
    <!-- banner -->
    <div id="carouselExampleControlsNoTouching" class="carousel slide" data-bs-touch="false">
        <div class="carousel-inner">
            @foreach ($banners as $banner)
                <div class="carousel-item active">
                    <img src="{{ asset(Storage::url($banner->image_path)) }}" 
                        class="img-fluid rounded-3">
                </div>
            @endforeach
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleControlsNoTouching" data-bs-slide="prev">
          <span class="carousel-control-prev-icon" aria-hidden="true"></span>
          <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleControlsNoTouching" data-bs-slide="next">
          <span class="carousel-control-next-icon" aria-hidden="true"></span>
          <span class="visually-hidden">Next</span>
        </button>
    </div>

    <!-- content -->
    <div class="container mt-5">
        <div class="row d-flex align-items-center justify-content-between mb-5">
            <div class="col-12 col-md-3">
                <span class="fs-4 fw-bolder">Bán chạy</span>
            </div>
            <div class="col-6 d-none d-md-block">
                <div class="border-1 border-black border-bottom"></div>
            </div>
            <div class="col-3 text-end d-none d-md-flex">
                <a href="" class="btn btn-danger rounded-pill">
                    Xem thêm sách
                </a>
            </div>
        </div>

        <div class="swiper mySwiper">
            <div class="swiper-wrapper">
                <!-- sản phẩm -->
            
            @foreach ($products_best as $product)
              <div class="swiper-slide py-4" >
                <!-- Nội dung sản phẩm -->
                <div class="product-meta rounded-3">
                    <a class="product text-decoration-none text-dark" href="{{route('product', $product)}}">
                        <div class="product-img">
                            @if($product)
                                <img src="{{asset(Storage::url($product->image_path))}}" alt="" class="img-fluid rounded-3"> 
                            @endif
                        </div>
                        <div class="product-content p-2 ">
                            <div class="category">
                                <span class="text-gray fw-medium" >{{$product->category->name}}</span>
                            </div>
                            <h3 class="fs-6 text-truncate product-name" >{{$product->name}}</h3>
                            <div class="rank d-flex">
                                <i class="fa-regular fa-star"></i>
                                <i class="fa-regular fa-star"></i>
                                <i class="fa-regular fa-star"></i>
                                <i class="fa-regular fa-star"></i>
                                <i class="fa-regular fa-star"></i>
                            </div>
                            <div class="product-price">
                                <span class="text-danger fw-medium">{{number_format($product->price)}}đ</span>
                            </div>
                            
                          </a>
                        </div>
                    </a>
                </div>
              </div>
              @endforeach
            
              <!-- ... -->
            </div>
        </div>

        <div class="row d-flex align-items-center justify-content-between my-5">
            <div class="col-12 col-md-3">
                <span class="fs-4 fw-bolder">Ưu đãi trong tuần</span>
            </div>
            <div class="col-6 d-none d-md-block">
                <div class="border-1 border-black  border-bottom "></div>
            </div>
            <div class="col-3 text-end d-none d-md-flex">
                <a href="" class="btn btn-danger rounded-pill">
                    Xem thêm sách
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-12 col-md-8 mb-4 mb-md-3">
                <div class="new-product rounded-5">
                </div>
            </div>

            <div class="col-12 col-md-4 d-inline-block">
                @foreach ($product_sell as $product)
                    <div class="product-item mb-3 py-2 border-1 border-bottom border-dark-subtle">
                    <a href="{{route('product', $product->id)}}" class="d-flex text-decoration-none text-dark">
                        <div class="col-3 col-md-6 col-lg-4 me-3">
                            <div class="image">
                                <img src="{{asset(Storage::url($product->image_path))}}" alt="" class="img-fluid rounded-3">
                            </div>
                        </div>
                        <div class="col-10 col-md-8 py-3">
                            <div class="category text-gray fw-medium ">{{$product->category->name}}</div>
                            <h5 class="fs-5 my-2 product-name">{{$product->name}}</h5>
                            <div class="rank d-flex my-3">
                                <i class="fa-regular fa-star"></i>
                                <i class="fa-regular fa-star"></i>
                                <i class="fa-regular fa-star"></i>
                                <i class="fa-regular fa-star"></i>
                                <i class="fa-regular fa-star"></i>
                            </div>
                            <div class="product-price">
                                <span class="fw-medium text-danger fs-5">{{number_format($product->price)}}đ</span>
                            </div>
                        </div>
                    </a>
                </div> 
                @endforeach
              
            </div>
            
        </div>
        
        <div class="row d-flex align-items-center justify-content-between my-5">
            <div class="col-12 col-md-3">
                <span class="fs-4 fw-bolder">Được đánh giá cao</span>
            </div>
            <div class="col-6 d-none d-md-block">
                <div class="border-1 border-black border-bottom"></div>
            </div>
            <div class="col-3 text-end d-none d-md-flex">
                <a href="" class="btn btn-danger rounded-pill">
                    Xem thêm sách
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-12 col-md-6 mb-3">
                <div class="rounded-4 bg-danger-subtle p-4">
                    <div class="row">
                        <div class="col-6">
                            <div class="image">
                                @if($hightrate_1)
                                <img src="{{asset(Storage::url($hightrate_1->image_path))}}" alt="" class="img-fluid rounded-3">
                                @endif
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="category">
                                <span class="fw-medium text-gray">{{$hightrate_1->category->name}}</span>
                            </div> 
                            <a class="text-decoration-none text-dark" href="{{route('product',$hightrate_1->id)}}">
                                <h3 class="product-name two-line-truncate" style="">{{$hightrate_1->name}}</h3>
                            </a>
                            <div class="description">
                                <span class="fs-6 four-line-truncate">{{$hightrate_1->description}}</span>
                            </div>
                            <div class="rank d-flex my-2 fs-6">
                                <i class="fa-regular fa-star"></i>
                                <i class="fa-regular fa-star"></i>
                                <i class="fa-regular fa-star"></i>
                                <i class="fa-regular fa-star"></i>
                                <i class="fa-regular fa-star"></i>
                            </div>
                            <div class="product-price">
                                <span class="fs-5 text-danger fw-medium">{{ number_format($hightrate_1->price) }} đ</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6">
                <div class="rounded-4 bg-info-subtle p-4">
                    <div class="row">
                        <div class="col-6">
                            <div class="image">
                                 @if($hightrate_2)
                                <img src="{{asset(Storage::url($hightrate_2->image_path))}}" alt="" class="img-fluid rounded-3">
                                @endif
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="category">
                                <span class="fw-medium text-gray">{{$hightrate_2->category->name}}</span>
                            </div>
                            <a class="text-decoration-none text-dark" href="{{route('product', $hightrate_2->id)}}">
                                <h3 class="product-name two-line-truncate">{{ $hightrate_2->name }}</h3>
                            </a>
                            <div class="description ">
                                <span class="fs-6 four-line-truncate">{{$hightrate_2->description}}</span>
                            </div>

                            <div class="rank d-flex my-2 fs-6">
                                <i class="fa-regular fa-star"></i>
                                <i class="fa-regular fa-star"></i>
                                <i class="fa-regular fa-star"></i>
                                <i class="fa-regular fa-star"></i>
                                <i class="fa-regular fa-star"></i>
                            </div>
                            <div class="product-price">
                                <span class="fs-5 text-danger fw-medium">{{ number_format($hightrate_2->price) }} đ</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection
@section('js')
<script>
  function addToCart(event){
    event.preventDefault();
     let urlCart  = $(this).data('url');
     $.ajax({
            type: "GET",
            url: urlCart,
            dataType: 'json',
            success: function(data){
                if(data.code === 200 ){
                    alert('Thêm sản phẩm thành công');
                } 
            },
            error: function(data){

            },
     });
  }

  $(function(){
    $('.add_to_cart').on('click',addToCart);
  });
</script>
@endsection
  

 


