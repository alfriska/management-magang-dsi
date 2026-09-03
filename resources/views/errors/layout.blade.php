<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - InternHub</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Inline CSS for Critical Styling -->
    <style>
        :root {
            --primary: #6366f1;
            --primary-dark: #4f46e5;
            --text-dark: #1e293b;
            --text-light: #64748b;
            --bg-light: #f8fafc;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: 'Outfit', sans-serif;
            background-color: var(--bg-light);
            background-image:
                radial-gradient(at 0% 0%, rgba(99, 102, 241, 0.15) 0px, transparent 50%),
                radial-gradient(at 100% 0%, rgba(168, 85, 247, 0.15) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(236, 72, 153, 0.15) 0px, transparent 50%),
                radial-gradient(at 0% 100%, rgba(56, 189, 248, 0.15) 0px, transparent 50%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            color: var(--text-dark);
        }

        .container {
            text-align: center;
            padding: 2rem;
            position: relative;
            z-index: 10;
            max-width: 600px;
            width: 100%;
        }

        .error-code {
            font-size: 8rem;
            font-weight: 800;
            line-height: 1;
            margin-bottom: -1rem;
            background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            opacity: 0.2;
            position: relative;
            z-index: 0;
            letter-spacing: -0.05em;
        }

        .icon-box {
            width: 80px;
            height: 80px;
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 25px -5px rgba(99, 102, 241, 0.2), 0 8px 10px -6px rgba(99, 102, 241, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 2rem;
            position: relative;
            z-index: 2;
        }

        .icon-box i {
            font-size: 32px;
            color: var(--primary);
        }

        .title {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.75rem;
            color: var(--text-dark);
            line-height: 1.2;
        }

        .description {
            font-size: 1.1rem;
            color: var(--text-light);
            margin-bottom: 2.5rem;
            line-height: 1.6;
        }

        .buttons {
            display: flex;
            justify-content: center;
            gap: 1rem;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            border-radius: 12px;
            font-weight: 600;
            font-size: 0.95rem;
            text-decoration: none;
            transition: all 0.2s ease;
            cursor: pointer;
        }

        .btn-primary {
            background-color: var(--primary);
            color: white;
            box-shadow: 0 4px 6px -1px rgba(99, 102, 241, 0.3);
            border: 1px solid transparent;
        }

        .btn-primary:hover {
            background-color: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(99, 102, 241, 0.3);
        }

        .btn-secondary {
            background-color: white;
            color: var(--text-dark);
            border: 1px solid #e2e8f0;
        }

        .btn-secondary:hover {
            border-color: var(--primary);
            color: var(--primary);
            background-color: #f8fafc;
        }

        .footer {
            position: absolute;
            bottom: 2rem;
            font-size: 0.75rem;
            color: #94a3b8;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.1em;
        }

        /* Mobile tweaks */
        @media (max-width: 640px) {
            .error-code { font-size: 6rem; }
            .title { font-size: 1.5rem; }
            .description { font-size: 1rem; padding: 0 1rem; }
            .buttons { flex-direction: column; width: 100%; padding: 0 2rem; box-sizing: border-box; }
            .btn { width: 100%; justify-content: center; }
        }
    </style>
</head>
<body>

    <div class="container">
        <!-- Icon -->
        <div class="icon-box">
            <i class="fas @yield('icon')"></i>
        </div>

        <!-- Content -->
        <div style="position: relative;">
            <div class="error-code">@yield('code')</div>
            <h1 class="title">@yield('message')</h1>
            <p class="description">@yield('description')</p>
        </div>

        <!-- Buttons -->
        <div class="buttons">
            <a href="/" class="btn btn-primary">
                <i class="fas fa-home"></i>
                Ke Beranda
            </a>

            @hasSection('secondary-action')
                @yield('secondary-action')
            @else
                <a href="{{ url()->previous() != url()->current() ? url()->previous() : '/' }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i>
                    Kembali
                </a>
            @endif
        </div>
    </div>

    <div class="footer">
        InternHub Management System &copy; {{ date('Y') }}
    </div>

</body>
</html>
