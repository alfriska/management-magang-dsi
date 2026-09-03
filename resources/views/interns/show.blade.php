@extends('layouts.app')

@section('title', 'Detail Anggota Magang')

@section('content')
    <div class="slide-up max-w-[1600px] mx-auto space-y-6">
        <!-- Header -->
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6">
            <div class="flex items-center gap-4">
                <a href="{{ route('interns.index') }}"
                    class="w-10 h-10 rounded-full bg-white border border-slate-200 flex items-center justify-center text-slate-500 hover:bg-slate-50 hover:text-slate-700 transition-colors shadow-sm">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div class="flex items-center gap-4">
                    @if ($intern->user->avatar)
                        <img src="{{ Str::startsWith($intern->user->avatar, ['http', 'https']) ? $intern->user->avatar : asset('storage/avatars/' . $intern->user->avatar) }}"
                            alt="{{ $intern->user->name }}" referrerpolicy="no-referrer"
                            class="w-14 h-14 rounded-full object-cover ring-4 ring-white shadow-md">
                    @else
                        <div
                            class="w-14 h-14 rounded-full bg-gradient-to-br from-emerald-400 to-emerald-600 flex items-center justify-center text-white text-xl font-bold ring-4 ring-white shadow-md">
                            {{ strtoupper(substr($intern->user->name, 0, 1)) }}
                        </div>
                    @endif
                    <div>
                        <h2 class="text-2xl font-bold text-slate-800 tracking-tight leading-none mb-1">
                            {{ $intern->user->name }}
                        </h2>
                        <p class="text-slate-500 font-medium text-sm flex items-center gap-2">
                            <i class="fas fa-school text-slate-400"></i> {{ $intern->school }}
                            <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                            {{ $intern->department }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="flex flex-wrap gap-3 w-full lg:w-auto">
                <a href="{{ route('interns.downloadReport', $intern) }}"
                    class="flex-1 lg:flex-none btn bg-emerald-500 hover:bg-emerald-600 text-white shadow-lg shadow-emerald-500/20 border-transparent">
                    <i class="fas fa-file-pdf mr-2"></i> Report
                </a>
                @if ($intern->status === 'completed')
                    <a href="{{ route('interns.certificate', $intern) }}" target="_blank"
                        class="flex-1 lg:flex-none btn bg-blue-500 hover:bg-blue-600 text-white shadow-lg shadow-blue-500/20 border-transparent">
                        <i class="fas fa-certificate mr-2"></i> Sertifikat
                    </a>
                @endif
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="card p-5 border border-indigo-100 bg-indigo-50/50">
                <div class="flex items-center gap-4">
                    <div
                        class="w-12 h-12 rounded-2xl bg-indigo-100 text-indigo-600 flex items-center justify-center text-xl">
                        <i class="fas fa-tasks"></i>
                    </div>
                    <div>
                        <div class="text-2xl font-black text-indigo-900">{{ $stats['completedTasks'] }} <span
                                class="text-sm font-medium text-indigo-400">/ {{ $stats['totalTasks'] }}</span></div>
                        <div class="text-xs font-bold text-indigo-500 uppercase tracking-wider">Tugas Selesai</div>
                    </div>
                </div>
            </div>

            <div class="card p-5 border border-emerald-100 bg-emerald-50/50">
                <div class="flex items-center gap-4">
                    <div
                        class="w-12 h-12 rounded-2xl bg-emerald-100 text-emerald-600 flex items-center justify-center text-xl">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div>
                        <div class="text-2xl font-black text-emerald-900">{{ $stats['attendancePercentage'] }}<span
                                class="text-lg">%</span></div>
                        <div class="text-xs font-bold text-emerald-500 uppercase tracking-wider">Kehadiran</div>
                    </div>
                </div>
            </div>

            <div class="card p-5 border border-sky-100 bg-sky-50/50">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-sky-100 text-sky-600 flex items-center justify-center text-xl">
                        <i class="fas fa-bolt"></i>
                    </div>
                    <div>
                        <div class="text-2xl font-black text-sky-900">{{ $stats['averageSpeed'] }}<span
                                class="text-lg">%</span></div>
                        <div class="text-xs font-bold text-sky-500 uppercase tracking-wider">Kecepatan</div>
                    </div>
                </div>
            </div>

            <div class="card p-5 border border-amber-100 bg-amber-50/50">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-amber-100 text-amber-600 flex items-center justify-center text-xl">
                        <i class="fas fa-star"></i>
                    </div>
                    <div>
                        <div class="text-2xl font-black text-amber-900">{{ $stats['overallScore'] }}</div>
                        <div class="text-xs font-bold text-amber-500 uppercase tracking-wider">Skor Rata-rata</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column (Profile Info) -->
            <div class="card h-full">
                <div class="flex items-center justify-between mb-6 pb-4 border-b border-slate-100">
                    <h3 class="font-bold text-slate-800 flex items-center gap-2">
                        <i class="fas fa-user-circle text-slate-400"></i> Informasi Profil
                    </h3>
                    <a href="{{ route('interns.edit', $intern) }}" class="btn btn-sm btn-warning">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                </div>

                <div class="space-y-5">
                    <div class="group">
                        <label class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1 block">Email</label>
                        <div class="text-sm font-semibold text-slate-700 flex items-center gap-2">
                            <i class="fas fa-envelope text-slate-300 w-5"></i> {{ $intern->user->email }}
                        </div>
                    </div>
                    <div class="group">
                        <label class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1 block">NIS</label>
                        <div class="text-sm font-semibold text-slate-700 flex items-center gap-2">
                            <i class="fas fa-id-card text-slate-300 w-5"></i> {{ $intern->nis ?? '-' }}
                        </div>
                    </div>
                    <div class="group">
                        <label class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1 block">No.
                            Telepon</label>
                        <div class="text-sm font-semibold text-slate-700 flex items-center gap-2">
                            <i class="fas fa-phone text-slate-300 w-5"></i> {{ $intern->phone ?? '-' }}
                        </div>
                    </div>
                    <div class="group">
                        <label class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1 block">Alamat</label>
                        <div class="text-sm font-semibold text-slate-700 flex items-center gap-2">
                            <i class="fas fa-map-marker-alt text-slate-300 w-5"></i> {{ $intern->address ?? '-' }}
                        </div>
                    </div>
                    <div class="group">
                        <label
                            class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1 block">Pembimbing</label>
                        <div class="text-sm font-semibold text-slate-700 flex items-center gap-2">
                            <i class="fas fa-chalkboard-teacher text-slate-300 w-5"></i>
                            {{ $intern->supervisor->name ?? '-' }}
                        </div>
                    </div>
                    <div class="group">
                        <label class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1 block">Periode</label>
                        <div class="text-sm font-semibold text-slate-700 flex items-center gap-2">
                            <i class="far fa-calendar-alt text-slate-300 w-5"></i>
                            {{ $intern->start_date->format('d M Y') }} - {{ $intern->end_date->format('d M Y') }}
                        </div>
                    </div>
                    <div class="group">
                        <label class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1 block">Status</label>
                        <div class="mt-1">
                            @if ($intern->status === 'active')
                                <span class="badge badge-success">Aktif</span>
                            @elseif($intern->status === 'completed')
                                <span class="badge badge-primary">Selesai</span>
                            @else
                                <span class="badge badge-danger">Dibatalkan</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Middle Column (Charts) -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Quick Actions -->
                <div
                    class="card p-5 bg-gradient-to-r from-violet-500 to-fuchsia-600 text-white border-0 shadow-lg shadow-violet-500/20">
                    <h3 class="font-bold text-white/90 text-sm uppercase tracking-wider mb-4 flex items-center gap-2">
                        <i class="fas fa-bolt"></i> Aksi Cepat
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <a href="{{ route('tasks.create') }}?intern_id={{ $intern->id }}"
                            class="btn bg-white/20 hover:bg-white/30 text-white border-0 backdrop-blur-sm justify-center">
                            <i class="fas fa-plus mr-2"></i> Tambah Tugas
                        </a>
                        <a href="{{ route('assessments.create') }}?intern_id={{ $intern->id }}"
                            class="btn bg-white/20 hover:bg-white/30 text-white border-0 backdrop-blur-sm justify-center">
                            <i class="fas fa-star mr-2"></i> Beri Penilaian
                        </a>
                        <a href="{{ route('reports.create') }}?intern_id={{ $intern->id }}"
                            class="btn bg-white/20 hover:bg-white/30 text-white border-0 backdrop-blur-sm justify-center">
                            <i class="fas fa-file-alt mr-2"></i> Buat Laporan
                        </a>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Task Status Chart -->
                    <div class="card min-h-[320px] flex flex-col">
                        <div class="card-header border-b border-slate-100 pb-3 mb-3">
                            <h3 class="card-title font-bold text-slate-700 text-sm"><i
                                    class="fas fa-chart-pie text-violet-500 mr-2"></i> Status Tugas</h3>
                        </div>
                        <div class="flex-1 relative">
                            <canvas id="taskChart"></canvas>
                        </div>
                    </div>

                    <!-- Attendance Chart -->
                    <div class="card min-h-[320px] flex flex-col">
                        <div class="card-header border-b border-slate-100 pb-3 mb-3">
                            <h3 class="card-title font-bold text-slate-700 text-sm"><i
                                    class="fas fa-user-clock text-emerald-500 mr-2"></i> Kehadiran</h3>
                        </div>
                        <div class="flex-1 relative">
                            <canvas id="attendanceChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // Common Chart Options
            const commonOptions = {
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
                            usePointStyle: true,
                            padding: 20
                        }
                    }
                },
                cutout: '75%',
                layout: {
                    padding: 10
                }
            };

            // Task Chart
            const taskCtx = document.getElementById('taskChart').getContext('2d');
            new Chart(taskCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Selesai', 'Proses', 'Menunggu', 'Revisi'],
                    datasets: [{
                        data: [{{ $taskStatusData['completed'] }}, {{ $taskStatusData['in_progress'] }},
                            {{ $taskStatusData['pending'] }}, {{ $taskStatusData['revision'] }}
                        ],
                        backgroundColor: ['#10b981', '#6366f1', '#94a3b8', '#f59e0b'],
                        borderWidth: 0,
                        hoverOffset: 4
                    }]
                },
                options: commonOptions
            });

            // Attendance Chart
            const attCtx = document.getElementById('attendanceChart').getContext('2d');
            new Chart(attCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Hadir', 'Telat', 'Absen', 'Sakit', 'Izin'],
                    datasets: [{
                        data: [{{ $attendanceData['present'] }}, {{ $attendanceData['late'] }},
                            {{ $attendanceData['absent'] }}, {{ $attendanceData['sick'] }},
                            {{ $attendanceData['permission'] }}
                        ],
                        backgroundColor: ['#10b981', '#f59e0b', '#ef4444', '#06b6d4', '#8b5cf6'],
                        borderWidth: 0,
                        hoverOffset: 4
                    }]
                },
                options: commonOptions
            });
        </script>
    @endpush
@endsection
