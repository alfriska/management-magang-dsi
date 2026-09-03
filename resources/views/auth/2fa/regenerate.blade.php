@extends('layouts.app')

@section('title', 'Regenerate 2FA')

@section('content')
    <div class="container-fluid py-4">
        <div class="row justify-content-center">
            <div class="col-lg-5">
                <div class="card p-6">
                    <!-- Header -->
                    <div class="text-center mb-6">
                        <div class="w-14 h-14 mx-auto mb-4 rounded-2xl flex items-center justify-center text-2xl text-white shadow-lg"
                            style="background: linear-gradient(135deg, #10b981 0%, #34d399 100%); box-shadow: 0 8px 20px -4px rgba(16,185,129,0.5);">
                            <i class="fas fa-qrcode"></i>
                        </div>
                        <h1 class="text-xl font-extrabold text-slate-800 mb-1 tracking-tight">Setup Authenticator Baru</h1>
                        <p class="text-slate-400 text-sm">Scan QR Code dengan Google Authenticator</p>
                    </div>

                    <!-- Warning -->
                    <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 mb-6">
                        <p class="text-amber-800 text-sm">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            QR Code lama tidak akan berfungsi lagi setelah Anda mengaktifkan yang baru.
                        </p>
                    </div>

                    <!-- QR Code -->
                    <div class="qr-container bg-slate-50 rounded-2xl p-6 flex justify-center mb-4">
                        {!! $qrCodeSvg !!}
                    </div>

                    <!-- Manual Entry -->
                    <div class="mb-6">
                        <p class="text-center text-sm text-slate-500 mb-2">Atau masukkan kode ini secara manual:</p>
                        <div
                            class="font-mono text-sm bg-amber-50 text-amber-800 p-3 rounded-lg text-center break-all select-all">
                            {{ $secret }}
                        </div>
                    </div>

                    <!-- Verification Form -->
                    <form method="POST" action="{{ route('2fa.regenerate.confirm') }}">
                        @csrf

                        <div class="form-group">
                            <label class="form-label">Kode Verifikasi</label>
                            <input type="text" name="one_time_password"
                                class="form-control text-center @error('one_time_password') border-red-500 @enderror"
                                style="font-size: 24px; letter-spacing: 8px; font-weight: 600;" placeholder="000000"
                                maxlength="6" pattern="\d{6}" inputmode="numeric" autocomplete="one-time-code" required
                                autofocus>

                            @error('one_time_password')
                                <p class="text-red-500 text-sm mt-2">
                                    <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary w-full py-3 mb-3">
                            <i class="fas fa-check-circle"></i> Aktifkan Verifikasi Ganda Baru
                        </button>
                    </form>

                    <a href="{{ route('2fa.manage') }}" class="btn btn-secondary w-full">
                        <i class="fas fa-arrow-left"></i> Batal
                    </a>
                </div>
            </div>
        </div>
    </div>

    <style>
        .qr-container svg {
            width: 200px;
            height: 200px;
        }
    </style>
@endsection
