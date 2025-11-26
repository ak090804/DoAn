@extends('client.layouts.ecommerce')

@section('title', 'Trang chủ')

@section('content')

  <section class="py-3"
    style="background-image: url('images/background-pattern.jpg');background-repeat: no-repeat;background-size: cover;">
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">

          <!-- Banner Blocks -->
          <div class="banner-blocks">
            <div class="banner-ad large bg-info block-1">
              <div class="swiper main-swiper">
                <div class="swiper-wrapper">

                  <div class="swiper-slide">
                    <div class="row banner-content p-5">
                      <div class="content-wrapper col-md-7">
                        <div class="categories my-3">100% natural</div>
                        <h3 class="display-4">Fresh Smoothie & Summer Juice</h3>
                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Dignissim massa diam elementum.</p>
                        <a href="#" class="btn btn-outline-dark btn-lg text-uppercase fs-6 rounded-1 px-4 py-3 mt-3">Shop
                          Now</a>
                      </div>
                      <div class="img-wrapper col-md-5">
                        <img src="images/product-thumb-1.png" class="img-fluid">
                      </div>
                    </div>
                  </div>

                  <div class="swiper-slide">
                    <div class="row banner-content p-5">
                      <div class="content-wrapper col-md-7">
                        <div class="categories mb-3 pb-3">100% natural</div>
                        <h3 class="banner-title">Fresh Smoothie & Summer Juice</h3>
                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Dignissim massa diam elementum.</p>
                        <a href="#" class="btn btn-outline-dark btn-lg text-uppercase fs-6 rounded-1">Shop Collection</a>
                      </div>
                      <div class="img-wrapper col-md-5">
                        <img src="images/product-thumb-1.png" class="img-fluid">
                      </div>
                    </div>
                  </div>

                  <div class="swiper-slide">
                    <div class="row banner-content p-5">
                      <div class="content-wrapper col-md-7">
                        <div class="categories mb-3 pb-3">100% natural</div>
                        <h3 class="banner-title">Heinz Tomato Ketchup</h3>
                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Dignissim massa diam elementum.</p>
                        <a href="#" class="btn btn-outline-dark btn-lg text-uppercase fs-6 rounded-1">Shop Collection</a>
                      </div>
                      <div class="img-wrapper col-md-5">
                        <img src="images/product-thumb-2.png" class="img-fluid">
                      </div>
                    </div>
                  </div>

                </div>

                <div class="swiper-pagination"></div>

              </div>
            </div>

            <div class="banner-ad bg-success-subtle block-2"
              style="background:url('images/ad-image-1.png') no-repeat;background-position: right bottom">
              <div class="row banner-content p-5">

                <div class="content-wrapper col-md-7">
                  <div class="categories sale mb-3 pb-3">20% off</div>
                  <h3 class="banner-title">Fruits & Vegetables</h3>
                  <a href="#" class="d-flex align-items-center nav-link">Shop Collection <svg width="24" height="24">
                      <use xlink:href="#arrow-right"></use>
                    </svg></a>
                </div>

              </div>
            </div>

            <div class="banner-ad bg-danger block-3"
              style="background:url('images/ad-image-2.png') no-repeat;background-position: right bottom">
              <div class="row banner-content p-5">

                <div class="content-wrapper col-md-7">
                  <div class="categories sale mb-3 pb-3">15% off</div>
                  <h3 class="item-title">Baked Products</h3>
                  <a href="#" class="d-flex align-items-center nav-link">Shop Collection <svg width="24" height="24">
                      <use xlink:href="#arrow-right"></use>
                    </svg></a>
                </div>

              </div>
            </div>

          </div>
          <!-- / Banner Blocks -->

        </div>
      </div>
    </div>
  </section>

  <!-- Sản phẩm mới nhất -->
  <section class="py-5">
    <div class="container-fluid">
      <div class="tabs-header d-flex justify-content-between border-bottom my-5">
        <h3>Sản phẩm mới nhất</h3>
        <span>
          <a href="{{ route('client.products') }}">Xem tất cả sản phẩm</a>
        </span>
      </div>

      {{-- sp 5 sp mới nhất--}}
      <div class="swiper brand-carousel">
        <div class="swiper-wrapper">
          @foreach ($newestProducts as $newestProduct)
            <div class="swiper-slide">
              <div class="card mb-3 p-3 rounded-4 shadow border-0">
                <div class="row g-0">
                  {{-- Ảnh sản phẩm --}}
                  <div class="col-md-4">
                    <a href="{{ route('client.productDetail', ['id' => $newestProduct->id]) }}">
                      @if($newestProduct->image)
                        <img src="{{ asset('storage/products/' . $newestProduct->image) }}" class="img-fluid rounded">
                      @else
                        <img src="images/no-image.jpg" class="img-fluid rounded">
                      @endif
                    </a>
                  </div>

                  {{-- Thông tin sản phẩm --}}
                  <div class="col-md-8">
                    <div class="card-body py-0">
                      <p class="text-muted mb-0">
                        <a href="{{ route('client.productDetail', ['id' => $newestProduct->id]) }}">
                          {{ $newestProduct->product->name }} {{ $newestProduct->brand }}
                        </a>
                      </p>
                      <h5>
                        {{ $newestProduct->description }}
                      </h5>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          @endforeach
        </div>
      </div>
    </div>
  </section>

  <!-- Sản phẩm Gợi ý -->
  <section class="py-5">
    <div class="container-fluid">
      <div class="tabs-header d-flex justify-content-between border-bottom my-5">
        <h3>
          {{ (isset($recommendedProducts) && $recommendedProducts->isNotEmpty()) ? 'Sản phẩm gợi ý cho bạn' : 'Sản phẩm Hot' }}
        </h3>
        <nav>
          <div class="nav nav-tabs">
            <a href="#" class="nav-link active" data-bs-toggle="tab" data-bs-target="#nav-all">All</a>
            <a href="#" class="nav-link" data-bs-toggle="tab" data-bs-target="#nav-fruits">Fruits & Veges</a>
            <a href="#" class="nav-link" data-bs-toggle="tab" data-bs-target="#nav-juices">Juices</a>
          </div>
        </nav>
      </div>

      <div class="tab-content">
        <div class="tab-pane fade show active" id="nav-all">
          <div class="products-carousel swiper">
            <div class="swiper-wrapper">

              @if(isset($recommendedProducts) && $recommendedProducts->isNotEmpty())
                @foreach($recommendedProducts as $variant)
                  <div class="product-item swiper-slide">
                    <a href="#" class="btn-wishlist"><svg width="24" height="24">
                        <use xlink:href="#heart"></use>
                      </svg></a>
                    <figure>
                      <a href="{{ route('client.productDetail', ['id' => $variant->id]) }}" title="Product Title">
                        @if($variant->image)
                          <img src="{{ asset('storage/products/' . $variant->image) }}" class="tab-image">
                        @else
                          <img src="images/no-image.jpg" class="tab-image">
                        @endif
                      </a>
                    </figure>
                    <h3>{{ $variant->product->name ?? 'Product' }} {{ $variant->brand ?? '' }}</h3>
                    <span class="qty">{{ $variant->quantity ?? '1' }} Unit</span>
                    <span class="price">{{ isset($variant->price) ? number_format($variant->price) . 'đ' : '' }}</span>
                    <div class="d-flex align-items-center justify-content-between">
                      <div class="input-group product-qty">
                        <span class="input-group-btn">
                          <button type="button" class="quantity-left-minus btn btn-danger btn-number" data-type="minus"><svg
                              width="16" height="16">
                              <use xlink:href="#minus"></use>
                            </svg></button>
                        </span>
                        <input type="text" id="quantity" name="quantity" class="form-control input-number" value="1">
                        <span class="input-group-btn">
                          <button type="button" class="quantity-right-plus btn btn-success btn-number" data-type="plus"><svg
                              width="16" height="16">
                              <use xlink:href="#plus"></use>
                            </svg></button>
                        </span>
                      </div>
                      <a href="#" class="nav-link"
                        onclick="event.preventDefault(); var qtyInput=this.closest('.product-item').querySelector('input[name=quantity]'); var qty=(qtyInput?qtyInput.value:1); addToCart({{ $variant->id }}, qty); return false;">Add
                        to Cart <iconify-icon icon="uil:shopping-cart"></iconify-icon></a>
                    </div>
                  </div>
                @endforeach
              @else
                @foreach ($topProducts as $topProduct)
                  @if ($topProduct->order_items_sum_quantity == 0) @continue @endif
                  <div class="product-item swiper-slide">
                    @if(isset($giftPromotions[$topProduct->id]))
                      <span class="badge bg-success position-absolute m-3" style="top: 0; right: 0;">Mua 1 Tăng 1</span>
                    @endif
                    <figure>
                      <a href="{{ route('client.productDetail', ['id' => $topProduct->id]) }}" title="Product Title">
                        @if($topProduct->image)
                          <img src="{{ asset('storage/products/' . $topProduct->image) }}" class="tab-image">
                        @else
                          <img src="images/no-image.jpg" class="tab-image">
                        @endif
                      </a>
                    </figure>
                    <h3>
                      <a href="{{ route('client.productDetail', ['id' => $topProduct->id]) }}"
                        class="product-title text-dark text-decoration-none">{{ $topProduct->product->name }}
                        {{ $topProduct->brand }}</a>
                    </h3>
                    <span class="qty">Đã bán: {{ $topProduct->order_items_sum_quantity }}</span>
                    <span class="price">{{ number_format($topProduct->price) }}đ</span>
                    <div class="d-flex align-items-center justify-content-between">
                      <div class="input-group product-qty">
                        <span class="input-group-btn">
                          <button type="button" class="quantity-left-minus btn btn-danger btn-number" data-type="minus"><svg
                              width="16" height="16">
                              <use xlink:href="#minus"></use>
                            </svg></button>
                        </span>
                        <input type="text" id="quantity" name="quantity" class="form-control input-number" value="1">
                        <span class="input-group-btn">
                          <button type="button" class="quantity-right-plus btn btn-success btn-number" data-type="plus"><svg
                              width="16" height="16">
                              <use xlink:href="#plus"></use>
                            </svg></button>
                        </span>
                      </div>
                      <a href="#" class="nav-link"
                        onclick="event.preventDefault(); var qtyInput=this.closest('.product-item').querySelector('input[name=quantity]'); var qty=(qtyInput?qtyInput.value:1); addToCart({{ $topProduct->id }}, qty); return false;">Add
                        to Cart <iconify-icon icon="uil:shopping-cart"></iconify-icon></a>
                    </div>
                  </div>
                @endforeach
              @endif

            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Quảng cáo đôi -->
  <section class="py-5">
    <div class="container-fluid row">
      <div class="col-md-6 mb-3">
        <div class="banner-ad bg-danger p-5" style="background: url('images/ad-image-3.png') right bottom no-repeat;">
          <div class="text-primary fs-3 fw-bold">Upto 25% Off</div>
          <h3>Luxa Dark Chocolate</h3>
          <p>Very tasty & creamy vanilla flavour creamy muffins.</p>
          <a href="#" class="btn btn-dark">Show Now</a>
        </div>
      </div>
      <div class="col-md-6">
        <div class="banner-ad bg-info p-5" style="background: url('images/ad-image-4.png') right bottom no-repeat;">
          <div class="text-primary fs-3 fw-bold">Upto 25% Off</div>
          <h3>Creamy Muffins</h3>
          <p>Very tasty & creamy vanilla flavour creamy muffins.</p>
          <a href="#" class="btn btn-dark">Show Now</a>
        </div>
      </div>
    </div>
  </section>

  <!-- Sản phẩm bán chạy -->
  <section class="py-5">
    <div class="container-fluid">
      <h2 class="my-5">Sản phẩm bán chạy</h2>

      <div class="row">
        <div class="col-md-12">
          <div class="products-carousel swiper">
            <div class="swiper-wrapper">
              @foreach ($topProducts as $topProduct)
                @if ($topProduct->order_items_sum_quantity == 0) @continue @endif
                <div class="product-item swiper-slide">
                  {{-- badge: Mua 1 tặng 1 --}}
                  @if(isset($giftPromotions[$topProduct->id]))
                    <span class="badge bg-success position-absolute m-3" style="top: 0; right: 0;">Mua 1 Tặng 1</span>
                  @endif
                  {{-- hình ảnh --}}
                  <figure>
                    <a href="{{ route('client.productDetail', ['id' => $topProduct->id]) }}" title="Product Title">
                      @if($topProduct->image)
                        <img src="{{ asset('storage/products/' . $topProduct->image) }}" class="tab-image">
                      @else
                        <img src="images/no-image.jpg" class="tab-image">
                      @endif
                    </a>
                  </figure>
                  {{-- tên sp --}}
                  <h3>
                    <a href="{{ route('client.productDetail', ['id' => $topProduct->id]) }}"
                      class="product-title text-dark text-decoration-none">{{ $topProduct->product->name }}
                      {{ $topProduct->brand }}</a>
                  </h3>
                  {{-- số lượng đã bán --}}
                  <span class="qty">Đã bán: {{ $topProduct->order_items_sum_quantity }}</span>
                  {{-- giá --}}
                  <span class="price">{{ number_format($topProduct->price) }}đ</span>
                  {{-- nút tăng giảm sl --}}
                  <div class="d-flex align-items-center justify-content-between">
                    <div class="input-group product-qty">
                      <span class="input-group-btn">
                        <button type="button" class="quantity-left-minus btn btn-danger btn-number" data-type="minus">
                          <svg width="16" height="16">
                            <use xlink:href="#minus"></use>
                          </svg>
                        </button>
                      </span>
                      <input type="text" id="quantity" name="quantity" class="form-control input-number" value="1">
                      <span class="input-group-btn">
                        <button type="button" class="quantity-right-plus btn btn-success btn-number" data-type="plus">
                          <svg width="16" height="16">
                            <use xlink:href="#plus"></use>
                          </svg>
                        </button>
                      </span>
                    </div>
                    {{-- nút thêm vào giỏ --}}
                    <a href="#" class="nav-link"
                      onclick="event.preventDefault(); var qtyInput=this.closest('.product-item').querySelector('input[name=quantity]'); var qty=(qtyInput?qtyInput.value:1); addToCart({{ $topProduct->id }}, qty); return false;">
                      @if(isset($giftPromotions[$topProduct->id]))
                        Mua 1 Tặng 1 <iconify-icon icon="uil:shopping-cart"></iconify-icon>
                      @else
                        Add to Cart <iconify-icon icon="uil:shopping-cart"></iconify-icon>
                      @endif
                    </a>
                  </div>
                </div>
              @endforeach
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- App Promotion -->
  <section class="py-5 my-5">
    <div class="container-fluid">
      <div class="bg-warning py-5 rounded-5" style="background: url('images/bg-pattern-2.png') no-repeat;">
        <div class="container row">
          <div class="col-md-4"><img src="images/phone.png" class="img-fluid"></div>
          <div class="col-md-8">
            <h2 class="my-5">Shop faster with foodmart App</h2>
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sagittis sed ptibus liberolectus nonet psryroin.
            </p>
            <div class="d-flex gap-2"><img src="images/app-store.jpg"><img src="images/google-play.jpg"></div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Tìm kiếm phổ biến -->
  <section class="py-5">
    <div class="container-fluid">
      <h2 class="my-5">Mọi người cũng tìm kiếm</h2>
      <a href="#" class="btn btn-warning me-2 mb-2">Blue diamon almonds</a>
      <a href="#" class="btn btn-warning me-2 mb-2">Angie’s Boomchickapop Corn</a>
      <a href="#" class="btn btn-warning me-2 mb-2">Salty kettle Corn</a>
      <a href="#" class="btn btn-warning me-2 mb-2">Chobani Greek Yogurt</a>
      <a href="#" class="btn btn-warning me-2 mb-2">Sweet Vanilla Yogurt</a>
    </div>
  </section>

  <!-- Dịch vụ -->
  <section class="py-5">
    <div class="container-fluid row row-cols-1 row-cols-sm-3 row-cols-lg-5 g-4">
      <div class="col">
        <div class="card border-0">
          <div class="row">
            <div class="col-md-2"></div>
            <div class="col-md-10">
              <h5>Free delivery</h5>
              <p>Lorem ipsum dolor sit amet.</p>
            </div>
          </div>
        </div>
      </div>
      <div class="col">
        <div class="card border-0">
          <div class="row">
            <div class="col-md-2"></div>
            <div class="col-md-10">
              <h5>100% secure payment</h5>
              <p>Lorem ipsum dolor sit amet.</p>
            </div>
          </div>
        </div>
      </div>
      <div class="col">
        <div class="card border-0">
          <div class="row">
            <div class="col-md-2"></div>
            <div class="col-md-10">
              <h5>Quality guarantee</h5>
              <p>Lorem ipsum dolor sit amet.</p>
            </div>
          </div>
        </div>
      </div>
      <div class="col">
        <div class="card border-0">
          <div class="row">
            <div class="col-md-2"></div>
            <div class="col-md-10">
              <h5>Guaranteed savings</h5>
              <p>Lorem ipsum dolor sit amet.</p>
            </div>
          </div>
        </div>
      </div>
      <div class="col">
        <div class="card border-0">
          <div class="row">
            <div class="col-md-2"></div>
            <div class="col-md-10">
              <h5>Daily offers</h5>
              <p>Lorem ipsum dolor sit amet.</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css">
  <script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>

@endsection