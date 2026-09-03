<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Lengkapi Profil - InternHub</title>

    <meta name="theme-color" content="#8b5cf6">
    <meta name="description" content="Lengkapi Profil - InternHub">

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
            max-width: 520px;
        }

        .card {
            background: white;
            border-radius: 24px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.1);
            padding: 40px;
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
            background: linear-gradient(135deg, #3b82f6 0%, #60a5fa 100%);
            box-shadow: 0 12px 24px -6px rgba(59, 130, 246, 0.4);
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
            margin-bottom: 32px;
        }

        .user-card {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
            padding: 14px 16px;
            background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
            border: 2px solid #86efac;
            border-radius: 16px;
            margin-bottom: 28px;
        }

        .user-avatar {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: #10b981;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 18px;
            overflow: hidden;
            flex-shrink: 0;
        }

        .user-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 0;
        }

        .user-info {
            flex: 1;
            min-width: 0;
        }

        .user-name {
            font-weight: 700;
            color: #166534;
            font-size: 0.9rem;
            word-break: break-word;
        }

        .user-email {
            font-size: 0.75rem;
            color: #15803d;
            word-break: break-all;
        }

        .verified-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            background: #10b981;
            color: white;
            padding: 4px 8px;
            border-radius: 20px;
            font-size: 0.65rem;
            font-weight: 600;
            white-space: nowrap;
            flex-shrink: 0;
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

        .form-label .required {
            color: #ef4444;
        }

        .form-control {
            width: 100%;
            padding: 14px 16px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-size: 0.9rem;
            transition: all 0.2s;
            font-family: inherit;
        }

        .form-control:focus {
            outline: none;
            border-color: #8b5cf6;
            box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.1);
        }

        .form-control.error {
            border-color: #ef4444;
        }

        select.form-control {
            cursor: pointer;
            background: white;
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%2364748b' d='M6 8L1 3h10z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 16px center;
            padding-right: 40px;
        }

        select.form-control:focus {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%238b5cf6' d='M6 8L1 3h10z'/%3E%3C/svg%3E");
        }

        .error-msg {
            color: #ef4444;
            font-size: 0.8rem;
            margin-top: 6px;
        }

        .role-selector {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-bottom: 24px;
        }

        .role-option {
            position: relative;
            cursor: pointer;
        }

        .role-option input {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }

        .role-card {
            padding: 20px;
            border: 2px solid #e2e8f0;
            border-radius: 16px;
            text-align: center;
            transition: all 0.2s;
        }

        .role-option input:checked+.role-card {
            border-color: #8b5cf6;
            background: linear-gradient(135deg, #faf5ff 0%, #f3e8ff 100%);
        }

        .role-icon {
            width: 48px;
            height: 48px;
            margin: 0 auto 12px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            color: white;
        }

        .role-intern .role-icon {
            background: linear-gradient(135deg, #10b981 0%, #34d399 100%);
        }

        .role-pembimbing .role-icon {
            background: linear-gradient(135deg, #f59e0b 0%, #fbbf24 100%);
        }

        .role-title {
            font-weight: 700;
            color: #1e293b;
            font-size: 0.9rem;
            margin-bottom: 4px;
        }

        .role-desc {
            font-size: 0.75rem;
            color: #64748b;
        }

        .section-title {
            font-size: 0.8rem;
            font-weight: 700;
            color: #8b5cf6;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 16px;
            padding-bottom: 8px;
            border-bottom: 2px solid #f1f5f9;
        }

        .date-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }


        .btn {
            width: 100%;
            padding: 16px 24px;
            border: none;
            border-radius: 12px;
            font-size: 0.9rem;
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

        .help-text {
            text-align: center;
            font-size: 0.75rem;
            color: #94a3b8;
            margin-top: 20px;
        }

        .alert-error {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #dc2626;
            padding: 12px 16px;
            border-radius: 12px;
            margin-bottom: 20px;
            font-size: 0.875rem;
        }

        /* ==================== PEMBIMBING FORM STYLES ==================== */

        .pembimbing-header {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 20px;
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            border-radius: 16px;
            margin-bottom: 24px;
            border: 1px solid #e2e8f0;
        }

        .pembimbing-icon {
            width: 56px;
            height: 56px;
            border-radius: 14px;
            background: linear-gradient(135deg, #6366f1 0%, #818cf8 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
            flex-shrink: 0;
        }

        .pembimbing-avatar {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            overflow: hidden;
            flex-shrink: 0;
            border: 3px solid #818cf8;
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
        }

        .pembimbing-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .pembimbing-header-text h3 {
            font-size: 1.1rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 4px;
        }

        .pembimbing-header-text p {
            font-size: 0.8rem;
            color: #64748b;
            margin: 0;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        .input-with-icon {
            position: relative;
        }

        .input-with-icon i {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 16px;
            pointer-events: none;
            z-index: 1;
        }

        .input-with-icon .form-control {
            padding-left: 48px;
        }

        /* ==================== RESPONSIVE STYLES ==================== */

        /* Large screens (desktop) */
        @media (min-width: 768px) {
            .card {
                padding: 48px;
            }

            .icon-header {
                width: 80px;
                height: 80px;
                font-size: 36px;
            }

            h1 {
                font-size: 1.75rem;
            }
        }

        /* Medium screens (tablets) */
        @media (max-width: 768px) {
            body {
                padding: 16px;
            }

            .card {
                padding: 32px 24px;
                border-radius: 20px;
            }

            .icon-header {
                width: 64px;
                height: 64px;
                font-size: 28px;
                margin-bottom: 20px;
            }

            h1 {
                font-size: 1.35rem;
            }

            .subtitle {
                font-size: 0.825rem;
                margin-bottom: 24px;
            }

            .role-selector {
                gap: 10px;
            }

            .role-card {
                padding: 16px 12px;
            }

            .role-icon {
                width: 42px;
                height: 42px;
                font-size: 18px;
            }

            .role-title {
                font-size: 0.85rem;
            }

            .role-desc {
                font-size: 0.7rem;
            }
        }

        /* Small screens (mobile) */
        @media (max-width: 480px) {
            body {
                padding: 12px;
                align-items: flex-start;
                padding-top: 20px;
            }

            .container {
                max-width: 100%;
            }

            .card {
                padding: 24px 18px;
                border-radius: 16px;
                box-shadow: 0 15px 35px -10px rgba(0, 0, 0, 0.08);
            }

            .icon-header {
                width: 56px;
                height: 56px;
                font-size: 24px;
                border-radius: 16px;
                margin-bottom: 16px;
            }

            h1 {
                font-size: 1.2rem;
                margin-bottom: 6px;
            }

            .subtitle {
                font-size: 0.8rem;
                margin-bottom: 20px;
            }

            .user-card {
                padding: 12px;
                border-radius: 12px;
                margin-bottom: 20px;
                gap: 10px;
            }

            .user-avatar {
                width: 38px;
                height: 38px;
                font-size: 14px;
            }

            .user-name {
                font-size: 0.8rem;
            }

            .user-email {
                font-size: 0.68rem;
            }

            .verified-badge {
                font-size: 0.58rem;
                padding: 3px 6px;
            }

            .section-title {
                font-size: 0.7rem;
                margin-bottom: 12px;
                padding-bottom: 6px;
            }

            .role-selector {
                grid-template-columns: 1fr 1fr;
                gap: 8px;
                margin-bottom: 20px;
            }

            .role-card {
                padding: 14px 8px;
                border-radius: 12px;
            }

            .role-icon {
                width: 36px;
                height: 36px;
                font-size: 16px;
                margin-bottom: 8px;
                border-radius: 10px;
            }

            .role-title {
                font-size: 0.75rem;
            }

            .role-desc {
                font-size: 0.65rem;
            }

            .form-group {
                margin-bottom: 16px;
            }

            .form-label {
                font-size: 0.8rem;
                margin-bottom: 6px;
            }

            .form-control {
                padding: 12px 14px;
                font-size: 0.85rem;
                border-radius: 10px;
            }

            select.form-control {
                padding-right: 36px;
                background-position: right 12px center;
            }

            .date-grid {
                grid-template-columns: 1fr;
                gap: 0;
            }

            .btn {
                padding: 14px 20px;
                font-size: 0.85rem;
                border-radius: 10px;
            }

            .help-text {
                font-size: 0.7rem;
                margin-top: 16px;
            }

            .help-note {
                font-size: 0.68rem !important;
            }

            .alert-error {
                padding: 10px 12px;
                font-size: 0.8rem;
                border-radius: 10px;
            }

            .error-msg {
                font-size: 0.72rem;
            }

            /* Pembimbing form responsive */
            .pembimbing-header {
                padding: 14px;
                gap: 12px;
                border-radius: 12px;
                margin-bottom: 20px;
            }

            .pembimbing-icon {
                width: 44px;
                height: 44px;
                font-size: 18px;
                border-radius: 10px;
            }

            .pembimbing-avatar {
                width: 44px;
                height: 44px;
                border-width: 2px;
            }

            .pembimbing-header-text h3 {
                font-size: 0.95rem;
            }

            .pembimbing-header-text p {
                font-size: 0.72rem;
            }

            .form-row {
                grid-template-columns: 1fr;
                gap: 0;
            }

            .input-with-icon i {
                left: 12px;
                font-size: 14px;
            }

            .input-with-icon .form-control {
                padding-left: 40px;
            }
        }

        /* Extra small screens */
        @media (max-width: 360px) {
            body {
                padding: 8px;
                padding-top: 12px;
            }

            .card {
                padding: 20px 14px;
                border-radius: 14px;
            }

            .icon-header {
                width: 48px;
                height: 48px;
                font-size: 20px;
                border-radius: 14px;
                margin-bottom: 12px;
            }

            h1 {
                font-size: 1.1rem;
            }

            .subtitle {
                font-size: 0.75rem;
                margin-bottom: 16px;
            }

            .user-card {
                flex-direction: column;
                text-align: center;
                padding: 14px 12px;
            }

            .user-avatar {
                width: 48px;
                height: 48px;
                font-size: 18px;
            }

            .user-info {
                text-align: center;
            }

            .user-name {
                font-size: 0.85rem;
            }

            .user-email {
                font-size: 0.72rem;
            }

            .role-selector {
                grid-template-columns: 1fr;
                gap: 10px;
            }

            .role-card {
                display: flex;
                align-items: center;
                gap: 12px;
                text-align: left;
                padding: 14px;
            }

            .role-icon {
                margin: 0;
                flex-shrink: 0;
            }

            .role-content {
                flex: 1;
            }

            .form-control {
                padding: 11px 12px;
                font-size: 0.82rem;
            }

            .btn {
                padding: 13px 18px;
                font-size: 0.82rem;
            }

            /* Pembimbing form extra small */
            .pembimbing-header {
                flex-direction: column;
                text-align: center;
                padding: 16px 12px;
            }

            .pembimbing-icon {
                width: 48px;
                height: 48px;
                font-size: 20px;
            }

            .pembimbing-avatar {
                width: 48px;
                height: 48px;
            }

            .pembimbing-header-text h3 {
                font-size: 0.9rem;
            }

            .pembimbing-header-text p {
                font-size: 0.7rem;
            }
        }

        /* Landscape orientation on mobile */
        @media (max-height: 500px) and (orientation: landscape) {
            body {
                align-items: flex-start;
                padding: 10px;
            }

            .card {
                padding: 20px;
            }

            .icon-header {
                width: 50px;
                height: 50px;
                font-size: 22px;
                margin-bottom: 12px;
            }

            h1 {
                font-size: 1.15rem;
                margin-bottom: 4px;
            }

            .subtitle {
                margin-bottom: 16px;
            }

            .role-selector {
                margin-bottom: 16px;
            }

            .form-group {
                margin-bottom: 12px;
            }
        }

        /* Touch device optimizations */
        @media (hover: none) and (pointer: coarse) {
            .form-control {
                min-height: 48px;
            }

            .btn {
                min-height: 52px;
            }

            .role-card {
                min-height: 80px;
            }

            select.form-control {
                min-height: 48px;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="card">
            <!-- Header -->
            <div class="icon-header">
                <i class="fas fa-user-edit"></i>
            </div>
            <h1>Lengkapi Profil Anda</h1>
            <p class="subtitle">Isi informasi berikut untuk melanjutkan pendaftaran</p>

            <!-- User Card (from Google) -->
            <div class="user-card">
                @if ($user->avatar)
                    <div class="user-avatar">
                        <img src="{{ $user->avatar }}" alt="{{ $user->name }}" referrerpolicy="no-referrer"
                            onerror="this.parentElement.innerHTML='<i class=\'fas fa-user\'></i>'">
                    </div>
                @else
                    <div class="user-avatar"><i class="fas fa-user"></i></div>
                @endif
                <div class="user-info">
                    <div class="user-name">{{ $user->name }}</div>
                    <div class="user-email">{{ $user->email }}</div>
                </div>
                <div class="verified-badge">
                    <i class="fas fa-check-circle"></i> Aktif Account
                </div>
            </div>

            <!-- Error Messages -->
            @if ($errors->any())
                <div class="alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    {{ $errors->first() }}
                </div>
            @endif

            <!-- Form -->
            <form method="POST" action="{{ route('oauth.complete-profile.submit') }}">
                @csrf

                <!-- Role Selection -->
                <div class="section-title"><i class="fas fa-user-tag"></i> Daftar Sebagai</div>
                <div class="role-selector">
                    <label class="role-option role-intern">
                        <input type="radio" name="role" value="intern"
                            {{ old('role', 'intern') == 'intern' ? 'checked' : '' }} required>
                        <div class="role-card">
                            <div class="role-icon"><i class="fas fa-user-graduate"></i></div>
                            <div class="role-title">Peserta Magang</div>
                            <div class="role-desc">Siswa / Mahasiswa</div>
                        </div>
                    </label>
                    <label class="role-option role-pembimbing">
                        <input type="radio" name="role" value="pembimbing"
                            {{ old('role') == 'pembimbing' ? 'checked' : '' }}>
                        <div class="role-card">
                            <div class="role-icon"><i class="fas fa-chalkboard-teacher"></i></div>
                            <div class="role-title">Pembimbing</div>
                            <div class="role-desc">Supervisor / Mentor</div>
                        </div>
                    </label>
                </div>

                <!-- Personal Information -->
                <div class="section-title"><i class="fas fa-info-circle"></i> Informasi Pribadi</div>

                <div class="form-group">
                    <label class="form-label">Nama Lengkap <span class="required">*</span></label>
                    <input type="text" name="name" class="form-control @error('name') error @enderror"
                        value="{{ old('name', $user->name) }}" required>
                    @error('name')
                        <p class="error-msg">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">No. Telepon <span class="required">*</span></label>
                    <input type="tel" name="phone" class="form-control @error('phone') error @enderror"
                        value="{{ old('phone') }}" placeholder="08xxxxxxxxxx" required>
                    @error('phone')
                        <p class="error-msg">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Intern Fields -->
                <!-- Intern Fields -->
                <div id="intern-fields" class="role-fields">
                    <div class="section-title">Informasi Magang</div>

                    <div class="form-group">
                        <label class="form-label">Nomor Induk (NISN/NIM) <span class="required">*</span></label>
                        <input type="text" name="nis" class="form-control" placeholder="Contoh: 1234567890"
                            value="{{ old('nis') }}">
                        @error('nis')
                            <p class="error-msg">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Asal Sekolah / Universitas <span class="required">*</span></label>
                        <input type="text" name="school" class="form-control"
                            placeholder="Contoh: SMKN 1 Jakarta / Universitas Indonesia" value="{{ old('school') }}">
                        @error('school')
                            <p class="error-msg">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Jurusan / Program Studi <span class="required">*</span></label>
                        <input type="text" name="department" class="form-control"
                            placeholder="Contoh: Teknik Informatika" value="{{ old('department') }}">
                        @error('department')
                            <p class="error-msg">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Alamat Lengkap <span class="required">*</span></label>
                        <textarea name="address" class="form-control" rows="3" placeholder="Masukkan alamat lengkap Anda">{{ old('address') }}</textarea>
                        @error('address')
                            <p class="error-msg">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="section-title"><i class="fas fa-calendar-alt"></i> Periode Magang</div>

                    <div class="date-grid">
                        <div class="form-group">
                            <label class="form-label">Tanggal Mulai <span class="required">*</span></label>
                            <input type="date" name="start_date" class="form-control"
                                value="{{ old('start_date', date('Y-m-d')) }}">
                            @error('start_date')
                                <p class="error-msg">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">Tanggal Selesai <span class="required">*</span></label>
                            <input type="date" name="end_date" class="form-control"
                                value="{{ old('end_date', date('Y-m-d', strtotime('+3 months'))) }}">
                            @error('end_date')
                                <p class="error-msg">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Supervisor Selection -->
                    <div class="section-title"><i class="fas fa-user-tie"></i> Pilih Pembimbing</div>

                    <div class="form-group">
                        <label class="form-label">Pembimbing Magang</label>
                        <select name="supervisor_id" class="form-control @error('supervisor_id') error @enderror">
                            <option value="">-- Pilih Pembimbing (Opsional) --</option>
                            @forelse($supervisors as $supervisor)
                                <option value="{{ $supervisor->id }}"
                                    {{ old('supervisor_id') == $supervisor->id ? 'selected' : '' }}>
                                    {{ $supervisor->name }}
                                    @if ($supervisor->supervisor)
                                        -
                                        {{ $supervisor->supervisor->department ?? ($supervisor->supervisor->position ?? '') }}
                                    @endif
                                </option>
                            @empty
                                <option value="" disabled>Belum ada pembimbing tersedia</option>
                            @endforelse
                        </select>
                        <p class="help-note" style="font-size: 0.75rem; color: #64748b; margin-top: 6px;">
                            <i class="fas fa-info-circle"></i> Pembimbing dapat diubah oleh admin setelah pendaftaran
                            disetujui
                        </p>
                        @error('supervisor_id')
                            <p class="error-msg">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Pembimbing Fields -->
                <div id="pembimbing-fields" style="display: none;">
                    <!-- Header Section -->
                    <div class="pembimbing-header">
                        @if ($user->avatar)
                            <div class="pembimbing-avatar">
                                <img src="{{ $user->avatar }}" alt="{{ $user->name }}"
                                    referrerpolicy="no-referrer"
                                    onerror="this.parentElement.innerHTML='<i class=\'fas fa-user-tie\'></i>'">
                            </div>
                        @else
                            <div class="pembimbing-icon">
                                <i class="fas fa-user-tie"></i>
                            </div>
                        @endif
                        <div class="pembimbing-header-text">
                            <h3>Data Pembimbing</h3>
                            <p>Informasi tentang diri Anda sebagai pembimbing</p>
                        </div>
                    </div>

                    <!-- NIP & WhatsApp Row -->
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">NIP</label>
                            <div class="input-with-icon">
                                <i class="fas fa-id-badge"></i>
                                <input type="text" name="nip"
                                    class="form-control @error('nip') error @enderror" value="{{ old('nip') }}"
                                    placeholder="Nomor Induk Pegawai">
                            </div>
                            @error('nip')
                                <p class="error-msg">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- <div class="form-group">
                            <label class="form-label">WHATSAPP / TELEPON</label>
                            <div class="input-with-icon">
                                <i class="fas fa-phone"></i>
                                <input type="tel" name="pembimbing_phone"
                                    class="form-control @error('pembimbing_phone') error @enderror"
                                    value="{{ old('pembimbing_phone') }}" placeholder="08xxxxxxxxxxx">
                            </div>
                            @error('pembimbing_phone')
                                <p class="error-msg">{{ $message }}</p>
                            @enderror
                        </div> --}}
                    </div>

                    <!-- Asal Instansi -->
                    <div class="form-group">
                        <label class="form-label">ASAL INSTANSI <span class="required">*</span></label>
                        <div class="input-with-icon">
                            <i class="fas fa-building"></i>
                            <input type="text" name="institution"
                                class="form-control @error('institution') error @enderror"
                                value="{{ old('institution') }}" placeholder="Nama perusahaan / lembaga">
                        </div>
                        @error('institution')
                            <p class="error-msg">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Alamat -->
                    <div class="form-group">
                        <label class="form-label">ALAMAT</label>
                        <textarea name="pembimbing_address" class="form-control @error('pembimbing_address') error @enderror" rows="3"
                            placeholder="Alamat tempat tinggal saat ini">{{ old('pembimbing_address') }}</textarea>
                        @error('pembimbing_address')
                            <p class="error-msg">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-paper-plane"></i> Kirim Permohonan
                </button>
            </form>

            <p class="help-text">
                <i class="fas fa-info-circle"></i>
                Permohonan Anda akan ditinjau oleh admin dalam 1-2 hari kerja
            </p>
        </div>
    </div>

    <script>
        // Toggle fields based on role selection
        const roleInputs = document.querySelectorAll('input[name="role"]');
        const internFields = document.getElementById('intern-fields');
        const pembimbingFields = document.getElementById('pembimbing-fields');

        function toggleFields() {
            const selectedRole = document.querySelector('input[name="role"]:checked')?.value;
            if (selectedRole === 'intern') {
                internFields.style.display = 'block';
                pembimbingFields.style.display = 'none';
            } else if (selectedRole === 'pembimbing') {
                internFields.style.display = 'none';
                pembimbingFields.style.display = 'block';
            }
        }

        roleInputs.forEach(input => {
            input.addEventListener('change', toggleFields);
        });

        // Initialize on load
        toggleFields();
    </script>
</body>

</html>
