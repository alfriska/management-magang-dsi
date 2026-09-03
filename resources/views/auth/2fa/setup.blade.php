<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Setup Authenticator - InternHub</title>

    <!-- PWA Meta Tags -->
    <meta name="theme-color" content="#8b5cf6">
    <meta name="description" content="Setup Google Authenticator - InternHub">
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
        .qr-container {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            border-radius: 16px;
            padding: 24px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .qr-container svg {
            width: 200px;
            height: 200px;
            border-radius: 8px;
        }

        .secret-key {
            font-family: 'Courier New', monospace;
            font-size: 14px;
            letter-spacing: 2px;
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            color: #92400e;
            padding: 12px 16px;
            border-radius: 8px;
            text-align: center;
            word-break: break-all;
            user-select: all;
        }

        .step-number {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: linear-gradient(135deg, #a78bfa 0%, #c084fc 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 14px;
            flex-shrink: 0;
        }

        .otp-input {
            font-size: 24px;
            letter-spacing: 8px;
            text-align: center;
            font-weight: 600;
            padding: 16px;
        }

        .otp-input::placeholder {
            letter-spacing: normal;
            font-size: 16px;
        }
    </style>
</head>

<body class="min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <div class="card p-8">
            <!-- Header -->
            <div class="text-center mb-6">
                <div class="w-14 h-14 mx-auto mb-4 rounded-2xl flex items-center justify-center text-2xl text-white shadow-lg"
                    style="background: linear-gradient(135deg, #10b981 0%, #34d399 100%); box-shadow: 0 8px 20px -4px rgba(16,185,129,0.5);">
                    <i class="fas fa-shield-halved"></i>
                </div>
                <h1 class="text-xl font-extrabold text-slate-800 mb-1 tracking-tight">Setup Authenticator</h1>
                <p class="text-slate-400 text-sm">Amankan akun Anda dengan Verifikasi Ganda</p>
            </div>

            <!-- User Info -->
            <div class="flex items-center gap-3 p-4 bg-slate-50 rounded-xl mb-6">
                @if ($user->avatar)
                    <img src="{{ $user->avatar }}" alt="Avatar" class="w-10 h-10 rounded-full">
                @else
                    <div class="w-10 h-10 rounded-full bg-violet-100 flex items-center justify-center">
                        <i class="fas fa-user text-violet-500"></i>
                    </div>
                @endif
                <div>
                    <p class="font-semibold text-slate-700">{{ $user->name }}</p>
                    <p class="text-sm text-slate-400">{{ $user->email }}</p>
                </div>
            </div>

            <!-- Instructions -->
            <div class="space-y-4 mb-6">
                <div class="flex gap-3">
                    <div class="step-number">1</div>
                    <div>
                        <p class="font-semibold text-slate-700">Install Google Authenticator</p>
                        <p class="text-sm text-slate-400">Download dari App Store atau Play Store</p>
                    </div>
                </div>

                <div class="flex gap-3">
                    <div class="step-number">2</div>
                    <div>
                        <p class="font-semibold text-slate-700">Scan QR Code</p>
                        <p class="text-sm text-slate-400">Buka app dan scan QR code di bawah</p>
                    </div>
                </div>
            </div>

            <!-- QR Code -->
            <div class="qr-container mb-4">
                {!! $qrCodeSvg !!}
            </div>

            <!-- Manual Entry -->
            <div class="mb-6">
                <p class="text-center text-sm text-slate-500 mb-2">Atau masukkan kode ini secara manual:</p>
                <div class="secret-key">{{ $secret }}</div>
            </div>

            <!-- Verification Form -->
            <form method="POST" action="{{ route('2fa.setup.verify') }}">
                @csrf

                <div class="flex gap-3 mb-4">
                    <div class="step-number">3</div>
                    <div class="flex-1">
                        <p class="font-semibold text-slate-700 mb-2">Verifikasi Kode dari google authentication</p>
                        <p class="text-sm text-slate-400 mb-3">Masukkan 6 digit kode dari aplikasi</p>

                        <input type="text" name="one_time_password"
                            class="form-control otp-input @error('one_time_password') border-red-500 @enderror"
                            placeholder="000000" maxlength="6" pattern="\d{6}" inputmode="numeric"
                            autocomplete="one-time-code" required autofocus>

                        @error('one_time_password')
                            <p class="text-red-500 text-sm mt-2">
                                <i class="fas fa-exclamation-circle"></i> {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-full py-3 text-sm mb-3">
                    <i class="fas fa-check-circle"></i> Aktifkan Verifikasi ganda
                </button>
            </form>

            <!-- Cancel -->
            <form method="POST" action="{{ route('2fa.cancel') }}">
                @csrf
                <button type="submit" class="btn btn-secondary w-full py-3 text-sm">
                    <i class="fas fa-arrow-left"></i> Batal
                </button>
            </form>
        </div>

        <!-- Help Text -->
        <p class="text-center text-sm text-slate-400 mt-4">
            <i class="fas fa-info-circle"></i>
            Simpan kode backup dengan aman jika diperlukan
        </p>
    </div>

    <script>
        // Auto-focus and format OTP input
        const otpInput = document.querySelector('input[name="one_time_password"]');
        otpInput.addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9]/g, '').slice(0, 6);
        });
    </script>
</body>

</html>
