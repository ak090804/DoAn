@extends('client.layouts.ecommerce')

@section('title', 'Đang xử lý thanh toán')

@section('content')
    <div class="container mt-5">
        <div class="card p-4 text-center">
            <h4>Đang xử lý thanh toán</h4>
            <p>Hệ thống đang chờ PayOS xác nhận thanh toán. Bạn sẽ được chuyển đến trang đơn sau khi thanh toán thành công.
            </p>
            <div id="status" class="mb-3">Trạng thái: Đang chờ...</div>
            <div id="spinner">
                <div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>
            </div>
            <div class="mt-3"><a href="/" class="btn btn-outline-secondary">Quay lại trang chủ</a></div>
        </div>
    </div>

    <script>
        const paymentId = @json($paymentId);
        const savedCartId = @json($savedCartId);

        function checkStatus() {
            if (!paymentId) return;
            fetch('/payos/status/' + encodeURIComponent(paymentId))
                .then(res => res.json())
                .then(json => {
                    if (json.status && (json.status === 'paid' || json.status === 'success' || json.status === 'completed')) {
                        document.getElementById('status').innerText = 'Thanh toán thành công. Chuyển hướng...';
                        // If webhook already created order, json.order_id will be set
                        if (json.order_id) {
                            setTimeout(() => {
                                window.location.href = '/checkout/confirm/' + json.order_id;
                            }, 800);
                            return;
                        }

                        // Otherwise poll for saved cart to be updated with order_id
                        pollForOrderId();
                    } else {
                        document.getElementById('status').innerText = 'Trạng thái: ' + (json.status || 'đang chờ');
                    }
                })
                .catch(err => {
                    console.error(err);
                });
        }

        function pollForOrderId() {
            // Poll SavedCart for order id by querying status endpoint repeatedly
            const interval = setInterval(() => {
                fetch('/payos/status/' + encodeURIComponent(paymentId))
                    .then(res => res.json())
                    .then(json => {
                        if (json.order_id) {
                            clearInterval(interval);
                            window.location.href = '/checkout/confirm/' + json.order_id;
                        }
                    })
                    .catch(err => console.error(err));
            }, 2000);
        }

        // start checking
        setTimeout(() => {
            checkStatus();
            setInterval(checkStatus, 5000);
        }, 800);
    </script>

@endsection