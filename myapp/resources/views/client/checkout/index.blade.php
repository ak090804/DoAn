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
                                        {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}₫
                                    </td>
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

                            <!-- Voucher / Mã giảm giá -->
                            <div class="mb-3">
                                <label class="form-label">Mã giảm giá (Voucher)</label>
                                <select name="voucher_code" id="voucher_code" class="form-select mb-2">
                                    <option value="">-- Chọn voucher (nếu có) --</option>
                                    @if(!empty($vouchers))
                                        @foreach($vouchers as $v)
                                            <option value="{{ $v->ma }}">{{ $v->ten }} - Giảm
                                                {{ number_format($v->gia_tri, 0, ',', '.') }}₫ @if($v->ma) ({{ $v->ma }}) @endif
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                                <div class="input-group mb-2">
                                    <input type="text" name="voucher_manual" id="voucher_manual" class="form-control"
                                        placeholder="Nhập mã voucher nếu có">
                                    <button type="button" id="applyVoucher" class="btn btn-outline-primary">Áp dụng</button>
                                </div>
                                <small class="text-muted">Lưu ý: Một voucher chỉ áp dụng cho giảm giá hóa đơn.</small>
                                <!-- Display discount info -->
                                <div id="discountInfo" class="mt-2" style="display:none;">
                                    <div class="alert alert-success p-2 mb-0">
                                        Voucher: <strong id="voucherNameDisplay"></strong>
                                        <br>Giảm: <strong id="discountAmountDisplay" class="text-success"></strong>
                                    </div>
                                </div>
                            </div>

                            <hr>

                            <!-- Tổng tiền display -->
                            <div class="mb-3">
                                <div class="mb-2">
                                    <strong>Tổng giá trị đơn hàng:</strong>
                                    <span id="subtotalDisplay"
                                        class="float-end">{{ number_format($total, 0, ',', '.') }}₫</span>
                                </div>
                                <div class="mb-2" id="discountLineDisplay" style="display:none;">
                                    <strong>Giảm giá:</strong>
                                    <span id="discountLineAmount" class="float-end text-danger">- 0₫</span>
                                </div>
                                <hr style="margin: 0.5rem 0;">
                                <div class="fs-5 fw-bold text-primary">
                                    <strong>Tổng cộng:</strong>
                                    <span id="finalTotalDisplay"
                                        class="float-end">{{ number_format($total, 0, ',', '.') }}₫</span>
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

        // Function to verify and apply voucher
        function verifyAndApplyVoucher(code) {
            if (!code) {
                document.getElementById('discountInfo').style.display = 'none';
                document.getElementById('discountLineDisplay').style.display = 'none';
                const subtotalText = document.getElementById('subtotalDisplay').textContent;
                document.getElementById('finalTotalDisplay').textContent = subtotalText;
                return;
            }

            // Get cart total from display
            const subtotalText = document.getElementById('subtotalDisplay').textContent;
            const subtotal = parseFloat(subtotalText.replace(/[^\d]/g, ''));

            // Call API to verify voucher
            fetch('{{ route("api.voucher.verify") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ code: code, cart_total: subtotal })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const discountAmount = data.discount_amount;
                        const finalTotal = subtotal - discountAmount;

                        // Show discount info
                        document.getElementById('voucherNameDisplay').textContent = data.voucher_name;
                        document.getElementById('discountAmountDisplay').textContent = new Intl.NumberFormat('vi-VN').format(discountAmount) + '₫';
                        document.getElementById('discountInfo').style.display = 'block';

                        // Update total display
                        document.getElementById('discountLineDisplay').style.display = 'block';
                        document.getElementById('discountLineAmount').textContent = '- ' + new Intl.NumberFormat('vi-VN').format(discountAmount) + '₫';
                        document.getElementById('finalTotalDisplay').textContent = new Intl.NumberFormat('vi-VN').format(finalTotal) + '₫';

                        // Set hidden input for form submission
                        if (!document.getElementById('selectedVoucherCode')) {
                            const hiddenInput = document.createElement('input');
                            hiddenInput.type = 'hidden';
                            hiddenInput.id = 'selectedVoucherCode';
                            hiddenInput.name = 'voucher_code';
                            hiddenInput.value = data.voucher_code;
                            document.getElementById('checkoutForm').appendChild(hiddenInput);
                        } else {
                            document.getElementById('selectedVoucherCode').value = data.voucher_code;
                        }

                        showNotification('Mã voucher hợp lệ! ✓ Giảm ' + new Intl.NumberFormat('vi-VN').format(discountAmount) + '₫', 'success');
                    } else {
                        document.getElementById('discountInfo').style.display = 'none';
                        document.getElementById('discountLineDisplay').style.display = 'none';
                        document.getElementById('finalTotalDisplay').textContent = subtotalText;
                        showNotification('Lỗi: ' + data.message, 'danger');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('Có lỗi xảy ra. Vui lòng thử lại.', 'danger');
                });
        }

        // Voucher dropdown change event
        document.getElementById('voucher_code')?.addEventListener('change', function () {
            const selectedCode = this.value;
            if (selectedCode) {
                verifyAndApplyVoucher(selectedCode);
            } else {
                verifyAndApplyVoucher('');
            }
        });

        // Manual voucher apply button
        document.getElementById('applyVoucher')?.addEventListener('click', function () {
            const manual = document.getElementById('voucher_manual').value.trim();
            if (!manual) {
                showNotification('Vui lòng nhập mã voucher.', 'warning');
                return;
            }
            verifyAndApplyVoucher(manual);
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