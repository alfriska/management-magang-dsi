<div class="slide-up space-y-5">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-800 mb-1">Daftar Pekerjaan</h2>
            <p class="text-slate-400 text-sm">Kelola tugas untuk siswa magang</p>
        </div>
        <div class="flex gap-2">
            @if (auth()->user()->canManage())
                <button class="btn btn-secondary"
                    onclick="window.location.href='{{ route('export.tasks', array_filter(['status' => $status !== '' ? $status : null])) }}'">
                    <i class="fas fa-file-excel"></i>
                    <span class="hidden sm:inline">Export</span>
                </button>
                <a href="{{ route('tasks.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i>
                    <span class="hidden sm:inline">Buat Tugas Baru</span>
                </a>
            @endif
        </div>
    </div>

    <!-- Filter -->
    <div class="filter-bar">
        <div class="filter-group flex-[2]">
            <label>Cari</label>
            <div class="search-input">
                <input type="text" wire:model.live.debounce.300ms="search" class="form-control"
                    placeholder="Judul tugas...">
                <i class="fas fa-search"></i>
            </div>
        </div>
        <div class="filter-group max-w-[130px]">
            <label>Status</label>
            <select wire:model.live="status" class="form-control">
                <option value="">Semua</option>
                <option value="pending">Belum Mulai</option>
                <option value="in_progress">Dikerjakan</option>
                <option value="completed">Selesai</option>
                <option value="revision">Revisi</option>
            </select>
        </div>
        <div class="filter-group max-w-[120px]">
            <label>Prioritas</label>
            <select wire:model.live="priority" class="form-control">
                <option value="">Semua</option>
                <option value="high">🔴 Tinggi</option>
                <option value="medium">🟡 Sedang</option>
                <option value="low">🟢 Rendah</option>
            </select>
        </div>
        @if (auth()->user()->canManage())
            <div class="filter-group max-w-[150px]">
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
    @if (auth()->user()->canManage() && count($selectedTasks) > 0)
        <div class="bulk-action-bar p-4 flex flex-wrap items-center gap-3"
            style="background: linear-gradient(135deg, #a78bfa 0%, #c084fc 100%);">
            <div class="text-white font-semibold text-sm">
                <i class="fas fa-check-square"></i> {{ count($selectedTasks) }} dipilih
            </div>
            <div class="flex gap-2 flex-1">
                <select wire:model="bulkAction" class="form-control max-w-[200px]" style="background: white;">
                    <option value="">-- Pilih Aksi --</option>
                    <optgroup label="Status">
                        <option value="pending">📝 Belum Mulai</option>
                        <option value="in_progress">🔄 Dikerjakan</option>
                        <option value="completed">✅ Selesai</option>
                    </optgroup>
                    <optgroup label="Prioritas">
                        <option value="priority_high">🔴 Tinggi</option>
                        <option value="priority_medium">🟡 Sedang</option>
                        <option value="priority_low">🟢 Rendah</option>
                    </optgroup>
                    <optgroup label="Lainnya">
                        <option value="delete">🗑️ Hapus</option>
                    </optgroup>
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
        @if ($tasks->isEmpty())
            <div class="empty-state">
                <div class="empty-state-icon">
                    <i class="fas fa-tasks"></i>
                </div>
                <h4 class="empty-state-title">Belum Ada Tugas</h4>
                <p class="empty-state-text">Mulai dengan membuat tugas baru.</p>
                @if (auth()->user()->canManage())
                    <a href="{{ route('tasks.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Buat Tugas
                    </a>
                @endif
            </div>
        @else
            <!-- Desktop Table View -->
            <div class="hidden sm:block table-container">
                <table>
                    <thead>
                        <tr>
                            @if (auth()->user()->canManage())
                                <th class="w-12">
                                    <input type="checkbox" wire:model.live="selectAll" class="form-checkbox">
                                </th>
                            @endif
                            <th>Tugas</th>
                            @if (auth()->user()->canManage())
                                <th class="hidden md:table-cell">Siswa</th>
                            @endif
                            <th class="hidden sm:table-cell">Prioritas</th>
                            <th>Status</th>
                            <th class="hidden md:table-cell">Deadline</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($tasks as $task)
                            @php
                                $rowStyle = '';
                                if (in_array((string) $task->id, $selectedTasks)) {
                                    $rowStyle = 'background: rgba(167,139,250,0.08);';
                                } elseif ($task->status === 'revision') {
                                    $rowStyle = 'background: rgba(251,191,36,0.08); border-left: 3px solid #f59e0b;';
                                } elseif ($task->status === 'submitted') {
                                    $rowStyle = 'background: rgba(14,165,233,0.08); border-left: 3px solid #0ea5e9;';
                                }
                            @endphp
                            <tr wire:key="task-d-{{ $task->id }}" style="{{ $rowStyle }}">
                                @if (auth()->user()->canManage())
                                    <td>
                                        <input type="checkbox" wire:model.live="selectedTasks"
                                            value="{{ $task->id }}" class="form-checkbox">
                                    </td>
                                @endif
                                <td>
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span
                                            class="font-semibold text-slate-700 text-sm">{{ Str::limit($task->title, 30) }}</span>
                                        @if ($task->status === 'completed' && $task->is_late)
                                            <span class="badge badge-warning text-[9px]"><i
                                                    class="fas fa-clock"></i></span>
                                        @elseif($task->status === 'completed' && !$task->is_late)
                                            <span class="badge badge-success text-[9px]"><i
                                                    class="fas fa-check"></i></span>
                                        @elseif($task->status === 'revision')
                                            <span class="badge badge-warning text-[9px]">Revisi</span>
                                        @elseif($task->status === 'submitted')
                                            <span class="badge badge-info text-[9px]">Review</span>
                                        @elseif($task->isOverdue())
                                            <span class="badge badge-danger text-[9px]">Lewat!</span>
                                        @endif
                                    </div>
                                    <div class="text-slate-400 text-[11px] mt-0.5 flex items-center gap-1">
                                        @if ($task->submission_type === 'github')
                                            <i class="fab fa-github"></i>
                                        @elseif($task->submission_type === 'file')
                                            <i class="fas fa-folder"></i>
                                        @else
                                            <i class="fas fa-layer-group"></i>
                                        @endif
                                        {{ Str::limit($task->description, 40) }}
                                    </div>
                                </td>
                                @if (auth()->user()->canManage())
                                    <td class="hidden md:table-cell">
                                        @if ($task->intern)
                                            <div class="flex items-center gap-2">
                                                @if ($task->intern->user->avatar)
                                                    <img src="{{ Str::startsWith($task->intern->user->avatar, ['http', 'https']) ? $task->intern->user->avatar : asset('storage/avatars/' . $task->intern->user->avatar) }}"
                                                        alt="{{ $task->intern->user->name }}"
                                                        class="w-6 h-6 rounded-full object-cover ring-1 ring-emerald-400/50 flex-shrink-0"
                                                        referrerpolicy="no-referrer">
                                                @else
                                                    <div class="user-avatar w-6 h-6 text-[9px] flex-shrink-0">
                                                        {{ strtoupper(substr($task->intern->user->name ?? 'N', 0, 1)) }}
                                                    </div>
                                                @endif
                                                <span
                                                    class="text-xs text-slate-600">{{ $task->intern->user->name ?? 'N/A' }}</span>
                                            </div>
                                        @else
                                            <span class="badge badge-secondary text-[9px]">Dihapus</span>
                                        @endif
                                    </td>
                                @endif
                                <td class="hidden sm:table-cell">
                                    <span
                                        class="badge badge-{{ $task->priority_color }}">{{ ucfirst($task->priority) }}</span>
                                </td>
                                <td>
                                    <span
                                        class="badge badge-{{ $task->status_color }}">{{ $task->status_label }}</span>
                                </td>
                                <td class="hidden md:table-cell">
                                    @if ($task->deadline)
                                        <div class="text-xs text-slate-600">
                                            {{ $task->deadline->format('d M Y') }}
                                            @if ($task->deadline_time)
                                                <div class="text-slate-400 text-[10px]">{{ $task->deadline_time }}
                                                </div>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-slate-300">-</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="flex gap-1.5">
                                        <a href="{{ route('tasks.show', $task) }}" class="btn btn-sm btn-info"
                                            title="Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if (auth()->user()->canManage())
                                            <a href="{{ route('tasks.edit', $task) }}" class="btn btn-sm btn-warning"
                                                title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button wire:click="deleteTask({{ $task->id }})"
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

            <!-- Mobile Card View -->
            <div class="block sm:hidden p-4 space-y-4 bg-slate-50/50">
                @foreach ($tasks as $task)
                    <div wire:key="task-m-{{ $task->id }}"
                        class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden relative">
                        <!-- Status Strip -->
                        <div
                            class="absolute top-0 left-0 w-1 h-full
                        @if ($task->status === 'completed') bg-emerald-500
                        @elseif($task->status === 'submitted') bg-sky-500
                        @elseif($task->status === 'revision') bg-amber-500
                        @elseif($task->status === 'in_progress') bg-indigo-500
                        @else bg-slate-300 @endif
                    ">
                        </div>

                        <div class="p-4 pl-5">
                            <div class="flex justify-between items-start mb-2">
                                <div class="flex-1 mr-2">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span
                                            class="badge badge-{{ $task->priority_color }} text-[10px] px-1.5 py-0.5">
                                            {{ ucfirst($task->priority) }}
                                        </span>
                                        <span class="text-[10px] text-slate-400 flex items-center gap-1">
                                            @if ($task->submission_type === 'github')
                                                <i class="fab fa-github"></i>
                                            @elseif($task->submission_type === 'file')
                                                <i class="fas fa-folder"></i>
                                            @else
                                                <i class="fas fa-layer-group"></i>
                                            @endif
                                            {{ ucfirst($task->submission_type) }}
                                        </span>
                                        @if ($task->isOverdue())
                                            <span
                                                class="text-[10px] font-bold text-rose-500 bg-rose-50 px-1.5 py-0.5 rounded">
                                                <i class="fas fa-exclamation-circle"></i> Late
                                            </span>
                                        @endif
                                    </div>
                                    <h4 class="font-bold text-slate-800 text-sm line-clamp-1">{{ $task->title }}</h4>
                                </div>
                                <span class="badge badge-{{ $task->status_color }} text-[10px] whitespace-nowrap">
                                    {{ $task->status_label }}
                                </span>
                            </div>

                            @if (auth()->user()->canManage() && $task->intern)
                                <div
                                    class="flex items-center gap-2 mb-3 bg-slate-50 p-2 rounded-lg border border-slate-100">
                                    @if ($task->intern->user->avatar)
                                        <img src="{{ Str::startsWith($task->intern->user->avatar, ['http', 'https']) ? $task->intern->user->avatar : asset('storage/avatars/' . $task->intern->user->avatar) }}"
                                            alt="{{ $task->intern->user->name }}"
                                            class="w-6 h-6 rounded-full object-cover ring-1 ring-emerald-400/50 flex-shrink-0"
                                            referrerpolicy="no-referrer">
                                    @else
                                        <div class="user-avatar w-6 h-6 text-[10px] flex-shrink-0">
                                            {{ strtoupper(substr($task->intern->user->name ?? 'N', 0, 1)) }}
                                        </div>
                                    @endif
                                    <div class="flex flex-col">
                                        <span
                                            class="text-xs font-semibold text-slate-700">{{ $task->intern->user->name }}</span>
                                        <span class="text-[10px] text-slate-400">Assigned Intern</span>
                                    </div>
                                </div>
                            @endif

                            <div
                                class="flex items-center justify-between text-xs text-slate-500 mb-4 pt-2 border-t border-slate-100 border-dashed">
                                <div class="flex items-center gap-1.5">
                                    <i class="far fa-calendar-alt text-slate-400"></i>
                                    @if ($task->deadline)
                                        <span class="{{ $task->isOverdue() ? 'text-rose-500 font-bold' : '' }}">
                                            {{ $task->deadline->format('d M') }}
                                            @if ($task->deadline_time)
                                                {{ $task->deadline_time }}
                                            @endif
                                        </span>
                                    @else
                                        <span>No deadline</span>
                                    @endif
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <i class="far fa-clock text-slate-400"></i>
                                    <span>{{ $task->created_at->diffForHumans() }}</span>
                                </div>
                            </div>

                            <div class="grid {{ auth()->user()->canManage() ? 'grid-cols-3' : 'grid-cols-1' }} gap-2">
                                <a href="{{ route('tasks.show', $task) }}"
                                    class="btn btn-sm bg-indigo-50 text-indigo-600 hover:bg-indigo-100 border-0 justify-center">
                                    <i class="fas fa-eye mr-1"></i> Detail
                                </a>
                                @if (auth()->user()->canManage())
                                    <a href="{{ route('tasks.edit', $task) }}"
                                        class="btn btn-sm bg-amber-50 text-amber-600 hover:bg-amber-100 border-0 justify-center">
                                        <i class="fas fa-edit mr-1"></i> Edit
                                    </a>
                                    <button wire:click="deleteTask({{ $task->id }})" wire:confirm="Yakin?"
                                        class="btn btn-sm bg-rose-50 text-rose-600 hover:bg-rose-100 border-0 justify-center">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="pagination">
                {{ $tasks->links('vendor.livewire.simple-tailwind') }}
            </div>
        @endif
    </div>
</div>
