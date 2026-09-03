@extends('layouts.app')

@section('title', 'Buat Laporan')

@section('content')
<div class="slide-up">
    <div class="d-flex align-center gap-4 mb-6">
        <a href="{{ route('reports.index') }}" class="btn btn-secondary btn-icon">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h2 style="margin-bottom: 4px;">Buat Laporan Baru</h2>
            <p class="text-muted">Buat laporan perkembangan siswa magang</p>
        </div>
    </div>

    <div class="card" style="max-width: 800px;">
        <form action="{{ route('reports.store') }}" method="POST">
            @csrf
            
            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">Siswa Magang *</label>
                    <select name="intern_id" class="form-control" required>
                        <option value="">-- Pilih Siswa --</option>
                        @foreach($interns as $intern)
                            <option value="{{ $intern->id }}" {{ old('intern_id', request('intern_id')) == $intern->id ? 'selected' : '' }}>
                                {{ $intern->user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Tipe Laporan *</label>
                    <select name="type" class="form-control" required>
                        <option value="weekly" {{ old('type') === 'weekly' ? 'selected' : '' }}>Mingguan</option>
                        <option value="monthly" {{ old('type') === 'monthly' ? 'selected' : '' }}>Bulanan</option>
                        <option value="final" {{ old('type') === 'final' ? 'selected' : '' }}>Akhir</option>
                    </select>
                </div>
            </div>
            
            <div class="form-group">
                <label class="form-label">Judul Laporan *</label>
                <input type="text" name="title" class="form-control" value="{{ old('title') }}" required placeholder="Contoh: Laporan Mingguan - Minggu ke-3">
            </div>
            
            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">Periode Mulai *</label>
                    <input type="date" name="period_start" class="form-control" value="{{ old('period_start') }}" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Periode Akhir *</label>
                    <input type="date" name="period_end" class="form-control" value="{{ old('period_end') }}" required>
                </div>
            </div>
            
            <div class="form-group">
                <label class="form-label">Isi Laporan *</label>
                <textarea name="content" class="form-control" rows="8" required placeholder="Tuliskan isi laporan perkembangan siswa...">{{ old('content') }}</textarea>
            </div>
            
            <div class="d-flex gap-4 mt-6">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Simpan & Ajukan
                </button>
                <a href="{{ route('reports.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
