<div class="slide-up space-y-5">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-800 mb-1">Daftar Anggota Magang</h2>
            <p class="text-slate-400 text-sm">Kelola data siswa magang</p>
        </div>
        <div class="flex gap-2">
            <div class="dropdown">
                <button class="btn btn-secondary" data-toggle="dropdown">
                    <i class="fas fa-file-export"></i>
                    <span class="hidden sm:inline">Export / Import</span>
                </button>
                <div class="dropdown-menu dropdown-menu-right">
                    <a class="dropdown-item" href="{{ route('export.interns') }}">
                        <i class="fas fa-file-excel text-emerald-500"></i> Export Excel
                    </a>
                    <a class="dropdown-item" href="{{ route('export.interns', ['status' => 'active']) }}">
                        <i class="fas fa-check-circle text-emerald-500"></i> Export Aktif
                    </a>
                    <a class="dropdown-item" href="{{ route('export.interns', ['status' => 'completed']) }}">
                        <i class="fas fa-graduation-cap text-violet-500"></i> Export Selesai
                    </a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="{{ route('import.interns.form') }}">
                        <i class="fas fa-upload text-sky-500"></i> Import Data
                    </a>
                    <a class="dropdown-item" href="{{ route('import.template') }}">
                        <i class="fas fa-download text-amber-500"></i> Download Template
                    </a>
                </div>
            </div>
            <a href="{{ route('interns.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i>
                <span class="hidden sm:inline">Tambah Anggota</span>
            </a>
        </div>
    </div>

    <!-- Filter -->
    <div class="filter-bar">
        <div class="filter-group flex-[2]">
            <label>Cari</label>
            <div class="search-input">
                <input type="text" wire:model.live.debounce.300ms="search" class="form-control"
                    placeholder="Nama, email, sekolah...">
                <i class="fas fa-search"></i>
            </div>
        </div>
        <div class="filter-group max-w-[180px]">
            <label>Status</label>
            <select wire:model.live="status" class="form-control">
                <option value="">Semua Status</option>
                <option value="pending">⏳ Menunggu Approval
                    {{ $this->pendingCount > 0 ? '(' . $this->pendingCount . ')' : '' }}</option>
                <option value="active">Aktif</option>
                <option value="completed">Selesai</option>
                <option value="cancelled">Dibatalkan</option>
            </select>
        </div>
    </div>

    <!-- Pending Alert -->
    @if ($this->pendingCount > 0 && $status !== 'pending')
        <div class="card p-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3"
            style="background: linear-gradient(135deg, rgba(251,191,36,0.1) 0%, rgba(245,158,11,0.1) 100%); border: 1px solid rgba(251,191,36,0.3);">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-amber-500 text-white flex items-center justify-center">
                    <i class="fas fa-user-clock"></i>
                </div>
                <div>
                    <p class="font-semibold text-amber-800">{{ $this->pendingCount }} Pendaftaran Menunggu Persetujuan
                    </p>
                    <p class="text-sm text-amber-600">Klik tombol di samping untuk mereview pendaftaran baru.</p>
                </div>
            </div>
            <button wire:click="$set('status', 'pending')" class="btn bg-amber-500 text-white hover:bg-amber-600">
                <i class="fas fa-eye"></i> Lihat Pendaftaran
            </button>
        </div>
    @endif

    <!-- Bulk Actions -->
    @if (count($selectedInterns) > 0)
        <div class="bulk-action-bar p-4 flex flex-wrap items-center gap-3"
            style="background: linear-gradient(135deg, #a78bfa 0%, #c084fc 100%);">
            <div class="text-white font-semibold text-sm">
                <i class="fas fa-check-square"></i> {{ count($selectedInterns) }} dipilih
            </div>
            <div class="flex gap-2 flex-1">
                <select wire:model="bulkAction" class="form-control max-w-[180px]" style="background: white;">
                    <option value="">-- Pilih Aksi --</option>
                    @if ($status === 'pending')
                        <option value="approve">✅ Approve Semua</option>
                        <option value="reject">❌ Tolak Semua</option>
                    @else
                        <option value="delete">🗑️ Hapus</option>
                        <option value="activate">✅ Set Aktif</option>
                        <option value="complete">🎓 Set Selesai</option>
                        <option value="cancel">❌ Set Dibatalkan</option>
                    @endif
                </select>
                <button wire:click="executeBulkAction" wire:confirm="Yakin ingin melakukan aksi ini?"
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
        {{-- Loading State --}}
        <div wire:loading.delay.longer class="p-6">
            <x-table-skeleton :rows="5" />
        </div>

        {{-- Content --}}
        <div wire:loading.delay.longer.remove>
            @if ($interns->isEmpty())
                <div class="empty-state">
                    <div class="empty-state-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h4 class="empty-state-title">Belum Ada Anggota Magang</h4>
                    <p class="empty-state-text">Mulai dengan menambahkan anggota magang baru.</p>
                    <a href="{{ route('interns.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Tambah Anggota
                    </a>
                </div>
            @else
                <!-- Desktop Table View -->
                <div class="hidden sm:block table-container">
                    <table class="table-fixed w-full">
                        <thead>
                            <tr>
                                <th class="w-12">
                                    <input type="checkbox" wire:model.live="selectAll" class="form-checkbox">
                                </th>
                                <th class="w-[200px]">Nama</th>
                                <th class="hidden lg:table-cell">Sekolah</th>
                                <th class="hidden lg:table-cell w-[140px]">Jurusan</th>
                                <th class="hidden md:table-cell w-[130px]">Pembimbing</th>
                                <th class="hidden md:table-cell w-[120px]">Periode</th>
                                <th class="w-[90px]">Status</th>
                                <th class="w-[140px]">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($interns as $intern)
                                <tr wire:key="intern-d-{{ $intern->id }}"
                                    class="{{ in_array((string) $intern->id, $selectedInterns) ? 'selected-row' : '' }}">
                                    <td>
                                        <input type="checkbox" wire:model.live="selectedInterns"
                                            value="{{ $intern->id }}" class="form-checkbox">
                                    </td>
                                    <td>
                                        <div class="flex items-center gap-2 min-w-0">
                                            @if ($intern->user->avatar)
                                                <img src="{{ Str::startsWith($intern->user->avatar, ['http', 'https']) ? $intern->user->avatar : asset('storage/avatars/' . $intern->user->avatar) }}"
                                                    alt="{{ $intern->user->name }}"
                                                    class="w-8 h-8 rounded-full object-cover ring-2 ring-emerald-400/50 flex-shrink-0"
                                                    referrerpolicy="no-referrer">
                                            @else
                                                <div class="user-avatar w-8 h-8 text-xs flex-shrink-0">
                                                    {{ strtoupper(substr($intern->user->name ?? 'N', 0, 1)) }}
                                                </div>
                                            @endif
                                            <div class="min-w-0 flex-1">
                                                <div class="font-semibold text-slate-600 text-sm truncate"
                                                    title="{{ $intern->user->name ?? 'N/A' }}">
                                                    {{ $intern->user->name ?? 'N/A' }}</div>
                                                <div class="text-slate-400 text-[11px] truncate">
                                                    {{ $intern->user->email ?? '' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="hidden lg:table-cell text-sm text-slate-600">
                                        <span class="block truncate"
                                            title="{{ $intern->school }}">{{ $intern->school }}</span>
                                    </td>
                                    <td class="hidden lg:table-cell text-sm text-slate-600">
                                        <span class="block truncate"
                                            title="{{ $intern->department }}">{{ $intern->department }}</span>
                                    </td>
                                    <td class="hidden md:table-cell text-sm text-slate-600">
                                        <span class="block truncate"
                                            title="{{ $intern->supervisor->name ?? '-' }}">{{ $intern->supervisor->name ?? '-' }}</span>
                                    </td>
                                    <td class="hidden md:table-cell">
                                        <div class="text-xs text-slate-600">
                                            {{ $intern->start_date->format('d M Y') }}
                                            <div class="text-slate-400">s/d {{ $intern->end_date->format('d M Y') }}
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if ($intern->status === 'pending')
                                            <span class="badge badge-warning">Menunggu</span>
                                        @elseif($intern->status === 'active')
                                            <span class="badge badge-success">Aktif</span>
                                        @elseif($intern->status === 'completed')
                                            <span class="badge badge-primary">Selesai</span>
                                        @else
                                            <span class="badge badge-danger">Dibatalkan</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="flex gap-1.5">
                                            @if ($intern->status === 'pending')
                                                <button wire:click="approveIntern({{ $intern->id }})"
                                                    wire:confirm="Approve pendaftaran {{ $intern->user->name }}?"
                                                    class="btn btn-sm btn-success" title="Approve">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                                <button wire:click="rejectIntern({{ $intern->id }})"
                                                    wire:confirm="Tolak dan hapus pendaftaran {{ $intern->user->name }}?"
                                                    class="btn btn-sm btn-danger" title="Reject">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            @else
                                                <a href="{{ route('interns.show', $intern) }}"
                                                    class="btn btn-sm btn-info" title="Detail">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('interns.edit', $intern) }}"
                                                    class="btn btn-sm btn-warning" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="{{ route('interns.downloadReport', $intern) }}"
                                                    class="btn btn-sm btn-success hidden sm:inline-flex"
                                                    title="PDF">
                                                    <i class="fas fa-file-pdf"></i>
                                                </a>
                                                @if ($intern->status === 'completed')
                                                    <a href="{{ route('interns.certificate', $intern) }}"
                                                        class="btn btn-sm btn-primary hidden sm:inline-flex"
                                                        title="Sertifikat" target="_blank">
                                                        <i class="fas fa-certificate"></i>
                                                    </a>
                                                @endif
                                                <button wire:click="deleteIntern({{ $intern->id }})"
                                                    wire:confirm="Yakin ingin menghapus?"
                                                    class="btn btn-sm btn-danger" title="Hapus">
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

                <!-- Mobile Card View -->
                <div class="block sm:hidden p-3 space-y-3 bg-slate-50/50">
                    @foreach ($interns as $intern)
                        <div wire:key="intern-m-{{ $intern->id }}"
                            class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden relative">
                            <!-- Status Strip -->
                            <div
                                class="absolute top-0 left-0 w-1 h-full
                            @if ($intern->status === 'active') bg-emerald-500
                            @elseif($intern->status === 'completed') bg-violet-500
                            @elseif($intern->status === 'pending') bg-amber-500
                            @else bg-rose-500 @endif">
                            </div>

                            <div class="p-3 pl-4">
                                <!-- Header with Name and Status -->
                                <div class="flex items-start justify-between gap-2 mb-2">
                                    <div class="flex items-center gap-2 min-w-0 flex-1">
                                        @if ($intern->user->avatar)
                                            <img src="{{ Str::startsWith($intern->user->avatar, ['http', 'https']) ? $intern->user->avatar : asset('storage/avatars/' . $intern->user->avatar) }}"
                                                alt="{{ $intern->user->name }}"
                                                class="w-9 h-9 rounded-full object-cover flex-shrink-0"
                                                referrerpolicy="no-referrer">
                                        @else
                                            <div class="user-avatar w-8 h-8 text-xs flex-shrink-0">
                                                {{ strtoupper(substr($intern->user->name ?? 'N', 0, 1)) }}
                                            </div>
                                        @endif
                                        <div class="min-w-0 flex-1">
                                            <h4 class="font-bold text-slate-800 text-sm leading-tight truncate">
                                                {{ $intern->user->name }}
                                            </h4>
                                            <p class="text-[11px] text-slate-400 truncate">{{ $intern->user->email }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="flex-shrink-0">
                                        @if ($intern->status === 'pending')
                                            <span class="badge badge-warning text-[10px] px-2 py-0.5">Menunggu</span>
                                        @elseif($intern->status === 'active')
                                            <span class="badge badge-success text-[10px] px-2 py-0.5">Aktif</span>
                                        @elseif($intern->status === 'completed')
                                            <span class="badge badge-primary text-[10px] px-2 py-0.5">Selesai</span>
                                        @else
                                            <span class="badge badge-danger text-[10px] px-2 py-0.5">Batal</span>
                                        @endif
                                    </div>
                                </div>

                                <!-- Details - Stacked Layout -->
                                <div class="space-y-1.5 mb-3 text-[11px] pl-11">
                                    <div class="flex items-start gap-2 text-slate-600">
                                        <i
                                            class="fas fa-school w-3.5 text-center text-slate-400 mt-0.5 flex-shrink-0"></i>
                                        <span
                                            class="font-medium leading-tight break-words">{{ $intern->school }}</span>
                                    </div>
                                    <div class="flex items-start gap-2 text-slate-500">
                                        <i
                                            class="fas fa-graduation-cap w-3.5 text-center text-slate-400 mt-0.5 flex-shrink-0"></i>
                                        <span class="leading-tight break-words">{{ $intern->department }}</span>
                                    </div>
                                    <div class="flex items-center gap-2 text-slate-500">
                                        <i class="fas fa-user-tie w-3.5 text-center text-slate-400 flex-shrink-0"></i>
                                        <span
                                            class="truncate">{{ $intern->supervisor->name ?? 'Belum ada pembimbing' }}</span>
                                    </div>
                                    <div class="flex items-center gap-2 text-slate-500">
                                        <i
                                            class="fas fa-calendar-alt w-3.5 text-center text-slate-400 flex-shrink-0"></i>
                                        <span>{{ $intern->start_date->format('d M') }} -
                                            {{ $intern->end_date->format('d M Y') }}</span>
                                    </div>
                                </div>

                                <!-- Actions -->
                                <div class="flex gap-2 pt-3 border-t border-slate-100">
                                    @if ($intern->status === 'pending')
                                        <button wire:click="approveIntern({{ $intern->id }})"
                                            wire:confirm="Approve pendaftaran {{ $intern->user->name }}?"
                                            class="flex-1 btn btn-sm bg-emerald-50 text-emerald-600 hover:bg-emerald-100">
                                            <i class="fas fa-check mr-1"></i> Approve
                                        </button>
                                        <button wire:click="rejectIntern({{ $intern->id }})"
                                            wire:confirm="Tolak dan hapus pendaftaran {{ $intern->user->name }}?"
                                            class="flex-1 btn btn-sm bg-rose-50 text-rose-600 hover:bg-rose-100">
                                            <i class="fas fa-times mr-1"></i> Tolak
                                        </button>
                                    @else
                                        <a href="{{ route('interns.show', $intern) }}"
                                            class="flex-1 btn btn-sm bg-indigo-50 text-indigo-600 hover:bg-indigo-100">
                                            <i class="fas fa-eye mr-1"></i> Detail
                                        </a>
                                        <a href="{{ route('interns.edit', $intern) }}"
                                            class="flex-1 btn btn-sm bg-amber-50 text-amber-600 hover:bg-amber-100">
                                            <i class="fas fa-edit mr-1"></i> Edit
                                        </a>
                                        <button wire:click="deleteIntern({{ $intern->id }})"
                                            wire:confirm="Yakin ingin menghapus?"
                                            class="btn btn-sm bg-rose-50 text-rose-600 hover:bg-rose-100 w-8">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="pagination">
                    {{ $interns->links('vendor.livewire.simple-tailwind') }}
                </div>
            @endif
        </div>
    </div>
</div>
