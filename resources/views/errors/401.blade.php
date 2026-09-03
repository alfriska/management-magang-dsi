@extends('errors.layout')

@section('title', 'Tidak Terautentikasi')
@section('code', '401')
@section('icon', 'fa-user-lock')

@section('message', 'Autentikasi Diperlukan')
@section('description', 'Sesi Anda mungkin telah berakhir atau Anda belum masuk. Silakan masuk kembali untuk melanjutkan akses.')

@section('actions')
    <a href="{{ route('login') }}"
        class="px-6 py-2.5 rounded-xl bg-indigo-600 text-white font-semibold hover:bg-indigo-700 shadow-lg hover:shadow-xl transition-all w-full sm:w-auto flex items-center justify-center gap-2">
        <i class="fas fa-sign-in-alt text-sm"></i> Halaman Login
    </a>
@endsection
