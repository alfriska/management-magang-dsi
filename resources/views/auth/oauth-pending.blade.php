<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Menunggu Persetujuan - InternHub</title>

    <meta name="theme-color" content="#8b5cf6">
    <meta name="description" content="Menunggu Persetujuan Admin - InternHub">

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

        .container {
            width: 100%;
            max-width: 500px;
        }

        .card {
            background: white;
            border-radius: 24px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.1);
            padding: 48px 40px;
            text-align: center;
        }

        .icon-container {
            position: relative;
            width: 100px;
            height: 100px;
            margin: 0 auto 32px;
        }

        .icon-bg {
            width: 100px;
            height: 100px;
            border-radius: 28px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 44px;
            color: white;
            background: linear-gradient(135deg, #f59e0b 0%, #fbbf24 100%);
            box-shadow: 0 16px 32px -8px rgba(245, 158, 11, 0.4);
            animation: pulse 2s ease-in-out infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.05);
            }
        }

        .spinner-ring {
            position: absolute;
            top: -10px;
            left: -10px;
            width: 120px;
            height: 120px;
            border: 4px solid transparent;
            border-top-color: #f59e0b;
            border-radius: 50%;
            animation: spin 2s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        h1 {
            color: #1e293b;
            font-size: 1.75rem;
            font-weight: 800;
            margin-bottom: 12px;
        }

        .subtitle {
            color: #64748b;
            font-size: 1rem;
            margin-bottom: 32px;
            line-height: 1.6;
        }

        .status-box {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            border: 2px solid #fbbf24;
            border-radius: 16px;
            padding: 20px 24px;
            margin-bottom: 32px;
        }

        .status-label {
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #92400e;
            margin-bottom: 8px;
        }

        .status-value {
            font-size: 1.25rem;
            font-weight: 700;
            color: #b45309;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .info-list {
            text-align: left;
            background: #f8fafc;
            border-radius: 16px;
            padding: 20px 24px;
            margin-bottom: 32px;
        }

        .info-item {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 12px 0;
            border-bottom: 1px solid #e2e8f0;
        }

        .info-item:last-child {
            border-bottom: none;
        }

        .info-icon {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            background: #e0e7ff;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6366f1;
            flex-shrink: 0;
        }

        .info-text {
            flex: 1;
        }

        .info-title {
            font-weight: 600;
            color: #1e293b;
            font-size: 0.875rem;
            margin-bottom: 2px;
        }

        .info-desc {
            font-size: 0.75rem;
            color: #64748b;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 14px 28px;
            border: none;
            border-radius: 12px;
            font-size: 0.875rem;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s;
        }

        .btn-secondary {
            background: #f1f5f9;
            color: #64748b;
        }

        .btn-secondary:hover {
            background: #e2e8f0;
        }

        .footer-text {
            margin-top: 24px;
            font-size: 0.75rem;
            color: #94a3b8;
        }

        .success-alert {
            background: #dcfce7;
            border: 1px solid #86efac;
            color: #166534;
            padding: 12px 16px;
            border-radius: 12px;
            margin-bottom: 24px;
            font-size: 0.875rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="card">
            <!-- Success Message -->
            @if (session('success'))
                <div class="success-alert">
                    <i class="fas fa-check-circle"></i>
                    {{ session('success') }}
                </div>
            @endif

            <!-- Icon with spinner -->
            <div class="icon-container">
                <div class="spinner-ring"></div>
                <div class="icon-bg">
                    <i class="fas fa-hourglass-half"></i>
                </div>
            </div>

            <!-- Title -->
            <h1>Menunggu Persetujuan</h1>
            <p class="subtitle">
                Pendaftaran Anda berhasil! Akun sedang ditinjau oleh admin atau pembimbing.
            </p>

            <!-- Status Box -->
            <div class="status-box">
                <div class="status-label">Status Akun</div>
                <div class="status-value">
                    <i class="fas fa-clock"></i>
                    Pending Approval
                </div>
            </div>

            <!-- Info List -->
            <div class="info-list">
                <div class="info-item">
                    <div class="info-icon">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <div class="info-text">
                        <div class="info-title">Notifikasi Email</div>
                        <div class="info-desc">Anda akan menerima email ketika akun disetujui</div>
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <div class="info-text">
                        <div class="info-title">Verifikasi Ganda Sudah Aktif</div>
                        <div class="info-desc">Keamanan akun Anda sudah terkonfigurasi</div>
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-icon">
                        <i class="fas fa-user-clock"></i>
                    </div>
                    <div class="info-text">
                        <div class="info-title">Waktu Review</div>
                        <div class="info-desc">Biasanya membutuhkan 1-2 hari kerja</div>
                    </div>
                </div>
            </div>

            <!-- Back to login -->
            <a href="{{ route('login') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i>
                Kembali ke Halaman Login
            </a>

            <p class="footer-text">
                <i class="fas fa-info-circle"></i>
                Jika sudah lama tidak mendapat kabar, hubungi admin melalui email.
            </p>

            <!-- Auto-check status indicator -->
            <div class="auto-check-status" id="autoCheckStatus">
                <i class="fas fa-sync-alt fa-spin"></i>
                <span>Memeriksa status otomatis...</span>
            </div>
        </div>
    </div>

    <!-- Approved Modal -->
    <div class="approved-modal" id="approvedModal">
        <div class="approved-modal-content">
            <div class="approved-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <h2>Selamat! 🎉</h2>
            <p>Hai <strong id="approvedUserName">User</strong>, akun Anda telah disetujui oleh admin.</p>
            <div class="role-badge" id="approvedRoleBadge">
                <i class="fas fa-user-tag"></i>
                <span id="approvedUserRole">User</span>
            </div>
            <p class="redirect-text">Anda akan diarahkan ke <strong>Dashboard</strong> dalam <span
                    id="countdown">60</span> detik...</p>
            <a href="#" id="loginNowBtn" class="btn btn-primary">
                <i class="fas fa-tachometer-alt"></i>
                Masuk Dashboard Sekarang
            </a>
        </div>
    </div>

    <style>
        .auto-check-status {
            margin-top: 24px;
            padding: 12px 16px;
            background: #f1f5f9;
            border-radius: 8px;
            font-size: 0.75rem;
            color: #64748b;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .approved-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(4px);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }

        .approved-modal.show {
            display: flex;
            animation: fadeIn 0.3s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        .approved-modal-content {
            background: white;
            border-radius: 24px;
            padding: 48px;
            text-align: center;
            max-width: 400px;
            margin: 20px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            animation: slideUp 0.4s ease-out;
        }

        @keyframes slideUp {
            from {
                transform: translateY(30px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .approved-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 24px;
            background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 40px;
            color: white;
            box-shadow: 0 16px 32px -8px rgba(34, 197, 94, 0.4);
        }

        .approved-modal-content h2 {
            color: #1e293b;
            font-size: 1.5rem;
            font-weight: 800;
            margin-bottom: 8px;
        }

        .approved-modal-content p {
            color: #64748b;
            font-size: 0.875rem;
            margin-bottom: 8px;
        }

        .role-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: linear-gradient(135deg, #8b5cf6 0%, #a78bfa 100%);
            color: white;
            padding: 10px 20px;
            border-radius: 50px;
            font-size: 0.875rem;
            font-weight: 600;
            margin: 12px 0 8px;
            box-shadow: 0 4px 12px rgba(139, 92, 246, 0.3);
        }

        .role-badge i {
            font-size: 0.875rem;
        }

        .redirect-text {
            background: #f0fdf4;
            padding: 12px 16px;
            border-radius: 8px;
            color: #166534;
            font-weight: 500;
            margin: 16px 0 24px !important;
        }

        .btn-primary {
            background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 16px -4px rgba(34, 197, 94, 0.4);
        }
    </style>

    <script>
        // Auto-check approval status every 5 seconds
        const CHECK_INTERVAL = 5000; // 5 seconds
        let checkCount = 0;
        const MAX_CHECKS = 120; // Check for up to 1 hour (120 * 5 seconds)

        function checkApprovalStatus() {
            checkCount++;

            if (checkCount > MAX_CHECKS) {
                document.getElementById('autoCheckStatus').innerHTML =
                    '<i class="fas fa-pause-circle"></i> <span>Pengecekan otomatis dihentikan. Silakan refresh halaman.</span>';
                return;
            }

            fetch('{{ route('oauth.check-status') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({})
                })
                .then(response => response.json())
                .then(data => {
                    if (data.approved) {
                        // Show approved modal with user info
                        showApprovedModal(data.redirect_url, data.user_name, data.user_role);
                    } else {
                        // Continue checking
                        setTimeout(checkApprovalStatus, CHECK_INTERVAL);
                    }
                })
                .catch(error => {
                    console.error('Error checking status:', error);
                    // Continue checking even on error
                    setTimeout(checkApprovalStatus, CHECK_INTERVAL);
                });
        }

        function showApprovedModal(redirectUrl, userName, userRole) {
            const modal = document.getElementById('approvedModal');
            const loginBtn = document.getElementById('loginNowBtn');
            const userNameEl = document.getElementById('approvedUserName');
            const userRoleEl = document.getElementById('approvedUserRole');

            // Set user info
            if (userName) userNameEl.textContent = userName;
            if (userRole) userRoleEl.textContent = userRole;

            // Set button href to redirect URL
            loginBtn.href = redirectUrl;

            // Show modal
            modal.classList.add('show');

            // Countdown and redirect
            let seconds = 60;
            const countdownEl = document.getElementById('countdown');

            const interval = setInterval(() => {
                seconds--;
                countdownEl.textContent = seconds;

                if (seconds <= 0) {
                    clearInterval(interval);
                    window.location.href = redirectUrl;
                }
            }, 1000);
        }

        // Start checking after page loads
        document.addEventListener('DOMContentLoaded', function() {
            // Start first check after 5 seconds
            setTimeout(checkApprovalStatus, 5000);
        });
    </script>
</body>

</html>
