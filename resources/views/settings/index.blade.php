@extends('layouts.app')

@section('title', 'Pengaturan Sistem')

@section('content')
    <div class="slide-up">
        <div class="mb-6">
            <h2 style="margin-bottom: 4px;">Pengaturan Sistem</h2>
            <p class="text-muted">Kelola konfigurasi jam operasional magang</p>
        </div>

        <div class="grid-2">
            <!-- Jam Kerja Card -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-clock"></i> Pengaturan Jam Kerja
                    </h3>
                </div>

                <form action="{{ route('settings.update') }}" method="POST">
                    @csrf

                    <div class="form-group">
                        <label class="form-label">Jam Masuk (Presensi Masuk)</label>
                        <div class="d-flex align-center gap-2">
                            <input type="time" name="office_start_time" class="form-control"
                                value="{{ $settings->get('office_start_time')?->value ?? '08:00' }}">
                            <span class="text-muted" style="font-size: 12px;">Waktu mulai absen masuk</span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Batas Toleransi Terlambat</label>
                        <div class="d-flex align-center gap-2">
                            <input type="time" name="late_tolerance_time" class="form-control"
                                value="{{ $settings->get('late_tolerance_time')?->value ?? '08:15' }}">
                            <span class="text-muted" style="font-size: 12px;">Lewat jam ini dianggap terlambat</span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Jam Pulang (Presensi Keluar)</label>
                        <div class="d-flex align-center gap-2">
                            <input type="time" name="office_end_time" class="form-control"
                                value="{{ $settings->get('office_end_time')?->value ?? '17:00' }}">
                            <span class="text-muted" style="font-size: 12px;">Waktu minimal absen pulang</span>
                        </div>
                    </div>

                    <div class="alert alert-info mt-4">
                        <i class="fas fa-info-circle"></i>
                        <div>
                            <strong>Info:</strong> Perubahan jam kerja akan berpengaruh pada status kehadiran (Tepat
                            Waktu/Terlambat) untuk presensi hari-hari berikutnya.
                        </div>
                    </div>

                    <div class="text-right mt-6">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Simpan Pengaturan
                        </button>
                    </div>
                </form>
            </div>

            <!-- Preview Card -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-eye"></i> Preview Aturan
                    </h3>
                </div>

                <div style="padding: 20px; background: var(--bg-tertiary); border-radius: var(--radius-md);">
                    <div class="mb-6">
                        <div class="text-muted mb-2"
                            style="font-size: 12px; text-transform: uppercase; letter-spacing: 1px;">Pagi Hari</div>
                        <div class="d-flex align-center gap-4">
                            <div>
                                <div class="text-success fw-bold">
                                    <i class="fas fa-check-circle"></i> Tepat Waktu
                                </div>
                                <small class="text-muted">Sebelum
                                    {{ $settings->get('office_start_time')?->value ?? '08:00' }}</small>
                            </div>
                            <div style="width: 2px; height: 40px; background: var(--border-color);"></div>
                            <div>
                                <div class="text-warning fw-bold">
                                    <i class="fas fa-exclamation-circle"></i> Terlambat
                                </div>
                                <small class="text-muted">Setelah
                                    {{ $settings->get('late_tolerance_time')?->value ?? '08:15' }}</small>
                            </div>
                        </div>
                    </div>

                    <div>
                        <div class="text-muted mb-2"
                            style="font-size: 12px; text-transform: uppercase; letter-spacing: 1px;">Sore Hari</div>
                        <div class="d-flex align-center gap-4">
                            <div>
                                <div class="text-success fw-bold">
                                    <i class="fas fa-sign-out-alt"></i> Boleh Pulang
                                </div>
                                <small class="text-muted">Mulai
                                    {{ $settings->get('office_end_time')?->value ?? '16:00' }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Location Settings -->
        <div class="card mt-6">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-map-marked-alt"></i> Pengaturan Lokasi Absensi
                </h3>
            </div>

            <form action="{{ route('settings.update') }}" method="POST">
                @csrf

                <div class="d-flex gap-4" style="flex-wrap: wrap;">
                    <div class="form-group" style="flex: 1; min-width: 200px;">
                        <label class="form-label">Latitude (Garis Lintang)</label>
                        <input type="text" name="office_latitude" class="form-control"
                            value="{{ $settings->get('office_latitude')?->value ?? '-7.052683' }}"
                            placeholder="Contoh: -7.052xxx">
                    </div>

                    <div class="form-group" style="flex: 1; min-width: 200px;">
                        <label class="form-label">Longitude (Garis Bujur)</label>
                        <input type="text" name="office_longitude" class="form-control"
                            value="{{ $settings->get('office_longitude')?->value ?? '110.469375' }}"
                            placeholder="Contoh: 110.469xxx">
                    </div>

                    <div class="form-group" style="flex: 1; min-width: 200px;">
                        <label class="form-label">Radius Maksimal (Meter)</label>
                        <input type="number" name="max_checkin_distance" class="form-control"
                            value="{{ $settings->get('max_checkin_distance')?->value ?? '100' }}">
                        <small class="text-muted">Jarak maksimal siswa dari titik kantor untuk bisa absen.</small>
                    </div>
                </div>

                <div class="alert alert-info mt-4">
                    <i class="fas fa-satellite"></i>
                    <div>
                        <strong>Tips:</strong> Gunakan Google Maps untuk mendapatkan titik koordinat yang presisi. Klik
                        kanan pada lokasi kantor di Google Maps dan pilih koordinat paling atas.
                    </div>
                </div>

                <div class="text-right mt-6">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan Lokasi
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
