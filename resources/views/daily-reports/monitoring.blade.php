@extends('layouts.app')

@section('title', 'Daily Report')

@section('content')
<div class="slide-up space-y-5">
    <div>
        <h2 class="text-xl font-bold text-slate-800 mb-1">Daily Report</h2>
        <p class="text-slate-400 text-sm">Kelola daily report aktivitas.</p>
    </div>

    <!-- Filter Bar -->
    <form action="{{ route('daily-reports.monitoring') }}" method="GET" class="filter-bar">
        <div class="filter-group" style="max-width: 240px;">
            <label>Pilih Instansi</label>
            <select name="school" class="form-control">
                <option value="">Semua Instansi</option>
                @foreach ($schools as $school)
                    <option value="{{ $school }}" {{ $filterSchool == $school ? 'selected' : '' }}>{{ $school }}</option>
                @endforeach
            </select>
        </div>
        <div class="filter-group" style="max-width: 180px;">
            <label>Pilih Tanggal</label>
            <input type="date" name="date" class="form-control" value="{{ $filterDate }}">
        </div>
        <div class="filter-group" style="max-width: 160px; display: flex; align-items: flex-end; gap: 8px;">
            <button type="submit" class="btn btn-secondary">
                <i class="fas fa-filter"></i> Filter
            </button>
            @if ($filterDate || $filterSchool)
                <a href="{{ route('daily-reports.monitoring') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i>
                </a>
            @endif
        </div>
    </form>

    <!-- Evaluasi Kinerja Intern -->
    <div class="card">
               <div class="flex flex-col md:flex-row items-center justify-start gap-8" style="padding-left: 24px;">
            <div class="flex flex-col items-center text-center" style="min-width: 220px;">
                <h3 class="text-base font-bold text-slate-700 mb-1">Evaluasi Kinerja Intern</h3>
                <p class="text-slate-400 text-sm mb-4">Laporan Tanggal: {{ \Carbon\Carbon::parse($summaryDate)->translatedFormat('d M Y') }}</p>
                <div style="width: 160px; height: 160px; position: relative; flex-shrink: 0;">
                    <canvas id="daily-report-donut"></canvas>
                </div>
            </div>
            <div class="flex flex-col sm:flex-row gap-4">
                <div class="flex items-center gap-3 bg-violet-50 rounded-xl px-5 py-4">
                    <div class="w-10 h-10 rounded-full bg-violet-600 text-white flex items-center justify-center">
                        <i class="fas fa-check"></i>
                    </div>
                    <div>
                        <p class="text-sm text-slate-500">Sudah Lapor</p>
                        <p class="text-2xl font-bold text-slate-800">{{ $sudahLaporCount }} <span class="text-sm font-normal text-slate-400">Siswa</span></p>
                    </div>
                </div>
                <div class="flex items-center gap-3 bg-slate-100 rounded-xl px-5 py-4">
                    <div class="w-10 h-10 rounded-full bg-slate-400 text-white flex items-center justify-center">
                        <i class="fas fa-times"></i>
                    </div>
                    <div>
                        <p class="text-sm text-slate-500">Belum Lapor</p>
                        <p class="text-2xl font-bold text-slate-800">{{ $belumLaporCount }} <span class="text-sm font-normal text-slate-400">Siswa</span></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Daily Report -->
    <div class="card p-0 overflow-hidden">
        @if ($dailyReports->isEmpty())
            <div class="empty-state">
                <div class="empty-state-icon">
                    <i class="fas fa-clipboard-list"></i>
                </div>
                <h4 class="empty-state-title">Belum Ada Daily Report</h4>
                <p class="empty-state-text">
                    {{ $filterDate || $filterSchool ? 'Tidak ada Daily Report yang cocok dengan filter.' : 'Belum ada intern yang membuat Daily Report.' }}
                </p>
            </div>
        @else
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
                        @foreach ($dailyReports as $report)
                            <tr class="border-b border-slate-50 last:border-0 hover:bg-slate-50/50">
                                <td class="px-5 py-3 font-semibold text-slate-700 whitespace-nowrap">
                                    {{ $report->date->translatedFormat('d M Y') }}
                                </td>
                                <td class="px-5 py-3 text-slate-600 whitespace-nowrap">
                                    {{ $report->intern->user->name ?? '-' }}
                                </td>
                                <td class="px-5 py-3 text-slate-600">
                                    {{ \Illuminate\Support\Str::limit($report->description, 50) }}
                                </td>
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
                                        @if (auth()->user()->role === 'admin')
                                            <form action="{{ route('daily-reports.destroy', $report) }}" method="POST"
                                                onsubmit="return confirm('Yakin ingin menghapus Daily Report ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="w-8 h-8 rounded-lg bg-rose-100 text-rose-600 flex items-center justify-center hover:bg-rose-200 transition-colors"
                                                    title="Hapus">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    new Chart(document.getElementById('daily-report-donut').getContext('2d'), {
        type: 'doughnut',
        data: {
            labels: ['Sudah Lapor', 'Belum Lapor'],
            datasets: [{
                data: [{{ $sudahLaporCount }}, {{ $belumLaporCount }}],
                backgroundColor: ['#7c3aed', '#cbd5e1'],
                borderWidth: 0,
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '70%',
            plugins: {
                legend: { display: false }
            }
        }
    });
</script>
@endpush
@endsection