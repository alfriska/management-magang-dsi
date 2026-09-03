<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Verifikasi 2FA - InternHub</title>

    <!-- PWA Meta Tags -->
    <meta name="theme-color" content="#8b5cf6">
    <meta name="description" content="Verifikasi Two-Factor Authentication - InternHub">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="InternHub">

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
            background: white;
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
            animation: shake 0.3s ease-in-out;
        }

        @keyframes shake {

            0%,
            100% {
                transform: translateX(0);
            }

            25% {
                transform: translateX(-5px);
            }

            75% {
                transform: translateX(5px);
            }
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

        .user-info {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            background: #f8fafc;
            border-radius: 12px;
            margin-bottom: 24px;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #8b5cf6, #a78bfa);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 16px;
        }

        .user-details {
            flex: 1;
            min-width: 0;
        }

        .user-name {
            font-weight: 600;
            color: #1e293b;
            font-size: 14px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .user-email {
            font-size: 12px;
            color: #64748b;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .loading-state {
            display: none;
        }

        .form-loading .loading-state {
            display: flex;
        }

        .form-loading .normal-state {
            display: none;
        }
    </style>
</head>

<body class="min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-sm">
        <div class="card p-8">
            <!-- Icon -->
            <div class="authenticator-icon">
                <i class="fas fa-shield-halved"></i>
            </div>

            <!-- Header -->
            <div class="text-center mb-4">
                <h1 class="text-xl font-extrabold text-slate-800 mb-2 tracking-tight">Verifikasi Kode 2FA</h1>
                <p class="text-slate-400 text-sm">Masukkan kode dari Google Authenticator</p>
            </div>

            <!-- User Info -->
            <div class="user-info">
                <div class="user-avatar">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <div class="user-details">
                    <div class="user-name">{{ $user->name }}</div>
                    <div class="user-email">{{ $user->email }}</div>
                </div>
            </div>

            <!-- Error Messages -->
            @if ($errors->any())
                <div class="alert alert-error mb-5">
                    <i class="fas fa-exclamation-circle"></i> {{ $errors->first() }}
                </div>
            @endif

            <!-- 2FA Form -->
            <form method="POST" action="{{ route('email.2fa.verify.submit') }}" id="otpForm">
                @csrf

                <p class="text-center text-slate-500 text-sm mb-4">
                    Masukkan 6 digit kode dari aplikasi authenticator
                </p>

                <!-- Visual OTP boxes -->
                <div class="otp-input-container" id="otpBoxes">
                    <input type="text" class="otp-input-single" maxlength="1" inputmode="numeric" data-index="0"
                        autocomplete="off">
                    <input type="text" class="otp-input-single" maxlength="1" inputmode="numeric" data-index="1"
                        autocomplete="off">
                    <input type="text" class="otp-input-single" maxlength="1" inputmode="numeric" data-index="2"
                        autocomplete="off">
                    <input type="text" class="otp-input-single" maxlength="1" inputmode="numeric" data-index="3"
                        autocomplete="off">
                    <input type="text" class="otp-input-single" maxlength="1" inputmode="numeric" data-index="4"
                        autocomplete="off">
                    <input type="text" class="otp-input-single" maxlength="1" inputmode="numeric" data-index="5"
                        autocomplete="off">
                </div>

                <!-- Hidden input for form submission -->
                <input type="hidden" name="code" id="codeHidden">

                <button type="submit" class="btn btn-primary w-full py-3 text-sm" id="submitBtn">
                    <span class="normal-state">
                        <i class="fas fa-check-circle"></i> Verifikasi
                    </span>
                    <span class="loading-state items-center justify-center gap-2">
                        <i class="fas fa-circle-notch fa-spin"></i> Memverifikasi...
                    </span>
                </button>
            </form>

            <!-- Back to login -->
            <div class="text-center mt-6">
                <form method="POST" action="{{ route('email.2fa.cancel') }}" class="inline">
                    @csrf
                    <button type="submit"
                        class="text-sm text-slate-500 hover:text-violet-500 bg-transparent border-0 cursor-pointer">
                        <i class="fas fa-arrow-left"></i> Kembali ke Login
                    </button>
                </form>
            </div>
        </div>

        <!-- Help Text -->
        <p class="text-center text-sm text-slate-400 mt-4">
            <i class="fas fa-mobile-alt"></i>
            Buka aplikasi Google Authenticator di ponsel Anda
        </p>
    </div>

    <script>
        // OTP input handling
        const inputs = document.querySelectorAll('.otp-input-single');
        const hiddenInput = document.getElementById('codeHidden');
        const form = document.getElementById('otpForm');
        const submitBtn = document.getElementById('submitBtn');
        let isSubmitting = false;

        function updateHiddenInput() {
            let value = '';
            inputs.forEach(input => {
                value += input.value;
            });
            hiddenInput.value = value;
            return value;
        }

        function setError() {
            inputs.forEach(input => {
                input.classList.add('error');
                setTimeout(() => input.classList.remove('error'), 300);
            });
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

                const fullCode = updateHiddenInput();

                // Auto submit when all filled
                if (fullCode.length === 6 && !isSubmitting) {
                    isSubmitting = true;
                    submitBtn.classList.add('form-loading');
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

                const fullCode = updateHiddenInput();

                if (pastedData.length === 6 && !isSubmitting) {
                    isSubmitting = true;
                    submitBtn.classList.add('form-loading');
                    form.submit();
                } else if (inputs[pastedData.length]) {
                    inputs[pastedData.length].focus();
                }
            });

            // Clear error state on focus
            input.addEventListener('focus', () => {
                input.classList.remove('error');
            });
        });

        // Form submission
        form.addEventListener('submit', (e) => {
            const code = updateHiddenInput();
            if (code.length !== 6) {
                e.preventDefault();
                setError();
                return false;
            }
            submitBtn.classList.add('form-loading');
        });

        // Focus first input on load
        inputs[0].focus();

        // Show error animation if there are errors
        @if ($errors->has('code'))
            setError();
        @endif
    </script>
</body>

</html>
