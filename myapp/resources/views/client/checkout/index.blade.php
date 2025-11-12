@extends('client.layouts.ecommerce')

@section('title', 'Xác nhận đơn hàng')

@section('content')
    <div class="container mt-5 mb-5">
        <div class="row g-5">
            <!-- Giỏ hàng -->
            <div class="col-lg-7">
                <h3 class="mb-4">Xác nhận giỏ hàng</h3>

                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>Tên sản phẩm</th>
                                <th class="text-end">Giá</th>
                                <th>Số lượng</th>
                                <th class="text-end">Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($cart as $item)
                                <tr>
                                    <td>{{ $item['name'] }}</td>
                                    <td class="text-end">{{ number_format($item['price'], 0, ',', '.') }}₫</td>
                                    <td>{{ $item['quantity'] }}</td>
                                    <td class="fw-bold text-end">
                                        {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}₫</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="alert alert-info">
                    <strong class="d-block text-end">Tổng tiền:</strong>
                    <span
                        class="fs-5 fw-bold text-primary d-block text-end">{{ number_format($total, 0, ',', '.') }}₫</span>
                </div>
            </div>

            <!-- Thông tin người dùng -->
            <div class="col-lg-5">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Thông tin giao hàng</h5>
                    </div>
                    <div class="card-body">
                        <form id="checkoutForm">
                            @csrf

                            <!-- Tên -->
                            <div class="mb-3">
                                <label for="name" class="form-label">Tên người nhận <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name"
                                    value="{{ $user->name ?? '' }}" required>
                            </div>

                            <!-- Email -->
                            <div class="mb-3">
                                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="email" name="email"
                                    value="{{ $user->email ?? '' }}" required>
                            </div>

                            <!-- Điện thoại -->
                            <div class="mb-3">
                                <label for="phone" class="form-label">Số điện thoại <span
                                        class="text-danger">*</span></label>
                                <input type="tel" class="form-control" id="phone" name="phone" placeholder="09xxxxxxxxx"
                                    required>
                            </div>

                            <!-- Địa chỉ -->
                            <div class="mb-3">
                                <label for="address" class="form-label">Địa chỉ giao hàng <span
                                        class="text-danger">*</span></label>
                                <textarea class="form-control" id="address" name="address" rows="3" required></textarea>
                            </div>

                            <!-- Ghi chú -->
                            <div class="mb-3">
                                <label for="note" class="form-label">Ghi chú (tuỳ chọn)</label>
                                <textarea class="form-control" id="note" name="note" rows="2"
                                    placeholder="Ghi chú về đơn hàng..."></textarea>
                            </div>

                            <hr>

                            <!-- Tổng tiền -->
                            <div class="mb-3">
                                <strong>Tổng cộng:</strong>
                                <div class="fs-4 fw-bold text-primary">
                                    {{ number_format($total, 0, ',', '.') }}₫
                                </div>
                            </div>

                            <!-- Nút hành động -->
                            <button type="submit" class="btn btn-success btn-lg w-100 mb-2">
                                <i class="fas fa-check"></i> Xác nhận đơn hàng
                            </button>
                            <a href="{{ route('client.cart') }}" class="btn btn-outline-secondary w-100">
                                <i class="fas fa-arrow-left"></i> Quay lại giỏ hàng
                            </a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('checkoutForm').addEventListener('submit', function (e) {
            e.preventDefault();

            const formData = new FormData(this);
            const data = Object.fromEntries(formData);

            fetch('{{ route("client.checkout.store") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(data)
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification('Đơn hàng được tạo thành công!', 'success');
                        setTimeout(() => {
                            window.location.href = data.redirect;
                        }, 1500);
                    } else {
                        showNotification('Lỗi: ' + data.message, 'danger');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('Có lỗi xảy ra. Vui lòng thử lại.', 'danger');
                });
        });

        function showNotification(message, type = 'info') {
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
            alertDiv.setAttribute('role', 'alert');
            alertDiv.style.position = 'fixed';
            alertDiv.style.top = '20px';
            alertDiv.style.right = '20px';
            alertDiv.style.zIndex = '9999';
            alertDiv.style.minWidth = '300px';
            alertDiv.innerHTML = `
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                `;

            const container = document.querySelector('body');
            container.insertBefore(alertDiv, container.firstChild);

            setTimeout(() => {
                alertDiv.remove();
            }, 3000);
        }
    </script>
@endsection