@extends('layouts.app')

@section('title', 'Tambah Presensi')

@section('content')
<div class="slide-up">
    <div class="d-flex align-center gap-4 mb-6">
        <a href="{{ route('attendances.index') }}" class="btn btn-secondary btn-icon">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h2 style="margin-bottom: 4px;">Tambah Presensi</h2>
            <p class="text-muted">Catat kehadiran siswa magang</p>
        </div>
    </div>

    <div class="card" style="max-width: 600px;">
        <form action="{{ route('attendances.store') }}" method="POST">
            @csrf
            
            <div class="form-group">
                <label class="form-label">Siswa Magang *</label>
                <select name="intern_id" class="form-control" required>
                    <option value="">-- Pilih Siswa --</option>
                    @foreach($interns as $intern)
                        <option value="{{ $intern->id }}" {{ old('intern_id') == $intern->id ? 'selected' : '' }}>
                            {{ $intern->user->name }} ({{ $intern->school }})
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div class="form-group">
                <label class="form-label">Tanggal *</label>
                <input type="date" name="date" class="form-control" value="{{ old('date', date('Y-m-d')) }}" required>
            </div>
            
            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">Presensi Masuk</label>
                    <input type="time" name="check_in" class="form-control" value="{{ old('check_in') }}">
                </div>
                
                <div class="form-group">
                    <label class="form-label">Presensi Keluar</label>
                    <input type="time" name="check_out" class="form-control" value="{{ old('check_out') }}">
                </div>
            </div>
            
            <div class="form-group">
                <label class="form-label">Status *</label>
                <select name="status" class="form-control" required>
                    <option value="present" {{ old('status') === 'present' ? 'selected' : '' }}>Hadir</option>
                    <option value="late" {{ old('status') === 'late' ? 'selected' : '' }}>Terlambat</option>
                    <option value="absent" {{ old('status') === 'absent' ? 'selected' : '' }}>Tidak Hadir</option>
                    <option value="sick" {{ old('status') === 'sick' ? 'selected' : '' }}>Sakit</option>
                    <option value="permission" {{ old('status') === 'permission' ? 'selected' : '' }}>Izin</option>
                </select>
            </div>
            
            <div class="form-group">
                <label class="form-label">Catatan</label>
                <textarea name="notes" class="form-control" rows="3" placeholder="Catatan tambahan (opsional)">{{ old('notes') }}</textarea>
            </div>
            
            <div class="d-flex gap-4 mt-6">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Simpan
                </button>
                <a href="{{ route('attendances.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
