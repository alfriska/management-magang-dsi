@extends('layouts.app')

@section('title', 'Detail Presensi')

@section('content')
    <div class="slide-up">
        <div class="d-flex align-center gap-4 mb-6">
            <a href="{{ route('attendances.index') }}" class="btn btn-secondary btn-icon">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h2 style="margin-bottom: 4px;">Detail Presensi</h2>
                <p class="text-muted">
                    @if ($attendance->intern)
                        {{ $attendance->intern->user->name }} - {{ $attendance->date->format('d M Y') }}
                    @else
                        <span class="badge badge-secondary">Siswa Dihapus</span> - {{ $attendance->date->format('d M Y') }}
                    @endif
                </p>
            </div>
        </div>

        <div class="grid-2">
            <!-- Main Info Card -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-info-circle"></i> Informasi Presensi
                    </h3>
                </div>

                <div style="padding: 20px;">
                    <!-- Status Badge -->
                    <div class="mb-6 text-center"
                        style="padding: 24px; background: var(--bg-tertiary); border-radius: var(--radius-md);">
                        <div class="badge badge-{{ $attendance->status_color }}"
                            style="font-size: 18px; padding: 12px 32px;">
                            @switch($attendance->status)
                                @case('present')
                                    <i class="fas fa-check-circle"></i> Hadir (Tepat Waktu)
                                @break

                                @case('late')
                                    <i class="fas fa-clock"></i> Hadir (Terlambat)
                                @break

                                @case('absent')
                                    <i class="fas fa-times-circle"></i> Tidak Hadir
                                @break

                                @case('sick')
                                    <i class="fas fa-medkit"></i> Sakit
                                @break

                                @case('permission')
                                    <i class="fas fa-file-alt"></i> Izin
                                @break
                            @endswitch
                        </div>
                    </div>

                    <div class="info-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div>
                            <label class="text-muted"
                                style="font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;">Tanggal</label>
                            <div class="fw-bold" style="margin-top: 4px;">
                                {{ $attendance->date->format('l, d F Y') }}
                            </div>
                        </div>

                        <div>
                            <label class="text-muted"
                                style="font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;">Siswa</label>
                            <div class="fw-bold" style="margin-top: 4px;">
                                @if ($attendance->intern)
                                    {{ $attendance->intern->user->name }}
                                @else
                                    <span class="text-muted"><i>Siswa telah dihapus</i></span>
                                @endif
                            </div>
                        </div>

                        <div>
                            <label class="text-muted"
                                style="font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;">Presensi
                                Masuk</label>
                            <div class="fw-bold" style="margin-top: 4px; font-size: 24px; color: var(--success);">
                                {{ $attendance->check_in ?? '-' }}
                            </div>
                        </div>

                        <div>
                            <label class="text-muted"
                                style="font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;">Presensi
                                Keluar</label>
                            <div class="fw-bold" style="margin-top: 4px; font-size: 24px; color: var(--info);">
                                {{ $attendance->check_out ?? '-' }}
                            </div>
                        </div>
                    </div>

                    @if ($attendance->working_hours)
                        <div class="mt-6"
                            style="padding: 16px; background: var(--bg-tertiary); border-radius: var(--radius-md); text-align: center;">
                            <label class="text-muted" style="font-size: 12px; text-transform: uppercase;">Total Jam
                                Kerja</label>
                            <div class="fw-bold" style="font-size: 28px; color: var(--accent-primary);">
                                {{ $attendance->getWorkingHours() }} Jam
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Late Reason Card (if applicable) -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-comment-alt"></i> Detail Tambahan
                    </h3>
                </div>

                <div style="padding: 20px;">
                    @if ($attendance->status === 'late')
                        <div class="mb-6">
                            <label class="text-muted"
                                style="font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;">
                                <i class="fas fa-exclamation-triangle text-warning"></i> Alasan Keterlambatan
                            </label>
                            <div
                                style="margin-top: 8px; padding: 16px; background: rgba(234, 179, 8, 0.1); border: 1px solid rgba(234, 179, 8, 0.3); border-radius: var(--radius-md);">
                                @if ($attendance->late_reason)
                                    <p style="margin: 0;">{{ $attendance->late_reason }}</p>
                                @else
                                    <p class="text-muted" style="margin: 0; font-style: italic;">Tidak ada alasan yang
                                        diberikan</p>
                                @endif
                            </div>
                        </div>
                    @endif

                    <div class="mb-6">
                        <label class="text-muted"
                            style="font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;">Catatan</label>
                        <div
                            style="margin-top: 8px; padding: 16px; background: var(--bg-tertiary); border-radius: var(--radius-md);">
                            @if ($attendance->notes)
                                <p style="margin: 0;">{{ $attendance->notes }}</p>
                            @else
                                <p class="text-muted" style="margin: 0; font-style: italic;">Tidak ada catatan</p>
                            @endif
                        </div>
                    </div>

                    @if ($attendance->intern)
                        <div style="padding: 16px; background: var(--bg-tertiary); border-radius: var(--radius-md);">
                            <label class="text-muted"
                                style="font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px; display: block;">Info
                                Siswa</label>
                            <div class="d-flex align-center gap-3">
                                @if ($attendance->intern->user->avatar)
                                    <img src="{{ Str::startsWith($attendance->intern->user->avatar, ['http', 'https']) ? $attendance->intern->user->avatar : asset('storage/avatars/' . $attendance->intern->user->avatar) }}"
                                        alt="{{ $attendance->intern->user->name }}"
                                        style="width: 48px; height: 48px; object-fit: cover; border-radius: 50%;"
                                        referrerpolicy="no-referrer">
                                @else
                                    <div class="user-avatar" style="width: 48px; height: 48px;">
                                        {{ strtoupper(substr($attendance->intern->user->name, 0, 1)) }}
                                    </div>
                                @endif
                                <div>
                                    <div class="fw-bold">{{ $attendance->intern->user->name }}</div>
                                    <div class="text-muted" style="font-size: 13px;">{{ $attendance->intern->school }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Proof File Card (if exists) -->
            @if ($attendance->proof_file)
                <div class="card mt-6">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-paperclip text-primary"></i> Bukti Lampiran
                        </h3>
                    </div>
                    <div style="padding: 20px; text-align: center;">
                        @php
                            $extension = pathinfo($attendance->proof_file, PATHINFO_EXTENSION);
                            $isImage = in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                        @endphp

                        @if ($isImage)
                            <div
                                style="border-radius: 12px; overflow: hidden; border: 1px solid #e5e7eb; display: inline-block;">
                                <img src="{{ asset('storage/' . $attendance->proof_file) }}" alt="Bukti Izin"
                                    style="max-width: 100%; max-height: 400px; display: block;">
                            </div>
                            <div class="mt-3">
                                <a href="{{ asset('storage/' . $attendance->proof_file) }}" target="_blank"
                                    class="btn btn-sm btn-primary">
                                    <i class="fas fa-expand"></i> Lihat Ukuran Penuh
                                </a>
                            </div>
                        @else
                            <div
                                style="padding: 40px; background: #f9fafb; border-radius: 12px; border: 1px dashed #d1d5db;">
                                <i class="fas fa-file-pdf text-danger" style="font-size: 48px; margin-bottom: 16px;"></i>
                                <h4 style="margin: 0 0 8px;">Dokumen Lampiran</h4>
                                <p class="text-muted mb-4">File bertipe {{ strtoupper($extension) }}</p>
                                <a href="{{ asset('storage/' . $attendance->proof_file) }}" target="_blank"
                                    class="btn btn-primary">
                                    <i class="fas fa-download"></i> Unduh / Lihat Dokumen
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        <!-- Location Map Card -->
        <div class="card mt-6">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-map-marker-alt text-danger"></i> Lokasi Check-In
                </h3>
            </div>
            <div style="padding: 20px;">
                @if ($attendance->latitude && $attendance->longitude)
                    <div id="location-map"
                        style="height: 350px; border-radius: var(--radius-md); border: 2px solid var(--border-color);">
                    </div>

                    <div class="d-flex gap-4 mt-4" style="flex-wrap: wrap;">
                        <div
                            style="flex: 1; min-width: 200px; padding: 16px; background: var(--bg-tertiary); border-radius: var(--radius-md);">
                            <label class="text-muted" style="font-size: 12px; text-transform: uppercase;">Latitude</label>
                            <div class="fw-bold" style="font-family: monospace;">{{ $attendance->latitude }}</div>
                        </div>
                        <div
                            style="flex: 1; min-width: 200px; padding: 16px; background: var(--bg-tertiary); border-radius: var(--radius-md);">
                            <label class="text-muted"
                                style="font-size: 12px; text-transform: uppercase;">Longitude</label>
                            <div class="fw-bold" style="font-family: monospace;">{{ $attendance->longitude }}</div>
                        </div>
                        @if ($attendance->distance_meters)
                            <div
                                style="flex: 1; min-width: 200px; padding: 16px; background: var(--bg-tertiary); border-radius: var(--radius-md);">
                                <label class="text-muted" style="font-size: 12px; text-transform: uppercase;">Jarak dari
                                    Kantor</label>
                                <div class="fw-bold" style="font-size: 18px;">
                                    <span
                                        class="{{ $attendance->distance_meters <= 100 ? 'text-success' : 'text-warning' }}">
                                        {{ $attendance->distance_meters }} meter
                                    </span>
                                </div>
                            </div>
                        @endif
                    </div>
                @else
                    <div class="text-center py-5">
                        <div style="font-size: 48px; color: var(--text-muted); margin-bottom: 16px;">
                            <i class="fas fa-map-marker-alt-slash"></i>
                        </div>
                        <h4>Lokasi Tidak Tersedia</h4>
                        <p class="text-muted">Data lokasi tidak tersimpan saat check-in dilakukan.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Actions -->
        <div class="d-flex gap-4 mt-6">
            <a href="{{ route('attendances.edit', $attendance) }}" class="btn btn-primary">
                <i class="fas fa-edit"></i> Edit Presensi
            </a>
            <form action="{{ route('attendances.destroy', $attendance) }}" method="POST"
                onsubmit="return confirm('Yakin ingin menghapus presensi ini?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-trash"></i> Hapus
                </button>
            </form>
        </div>
    </div>

    @if ($attendance->latitude && $attendance->longitude)
        @push('styles')
            <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
                integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
        @endpush

        @push('scripts')
            <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
                integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
            <script>
                // Lokasi Check-in siswa
                const checkInLat = {{ $attendance->latitude }};
                const checkInLon = {{ $attendance->longitude }};

                // Lokasi Kantor
                const officeLat = {{ $officeLat ?? -7.052683 }};
                const officeLon = {{ $officeLon ?? 110.469375 }};
                const maxDistance = {{ $maxDistance ?? 100 }};

                // Initialize Map
                const map = L.map('location-map').setView([checkInLat, checkInLon], 17);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap contributors'
                }).addTo(map);

                // Office Marker & Radius Circle
                const officeMarker = L.marker([officeLat, officeLon], {
                    icon: L.divIcon({
                        className: 'custom-office-marker',
                        html: '<div style="background: #ef4444; color: white; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 16px; box-shadow: 0 2px 8px rgba(0,0,0,0.3);"><i class="fas fa-building"></i></div>',
                        iconSize: [32, 32],
                        iconAnchor: [16, 16]
                    })
                }).addTo(map).bindPopup("<b>Kantor</b><br>Titik Pusat Presensi");

                // Office radius circle
                L.circle([officeLat, officeLon], {
                    color: '#ef4444',
                    fillColor: '#ef4444',
                    fillOpacity: 0.1,
                    radius: maxDistance
                }).addTo(map);

                // Student Check-in Location Marker
                const studentMarker = L.marker([checkInLat, checkInLon], {
                    icon: L.divIcon({
                        className: 'custom-student-marker',
                        html: '<div style="background: #22c55e; color: white; width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 18px; box-shadow: 0 2px 8px rgba(0,0,0,0.3); border: 3px solid white;"><i class="fas fa-user"></i></div>',
                        iconSize: [36, 36],
                        iconAnchor: [18, 18]
                    })
                }).addTo(map).bindPopup(
                    "<b>Lokasi Check-In</b><br>" +
                    "@if ($attendance->intern){{ $attendance->intern->user->name }}@else Siswa @endif<br>" +
                    "<small>{{ $attendance->date->format('d M Y') }} - {{ $attendance->check_in }}</small>"
                ).openPopup();

                // Draw line between office and check-in location
                L.polyline([
                    [officeLat, officeLon],
                    [checkInLat, checkInLon]
                ], {
                    color: '#6366f1',
                    weight: 2,
                    opacity: 0.7,
                    dashArray: '5, 10'
                }).addTo(map);

                // Fit bounds to show both markers
                const group = new L.featureGroup([officeMarker, studentMarker]);
                map.fitBounds(group.getBounds().pad(0.3));
            </script>
        @endpush
    @endif
@endsection
