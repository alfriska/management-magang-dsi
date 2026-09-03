<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Laporan Magang - {{ $intern->user->name }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            line-height: 1.5;
            color: #1e293b;
            background: #fff;
        }

        .page {
            padding: 30px 40px;
        }

        /* Header */
        .header {
            border-bottom: 3px solid #a78bfa;
            padding-bottom: 20px;
            margin-bottom: 25px;
        }

        .header-content {
            display: table;
            width: 100%;
        }

        .logo-section {
            display: table-cell;
            width: 80px;
            vertical-align: top;
        }

        .logo {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #a78bfa, #c084fc);
            border-radius: 10px;
            text-align: center;
            line-height: 60px;
            color: white;
            font-size: 24px;
            font-weight: bold;
        }

        .title-section {
            display: table-cell;
            vertical-align: top;
            padding-left: 15px;
        }

        .company-name {
            font-size: 18px;
            font-weight: bold;
            color: #a78bfa;
            margin-bottom: 3px;
        }

        .report-title {
            font-size: 14px;
            color: #64748b;
        }

        .report-date {
            font-size: 10px;
            color: #94a3b8;
            margin-top: 5px;
        }

        /* Profile Section */
        .profile-section {
            background: linear-gradient(135deg, #f5f3ff, #ede9fe);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .profile-content {
            display: table;
            width: 100%;
        }

        .avatar-section {
            display: table-cell;
            width: 100px;
            vertical-align: top;
        }

        .avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            border: 3px solid #a78bfa;
            object-fit: cover;
        }

        .avatar-placeholder {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(135deg, #a78bfa, #c084fc);
            text-align: center;
            line-height: 80px;
            color: white;
            font-size: 28px;
            font-weight: bold;
            border: 3px solid #a78bfa;
        }

        .profile-info {
            display: table-cell;
            vertical-align: top;
            padding-left: 15px;
        }

        .intern-name {
            font-size: 20px;
            font-weight: bold;
            color: #7c3aed;
            margin-bottom: 5px;
        }

        .intern-details {
            color: #6d28d9;
            font-size: 11px;
        }

        .intern-details p {
            margin-bottom: 3px;
        }

        .badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 15px;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .badge-success {
            background: #a78bfa;
            color: white;
        }

        .badge-warning {
            background: #f59e0b;
            color: white;
        }

        .badge-info {
            background: #3b82f6;
            color: white;
        }

        /* Progress Bar */
        .progress-section {
            margin-top: 15px;
        }

        .progress-label {
            font-size: 10px;
            color: #7c3aed;
            margin-bottom: 5px;
        }

        .progress-bar-bg {
            height: 8px;
            background: #ddd6fe;
            border-radius: 4px;
            overflow: hidden;
        }

        .progress-bar-fill {
            height: 100%;
            background: linear-gradient(90deg, #a78bfa, #c084fc);
            border-radius: 4px;
        }

        /* Stats Grid */
        .stats-grid {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }

        .stat-card {
            display: table-cell;
            width: 25%;
            padding: 5px;
            vertical-align: top;
        }

        .stat-card-inner {
            background: #f8fafc;
            border-radius: 10px;
            padding: 15px;
            text-align: center;
            border: 1px solid #e2e8f0;
        }

        .stat-icon {
            font-size: 20px;
            color: #a78bfa;
            margin-bottom: 5px;
        }

        .stat-value {
            font-size: 24px;
            font-weight: bold;
            color: #1e293b;
        }

        .stat-label {
            font-size: 9px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Section Title */
        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #1e293b;
            margin-bottom: 10px;
            padding-bottom: 8px;
            border-bottom: 2px solid #e2e8f0;
        }

        .section-title span {
            color: #a78bfa;
        }

        /* Two Column Layout */
        .two-column {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }

        .column {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }

        .column:first-child {
            padding-right: 10px;
        }

        .column:last-child {
            padding-left: 10px;
        }

        /* Info Box */
        .info-box {
            background: #f8fafc;
            border-radius: 8px;
            padding: 12px;
            margin-bottom: 10px;
            border-left: 3px solid #a78bfa;
        }

        .info-box-title {
            font-size: 10px;
            color: #64748b;
            text-transform: uppercase;
            margin-bottom: 3px;
        }

        .info-box-value {
            font-size: 18px;
            font-weight: bold;
            color: #1e293b;
        }

        .info-box-subtitle {
            font-size: 9px;
            color: #94a3b8;
        }

        /* Table */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
            margin-bottom: 15px;
        }

        .data-table th {
            background: #f1f5f9;
            padding: 8px 10px;
            text-align: left;
            font-weight: bold;
            color: #475569;
            border-bottom: 2px solid #e2e8f0;
        }

        .data-table td {
            padding: 8px 10px;
            border-bottom: 1px solid #e2e8f0;
        }

        .data-table tr:last-child td {
            border-bottom: none;
        }

        .data-table tr:nth-child(even) {
            background: #f8fafc;
        }

        /* Assessment Scores */
        .assessment-grid {
            display: table;
            width: 100%;
        }

        .assessment-item {
            display: table-cell;
            width: 20%;
            padding: 5px;
            text-align: center;
        }

        .assessment-score {
            font-size: 20px;
            font-weight: bold;
            color: #a78bfa;
        }

        .assessment-label {
            font-size: 8px;
            color: #64748b;
            text-transform: uppercase;
        }

        /* Attendance Summary */
        .attendance-summary {
            display: table;
            width: 100%;
        }

        .attendance-item {
            display: table-cell;
            width: 16.66%;
            padding: 5px;
            text-align: center;
        }

        .attendance-count {
            font-size: 18px;
            font-weight: bold;
        }

        .attendance-count.present {
            color: #a78bfa;
        }

        .attendance-count.late {
            color: #f59e0b;
        }

        .attendance-count.absent {
            color: #ef4444;
        }

        .attendance-count.sick {
            color: #3b82f6;
        }

        .attendance-count.permission {
            color: #8b5cf6;
        }

        .attendance-type {
            font-size: 8px;
            color: #64748b;
            text-transform: uppercase;
        }

        /* Footer */
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #e2e8f0;
            text-align: center;
            color: #94a3b8;
            font-size: 9px;
        }

        .signature-section {
            display: table;
            width: 100%;
            margin-top: 40px;
        }

        .signature-box {
            display: table-cell;
            width: 33.33%;
            text-align: center;
            padding: 10px;
        }

        .signature-line {
            border-bottom: 1px solid #1e293b;
            margin-bottom: 5px;
            height: 50px;
        }

        .signature-name {
            font-size: 10px;
            font-weight: bold;
        }

        .signature-title {
            font-size: 9px;
            color: #64748b;
        }

        /* Page Break */
        .page-break {
            page-break-after: always;
        }
    </style>
</head>

<body>
    <div class="page">
        <!-- Header -->
        <div class="header">
            <div class="header-content">
                <div class="logo-section">
                    <div class="logo">M</div>
                </div>
                <div class="title-section">
                    <div class="company-name">Sistem Manajemen Magang</div>
                    <div class="report-title">Laporan Kinerja Peserta Magang</div>
                    <div class="report-date">Dibuat pada: {{ $generatedAt }}</div>
                </div>
            </div>
        </div>

        <!-- Profile Section -->
        <div class="profile-section">
            <div class="profile-content">
                <div class="avatar-section">
                    @if ($avatarUrl)
                        <img src="{{ $avatarUrl }}" class="avatar" alt="Avatar">
                    @else
                        <div class="avatar-placeholder">
                            {{ strtoupper(substr($intern->user->name, 0, 1)) }}
                        </div>
                    @endif
                </div>
                <div class="profile-info">
                    <div class="intern-name">{{ $intern->user->name }}</div>
                    <div class="intern-details">
                        <p><strong>NIS:</strong> {{ $intern->nis ?? '-' }}</p>
                        <p><strong>Sekolah:</strong> {{ $intern->school ?? '-' }}</p>
                        <p><strong>Jurusan:</strong> {{ $intern->department ?? '-' }}</p>
                        <p><strong>Pembimbing:</strong> {{ $intern->supervisor->name ?? '-' }}</p>
                        <p><strong>Periode:</strong> {{ $intern->start_date?->format('d M Y') }} -
                            {{ $intern->end_date?->format('d M Y') ?? 'Sekarang' }}</p>
                        <p style="margin-top: 8px;">
                            <span class="badge {{ $intern->status === 'active' ? 'badge-success' : 'badge-warning' }}">
                                {{ $intern->status === 'active' ? 'Aktif' : 'Tidak Aktif' }}
                            </span>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Progress -->
            <div class="progress-section">
                <div class="progress-label">Progress Magang: {{ $progress }}% ({{ $daysCompleted }} dari
                    {{ $duration }}
                    hari)</div>
                <div class="progress-bar-bg">
                    <div class="progress-bar-fill" style="width: {{ $progress }}%;"></div>
                </div>
            </div>
        </div>

        <!-- Overview Stats -->
        <div class="section-title"><span>📊</span> Ringkasan Kinerja</div>
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-card-inner">
                    <div class="stat-value">{{ $taskStats['completed'] }}/{{ $taskStats['total'] }}</div>
                    <div class="stat-label">Tugas Selesai</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-card-inner">
                    <div class="stat-value">{{ $attendanceStats['percentage'] }}%</div>
                    <div class="stat-label">Kehadiran</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-card-inner">
                    <div class="stat-value">{{ number_format($taskStats['average_score'], 1) }}</div>
                    <div class="stat-label">Rata-rata Nilai</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-card-inner">
                    <div class="stat-value">{{ $assessmentStats['overall'] }}</div>
                    <div class="stat-label">Skor Penilaian</div>
                </div>
            </div>
        </div>

        <!-- Task Statistics -->
        <div class="two-column">
            <div class="column">
                <div class="section-title"><span>📋</span> Statistik Tugas</div>
                <div class="info-box">
                    <div class="info-box-title">Total Tugas</div>
                    <div class="info-box-value">{{ $taskStats['total'] }}</div>
                </div>
                <div class="info-box" style="border-color: #a78bfa;">
                    <div class="info-box-title">Selesai Tepat Waktu</div>
                    <div class="info-box-value" style="color: #a78bfa;">{{ $taskStats['on_time'] }}</div>
                </div>
                <div class="info-box" style="border-color: #f59e0b;">
                    <div class="info-box-title">Selesai Terlambat</div>
                    <div class="info-box-value" style="color: #f59e0b;">{{ $taskStats['late'] }}</div>
                </div>
                <div class="info-box" style="border-color: #3b82f6;">
                    <div class="info-box-title">Dalam Proses</div>
                    <div class="info-box-value" style="color: #3b82f6;">{{ $taskStats['in_progress'] }}</div>
                </div>
                <div class="info-box" style="border-color: #64748b;">
                    <div class="info-box-title">Menunggu</div>
                    <div class="info-box-value" style="color: #64748b;">{{ $taskStats['pending'] }}</div>
                </div>
            </div>
            <div class="column">
                <div class="section-title"><span>📅</span> Statistik Kehadiran</div>
                <div class="info-box">
                    <div class="info-box-title">Total Hari Kerja</div>
                    <div class="info-box-value">{{ $attendanceStats['total'] }}</div>
                </div>
                <div class="attendance-summary">
                    <div class="attendance-item">
                        <div class="attendance-count present">{{ $attendanceStats['present'] }}</div>
                        <div class="attendance-type">Hadir</div>
                    </div>
                    <div class="attendance-item">
                        <div class="attendance-count late">{{ $attendanceStats['late'] }}</div>
                        <div class="attendance-type">Terlambat</div>
                    </div>
                    <div class="attendance-item">
                        <div class="attendance-count absent">{{ $attendanceStats['absent'] }}</div>
                        <div class="attendance-type">Absen</div>
                    </div>
                    <div class="attendance-item">
                        <div class="attendance-count sick">{{ $attendanceStats['sick'] }}</div>
                        <div class="attendance-type">Sakit</div>
                    </div>
                    <div class="attendance-item">
                        <div class="attendance-count permission">{{ $attendanceStats['permission'] }}</div>
                        <div class="attendance-type">Izin</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Assessment Scores -->
        @if ($assessmentStats['count'] > 0)
            <div class="section-title"><span>⭐</span> Penilaian Kompetensi</div>
            <div class="assessment-grid" style="margin-bottom: 20px;">
                <div class="assessment-item">
                    <div class="assessment-score">{{ $assessmentStats['quality'] }}</div>
                    <div class="assessment-label">Kualitas</div>
                </div>
                <div class="assessment-item">
                    <div class="assessment-score">{{ $assessmentStats['speed'] }}</div>
                    <div class="assessment-label">Kecepatan</div>
                </div>
                <div class="assessment-item">
                    <div class="assessment-score">{{ $assessmentStats['initiative'] }}</div>
                    <div class="assessment-label">Inisiatif</div>
                </div>
                <div class="assessment-item">
                    <div class="assessment-score">{{ $assessmentStats['teamwork'] }}</div>
                    <div class="assessment-label">Kerjasama</div>
                </div>
                <div class="assessment-item">
                    <div class="assessment-score">{{ $assessmentStats['communication'] }}</div>
                    <div class="assessment-label">Komunikasi</div>
                </div>
            </div>
        @endif

        <!-- Recent Tasks Table -->
        <div class="section-title"><span>📝</span> Daftar Tugas Terakhir</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 40%;">Judul Tugas</th>
                    <th style="width: 15%;">Status</th>
                    <th style="width: 15%;">Nilai</th>
                    <th style="width: 15%;">Deadline</th>
                    <th style="width: 15%;">Waktu</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentTasks as $task)
                    <tr>
                        <td>{{ Str::limit($task->title, 35) }}</td>
                        <td>
                            @if ($task->status === 'completed')
                                <span style="color: #a78bfa;">✓ Selesai</span>
                            @elseif($task->status === 'in_progress')
                                <span style="color: #3b82f6;">⏳ Proses</span>
                            @elseif($task->status === 'revision')
                                <span style="color: #f59e0b;">🔄 Revisi</span>
                            @else
                                <span style="color: #64748b;">⏸ Menunggu</span>
                            @endif
                        </td>
                        <td>{{ $task->score ?? '-' }}</td>
                        <td>{{ $task->deadline?->format('d/m/Y') ?? '-' }}</td>
                        <td>
                            @if ($task->is_late)
                                <span style="color: #f59e0b;">Terlambat</span>
                            @elseif($task->status === 'completed')
                                <span style="color: #a78bfa;">Tepat</span>
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align: center; color: #94a3b8;">Belum ada tugas</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Recent Attendances Table -->
        <div class="section-title"><span>📆</span> Riwayat Kehadiran Terakhir</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 25%;">Tanggal</th>
                    <th style="width: 20%;">Status</th>
                    <th style="width: 15%;">Presensi Masuk</th>
                    <th style="width: 15%;">Presensi Keluar</th>
                    <th style="width: 25%;">Catatan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentAttendances as $attendance)
                    <tr>
                        <td>{{ $attendance->date->format('d M Y') }}</td>
                        <td>
                            @if ($attendance->status === 'present')
                                <span style="color: #a78bfa;">✓ Hadir</span>
                            @elseif($attendance->status === 'late')
                                <span style="color: #f59e0b;">⏰ Terlambat</span>
                            @elseif($attendance->status === 'absent')
                                <span style="color: #ef4444;">✗ Absen</span>
                            @elseif($attendance->status === 'sick')
                                <span style="color: #3b82f6;">🏥 Sakit</span>
                            @else
                                <span style="color: #8b5cf6;">📋 Izin</span>
                            @endif
                        </td>
                        <td>{{ $attendance->check_in ? \Carbon\Carbon::parse($attendance->check_in)->format('H:i') : '-' }}
                        </td>
                        <td>{{ $attendance->check_out ? \Carbon\Carbon::parse($attendance->check_out)->format('H:i') : '-' }}
                        </td>
                        <td>{{ Str::limit($attendance->notes, 20) ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align: center; color: #94a3b8;">Belum ada data kehadiran</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Signature Section -->
        <div class="signature-section">
            <div class="signature-box">
                <div class="signature-line"></div>
                <div class="signature-name">{{ $intern->user->name }}</div>
                <div class="signature-title">Peserta Magang</div>
            </div>
            <div class="signature-box">
                <div class="signature-line"></div>
                <div class="signature-name">{{ $intern->supervisor->name ?? '____________________' }}</div>
                <div class="signature-title">Pembimbing</div>
            </div>
            <div class="signature-box">
                <div class="signature-line"></div>
                <div class="signature-name">____________________</div>
                <div class="signature-title">Kepala Divisi</div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>Dokumen ini dibuat secara otomatis oleh Sistem Manajemen Magang</p>
            <p>© {{ date('Y') }} - Semua data bersifat rahasia dan hanya untuk keperluan internal</p>
        </div>
    </div>
</body>

</html>
