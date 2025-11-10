<!doctype html>
<html lang="vi">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Thông tin tài khoản</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            padding: 30px
        }

        .card {
            max-width: 640px;
            margin: 40px auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 6px
        }

        .field {
            margin: 8px 0
        }

        .label {
            color: #666;
            font-size: 13px
        }

        .value {
            font-size: 16px
        }

        button {
            margin-top: 12px;
            padding: 8px 14px
        }
    </style>
</head>

<body>
    <div class="card">
        <h2>Thông tin tài khoản</h2>

        @if(!$user)
            <p>Không tìm thấy thông tin tài khoản.</p>
            <p><a href="{{ route('login') }}">Đăng nhập</a></p>
        @else
            <div class="field">
                <div class="label">Họ và tên</div>
                <div class="value">{{ $user->name }}</div>
            </div>
            <div class="field">
                <div class="label">Email</div>
                <div class="value">{{ $user->email }}</div>
            </div>

            @if($customer)
                <hr>
                <h4>Thông tin khách hàng</h4>
                <div class="field">
                    <div class="label">Số điện thoại</div>
                    <div class="value">{{ $customer->phone }}</div>
                </div>
                <div class="field">
                    <div class="label">Địa chỉ</div>
                    <div class="value">{{ $customer->address }}</div>
                </div>
            @endif

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit">Đăng xuất</button>
            </form>
        @endif
    </div>
</body>

</html>