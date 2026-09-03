<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Setup Authenticator - InternHub</title>

    <meta name="theme-color" content="#8b5cf6">
    <meta name="description" content="Setup Google Authenticator - InternHub">

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
            max-width: 440px;
        }

        .icon-header {
            width: 70px;
            height: 70px;
            margin: 0 auto 24px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            color: white;
            background: linear-gradient(135deg, #10b981 0%, #34d399 100%);
            box-shadow: 0 12px 24px -6px rgba(16, 185, 129, 0.4);
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

        .steps {
            margin-bottom: 24px;
        }

        .step {
            display: flex;
            gap: 12px;
            margin-bottom: 16px;
        }

        .step-num {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            flex-shrink: 0;
            background: linear-gradient(135deg, #8b5cf6 0%, #a78bfa 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 14px;
        }

        .step-content h4 {
            font-size: 0.875rem;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 4px;
        }

        .step-content p {
            font-size: 0.75rem;
            color: #64748b;
        }

        .qr-container {
            background: #f8fafc;
            border-radius: 16px;
            padding: 24px;
            display: flex;
            justify-content: center;
            margin-bottom: 16px;
        }

        .qr-container svg {
            width: 180px;
            height: 180px;
        }

        .secret-key {
            font-family: 'Courier New', monospace;
            font-size: 13px;
            letter-spacing: 2px;
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            color: #92400e;
            padding: 12px 16px;
            border-radius: 8px;
            text-align: center;
            word-break: break-all;
            margin-bottom: 24px;
        }

        .secret-label {
            text-align: center;
            font-size: 0.75rem;
            color: #64748b;
            margin-bottom: 8px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            font-size: 0.875rem;
            font-weight: 600;
            color: #374151;
            margin-bottom: 8px;
        }

        .form-control {
            width: 100%;
            padding: 16px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-size: 24px;
            text-align: center;
            letter-spacing: 8px;
            font-weight: 600;
            transition: all 0.2s;
        }

        .form-control:focus {
            outline: none;
            border-color: #8b5cf6;
            box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.1);
        }

        .form-control.error {
            border-color: #ef4444;
        }

        .error-msg {
            color: #ef4444;
            font-size: 0.875rem;
            margin-top: 8px;
            text-align: center;
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
            <i class="fas fa-shield-halved"></i>
        </div>
        <h1>Setup Authenticator</h1>
        <p class="subtitle">Amankan akun Anda dengan verifikasi ganda sebelum melanjutkan</p>

        <!-- User Info -->
        <div class="user-info">
            @if ($user->avatar)
                <div class="user-avatar"><img src="{{ $user->avatar }}" alt="Avatar"></div>
            @else
                <div class="user-avatar"><i class="fas fa-user"></i></div>
            @endif
            <div>
                <div class="user-name">{{ $user->name }}</div>
                <div class="user-email">{{ $user->email }}</div>
            </div>
        </div>

        <!-- Steps -->
        <div class="steps">
            <div class="step">
                <div class="step-num">1</div>
                <div class="step-content">
                    <h4>Install Google Authenticator</h4>
                    <p>Download dari App Store atau Play Store</p>
                </div>
            </div>
            <div class="step">
                <div class="step-num">2</div>
                <div class="step-content">
                    <h4>Scan QR Code</h4>
                    <p>Buka app dan scan QR code di bawah</p>
                </div>
            </div>
        </div>

        <!-- QR Code -->
        <div class="qr-container">
            {!! $qrCodeSvg !!}
        </div>

        <!-- Manual Entry -->
        <p class="secret-label">Atau masukkan kode ini secara manual:</p>
        <div class="secret-key">{{ $secret }}</div>

        <!-- Verification Form -->
        <form method="POST" action="{{ route('oauth.2fa.setup.verify') }}">
            @csrf

            <div class="step" style="margin-bottom: 16px;">
                <div class="step-num">3</div>
                <div class="step-content">
                    <h4>Masukkan Kode Verifikasi</h4>
                    <p>Masukkan 6 digit kode dari aplikasi</p>
                </div>
            </div>

            <div class="form-group">
                <input type="text" name="code" class="form-control @error('code') error @enderror"
                    placeholder="000000" maxlength="6" pattern="\d{6}" inputmode="numeric" autocomplete="one-time-code"
                    required autofocus>
                @error('code')
                    <p class="error-msg"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="fas fa-check-circle"></i>
                @if (isset($afterAction) && $afterAction === 'login')
                    Aktifkan Verifikasi Ganda & Masuk
                @else
                    Aktifkan Verifikasi Ganda & Lanjutkan
                @endif
            </button>
        </form>

        <!-- Cancel -->
        <form method="POST" action="{{ route('oauth.2fa.cancel') }}">
            @csrf
            <button type="submit" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Batal
            </button>
        </form>

        <p class="help-text">
            <i class="fas fa-info-circle"></i>
            @if (isset($afterAction) && $afterAction === 'login')
                Anda wajib mengaktifkan verifikasi ganda untuk keamanan akun
            @else
                Setelah ini Anda akan mengisi informasi profil
            @endif
        </p>
    </div>

    <script>
        const input = document.querySelector('input[name="code"]');
        input.addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9]/g, '').slice(0, 6);
        });
    </script>
</body>

</html>
