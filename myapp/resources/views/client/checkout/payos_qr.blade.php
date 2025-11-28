@extends('layouts.app')

@section('content')
    <div class="container">
        <h3>Thanh toán bằng PayOS</h3>

        @if(!empty($payos['qr_image']))
            <p>Quét mã dưới đây để thanh toán:</p>
            <img src="data:image/png;base64,{{ $payos['qr_image'] }}" alt="QR PayOS" />
        @else
            <p>Quét mã dưới đây để thanh toán (dùng camera/ứng dụng ngân hàng):</p>
            <div id="qrcode"></div>
            <p id="qr_text" style="display:none">{{ $payos['qr_text'] ?? ($payos['checkout_url'] ?? '') }}</p>
        @endif

        <p>Hoặc <a href="{{ $payos['checkout_url'] ?? '#' }}" target="_blank">mở cổng thanh toán</a></p>

        <script src="https://cdn.jsdelivr.net/npm/qrcodejs2@0.0.2/qrcode.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                var qrTextEl = document.getElementById('qr_text');
                if (qrTextEl && qrTextEl.textContent.trim().length > 0) {
                    new QRCode(document.getElementById('qrcode'), {
                        text: qrTextEl.textContent.trim(),
                        width: 256,
                        height: 256,
                    });
                }
            });
        </script>
        <script>
            // Poll PayOS status and redirect when order is created by webhook
            (function () {
                var payos = @json($payos ?? []);
                var paymentId = payos.payment_request_id || payos.payment_link_id || payos.payment_link_alt || payos.paymentLinkId || payos.id || payos.payment_request || payos.saved_cart_id;
                if (!paymentId) return;

                var statusEl = document.createElement('p');
                statusEl.id = 'payos_status';
                statusEl.textContent = 'Đang chờ xác nhận thanh toán...';
                document.querySelector('.container').appendChild(statusEl);

                function checkStatus() {
                    fetch('/payos/status/' + encodeURIComponent(paymentId))
                        .then(function (r) { return r.json(); })
                        .then(function (data) {
                            if (data && data.payos && data.payos.status) {
                                var s = (data.payos.status || data.status || '').toString().toLowerCase();
                                if (s === 'paid' || s === 'success' || s === 'completed') {
                                    var orderId = data.payos.order_id || data.order_id || data.orderId || data.orderId;
                                    if (orderId) {
                                        // Redirect to homepage (order is saved and visible in history)
                                        window.location.href = '/';
                                    } else {
                                        // If webhook may still be processing, keep polling a few more times
                                        setTimeout(checkStatus, 2000);
                                    }
                                } else {
                                    // keep polling
                                    setTimeout(checkStatus, 3000);
                                }
                            } else {
                                // Not found or not ready yet
                                setTimeout(checkStatus, 3000);
                            }
                        })
                        .catch(function (err) {
                            console.error('Status check failed', err);
                            setTimeout(checkStatus, 5000);
                        });
                }

                // Start polling shortly after page load
                setTimeout(checkStatus, 1500);
            })();
        </script>
    </div>
@endsection