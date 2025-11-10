<!doctype html>
<html lang="vi">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Đăng nhập</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            padding: 40px
        }

        .card {
            max-width: 420px;
            margin: 40px auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 6px
        }

        .person-icon {
            font-size: 48px;
            display: block;
            text-align: center;
            margin-bottom: 10px
        }

        .error {
            color: #b00020
        }

        label {
            display: block;
            margin-top: 8px
        }

        input {
            width: 100%;
            padding: 8px;
            margin-top: 4px
        }

        button {
            margin-top: 12px;
            padding: 10px 16px
        }
    </style>
</head>

<body>
    <div class="card">
        <div class="person-icon">👤</div>
        <h2 style="text-align:center">Đăng nhập</h2>

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
            <label for="email">Email</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required>

            <label for="password">Mật khẩu</label>
            <input id="password" name="password" type="password" required>

            <button type="submit">Đăng nhập</button>
        </form>

        <p style="margin-top:12px">Chưa có tài khoản? <a href="{{ route('register') }}">Đăng ký</a></p>
    </div>
</body>

</html>