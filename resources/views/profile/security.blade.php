@extends('layouts.app')

@section('title', 'Keamanan Akun')

@section('content')
    <div class="container-fluid py-4">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <!-- Header -->
                <div class="mb-4">
                    <h1 class="text-2xl font-bold text-slate-800">Keamanan Akun</h1>
                    <p class="text-slate-500">Kelola Two-Factor Authentication untuk keamanan akun</p>
                </div>

                <!-- 2FA Status Card -->
                <div class="card p-6 mb-4">
                    <div class="flex items-center gap-4 mb-6">
                        @if (auth()->user()->two_factor_confirmed_at)
                            <div
                                class="w-14 h-14 rounded-2xl flex items-center justify-center text-2xl text-white shadow-lg bg-gradient-to-br from-green-500 to-emerald-500">
                                <i class="fas fa-shield-check"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-lg text-slate-800">Two-Factor Authentication</h3>
                                <p class="text-sm text-green-600">
                                    <i class="fas fa-check-circle"></i> Aktif dan Terkonfirmasi
                                </p>
                            </div>
                        @elseif(auth()->user()->two_factor_secret)
                            <div
                                class="w-14 h-14 rounded-2xl flex items-center justify-center text-2xl text-white shadow-lg bg-gradient-to-br from-amber-500 to-yellow-500">
                                <i class="fas fa-shield-halved"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-lg text-slate-800">Two-Factor Authentication</h3>
                                <p class="text-sm text-amber-600">
                                    <i class="fas fa-exclamation-circle"></i> Belum Dikonfirmasi
                                </p>
                            </div>
                        @else
                            <div
                                class="w-14 h-14 rounded-2xl flex items-center justify-center text-2xl text-white shadow-lg bg-gradient-to-br from-red-500 to-rose-500">
                                <i class="fas fa-shield-xmark"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-lg text-slate-800">Two-Factor Authentication</h3>
                                <p class="text-sm text-red-600">
                                    <i class="fas fa-times-circle"></i> Tidak Aktif
                                </p>
                            </div>
                        @endif
                    </div>

                    @if (auth()->user()->two_factor_confirmed_at)
                        <!-- 2FA is enabled and confirmed -->
                        <div class="bg-green-50 border border-green-200 rounded-xl p-4 mb-6">
                            <p class="text-green-800 text-sm">
                                <i class="fas fa-info-circle mr-2"></i>
                                Akun Anda dilindungi dengan Two-Factor Authentication. Setiap login akan memerlukan kode
                                dari Google Authenticator.
                            </p>
                        </div>

                        <!-- Recovery Codes -->
                        <div class="mb-6">
                            <h4 class="font-semibold text-slate-700 mb-3">Kode Recovery</h4>
                            <p class="text-sm text-slate-500 mb-3">
                                Simpan kode-kode ini di tempat yang aman. Gunakan jika Anda kehilangan akses ke aplikasi
                                authenticator.
                            </p>
                            <div class="bg-slate-50 rounded-lg p-4 font-mono text-sm grid grid-cols-2 gap-2"
                                id="recoveryCodes">
                                <p class="text-slate-400 text-center col-span-2">Klik tombol di bawah untuk melihat kode
                                    recovery</p>
                            </div>
                            <button type="button" onclick="showRecoveryCodes()" class="btn btn-secondary mt-3">
                                <i class="fas fa-eye"></i> Lihat Kode Recovery
                            </button>
                        </div>

                        <!-- Actions -->
                        <div class="flex flex-wrap gap-3">
                            <form method="POST" action="{{ route('two-factor.regenerate-recovery-codes') }}">
                                @csrf
                                <button type="submit" class="btn btn-secondary">
                                    <i class="fas fa-refresh"></i> Regenerate Kode Recovery
                                </button>
                            </form>

                            <form method="POST" action="{{ route('two-factor.disable') }}"
                                onsubmit="return confirm('Yakin ingin menonaktifkan 2FA? Ini akan mengurangi keamanan akun Anda.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn"
                                    style="background: linear-gradient(135deg, #ef4444 0%, #f87171 100%); color: white;">
                                    <i class="fas fa-power-off"></i> Nonaktifkan 2FA
                                </button>
                            </form>
                        </div>
                    @elseif(auth()->user()->two_factor_secret)
                        <!-- 2FA enabled but not confirmed yet -->
                        <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 mb-6">
                            <p class="text-amber-800 text-sm">
                                <i class="fas fa-exclamation-triangle mr-2"></i>
                                2FA sudah diaktifkan tapi belum dikonfirmasi. Scan QR Code dan masukkan kode untuk
                                mengkonfirmasi.
                            </p>
                        </div>

                        <!-- QR Code -->
                        <div class="text-center mb-6">
                            <h4 class="font-semibold text-slate-700 mb-3">Scan QR Code</h4>
                            <div class="inline-block bg-white p-4 rounded-xl shadow-sm border" id="qrCodeContainer">
                                <img src="{{ route('two-factor.qr-code') }}" alt="QR Code" class="mx-auto"
                                    style="width: 200px; height: 200px;">
                            </div>
                            <p class="text-sm text-slate-500 mt-3">Scan dengan Google Authenticator atau app sejenis</p>
                        </div>

                        <!-- Confirm Form -->
                        <form method="POST" action="{{ route('two-factor.confirm') }}" class="max-w-sm mx-auto">
                            @csrf

                            <div class="form-group">
                                <label class="form-label text-center block">Masukkan Kode Verifikasi</label>
                                <input type="text" name="code"
                                    class="form-control text-center @error('code') border-red-500 @enderror"
                                    style="font-size: 24px; letter-spacing: 8px; font-weight: 600;" placeholder="000000"
                                    maxlength="6" pattern="\d{6}" inputmode="numeric" required autofocus>
                                @error('code')
                                    <p class="text-red-500 text-sm mt-2 text-center">{{ $message }}</p>
                                @enderror
                            </div>

                            <button type="submit" class="btn btn-primary w-full">
                                <i class="fas fa-check-circle"></i> Konfirmasi 2FA
                            </button>
                        </form>

                        <div class="text-center mt-4">
                            <form method="POST" action="{{ route('two-factor.disable') }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-sm text-red-500 hover:text-red-600">
                                    <i class="fas fa-times"></i> Batalkan Setup 2FA
                                </button>
                            </form>
                        </div>
                    @else
                        <!-- 2FA not enabled -->
                        <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 mb-6">
                            <p class="text-amber-800 text-sm">
                                <i class="fas fa-exclamation-triangle mr-2"></i>
                                Two-Factor Authentication belum diaktifkan. Aktifkan untuk keamanan akun yang lebih baik.
                            </p>
                        </div>

                        <form method="POST" action="{{ route('two-factor.enable') }}">
                            @csrf
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-shield-alt"></i> Aktifkan 2FA
                            </button>
                        </form>
                    @endif
                </div>

                <!-- Back Button -->
                <a href="{{ route('profile.show') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali ke Profil
                </a>
            </div>
        </div>
    </div>

    <script>
        function showRecoveryCodes() {
            fetch('{{ route('two-factor.recovery-codes') }}', {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(codes => {
                    const container = document.getElementById('recoveryCodes');
                    container.innerHTML = codes.map(code =>
                        `<div class="bg-white p-2 rounded text-center">${code}</div>`
                    ).join('');
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Gagal mengambil kode recovery');
                });
        }
    </script>
@endsection
