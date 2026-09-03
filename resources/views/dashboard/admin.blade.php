@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    @push('styles')
        <style>
            /* Minimal custom styles - most styling now uses Tailwind */
            .stat-card,
            .card {
                background: rgba(255, 255, 255, 0.85) !important;
                backdrop-filter: blur(12px);
                -webkit-backdrop-filter: blur(12px);
            }

            .chart-container {
                position: relative;
            }
        </style>
    @endpush

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">
        <!-- Header -->
        <div class="mb-6 md:mb-8">
            <h2 class="text-2xl sm:text-3xl font-bold text-slate-800 mb-2">
                Selamat Datang, {{ auth()->user()->name }}! 👋
            </h2>
            <p class="text-slate-600 text-sm sm:text-base">
                Dashboard ringkas dan bersih untuk memantau aktivitas magang.
            </p>
        </div>

        <!-- Pending Registration Alert -->
        @if (isset($pendingRegistrations) && $pendingRegistrations > 0)
            <div class="card p-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6"
                style="background: linear-gradient(135deg, rgba(251,191,36,0.15) 0%, rgba(245,158,11,0.15) 100%); border: 2px solid rgba(251,191,36,0.4);">
                <div class="flex items-center gap-4">
                    <div
                        class="w-12 h-12 rounded-2xl bg-amber-500 text-white flex items-center justify-center shadow-lg shadow-amber-500/30">
                        <i class="fas fa-user-clock text-xl"></i>
                    </div>
                    <div>
                        <p class="font-bold text-amber-800 text-base">{{ $pendingRegistrations }} Pendaftaran Menunggu
                            Approval</p>
                        <p class="text-sm text-amber-600">Ada calon magang yang mendaftar dan membutuhkan persetujuan Anda.
                        </p>
                    </div>
                </div>
                <a href="{{ route('interns.index', ['status' => 'pending']) }}"
                    class="btn bg-amber-500 hover:bg-amber-600 text-white shadow-lg shadow-amber-500/30 whitespace-nowrap">
                    <i class="fas fa-eye"></i> Lihat & Approve
                </a>
            </div>
        @endif

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
            <!-- Total Siswa -->
            <div
                class="stat-card bg-white/85 backdrop-blur-xl rounded-2xl p-6 border border-slate-200/80 shadow-sm hover:shadow-md hover:-translate-y-1 transition-all duration-200">
                <div
                    class="flex items-center justify-center w-14 h-14 sm:w-16 sm:h-16 rounded-2xl bg-violet-100 text-violet-700 mb-4">
                    <i class="fas fa-users text-2xl"></i>
                </div>
                <div class="text-3xl sm:text-4xl font-extrabold text-slate-800 mb-1">
                    {{ $totalInterns }}
                </div>
                <div class="text-sm font-medium text-slate-500 uppercase tracking-wide">
                    Total Siswa
                </div>
            </div>

            <!-- Tepat Waktu -->
            <div
                class="stat-card bg-white/85 backdrop-blur-xl rounded-2xl p-6 border border-slate-200/80 shadow-sm hover:shadow-md hover:-translate-y-1 transition-all duration-200">
                <div
                    class="flex items-center justify-center w-14 h-14 sm:w-16 sm:h-16 rounded-2xl bg-green-100 text-green-700 mb-4">
                    <i class="fas fa-check-circle text-2xl"></i>
                </div>
                <div class="text-3xl sm:text-4xl font-extrabold text-slate-800 mb-1">
                    {{ $completedOnTime }}
                </div>
                <div class="text-sm font-medium text-slate-500 uppercase tracking-wide">
                    Tepat Waktu
                </div>
            </div>

            <!-- Terlambat -->
            <div
                class="stat-card bg-white/85 backdrop-blur-xl rounded-2xl p-6 border border-slate-200/80 shadow-sm hover:shadow-md hover:-translate-y-1 transition-all duration-200">
                <div
                    class="flex items-center justify-center w-14 h-14 sm:w-16 sm:h-16 rounded-2xl bg-orange-100 text-orange-700 mb-4">
                    <i class="fas fa-clock text-2xl"></i>
                </div>
                <div class="text-3xl sm:text-4xl font-extrabold text-slate-800 mb-1">
                    {{ $completedLate }}
                </div>
                <div class="text-sm font-medium text-slate-500 uppercase tracking-wide">
                    Terlambat
                </div>
            </div>

            <!-- Kehadiran -->
            <div
                class="stat-card bg-white/85 backdrop-blur-xl rounded-2xl p-6 border border-slate-200/80 shadow-sm hover:shadow-md hover:-translate-y-1 transition-all duration-200">
                <div
                    class="flex items-center justify-center w-14 h-14 sm:w-16 sm:h-16 rounded-2xl bg-sky-100 text-sky-700 mb-4">
                    <i class="fas fa-calendar-check text-2xl"></i>
                </div>
                <div class="text-2xl sm:text-3xl font-extrabold text-slate-800 mb-1">
                    {{ $presentToday }} / {{ $totalInterns }}
                </div>
                <div class="text-sm font-medium text-slate-500 uppercase tracking-wide">
                    Kehadiran
                </div>
            </div>
        </div>

        <!-- Task Overview Card -->
        <div class="card bg-white/85 backdrop-blur-xl rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 p-6 border-b border-slate-100">
                <h3 class="text-lg font-semibold text-slate-700 flex items-center gap-2">
                    <i class="fas fa-chart-pie text-violet-500"></i>
                    Statistik Tugas
                </h3>
                <a href="{{ route('tasks.create') }}"
                    class="inline-flex items-center justify-center px-4 py-2 bg-violet-500 hover:bg-violet-600 text-white text-sm font-semibold rounded-xl transition-colors duration-200">
                    <i class="fas fa-plus mr-2"></i>
                    Buat Tugas
                </a>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-center">
                    <div class="chart-container h-64 sm:h-72">
                        <canvas id="taskPieChart"></canvas>
                    </div>
                    <div class="space-y-3">
                        <div class="flex items-center gap-4 p-4 bg-slate-50 rounded-xl border border-slate-100">
                            <div class="w-3 h-3 rounded-full bg-green-400 flex-shrink-0"></div>
                            <div class="flex-1 text-sm font-medium text-slate-600">Tepat Waktu</div>
                            <strong class="text-lg font-bold text-slate-800">{{ $completedOnTime }}</strong>
                        </div>
                        <div class="flex items-center gap-4 p-4 bg-slate-50 rounded-xl border border-slate-100">
                            <div class="w-3 h-3 rounded-full bg-yellow-400 flex-shrink-0"></div>
                            <div class="flex-1 text-sm font-medium text-slate-600">Terlambat</div>
                            <strong class="text-lg font-bold text-slate-800">{{ $completedLate }}</strong>
                        </div>
                        <div class="flex items-center gap-4 p-4 bg-slate-50 rounded-xl border border-slate-100">
                            <div class="w-3 h-3 rounded-full bg-violet-400 flex-shrink-0"></div>
                            <div class="flex-1 text-sm font-medium text-slate-600">Dalam Proses</div>
                            <strong class="text-lg font-bold text-slate-800">{{ $pendingTasks }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Submitted Tasks (Pending Review) -->
        @if (isset($submittedTasks) && $submittedTasks->isNotEmpty())
            <div
                class="card bg-gradient-to-br from-sky-50 to-blue-50 backdrop-blur-xl rounded-2xl border-2 border-sky-200 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-sky-100">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <div>
                            <h3 class="text-lg font-bold text-sky-700 flex items-center gap-2 mb-1">
                                <i class="fas fa-clipboard-check text-xl"></i>
                                Tugas Menunggu Review
                                <span
                                    class="inline-flex items-center justify-center w-7 h-7 bg-sky-600 text-white text-xs font-bold rounded-full">
                                    {{ $submittedTasks->count() }}
                                </span>
                            </h3>
                            <p class="text-sm text-sky-600 ml-8">
                                Tugas berikut menunggu penilaian dan konfirmasi dari Anda.
                            </p>
                        </div>
                    </div>
                </div>
                <!-- Mobile View (Cards) -->
                <div class="block sm:hidden divide-y divide-sky-100">
                    @foreach ($submittedTasks as $task)
                        <div class="p-4 bg-white/40 hover:bg-white/60 transition-colors">
                            <div class="flex justify-between items-start mb-3">
                                <div>
                                    <h4 class="font-bold text-slate-800 text-sm mb-1 line-clamp-2">{{ $task->title }}</h4>
                                    @if ($task->is_late)
                                        <span
                                            class="inline-block px-2 py-0.5 text-[10px] font-bold bg-orange-100 text-orange-700 rounded-lg">
                                            Terlambat
                                        </span>
                                    @endif
                                </div>
                                <span
                                    class="text-[10px] text-sky-600 font-semibold whitespace-nowrap bg-sky-100 px-2 py-1 rounded-full">
                                    {{ $task->submitted_at->diffForHumans() }}
                                </span>
                            </div>

                            <div class="flex items-center gap-2 mb-4">
                                <div
                                    class="w-7 h-7 bg-sky-200 text-sky-700 rounded-full flex items-center justify-center text-[10px] font-bold shadow-sm">
                                    {{ substr($task->intern->user->name, 0, 1) }}
                                </div>
                                <div class="flex flex-col">
                                    <span
                                        class="text-xs font-semibold text-slate-700">{{ $task->intern->user->name }}</span>
                                    <span class="text-[10px] text-slate-400">Siswa Magang</span>
                                </div>
                            </div>

                            <a href="{{ route('tasks.show', $task) }}"
                                class="flex items-center justify-center w-full px-4 py-2.5 bg-sky-600 hover:bg-sky-700 text-white text-sm font-semibold rounded-xl transition-all shadow-md shadow-sky-500/20 active:scale-95">
                                <i class="fas fa-feather-alt mr-2"></i> Review & Nilai
                            </a>
                        </div>
                    @endforeach
                </div>

                <!-- Desktop View (Table) -->
                <div class="hidden sm:block overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-sky-100">
                                <th class="px-6 py-3 text-left text-xs font-semibold text-sky-700 uppercase tracking-wider">
                                    Tugas</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-sky-700 uppercase tracking-wider">
                                    Siswa</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold text-sky-700 uppercase tracking-wider hidden sm:table-cell">
                                    Waktu Submit</th>
                                <th
                                    class="px-6 py-3 text-right text-xs font-semibold text-sky-700 uppercase tracking-wider">
                                    Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-sky-100">
                            @foreach ($submittedTasks as $task)
                                <tr class="hover:bg-sky-50/50 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="font-semibold text-slate-800 mb-1">
                                            {{ Str::limit($task->title, 40) }}
                                        </div>
                                        @if ($task->is_late)
                                            <span
                                                class="inline-block px-2 py-1 text-xs font-semibold bg-orange-100 text-orange-700 rounded">
                                                Terlambat
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-2">
                                            <div
                                                class="w-8 h-8 bg-sky-200 text-sky-700 rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0">
                                                {{ substr($task->intern->user->name, 0, 1) }}
                                            </div>
                                            <span class="text-sm text-slate-700">{{ $task->intern->user->name }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-sky-600 hidden sm:table-cell">
                                        {{ $task->submitted_at->diffForHumans() }}
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="{{ route('tasks.show', $task) }}"
                                            class="inline-flex items-center px-4 py-2 bg-sky-600 hover:bg-sky-700 text-white text-sm font-semibold rounded-lg transition-colors duration-200">
                                            <i class="fas fa-feather-alt mr-2"></i>
                                            <span class="hidden sm:inline">Review & Nilai</span>
                                            <span class="sm:hidden">Review</span>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        <!-- Recent Tasks & Today's Attendance -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Recent Tasks -->
            <div
                class="card bg-white/85 backdrop-blur-xl rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
                <div class="flex items-center justify-between p-6 border-b border-slate-100">
                    <h3 class="text-lg font-semibold text-slate-700 flex items-center gap-2">
                        <i class="fas fa-tasks text-amber-500"></i>
                        Tugas Terbaru
                    </h3>
                    <a href="{{ route('tasks.index') }}"
                        class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-600 text-sm font-semibold rounded-lg transition-colors">
                        Semua
                    </a>
                </div>

                @if ($recentTasks->isEmpty())
                    <div class="p-12 text-center">
                        <p class="text-slate-400">Belum ada tugas.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b border-slate-100">
                                    <th
                                        class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                        Tugas</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider hidden sm:table-cell">
                                        Siswa</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                        Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach ($recentTasks as $task)
                                    <tr class="hover:bg-slate-50 transition-colors">
                                        <td class="px-6 py-4">
                                            <div class="font-semibold text-slate-800">
                                                {{ Str::limit($task->title, 20) }}
                                            </div>
                                            @if ($task->is_late && $task->status === 'completed')
                                                <span
                                                    class="inline-block mt-1 px-2 py-0.5 text-xs font-semibold bg-red-100 text-red-700 rounded">
                                                    Late
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-sm text-slate-600 hidden sm:table-cell">
                                            {{ $task->intern->user->name ?? '-' }}
                                        </td>
                                        <td class="px-6 py-4">
                                            <span
                                                class="inline-block px-3 py-1 text-xs font-semibold rounded-lg
                                                @if ($task->status_color === 'success') bg-green-100 text-green-700
                                                @elseif($task->status_color === 'warning') bg-amber-100 text-amber-700
                                                @elseif($task->status_color === 'danger') bg-red-100 text-red-700
                                                @else bg-sky-100 text-sky-700 @endif">
                                                {{ $task->status_label }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            <!-- Today's Attendance -->
            <div
                class="card bg-white/85 backdrop-blur-xl rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
                <div class="flex items-center justify-between p-6 border-b border-slate-100">
                    <h3 class="text-lg font-semibold text-slate-700 flex items-center gap-2">
                        <i class="fas fa-calendar-check text-sky-500"></i>
                        Presensi Hari Ini
                    </h3>
                    <a href="{{ route('attendances.index') }}"
                        class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-600 text-sm font-semibold rounded-lg transition-colors">
                        Semua
                    </a>
                </div>

                @if ($recentAttendances->isEmpty())
                    <div class="p-12 text-center">
                        <p class="text-slate-400">Belum ada presensi.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b border-slate-100">
                                    <th
                                        class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                        Nama</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider hidden sm:table-cell">
                                        Waktu</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                        Status</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider hidden md:table-cell">
                                        Bukti</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach ($recentAttendances as $attendance)
                                    <tr class="hover:bg-slate-50 transition-colors">
                                        <td class="px-6 py-4 font-medium text-slate-800">
                                            {{ $attendance->intern->user->name ?? '-' }}
                                        </td>
                                        <td class="px-6 py-4 font-mono text-sm text-slate-600 hidden sm:table-cell">
                                            {{ $attendance->check_in ?? '-' }}
                                        </td>
                                        <td class="px-6 py-4">
                                            <span
                                                class="inline-block px-3 py-1 text-xs font-semibold rounded-lg
                                                @if ($attendance->status_color === 'success') bg-green-100 text-green-700
                                                @elseif($attendance->status_color === 'warning') bg-amber-100 text-amber-700
                                                @elseif($attendance->status_color === 'danger') bg-red-100 text-red-700
                                                @else bg-sky-100 text-sky-700 @endif">
                                                {{ $attendance->status_label }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 hidden md:table-cell">
                                            @if ($attendance->proof_file)
                                                <a href="{{ asset('storage/' . $attendance->proof_file) }}"
                                                    target="_blank" class="text-indigo-600 hover:text-indigo-700 text-sm">
                                                    <i class="fas fa-paperclip"></i> Lihat
                                                </a>
                                            @else
                                                <span class="text-slate-400 text-sm">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

        <!-- Attendance Charts -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Attendance Today - Donut Chart -->
            <div
                class="card bg-white/85 backdrop-blur-xl rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-slate-100">
                    <h3 class="text-lg font-semibold text-slate-700 flex items-center gap-2">
                        <i class="fas fa-user-check text-green-500"></i>
                        Kehadiran Hari Ini
                    </h3>
                </div>
                <div class="p-6">
                    <div class="chart-container h-64 sm:h-72">
                        <canvas id="attendanceTodayChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Weekly Attendance Trend - Bar Chart -->
            <div
                class="card bg-white/85 backdrop-blur-xl rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-slate-100">
                    <h3 class="text-lg font-semibold text-slate-700 flex items-center gap-2">
                        <i class="fas fa-chart-bar text-indigo-500"></i>
                        Trend Kehadiran 7 Hari
                    </h3>
                </div>
                <div class="p-6">
                    <div class="chart-container h-64 sm:h-72">
                        <canvas id="attendanceTrendChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Performance Line Chart -->
        <div class="card bg-white/85 backdrop-blur-xl rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-slate-100">
                <h3 class="text-lg font-semibold text-slate-700 flex items-center gap-2">
                    <i class="fas fa-chart-line text-rose-500"></i>
                    Performa Siswa
                </h3>
            </div>
            <div class="p-6">
                <div class="chart-container h-80 sm:h-96">
                    <canvas id="performanceChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // Task Pie Chart
            new Chart(document.getElementById('taskPieChart').getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: ['Tepat Waktu', 'Terlambat', 'Dalam Proses'],
                    datasets: [{
                        data: [{{ $completedOnTime }}, {{ $completedLate }}, {{ $pendingTasks }}],
                        backgroundColor: ['#4ade80', '#fbbf24', '#a78bfa'],
                        borderWidth: 0,
                        hoverOffset: 4
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
                                    family: 'Inter',
                                    size: 12
                                },
                                padding: 20
                            }
                        }
                    },
                    cutout: '70%',
                    animation: {
                        animateScale: true,
                        animateRotate: true
                    }
                }
            });

            // Performance Chart
            const interns = [
                @foreach ($interns as $intern)
                    {
                        name: "{{ $intern->user->name }}",
                        on_time: {{ $intern->getCompletedOnTimeCount() }},
                        late: {{ $intern->getCompletedLateCount() }},
                    },
                @endforeach
            ];

            new Chart(document.getElementById('performanceChart').getContext('2d'), {
                type: 'line',
                data: {
                    labels: interns.map(i => i.name),
                    datasets: [{
                            label: 'Tepat Waktu',
                            data: interns.map(i => i.on_time),
                            borderColor: '#22c55e',
                            backgroundColor: 'rgba(34, 197, 94, 0.15)',
                            borderWidth: 3,
                            fill: true,
                            tension: 0.4,
                            pointRadius: 6,
                            pointBackgroundColor: '#22c55e',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
                            pointHoverRadius: 8,
                            pointHoverBackgroundColor: '#16a34a',
                        },
                        {
                            label: 'Terlambat',
                            data: interns.map(i => i.late),
                            borderColor: '#f59e0b',
                            backgroundColor: 'rgba(245, 158, 11, 0.15)',
                            borderWidth: 3,
                            fill: true,
                            tension: 0.4,
                            pointRadius: 6,
                            pointBackgroundColor: '#f59e0b',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
                            pointHoverRadius: 8,
                            pointHoverBackgroundColor: '#d97706',
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        intersect: false,
                        mode: 'index',
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                color: '#64748b',
                                font: {
                                    family: 'Inter',
                                    size: 12,
                                    weight: 500
                                },
                                padding: 8
                            }
                        },
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(226, 232, 240, 0.6)',
                                drawBorder: false,
                            },
                            ticks: {
                                color: '#64748b',
                                font: {
                                    family: 'Inter',
                                    size: 12
                                },
                                padding: 12,
                                stepSize: 1
                            },
                            border: {
                                display: false
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'top',
                            align: 'end',
                            labels: {
                                color: '#64748b',
                                usePointStyle: true,
                                pointStyle: 'circle',
                                padding: 20,
                                font: {
                                    family: 'Inter',
                                    size: 13,
                                    weight: 500
                                }
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(30, 41, 59, 0.95)',
                            titleColor: '#fff',
                            bodyColor: '#e2e8f0',
                            borderColor: 'rgba(255, 255, 255, 0.1)',
                            borderWidth: 1,
                            cornerRadius: 12,
                            padding: 14,
                            titleFont: {
                                family: 'Inter',
                                size: 14,
                                weight: 600
                            },
                            bodyFont: {
                                family: 'Inter',
                                size: 13
                            },
                            displayColors: true,
                            boxWidth: 12,
                            boxHeight: 12,
                            boxPadding: 4
                        }
                    }
                }
            });

            // Attendance Today Donut Chart
            new Chart(document.getElementById('attendanceTodayChart').getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: ['Hadir', 'Terlambat', 'Izin', 'Sakit', 'Belum Absen'],
                    datasets: [{
                        data: [
                            {{ $attendanceToday['present'] }},
                            {{ $attendanceToday['late'] }},
                            {{ $attendanceToday['permission'] }},
                            {{ $attendanceToday['sick'] }},
                            {{ $attendanceToday['absent'] }}
                        ],
                        backgroundColor: ['#10b981', '#f59e0b', '#3b82f6', '#a855f7', '#ef4444'],
                        borderWidth: 0,
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'right',
                            labels: {
                                color: '#64748b',
                                font: {
                                    family: 'Inter',
                                    size: 11
                                },
                                padding: 12,
                                usePointStyle: true,
                                pointStyle: 'circle'
                            }
                        }
                    },
                    cutout: '65%'
                }
            });

            // Attendance Trend Bar Chart
            const trendData = @json($attendanceTrend);
            new Chart(document.getElementById('attendanceTrendChart').getContext('2d'), {
                type: 'bar',
                data: {
                    labels: trendData.map(d => d.date),
                    datasets: [{
                            label: 'Hadir',
                            data: trendData.map(d => d.present),
                            backgroundColor: '#10b981',
                            borderRadius: 6,
                            barThickness: 20
                        },
                        {
                            label: 'Tidak Hadir',
                            data: trendData.map(d => d.absent),
                            backgroundColor: '#ef4444',
                            borderRadius: 6,
                            barThickness: 20
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: {
                            stacked: true,
                            grid: {
                                display: false
                            },
                            ticks: {
                                color: '#64748b',
                                font: {
                                    size: 11
                                }
                            }
                        },
                        y: {
                            stacked: true,
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(226, 232, 240, 0.6)'
                            },
                            ticks: {
                                color: '#64748b',
                                stepSize: 1,
                                font: {
                                    size: 11
                                }
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'top',
                            align: 'end',
                            labels: {
                                color: '#64748b',
                                usePointStyle: true,
                                padding: 16,
                                font: {
                                    size: 12
                                }
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(30, 41, 59, 0.95)',
                            titleColor: '#fff',
                            bodyColor: '#e2e8f0',
                            borderColor: 'rgba(255, 255, 255, 0.1)',
                            borderWidth: 1,
                            cornerRadius: 8,
                            padding: 10,
                            titleFont: {
                                family: 'Inter',
                                size: 13,
                                weight: 600
                            },
                            bodyFont: {
                                family: 'Inter',
                                size: 12
                            }
                        }
                    }
                }
            });
        </script>
    @endpush
@endsection
