<!doctype html>
<html lang="vi">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Thông tin tài khoản</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --accent: #f9cf00ff;
            --muted: #f3f4f6;
        }

        body {
            font-family: 'Nunito', Arial, sans-serif;
            background: #e9eef2;
            margin: 0;
            padding: 40px;
        }

        .wrap {
            max-width: 520px;
            margin: 40px auto;
        }

        .card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 6px 24px rgba(0, 0, 0, .08);
            padding: 28px;
            text-align: center;
        }

        .brand-logo img {
            width: 150px;
            margin-bottom: 12px;
        }

        h2 {
            font-weight: 700;
            margin-bottom: 18px;
        }

        .field {
            text-align: left;
            margin: 12px 0;
        }

        .label {
            color: #666;
            font-size: 14px;
            margin-bottom: 4px;
        }

        .value {
            font-size: 16px;
            font-weight: 600;
        }

        hr {
            border: none;
            border-top: 1px solid #eee;
            margin: 18px 0;
        }

        .btn {
            display: inline-block;
            background: var(--accent);
            color: #000;
            font-weight: 600;
            padding: 10px 18px;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            transition: 0.2s;
            text-decoration: none;
            margin-top: 20px;
        }

        .btn:hover {
            background: #ffd600;
        }

        .secondary {
            background: var(--muted);
            color: #333;
        }

        .message {
            background: #fff1f0;
            border: 1px solid #f2c6c4;
            color: #8a1f1f;
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 12px;
        }
    </style>
</head>

<body>
    <div class="wrap">
        <div class="card">
            <div class="brand-logo">
                <img src="{{ asset('images/logo.png') }}" alt="FoodMart Logo">
            </div>

            <h2>Thông tin tài khoản</h2>

            @if(!$user)
                <div class="message">
                    Không tìm thấy thông tin tài khoản.
                </div>
                <a href="{{ route('login') }}" class="btn secondary">Quay lại đăng nhập</a>
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
                    <h4 style="text-align:left;">Thông tin khách hàng</h4>
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
                    <button type="submit" class="btn">Đăng xuất</button>
                </form>
            @endif
        </div>
    </div>
</body>

</html>