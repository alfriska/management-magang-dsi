<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Konfirmasi Password - InternHub</title>

    <meta name="theme-color" content="#8b5cf6">
    <link rel="manifest" href="/manifest.json">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    @vite(['resources/css/app.css'])
</head>

<body class="min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-sm">
        <div class="card p-8">
            <!-- Header -->
            <div class="text-center mb-6">
                <div class="w-14 h-14 mx-auto mb-4 rounded-2xl flex items-center justify-center text-2xl text-white shadow-lg"
                    style="background: linear-gradient(135deg, #f59e0b 0%, #fbbf24 100%); box-shadow: 0 8px 20px -4px rgba(245,158,11,0.5);">
                    <i class="fas fa-lock"></i>
                </div>
                <h1 class="text-xl font-extrabold text-slate-800 mb-1 tracking-tight">Konfirmasi Password</h1>
                <p class="text-slate-400 text-sm">Masukkan password untuk melanjutkan</p>
            </div>

            <!-- Error Messages -->
            @if ($errors->any())
                <div class="alert alert-error mb-5">
                    <i class="fas fa-exclamation-circle"></i> {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.confirm') }}">
                @csrf

                <div class="form-group">
                    <label class="form-label">Password</label>
                    <div class="search-input">
                        <input type="password" name="password" class="form-control" placeholder="••••••••" required
                            autofocus>
                        <i class="fas fa-lock"></i>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-full py-3 text-sm">
                    <i class="fas fa-check"></i> Konfirmasi
                </button>
            </form>
        </div>
    </div>
</body>

</html>
