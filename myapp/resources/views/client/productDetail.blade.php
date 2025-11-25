@extends('client.layouts.ecommerce')

@section('title', $productVariant->product->name . ' ' . $productVariant->brand . ' - Chi tiết')

@section('content')
    <div class="container-fluid py-4">
        <div class="row g-5">

            <!-- Hình ảnh -->
            <div class="col-lg-5">
                <div class="product-gallery">
                    <div class="main-image mb-3">
                        <img src="{{ $productVariant->image ? asset('storage/products/' . $productVariant->image) : asset('images/no-image.jpg') }}"
                            class="img-fluid rounded-3 shadow-sm w-100"
                            alt="{{ $productVariant->product->name }} {{ $productVariant->brand }}"
                            style="height: 500px; object-fit: cover;">
                    </div>
                </div>
            </div>

            <!-- Thông tin -->
            <div class="col-lg-7">
                <div class="product-info">

                    <!-- Breadcrumb -->
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb small">
                            <li class="breadcrumb-item"><a href="{{ route('client.home') }}">Trang chủ</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('client.products') }}">Sản phẩm</a></li>
                            <li class="breadcrumb-item active">
                                {{ $productVariant->product->category->name ?? 'Chưa phân loại' }}
                            </li>
                        </ol>
                    </nav>

                    <!-- Tên sản phẩm -->
                    <h1 class="display-6 fw-bold mb-2">
                        {{ $productVariant->product->name }} {{ $productVariant->brand }}
                    </h1>

                    <!-- Thuộc tính -->
                    <p class="text-primary fw-semibold mb-3">
                        {{ $productVariant->attribute }}
                    </p>

                    <!-- Giá -->
                    <div class="price-section mb-4">
                        <span class="text-dark fs-3 fw-bold">
                            {{ number_format($productVariant->price) }}₫
                        </span>
                    </div>

                    <!-- Mô tả ngắn (từ ProductVariant) -->
                    @if($productVariant->description)
                        <div class="alert alert-light border mb-4">
                            <small class="text-muted">{!! nl2br(e($productVariant->description)) !!}</small>
                        </div>
                    @endif

                    <!-- Tồn kho -->
                    <div class="d-flex align-items-center mb-3">
                        <span class="text-success small">
                            <i class="bi bi-check-circle-fill"></i>
                            Còn {{ $productVariant->quantity }} sản phẩm
                        </span>
                    </div>

                    <!-- Số lượng -->
                    <div class="d-flex align-items-center mb-4 gap-3">
                        <label class="fw-bold">Số lượng:</label>
                        <div class="input-group" style="width: 140px;">
                            <button class="btn btn-outline-secondary btn-sm" type="button" id="qty-minus">-</button>
                            <input type="text" class="form-control text-center" id="quantity" value="1" readonly>
                            <button class="btn btn-outline-secondary btn-sm" type="button" id="qty-plus">+</button>
                        </div>
                    </div>

                    <!-- Nút hành động -->
                    <div class="d-flex gap-3 mb-4">
                        <button type="button"
                            class="btn btn-primary w-100 py-3 fw-bold d-flex align-items-center justify-content-center gap-2"
                            onclick="addToCart({{ $productVariant->id }}, parseInt(document.getElementById('quantity').value))">
                            <i class="bi bi-cart-plus"></i>
                            Thêm vào giỏ hàng
                        </button>
                    </div>
                </div>
            </div>


            @php
                $otherVariants = $productVariant->product->productVariants->where('id', '!=', $productVariant->id);
            @endphp
            @if($otherVariants->count() > 0)
                <div class="mt-5">
                    <h5 class="fw-bold mb-4 text-start">Các lựa chọn khác:</h5>
                    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4 col-lg-9">
                        @foreach($otherVariants as $variant)
                            <div class="col">
                                <div class="product-item">
                                    {{-- hình ảnh --}}
                                    <figure>
                                        <a href="{{ route('client.productDetail', ['id' => $variant->id]) }}" title="Product Title">
                                            @if($variant->image)
                                                <img src="{{ asset('storage/products/' . $variant->image) }}" class="tab-image">
                                            @else
                                                <img src="{{ asset('images/no-image.jpg')}}" class="tab-image">
                                            @endif
                                        </a>
                                    </figure>
                                    {{-- tên sp --}}
                                    <h3 class="card-title mb-1">
                                        <a href="{{ route('client.productDetail', ['id' => $variant->id]) }}"
                                            class="product-title text-dark text-decoration-none">
                                            {{ $variant->product->name }} {{ $variant->brand }}
                                        </a>
                                    </h3>
                                    <span class="qty">{{ $variant->attribute }}</span>
                                    {{-- giá --}}
                                    <span class="price">{{ number_format($variant->price) }}đ</span>
                                    {{-- số lượng và thêm vào giỏ hàng --}}
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="input-group product-qty">
                                            <span class="input-group-btn">
                                                <button type="button" class="quantity-left-minus btn btn-danger btn-number"
                                                    data-type="minus">
                                                    <svg width="16" height="16">
                                                        <use xlink:href="#minus"></use>
                                                    </svg>
                                                </button>
                                            </span>
                                            <input type="text" id="quantity" name="quantity" class="form-control input-number"
                                                value="1">
                                            <span class="input-group-btn">
                                                <button type="button" class="quantity-right-plus btn btn-success btn-number"
                                                    data-type="plus">
                                                    <svg width="16" height="16">
                                                        <use xlink:href="#plus"></use>
                                                    </svg>
                                                </button>
                                            </span>
                                        </div>
                                        <a href="#" class="nav-link">Add to Cart <iconify-icon icon="uil:shopping-cart"></a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <!-- Mô tả chi tiết -->
        @if($productVariant->product->description)
            <div class="row mt-5">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">Mô tả chi tiết</h5>
                            <div class="text-muted">
                                {!! nl2br(e($productVariant->product->description)) !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    {{-- JS: Điều khiển số lượng --}}
    <script>


        // Định dạng giá
        function formatPrice(price) {
            return new Intl.NumberFormat('vi-VN').format(price);
        }


        // Khởi tạo khi load trang
        document.addEventListener('DOMContentLoaded', function () {
            const qtyInput = document.getElementById('quantity');
            const cartQty = document.getElementById('cart-quantity');
            const minusBtn = document.getElementById('qty-minus');
            const plusBtn = document.getElementById('qty-plus');
            const maxQty = {{ $productVariant->quantity }};

            minusBtn.addEventListener('click', () => {
                let val = parseInt(qtyInput.value);
                if (val > 1) {
                    qtyInput.value = val - 1;
                    cartQty.value = val - 1;
                }
            });

            plusBtn.addEventListener('click', () => {
                let val = parseInt(qtyInput.value);
                if (val < maxQty) {
                    qtyInput.value = val + 1;
                    cartQty.value = val + 1;
                }
            });

            // Cập nhật giỏ hàng khi load (chỉ badge)
            updateCartData();
        });

        // Lấy dữ liệu giỏ hàng (chỉ cập nhật badge)
        function updateCartData() {
            fetch('/api/cart/data', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        updateCartBadge(data.cart_count);
                    }
                })
                .catch(error => console.error('Error:', error));
        }
    </script>
@endsection