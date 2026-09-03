<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Pendaftaran - InternHub</title>

    <!-- PWA Meta Tags -->
    <meta name="theme-color" content="#8b5cf6">
    <meta name="description" content="Daftar sebagai Peserta Magang atau Pembimbing di InternHub">
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
        .register-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }

        .register-card {
            width: 100%;
            max-width: 640px;
        }

        .section-divider {
            height: 6px;
            background: linear-gradient(90deg, rgba(139, 92, 246, 0.1) 0%, rgba(192, 132, 252, 0.1) 100%);
        }

        .form-section {
            padding: 1.5rem;
        }

        @media (min-width: 640px) {
            .form-section {
                padding: 2rem;
            }
        }

        .section-icon {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            background: rgba(139, 92, 246, 0.1);
            color: #8b5cf6;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
        }

        .role-selector {
            display: flex;
            gap: 1rem;
        }

        .role-option {
            flex: 1;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            padding: 1rem;
            cursor: pointer;
            transition: all 0.2s;
            text-align: center;
        }

        .role-option:hover {
            border-color: #c4b5fd;
            background: rgba(139, 92, 246, 0.03);
        }

        .role-option.active {
            border-color: #8b5cf6;
            background: rgba(139, 92, 246, 0.05);
        }

        .role-option .icon {
            width: 48px;
            height: 48px;
            margin: 0 auto 0.75rem;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            transition: all 0.2s;
        }

        .role-option.active .icon {
            color: white;
        }

        .role-option[data-role="intern"] .icon {
            background: rgba(34, 197, 94, 0.1);
            color: #22c55e;
        }

        .role-option[data-role="intern"].active .icon {
            background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
        }

        .role-option[data-role="pembimbing"] .icon {
            background: rgba(59, 130, 246, 0.1);
            color: #3b82f6;
        }

        .role-option[data-role="pembimbing"].active .icon {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        }

        .password-toggle {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #94a3b8;
            cursor: pointer;
            padding: 4px;
            z-index: 10;
        }

        .password-toggle:hover {
            color: #64748b;
        }

        .password-wrapper {
            position: relative;
        }

        .password-wrapper input {
            padding-right: 40px;
        }
    </style>
</head>

<body class="register-container">
    <div class="register-card">
        <div class="card p-0 overflow-hidden">
            <!-- Header -->
            <div class="form-section text-center"
                style="background: linear-gradient(135deg, rgba(139,92,246,0.05) 0%, rgba(192,132,252,0.05) 100%);">
                <div class="w-16 h-16 mx-auto mb-4 rounded-2xl flex items-center justify-center text-2xl text-white shadow-lg"
                    style="background: linear-gradient(135deg, #a78bfa 0%, #c084fc 100%); box-shadow: 0 8px 20px -4px rgba(167,139,250,0.5);">
                    <i class="fas fa-user-plus"></i>
                </div>
                <h1 class="text-xl font-extrabold text-slate-800 mb-1 tracking-tight">Pendaftaran</h1>
                <p class="text-slate-400 text-sm">Pilih jenis akun dan isi data lengkap untuk mendaftar</p>
            </div>

            <!-- Success/Error Messages -->
            @if (session('success'))
                <div class="alert alert-success mx-6 mt-4">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-error mx-6 mt-4">
                    <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-error mx-6 mt-4">
                    <i class="fas fa-exclamation-circle"></i> {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}" id="registerForm">
                @csrf

                <!-- Role Selector -->
                <div class="form-section">
                    <div class="flex items-center gap-3 mb-5 pb-4"
                        style="border-bottom: 1px solid rgba(148,163,184,0.1);">
                        <div class="section-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-slate-800 text-base">Daftar Sebagai</h4>
                            <p class="text-sm text-slate-400">Pilih jenis akun yang ingin Anda buat</p>
                        </div>
                    </div>

                    <input type="hidden" name="role" id="roleInput" value="{{ old('role', 'intern') }}">

                    <div class="role-selector">
                        <div class="role-option {{ old('role', 'intern') == 'intern' ? 'active' : '' }}"
                            data-role="intern">
                            <div class="icon">
                                <i class="fas fa-user-graduate"></i>
                            </div>
                            <div class="font-semibold text-slate-700 text-sm">Peserta Magang</div>
                            <div class="text-xs text-slate-400 mt-1">Siswa / Mahasiswa</div>
                        </div>
                        <div class="role-option {{ old('role') == 'pembimbing' ? 'active' : '' }}"
                            data-role="pembimbing">
                            <div class="icon">
                                <i class="fas fa-chalkboard-teacher"></i>
                            </div>
                            <div class="font-semibold text-slate-700 text-sm">Pembimbing</div>
                            <div class="text-xs text-slate-400 mt-1">Mentor / Supervisor</div>
                        </div>
                    </div>
                </div>

                <div class="section-divider"></div>

                <!-- Section: Informasi Akun -->
                <div class="form-section">
                    <div class="flex items-center gap-3 mb-5 pb-4"
                        style="border-bottom: 1px solid rgba(148,163,184,0.1);">
                        <div class="section-icon">
                            <i class="fas fa-user-shield"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-slate-800 text-base">Informasi Akun</h4>
                            <p class="text-sm text-slate-400">Credential untuk login ke sistem</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="form-group mb-0 sm:col-span-2">
                            <label class="form-label">Nama Lengkap <span class="text-rose-500">*</span></label>
                            <div class="search-input">
                                <input type="text" name="name"
                                    class="form-control @error('name') !border-rose-400 @enderror"
                                    value="{{ old('name') }}" placeholder="Masukkan nama lengkap" required>
                                <i class="fas fa-user"></i>
                            </div>
                            @error('name')
                                <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group mb-0 sm:col-span-2">
                            <label class="form-label">Email <span class="text-rose-500">*</span></label>
                            <div class="search-input">
                                <input type="email" name="email"
                                    class="form-control @error('email') !border-rose-400 @enderror"
                                    value="{{ old('email') }}" placeholder="email@example.com" required>
                                <i class="fas fa-envelope"></i>
                            </div>
                            @error('email')
                                <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group mb-0">
                            <label class="form-label">Password <span class="text-rose-500">*</span></label>
                            <div class="password-wrapper">
                                <input type="password" name="password" id="password"
                                    class="form-control @error('password') !border-rose-400 @enderror"
                                    placeholder="Minimal 8 karakter" required>
                                <button type="button" class="password-toggle"
                                    onclick="togglePassword('password', this)">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            @error('password')
                                <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group mb-0">
                            <label class="form-label">Konfirmasi Password <span class="text-rose-500">*</span></label>
                            <div class="password-wrapper">
                                <input type="password" name="password_confirmation" id="password_confirmation"
                                    class="form-control" placeholder="Ulangi password" required>
                                <button type="button" class="password-toggle"
                                    onclick="togglePassword('password_confirmation', this)">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="section-divider"></div>

                <!-- Section: Data Intern (shown when intern selected) -->
                <div id="internFields" class="form-section"
                    style="{{ old('role', 'intern') == 'intern' ? '' : 'display: none;' }}">
                    <div class="flex items-center gap-3 mb-5 pb-4"
                        style="border-bottom: 1px solid rgba(148,163,184,0.1);">
                        <div class="section-icon" style="background: rgba(34,197,94,0.1); color: #22c55e;">
                            <i class="fas fa-id-card"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-slate-800 text-base">Data Peserta Magang</h4>
                            <p class="text-sm text-slate-400">Informasi tentang diri Anda</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="form-group mb-0">
                            <label class="form-label">NISN / NIM</label>
                            <input type="text" name="nis"
                                class="form-control @error('nis') !border-rose-400 @enderror"
                                value="{{ old('nis') }}" placeholder="Nomor induk">
                            @error('nis')
                                <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group mb-0">
                            <label class="form-label">WhatsApp / Telepon</label>
                            <div class="search-input">
                                <input type="text" name="phone"
                                    class="form-control @error('phone') !border-rose-400 @enderror"
                                    value="{{ old('phone') }}" placeholder="08xxxxxxxxxx">
                                <i class="fas fa-phone"></i>
                            </div>
                            @error('phone')
                                <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group mb-0">
                            <label class="form-label">Asal Sekolah / Kampus <span
                                    class="text-rose-500">*</span></label>
                            <div class="search-input">
                                <input type="text" name="school"
                                    class="form-control @error('school') !border-rose-400 @enderror"
                                    value="{{ old('school') }}" placeholder="Nama instansi pendidikan">
                                <i class="fas fa-school"></i>
                            </div>
                            @error('school')
                                <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group mb-0">
                            <label class="form-label">Jurusan / Bidang Studi <span
                                    class="text-rose-500">*</span></label>
                            <div class="search-input">
                                <input type="text" name="department"
                                    class="form-control @error('department') !border-rose-400 @enderror"
                                    value="{{ old('department') }}" placeholder="Contoh: RPL, TKJ, Informatika">
                                <i class="fas fa-graduation-cap"></i>
                            </div>
                            @error('department')
                                <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group mb-0 sm:col-span-2">
                            <label class="form-label">Alamat</label>
                            <textarea name="address" class="form-control @error('address') !border-rose-400 @enderror" rows="2"
                                placeholder="Alamat tempat tinggal saat ini">{{ old('address') }}</textarea>
                            @error('address')
                                <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group mb-0 sm:col-span-2">
                            <label class="form-label">Pembimbing</label>
                            <div class="search-input">
                                <select name="supervisor_id"
                                    class="form-control @error('supervisor_id') !border-rose-400 @enderror">
                                    <option value="">-- Pilih Pembimbing (Opsional) --</option>
                                    @foreach ($supervisors as $supervisor)
                                        <option value="{{ $supervisor->user_id }}"
                                            {{ old('supervisor_id') == $supervisor->user_id ? 'selected' : '' }}>
                                            {{ $supervisor->user->name }} -
                                            {{ $supervisor->institution ?? 'Tidak ada instansi' }}
                                        </option>
                                    @endforeach
                                </select>
                                <i class="fas fa-chalkboard-teacher"></i>
                            </div>
                            @error('supervisor_id')
                                <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <!-- Periode Magang (for intern only) -->
                    <div class="flex items-center gap-3 mb-5 mt-6 pb-4"
                        style="border-bottom: 1px solid rgba(148,163,184,0.1);">
                        <div class="section-icon" style="background: rgba(34,197,94,0.1); color: #22c55e;">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-slate-800 text-base">Periode Magang</h4>
                            <p class="text-sm text-slate-400">Rencana durasi magang Anda</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="form-group mb-0">
                            <label class="form-label">Tanggal Mulai <span class="text-rose-500">*</span></label>
                            <input type="date" name="start_date"
                                class="form-control @error('start_date') !border-rose-400 @enderror"
                                value="{{ old('start_date') }}">
                            @error('start_date')
                                <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group mb-0">
                            <label class="form-label">Tanggal Selesai <span class="text-rose-500">*</span></label>
                            <input type="date" name="end_date"
                                class="form-control @error('end_date') !border-rose-400 @enderror"
                                value="{{ old('end_date') }}">
                            @error('end_date')
                                <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Section: Data Pembimbing (shown when pembimbing selected) -->
                <div id="pembimbingFields" class="form-section"
                    style="{{ old('role') == 'pembimbing' ? '' : 'display: none;' }}">
                    <div class="flex items-center gap-3 mb-5 pb-4"
                        style="border-bottom: 1px solid rgba(148,163,184,0.1);">
                        <div class="section-icon" style="background: rgba(59,130,246,0.1); color: #3b82f6;">
                            <i class="fas fa-user-tie"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-slate-800 text-base">Data Pembimbing</h4>
                            <p class="text-sm text-slate-400">Informasi tentang diri Anda sebagai pembimbing</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="form-group mb-0">
                            <label class="form-label">NIP</label>
                            <div class="search-input">
                                <input type="text" name="nip"
                                    class="form-control @error('nip') !border-rose-400 @enderror"
                                    value="{{ old('nip') }}" placeholder="Nomor Induk Pegawai">
                                <i class="fas fa-id-badge"></i>
                            </div>
                            @error('nip')
                                <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group mb-0">
                            <label class="form-label">WhatsApp / Telepon</label>
                            <div class="search-input">
                                <input type="text" name="supervisor_phone"
                                    class="form-control @error('supervisor_phone') !border-rose-400 @enderror"
                                    value="{{ old('supervisor_phone') }}" placeholder="08xxxxxxxxxx">
                                <i class="fas fa-phone"></i>
                            </div>
                            @error('supervisor_phone')
                                <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group mb-0 sm:col-span-2">
                            <label class="form-label">Asal Instansi <span class="text-rose-500">*</span></label>
                            <div class="search-input">
                                <input type="text" name="institution"
                                    class="form-control @error('institution') !border-rose-400 @enderror"
                                    value="{{ old('institution') }}" placeholder="Nama perusahaan / lembaga">
                                <i class="fas fa-building"></i>
                            </div>
                            @error('institution')
                                <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group mb-0 sm:col-span-2">
                            <label class="form-label">Alamat</label>
                            <textarea name="supervisor_address" class="form-control @error('supervisor_address') !border-rose-400 @enderror"
                                rows="2" placeholder="Alamat tempat tinggal saat ini">{{ old('supervisor_address') }}</textarea>
                            @error('supervisor_address')
                                <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="form-section pt-0">
                    <div class="p-4 rounded-xl mb-5"
                        style="background: rgba(139,92,246,0.05); border: 1px solid rgba(139,92,246,0.1);">
                        <div class="flex gap-3">
                            <i class="fas fa-info-circle text-violet-500 mt-0.5"></i>
                            <p class="text-sm text-slate-600">
                                Setelah mendaftar, akun Anda akan menunggu persetujuan dari admin.
                                Anda akan menerima notifikasi setelah akun diaktifkan.
                            </p>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-full py-3 text-sm">
                        <i class="fas fa-paper-plane"></i> Kirim Pendaftaran
                    </button>

                    <p class="text-center text-sm text-slate-500 mt-5">
                        Sudah punya akun?
                        <a href="{{ route('login') }}" class="text-violet-500 hover:text-violet-600 font-medium">
                            Masuk di sini
                        </a>
                    </p>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Role selector
        document.querySelectorAll('.role-option').forEach(option => {
            option.addEventListener('click', function() {
                document.querySelectorAll('.role-option').forEach(o => o.classList.remove('active'));
                this.classList.add('active');

                const role = this.dataset.role;
                document.getElementById('roleInput').value = role;

                // Toggle field sections
                document.getElementById('internFields').style.display = role === 'intern' ? '' : 'none';
                document.getElementById('pembimbingFields').style.display = role === 'pembimbing' ? '' :
                    'none';

                // Toggle required fields
                const internRequired = document.querySelectorAll(
                    '#internFields input[name="school"], #internFields input[name="department"], #internFields input[name="start_date"], #internFields input[name="end_date"]'
                    );
                const pembimbingRequired = document.querySelectorAll(
                    '#pembimbingFields input[name="institution"]');

                internRequired.forEach(input => input.required = role === 'intern');
                pembimbingRequired.forEach(input => input.required = role === 'pembimbing');
            });
        });

        // Show/hide password toggle
        function togglePassword(inputId, button) {
            const input = document.getElementById(inputId);
            const icon = button.querySelector('i');

            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        // Initialize required fields based on initial role
        document.addEventListener('DOMContentLoaded', function() {
            const role = document.getElementById('roleInput').value;
            const internRequired = document.querySelectorAll(
                '#internFields input[name="school"], #internFields input[name="department"], #internFields input[name="start_date"], #internFields input[name="end_date"]'
                );
            const pembimbingRequired = document.querySelectorAll('#pembimbingFields input[name="institution"]');

            internRequired.forEach(input => input.required = role === 'intern');
            pembimbingRequired.forEach(input => input.required = role === 'pembimbing');
        });
    </script>
</body>

</html>
