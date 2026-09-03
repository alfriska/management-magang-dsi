@extends('layouts.app')

@section('title', 'Tambah Penilaian')

@section('content')
<div class="slide-up">
    <div class="d-flex align-center gap-4 mb-6">
        <a href="{{ route('assessments.index') }}" class="btn btn-secondary btn-icon">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h2 style="margin-bottom: 4px;">Tambah Penilaian</h2>
            <p class="text-muted">Evaluasi performa siswa magang</p>
        </div>
    </div>

    <div class="card" style="max-width: 800px;">
        <form action="{{ route('assessments.store') }}" method="POST">
            @csrf
            
            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">Siswa Magang *</label>
                    <select name="intern_id" class="form-control" required>
                        <option value="">-- Pilih Siswa --</option>
                        @foreach($interns as $intern)
                            <option value="{{ $intern->id }}" {{ old('intern_id', request('intern_id') ?? $selectedIntern?->id) == $intern->id ? 'selected' : '' }}>
                                {{ $intern->user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Tugas (Opsional)</label>
                    <select name="task_id" class="form-control">
                        <option value="">-- Penilaian Umum --</option>
                        @foreach($tasks as $task)
                            <option value="{{ $task->id }}" {{ old('task_id', request('task_id') ?? $selectedTask?->id) == $task->id ? 'selected' : '' }}>
                                {{ $task->title }} ({{ $task->intern->user->name ?? '' }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            
            <h4 style="margin: 24px 0 16px; padding-bottom: 12px; border-bottom: 1px solid var(--border-color);">
                <i class="fas fa-chart-bar"></i> Skor Penilaian (0-100)
            </h4>
            
            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">Kualitas Kerja *</label>
                    <input type="number" name="quality_score" class="form-control" value="{{ old('quality_score', 75) }}" min="0" max="100" required>
                    <small class="text-muted">Kualitas hasil pekerjaan yang dikerjakan</small>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Kecepatan *</label>
                    <input type="number" name="speed_score" class="form-control" value="{{ old('speed_score', 75) }}" min="0" max="100" required>
                    <small class="text-muted">Kecepatan dalam menyelesaikan tugas</small>
                </div>
            </div>
            
            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">Inisiatif *</label>
                    <input type="number" name="initiative_score" class="form-control" value="{{ old('initiative_score', 75) }}" min="0" max="100" required>
                    <small class="text-muted">Kemampuan mengambil inisiatif dan proaktif</small>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Kerjasama Tim *</label>
                    <input type="number" name="teamwork_score" class="form-control" value="{{ old('teamwork_score', 75) }}" min="0" max="100" required>
                    <small class="text-muted">Kemampuan bekerja dalam tim</small>
                </div>
            </div>
            
            <div class="form-group">
                <label class="form-label">Komunikasi *</label>
                <input type="number" name="communication_score" class="form-control" value="{{ old('communication_score', 75) }}" min="0" max="100" required>
                <small class="text-muted">Kemampuan berkomunikasi dengan baik</small>
            </div>
            
            <h4 style="margin: 24px 0 16px; padding-bottom: 12px; border-bottom: 1px solid var(--border-color);">
                <i class="fas fa-comment"></i> Komentar
            </h4>
            
            <div class="form-group">
                <label class="form-label">Kelebihan</label>
                <textarea name="strengths" class="form-control" rows="3" placeholder="Sebutkan kelebihan siswa...">{{ old('strengths') }}</textarea>
            </div>
            
            <div class="form-group">
                <label class="form-label">Area yang Perlu Ditingkatkan</label>
                <textarea name="improvements" class="form-control" rows="3" placeholder="Sebutkan area yang perlu diperbaiki...">{{ old('improvements') }}</textarea>
            </div>
            
            <div class="form-group">
                <label class="form-label">Komentar Tambahan</label>
                <textarea name="comments" class="form-control" rows="3" placeholder="Komentar atau saran tambahan...">{{ old('comments') }}</textarea>
            </div>
            
            <div class="d-flex gap-4 mt-6">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Simpan Penilaian
                </button>
                <a href="{{ route('assessments.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
