<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Verifikasi Ganda - InternHub</title>

    <meta name="theme-color" content="#8b5cf6">
    <meta name="description" content="Verifikasi Google Authenticator - InternHub">

    <link rel="manifest" href="/manifest.json">
    <link rel="icon" type="image/png" sizes="32x32" href="/icons/icon-96x96.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/icons/icon-192x192.png">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .card {
            background: white;
            border-radius: 24px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.1);
            padding: 40px;
            width: 100%;
            max-width: 400px;
        }

        .icon-header {
            width: 80px;
            height: 80px;
            margin: 0 auto 24px;
            border-radius: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 36px;
            color: white;
            background: linear-gradient(135deg, #8b5cf6 0%, #a78bfa 100%);
            box-shadow: 0 12px 24px -6px rgba(139, 92, 246, 0.4);
        }

        h1 {
            text-align: center;
            color: #1e293b;
            font-size: 1.5rem;
            margin-bottom: 8px;
        }

        .subtitle {
            text-align: center;
            color: #64748b;
            font-size: 0.875rem;
            margin-bottom: 24px;
        }

        .user-info {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            padding: 16px;
            background: #f8fafc;
            border-radius: 12px;
            margin-bottom: 24px;
        }

        .user-avatar {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: #e0e7ff;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6366f1;
            font-size: 20px;
        }

        .user-avatar img {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
        }

        .user-name {
            font-weight: 600;
            color: #1e293b;
        }

        .user-email {
            font-size: 0.875rem;
            color: #64748b;
        }

        .error-alert {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #dc2626;
            padding: 12px 16px;
            border-radius: 12px;
            margin-bottom: 20px;
            font-size: 0.875rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .otp-container {
            display: flex;
            gap: 8px;
            justify-content: center;
            margin-bottom: 24px;
        }

        .otp-input {
            width: 48px;
            height: 56px;
            text-align: center;
            font-size: 24px;
            font-weight: 700;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            transition: all 0.2s;
        }

        .otp-input:focus {
            outline: none;
            border-color: #8b5cf6;
            box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.1);
        }

        .otp-input.filled {
            border-color: #8b5cf6;
            background: #faf5ff;
        }

        .otp-input.error {
            border-color: #ef4444;
            background: #fef2f2;
        }

        .hidden-input {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }

        .btn {
            width: 100%;
            padding: 14px 24px;
            border: none;
            border-radius: 12px;
            font-size: 0.875rem;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.2s;
        }

        .btn-primary {
            background: linear-gradient(135deg, #8b5cf6 0%, #a78bfa 100%);
            color: white;
            box-shadow: 0 4px 12px rgba(139, 92, 246, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(139, 92, 246, 0.4);
        }

        .btn-secondary {
            background: #f1f5f9;
            color: #64748b;
            margin-top: 12px;
        }

        .btn-secondary:hover {
            background: #e2e8f0;
        }

        .help-text {
            text-align: center;
            font-size: 0.75rem;
            color: #94a3b8;
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <div class="card">
        <!-- Header -->
        <div class="icon-header">
            <i class="fas fa-lock"></i>
        </div>
        <h1>Verifikasi Ganda</h1>
        <p class="subtitle">Masukkan 6 digit kode dari Google Authenticator</p>

        <!-- User Info -->
        <div class="user-info">
            @if ($user->avatar)
                <div class="user-avatar"><img src="{{ $user->avatar }}" alt="Avatar"></div>
            @else
                <div class="user-avatar"><i class="fas fa-user"></i></div>
            @endif
            <div style="text-align: left;">
                <div class="user-name">{{ $user->name }}</div>
                <div class="user-email">{{ $user->email }}</div>
            </div>
        </div>

        <!-- Error -->
        @if ($errors->any())
            <div class="error-alert">
                <i class="fas fa-exclamation-circle"></i> {{ $errors->first() }}
            </div>
        @endif

        <!-- OTP Form -->
        <form method="POST" action="{{ route('oauth.2fa.verify.submit') }}" id="otpForm">
            @csrf

            <input type="hidden" name="code" id="codeHidden">

            <div class="otp-container">
                <input type="text" class="otp-input @error('code') error @enderror" maxlength="1"
                    inputmode="numeric" data-index="0">
                <input type="text" class="otp-input @error('code') error @enderror" maxlength="1"
                    inputmode="numeric" data-index="1">
                <input type="text" class="otp-input @error('code') error @enderror" maxlength="1"
                    inputmode="numeric" data-index="2">
                <input type="text" class="otp-input @error('code') error @enderror" maxlength="1"
                    inputmode="numeric" data-index="3">
                <input type="text" class="otp-input @error('code') error @enderror" maxlength="1"
                    inputmode="numeric" data-index="4">
                <input type="text" class="otp-input @error('code') error @enderror" maxlength="1"
                    inputmode="numeric" data-index="5">
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="fas fa-check-circle"></i> Verifikasi & Masuk
            </button>
        </form>

        <!-- Cancel -->
        <form method="POST" action="{{ route('oauth.2fa.cancel') }}">
            @csrf
            <button type="submit" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Gunakan Akun Lain
            </button>
        </form>

        <p class="help-text">
            <i class="fas fa-mobile-alt"></i>
            Buka aplikasi Google Authenticator di ponsel Anda
        </p>
    </div>

    <script>
        const inputs = document.querySelectorAll('.otp-input');
        const hiddenInput = document.getElementById('codeHidden');
        const form = document.getElementById('otpForm');

        function updateHiddenInput() {
            let value = '';
            inputs.forEach(input => {
                value += input.value;
            });
            hiddenInput.value = value;
        }

        inputs.forEach((input, index) => {
            input.addEventListener('input', (e) => {
                const value = e.target.value.replace(/[^0-9]/g, '');
                e.target.value = value;

                if (value) {
                    e.target.classList.add('filled');
                    if (index < inputs.length - 1) inputs[index + 1].focus();
                } else {
                    e.target.classList.remove('filled');
                }

                updateHiddenInput();
                if (hiddenInput.value.length === 6) form.submit();
            });

            input.addEventListener('keydown', (e) => {
                if (e.key === 'Backspace' && !e.target.value && index > 0) {
                    inputs[index - 1].focus();
                }
            });

            input.addEventListener('paste', (e) => {
                e.preventDefault();
                const pastedData = e.clipboardData.getData('text').replace(/[^0-9]/g, '').slice(0, 6);
                pastedData.split('').forEach((char, i) => {
                    if (inputs[i]) {
                        inputs[i].value = char;
                        inputs[i].classList.add('filled');
                    }
                });
                updateHiddenInput();
                if (pastedData.length === 6) form.submit();
                else if (inputs[pastedData.length]) inputs[pastedData.length].focus();
            });
        });

        inputs[0].focus();
    </script>
</body>

</html>
