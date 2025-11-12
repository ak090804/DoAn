@extends('client.layouts.ecommerce')

@section('content')
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-8">
                <h2 class="mb-4">Giỏ hàng của bạn</h2>

                @if(count($cart) > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Hình ảnh</th>
                                    <th>Tên sản phẩm</th>
                                    <th>Giá</th>
                                    <th>Số lượng</th>
                                    <th>Thành tiền</th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($cart as $item)
                                    <tr id="cart-item-{{ $item['id'] }}">
                                        <td>
                                            @if($item['image'])
                                                <img src="{{ asset($item['image']) }}" alt="{{ $item['name'] }}"
                                                    style="max-width: 80px; height: auto;">
                                            @else
                                                <span class="text-muted">Không có ảnh</span>
                                            @endif
                                        </td>
                                        <td>{{ $item['name'] }}</td>
                                        <td class="fw-bold text-end">{{ number_format($item['price'], 0, ',', '.') }}₫</td>
                                        <td>
                                            <div class="input-group" style="width: 100px;">
                                                <button class="btn btn-sm btn-outline-secondary" type="button"
                                                    onclick="updateQuantity({{ $item['id'] }}, {{ $item['quantity'] - 1 }})">-</button>
                                                <input type="number" class="form-control text-center quantity-input"
                                                    id="qty-{{ $item['id'] }}" value="{{ $item['quantity'] }}" min="1"
                                                    onchange="updateQuantity({{ $item['id'] }}, this.value)">
                                                <button class="btn btn-sm btn-outline-secondary" type="button"
                                                    onclick="updateQuantity({{ $item['id'] }}, {{ $item['quantity'] + 1 }})">+</button>
                                            </div>
                                        </td>
                                        <td class="fw-bold text-end">
                                            {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}₫
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-danger" onclick="removeFromCart({{ $item['id'] }})">
                                                <i class="fas fa-trash"></i> Xóa
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-info text-center">
                        <h5>Giỏ hàng của bạn đang trống</h5>
                        <a href="{{ route('client.products') }}" class="btn btn-primary mt-2">Tiếp tục mua sắm</a>
                    </div>
                @endif
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Tóm tắt đơn hàng</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-2">
                            <strong>Số lượng sản phẩm:</strong>
                            <span id="cart-count">{{ count($cart) }}</span>
                        </div>
                        <hr>
                        <div class="mb-3">
                            <strong class="fs-5">Tổng tiền:</strong>
                            <span class="fs-5 fw-bold text-primary"
                                id="cart-total">{{ number_format($total, 0, ',', '.') }}₫</span>
                        </div>
                        @if(count($cart) > 0)
                            <button class="btn btn-success btn-lg w-100 mb-2" onclick="goToCheckout()">
                                <i class="fas fa-credit-card"></i> Thanh toán
                            </button>
                            <button class="btn btn-warning btn-outline w-100" onclick="clearCart()">
                                <i class="fas fa-trash"></i> Xóa giỏ hàng
                            </button>
                        @endif
                        <a href="{{ route('client.products') }}" class="btn btn-outline-primary w-100 mt-2">
                            <i class="fas fa-shopping-bag"></i> Tiếp tục mua sắm
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Update quantity
        function updateQuantity(productId, quantity) {
            quantity = parseInt(quantity);
            if (quantity < 1) {
                removeFromCart(productId);
                return;
            }

            fetch('/api/cart/update', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    product_id: productId,
                    quantity: quantity
                })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload(); // Reload to update totals
                    } else {
                        alert('Cập nhật thất bại: ' + data.message);
                    }
                })
                .catch(error => console.error('Error:', error));
        }

        // Remove from cart
        function removeFromCart(productId) {
            if (!confirm('Bạn có chắc chắn muốn xóa sản phẩm này?')) {
                return;
            }

            fetch('/api/cart/remove', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    product_id: productId
                })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Xóa thất bại: ' + data.message);
                    }
                })
                .catch(error => console.error('Error:', error));
        }

        // Clear cart
        function clearCart() {
            if (!confirm('Bạn có chắc chắn muốn xóa toàn bộ giỏ hàng?')) {
                return;
            }

            fetch('/api/cart/clear', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Xóa thất bại: ' + data.message);
                    }
                })
                .catch(error => console.error('Error:', error));
        }

        // Go to checkout
        function goToCheckout() {
            // Kiểm tra đăng nhập
            fetch('/api/cart/check-login', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.isLoggedIn) {
                        // Đã đăng nhập, chuyển đến trang checkout
                        window.location.href = '/checkout';
                    } else {
                        // Chưa đăng nhập, chuyển đến trang login
                        window.location.href = '/login?redirect=/cart';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Có lỗi xảy ra. Vui lòng thử lại.');
                });
        }
    </script>
@endsection