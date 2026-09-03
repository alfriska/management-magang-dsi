@extends('layouts.app')

@section('title', 'Kelola Tugas Grup')

@section('content')
    <div class="slide-up">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
            <div>
                <h2 class="text-xl font-bold text-slate-800 mb-1">Kelola Tugas Grup</h2>
                <p class="text-slate-400 text-sm">Lihat dan kelola tugas yang diberikan ke banyak siswa sekaligus</p>
            </div>
            <div class="flex gap-2 w-full sm:w-auto">
                <a href="{{ route('tasks.index') }}" class="btn btn-secondary flex-1 sm:flex-none">
                    <i class="fas fa-list"></i>
                    <span class="hidden sm:inline">Lihat Per Siswa</span>
                </a>
                <a href="{{ route('tasks.create') }}" class="btn btn-primary flex-1 sm:flex-none">
                    <i class="fas fa-plus"></i>
                    <span class="hidden sm:inline">Buat Tugas Baru</span>
                </a>
            </div>
        </div>

        <!-- Search & Filter -->
        <div class="card mb-4">
            <form method="GET" class="d-flex gap-4" style="flex-wrap: wrap;">
                <div class="form-group" style="flex: 1; min-width: 200px; margin-bottom: 0;">
                    <input type="text" name="search" class="form-control" placeholder="Cari judul tugas..."
                        value="{{ request('search') }}">
                </div>
                <div class="form-group" style="min-width: 120px; margin-bottom: 0;">
                    <select name="priority" class="form-control">
                        <option value="">Semua Prioritas</option>
                        <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>Tinggi</option>
                        <option value="medium" {{ request('priority') == 'medium' ? 'selected' : '' }}>Sedang</option>
                        <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>Rendah</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary" style="width: auto;">
                    <i class="fas fa-search"></i> Cari
                </button>
            </form>
        </div>

        <!-- Task Assignments List -->
        @if($taskAssignments->isEmpty())
            <div class="card text-center" style="padding: 60px;">
                <i class="fas fa-tasks" style="font-size: 48px; color: var(--text-muted); margin-bottom: 16px;"></i>
                <h3>Belum Ada Tugas Grup</h3>
                <p class="text-muted">Buat tugas baru untuk memberikan ke banyak siswa sekaligus.</p>
                <a href="{{ route('tasks.create') }}" class="btn btn-primary" style="margin-top: 16px;">
                    <i class="fas fa-plus"></i> Buat Tugas Baru
                </a>
            </div>
        @else
            <div class="task-assignments-grid">
                @foreach($taskAssignments as $assignment)
                    @php
                        $stats = $assignment->stats;
                        $progressColor = $stats['progress_percentage'] == 100 ? '#10b981' : ($stats['progress_percentage'] > 50 ? '#3b82f6' : '#f59e0b');
                    @endphp
                    <div class="task-assignment-card">
                        <div class="assignment-header">
                            <div class="d-flex gap-2 align-center" style="flex-wrap: wrap;">
                                <span class="badge badge-{{ $assignment->priority_color }}"
                                    style="font-size: 10px; padding: 4px 8px;">
                                    {{ strtoupper($assignment->priority) }}
                                </span>
                                @if($assignment->deadline && $assignment->deadline->isPast())
                                    <span class="badge badge-danger" style="font-size: 10px; padding: 4px 8px;">
                                        DEADLINE LEWAT
                                    </span>
                                @endif
                            </div>
                            <span class="text-muted" style="font-size: 12px;">
                                {{ $assignment->created_at->format('d M Y') }}
                            </span>
                        </div>

                        <h3 class="assignment-title">{{ $assignment->title }}</h3>

                        <p class="assignment-description text-muted">
                            {{ Str::limit($assignment->description, 80) }}
                        </p>

                        <!-- Progress Bar -->
                        <div class="assignment-progress">
                            <div class="d-flex justify-between" style="margin-bottom: 8px;">
                                <span style="font-size: 12px; font-weight: 600; color: var(--text-secondary);">
                                    <i class="fas fa-users"></i> {{ $stats['total'] }} Siswa
                                </span>
                                <span style="font-size: 12px; font-weight: 700; color: {{ $progressColor }};">
                                    {{ $stats['progress_percentage'] }}% Selesai
                                </span>
                            </div>
                            <div style="height: 8px; background: var(--bg-tertiary); border-radius: 4px; overflow: hidden;">
                                <div
                                    style="height: 100%; width: {{ $stats['progress_percentage'] }}%; background: {{ $progressColor }}; border-radius: 4px; transition: width 0.5s ease;">
                                </div>
                            </div>
                        </div>

                        <!-- Stats Grid -->
                        <div class="assignment-stats">
                            <div class="stat-item stat-completed">
                                <span class="stat-value">{{ $stats['completed'] }}</span>
                                <span class="stat-label">Selesai</span>
                            </div>
                            <div class="stat-item stat-submitted">
                                <span class="stat-value">{{ $stats['submitted'] }}</span>
                                <span class="stat-label">Review</span>
                            </div>
                            <div class="stat-item stat-progress">
                                <span class="stat-value">{{ $stats['in_progress'] }}</span>
                                <span class="stat-label">Proses</span>
                            </div>
                            <div class="stat-item stat-pending">
                                <span class="stat-value">{{ $stats['pending'] }}</span>
                                <span class="stat-label">Belum</span>
                            </div>
                        </div>

                        <!-- Deadline -->
                        @if($assignment->deadline)
                            <div class="assignment-deadline {{ $assignment->deadline->isPast() ? 'overdue' : '' }}">
                                <i class="far fa-calendar-alt"></i>
                                Deadline: {{ $assignment->deadline->format('d M Y') }}
                                @if($assignment->deadline_time)
                                    {{ $assignment->deadline_time }}
                                @endif
                            </div>
                        @endif

                        <!-- Action -->
                        <a href="{{ route('task-assignments.show', $assignment) }}" class="btn btn-primary btn-sm"
                            style="width: 100%; margin-top: 16px;">
                            <i class="fas fa-eye"></i> Lihat Detail & Statistik
                        </a>
                    </div>
                @endforeach
            </div>

            <div style="margin-top: 24px;">
                {{ $taskAssignments->links() }}
            </div>
        @endif
    </div>

    @push('styles')
        <style>
            .task-assignments-grid {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
                gap: 20px;
            }

            .task-assignment-card {
                background: var(--bg-secondary);
                border: 1px solid var(--border-color);
                border-radius: var(--radius-lg);
                padding: 24px;
                transition: all 0.3s ease;
            }

            .task-assignment-card:hover {
                border-color: var(--accent-primary);
                box-shadow: 0 8px 25px rgba(99, 102, 241, 0.15);
                transform: translateY(-2px);
            }

            .assignment-header {
                display: flex;
                justify-content: space-between;
                align-items: flex-start;
                margin-bottom: 12px;
            }

            .assignment-title {
                font-size: 18px;
                font-weight: 700;
                margin: 0 0 8px 0;
                color: var(--text-primary);
                line-height: 1.3;
            }

            .assignment-description {
                font-size: 13px;
                margin-bottom: 16px;
                line-height: 1.5;
            }

            .assignment-progress {
                margin-bottom: 16px;
            }

            .assignment-stats {
                display: grid;
                grid-template-columns: repeat(4, 1fr);
                gap: 8px;
                margin-bottom: 12px;
            }

            .stat-item {
                text-align: center;
                padding: 10px 6px;
                border-radius: 8px;
            }

            .stat-value {
                display: block;
                font-size: 18px;
                font-weight: 800;
            }

            .stat-label {
                display: block;
                font-size: 10px;
                font-weight: 600;
                text-transform: uppercase;
                opacity: 0.8;
            }

            .stat-completed {
                background: rgba(16, 185, 129, 0.1);
                color: #10b981;
            }

            .stat-submitted {
                background: rgba(139, 92, 246, 0.1);
                color: #8b5cf6;
            }

            .stat-progress {
                background: rgba(59, 130, 246, 0.1);
                color: #3b82f6;
            }

            .stat-pending {
                background: rgba(156, 163, 175, 0.15);
                color: #6b7280;
            }

            .assignment-deadline {
                font-size: 12px;
                color: var(--text-muted);
                padding: 8px 12px;
                background: var(--bg-tertiary);
                border-radius: 6px;
                text-align: center;
            }

            .assignment-deadline.overdue {
                background: rgba(239, 68, 68, 0.1);
                color: #ef4444;
            }
        </style>
    @endpush
@endsection
