@extends('layouts.app')

@section('title', 'Daily Report')

@section('content')
<div class="max-w-6xl mx-auto space-y-6"
     x-data="{
        showCreate: {{ $errors->any() && !$todayReport ? 'true' : 'false' }},
        showEdit: {{ $errors->any() && $todayReport ? 'true' : 'false' }}
     }">

    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-slate-900 tracking-tight">Daily Report</h1>
            <p class="text-slate-500 mt-1">Kelola daily report aktivitas.</p>
        </div>
        @if (!$todayReport)
            <button type="button" @click="showCreate = true" class="btn btn-primary">
                <i class="fas fa-plus"></i> Buat Laporan Hari Ini
            </button>
        @endif
    </div>

    @if ($errors->any())
        <div class="bg-rose-50 border border-rose-100 rounded-2xl p-4 flex gap-3 animate-fade-in-up">
            <div class="shrink-0 text-rose-500">
                <i class="fas fa-exclamation-circle text-xl"></i>
            </div>
            <div>
                <h3 class="font-bold text-rose-900 text-sm">Terdapat kesalahan pada inputan</h3>
                <ul class="list-disc list-inside text-sm text-rose-600 mt-1 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <!-- Filter Tanggal -->
    <div class="card p-4">
        <form method="GET" action="{{ route('daily-reports.index') }}" class="flex flex-col sm:flex-row items-end gap-3">
            <div class="form-group mb-0 flex-1">
                <label class="form-label">Pilih Tanggal</label>
                <input type="date" name="date" class="form-control" value="{{ $filterDate }}">
            </div>
            <button type="submit" class="btn btn-secondary">
                <i class="fas fa-filter"></i> Filter
            </button>
            @if ($filterDate)
                <a href="{{ route('daily-reports.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Reset
                </a>
            @endif
        </form>
    </div>

    <!-- Tabel Riwayat Daily Report -->
    <div class="card p-0 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-100 text-left text-xs font-semibold text-slate-400 uppercase tracking-wide">
                        <th class="px-5 py-3">Tanggal</th>
                        <th class="px-5 py-3">Siswa</th>
                        <th class="px-5 py-3">Isi Laporan</th>
                        <th class="px-5 py-3">Status</th>
                        <th class="px-5 py-3">Lampiran</th>
                        <th class="px-5 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($dailyReports as $report)
                        <tr class="border-b border-slate-50 last:border-0 hover:bg-slate-50/50">
                            <td class="px-5 py-3 font-semibold text-slate-700 whitespace-nowrap">
                                {{ $report->date->translatedFormat('d M Y') }}
                            </td>
                            <td class="px-5 py-3 text-slate-600 whitespace-nowrap">{{ $siswaName }}</td>
                            <td class="px-5 py-3 text-slate-600">{{ \Illuminate\Support\Str::limit($report->description, 60) }}</td>
                            <td class="px-5 py-3">
                                @php
                                    $statusBadge = match ($report->status) {
                                        'selesai' => ['class' => 'badge-success', 'label' => 'Selesai'],
                                        'on_progress' => ['class' => 'badge-warning', 'label' => 'On Progress'],
                                        default => ['class' => 'badge-secondary', 'label' => 'Belum Dikerjakan'],
                                    };
                                @endphp
                                <span class="badge {{ $statusBadge['class'] }}">{{ $statusBadge['label'] }}</span>
                            </td>
                            <td class="px-5 py-3">
                                @if ($report->image)
                                    <span class="badge badge-info"><i class="fas fa-image mr-1"></i> Foto</span>
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>
                            <td class="px-5 py-3">
                                <div class="flex gap-2">
                                    <a href="{{ route('daily-reports.show', $report) }}"
                                        class="w-8 h-8 rounded-lg bg-sky-100 text-sky-600 flex items-center justify-center hover:bg-sky-200 transition-colors"
                                        title="Lihat Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if ($todayReport && $report->id === $todayReport->id)
                                        <button type="button" @click="showEdit = true"
                                            class="w-8 h-8 rounded-lg bg-amber-100 text-amber-600 flex items-center justify-center hover:bg-amber-200 transition-colors"
                                            title="Edit">
                                            <i class="fas fa-pen"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-10 text-center text-slate-400">
                                {{ $filterDate ? 'Tidak ada Daily Report pada tanggal tersebut.' : 'Belum ada Daily Report yang dibuat.' }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Create Modal -->
    @if (!$todayReport)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6" x-show="showCreate"
        @click.self="showCreate = false" style="display: none;">
        <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm"></div>
        <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
            <div class="p-5 border-b border-slate-100 flex justify-between items-start bg-slate-50/50">
                <div>
                    <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                        <i class="fas fa-clipboard-list text-indigo-500"></i>
                        Buat Daily Report
                    </h3>
                    <p class="text-slate-500 text-sm mt-1">{{ now()->translatedFormat('l, d F Y') }}</p>
                </div>
                <button type="button" @click="showCreate = false"
                    class="w-8 h-8 rounded-lg bg-slate-100 text-slate-500 flex items-center justify-center hover:bg-rose-50 hover:text-rose-500 transition-colors">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form action="{{ route('daily-reports.store') }}" method="POST" class="p-5" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="date" value="{{ now()->toDateString() }}">

                <div class="form-group">
                    <label class="form-label">Deskripsi *</label>
                    <textarea name="description" class="form-control" rows="4"
                        placeholder="Deskripsikan kegiatan yang dilakukan hari ini" required>{{ old('description') }}</textarea>
                </div>

                <div class="form-group">
                    <label class="form-label">Gambar (opsional)</label>
                    <input type="file" name="image" accept=".jpg,.jpeg,.png" class="form-control">
                    <p class="text-xs text-slate-400 mt-1">Format JPG/PNG, maksimal 5MB.</p>
                </div>

                <div class="form-group">
                    <label class="form-label">Target Selesai (opsional)</label>
                    <input type="date" name="target_date" class="form-control" value="{{ old('target_date') }}">
                </div>

                <div class="form-group">
                    <label class="form-label">Status *</label>
                    <select name="status" class="form-control" required>
                        <option value="belum_dikerjakan" {{ old('status') == 'belum_dikerjakan' ? 'selected' : '' }}>Belum Dikerjakan</option>
                        <option value="on_progress" {{ old('status') == 'on_progress' ? 'selected' : '' }}>On Progress</option>
                        <option value="selesai" {{ old('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                    </select>
                </div>

                <div class="d-flex gap-4 mt-6">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan
                    </button>
                    <button type="button" @click="showCreate = false" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <!-- Edit Modal (hanya untuk Daily Report hari ini) -->
    @if ($todayReport)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6" x-show="showEdit"
        @click.self="showEdit = false" style="display: none;">
        <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm"></div>
        <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
            <div class="p-5 border-b border-slate-100 flex justify-between items-start bg-slate-50/50">
                <div>
                    <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                        <i class="fas fa-pen text-amber-500"></i>
                        Edit Daily Report
                    </h3>
                    <p class="text-slate-500 text-sm mt-1">{{ $todayReport->date->translatedFormat('l, d F Y') }}</p>
                </div>
                <button type="button" @click="showEdit = false"
                    class="w-8 h-8 rounded-lg bg-slate-100 text-slate-500 flex items-center justify-center hover:bg-rose-50 hover:text-rose-500 transition-colors">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form action="{{ route('daily-reports.update', $todayReport) }}" method="POST" class="p-5" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label class="form-label">Deskripsi *</label>
                    <textarea name="description" class="form-control" rows="4" required>{{ old('description', $todayReport->description) }}</textarea>
                </div>

                <div class="form-group">
                    <label class="form-label">Gambar (opsional)</label>
                    @if ($todayReport->image)
                        <p class="text-xs text-slate-400 mb-1">Gambar saat ini tetap dipakai kalau tidak diganti.</p>
                    @endif
                    <input type="file" name="image" accept=".jpg,.jpeg,.png" class="form-control">
                    <p class="text-xs text-slate-400 mt-1">Format JPG/PNG, maksimal 5MB.</p>
                </div>

                <div class="form-group">
                    <label class="form-label">Target Selesai (opsional)</label>
                    <input type="date" name="target_date" class="form-control"
                        value="{{ old('target_date', $todayReport->target_date?->format('Y-m-d')) }}">
                </div>

                <div class="form-group">
                    <label class="form-label">Status *</label>
                    <select name="status" class="form-control" required>
                        <option value="belum_dikerjakan" {{ old('status', $todayReport->status) == 'belum_dikerjakan' ? 'selected' : '' }}>Belum Dikerjakan</option>
                        <option value="on_progress" {{ old('status', $todayReport->status) == 'on_progress' ? 'selected' : '' }}>On Progress</option>
                        <option value="selesai" {{ old('status', $todayReport->status) == 'selesai' ? 'selected' : '' }}>Selesai</option>
                    </select>
                </div>

                <div class="d-flex gap-4 mt-6">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan Perubahan
                    </button>
                    <button type="button" @click="showEdit = false" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
@endsection