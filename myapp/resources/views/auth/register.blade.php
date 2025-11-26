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
            box-sizing: border-box;
        }

        .actions {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 20px;
            gap: 10px;
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
            flex: 1;
        }

        .btn:hover {
            background: #ffd600;
        }

        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .secondary {
            background: #f3f4f6;
            color: #333;
            padding: 10px 14px;
            border-radius: 8px;
            text-decoration: none;
            display: inline-block;
            flex: 1;
            text-align: center;
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

        .step {
            display: none;
        }

        .step.active {
            display: block;
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

            <!-- Step 1: Enter Email -->
            <div id="step1" class="step active">
                <form id="step1Form">
                    @csrf
                    <div class="field">
                        <label for="step1_email">Email</label>
                        <input id="step1_email" name="email" type="email" placeholder="Nhập email" required>
                    </div>
                    <p class="note">Chúng tôi sẽ gửi mã xác thực đến email của bạn</p>
                    <div class="actions">
                        <button class="btn" type="button" id="sendCodeBtn">Gửi mã xác thực</button>
                        <a href="{{ route('login') }}" class="secondary">Đăng nhập</a>
                    </div>
                </form>
            </div>

            <!-- Step 2: Verify Code -->
            <div id="step2" class="step">
                <form id="step2Form">
                    @csrf
                    <input type="hidden" id="verified_email" name="email">
                    
                    <div class="field">
                        <label>Mã xác thực đã được gửi đến email của bạn</label>
                        <input id="verification_code" name="verification_code" type="text" placeholder="Nhập 6 chữ số" maxlength="6" required pattern="\d{6}">
                    </div>
                    <p class="note">Nhập mã 6 chữ số từ email</p>
                    <div class="actions">
                        <button class="btn" type="button" id="verifyCodeBtn">Xác thực</button>
                        <button class="btn secondary" type="button" id="backBtn">Quay lại</button>
                    </div>
                </form>
            </div>

            <!-- Step 3: Fill in personal info -->
            <div id="step3" class="step">
                <form id="step3Form" method="POST" action="{{ route('register.post') }}">
                    @csrf
                    <input type="hidden" id="final_email" name="email">

                    <div class="field">
                        <label for="name">Họ và tên</label>
                        <input id="name" name="name" type="text" placeholder="Nhập họ và tên" required>
                    </div>

                    <div class="field">
                        <label for="password">Mật khẩu</label>
                        <input id="password" name="password" type="password" placeholder="Nhập mật khẩu" required>
                    </div>

                    <div class="field">
                        <label for="password_confirmation">Xác nhận mật khẩu</label>
                        <input id="password_confirmation" name="password_confirmation" type="password" placeholder="Nhập lại mật khẩu" required>
                    </div>

                    <div class="field">
                        <label for="phone">Số điện thoại</label>
                        <input id="phone" name="phone" type="text" placeholder="Nhập số điện thoại" required>
                    </div>

                    <div class="field">
                        <label for="address">Địa chỉ</label>
                        <input id="address" name="address" type="text" placeholder="Nhập địa chỉ của bạn">
                    </div>
                    <hr>

                    <div class="actions">
                        <button class="btn" type="submit">Đăng ký</button>
                        <button class="btn secondary" type="button" id="backBtn2">Quay lại</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function showStep(stepNum) {
            document.getElementById('step1').classList.remove('active');
            document.getElementById('step2').classList.remove('active');
            document.getElementById('step3').classList.remove('active');
            document.getElementById('step' + stepNum).classList.add('active');
        }

        document.getElementById('sendCodeBtn').addEventListener('click', function(e) {
            e.preventDefault();
            const email = document.getElementById('step1_email').value;
            const btn = this;
            
            if (!email) {
                alert('Vui lòng nhập email');
                return;
            }

            btn.disabled = true;
            btn.textContent = 'Đang gửi...';

            const csrfToken = document.querySelector('#step1Form input[name="_token"]').value;

            fetch('{{ route("register.send-code") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ email: email })
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('verified_email').value = email;
                    showStep(2);
                    alert('Mã xác thực đã được gửi! Kiểm tra email của bạn.');
                } else {
                    alert(data.message || 'Lỗi gửi mã');
                }
            })
            .catch(err => {
                alert('Lỗi: ' + err.message);
                console.error(err);
            })
            .finally(() => {
                btn.disabled = false;
                btn.textContent = 'Gửi mã xác thực';
            });
        });

        document.getElementById('verifyCodeBtn').addEventListener('click', function() {
            const email = document.getElementById('verified_email').value;
            const code = document.getElementById('verification_code').value;
            const btn = this;

            if (code.length !== 6 || !/^\d{6}$/.test(code)) {
                alert('Mã xác thực phải là 6 chữ số');
                return;
            }

            btn.disabled = true;
            btn.textContent = 'Đang xác thực...';

            const csrfToken = document.querySelector('#step2Form input[name="_token"]').value;

            fetch('{{ route("register.verify-code") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ email: email, code: code })
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('final_email').value = email;
                    showStep(3);
                    alert('Xác thực thành công! Điền thông tin cá nhân để hoàn tất đăng ký.');
                } else {
                    alert(data.message || 'Lỗi xác thực');
                    document.getElementById('verification_code').value = '';
                }
            })
            .catch(err => {
                alert('Lỗi: ' + err.message);
                console.error(err);
            })
            .finally(() => {
                btn.disabled = false;
                btn.textContent = 'Xác thực';
            });
        });

        document.getElementById('backBtn').addEventListener('click', function() {
            showStep(1);
            document.getElementById('verification_code').value = '';
        });

        document.getElementById('backBtn2').addEventListener('click', function() {
            showStep(2);
        });
    </script>
</body>

</html>
