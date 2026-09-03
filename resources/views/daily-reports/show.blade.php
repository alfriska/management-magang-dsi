@extends('layouts.app')

@section('title', 'Detail Daily Report')

@section('content')
<div class="slide-up">
    <div class="d-flex align-center gap-4 mb-6">
        <a href="{{ auth()->user()->isIntern() ? route('daily-reports.index') : route('daily-reports.monitoring') }}"
            class="btn btn-secondary btn-icon">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h2 style="margin-bottom: 4px;">Detail Daily Report</h2>
            <p class="text-muted">{{ $dailyReport->date->translatedFormat('l, d F Y') }}</p>
        </div>
    </div>

    <div class="card" style="max-width: 700px;">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-clipboard-list"></i> Daily Report
            </h3>
            @php
                $statusBadge = match ($dailyReport->status) {
                    'selesai' => ['class' => 'badge-success', 'label' => 'Selesai'],
                    'on_progress' => ['class' => 'badge-warning', 'label' => 'On Progress'],
                    default => ['class' => 'badge-secondary', 'label' => 'Belum Dikerjakan'],
                };
            @endphp
            <span class="badge {{ $statusBadge['class'] }}">{{ $statusBadge['label'] }}</span>
        </div>

        @unless (auth()->user()->isIntern())
            <div class="form-group">
                <label class="form-label">Siswa</label>
                <p class="text-slate-600">{{ $dailyReport->intern->user->name ?? '-' }}</p>
            </div>
        @endunless

        @if ($dailyReport->image)
            <div class="form-group">
                <label class="form-label">Gambar</label>
                <a href="{{ asset('storage/' . $dailyReport->image) }}" target="_blank">
                    <img src="{{ asset('storage/' . $dailyReport->image) }}" alt="Gambar Daily Report"
                        style="max-height: 280px; width: 100%; object-fit: cover; border-radius: 8px; border: 1px solid #e2e8f0;">
                </a>
            </div>
        @endif

        <div class="form-group">
            <label class="form-label">Deskripsi</label>
            <p class="text-slate-600" style="white-space: pre-line;">{{ $dailyReport->description }}</p>
        </div>

        <div class="form-group">
            <label class="form-label">Target Selesai</label>
            <p class="text-slate-600">
                {{ $dailyReport->target_date ? $dailyReport->target_date->translatedFormat('d F Y') : '-' }}
            </p>
        </div>

        <div class="form-group">
            <label class="form-label">Waktu Dibuat</label>
            <p class="text-slate-600">{{ $dailyReport->created_at->translatedFormat('d M Y, H:i') }}</p>
        </div>
    </div>
</div>
@endsection