<div class="slide-up space-y-5">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-800 mb-1">Daftar Pembimbing</h2>
            <p class="text-slate-400 text-sm">Kelola data pembimbing magang</p>
        </div>
        <a href="{{ route('supervisors.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Tambah Pembimbing
        </a>
    </div>

    <!-- Pending Alert -->
    @if ($this->pendingCount > 0)
        <div class="p-4 rounded-xl flex items-center justify-between gap-4"
            style="background: linear-gradient(135deg, rgba(245,158,11,0.1) 0%, rgba(251,191,36,0.1) 100%); border: 1px solid rgba(245,158,11,0.2);">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-amber-500 text-white flex items-center justify-center">
                    <i class="fas fa-user-clock"></i>
                </div>
                <div>
                    <p class="font-semibold text-slate-700">{{ $this->pendingCount }} pendaftaran pembimbing menunggu
                        persetujuan</p>
                    <p class="text-sm text-slate-500">Klik tombol untuk melihat dan menyetujui pendaftaran</p>
                </div>
            </div>
            <a href="{{ route('supervisors.index', ['status' => 'pending']) }}"
                class="btn btn-warning whitespace-nowrap">
                <i class="fas fa-eye"></i> Lihat & Approve
            </a>
        </div>
    @endif

    <!-- Filter -->
    <div class="filter-bar">
        <div class="filter-group flex-[2]">
            <label>Cari</label>
            <div class="search-input">
                <input type="text" wire:model.live.debounce.300ms="search" class="form-control"
                    placeholder="Nama, email, NIP, atau instansi...">
                <i class="fas fa-search"></i>
            </div>
        </div>
        <div class="filter-group flex-1">
            <label>Status</label>
            <select wire:model.live="status" class="form-control">
                <option value="">Semua Status</option>
                <option value="pending">Menunggu Approval</option>
                <option value="active">Aktif</option>
            </select>
        </div>
        @if ($status === 'pending')
            <div class="filter-group">
                <label class="invisible">Bulk</label>
                <div class="flex gap-2">
                    <select wire:model="bulkAction" class="form-control text-sm">
                        <option value="">Pilih Aksi Massal...</option>
                        <option value="approve">✓ Approve Terpilih</option>
                        <option value="reject">✗ Tolak Terpilih</option>
                    </select>
                    <button wire:click="executeBulkAction"
                        wire:confirm="Yakin ingin menjalankan aksi ini untuk data terpilih?"
                        class="btn btn-sm btn-primary" {{ empty($selectedSupervisors) ? 'disabled' : '' }}>
                        Jalankan
                    </button>
                </div>
            </div>
        @endif
    </div>

    <!-- Table -->
    <div class="card p-0 overflow-hidden">
        @if ($supervisors->isEmpty())
            <div class="empty-state">
                <div class="empty-state-icon">
                    <i class="fas fa-user-tie"></i>
                </div>
                <h4 class="empty-state-title">Belum Ada Pembimbing</h4>
                <p class="empty-state-text">Mulai dengan menambahkan pembimbing baru.</p>
                <a href="{{ route('supervisors.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Tambah Pembimbing
                </a>
            </div>
        @else
            <!-- Desktop Table View -->
            <div class="hidden sm:block table-container">
                <table>
                    <thead>
                        <tr>
                            @if ($status === 'pending')
                                <th class="w-12">
                                    <input type="checkbox" wire:model.live="selectAll" class="rounded border-slate-300">
                                </th>
                            @endif
                            <th>Nama</th>
                            <th class="hidden sm:table-cell">Email</th>
                            <th>Instansi</th>
                            <th>Status</th>
                            <th class="hidden md:table-cell">Siswa</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($supervisors as $supervisor)
                            <tr wire:key="supervisor-d-{{ $supervisor->id }}">
                                @if ($status === 'pending')
                                    <td>
                                        <input type="checkbox" wire:model.live="selectedSupervisors"
                                            value="{{ $supervisor->id }}" class="rounded border-slate-300">
                                    </td>
                                @endif
                                <td>
                                    <div class="flex items-center gap-3">
                                        @if ($supervisor->user->avatar)
                                            <img src="{{ Str::startsWith($supervisor->user->avatar, ['http', 'https']) ? $supervisor->user->avatar : asset('storage/avatars/' . $supervisor->user->avatar) }}"
                                                alt="{{ $supervisor->user->name }}"
                                                class="w-8 h-8 rounded-full object-cover ring-2 ring-violet-400/50 flex-shrink-0"
                                                referrerpolicy="no-referrer">
                                        @else
                                            <div class="user-avatar w-8 h-8 text-sm flex-shrink-0">
                                                {{ strtoupper(substr($supervisor->user->name ?? 'X', 0, 1)) }}
                                            </div>
                                        @endif
                                        <div>
                                            <div class="font-semibold text-slate-700 text-sm">
                                                {{ $supervisor->user->name ?? '-' }}</div>
                                            @if ($supervisor->nip)
                                                <div class="text-slate-400 text-[11px]">NIP: {{ $supervisor->nip }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="hidden sm:table-cell text-sm text-slate-500">
                                    {{ $supervisor->user->email ?? '-' }}</td>
                                <td class="text-sm text-slate-600">{{ $supervisor->institution ?? '-' }}</td>
                                <td>
                                    @if ($supervisor->status === 'pending')
                                        <span class="badge badge-warning"><i class="fas fa-clock mr-1"></i>
                                            Pending</span>
                                    @else
                                        <span class="badge badge-success"><i class="fas fa-check mr-1"></i> Aktif</span>
                                    @endif
                                </td>
                                <td class="hidden md:table-cell">
                                    <span
                                        class="badge {{ ($supervisor->user->supervised_interns_count ?? 0) > 0 ? 'badge-info' : 'badge-secondary' }}">
                                        <i class="fas fa-users mr-1"></i>
                                        {{ $supervisor->user->supervised_interns_count ?? 0 }}
                                    </span>
                                </td>
                                <td>
                                    <div class="flex gap-1.5">
                                        @if ($supervisor->status === 'pending')
                                            <button wire:click="approveSupervisor({{ $supervisor->id }})"
                                                class="btn btn-sm btn-success" title="Approve">
                                                <i class="fas fa-check"></i>
                                            </button>
                                            <button wire:click="rejectSupervisor({{ $supervisor->id }})"
                                                wire:confirm="Yakin ingin menolak pendaftaran ini? Data akan dihapus."
                                                class="btn btn-sm btn-danger" title="Tolak">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        @else
                                            <a href="{{ route('supervisors.edit', $supervisor) }}"
                                                class="btn btn-sm btn-warning" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @if (($supervisor->user->supervised_interns_count ?? 0) == 0)
                                                <button wire:click="deleteSupervisor({{ $supervisor->id }})"
                                                    wire:confirm="Yakin ingin menghapus?" class="btn btn-sm btn-danger"
                                                    title="Hapus">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            @else
                                                <button class="btn btn-sm btn-secondary opacity-50 cursor-not-allowed"
                                                    disabled title="Memiliki siswa">
                                                    <i class="fas fa-lock"></i>
                                                </button>
                                            @endif
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Mobile Card View -->
            <div class="block sm:hidden p-4 space-y-4 bg-slate-50/50">
                @foreach ($supervisors as $supervisor)
                    <div wire:key="supervisor-m-{{ $supervisor->id }}"
                        class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                        <div class="p-5">
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex items-center gap-3">
                                    @if ($supervisor->user->avatar)
                                        <img src="{{ Str::startsWith($supervisor->user->avatar, ['http', 'https']) ? $supervisor->user->avatar : asset('storage/avatars/' . $supervisor->user->avatar) }}"
                                            alt="{{ $supervisor->user->name }}"
                                            class="w-8 h-8 rounded-full object-cover ring-2 ring-violet-400/50 flex-shrink-0"
                                            referrerpolicy="no-referrer">
                                    @else
                                        <div
                                            class="w-8 h-8 rounded-full bg-violet-100 text-violet-600 flex items-center justify-center text-lg font-bold flex-shrink-0">
                                            {{ strtoupper(substr($supervisor->user->name ?? 'X', 0, 1)) }}
                                        </div>
                                    @endif
                                    <div>
                                        <h4 class="font-bold text-slate-800">{{ $supervisor->user->name ?? '-' }}</h4>
                                        <p class="text-xs text-slate-400">{{ $supervisor->user->email ?? '-' }}</p>
                                    </div>
                                </div>
                                @if ($supervisor->status === 'pending')
                                    <span class="badge badge-warning"><i class="fas fa-clock mr-1"></i> Pending</span>
                                @else
                                    <span class="badge badge-success"><i class="fas fa-check mr-1"></i> Aktif</span>
                                @endif
                            </div>

                            <div class="text-xs text-slate-500 space-y-1 mb-4">
                                @if ($supervisor->nip)
                                    <div><i class="fas fa-id-badge mr-2"></i> NIP: {{ $supervisor->nip }}</div>
                                @endif
                                @if ($supervisor->institution)
                                    <div><i class="fas fa-building mr-2"></i> {{ $supervisor->institution }}</div>
                                @endif
                                @if ($supervisor->phone)
                                    <div><i class="fas fa-phone mr-2"></i> {{ $supervisor->phone }}</div>
                                @endif
                            </div>

                            <div class="grid grid-cols-2 gap-3 pt-4 border-t border-slate-100">
                                @if ($supervisor->status === 'pending')
                                    <button wire:click="approveSupervisor({{ $supervisor->id }})"
                                        class="btn bg-emerald-50 text-emerald-600 hover:bg-emerald-100 border-0 justify-center">
                                        <i class="fas fa-check mr-2"></i> Approve
                                    </button>
                                    <button wire:click="rejectSupervisor({{ $supervisor->id }})"
                                        wire:confirm="Yakin ingin menolak?"
                                        class="btn bg-rose-50 text-rose-600 hover:bg-rose-100 border-0 justify-center">
                                        <i class="fas fa-times mr-2"></i> Tolak
                                    </button>
                                @else
                                    <a href="{{ route('supervisors.edit', $supervisor) }}"
                                        class="btn bg-amber-50 text-amber-600 hover:bg-amber-100 border-0 justify-center">
                                        <i class="fas fa-edit mr-2"></i> Edit
                                    </a>
                                    @if (($supervisor->user->supervised_interns_count ?? 0) == 0)
                                        <button wire:click="deleteSupervisor({{ $supervisor->id }})"
                                            wire:confirm="Yakin ingin menghapus?"
                                            class="btn bg-rose-50 text-rose-600 hover:bg-rose-100 border-0 justify-center">
                                            <i class="fas fa-trash mr-2"></i> Hapus
                                        </button>
                                    @else
                                        <button
                                            class="btn bg-slate-100 text-slate-400 border-0 justify-center cursor-not-allowed opacity-70"
                                            disabled>
                                            <i class="fas fa-lock mr-2"></i> Terkunci
                                        </button>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="pagination">
                {{ $supervisors->links('vendor.livewire.simple-tailwind') }}
            </div>
        @endif
    </div>

</div>
