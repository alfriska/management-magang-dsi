@extends('layouts.app')

@section('title', 'Profil Saya')

@section('content')
    <div class="slide-up">
        <!-- Profile Header -->
        <div class="card mb-6"
            style="background: linear-gradient(135deg, rgba(99, 102, 241, 0.2), rgba(168, 85, 247, 0.2)); border-color: rgba(99, 102, 241, 0.3);">
            <div class="d-flex align-center gap-4" style="flex-wrap: wrap;">
                @if ($user->avatar)
                    <img src="{{ Str::startsWith($user->avatar, ['http', 'https']) ? $user->avatar : asset('storage/avatars/' . $user->avatar) }}"
                        alt="Avatar" class="user-avatar" referrerpolicy="no-referrer"
                        style="width: 100px; height: 100px; font-size: 40px; flex-shrink: 0; object-fit: cover; border-radius: 50%;">
                @else
                    <div class="user-avatar" style="width: 100px; height: 100px; font-size: 40px; flex-shrink: 0;">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                @endif
                <div style="flex: 1;">
                    <h2 style="font-size: 28px; margin-bottom: 4px;">{{ $user->name }}</h2>
                    <p class="text-muted">{{ $user->email }}</p>
                    <div class="mt-4">
                        <span class="badge badge-primary" style="font-size: 14px; padding: 8px 16px;">
                            {{ ucfirst($user->role) }}
                        </span>
                        @if ($intern)
                            <span class="badge badge-{{ $intern->status === 'active' ? 'success' : 'secondary' }}"
                                style="font-size: 14px; padding: 8px 16px; margin-left: 8px;">
                                {{ $intern->status === 'active' ? 'Aktif' : ucfirst($intern->status) }}
                            </span>
                        @endif
                    </div>
                </div>
                <a href="{{ route('profile.edit') }}" class="btn btn-secondary">
                    <i class="fas fa-cog"></i> Edit Profil
                </a>
            </div>
        </div>

        @if ($intern)
            <!-- Stats Grid -->
            <div class="stat-grid">
                <div class="stat-card">
                    <div class="stat-icon primary">
                        <i class="fas fa-tasks"></i>
                    </div>
                    <div class="stat-value">{{ $stats['completedTasks'] }} / {{ $stats['totalTasks'] }}</div>
                    <div class="stat-label">Tugas Diselesaikan</div>
                    <div class="progress mt-4">
                        <div class="progress-bar"
                            style="width: {{ $stats['totalTasks'] > 0 ? ($stats['completedTasks'] / $stats['totalTasks']) * 100 : 0 }}%">
                        </div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon success">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div class="stat-value">{{ $stats['attendancePercentage'] }}%</div>
                    <div class="stat-label">Tingkat Kehadiran</div>
                    <div class="progress mt-4">
                        <div class="progress-bar"
                            style="width: {{ $stats['attendancePercentage'] }}%; background: linear-gradient(90deg, #22c55e, #16a34a);">
                        </div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon info">
                        <i class="fas fa-tachometer-alt"></i>
                    </div>
                    <div class="stat-value">{{ $stats['averageSpeed'] }}%</div>
                    <div class="stat-label">Kecepatan Mengerjakan</div>
                    <div class="progress mt-4">
                        <div class="progress-bar"
                            style="width: {{ min($stats['averageSpeed'], 100) }}%; background: linear-gradient(90deg, #06b6d4, #0891b2);">
                        </div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon warning">
                        <i class="fas fa-star"></i>
                    </div>
                    <div class="stat-value">{{ $stats['overallScore'] }}</div>
                    <div class="stat-label">Skor Rata-rata</div>
                    <div class="progress mt-4">
                        <div class="progress-bar"
                            style="width: {{ $stats['overallScore'] }}%; background: linear-gradient(90deg, #f59e0b, #d97706);">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Grid -->
            <div class="grid-2 mt-6">
                <!-- Task Status Pie Chart -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-chart-pie"></i> Status Pekerjaan</h3>
                    </div>
                    <div class="chart-container" style="height: 280px;">
                        <canvas id="taskPieChart"></canvas>
                    </div>
                </div>

                <!-- Attendance Pie Chart -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-chart-pie"></i> Status Kehadiran</h3>
                    </div>
                    <div class="chart-container" style="height: 280px;">
                        <canvas id="attendancePieChart"></canvas>
                    </div>
                </div>
            </div>

            @if (!empty($assessmentData))
                <!-- Assessment Radar Chart -->
                <div class="card mt-6">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-chart-radar"></i> Radar Penilaian (Rata-rata dari 5
                            Penilaian Terakhir)</h3>
                    </div>
                    <div class="chart-container" style="height: 350px;">
                        <canvas id="assessmentRadarChart"></canvas>
                    </div>
                </div>
            @endif

            <!-- Profile Details -->
            <div class="grid-2 mt-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-user"></i> Informasi Pribadi</h3>
                    </div>
                    <div style="display: grid; gap: 16px;">
                        <div>
                            <label class="text-muted" style="font-size: 12px;">NIS</label>
                            <div><strong>{{ $intern->nis ?? '-' }}</strong></div>
                        </div>
                        <div>
                            <label class="text-muted" style="font-size: 12px;">Asal Sekolah</label>
                            <div><strong>{{ $intern->school }}</strong></div>
                        </div>
                        <div>
                            <label class="text-muted" style="font-size: 12px;">Jurusan</label>
                            <div><strong>{{ $intern->department }}</strong></div>
                        </div>
                        <div>
                            <label class="text-muted" style="font-size: 12px;">No. Telepon</label>
                            <div><strong>{{ $intern->phone ?? '-' }}</strong></div>
                        </div>
                        <div>
                            <label class="text-muted" style="font-size: 12px;">Alamat</label>
                            <div><strong>{{ $intern->address ?? '-' }}</strong></div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-briefcase"></i> Informasi Magang</h3>
                    </div>
                    <div style="display: grid; gap: 16px;">
                        <div>
                            <label class="text-muted" style="font-size: 12px;">Pembimbing</label>
                            <div class="d-flex align-center gap-2">
                                @if ($intern->supervisor)
                                    @if ($intern->supervisor->avatar)
                                        <img src="{{ Str::startsWith($intern->supervisor->avatar, ['http', 'https']) ? $intern->supervisor->avatar : asset('storage/avatars/' . $intern->supervisor->avatar) }}"
                                            alt="{{ $intern->supervisor->name }}"
                                            style="width: 32px; height: 32px; font-size: 12px; object-fit: cover; border-radius: 50%;"
                                            referrerpolicy="no-referrer">
                                    @else
                                        <div class="user-avatar" style="width: 32px; height: 32px; font-size: 12px;">
                                            {{ strtoupper(substr($intern->supervisor->name, 0, 1)) }}
                                        </div>
                                    @endif
                                    <strong>{{ $intern->supervisor->name }}</strong>
                                @else
                                    <span class="text-muted">Belum ditentukan</span>
                                @endif
                            </div>
                        </div>
                        <div>
                            <label class="text-muted" style="font-size: 12px;">Periode Magang</label>
                            <div><strong>{{ $intern->start_date->format('d M Y') }} -
                                    {{ $intern->end_date->format('d M Y') }}</strong></div>
                        </div>
                        <div>
                            <label class="text-muted" style="font-size: 12px;">Durasi</label>
                            <div><strong>{{ $intern->start_date->diffInDays($intern->end_date) }} Hari</strong></div>
                        </div>
                        <div>
                            <label class="text-muted" style="font-size: 12px;">Sisa Waktu</label>
                            <div>
                                @if ($intern->end_date->isPast())
                                    <span class="badge badge-secondary">Telah Berakhir</span>
                                @else
                                    <strong>{{ now()->diffInDays($intern->end_date) }} Hari Lagi</strong>
                                @endif
                            </div>
                        </div>
                        <div>
                            <label class="text-muted" style="font-size: 12px;">Status</label>
                            <div>
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
            </div>
        @else
            <!-- Non-intern profile -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-user"></i> Informasi Akun</h3>
                </div>
                <div class="grid-2">
                    <div>
                        <label class="text-muted" style="font-size: 12px;">Email</label>
                        <div><strong>{{ $user->email }}</strong></div>
                    </div>
                    <div>
                        <label class="text-muted" style="font-size: 12px;">Role</label>
                        <div><span class="badge badge-primary">{{ ucfirst($user->role) }}</span></div>
                    </div>
                    <div>
                        <label class="text-muted" style="font-size: 12px;">Akun Dibuat</label>
                        <div><strong>{{ $user->created_at->format('d M Y') }}</strong></div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    @if ($intern)
        @push('scripts')
            <script>
                // Task Status Pie Chart
                const taskData = @json($taskStatusData);
                const taskLabels = Object.keys(taskData);
                const taskValues = Object.values(taskData);

                new Chart(document.getElementById('taskPieChart').getContext('2d'), {
                    type: 'doughnut',
                    data: {
                        labels: taskLabels,
                        datasets: [{
                            data: taskValues,
                            backgroundColor: ['#22c55e', '#6366f1', '#71717a', '#f59e0b'],
                            borderWidth: 0,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    color: '#a1a1aa',
                                    font: {
                                        family: 'Inter'
                                    }
                                }
                            }
                        },
                        cutout: '65%'
                    }
                });

                // Attendance Pie Chart
                const attData = @json($attendanceData);
                const attLabels = Object.keys(attData);
                const attValues = Object.values(attData);

                new Chart(document.getElementById('attendancePieChart').getContext('2d'), {
                    type: 'doughnut',
                    data: {
                        labels: attLabels,
                        datasets: [{
                            data: attValues,
                            backgroundColor: ['#22c55e', '#f59e0b', '#ef4444', '#06b6d4', '#6366f1'],
                            borderWidth: 0,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    color: '#a1a1aa',
                                    font: {
                                        family: 'Inter'
                                    }
                                }
                            }
                        },
                        cutout: '65%'
                    }
                });

                @if (!empty($assessmentData))
                    // Assessment Radar Chart
                    const assessData = @json($assessmentData);
                    const assessLabels = Object.keys(assessData);
                    const assessValues = Object.values(assessData);

                    new Chart(document.getElementById('assessmentRadarChart').getContext('2d'), {
                        type: 'radar',
                        data: {
                            labels: assessLabels,
                            datasets: [{
                                label: 'Skor Rata-rata',
                                data: assessValues,
                                backgroundColor: 'rgba(99, 102, 241, 0.2)',
                                borderColor: 'rgba(99, 102, 241, 1)',
                                borderWidth: 2,
                                pointBackgroundColor: 'rgba(99, 102, 241, 1)',
                                pointBorderColor: '#fff',
                                pointRadius: 5,
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                r: {
                                    beginAtZero: true,
                                    max: 100,
                                    ticks: {
                                        stepSize: 20,
                                        color: '#71717a',
                                        backdropColor: 'transparent'
                                    },
                                    grid: {
                                        color: 'rgba(255, 255, 255, 0.1)'
                                    },
                                    angleLines: {
                                        color: 'rgba(255, 255, 255, 0.1)'
                                    },
                                    pointLabels: {
                                        color: '#a1a1aa',
                                        font: {
                                            family: 'Inter',
                                            size: 13,
                                            weight: 500
                                        }
                                    }
                                }
                            },
                            plugins: {
                                legend: {
                                    display: false
                                }
                            }
                        }
                    });
                @endif
            </script>
        @endpush
    @endif
@endsection
