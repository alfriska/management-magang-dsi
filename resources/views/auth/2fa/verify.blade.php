<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Verifikasi Ganda - InternHub</title>

    <!-- PWA Meta Tags -->
    <meta name="theme-color" content="#8b5cf6">
    <meta name="description" content="Verifikasi Google Authenticator - InternHub">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="InternHub">

    <!-- PWA Manifest & Icons -->
    <link rel="manifest" href="/manifest.json">
    <link rel="icon" type="image/png" sizes="32x32" href="/icons/icon-96x96.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/icons/icon-192x192.png">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    @vite(['resources/css/app.css'])

    <style>
        .otp-input-container {
            display: flex;
            gap: 8px;
            justify-content: center;
            margin-bottom: 24px;
        }

        .otp-input-single {
            width: 48px;
            height: 56px;
            text-align: center;
            font-size: 24px;
            font-weight: 700;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            transition: all 0.2s;
        }

        .otp-input-single:focus {
            border-color: #8b5cf6;
            box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.1);
            outline: none;
        }

        .otp-input-single.filled {
            border-color: #8b5cf6;
            background: #faf5ff;
        }

        .otp-input-single.error {
            border-color: #ef4444;
            background: #fef2f2;
        }

        .authenticator-icon {
            background: linear-gradient(135deg, #8b5cf6 0%, #a78bfa 100%);
            width: 80px;
            height: 80px;
            border-radius: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px;
            box-shadow: 0 12px 24px -6px rgba(139, 92, 246, 0.4);
        }

        .authenticator-icon i {
            font-size: 36px;
            color: white;
        }

        .hidden-input {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }
    </style>
</head>

<body class="min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-sm">
        <div class="card p-8">
            <!-- Icon -->
            <div class="authenticator-icon">
                <i class="fas fa-lock"></i>
            </div>

            <!-- Header -->
            <div class="text-center mb-6">
                <h1 class="text-xl font-extrabold text-slate-800 mb-2 tracking-tight">Verifikasi Ganda</h1>
                <p class="text-slate-400 text-sm">Masukkan 6 digit kode dari Google Authenticator</p>
            </div>

            <!-- User Info -->
            <div class="flex items-center justify-center gap-3 p-4 bg-slate-50 rounded-xl mb-6">
                @if ($user->avatar)
                    <img src="{{ $user->avatar }}" alt="Avatar" class="w-10 h-10 rounded-full">
                @else
                    <div class="w-10 h-10 rounded-full bg-violet-100 flex items-center justify-center">
                        <i class="fas fa-user text-violet-500"></i>
                    </div>
                @endif
                <div class="text-left">
                    <p class="font-semibold text-slate-700">{{ $user->name }}</p>
                    <p class="text-sm text-slate-400">{{ $user->email }}</p>
                </div>
            </div>

            <!-- Error Messages -->
            @if ($errors->any())
                <div class="alert alert-error mb-5">
                    <i class="fas fa-exclamation-circle"></i> {{ $errors->first() }}
                </div>
            @endif

            <!-- OTP Form -->
            <form method="POST" action="{{ route('2fa.verify.submit') }}" id="otpForm">
                @csrf

                <!-- Hidden actual input -->
                <input type="text" name="one_time_password" id="otpHidden" class="hidden-input" maxlength="6"
                    pattern="\d{6}" required>

                <!-- Visual OTP boxes -->
                <div class="otp-input-container" id="otpBoxes">
                    <input type="text" class="otp-input-single @error('one_time_password') error @enderror"
                        maxlength="1" inputmode="numeric" data-index="0">
                    <input type="text" class="otp-input-single @error('one_time_password') error @enderror"
                        maxlength="1" inputmode="numeric" data-index="1">
                    <input type="text" class="otp-input-single @error('one_time_password') error @enderror"
                        maxlength="1" inputmode="numeric" data-index="2">
                    <input type="text" class="otp-input-single @error('one_time_password') error @enderror"
                        maxlength="1" inputmode="numeric" data-index="3">
                    <input type="text" class="otp-input-single @error('one_time_password') error @enderror"
                        maxlength="1" inputmode="numeric" data-index="4">
                    <input type="text" class="otp-input-single @error('one_time_password') error @enderror"
                        maxlength="1" inputmode="numeric" data-index="5">
                </div>

                <button type="submit" class="btn btn-primary w-full py-3 text-sm mb-3" id="verifyBtn">
                    <i class="fas fa-check-circle"></i> Verifikasi
                </button>
            </form>

            <!-- Cancel -->
            <form method="POST" action="{{ route('2fa.cancel') }}">
                @csrf
                <button type="submit" class="btn btn-secondary w-full py-3 text-sm">
                    <i class="fas fa-arrow-left"></i> Gunakan Akun Lain
                </button>
            </form>
        </div>

        <!-- Help Text -->
        <p class="text-center text-sm text-slate-400 mt-4">
            <i class="fas fa-mobile-alt"></i>
            Buka aplikasi Google Authenticator di ponsel Anda
        </p>
    </div>

    <script>
        const inputs = document.querySelectorAll('.otp-input-single');
        const hiddenInput = document.getElementById('otpHidden');
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
                    if (index < inputs.length - 1) {
                        inputs[index + 1].focus();
                    }
                } else {
                    e.target.classList.remove('filled');
                }

                updateHiddenInput();

                // Auto submit when all filled
                if (hiddenInput.value.length === 6) {
                    form.submit();
                }
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

                if (pastedData.length === 6) {
                    form.submit();
                } else if (inputs[pastedData.length]) {
                    inputs[pastedData.length].focus();
                }
            });
        });

        // Focus first input on load
        inputs[0].focus();
    </script>
</body>

</html>
