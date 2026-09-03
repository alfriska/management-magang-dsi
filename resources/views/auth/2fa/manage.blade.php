@extends('layouts.app')

@section('title', 'Kelola 2FA')

@section('content')
    <div class="container-fluid py-4">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <!-- Header -->
                <div class="mb-4">
                    <h1 class="text-2xl font-bold text-slate-800">Keamanan Akun</h1>
                    <p class="text-slate-500">Kelola pengaturan Two-Factor Authentication</p>
                </div>

                <!-- 2FA Status Card -->
                <div class="card p-6 mb-4">
                    <div class="flex items-center gap-4 mb-6">
                        <div
                            class="w-14 h-14 rounded-2xl flex items-center justify-center text-2xl text-white shadow-lg
                        {{ $user->google2fa_enabled ? 'bg-gradient-to-br from-green-500 to-emerald-500' : 'bg-gradient-to-br from-red-500 to-rose-500' }}">
                            <i class="fas {{ $user->google2fa_enabled ? 'fa-shield-check' : 'fa-shield-xmark' }}"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-lg text-slate-800">Two-Factor Authentication</h3>
                            <p class="text-sm {{ $user->google2fa_enabled ? 'text-green-600' : 'text-red-600' }}">
                                <i class="fas {{ $user->google2fa_enabled ? 'fa-check-circle' : 'fa-times-circle' }}"></i>
                                {{ $user->google2fa_enabled ? 'Aktif' : 'Tidak Aktif' }}
                            </p>
                        </div>
                    </div>

                    @if ($user->google2fa_enabled)
                        <div class="bg-green-50 border border-green-200 rounded-xl p-4 mb-6">
                            <p class="text-green-800 text-sm">
                                <i class="fas fa-info-circle mr-2"></i>
                                Akun Anda dilindungi dengan Two-Factor Authentication. Setiap login akan memerlukan kode
                                dari Google Authenticator.
                            </p>
                        </div>

                        <!-- Actions -->
                        <div class="space-y-3">
                            <a href="{{ route('2fa.regenerate') }}"
                                class="btn btn-secondary w-full flex items-center justify-center gap-2">
                                <i class="fas fa-refresh"></i> Regenerate QR Code
                            </a>

                            <button type="button"
                                onclick="document.getElementById('disableModal').classList.remove('hidden')"
                                class="btn w-full flex items-center justify-center gap-2"
                                style="background: linear-gradient(135deg, #ef4444 0%, #f87171 100%); color: white;">
                                <i class="fas fa-power-off"></i> Nonaktifkan Verifikasi Ganda
                            </button>
                        </div>
                    @else
                        <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 mb-6">
                            <p class="text-amber-800 text-sm">
                                <i class="fas fa-exclamation-triangle mr-2"></i>
                                Two-Factor Authentication belum diaktifkan. Aktifkan untuk keamanan akun yang lebih baik.
                            </p>
                        </div>

                        <a href="{{ route('2fa.regenerate') }}"
                            class="btn btn-primary w-full flex items-center justify-center gap-2">
                            <i class="fas fa-shield-alt"></i> Aktifkan Verifikasi Ganda
                        </a>
                    @endif
                </div>

                <!-- Back Button -->
                <a href="{{ route('profile.show') }}" class="btn btn-secondary flex items-center justify-center gap-2">
                    <i class="fas fa-arrow-left"></i> Kembali ke Profil
                </a>
            </div>
        </div>
    </div>

    <!-- Disable 2FA Modal -->
    <div id="disableModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4"
        style="background: rgba(0,0,0,0.5);">
        <div class="bg-white rounded-2xl shadow-xl max-w-md w-full p-6">
            <div class="text-center mb-6">
                <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-red-100 flex items-center justify-center">
                    <i class="fas fa-exclamation-triangle text-3xl text-red-500"></i>
                </div>
                <h3 class="text-xl font-bold text-slate-800 mb-2">Nonaktifkan Verifikasi Ganda</h3>
                <p class="text-slate-500 text-sm">Ini akan mengurangi keamanan akun Anda</p>
            </div>

            <form method="POST" action="{{ route('2fa.disable') }}">
                @csrf
                @method('DELETE')

                <div class="form-group">
                    <label class="form-label">Kode Authenticator</label>
                    <input type="text" name="one_time_password" class="form-control text-center" placeholder="000000"
                        maxlength="6" pattern="\d{6}" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Password (opsional untuk OAuth users)</label>
                    <input type="password" name="password" class="form-control" placeholder="Masukkan password">
                </div>

                <div class="flex gap-3">
                    <button type="button" onclick="document.getElementById('disableModal').classList.add('hidden')"
                        class="btn btn-secondary flex-1">
                        Batal
                    </button>
                    <button type="submit" class="btn flex-1"
                        style="background: linear-gradient(135deg, #ef4444 0%, #f87171 100%); color: white;">
                        Nonaktifkan
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
