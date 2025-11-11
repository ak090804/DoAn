<!doctype html>
<html lang="vi">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Đăng ký</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --accent: #f9cf00ff;
            --muted: #f3f4f6;
        }

        body {
            font-family: 'Nunito', Arial, Helvetica, sans-serif;
            background: #e9eef2;
            margin: 0;
            padding: 40px;
        }

        .wrap {
            max-width: 500px;
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
            margin-bottom: 16px;
        }

        .tabs {
            display: flex;
            gap: 12px;
            justify-content: center;
            margin-bottom: 18px;
        }

        .tab {
            padding: 12px 26px;
            border-radius: 8px;
            cursor: pointer;
            text-decoration: none;
            color: #222;
        }

        .tab.active {
            background: var(--accent);
            color: #000;
            font-weight: 700;
        }

        h3 {
            margin: 10px 0 18px;
            font-weight: 700;
        }

        .field {
            margin: 12px 0;
            text-align: left;
        }

        label {
            display: block;
            font-size: 14px;
            color: #444;
            margin-bottom: 6px;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #e1e6ea;
            border-radius: 8px;
            font-size: 14px;
        }

        .actions {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 20px;
        }

        .btn {
            background: var(--accent);
            color: #000;
            font-weight: 600;
            padding: 10px 18px;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            transition: 0.2s;
        }

        .btn:hover {
            background: #ffd600;
        }

        .secondary {
            background: #f3f4f6;
            color: #333;
            padding: 10px 14px;
            border-radius: 8px;
            text-decoration: none;
        }

        .error {
            background: #fff1f0;
            border: 1px solid #f2c6c4;
            color: #8a1f1f;
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 12px;
        }

        hr {
            border: none;
            border-top: 1px solid #eee;
            margin: 20px 0;
        }

        p.note {
            font-size: 14px;
            color: #555;
            margin: 4px 0 12px;
            text-align: left;
        }
    </style>
</head>

<body>
    <div class="wrap">
        <div class="card">
            <div class="brand-logo">
                <img src="{{ asset('images/logo.png') }}" alt="logo">
            </div>

            <div class="tabs">
                <a href="{{ route('login') }}" class="tab">Đăng nhập</a>
                <a class="tab active">Đăng ký</a>
            </div>

            <h3>Tạo tài khoản mới</h3>

            @if($errors->any())
                <div class="error">
                    <ul style="margin:0;padding-left:18px">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('register.post') }}">
                @csrf

                <div class="field">
                    <label for="name">Họ và tên</label>
                    <input id="name" name="name" type="text" value="{{ old('name') }}" placeholder="Nhập họ và tên"
                        required>
                </div>

                <div class="field">
                    <label for="email">Email</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" placeholder="Nhập email"
                        required>
                </div>

                <div class="field">
                    <label for="password">Mật khẩu</label>
                    <input id="password" name="password" type="password" placeholder="Nhập mật khẩu" required>
                </div>

                <div class="field">
                    <label for="password_confirmation">Xác nhận mật khẩu</label>
                    <input id="password_confirmation" name="password_confirmation" type="password"
                        placeholder="Nhập lại mật khẩu" required>
                </div>

                <div class="field">
                    <label for="phone">Số điện thoại</label>
                    <input id="phone" name="phone" type="text" value="{{ old('phone') }}"
                        placeholder="Nhập số điện thoại" required>
                </div>

                <div class="field">
                    <label for="address">Địa chỉ</label>
                    <input id="address" name="address" type="text" value="{{ old('address') }}"
                        placeholder="Nhập địa chỉ của bạn">
                </div>
                <hr>



                <div class="actions">
                    <button class="btn" type="submit">Đăng ký</button>
                    <a href="{{ route('login') }}" class="secondary">Đăng nhập</a>
                </div>
            </form>
        </div>
    </div>
</body>

</html>