<!doctype html>
<html lang="vi">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Đăng ký</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            padding: 40px
        }

        .card {
            max-width: 520px;
            margin: 40px auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 6px
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
        <h2 style="text-align:center">Đăng ký</h2>

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
            <label for="name">Họ và tên</label>
            <input id="name" name="name" type="text" value="{{ old('name') }}" required>

            <label for="email">Email</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required>

            <label for="password">Mật khẩu</label>
            <input id="password" name="password" type="password" required>

            <label for="password_confirmation">Xác nhận mật khẩu</label>
            <input id="password_confirmation" name="password_confirmation" type="password" required>

            <hr>
            <h4>Thông tin liên hệ</h4>
            <p>Vui lòng nhập Số điện thoại và Địa chỉ để lưu vào hồ sơ khách hàng.</p>

            <label for="phone">Số điện thoại</label>
            <input id="phone" name="phone" type="text" value="{{ old('phone') }}" required>

            <label for="address">Địa chỉ</label>
            <input id="address" name="address" type="text" value="{{ old('address') }}">

            <button type="submit">Đăng ký</button>
        </form>

        <p style="margin-top:12px">Đã có tài khoản? <a href="{{ route('login') }}">Đăng nhập</a></p>
    </div>
</body>

</html>