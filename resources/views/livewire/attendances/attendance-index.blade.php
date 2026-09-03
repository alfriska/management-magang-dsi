<div class="slide-up space-y-5">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-800 mb-1">Presensi Magang</h2>
            <p class="text-slate-400 text-sm">Kelola kehadiran siswa magang</p>
        </div>
        <div class="flex gap-2">
            @if (auth()->user()->canManage())
                <button class="btn btn-secondary"
                    onclick="window.location.href='{{ route('export.attendances', array_filter(['date' => $date, 'status' => $status !== '' ? $status : null])) }}'">
                    <i class="fas fa-file-excel"></i>
                    <span class="hidden sm:inline">Export Excel</span>
                </button>
                <a href="{{ route('attendances.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i>
                    <span class="hidden sm:inline">Tambah Presensi</span>
                </a>
            @endif
        </div>
    </div>

    <!-- Filter -->
    <div class="filter-bar">
        <div class="filter-group max-w-[150px]">
            <label>Tanggal</label>
            <input type="date" wire:model.live="date" class="form-control">
        </div>
        <div class="filter-group max-w-[150px]">
            <label>Bulan</label>
            <input type="month" wire:model.live="month" class="form-control">
        </div>
        <div class="filter-group max-w-[140px]">
            <label>Status</label>
            <select wire:model.live="status" class="form-control">
                <option value="">Semua</option>
                <option value="present">Hadir</option>
                <option value="late">Terlambat</option>
                <option value="absent">Tidak Hadir</option>
                <option value="sick">Sakit</option>
                <option value="permission">Izin</option>
            </select>
        </div>
        @if (auth()->user()->canManage())
            <div class="filter-group max-w-[160px]">
                <label>Siswa</label>
                <select wire:model.live="intern_id" class="form-control">
                    <option value="">Semua</option>
                    @foreach ($interns as $intern)
                        <option value="{{ $intern->id }}">{{ $intern->user->name }}</option>
                    @endforeach
                </select>
            </div>
        @endif
    </div>

    <!-- Bulk Actions -->
    @if (auth()->user()->canManage() && count($selectedAttendances) > 0)
        <div class="bulk-action-bar p-4 flex flex-wrap items-center gap-3"
            style="background: linear-gradient(135deg, #a78bfa 0%, #c084fc 100%);">
            <div class="text-white font-semibold text-sm">
                <i class="fas fa-check-square"></i> {{ count($selectedAttendances) }} dipilih
            </div>
            <div class="flex gap-2 flex-1">
                <select wire:model="bulkAction" class="form-control max-w-[180px]" style="background: white;">
                    <option value="">-- Pilih Aksi --</option>
                    <option value="present">✅ Hadir</option>
                    <option value="late">⏰ Terlambat</option>
                    <option value="absent">❌ Tidak Hadir</option>
                    <option value="sick">🏥 Sakit</option>
                    <option value="permission">📝 Izin</option>
                    <option value="delete">🗑️ Hapus</option>
                </select>
                <button wire:click="executeBulkAction" wire:confirm="Yakin?"
                    class="btn bg-white text-violet-600 hover:bg-violet-50">
                    <i class="fas fa-play"></i> Jalankan
                </button>
            </div>
            <button wire:click="resetBulkSelection" class="btn bg-white/20 text-white hover:bg-white/30">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    <!-- Table -->
    <div class="card p-0 overflow-hidden">
        @if ($attendances->isEmpty())
            <div class="empty-state">
                <div class="empty-state-icon">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <h4 class="empty-state-title">Belum Ada Presensi</h4>
                <p class="empty-state-text">Data presensi akan muncul di sini.</p>
                @if (auth()->user()->canManage())
                    <a href="{{ route('attendances.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Tambah Presensi
                    </a>
                @endif
            </div>
        @else
            <!-- Mobile/Tablet View (Cards) -->
            <div class="block lg:hidden space-y-4">
                @foreach ($attendances as $attendance)
                    <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm relative overflow-hidden">
                        <div class="flex items-start justify-between gap-4 mb-4">
                            @if (auth()->user()->canManage())
                                <div class="flex items-center gap-3">
                                    @if ($attendance->intern->user->avatar)
                                        <img src="{{ Str::startsWith($attendance->intern->user->avatar, ['http', 'https']) ? $attendance->intern->user->avatar : asset('storage/avatars/' . $attendance->intern->user->avatar) }}"
                                            alt="{{ $attendance->intern->user->name }}"
                                            class="w-10 h-10 rounded-full object-cover ring-2 ring-indigo-400/50 flex-shrink-0"
                                            referrerpolicy="no-referrer">
                                    @else
                                        <div
                                            class="user-avatar w-10 h-10 text-xs text-white shrink-0 bg-indigo-500 rounded-full flex items-center justify-center">
                                            {{ strtoupper(substr($attendance->intern->user->name ?? 'N', 0, 1)) }}
                                        </div>
                                    @endif
                                    <div class="min-w-0">
                                        <div class="font-bold text-slate-700 text-sm truncate">
                                            {{ $attendance->intern->user->name ?? 'N/A' }}</div>
                                        <div class="text-[11px] text-slate-400 truncate">
                                            {{ $attendance->intern->department ?? 'Magang' }}</div>
                                    </div>
                                </div>
                            @else
                                <div class="flex items-center gap-2 text-slate-700 font-bold">
                                    <i class="far fa-calendar-alt text-indigo-500"></i>
                                    {{ $attendance->date->format('d M Y') }}
                                </div>
                            @endif

                            <span class="badge badge-{{ $attendance->status_color }} text-[10px]">
                                {{ $attendance->status_label }}
                            </span>
                        </div>

                        <div class="grid grid-cols-2 gap-3 mb-4">
                            <div
                                class="p-3 bg-slate-50 rounded-xl border border-slate-100 flex flex-col justify-center text-center">
                                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1">Presensi
                                    Masuk</span>
                                <div class="text-sm font-black text-slate-700">
                                    @if ($attendance->check_in)
                                        <span
                                            class="text-emerald-600">{{ $attendance->check_in->format('H:i') }}</span>
                                    @else
                                        <span class="text-slate-300">-</span>
                                    @endif
                                </div>
                            </div>
                            <div
                                class="p-3 bg-slate-50 rounded-xl border border-slate-100 flex flex-col justify-center text-center">
                                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1">Presensi
                                    Keluar</span>
                                <div class="text-sm font-black text-slate-700">
                                    @if ($attendance->check_out)
                                        <span class="text-rose-600">{{ $attendance->check_out->format('H:i') }}</span>
                                    @else
                                        <span class="text-slate-300">-</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        @if (auth()->user()->canManage())
                            <div
                                class="flex items-center justify-between mb-4 bg-slate-50 p-2.5 rounded-lg border border-slate-100">
                                <span
                                    class="text-[10px] font-bold text-slate-500 uppercase tracking-wider ml-1">Tanggal</span>
                                <div class="text-xs font-bold text-slate-700">{{ $attendance->date->format('d F Y') }}
                                </div>
                            </div>
                        @endif

                        @if ($attendance->notes)
                            <div
                                class="p-3 bg-amber-50 rounded-lg text-xs text-amber-700 mb-4 border border-amber-100 italic relative pl-8">
                                <i class="fas fa-quote-left absolute left-3 top-3 opacity-20 text-xl"></i>
                                "{{ Str::limit($attendance->notes, 60) }}"
                            </div>
                        @endif

                        <div class="flex justify-between items-center pt-3 border-t border-slate-100 gap-3">
                            @if (auth()->user()->canManage())
                                <label class="flex items-center gap-2 cursor-pointer p-1">
                                    <input type="checkbox" wire:model.live="selectedAttendances"
                                        value="{{ $attendance->id }}"
                                        class="form-checkbox w-4 h-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                    <span class="text-xs font-bold text-slate-500 select-none">Pilih Item</span>
                                </label>
                            @else
                                <div></div>
                            @endif

                            <div class="flex gap-2">
                                <a href="{{ route('attendances.show', $attendance) }}"
                                    class="btn btn-sm btn-secondary" title="Detail">
                                    <i class="fas fa-eye text-xs"></i>
                                </a>
                                @if (auth()->user()->canManage())
                                    <a href="{{ route('attendances.edit', $attendance) }}"
                                        class="btn btn-sm btn-warning" title="Edit">
                                        <i class="fas fa-pencil-alt text-xs"></i>
                                    </a>
                                    <button wire:click="deleteAttendance({{ $attendance->id }})"
                                        wire:confirm="Yakin?" class="btn btn-sm btn-danger" title="Hapus">
                                        <i class="fas fa-trash-alt text-xs"></i>
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Desktop View (Table) -->
            <div class="hidden lg:block table-container">
                <table>
                    <thead>
                        <tr>
                            @if (auth()->user()->canManage())
                                <th class="w-12">
                                    <input type="checkbox" wire:model.live="selectAll" class="form-checkbox">
                                </th>
                                <th>Siswa</th>
                            @endif
                            <th>Tanggal</th>
                            <th>Presensi Masuk</th>
                            <th class="hidden sm:table-cell">Presensi Keluar</th>
                            <th>Status</th>
                            <th class="hidden md:table-cell">Keterangan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($attendances as $attendance)
                            <tr wire:key="attendance-{{ $attendance->id }}"
                                class="{{ in_array((string) $attendance->id, $selectedAttendances) ? 'selected-row' : '' }}">
                                @if (auth()->user()->canManage())
                                    <td>
                                        <input type="checkbox" wire:model.live="selectedAttendances"
                                            value="{{ $attendance->id }}" class="form-checkbox">
                                    </td>
                                    <td>
                                        @if ($attendance->intern)
                                            <div class="flex items-center gap-2">
                                                @if ($attendance->intern->user->avatar)
                                                    <img src="{{ Str::startsWith($attendance->intern->user->avatar, ['http', 'https']) ? $attendance->intern->user->avatar : asset('storage/avatars/' . $attendance->intern->user->avatar) }}"
                                                        alt="{{ $attendance->intern->user->name }}"
                                                        class="w-7 h-7 rounded-full object-cover ring-1 ring-indigo-400/50 flex-shrink-0"
                                                        referrerpolicy="no-referrer">
                                                @else
                                                    <div class="user-avatar w-7 h-7 text-[10px] flex-shrink-0">
                                                        {{ strtoupper(substr($attendance->intern->user->name ?? 'N', 0, 1)) }}
                                                    </div>
                                                @endif
                                                <span
                                                    class="text-sm text-slate-700">{{ $attendance->intern->user->name ?? 'N/A' }}</span>
                                            </div>
                                        @else
                                            <span class="badge badge-secondary text-[10px]">Dihapus</span>
                                        @endif
                                    </td>
                                @endif
                                <td class="text-sm text-slate-600">{{ $attendance->date->format('d M Y') }}</td>
                                <td>
                                    @if ($attendance->check_in)
                                        <span
                                            class="text-sm font-mono text-slate-700">{{ $attendance->check_in->format('H:i') }}</span>
                                    @else
                                        <span class="text-slate-300">-</span>
                                    @endif
                                </td>
                                <td class="hidden sm:table-cell">
                                    @if ($attendance->check_out)
                                        <span
                                            class="text-sm font-mono text-slate-700">{{ $attendance->check_out->format('H:i') }}</span>
                                    @else
                                        <span class="text-slate-300">-</span>
                                    @endif
                                </td>
                                <td>
                                    <span
                                        class="badge badge-{{ $attendance->status_color }}">{{ $attendance->status_label }}</span>
                                </td>
                                <td class="hidden md:table-cell text-xs text-slate-400">
                                    {{ Str::limit($attendance->notes ?? '-', 30) }}</td>
                                <td>
                                    <div class="flex gap-1.5">
                                        <a href="{{ route('attendances.show', $attendance) }}"
                                            class="btn btn-sm btn-primary" title="Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if (auth()->user()->canManage())
                                            <a href="{{ route('attendances.edit', $attendance) }}"
                                                class="btn btn-sm btn-warning" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button wire:click="deleteAttendance({{ $attendance->id }})"
                                                wire:confirm="Yakin?" class="btn btn-sm btn-danger" title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="pagination">
                {{ $attendances->links('vendor.livewire.simple-tailwind') }}
            </div>
        @endif
    </div>
</div>
