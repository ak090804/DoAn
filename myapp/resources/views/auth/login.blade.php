<!doctype html>
<html lang="vi">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Đăng nhập</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --accent: #f9cf00ff;
            --muted: #f3f4f6
        }

        body {
            font-family: 'Nunito', Arial, Helvetica, sans-serif;
            background: #e9eef2;
            margin: 0;
            padding: 40px
        }

        .wrap {
            max-width: 500px;
            margin: 40px auto;
        }

        .card {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 6px 24px rgba(0, 0, 0, .08);
            padding: 28px;
            text-align: center;
        }

        .card .brand-logo img {
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
            color: #222
        }

        .tab.active {
            background: var(--accent);
            color: #fff
        }

        h3 {
            margin: 10px 0 18px
        }

        .field {
            margin: 12px 0;
            text-align: left;
        }

        label {
            display: block;
            font-size: 14px;
            color: #444;
            margin-bottom: 6px
        }

        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #e1e6ea;
            border-radius: 8px;
            font-size: 14px
        }

        .field.remember {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 6px;
        }

        .muted-link {
            color: #666;
            text-decoration: underline;
            font-size: 14px;
        }

        .actions {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 20px
        }

        .btn {
            background: var(--accent);
            color: #fff;
            padding: 10px 18px;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            transition: 0.2s;
        }

        .btn:hover {
            background: #f9cf00ff;
        }

        .secondary {
            background: #f3f4f6;
            color: #333;
            padding: 10px 14px;
            border-radius: 8px;
            text-decoration: none
        }

        .error {
            background: #fff1f0;
            border: 1px solid #f2c6c4;
            color: #8a1f1f;
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 12px
        }

        .flash {
            background: #e6ffed;
            border: 1px solid #b7f0c2;
            color: #12721f;
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 12px
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
                <a class="tab active">Đăng nhập</a>
                <a href="{{ route('register') }}" class="tab">Đăng ký</a>
            </div>

            <h3>Đăng nhập vào tài khoản của bạn</h3>

            @if(session('success'))
                <div class="flash">{{ session('success') }}</div>
            @endif

            @if($errors->any())
                <div class="error">
                    <ul style="margin:0;padding-left:18px">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('login.post') }}">
                @csrf

                <div class="field">
                    <label for="email">Email hoặc tên đăng nhập</label>
                    <input id="email" name="email" type="text" value="{{ old('email') }}"
                        placeholder="Nhập email hoặc tên đăng nhập" required>
                </div>

                <div class="field">
                    <label for="password">Mật khẩu</label>
                    <input id="password" name="password" type="password" placeholder="Nhập mật khẩu" required>
                </div>

                <div class="field remember">
                    <label style="display:flex;align-items:center;gap:8px">
                        <input type="checkbox" name="remember"> Ghi nhớ đăng nhập
                    </label>
                    <a class="muted-link" href="#">Quên mật khẩu?</a>
                </div>

                <div class="actions">
                    <button class="btn" type="submit">Đăng nhập</button>
                    <a href="{{ route('register') }}" class="secondary">Tạo tài khoản mới</a>
                </div>
            </form>
        </div>
    </div>
</body>

</html>