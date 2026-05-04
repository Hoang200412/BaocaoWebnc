@extends('project_1.customer.layouts.layout')

@section('content')
    <main>
        <section>
            <div class="bg-light-pink p-3 d-flex justify-content-center">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                      <li class="breadcrumb-item"><a href="#" class="text-decoration-none text-gray">Trang chủ</a></li>
                      <li class="breadcrumb-item"><a href="#" class="text-decoration-none text-gray">{{$product->category->name}}</a></li>
                      <li class="breadcrumb-item"><a href="#" class="text-decoration-none text-gray">{{$product->name}}</a></li>
                    </ol>
                </nav>
            </div>
        </section>
        <div class="container py-5">
            <div class="row">
                <div class="col-md-6">
                    <div class="row">
                        <div class="col-2 pe-0">
                            <div class="bg-dark text-white text-center py-2" role="button" onclick="scrollUp()">
                                <i class="bi bi-chevron-up fs-4"></i>
                              </div>
                              <!-- Thanh cuộn ảnh -->
                              <div id="imageList">
                                @foreach ($product->galleries as $gallery)
                                    <img src="{{asset(Storage::url($gallery->image_path))}}" alt="" class="img-fluid my-1">
                                @endforeach
                                <!-- Thêm nhiều ảnh -->
                              </div>
                        </div>
                        <div class="col-10">
                            <div class="image">
                                <img src="{{asset(Storage::url($product->image_path))}}" alt="" class="img-fluid">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 px-5">
                    <div class="name_product">
                        <span class="fs-1 fw-bold">{{$product->name}}</span>
                    </div>
                    <div class="share d-flex align-items-center">
                        <span class="fs-5 text-gray">share</span>
                        <ul class="list-unstyled d-flex mb-0">
                            <li class="facebook icon-gray ms-3"><a href="#" class="text-decoration-none" title="Share" target="_blank">
                                <i class="fa-brands fa-facebook "></i>
                            </a></li>
                            <li class="twitter icon-gray ms-3"><a href="#" class="text-decoration-none" title="Tweet" target="_blank">
                                <i class="fa-brands fa-twitter text-info"></i>
                            </a></li>
                            <li class="pinterest icon-gray ms-3"><a href="#" class="text-decoration-none" title="Pinterest" target="_blank">
                                <i class="fa-brands fa-pinterest text-danger"></i>
                            </a></li>
                        </ul>
                    </div>

                    <div class="tags">
                        @foreach ($product->tags as $tag)
                            <span class="fs-6 fw-medium text-light badge bg-warning my-3">{{$tag->name}}</span>
                        @endforeach
                    </div>

                    <div class="price my-3">
                        <span class="fs-4 fw-medium text-danger">{{number_format($product->price)}}</span>
                        <span class="fs-4 fw-medium text-danger">đ</span>
                    </div>
                    
                    <div class="action my-3">
                        <form action="{{route('checkout')}}" method="get" >
                            @csrf
                            <input type="hidden" name="product_id" value="{{$product->id}}">
                            <div class="d-flex">
                                <label for="quantity" class="form-label me-3">Số lượng</label>
                                <input class="quantity" type="number" name="quantity" id="" value="1" min="1" max="{{ $product->quantity }}"> 
                            </div>
                            <div class="mt-2">
                                <span class="text-gray">Số lượng trong kho: </span>
                                <span class="fw-medium">{{ $product->quantity }}</span>
                            </div>

                            <div class="option mt-3">
                                <button type="submit" class="btn btn-dark pay" @disabled($product->quantity <= 0)>Mua luôn</button>
                                <a href="#" class="btn border border-2 border-black add_to_cart {{ $product->quantity <= 0 ? 'disabled' : '' }}" id="add_to_cart" data-url = "{{route('addToCart',['id'=>$product->id])}}" @if($product->quantity <= 0) aria-disabled="true" tabindex="-1" @endif>Thêm vào giỏ hàng</a>
                            </div>
                   
                        </form>
                    </div>
                </div>
            </div>


            <div class="accordion mt-5" id="productAccordion">
                <!-- DESCRIPTION -->
                <div class="accordion-item">
                  <h2 class="accordion-header">
                    <button class="accordion-button d-flex justify-content-between align-items-center" type="button" data-bs-toggle="collapse" data-bs-target="#descCollapse" aria-expanded="true">
                      <span class="fw-bold text-danger">DESCRIPTION</span>
                      <span class="ms-auto icon"></span>
                    </button>
                  </h2>
                  <div id="descCollapse" class="accordion-collapse collapse show">
                    <div class="accordion-body">
                       <span class="fw-medium text-gray ">{{ $product->description }}</span>
                    </div>
                  </div>
                </div>
              
                <!-- PRODUCT DETAILS -->
                <div class="accordion-item">
                  <h2 class="accordion-header">
                    <button class="accordion-button collapsed d-flex justify-content-between align-items-center" type="button" data-bs-toggle="collapse" data-bs-target="#detailsCollapse" aria-expanded="false">
                      <span class="fw-bold">PRODUCT DETAILS</span>
                      <span class="ms-auto icon"></span>
                    </button>
                  </h2>
                  <div id="detailsCollapse" class="accordion-collapse collapse">
                    <div class="accordion-body">
                      These are the product details...
                    </div>
                  </div>
                </div>


                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed d-flex justify-content-between align-items-center" type="button" data-bs-toggle="collapse" data-bs-target="#detailsCollapse" aria-expanded="false">
                            <span class="fw-bold">REVIEWS</span>
                            <span class="ms-auto icon"></span>
                        </button>
                    </h2>
                    <div id="detailsCollapse" class="accordion-collapse collapse">
                        <div class="accordion-body">
                            These are the product review...
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
            error: function(xhr){
                const message = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Không thể thêm sản phẩm vào giỏ hàng';
                alert(message);
            },
     });
  }

  $(function(){
    $('#add_to_cart').on('click',addToCart);
  });
</script>
@endsection