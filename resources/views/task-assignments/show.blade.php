@extends('layouts.app')

@section('title', 'Detail Tugas: ' . $taskAssignment->title)

@section('content')
    <div class="slide-up max-w-7xl mx-auto space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div class="flex items-center gap-4">
                <a href="{{ route('task-assignments.index') }}" class="btn btn-secondary btn-icon">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div>
                    <div class="flex items-center gap-3 mb-1">
                        <h2 class="text-2xl font-bold text-slate-800 tracking-tight">{{ $taskAssignment->title }}</h2>
                        <span
                            class="badge badge-{{ $taskAssignment->priority_color }} text-[10px] uppercase tracking-wider px-2 py-0.5">
                            {{ strtoupper($taskAssignment->priority) }}
                        </span>
                        @if ($taskAssignment->deadline && $taskAssignment->deadline->isPast())
                            <span class="badge badge-danger text-[10px] uppercase tracking-wider px-2 py-0.5">Deadline
                                Lewat</span>
                        @endif
                    </div>
                    <div class="text-sm text-slate-500 font-medium">
                        <span class="text-slate-400">Dibuat oleh</span> {{ $taskAssignment->assignedBy->name ?? 'Admin' }}
                        <span class="mx-1 text-slate-300">•</span>
                        {{ $taskAssignment->created_at->format('d M Y H:i') }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <!-- Total -->
            <div class="card p-5 border border-indigo-100 bg-indigo-50/30 flex items-center gap-4">
                <div
                    class="w-12 h-12 rounded-xl bg-indigo-100 text-indigo-600 flex items-center justify-center text-xl shrink-0">
                    <i class="fas fa-users"></i>
                </div>
                <div>
                    <div class="text-2xl font-bold text-slate-800">{{ $stats['total'] }}</div>
                    <div class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Siswa</div>
                </div>
            </div>

            <!-- Selesai -->
            <div class="card p-5 border border-emerald-100 bg-emerald-50/30 flex items-center gap-4">
                <div
                    class="w-12 h-12 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center text-xl shrink-0">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div>
                    <div class="text-2xl font-bold text-slate-800">{{ $stats['completed'] }}</div>
                    <div class="text-xs font-bold text-slate-400 uppercase tracking-wider">Selesai</div>
                </div>
            </div>

            <!-- Review -->
            <div class="card p-5 border border-violet-100 bg-violet-50/30 flex items-center gap-4">
                <div
                    class="w-12 h-12 rounded-xl bg-violet-100 text-violet-600 flex items-center justify-center text-xl shrink-0">
                    <i class="fas fa-clock"></i>
                </div>
                <div>
                    <div class="text-2xl font-bold text-slate-800">{{ $stats['submitted'] }}</div>
                    <div class="text-xs font-bold text-slate-400 uppercase tracking-wider">Review</div>
                </div>
            </div>

            <!-- Belum Selesai -->
            <div class="card p-5 border border-amber-100 bg-amber-50/30 flex items-center gap-4">
                <div
                    class="w-12 h-12 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center text-xl shrink-0">
                    <i class="fas fa-spinner"></i>
                </div>
                <div>
                    <div class="text-2xl font-bold text-slate-800">{{ $stats['in_progress'] + $stats['pending'] }}</div>
                    <div class="text-xs font-bold text-slate-400 uppercase tracking-wider">Proses</div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Progress Chart -->
            <div class="card p-6 border border-slate-100">
                <h3 class="font-bold text-slate-800 text-lg mb-6 flex items-center gap-2">
                    <i class="fas fa-chart-pie text-violet-500"></i> Progress Keseluruhan
                </h3>

                <!-- Circular Progress -->
                <div class="relative w-48 h-48 mx-auto mb-8">
                    <svg class="w-full h-full transform -rotate-90" viewBox="0 0 36 36">
                        <path d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"
                            fill="none" stroke="#f1f5f9" stroke-width="3" />
                        <path d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"
                            fill="none" stroke="#10b981" stroke-width="3"
                            stroke-dasharray="{{ $stats['progress_percentage'] }}, 100" stroke-linecap="round" />
                    </svg>
                    <div class="absolute inset-0 flex flex-col items-center justify-center">
                        <span class="text-4xl font-black text-slate-800">{{ $stats['progress_percentage'] }}<span
                                class="text-xl text-slate-400">%</span></span>
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Completion</span>
                    </div>
                </div>

                <!-- Legends -->
                <div class="grid grid-cols-2 gap-3">
                    <div class="flex items-center gap-2 p-2 rounded-lg bg-emerald-50 border border-emerald-100">
                        <div class="w-2 h-2 rounded-full bg-emerald-500"></div>
                        <span class="text-xs text-slate-600 flex-1">Tepat Waktu</span>
                        <span class="font-bold text-slate-800 text-xs">{{ $stats['completed_on_time'] }}</span>
                    </div>
                    <div class="flex items-center gap-2 p-2 rounded-lg bg-rose-50 border border-rose-100">
                        <div class="w-2 h-2 rounded-full bg-rose-500"></div>
                        <span class="text-xs text-slate-600 flex-1">Terlambat</span>
                        <span class="font-bold text-slate-800 text-xs">{{ $stats['completed_late'] }}</span>
                    </div>
                    <!-- More layout items... handled dynamically usually but hardcoded for fidelity -->
                    <div class="flex items-center gap-2 p-2 rounded-lg bg-violet-50 border border-violet-100">
                        <div class="w-2 h-2 rounded-full bg-violet-500"></div>
                        <span class="text-xs text-slate-600 flex-1">Review</span>
                        <span class="font-bold text-slate-800 text-xs">{{ $stats['submitted'] }}</span>
                    </div>
                    <div class="flex items-center gap-2 p-2 rounded-lg bg-sky-50 border border-sky-100">
                        <div class="w-2 h-2 rounded-full bg-sky-500"></div>
                        <span class="text-xs text-slate-600 flex-1">Dikerjakan</span>
                        <span class="font-bold text-slate-800 text-xs">{{ $stats['in_progress'] }}</span>
                    </div>
                    <div class="flex items-center gap-2 p-2 rounded-lg bg-amber-50 border border-amber-100">
                        <div class="w-2 h-2 rounded-full bg-amber-500"></div>
                        <span class="text-xs text-slate-600 flex-1">Revisi</span>
                        <span class="font-bold text-slate-800 text-xs">{{ $stats['revision'] }}</span>
                    </div>
                    <div class="flex items-center gap-2 p-2 rounded-lg bg-slate-50 border border-slate-100">
                        <div class="w-2 h-2 rounded-full bg-slate-400"></div>
                        <span class="text-xs text-slate-600 flex-1">Belum</span>
                        <span class="font-bold text-slate-800 text-xs">{{ $stats['pending'] }}</span>
                    </div>
                </div>
            </div>

            <!-- Details & Charts -->
            <div class="card p-6 border border-slate-100">
                <h3 class="font-bold text-slate-800 text-lg mb-6 flex items-center gap-2">
                    <i class="fas fa-info-circle text-sky-500"></i> Detail Tugas
                </h3>

                <div class="space-y-4">
                    <div class="p-4 bg-slate-50 rounded-xl border border-slate-100">
                        <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Deadline</div>
                        <div class="font-bold text-slate-800 flex items-center gap-2">
                            @if ($taskAssignment->deadline)
                                <i class="far fa-calendar-alt text-slate-400"></i>
                                {{ $taskAssignment->deadline->format('d M Y') }}
                                @if ($taskAssignment->deadline_time)
                                    <span
                                        class="text-xs text-slate-500 font-normal">({{ $taskAssignment->deadline_time }})</span>
                                @endif
                            @else
                                <span class="text-slate-400 italic">Tidak ada deadline</span>
                            @endif
                        </div>
                    </div>

                    <div class="p-4 bg-slate-50 rounded-xl border border-slate-100">
                        <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Metode Submit</div>
                        <div class="font-bold text-slate-800 flex items-center gap-2">
                            @if ($taskAssignment->submission_type == 'github')
                                <i class="fab fa-github"></i> GitHub Only
                            @elseif($taskAssignment->submission_type == 'file')
                                <i class="fas fa-file-upload"></i> File Upload
                            @else
                                <i class="fas fa-layer-group"></i> GitHub / File
                            @endif
                        </div>
                    </div>

                    <div class="p-4 bg-emerald-50 rounded-xl border border-emerald-100">
                        <div class="text-[10px] font-bold text-emerald-600 uppercase tracking-wider mb-1">Rata-rata Nilai
                        </div>
                        <div class="text-3xl font-black text-emerald-600">
                            {{ $stats['average_score'] ?: '-' }}
                        </div>
                    </div>
                </div>
                <!-- Donut Chart Canvas -->
                <div class="mt-6 h-48 relative">
                    <canvas id="statusChart"></canvas>
                </div>
            </div>

            <!-- Description -->
            <div class="card p-6 border border-slate-100 flex flex-col">
                <h3 class="font-bold text-slate-800 text-lg mb-4 flex items-center gap-2">
                    <i class="fas fa-align-left text-slate-400"></i> Deskripsi
                </h3>
                <div
                    class="flex-1 overflow-y-auto max-h-[400px] prose prose-sm prose-slate max-w-none text-slate-600 leading-relaxed bg-slate-50 p-4 rounded-xl border border-slate-100">
                    {!! nl2br(e($taskAssignment->description ?? 'Tidak ada deskripsi.')) !!}
                </div>
            </div>
        </div>

        <!-- Students List -->
        <div class="card p-0 overflow-hidden border border-slate-200">
            <div class="p-6 border-b border-slate-100 bg-white">
                <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                    <h3 class="font-bold text-slate-800 text-lg flex items-center gap-2">
                        <i class="fas fa-user-graduate text-indigo-500"></i> Daftar Siswa
                    </h3>

                    <!-- Filter Tabs -->
                    <div class="flex flex-wrap gap-2 p-1 bg-slate-100 rounded-xl">
                        <button
                            class="status-tab active px-3 py-1.5 rounded-lg text-xs font-bold transition-all text-slate-600 hover:bg-white hover:shadow-sm"
                            data-status="all">
                            Semua <span
                                class="ml-1 px-1.5 py-0.5 rounded bg-slate-200 text-[10px]">{{ $stats['total'] }}</span>
                        </button>
                        <button
                            class="status-tab px-3 py-1.5 rounded-lg text-xs font-bold transition-all text-slate-600 hover:bg-white hover:shadow-sm"
                            data-status="completed">
                            Selesai <span
                                class="ml-1 px-1.5 py-0.5 rounded bg-emerald-100 text-emerald-700 text-[10px]">{{ $stats['completed'] }}</span>
                        </button>
                        <button
                            class="status-tab px-3 py-1.5 rounded-lg text-xs font-bold transition-all text-slate-600 hover:bg-white hover:shadow-sm"
                            data-status="submitted">
                            Review <span
                                class="ml-1 px-1.5 py-0.5 rounded bg-violet-100 text-violet-700 text-[10px]">{{ $stats['submitted'] }}</span>
                        </button>
                        <button
                            class="status-tab px-3 py-1.5 rounded-lg text-xs font-bold transition-all text-slate-600 hover:bg-white hover:shadow-sm"
                            data-status="in_progress">
                            Proses <span
                                class="ml-1 px-1.5 py-0.5 rounded bg-sky-100 text-sky-700 text-[10px]">{{ $stats['in_progress'] }}</span>
                        </button>
                        <button
                            class="status-tab px-3 py-1.5 rounded-lg text-xs font-bold transition-all text-slate-600 hover:bg-white hover:shadow-sm"
                            data-status="pending">
                            Belum <span
                                class="ml-1 px-1.5 py-0.5 rounded bg-slate-200 text-slate-600 text-[10px]">{{ $stats['pending'] }}</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Mobile View (Cards) -->
            <div class="block sm:hidden space-y-4 p-4 bg-slate-50/50">
                @foreach ($taskAssignment->tasks->sortByDesc(function ($task) {
            $order = ['submitted' => 5, 'revision' => 4, 'in_progress' => 3, 'pending' => 2, 'completed' => 1];
            return $order[$task->status] ?? 0;
        }) as $task)
                    <div class="student-card bg-white p-4 rounded-xl border border-slate-200 shadow-sm relative overflow-hidden"
                        data-status="{{ $task->status }}">
                        <div class="flex items-start justify-between gap-4 mb-4">
                            <div class="flex items-center gap-3">
                                @if ($task->intern->user->avatar)
                                    <img src="{{ Str::startsWith($task->intern->user->avatar, ['http', 'https']) ? $task->intern->user->avatar : asset('storage/avatars/' . $task->intern->user->avatar) }}"
                                        alt="{{ $task->intern->user->name }}"
                                        class="w-10 h-10 rounded-full object-cover ring-2 ring-emerald-400/50 flex-shrink-0"
                                        referrerpolicy="no-referrer">
                                @else
                                    <div class="user-avatar w-10 h-10 text-xs shrink-0">
                                        {{ strtoupper(substr($task->intern->user->name ?? 'N', 0, 1)) }}
                                    </div>
                                @endif
                                <div class="min-w-0">
                                    <div class="font-bold text-slate-700 text-sm truncate">
                                        {{ $task->intern->user->name ?? 'N/A' }}</div>
                                    <div class="text-[11px] text-slate-400 truncate">
                                        {{ $task->intern->user->email ?? '' }}</div>
                                </div>
                            </div>
                            <div class="flex flex-col items-end gap-1">
                                <span class="badge badge-{{ $task->status_color }} text-[10px]">
                                    {{ $task->status_label }}
                                </span>
                                @if ($task->is_late)
                                    <span
                                        class="text-[9px] font-bold text-rose-500 bg-rose-50 px-1.5 py-0.5 rounded border border-rose-100">LATE</span>
                                @endif
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3 mb-4">
                            <div class="p-2.5 bg-slate-50 rounded-lg border border-slate-100">
                                <span
                                    class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Sekolah</span>
                                <div class="text-xs font-bold text-slate-600 truncate">{{ $task->intern->school ?? '-' }}
                                </div>
                            </div>
                            <div class="p-2.5 bg-slate-50 rounded-lg border border-slate-100">
                                <span
                                    class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Dikumpulkan</span>
                                <div class="text-xs font-bold text-slate-600">
                                    @if ($task->submitted_at)
                                        {{ $task->submitted_at->format('d/m H:i') }}
                                    @else
                                        -
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-between gap-4 pt-4 border-t border-slate-100">
                            <div class="flex flex-col">
                                <span
                                    class="text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">Nilai</span>
                                @if ($task->score)
                                    @php
                                        $scoreColor =
                                            $task->score >= 80
                                                ? 'text-emerald-600'
                                                : ($task->score >= 60
                                                    ? 'text-amber-500'
                                                    : 'text-rose-500');
                                    @endphp
                                    <span
                                        class="font-black {{ $scoreColor }} text-lg leading-none">{{ $task->score }}</span>
                                @else
                                    <span class="text-slate-300 text-lg leading-none">-</span>
                                @endif
                            </div>

                            <div class="flex items-center gap-2">
                                <a href="{{ route('tasks.show', $task) }}" class="btn btn-sm btn-secondary"
                                    title="Lihat">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if ($task->status === 'submitted')
                                    <a href="{{ route('tasks.show', $task) }}" class="btn btn-sm btn-primary"
                                        title="Review">
                                        <i class="fas fa-check"></i>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Desktop View (Table) -->
            <div class="hidden sm:block overflow-x-auto">
                <table class="w-full">
                    <thead
                        class="bg-slate-50 border-b border-slate-100 text-xs text-slate-500 font-bold uppercase tracking-wider text-left">
                        <tr>
                            <th class="px-6 py-4">Siswa</th>
                            <th class="px-6 py-4">Sekolah</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4">Submitted</th>
                            <th class="px-6 py-4">Nilai</th>
                            <th class="px-6 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($taskAssignment->tasks->sortByDesc(function ($task) {
            $order = ['submitted' => 5, 'revision' => 4, 'in_progress' => 3, 'pending' => 2, 'completed' => 1];
            return $order[$task->status] ?? 0;
        }) as $task)
                            <tr class="student-row hover:bg-slate-50/50 transition-colors"
                                data-status="{{ $task->status }}">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        @if ($task->intern->user->avatar)
                                            <img src="{{ Str::startsWith($task->intern->user->avatar, ['http', 'https']) ? $task->intern->user->avatar : asset('storage/avatars/' . $task->intern->user->avatar) }}"
                                                alt="{{ $task->intern->user->name }}"
                                                class="w-9 h-9 rounded-full object-cover ring-2 ring-emerald-400/50 flex-shrink-0"
                                                referrerpolicy="no-referrer">
                                        @else
                                            <div class="user-avatar w-9 h-9 text-xs flex-shrink-0">
                                                {{ strtoupper(substr($task->intern->user->name ?? 'N', 0, 1)) }}
                                            </div>
                                        @endif
                                        <div>
                                            <div class="font-bold text-slate-700 text-sm">
                                                {{ $task->intern->user->name ?? 'N/A' }}</div>
                                            <div class="text-[11px] text-slate-400">{{ $task->intern->user->email ?? '' }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-600">
                                    {{ $task->intern->school ?? '-' }}
                                </td>
                                <td class="px-6 py-4">
                                    <span class="badge badge-{{ $task->status_color }}">
                                        {{ $task->status_label }}
                                    </span>
                                    @if ($task->is_late)
                                        <span class="badge badge-danger text-[9px] px-1.5 py-0.5 ml-1">LATE</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-600">
                                    @if ($task->submitted_at)
                                        {{ $task->submitted_at->format('d/m/Y H:i') }}
                                    @else
                                        <span class="text-slate-300">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @if ($task->score)
                                        @php
                                            $scoreColor =
                                                $task->score >= 80
                                                    ? 'text-emerald-600'
                                                    : ($task->score >= 60
                                                        ? 'text-amber-500'
                                                        : 'text-rose-500');
                                        @endphp
                                        <span class="font-black {{ $scoreColor }}">{{ $task->score }}</span>
                                    @else
                                        <span class="text-slate-300">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('tasks.show', $task) }}"
                                            class="btn btn-sm btn-secondary btn-icon" title="Lihat">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if ($task->status === 'submitted')
                                            <a href="{{ route('tasks.show', $task) }}" class="btn btn-sm btn-primary"
                                                title="Review">
                                                <i class="fas fa-check mr-1"></i> Review
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            // Status Chart
            const ctx = document.getElementById('statusChart').getContext('2d');
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Selesai', 'Review', 'Proses', 'Revisi', 'Belum Mulai'],
                    datasets: [{
                        data: [
                            {{ $stats['completed'] }},
                            {{ $stats['submitted'] }},
                            {{ $stats['in_progress'] }},
                            {{ $stats['revision'] }},
                            {{ $stats['pending'] }}
                        ],
                        backgroundColor: ['#10b981', '#8b5cf6', '#3b82f6', '#f59e0b', '#9ca3af'],
                        borderWidth: 0,
                        hoverOffset: 10
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                color: '#64748b',
                                font: {
                                    family: "'Plus Jakarta Sans', sans-serif",
                                    size: 11
                                },
                                padding: 15,
                                usePointStyle: true,
                                pointStyle: 'circle'
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(15, 23, 42, 0.9)',
                            padding: 12,
                            displayColors: true,
                            titleFont: {
                                family: "'Plus Jakarta Sans', sans-serif"
                            },
                            bodyFont: {
                                family: "'Plus Jakarta Sans', sans-serif"
                            }
                        }
                    },
                    cutout: '75%',
                    layout: {
                        padding: 10
                    }
                }
            });

            // Tab Filtering Logic
            const tabs = document.querySelectorAll('.status-tab');
            tabs.forEach(tab => {
                tab.addEventListener('click', function() {
                    // Remove active classes
                    tabs.forEach(t => {
                        t.classList.remove('bg-indigo-600', 'text-white', 'shadow-md');
                        t.classList.add('text-slate-600', 'hover:bg-white');
                        // Note: The specific styles in the HTML above use 'status-tab active' as a selector for custom CSS previously?
                        // Now we are using Tailwind classes. We need to manually toggle them.

                        // Let's rely on a simpler approach: 
                        // Currently I used `active` class in HTML. Let's make sure our JS toggles a styling class instead of 'active'.
                        // Actually the above HTML uses `active` in classList. Let's stick to adding/removing a style class.
                    });

                    // For simplicity in this replacement string, I will re-implement the JS to toggle specific Tailwind classes
                    // But wait, the previous code used custom css `.status-tab.active`.
                    // I should just use a simple class and let CSS handle it or fully JS.
                    // Let's reuse a simple `.active-tab-style` logic in JS.
                });
            });

            // Re-implementing Tab Logic robustly for Tailwind
            document.querySelectorAll('.status-tab').forEach(tab => {
                tab.addEventListener('click', function() {
                    // Reset all tabs
                    document.querySelectorAll('.status-tab').forEach(t => {
                        t.classList.remove('bg-white', 'shadow-sm', 'text-indigo-600', 'ring-1',
                            'ring-indigo-100');
                        t.classList.add('text-slate-600', 'hover:bg-white');
                    });

                    // Activate current
                    this.classList.remove('text-slate-600', 'hover:bg-white');
                    this.classList.add('bg-white', 'shadow-sm', 'text-indigo-600', 'ring-1', 'ring-indigo-100');

                    const status = this.dataset.status;
                    document.querySelectorAll('.student-row').forEach(row => {
                        if (status === 'all' || row.dataset.status === status) {
                            row.style.display = '';
                        } else {
                            row.style.display = 'none';
                        }
                    });
                });
            });

            // Set initial active state style manually for "All" tab or based on HTML "active" class
            document.querySelector('.status-tab[data-status="all"]').click();
        </script>
    @endpush
@endsection
