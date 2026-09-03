@extends('layouts.app')

@section('title', 'Edit Profil')

@section('content')
    <div class="slide-up">
        <div class="d-flex align-center gap-4 mb-6">
            <a href="{{ route('profile.show') }}" class="btn btn-secondary btn-icon">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h2 style="margin-bottom: 4px;">Edit Profil</h2>
                <p class="text-muted">Perbarui informasi akun Anda</p>
            </div>
        </div>

        <div class="grid-2">
            <!-- Edit Profile Form -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-user-edit"></i> Informasi Profil</h3>
                </div>
                <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <!-- Avatar Upload Section -->
                    <div class="form-group" style="margin-bottom: 24px;">
                        <label class="form-label">Foto Profil</label>
                        <div style="display: flex; align-items: center; gap: 20px;">
                            <!-- Current Avatar Preview -->
                            <div id="avatar-preview-container" style="position: relative;">
                                @if ($user->avatar)
                                    <img id="avatar-preview"
                                        src="{{ Str::startsWith($user->avatar, ['http', 'https']) ? $user->avatar : asset('storage/avatars/' . $user->avatar) }}"
                                        referrerpolicy="no-referrer" alt="Avatar"
                                        style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover; border: 3px solid var(--accent-primary);">
                                @else
                                    <div id="avatar-placeholder"
                                        style="width: 100px; height: 100px; border-radius: 50%; background: var(--accent-gradient); display: flex; align-items: center; justify-content: center; font-size: 36px; font-weight: 700; color: white; border: 3px solid var(--accent-primary);">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <img id="avatar-preview" src="" alt="Preview"
                                        style="display: none; width: 100px; height: 100px; border-radius: 50%; object-fit: cover; border: 3px solid var(--accent-primary);">
                                @endif
                            </div>

                            <!-- Upload Controls -->
                            <div style="flex: 1;">
                                <label for="avatar-input" class="btn btn-secondary"
                                    style="cursor: pointer; margin-bottom: 8px;">
                                    <i class="fas fa-camera"></i> Pilih Foto
                                </label>
                                <input type="file" id="avatar-input" name="avatar" accept="image/*"
                                    style="display: none;">
                                <p class="text-muted" style="font-size: 12px; margin: 0;">
                                    Format: JPG, PNG, GIF. Maksimal 2MB.<br>
                                    Rekomendasi: Foto persegi 200x200 piksel.
                                </p>
                                @if ($user->avatar)
                                    <p style="color: var(--success); font-size: 12px; margin-top: 4px;">
                                        <i class="fas fa-check-circle"></i> Foto profil sudah diupload
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Nama Lengkap *</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}"
                            required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Email *</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}"
                            required>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan Perubahan
                    </button>
                </form>
            </div>

            <!-- Change Password Form -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-lock"></i> Ubah Password</h3>
                </div>
                <form action="{{ route('profile.password') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label class="form-label">Password Saat Ini *</label>
                        <input type="password" name="current_password" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Password Baru *</label>
                        <input type="password" name="password" class="form-control" required>
                        <small class="text-muted">Minimal 8 karakter</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Konfirmasi Password Baru *</label>
                        <input type="password" name="password_confirmation" class="form-control" required>
                    </div>

                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-key"></i> Ubah Password
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Avatar Preview
        document.getElementById('avatar-input').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                // Validate file size (max 2MB)
                if (file.size > 2 * 1024 * 1024) {
                    Swal.fire({
                        icon: 'error',
                        title: 'File Terlalu Besar',
                        text: 'Ukuran file maksimal 2MB',
                        confirmButtonColor: '#ef4444'
                    });
                    e.target.value = '';
                    return;
                }

                // Validate file type
                if (!file.type.match('image.*')) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Format Tidak Valid',
                        text: 'Pilih file gambar (JPG, PNG, GIF)',
                        confirmButtonColor: '#ef4444'
                    });
                    e.target.value = '';
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('avatar-preview');
                    const placeholder = document.getElementById('avatar-placeholder');

                    preview.src = e.target.result;
                    preview.style.display = 'block';

                    if (placeholder) {
                        placeholder.style.display = 'none';
                    }
                };
                reader.readAsDataURL(file);
            }
        });
    </script>
@endpush
