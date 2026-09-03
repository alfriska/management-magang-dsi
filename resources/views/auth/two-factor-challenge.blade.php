<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Verifikasi Ganda - InternHub</title>

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

        .tab-btn {
            flex: 1;
            padding: 12px;
            border: none;
            background: transparent;
            cursor: pointer;
            font-weight: 600;
            color: #94a3b8;
            transition: all 0.2s;
            border-bottom: 2px solid transparent;
        }

        .tab-btn.active {
            color: #8b5cf6;
            border-bottom-color: #8b5cf6;
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
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
            <div class="text-center mb-6">
                <h1 class="text-xl font-extrabold text-slate-800 mb-2 tracking-tight">Verifikasi Ganda</h1>
                <p class="text-slate-400 text-sm">Masukkan kode autentikasi untuk melanjutkan</p>
            </div>
            <!-- Error Messages -->
            @if ($errors->any())
                <div class="alert alert-error mb-5">
                    <i class="fas fa-exclamation-circle"></i> {{ $errors->first() }}
                </div>
            @endif

            <!-- Tabs -->
            <div class="flex border-b border-slate-200 mb-6">
                <button type="button" class="tab-btn active" data-tab="code">
                    <i class="fas fa-mobile-alt mr-2"></i> Kode Autentikator
                </button>
                <button type="button" class="tab-btn" data-tab="recovery">
                    <i class="fas fa-key mr-2"></i> Kode Recovery
                </button>
            </div>

            <!-- Authenticator Code Form -->
            <div id="code-tab" class="tab-content active">
                <form method="POST" action="{{ route('two-factor.login') }}" id="codeForm">
                    @csrf

                    <p class="text-center text-slate-500 text-sm mb-4">
                        Masukkan 6 digit kode dari Google Authenticator
                    </p>

                    <!-- Visual OTP boxes -->
                    <div class="otp-input-container" id="otpBoxes">
                        <input type="text" class="otp-input-single" maxlength="1" inputmode="numeric"
                            data-index="0">
                        <input type="text" class="otp-input-single" maxlength="1" inputmode="numeric"
                            data-index="1">
                        <input type="text" class="otp-input-single" maxlength="1" inputmode="numeric"
                            data-index="2">
                        <input type="text" class="otp-input-single" maxlength="1" inputmode="numeric"
                            data-index="3">
                        <input type="text" class="otp-input-single" maxlength="1" inputmode="numeric"
                            data-index="4">
                        <input type="text" class="otp-input-single" maxlength="1" inputmode="numeric"
                            data-index="5">
                    </div>

                    <!-- Hidden input for form submission -->
                    <input type="hidden" name="code" id="codeHidden">

                    <button type="submit" class="btn btn-primary w-full py-3 text-sm">
                        <i class="fas fa-check-circle"></i> Verifikasi
                    </button>
                </form>
            </div>

            <!-- Recovery Code Form -->
            <div id="recovery-tab" class="tab-content">
                <form method="POST" action="{{ route('two-factor.login') }}">
                    @csrf

                    <p class="text-center text-slate-500 text-sm mb-4">
                        Masukkan salah satu kode recovery Anda
                    </p>

                    <div class="form-group">
                        <input type="text" name="recovery_code" class="form-control text-center"
                            placeholder="xxxxx-xxxxx" style="font-family: monospace; letter-spacing: 2px;">
                    </div>

                    <button type="submit" class="btn btn-primary w-full py-3 text-sm">
                        <i class="fas fa-key"></i> Gunakan Kode Recovery
                    </button>
                </form>
            </div>

            <!-- Back to login -->
            <div class="text-center mt-6">
                <a href="{{ route('login') }}" class="text-sm text-slate-500 hover:text-violet-500">
                    <i class="fas fa-arrow-left"></i> Kembali ke Login
                </a>
            </div>
        </div>

        <!-- Help Text -->
        <p class="text-center text-sm text-slate-400 mt-4">
            <i class="fas fa-mobile-alt"></i>
            Buka aplikasi Google Authenticator di ponsel Anda
        </p>
    </div>

    <script>
        // Tab switching
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
                document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));

                btn.classList.add('active');
                document.getElementById(btn.dataset.tab + '-tab').classList.add('active');
            });
        });

        // OTP input handling
        const inputs = document.querySelectorAll('.otp-input-single');
        const hiddenInput = document.getElementById('codeHidden');
        const form = document.getElementById('codeForm');

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
