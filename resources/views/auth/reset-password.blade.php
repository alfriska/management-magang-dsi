<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Reset Password - InternHub</title>

    <!-- PWA Meta Tags -->
    <meta name="theme-color" content="#8b5cf6">
    <meta name="description" content="Reset password akun InternHub">
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
</head>

<body class="min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-sm">
        <div class="card p-8 sm:p-10">
            <!-- Brand -->
            <div class="text-center mb-8">
                <div class="w-14 h-14 mx-auto mb-4 rounded-2xl flex items-center justify-center text-2xl text-white shadow-lg"
                    style="background: linear-gradient(135deg, #a78bfa 0%, #c084fc 100%); box-shadow: 0 8px 20px -4px rgba(167,139,250,0.5);">
                    <i class="fas fa-lock-open"></i>
                </div>
                <h1 class="text-xl font-extrabold text-slate-800 mb-1 tracking-tight">Reset Password</h1>
                <p class="text-slate-400 text-sm">Buat password baru untuk akun Anda</p>
            </div>

            <!-- Error Messages -->
            @if ($errors->any())
                <div class="alert alert-error mb-5">
                    <i class="fas fa-exclamation-circle"></i> {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.update') }}" class="space-y-5">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                <div class="form-group mb-0">
                    <label class="form-label">Email</label>
                    <div class="search-input">
                        <input type="email" name="email" class="form-control" value="{{ $email ?? old('email') }}"
                            placeholder="Masukkan email Anda" required>
                        <i class="fas fa-envelope"></i>
                    </div>
                </div>
                <div class="from-label mt-4">
                    <label class="form-label">Password Baru</label>
                    <div class="search-input">
                        <input type="password" name="password" class="form-control" placeholder="Minimal 8 karakter"
                            required autofocus>
                        <i class="fas fa-lock"></i>
                    </div>
                </div>
                <div class="form-group mb-0">
                    <label class="form-label">Konfirmasi Password</label>
                    <div class="search-input">
                        <input type="password" name="password_confirmation" class="form-control"
                            placeholder="Ulangi password baru" required>
                        <i class="fas fa-lock"></i>
                    </div>
                </div>
        </div>


        <button type="submit" class="btn btn-primary w-full py-3 text-sm mt-5">
            <i class="fas fa-check"></i> Reset Password
        </button>

        <div class="text-center mt-5">
            <a href="{{ route('login') }}" class="text-sm text-slate-500 hover:text-violet-500 font-medium">
                <i class="fas fa-arrow-left mr-1"></i> Kembali ke Login
            </a>
        </div>
        </form>
    </div>
    </div>
</body>

</html>
