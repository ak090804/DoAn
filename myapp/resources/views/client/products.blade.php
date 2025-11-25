@extends('client.layouts.ecommerce')

@section('title', 'Danh sách sản phẩm')

@section('content')
    <div class="container-fluid py-4">
        <div class="row">

            <!-- Sidebar: Bộ lọc -->
            <div class="col-lg-3 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <h5 class="mb-4">Bộ lọc sản phẩm</h5>
                        <form method="GET" action="{{ route('client.products') }}" id="filter-form">

                            <!-- Danh mục -->
                            <div class="mb-3">
                                <label class="form-label">Danh mục</label>
                                <select name="category_id" class="form-select">
                                    <option value="">Tất cả danh mục</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ ($filters['category_id'] ?? '') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Khoảng giá -->
                            <div class="mb-3">
                                <label class="form-label">Khoảng giá</label>
                                <div class="d-flex gap-2 align-items-center">
                                    <input type="number" name="price_min" class="form-control" placeholder="Từ"
                                        value="{{ $filters['price_min'] ?? '' }}">
                                    <span>-</span>
                                    <input type="number" name="price_max" class="form-control" placeholder="Đến"
                                        value="{{ $filters['price_max'] ?? '' }}">
                                </div>
                            </div>

                            <!-- Sắp xếp -->
                            <div class="mb-4">
                                <label class="form-label">Sắp xếp</label>
                                <select name="sort" class="form-select">
                                    <option value="">Mặc định</option>
                                    <option value="price_asc" {{ $filters['sort'] == 'price_asc' ? 'selected' : '' }}>
                                        Giá: Thấp → Cao
                                    </option>
                                    <option value="price_desc" {{ $filters['sort'] == 'price_desc' ? 'selected' : '' }}>
                                        Giá: Cao → Thấp
                                    </option>
                                    {{-- <option value="newest" {{ $filters['sort']=='newest' ? 'selected' : '' }}>
                                        Mới nhất
                                    </option> --}}
                                </select>
                            </div>

                            <button type="submit" class="btn btn-primary w-100">Áp dụng</button>
                            <a href="{{ route('client.products') }}" class="btn btn-outline-secondary w-100 mt-2">Xóa
                                lọc</a>
                            <a href="{{ route('client.home') }}" class="btn btn-outline-secondary w-100 mt-2">Về trang
                                chủ</a>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Danh sách sản phẩm -->
            <div class="col-lg-9">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="mb-0">
                        Tất cả sản phẩm
                        <span class="text-muted fs-6">({{ $productVariants->total() }} kết quả)</span>
                    </h4>
                    <small class="text-muted">
                        Trang {{ $productVariants->currentPage() }} / {{ $productVariants->lastPage() }}
                    </small>
                </div>

                @if($productVariants->isEmpty())
                    <div class="text-center py-5">
                        <img src="{{ asset('images/empty-box.jpg') }}" alt="Không có sản phẩm" width="100">
                        <p class="text-muted mt-3">Không tìm thấy sản phẩm nào.</p>
                        <a href="{{ route('client.products') }}" class="btn btn-outline-primary">Xem tất cả</a>
                    </div>
                @else
                    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
                        @foreach($productVariants as $productVariant)
                            <div class="col">
                                <div class="product-item">
                                    {{-- <span class="badge bg-success position-absolute m-3">-30%</span> --}}
                                    {{-- hình ảnh --}}
                                    <figure>
                                        <a href="{{ route('client.productDetail', ['id' => $productVariant->id]) }}"
                                            title="Product Title">
                                            @if($productVariant->image)
                                                <img src="{{ asset('storage/products/' . $productVariant->image) }}" class="tab-image">
                                            @else
                                                <img src="images/no-image.jpg" class="tab-image">
                                            @endif
                                        </a>
                                    </figure>
                                    {{-- tên sp --}}
                                    <h3 class="card-title mb-1">
                                        <a href="{{ route('client.productDetail', ['id' => $productVariant->id]) }}"
                                            class="product-title text-dark text-decoration-none">
                                            {{ $productVariant->product->name }} {{ $productVariant->brand }}
                                        </a>
                                    </h3>
                                    <span class="qty">{{ $productVariant->attribute }}</span>
                                    {{-- giá --}}
                                    <span class="price">{{ number_format($productVariant->price) }}đ</span>
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
                                        <a href="#" class="nav-link"
                                            onclick="event.preventDefault(); var qtyInput=this.closest('.product-item').querySelector('input[name=quantity]'); var qty=(qtyInput?qtyInput.value:1); addToCart({{ $productVariant->id }}, qty); return false;">Add
                                            to Cart <iconify-icon icon="uil:shopping-cart"></a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Phân trang -->
                    <div class="mt-5">
                        {{ $productVariants->appends(request()->query())->links('pagination::bootstrap-5') }}
                    </div>
                @endif
            </div>
        </div>
    </div>

@endsection