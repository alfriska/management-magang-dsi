@extends('layouts.app')

@section('title', 'Laporan')

@section('content')
    <div class="slide-up space-y-5">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="text-xl font-bold text-slate-800 mb-1">Laporan Magang</h2>
                <p class="text-slate-400 text-sm">Kelola laporan perkembangan siswa</p>
            </div>
            <a href="{{ route('reports.create') }}" class="btn btn-primary w-full sm:w-auto">
                <i class="fas fa-plus"></i>
                <span class="hidden sm:inline">Buat Laporan</span>
                <span class="sm:hidden">Buat</span>
            </a>
        </div>

        <!-- Filter Bar -->
        <form action="{{ route('reports.index') }}" method="GET" class="filter-bar">
            <div class="filter-group" style="max-width: 180px;">
                <label>Tipe</label>
                <select name="type" class="form-control">
                    <option value="">Semua Tipe</option>
                    <option value="weekly" {{ request('type') === 'weekly' ? 'selected' : '' }}>Mingguan</option>
                    <option value="monthly" {{ request('type') === 'monthly' ? 'selected' : '' }}>Bulanan</option>
                    <option value="final" {{ request('type') === 'final' ? 'selected' : '' }}>Akhir</option>
                </select>
            </div>
            <div class="filter-group" style="max-width: 180px;">
                <label>Status</label>
                <select name="status" class="form-control">
                    <option value="">Semua Status</option>
                    <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="submitted" {{ request('status') === 'submitted' ? 'selected' : '' }}>Diajukan</option>
                    <option value="reviewed" {{ request('status') === 'reviewed' ? 'selected' : '' }}>Sudah Direview</option>
                </select>
            </div>
            <div class="filter-group" style="max-width: 200px;">
                <label>Siswa</label>
                <select name="intern_id" class="form-control">
                    <option value="">Semua Siswa</option>
                    @foreach($interns as $intern)
                        <option value="{{ $intern->id }}" {{ request('intern_id') == $intern->id ? 'selected' : '' }}>
                            {{ $intern->user->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="filter-group" style="max-width: 120px; display: flex; align-items: flex-end;">
                <button type="submit" class="btn btn-secondary">
                    <i class="fas fa-filter"></i> Filter
                </button>
            </div>
        </form>

        <div class="card">
            @if($reports->isEmpty())
                <div class="empty-state">
                    <div class="empty-state-icon">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <h4 class="empty-state-title">Belum Ada Laporan</h4>
                    <p class="empty-state-text">Mulai dengan membuat laporan baru.</p>
                    <a href="{{ route('reports.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Buat Laporan
                    </a>
                </div>
            @else
                <!-- Mobile/Tablet View (Cards) -->
                <div class="block lg:hidden space-y-4">
                    @foreach($reports as $report)
                        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm relative overflow-hidden">
                            <div class="flex items-start justify-between gap-4 mb-4">
                                <div class="flex items-center gap-3">
                                     <div class="w-10 h-10 rounded-full bg-violet-100 flex items-center justify-center text-violet-600 shrink-0">
                                        <i class="fas fa-file-alt"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <h4 class="font-bold text-slate-800 text-sm truncate">{{ Str::limit($report->title, 25) }}</h4>
                                        <div class="text-[11px] text-slate-400 truncate">Oleh: {{ $report->createdBy->name }}</div>
                                    </div>
                                </div>
                                <span class="badge badge-{{ $report->status_color }} text-[10px]">
                                    {{ $report->status_label }}
                                </span>
                            </div>

                            <div class="space-y-3 mb-4">
                                <div class="flex items-center justify-between text-xs">
                                    <span class="text-slate-400 font-medium">Siswa</span>
                                    <span class="font-bold text-slate-700">{{ $report->intern->user->name ?? 'N/A' }}</span>
                                </div>
                                <div class="flex items-center justify-between text-xs">
                                    <span class="text-slate-400 font-medium">Tipe</span>
                                    <span class="badge badge-primary px-2 py-0.5 text-[10px]">{{ $report->type_label }}</span>
                                </div>
                                <div class="flex items-center justify-between text-xs">
                                    <span class="text-slate-400 font-medium">Periode</span>
                                    <span class="font-bold text-slate-700">
                                        {{ $report->period_start->format('d M') }} - {{ $report->period_end->format('d M Y') }}
                                    </span>
                                </div>
                            </div>

                            <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
                                <a href="{{ route('reports.show', $report) }}" class="btn btn-sm btn-info" title="Detail">
                                    <i class="fas fa-eye text-xs"></i>
                                </a>
                                <a href="{{ route('reports.edit', $report) }}" class="btn btn-sm btn-warning" title="Edit">
                                    <i class="fas fa-edit text-xs"></i>
                                </a>
                                <form action="{{ route('reports.destroy', $report) }}" method="POST"
                                    onsubmit="return confirm('Yakin ingin menghapus laporan ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
                                        <i class="fas fa-trash text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Desktop/Laptop View (Table) -->
                <div class="hidden lg:block table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Judul</th>
                                <th class="hidden sm:table-cell">Siswa</th>
                                <th>Tipe</th>
                                <th class="hidden md:table-cell">Periode</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($reports as $report)
                                <tr>
                                    <td>
                                        <strong>{{ Str::limit($report->title, 35) }}</strong>
                                        <div class="text-muted" style="font-size: 12px;">
                                            Dibuat oleh {{ $report->createdBy->name }}
                                        </div>
                                    </td>
                                    <td class="hidden sm:table-cell">{{ $report->intern->user->name ?? 'N/A' }}</td>
                                    <td>
                                        <span class="badge badge-primary">{{ $report->type_label }}</span>
                                    </td>
                                    <td class="hidden md:table-cell">
                                        <div style="font-size: 13px;">
                                            {{ $report->period_start->format('d M') }} - {{ $report->period_end->format('d M Y') }}
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $report->status_color }}">
                                            {{ $report->status_label }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('reports.show', $report) }}" class="btn btn-sm btn-info"
                                                title="Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('reports.edit', $report) }}" class="btn btn-sm btn-warning"
                                                title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('reports.destroy', $report) }}" method="POST"
                                                onsubmit="return confirm('Yakin ingin menghapus laporan ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="pagination">
                    {{ $reports->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
