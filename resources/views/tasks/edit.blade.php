@extends('layouts.app')

@section('title', 'Edit Tugas')

@section('content')
    <div class="slide-up max-w-4xl mx-auto space-y-6">
        <!-- Header -->
        <div class="flex items-center gap-4 mb-2">
            <a href="{{ route('tasks.index') }}"
                class="w-10 h-10 rounded-xl bg-white border border-slate-200 flex items-center justify-center text-slate-500 hover:bg-slate-50 hover:text-slate-700 transition-all shadow-sm">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h2 class="text-2xl font-bold text-slate-800 tracking-tight">Edit Tugas</h2>
                <div class="flex items-center gap-2 text-sm text-slate-500">
                    <span class="font-mono bg-slate-100 px-1.5 py-0.5 rounded text-xs">#{{ $task->id }}</span>
                    <span>{{ $task->title }}</span>
                </div>
            </div>
        </div>

        <form action="{{ route('tasks.update', $task) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Form Area -->
                <div class="lg:col-span-2 space-y-6">

                    <!-- Basic Info Card -->
                    <div class="card p-0 overflow-hidden">
                        <div class="p-5 border-b border-slate-100 bg-slate-50/50 flex justify-between items-center">
                            <h3 class="font-bold text-slate-800 flex items-center gap-2">
                                <i class="fas fa-edit text-indigo-500"></i> Informasi Tugas
                            </h3>
                        </div>
                        <div class="p-6 space-y-5">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase mb-2">Judul Tugas <span
                                        class="text-rose-500">*</span></label>
                                <input type="text" name="title"
                                    class="form-input w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 font-semibold text-slate-700"
                                    value="{{ old('title', $task->title) }}" required>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase mb-2">Deskripsi</label>
                                <textarea name="description"
                                    class="form-textarea w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                                    rows="5">{{ old('description', $task->description) }}</textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Status & Feedback Card -->
                    <div class="card p-0 overflow-hidden">
                        <div class="p-5 border-b border-slate-100 bg-slate-50/50">
                            <h3 class="font-bold text-slate-800 flex items-center gap-2">
                                <i class="fas fa-tasks text-emerald-500"></i> Status & Revisi
                            </h3>
                        </div>
                        <div class="p-6 space-y-5">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 uppercase mb-2">Status <span
                                            class="text-rose-500">*</span></label>
                                    <div class="relative">
                                        <select name="status"
                                            class="form-select w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 font-semibold"
                                            required>
                                            <option value="pending" {{ old('status', $task->status) === 'pending' ? 'selected' : '' }}>⏳ Menunggu</option>
                                            <option value="in_progress" {{ old('status', $task->status) === 'in_progress' ? 'selected' : '' }}>🔨 Dalam Proses</option>
                                            <option value="completed" {{ old('status', $task->status) === 'completed' ? 'selected' : '' }}>✅ Selesai</option>
                                            <option value="revision" {{ old('status', $task->status) === 'revision' ? 'selected' : '' }}>🛑 Revisi</option>
                                        </select>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 uppercase mb-2">Prioritas <span
                                            class="text-rose-500">*</span></label>
                                    <div class="relative">
                                        <select name="priority"
                                            class="form-select w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 font-semibold"
                                            required>
                                            <option value="low" {{ old('priority', $task->priority) === 'low' ? 'selected' : '' }}>🟢 Rendah</option>
                                            <option value="medium" {{ old('priority', $task->priority) === 'medium' ? 'selected' : '' }}>🟡 Sedang</option>
                                            <option value="high" {{ old('priority', $task->priority) === 'high' ? 'selected' : '' }}>🔴 Tinggi</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase mb-2">Catatan Revisi /
                                    Feedback</label>
                                <textarea name="admin_feedback"
                                    class="form-textarea w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 text-sm bg-slate-50"
                                    rows="3"
                                    placeholder="Tambahkan catatan untuk revisi atau feedback penilaian...">{{ old('admin_feedback', $task->admin_feedback) }}</textarea>
                                <p class="text-xs text-slate-400 mt-1">Catatan ini akan tampil menonjol jika status adalah
                                    "Revisi".</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar / Settings -->
                <div class="space-y-6">

                    <!-- Assignment Card -->
                    <div class="card p-0 overflow-hidden">
                        <div class="p-5 border-b border-slate-100 bg-slate-50/50">
                            <h3 class="font-bold text-slate-800 flex items-center gap-2">
                                <i class="fas fa-user-tag text-amber-500"></i> Penugasan
                            </h3>
                        </div>
                        <div class="p-6 space-y-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase mb-2">Ditugaskan
                                    Kepada</label>
                                <select name="intern_id"
                                    class="form-select w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                    <option value="">-- Tidak Ada Siswa --</option>
                                    <option value="all_active" class="font-bold bg-slate-50">
                                        🎯 Semua Siswa Aktif ({{ $interns->count() }})
                                    </option>
                                    <optgroup label="Pilih Siswa Tertentu">
                                        @foreach($interns as $intern)
                                            <option value="{{ $intern->id }}" {{ old('intern_id', $task->intern_id) == $intern->id ? 'selected' : '' }}>
                                                {{ $intern->user->name }}
                                            </option>
                                        @endforeach
                                    </optgroup>
                                </select>
                                @if($task->intern_id && !$task->intern)
                                    <div
                                        class="mt-2 flex items-center gap-2 text-xs text-amber-600 font-medium bg-amber-50 p-2 rounded-lg border border-amber-100">
                                        <i class="fas fa-exclamation-triangle"></i> Siswa sebelumnya dihapus
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Setup Card -->
                    <div class="card p-0 overflow-hidden">
                        <div class="p-5 border-b border-slate-100 bg-slate-50/50">
                            <h3 class="font-bold text-slate-800 flex items-center gap-2">
                                <i class="fas fa-cog text-slate-500"></i> Pengaturan
                            </h3>
                        </div>
                        <div class="p-6 space-y-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase mb-2">Deadline</label>
                                <div class="grid grid-cols-2 gap-3">
                                    <input type="date" name="deadline"
                                        class="form-input w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                                        value="{{ old('deadline', $task->deadline?->format('Y-m-d')) }}">
                                    <input type="time" name="deadline_time"
                                        class="form-input w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                                        value="{{ old('deadline_time', $task->deadline_time ?? '23:59') }}">
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase mb-2">Estimasi Jam</label>
                                <div class="relative">
                                    <input type="number" name="estimated_hours"
                                        class="form-input w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 text-sm pl-10"
                                        value="{{ old('estimated_hours', $task->estimated_hours) }}" min="1">
                                    <div
                                        class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                        <i class="fas fa-hourglass-half text-xs"></i>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase mb-2">Metode Submit</label>
                                <select name="submission_type"
                                    class="form-select w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                    <option value="github" {{ old('submission_type', $task->submission_type) === 'github' ? 'selected' : '' }}>📌 Link GitHub</option>
                                    <option value="file" {{ old('submission_type', $task->submission_type) === 'file' ? 'selected' : '' }}>📁 Upload File</option>
                                    <option value="both" {{ old('submission_type', $task->submission_type) === 'both' ? 'selected' : '' }}>📦 Keduanya</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Submission Info (Read Only) -->
                    @if($task->started_at || $task->completed_at || $task->github_link || $task->submission_file)
                        <div class="card p-5 bg-slate-50 border-slate-200">
                            <h4 class="font-bold text-slate-700 text-xs uppercase mb-3 border-b border-slate-200 pb-2">Tracking
                                & Submission</h4>
                            <div class="space-y-3 text-sm">
                                @if($task->started_at)
                                    <div class="flex justify-between">
                                        <span class="text-slate-500">Mulai:</span>
                                        <span class="font-medium text-slate-700">{{ $task->started_at->format('d M H:i') }}</span>
                                    </div>
                                @endif
                                @if($task->completed_at)
                                    <div class="flex justify-between">
                                        <span class="text-slate-500">Selesai:</span>
                                        <span class="font-medium text-slate-700">{{ $task->completed_at->format('d M H:i') }}</span>
                                    </div>
                                @endif
                                @if($task->github_link)
                                    <div class="pt-2 border-t border-slate-200">
                                        <a href="{{ $task->github_link }}" target="_blank"
                                            class="flex items-center gap-2 text-indigo-600 hover:text-indigo-700 font-medium">
                                            <i class="fab fa-github"></i> Link Repo
                                        </a>
                                    </div>
                                @endif
                                @if($task->submission_file)
                                    <div class="pt-2 border-t border-slate-200">
                                        <a href="{{ Storage::url('submissions/' . $task->submission_file) }}" target="_blank"
                                            class="flex items-center gap-2 text-indigo-600 hover:text-indigo-700 font-medium">
                                            <i class="fas fa-file-download"></i> Download File
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Action Buttons Footer -->
            <div
                class="card p-4 mt-6 flex justify-end gap-3 sticky bottom-4 z-10 shadow-lg border-t border-slate-100 bg-white/90 backdrop-blur-sm">
                <a href="{{ route('tasks.show', $task) }}" class="btn btn-secondary px-6">Batal</a>
                <button type="submit" class="btn btn-primary px-6 shadow-indigo-500/20 shadow-lg">
                    <i class="fas fa-save mr-2"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
@endsection
