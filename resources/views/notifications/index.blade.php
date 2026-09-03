@extends('layouts.app')

@section('title', 'Notifikasi')

@section('content')
    <div class="slide-up">
        <div class="d-flex justify-between align-center mb-6" style="flex-wrap: wrap; gap: 16px;">
            <div>
                <h2 style="margin-bottom: 4px;">Notifikasi</h2>
                <p class="text-muted">Riwayat notifikasi Anda</p>
            </div>
            @if($notifications->where('read_at', null)->count() > 0)
                <a href="{{ route('notifications.markAllRead') }}" class="btn btn-secondary">
                    <i class="fas fa-check-double"></i> Tandai Semua Dibaca
                </a>
            @endif
        </div>

        <div class="card">
            @if($notifications->isEmpty())
                <div class="empty-state">
                    <div class="empty-state-icon">
                        <i class="fas fa-bell-slash"></i>
                    </div>
                    <h4 class="empty-state-title">Belum Ada Notifikasi</h4>
                    <p class="empty-state-text">Notifikasi akan muncul di sini saat ada aktivitas baru.</p>
                </div>
            @else
                <div style="display: flex; flex-direction: column;">
                    @foreach($notifications as $notification)
                        <a href="{{ $notification->link ? route('notifications.read', $notification) : '#' }}"
                            style="display: flex; gap: 16px; padding: 20px 24px; text-decoration: none; border-bottom: 1px solid var(--border-color); transition: background 0.2s; {{ !$notification->read_at ? 'background: rgba(167, 139, 250, 0.05);' : '' }}"
                            onmouseover="this.style.background='var(--bg-hover)'"
                            onmouseout="this.style.background='{{ !$notification->read_at ? 'rgba(167, 139, 250, 0.05)' : 'transparent' }}'">
                            <div
                                style="width: 48px; height: 48px; background: var(--bg-tertiary); border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                <i class="{{ $notification->icon_class }}"
                                    style="font-size: 18px; color: {{ $notification->color }};"></i>
                            </div>
                            <div style="flex: 1; min-width: 0;">
                                <div
                                    style="display: flex; justify-content: space-between; align-items: flex-start; gap: 12px; margin-bottom: 4px;">
                                    <div style="font-weight: 600; color: var(--text-primary); font-size: 15px;">
                                        {{ $notification->title }}</div>
                                    @if(!$notification->read_at)
                                        <span
                                            style="width: 8px; height: 8px; background: var(--accent-primary); border-radius: 50%; flex-shrink: 0;"></span>
                                    @endif
                                </div>
                                <div style="color: var(--text-secondary); font-size: 14px; line-height: 1.5; margin-bottom: 8px;">
                                    {{ $notification->message }}</div>
                                <div style="color: var(--text-muted); font-size: 12px;">
                                    <i class="far fa-clock" style="margin-right: 4px;"></i>
                                    {{ $notification->created_at->diffForHumans() }}
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>

                <div class="pagination" style="padding: 16px 24px;">
                    {{ $notifications->links('vendor.livewire.simple-pagination') }}
                </div>
            @endif
        </div>
    </div>
@endsection
