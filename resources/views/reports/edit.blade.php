@extends('layouts.app')

@section('title', 'Edit Laporan')

@section('content')
<div class="slide-up">
    <div class="d-flex align-center gap-4 mb-6">
        <a href="{{ route('reports.index') }}" class="btn btn-secondary btn-icon">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h2 style="margin-bottom: 4px;">Edit Laporan</h2>
            <p class="text-muted">{{ $report->title }}</p>
        </div>
    </div>

    <div class="card" style="max-width: 800px;">
        <form action="{{ route('reports.update', $report) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">Siswa Magang</label>
                    <input type="text" class="form-control" value="{{ $report->intern->user->name }}" disabled>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Tipe Laporan *</label>
                    <select name="type" class="form-control" required>
                        <option value="weekly" {{ old('type', $report->type) === 'weekly' ? 'selected' : '' }}>Mingguan</option>
                        <option value="monthly" {{ old('type', $report->type) === 'monthly' ? 'selected' : '' }}>Bulanan</option>
                        <option value="final" {{ old('type', $report->type) === 'final' ? 'selected' : '' }}>Akhir</option>
                    </select>
                </div>
            </div>
            
            <div class="form-group">
                <label class="form-label">Judul Laporan *</label>
                <input type="text" name="title" class="form-control" value="{{ old('title', $report->title) }}" required>
            </div>
            
            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">Periode Mulai *</label>
                    <input type="date" name="period_start" class="form-control" value="{{ old('period_start', $report->period_start->format('Y-m-d')) }}" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Periode Akhir *</label>
                    <input type="date" name="period_end" class="form-control" value="{{ old('period_end', $report->period_end->format('Y-m-d')) }}" required>
                </div>
            </div>
            
            <div class="form-group">
                <label class="form-label">Isi Laporan *</label>
                <textarea name="content" class="form-control" rows="8" required>{{ old('content', $report->content) }}</textarea>
            </div>
            
            <div class="form-group">
                <label class="form-label">Status *</label>
                <select name="status" class="form-control" required>
                    <option value="draft" {{ old('status', $report->status) === 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="submitted" {{ old('status', $report->status) === 'submitted' ? 'selected' : '' }}>Diajukan</option>
                    <option value="reviewed" {{ old('status', $report->status) === 'reviewed' ? 'selected' : '' }}>Sudah Direview</option>
                </select>
            </div>
            
            <div class="form-group">
                <label class="form-label">Feedback</label>
                <textarea name="feedback" class="form-control" rows="4" placeholder="Berikan feedback untuk laporan ini...">{{ old('feedback', $report->feedback) }}</textarea>
            </div>
            
            <div class="d-flex gap-4 mt-6">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Simpan Perubahan
                </button>
                <a href="{{ route('reports.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
