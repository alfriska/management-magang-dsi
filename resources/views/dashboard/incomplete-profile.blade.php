@extends('layouts.app')

@section('title', 'Lengkapi Profil')

@section('content')
    <div class="slide-up">
        <div class="card" style="max-width: 600px; margin: 0 auto; text-align: center;">
            <div class="empty-state-icon" style="background: rgba(245, 158, 11, 0.2); color: #f59e0b;">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <h2 style="margin-bottom: 16px;">Profil Belum Lengkap</h2>
            <p class="text-muted" style="margin-bottom: 24px;">
                Akun Anda belum terdaftar sebagai siswa magang. Mohon untuk daftar terlebih dahulu atau minta bantuan ke
                pembimbing magang
            </p>
            <div class="d-flex justify-center gap-4" style="justify-content: center;">
                <a href="{{ route('profile.show') }}" class="btn btn-primary">
                    <i class="fas fa-user"></i> Lihat Profil
                </a>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-secondary">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection
