@extends('layouts.app')

@section('title', 'Import Peserta Magang')

@section('content')
    <div class="slide-up">
        <div class="d-flex align-center gap-4 mb-6">
            <a href="{{ route('interns.index') }}" class="btn btn-secondary btn-icon">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h2 style="margin-bottom: 4px;">Import Peserta Magang</h2>
                <p class="text-muted">Upload file Excel untuk menambahkan peserta magang secara massal</p>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
            </div>
        @endif

        @if(session('warning'))
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle"></i> {{ session('warning') }}

                @if(session('import_errors'))
                    <div style="margin-top: 12px;">
                        <strong>Detail Error:</strong>
                        <ul style="margin-top: 8px;">
                            @foreach(session('import_errors') as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">
                <i class="fas fa-times-circle"></i> {{ session('error') }}
            </div>
        @endif

        <div class="grid-2">
            <!-- Upload Form -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-upload"></i> Upload File Excel</h3>
                </div>

                <form action="{{ route('import.interns') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="form-group">
                        <label for="file">File Excel <span class="text-danger">*</span></label>
                        <input type="file" id="file" name="file" class="form-control @error('file') is-invalid @enderror"
                            accept=".xlsx,.xls,.csv" required>
                        @error('file')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">
                            Format yang didukung: XLSX, XLS, CSV (Maksimal 10MB)
                        </small>
                    </div>

                    <div class="form-group">
                        <label for="supervisor_id">Pembimbing Default (Opsional)</label>
                        <select id="supervisor_id" name="supervisor_id" class="form-control">
                            <option value="">-- Pilih Pembimbing --</option>
                            @foreach(\App\Models\User::where('role', 'pembimbing')->get() as $supervisor)
                                <option value="{{ $supervisor->id }}">{{ $supervisor->name }}</option>
                            @endforeach
                        </select>
                        <small class="form-text text-muted">
                            Jika diisi, semua peserta yang diimport akan menggunakan pembimbing ini (bisa diubah manual di
                            file Excel)
                        </small>
                    </div>

                    <div class="d-flex gap-3" style="margin-top: 24px;">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-upload"></i> Import Data
                        </button>
                        <a href="{{ route('interns.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Batal
                        </a>
                    </div>
                </form>
            </div>

            <!-- Instructions & Template -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-info-circle"></i> Petunjuk Import</h3>
                </div>

                <div style="display: grid; gap: 20px;">
                    <!-- Download Template -->
                    <div
                        style="background: linear-gradient(135deg, #10b981, #059669); padding: 20px; border-radius: 12px; color: white;">
                        <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 12px;">
                            <i class="fas fa-file-excel" style="font-size: 32px;"></i>
                            <div>
                                <h4 style="margin: 0; font-size: 16px;">Template Excel</h4>
                                <p style="margin: 4px 0 0 0; opacity: 0.9; font-size: 13px;">
                                    Download template untuk memudahkan import
                                </p>
                            </div>
                        </div>
                        <a href="{{ route('import.template') }}" class="btn"
                            style="background: white; color: #10b981; width: 100%; border: none;">
                            <i class="fas fa-download"></i> Download Template
                        </a>
                    </div>

                    <!-- Steps -->
                    <div>
                        <h4 style="margin-bottom: 12px; color: #1e293b;">Langkah-langkah:</h4>
                        <ol style="padding-left: 20px; line-height: 1.8; color: #64748b;">
                            <li>Download template Excel di atas</li>
                            <li>Isi data peserta magang sesuai kolom yang tersedia</li>
                            <li>Pastikan email unik dan belum terdaftar</li>
                            <li>Hapus baris contoh sebelum import</li>
                            <li>Upload file yang sudah diisi</li>
                        </ol>
                    </div>

                    <!-- Important Notes -->
                    <div style="background: #fef3c7; padding: 16px; border-radius: 8px; border-left: 4px solid #f59e0b;">
                        <h4 style="margin: 0 0 8px 0; color: #92400e; font-size: 14px;">
                            <i class="fas fa-exclamation-triangle"></i> Penting!
                        </h4>
                        <ul style="margin: 0; padding-left: 20px; color: #78350f; font-size: 13px; line-height: 1.6;">
                            <li>Kolom <strong>nama</strong> dan <strong>email</strong> wajib diisi</li>
                            <li>Email harus valid dan belum terdaftar di sistem</li>
                            <li>Password default: <code>password123</code> (bisa diubah oleh user)</li>
                            <li>Format tanggal: DD/MM/YYYY (contoh: 01/01/2026)</li>
                            <li>Jangan mengubah nama atau urutan kolom</li>
                        </ul>
                    </div>

                    <!-- Sample Data -->
                    <div>
                        <h4 style="margin-bottom: 12px; color: #1e293b;">Contoh Data:</h4>
                        <div style="overflow-x: auto;">
                            <table class="data-table" style="font-size: 11px;">
                                <thead>
                                    <tr>
                                        <th>nama</th>
                                        <th>email</th>
                                        <th>nis</th>
                                        <th>sekolah</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>John Doe</td>
                                        <td>john@example.com</td>
                                        <td>12345</td>
                                        <td>SMK Negeri 1</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }

        .data-table th,
        .data-table td {
            padding: 8px 12px;
            border: 1px solid #e2e8f0;
            text-align: left;
        }

        .data-table th {
            background: #f1f5f9;
            font-weight: 600;
            color: #475569;
        }

        .data-table tbody tr:hover {
            background: #f8fafc;
        }

        code {
            background: rgba(0, 0, 0, 0.1);
            padding: 2px 6px;
            border-radius: 4px;
            font-family: 'Courier New', monospace;
            font-size: 12px;
        }
    </style>
@endsection
