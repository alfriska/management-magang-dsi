@extends('layouts.app')

@section('title', 'Detail Laporan')

@section('content')
    <div class="slide-up">
        <div class="d-flex align-center gap-4 mb-6">
            <a href="{{ route('reports.index') }}" class="btn btn-secondary btn-icon">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div style="flex: 1;">
                <div class="d-flex align-center gap-4" style="flex-wrap: wrap;">
                    <h2 style="margin-bottom: 4px;">{{ $report->title }}</h2>
                    <span class="badge badge-primary">{{ $report->type_label }}</span>
                    <span class="badge badge-{{ $report->status_color }}">{{ $report->status_label }}</span>
                </div>
                <p class="text-muted">{{ $report->intern->user->name }} | {{ $report->period_start->format('d M') }} -
                    {{ $report->period_end->format('d M Y') }}</p>
            </div>
            <a href="{{ route('reports.edit', $report) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Edit
            </a>
        </div>

        <div class="grid-2">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-file-alt"></i> Isi Laporan</h3>
                </div>
                <div style="line-height: 1.8; white-space: pre-wrap;">{{ $report->content }}</div>
            </div>

            <div>
                <div class="card mb-6">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-info-circle"></i> Informasi</h3>
                    </div>
                    <div style="display: grid; gap: 16px;">
                        <div>
                            <label class="text-muted" style="font-size: 12px;">Siswa</label>
                            <div class="d-flex align-center gap-2">
                                @if ($report->intern->user->avatar)
                                    <img src="{{ Str::startsWith($report->intern->user->avatar, ['http', 'https']) ? $report->intern->user->avatar : asset('storage/avatars/' . $report->intern->user->avatar) }}"
                                        alt="{{ $report->intern->user->name }}"
                                        style="width: 32px; height: 32px; font-size: 12px; object-fit: cover; border-radius: 50%;"
                                        referrerpolicy="no-referrer">
                                @else
                                    <div class="user-avatar" style="width: 32px; height: 32px; font-size: 12px;">
                                        {{ strtoupper(substr($report->intern->user->name ?? 'N', 0, 1)) }}
                                    </div>
                                @endif
                                <strong>{{ $report->intern->user->name }}</strong>
                            </div>
                        </div>
                        <div>
                            <label class="text-muted" style="font-size: 12px;">Dibuat Oleh</label>
                            <div><strong>{{ $report->createdBy->name }}</strong></div>
                        </div>
                        <div>
                            <label class="text-muted" style="font-size: 12px;">Tanggal Dibuat</label>
                            <div><strong>{{ $report->created_at->format('d M Y H:i') }}</strong></div>
                        </div>
                    </div>
                </div>

                @if ($report->feedback)
                    <div class="card" style="background: rgba(34, 197, 94, 0.1); border-color: rgba(34, 197, 94, 0.3);">
                        <div class="card-header" style="border-color: rgba(34, 197, 94, 0.3);">
                            <h3 class="card-title" style="color: var(--success);"><i class="fas fa-comment-alt"></i>
                                Feedback</h3>
                        </div>
                        <div style="line-height: 1.8;">{{ $report->feedback }}</div>
                    </div>
                @elseif($report->status !== 'reviewed')
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-comment-alt"></i> Berikan Feedback</h3>
                        </div>
                        <form action="{{ route('reports.feedback', $report) }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <textarea name="feedback" class="form-control" rows="4" required
                                    placeholder="Tulis feedback untuk laporan ini..."></textarea>
                            </div>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-check"></i> Kirim Feedback
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
