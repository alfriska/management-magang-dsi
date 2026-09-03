@extends('layouts.app')

@section('title', 'Edit Penilaian')

@section('content')
<div class="slide-up">
    <div class="d-flex align-center gap-4 mb-6">
        <a href="{{ route('assessments.index') }}" class="btn btn-secondary btn-icon">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h2 style="margin-bottom: 4px;">Edit Penilaian</h2>
            <p class="text-muted">{{ $assessment->intern->user->name }}</p>
        </div>
    </div>

    <div class="card" style="max-width: 800px;">
        <form action="{{ route('assessments.update', $assessment) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">Siswa Magang</label>
                    <input type="text" class="form-control" value="{{ $assessment->intern->user->name }}" disabled>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Tugas</label>
                    <input type="text" class="form-control" value="{{ $assessment->task->title ?? 'Penilaian Umum' }}" disabled>
                </div>
            </div>
            
            <h4 style="margin: 24px 0 16px; padding-bottom: 12px; border-bottom: 1px solid var(--border-color);">
                <i class="fas fa-chart-bar"></i> Skor Penilaian (0-100)
            </h4>
            
            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">Kualitas Kerja *</label>
                    <input type="number" name="quality_score" class="form-control" value="{{ old('quality_score', $assessment->quality_score) }}" min="0" max="100" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Kecepatan *</label>
                    <input type="number" name="speed_score" class="form-control" value="{{ old('speed_score', $assessment->speed_score) }}" min="0" max="100" required>
                </div>
            </div>
            
            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">Inisiatif *</label>
                    <input type="number" name="initiative_score" class="form-control" value="{{ old('initiative_score', $assessment->initiative_score) }}" min="0" max="100" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Kerjasama Tim *</label>
                    <input type="number" name="teamwork_score" class="form-control" value="{{ old('teamwork_score', $assessment->teamwork_score) }}" min="0" max="100" required>
                </div>
            </div>
            
            <div class="form-group">
                <label class="form-label">Komunikasi *</label>
                <input type="number" name="communication_score" class="form-control" value="{{ old('communication_score', $assessment->communication_score) }}" min="0" max="100" required>
            </div>
            
            <h4 style="margin: 24px 0 16px; padding-bottom: 12px; border-bottom: 1px solid var(--border-color);">
                <i class="fas fa-comment"></i> Komentar
            </h4>
            
            <div class="form-group">
                <label class="form-label">Kelebihan</label>
                <textarea name="strengths" class="form-control" rows="3">{{ old('strengths', $assessment->strengths) }}</textarea>
            </div>
            
            <div class="form-group">
                <label class="form-label">Area yang Perlu Ditingkatkan</label>
                <textarea name="improvements" class="form-control" rows="3">{{ old('improvements', $assessment->improvements) }}</textarea>
            </div>
            
            <div class="form-group">
                <label class="form-label">Komentar Tambahan</label>
                <textarea name="comments" class="form-control" rows="3">{{ old('comments', $assessment->comments) }}</textarea>
            </div>
            
            <div class="d-flex gap-4 mt-6">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Simpan Perubahan
                </button>
                <a href="{{ route('assessments.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
