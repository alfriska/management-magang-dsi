<div class="slide-up space-y-5">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-800 mb-1">Penilaian Pekerjaan</h2>
            <p class="text-slate-400 text-sm">Evaluasi performa siswa magang</p>
        </div>
        <a href="{{ route('assessments.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Tambah Penilaian
        </a>
    </div>

    <!-- Filter -->
    <div class="filter-bar">
        <div class="filter-group max-w-[220px]">
            <label>Siswa</label>
            <select wire:model.live="intern_id" class="form-control">
                <option value="">Semua Siswa</option>
                @foreach ($interns as $intern)
                    <option value="{{ $intern->id }}">{{ $intern->user->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Table -->
    <div class="card p-0 overflow-hidden">
        {{-- Loading State --}}
        <div wire:loading.delay.longer class="p-6">
            <x-table-skeleton :rows="5" />
        </div>

        {{-- Content --}}
        <div wire:loading.delay.longer.remove>
            @if ($assessments->isEmpty())
                <div class="empty-state">
                    <div class="empty-state-icon">
                        <i class="fas fa-star"></i>
                    </div>
                    <h4 class="empty-state-title">Belum Ada Penilaian</h4>
                    <p class="empty-state-text">Mulai dengan memberikan penilaian untuk siswa.</p>
                    <a href="{{ route('assessments.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Tambah Penilaian
                    </a>
                </div>
            @else
                <!-- Mobile/Tablet View (Cards) -->
                <div class="block lg:hidden space-y-4 p-4 bg-slate-50/50">
                    @foreach ($assessments as $assessment)
                        <div
                            class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm relative overflow-hidden group hover:shadow-md transition-all">
                            <div class="flex items-start justify-between gap-4 mb-4">
                                <!-- Intern Info -->
                                @if ($assessment->intern)
                                    <div class="flex items-center gap-3">
                                        @if ($assessment->intern->user->avatar)
                                            <img src="{{ Str::startsWith($assessment->intern->user->avatar, ['http', 'https']) ? $assessment->intern->user->avatar : asset('storage/avatars/' . $assessment->intern->user->avatar) }}"
                                                alt="{{ $assessment->intern->user->name }}"
                                                class="w-12 h-12 rounded-full object-cover ring-2 ring-white shadow-sm flex-shrink-0"
                                                referrerpolicy="no-referrer">
                                        @else
                                            <div
                                                class="user-avatar w-12 h-12 text-sm shrink-0 ring-2 ring-white shadow-sm">
                                                {{ strtoupper(substr($assessment->intern->user->name ?? 'N', 0, 1)) }}
                                            </div>
                                        @endif
                                        <div class="min-w-0">
                                            <div class="font-bold text-slate-800 text-sm truncate">
                                                {{ $assessment->intern->user->name ?? 'N/A' }}</div>
                                            <div class="text-[11px] text-slate-400 truncate">
                                                {{ $assessment->intern->user->email ?? '' }}</div>
                                        </div>
                                    </div>
                                @else
                                    <span class="badge badge-secondary">Siswa Dihapus</span>
                                @endif

                                <!-- Grade Badge -->
                                <div class="flex flex-col items-end">
                                    <span
                                        class="badge badge-{{ $assessment->grade_color }} text-lg font-black px-3 py-1 shadow-sm">
                                        {{ $assessment->grade }}
                                    </span>
                                    <span
                                        class="text-[10px] font-bold text-slate-400 mt-1 uppercase tracking-wider">Grade</span>
                                </div>
                            </div>

                            <!-- Task Info -->
                            <div class="mb-4 pb-4 border-b border-slate-100">
                                <span
                                    class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Tugas
                                    / Penilaian</span>
                                <div class="font-bold text-slate-700 text-sm leading-snug">
                                    {{ $assessment->task->title ?? 'Penilaian Umum' }}
                                </div>
                            </div>

                            <!-- Scores Grid -->
                            <div class="grid grid-cols-2 gap-3 mb-4">
                                <div class="bg-slate-50 rounded-xl p-3 border border-slate-100">
                                    <span
                                        class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Rata-rata</span>
                                    <div class="text-2xl font-black text-slate-800">{{ $assessment->average_score }}
                                    </div>
                                </div>
                                <div
                                    class="bg-slate-50 rounded-xl p-3 border border-slate-100 flex flex-col justify-center gap-2">
                                    <div class="flex justify-between items-center text-xs">
                                        <span class="text-slate-500 font-medium">Kualitas</span>
                                        <span class="font-bold text-slate-700">{{ $assessment->quality_score }}</span>
                                    </div>
                                    <div class="flex justify-between items-center text-xs">
                                        <span class="text-slate-500 font-medium">Kecepatan</span>
                                        <span class="font-bold text-slate-700">{{ $assessment->speed_score }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('assessments.show', $assessment) }}"
                                    class="btn btn-sm btn-info w-full justify-center lg:w-auto" title="Detail">
                                    <i class="fas fa-eye mr-1"></i> Detail
                                </a>
                                <a href="{{ route('assessments.edit', $assessment) }}"
                                    class="btn btn-sm btn-warning w-full justify-center lg:w-auto" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button wire:click="deleteAssessment({{ $assessment->id }})" wire:confirm="Yakin?"
                                    class="btn btn-sm btn-danger w-full justify-center lg:w-auto" title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Desktop View (Table) -->
                <div class="hidden lg:block table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Siswa</th>
                                <th class="hidden md:table-cell">Tugas</th>
                                <th class="hidden lg:table-cell">Kualitas</th>
                                <th class="hidden lg:table-cell">Kecepatan</th>
                                <th class="hidden lg:table-cell">Inisiatif</th>
                                <th>Rata-rata</th>
                                <th>Grade</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($assessments as $assessment)
                                <tr wire:key="assessment-{{ $assessment->id }}">
                                    <td>
                                        @if ($assessment->intern)
                                            <div class="flex items-center gap-2">
                                                @if ($assessment->intern->user->avatar)
                                                    <img src="{{ Str::startsWith($assessment->intern->user->avatar, ['http', 'https']) ? $assessment->intern->user->avatar : asset('storage/avatars/' . $assessment->intern->user->avatar) }}"
                                                        alt="{{ $assessment->intern->user->name }}"
                                                        class="w-8 h-8 rounded-full object-cover ring-2 ring-emerald-400/50 flex-shrink-0"
                                                        referrerpolicy="no-referrer">
                                                @else
                                                    <div class="user-avatar w-8 h-8 text-xs flex-shrink-0">
                                                        {{ strtoupper(substr($assessment->intern->user->name ?? 'N', 0, 1)) }}
                                                    </div>
                                                @endif
                                                <span
                                                    class="text-sm text-slate-700">{{ $assessment->intern->user->name ?? 'N/A' }}</span>
                                            </div>
                                        @else
                                            <span class="badge badge-secondary text-[10px]">Dihapus</span>
                                        @endif
                                    </td>
                                    <td class="hidden md:table-cell text-sm text-slate-600">
                                        {{ Str::limit($assessment->task->title ?? 'Penilaian Umum', 25) }}</td>
                                    <td class="hidden lg:table-cell">
                                        <div class="flex items-center gap-2">
                                            <div class="progress w-14">
                                                <div class="progress-bar"
                                                    style="width: {{ $assessment->quality_score }}%;"></div>
                                            </div>
                                            <span
                                                class="text-xs text-slate-600">{{ $assessment->quality_score }}</span>
                                        </div>
                                    </td>
                                    <td class="hidden lg:table-cell">
                                        <div class="flex items-center gap-2">
                                            <div class="progress w-14">
                                                <div class="progress-bar bg-emerald-500"
                                                    style="width: {{ $assessment->speed_score }}%;"></div>
                                            </div>
                                            <span class="text-xs text-slate-600">{{ $assessment->speed_score }}</span>
                                        </div>
                                    </td>
                                    <td class="hidden lg:table-cell">
                                        <div class="flex items-center gap-2">
                                            <div class="progress w-14">
                                                <div class="progress-bar bg-amber-500"
                                                    style="width: {{ $assessment->initiative_score }}%;"></div>
                                            </div>
                                            <span
                                                class="text-xs text-slate-600">{{ $assessment->initiative_score }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <span
                                            class="text-lg font-bold text-slate-700">{{ $assessment->average_score }}</span>
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $assessment->grade_color }} text-sm px-3 py-1.5">
                                            {{ $assessment->grade }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="flex gap-1.5">
                                            <a href="{{ route('assessments.show', $assessment) }}"
                                                class="btn btn-sm btn-info" title="Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('assessments.edit', $assessment) }}"
                                                class="btn btn-sm btn-warning" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button wire:click="deleteAssessment({{ $assessment->id }})"
                                                wire:confirm="Yakin?" class="btn btn-sm btn-danger" title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="pagination">
                    {{ $assessments->links('vendor.livewire.simple-tailwind') }}
                </div>
            @endif
        </div>
    </div>
</div>
