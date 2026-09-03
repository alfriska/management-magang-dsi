@extends('layouts.app')

@section('title', 'Detail Tugas')

@section('content')
    <div class="slide-up max-w-6xl mx-auto space-y-6">
        <!-- Header Navigation & Title -->
        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
            <a href="{{ route('tasks.index') }}" class="btn btn-icon btn-secondary">
                <i class="fas fa-arrow-left text-slate-500"></i>
            </a>
            <div class="flex-1 w-full">
                <div class="flex flex-col sm:flex-row sm:items-center gap-3 mb-2">
                    <h2 class="text-2xl font-bold text-slate-800 tracking-tight">{{ $task->title }}</h2>
                    <div class="flex flex-wrap gap-2">
                        <span
                            class="badge badge-{{ $task->priority_color }} uppercase text-[10px] tracking-wider px-2.5 py-1">
                            {{ ucfirst($task->priority) }}
                        </span>
                        <span
                            class="badge badge-{{ $task->status_color }} uppercase text-[10px] tracking-wider px-2.5 py-1">
                            {{ $task->status_label }}
                        </span>
                        @if ($task->is_late && $task->status === 'completed')
                            <span class="badge badge-warning uppercase text-[10px] tracking-wider px-2.5 py-1">
                                <i class="fas fa-clock mr-1"></i> Terlambat
                            </span>
                        @endif
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <div
                        class="w-6 h-6 rounded-full bg-slate-100 flex items-center justify-center text-[10px] font-bold text-slate-500">
                        {{ strtoupper(substr($task->assignedBy->name, 0, 1)) }}
                    </div>
                    <p class="text-slate-500 text-sm">Diberikan oleh <span
                            class="font-semibold text-slate-600">{{ $task->assignedBy->name }}</span></p>
                </div>
            </div>
            @if (auth()->user()->canManage())
                <a href="{{ route('tasks.edit', $task) }}" class="btn btn-warning shadow-sm">
                    <i class="fas fa-edit mr-2"></i> Edit
                </a>
            @endif
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Info Card -->
            <!-- Main Info Card -->
            <div class="lg:col-span-2 space-y-6">
                <div class="card p-0 overflow-hidden border border-indigo-100 shadow-indigo-100/50">
                    <div class="p-6 sm:p-8 bg-white relative overflow-hidden">
                        <div class="absolute top-0 right-0 p-4 opacity-5">
                            <i class="fas fa-tasks text-9xl transform -rotate-12"></i>
                        </div>

                        <div class="relative z-10">
                            <h3 class="font-bold text-slate-800 text-xl mb-6 flex items-center gap-3">
                                <div
                                    class="w-10 h-10 rounded-xl bg-indigo-100 text-indigo-600 flex items-center justify-center text-lg shadow-sm">
                                    <i class="fas fa-info-circle"></i>
                                </div>
                                Informasi Detail
                            </h3>

                            <div class="mb-8">
                                <label
                                    class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-3">Deskripsi
                                    Tugas</label>
                                <div
                                    class="prose prose-sm prose-slate max-w-none text-slate-600 leading-relaxed bg-slate-50/50 p-5 rounded-2xl border border-slate-100">
                                    {!! nl2br(e($task->description ?? 'Tidak ada deskripsi.')) !!}
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <!-- Assigned Intern (Already Updated) -->
                                <div
                                    class="group relative bg-white p-4 rounded-2xl border border-slate-200 shadow-sm hover:border-indigo-300 hover:shadow-md transition-all duration-300">
                                    <div
                                        class="absolute top-0 right-0 p-3 opacity-5 group-hover:opacity-10 transition-opacity">
                                        <i class="fas fa-user-graduate text-5xl text-indigo-600 transform -rotate-12"></i>
                                    </div>
                                    <label
                                        class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-3 group-hover:text-indigo-500 transition-colors">Ditugaskan
                                        Kepada</label>
                                    @if ($task->intern)
                                        <a href="{{ route('interns.show', $task->intern) }}"
                                            class="flex items-center gap-4 relative z-10">
                                            @if ($task->intern->user->avatar)
                                                <img src="{{ Str::startsWith($task->intern->user->avatar, ['http', 'https']) ? $task->intern->user->avatar : asset('storage/avatars/' . $task->intern->user->avatar) }}"
                                                    alt="{{ $task->intern->user->name }}" referrerpolicy="no-referrer"
                                                    class="w-12 h-12 rounded-full object-cover ring-2 ring-slate-100 group-hover:ring-indigo-100 transition-all">
                                            @else
                                                <div
                                                    class="w-12 h-12 rounded-full bg-gradient-to-br from-indigo-100 to-violet-100 text-indigo-600 flex items-center justify-center text-sm font-bold ring-2 ring-slate-100 group-hover:ring-indigo-100 transition-all">
                                                    {{ strtoupper(substr($task->intern->user->name ?? 'N', 0, 1)) }}
                                                </div>
                                            @endif
                                            <div class="flex-1 min-w-0">
                                                <div
                                                    class="font-bold text-slate-700 text-sm group-hover:text-indigo-700 transition-colors truncate">
                                                    {{ $task->intern->user->name }}</div>
                                                <div class="text-[11px] text-slate-500 truncate">
                                                    {{ $task->intern->department ?? 'Magang' }}</div>
                                            </div>
                                            <div
                                                class="w-8 h-8 rounded-full bg-slate-50 flex items-center justify-center text-slate-300 group-hover:bg-indigo-50 group-hover:text-indigo-500 transition-all">
                                                <i class="fas fa-chevron-right text-xs"></i>
                                            </div>
                                        </a>
                                    @else
                                        <div class="flex items-center gap-3 opacity-50">
                                            <div
                                                class="w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center text-slate-400">
                                                <i class="fas fa-user-slash"></i>
                                            </div>
                                            <div class="text-sm font-medium text-slate-500">Belum ada siswa</div>
                                        </div>
                                    @endif
                                </div>

                                <!-- Deadline Card -->
                                <div
                                    class="p-4 rounded-2xl border border-slate-200 bg-white relative overflow-hidden group hover:border-rose-200 transition-colors">
                                    <div
                                        class="absolute top-0 right-0 p-3 opacity-5 group-hover:opacity-10 transition-opacity">
                                        <i class="fas fa-calendar-alt text-5xl text-rose-500 transform -rotate-12"></i>
                                    </div>
                                    <label
                                        class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-3">Tenggat
                                        Waktu</label>
                                    @if ($task->deadline)
                                        <div class="flex items-center gap-4 relative z-10">
                                            <div
                                                class="w-12 h-12 rounded-xl bg-rose-50 text-rose-500 flex items-center justify-center shrink-0">
                                                <i class="far fa-calendar-check text-xl"></i>
                                            </div>
                                            <div>
                                                <strong
                                                    class="text-slate-700 text-sm block">{{ $task->deadline->format('d M Y') }}</strong>
                                                @if ($task->deadline_time)
                                                    <span
                                                        class="text-xs text-rose-500 font-bold bg-rose-50 px-2 py-0.5 rounded-md mt-1 inline-block">{{ $task->deadline_time }}
                                                        WIB</span>
                                                @endif
                                            </div>
                                        </div>
                                    @else
                                        <div class="flex items-center gap-3 opacity-50">
                                            <div
                                                class="w-12 h-12 rounded-xl bg-slate-100 flex items-center justify-center text-slate-400">
                                                <i class="fas fa-infinity"></i>
                                            </div>
                                            <span class="text-slate-400 text-sm">Tidak ada deadline</span>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Start Date Card -->
                            <div
                                class="p-4 rounded-2xl border border-slate-200 bg-white group hover:border-blue-200 transition-colors">
                                <label
                                    class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-3">Tanggal
                                    Mulai</label>
                                <div class="flex items-center gap-4">
                                    <div
                                        class="w-12 h-12 rounded-xl bg-blue-50 text-blue-500 flex items-center justify-center shrink-0">
                                        <i class="fas fa-play-circle text-xl"></i>
                                    </div>
                                    <div>
                                        <div class="font-bold text-slate-700 text-sm">
                                            {{ $task->start_date ? $task->start_date->format('d M Y') : 'Langsung' }}</div>
                                        <div class="text-xs text-slate-400">
                                            {{ $task->status === 'scheduled' ? 'Terjadwal' : 'Aktif' }}</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Submission Type Card -->
                            <div
                                class="p-4 rounded-2xl border border-slate-200 bg-white group hover:border-sky-200 transition-colors">
                                <label
                                    class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-3">Metode
                                    Pengumpulan</label>
                                <div class="flex items-center gap-4">
                                    <div
                                        class="w-12 h-12 rounded-xl bg-sky-50 text-sky-500 flex items-center justify-center shrink-0">
                                        <i class="fas fa-link text-xl"></i>
                                    </div>
                                    <div>
                                        <div class="font-bold text-slate-700 text-sm">Multiple Links</div>
                                        <div class="text-xs text-slate-400">Repo, Demo, Dokumentasi</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <!-- Timeline Card -->
                <div class="card p-6 sm:p-8">
                    <div class="flex items-center gap-3 mb-6 pb-4 border-b border-slate-100">
                        <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center">
                            <i class="fas fa-history text-lg"></i>
                        </div>
                        <h3 class="font-bold text-slate-800 text-lg">Timeline</h3>
                    </div>

                    <div class="relative pl-3">
                        <!-- Line -->
                        <div class="absolute left-[7px] top-2 bottom-6 w-0.5 bg-slate-200"></div>

                        <!-- Item 1: Created -->
                        <div class="mb-8 relative pl-8">
                            <div
                                class="absolute left-0 w-4 h-4 bg-white border-2 border-indigo-500 rounded-full ring-4 ring-indigo-500/10 z-10">
                            </div>
                            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">Dibuat</div>
                            <strong class="text-slate-700 text-sm block">{{ $task->created_at->format('d M Y') }}</strong>
                            <div class="text-xs text-slate-500">{{ $task->created_at->format('H:i') }} WIB</div>
                        </div>

                        <!-- Item 2: Started -->
                        @if ($task->started_at)
                            <div class="mb-8 relative pl-8">
                                <div
                                    class="absolute left-0 w-4 h-4 bg-white border-2 border-cyan-500 rounded-full ring-4 ring-cyan-500/10 z-10">
                                </div>
                                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">Mulai
                                    Dikerjakan</div>
                                <strong
                                    class="text-slate-700 text-sm block">{{ $task->started_at->format('d M Y') }}</strong>
                                <div class="text-xs text-slate-500">{{ $task->started_at->format('H:i') }} WIB</div>
                            </div>
                        @endif

                        <!-- Item 3: Submitted -->
                        @if ($task->submitted_at)
                            <div class="relative pl-8">
                                <div
                                    class="absolute left-0 w-4 h-4 bg-white border-2 {{ $task->is_late ? 'border-amber-500 ring-amber-500/10' : 'border-emerald-500 ring-emerald-500/10' }} rounded-full ring-4 z-10">
                                </div>
                                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">
                                    Dikumpulkan</div>
                                <strong
                                    class="text-slate-700 text-sm block">{{ $task->submitted_at->format('d M Y') }}</strong>
                                <div class="text-xs text-slate-500">{{ $task->submitted_at->format('H:i') }} WIB</div>
                                @if ($task->is_late)
                                    <div
                                        class="mt-2 inline-flex px-2 py-0.5 rounded text-[10px] font-bold bg-amber-50 text-amber-600 border border-amber-200">
                                        TERLAMBAT</div>
                                @else
                                    <div
                                        class="mt-2 inline-flex px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-600 border border-emerald-200">
                                        ON TIME</div>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Grading Result (If completed) -->
                @if ($task->status === 'completed' && $task->score !== null)
                    <div class="card p-0 overflow-hidden relative">
                        <div class="h-1.5 w-full bg-gradient-to-r from-emerald-500 via-emerald-400 to-teal-400"></div>
                        <div class="p-6 text-center">
                            <div class="relative w-28 h-28 mx-auto mb-4">
                                <!-- Progress Circle (Using simple SVG or CSS) -->
                                <svg class="w-full h-full transform -rotate-90 text-slate-100" viewBox="0 0 36 36">
                                    <path d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"
                                        fill="none" stroke="currentColor" stroke-width="2.5" />
                                    <path d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"
                                        fill="none" stroke="#22c55e" stroke-width="2.5"
                                        stroke-dasharray="{{ $task->score }}, 100" stroke-linecap="round"
                                        class="drop-shadow-sm" />
                                </svg>
                                <div class="absolute inset-0 flex flex-col items-center justify-center">
                                    <span
                                        class="text-3xl font-black text-emerald-700 leading-none">{{ $task->score }}</span>
                                    <span class="text-[9px] font-bold text-emerald-500 uppercase">POIN</span>
                                </div>
                            </div>

                            <div class="text-lg font-bold text-emerald-700 mb-1">
                                @if ($task->score >= 90)
                                    Excellent Job!
                                @elseif($task->score >= 75)
                                    Good Job!
                                @else
                                    Keep Going!
                                @endif
                            </div>
                            <div class="text-xs text-slate-500 mb-6">Dinilai pada
                                {{ $task->approved_at?->format('d M Y') }}</div>

                            @if ($task->admin_feedback)
                                <div class="bg-slate-50 rounded-xl p-4 text-left border border-slate-100 relative">
                                    <i class="fas fa-quote-left text-slate-200 text-2xl absolute -top-2 -left-2"></i>
                                    <p class="text-sm text-slate-600 italic relative z-10 leading-relaxed">
                                        "{{ $task->admin_feedback }}"
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Revisions Component -->
        @if ($task->status === 'revision')
            <div class="rounded-2xl border border-amber-200 bg-white overflow-hidden shadow-sm">
                <div class="bg-amber-50/50 p-6 border-b border-amber-100 flex items-start gap-5">
                    <div
                        class="w-12 h-12 rounded-xl bg-amber-100 border border-amber-200 flex items-center justify-center shrink-0">
                        <i class="fas fa-exclamation-triangle text-2xl text-amber-600"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-amber-800 mb-1">Perlu Revisi</h3>
                        <p class="text-sm text-amber-700">Tugas Anda memerlukan revisi. Silakan perbaiki berdasarkan
                            catatan di bawah ini.</p>
                    </div>
                </div>
                <div class="p-6">
                    <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-2">Catatan
                        Pembimbing</label>
                    <div
                        class="bg-slate-50 border-l-4 border-amber-500 rounded-r-xl p-5 mb-5 text-slate-700 text-sm leading-relaxed">
                        "{{ $task->admin_feedback ?? 'Mohon perbaiki tugas sesuai dengan instruksi awal.' }}"
                    </div>
                    @if (auth()->user()->isIntern())
                        <div
                            class="flex items-center gap-3 px-4 py-3 bg-rose-50 border border-rose-100 rounded-xl text-rose-700 text-sm font-medium">
                            <i class="fas fa-info-circle"></i>
                            Silakan upload ulang file/link revisi Anda pada form di bawah.
                        </div>
                    @endif
                </div>
            </div>
        @endif

        <!-- Submission Section for Interns -->
        @if (auth()->user()->isIntern() && ($task->status !== 'completed' && $task->status !== 'submitted'))
            <div class="card p-0 overflow-hidden" x-data="linksForm()">
                <div class="p-6 border-b border-slate-100">
                    <h3 class="font-bold text-slate-800 text-lg flex items-center gap-2">
                        <i class="fas fa-paper-plane text-violet-500"></i>
                        {{ $task->status === 'revision' ? 'Kumpulkan Revisi' : 'Kumpulkan Tugas' }}
                    </h3>
                </div>

                @if ($task->isOverdue())
                    <div
                        class="mx-6 mt-6 p-4 bg-amber-50 text-amber-700 border border-amber-200 rounded-xl flex items-center gap-3 text-sm font-medium">
                        <i class="fas fa-exclamation-triangle"></i>
                        Deadline sudah lewat. Pengumpulan akan dihitung terlambat.
                    </div>
                @endif

                <div class="p-6">
                    <form action="{{ route('tasks.submit', $task) }}" method="POST" class="space-y-6">
                        @csrf

                        <!-- Multiple Links Section -->
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <label class="form-label mb-0">
                                    <i class="fas fa-link"></i> Links Pengumpulan <span class="text-rose-500">*</span>
                                </label>
                                <button type="button" @click="addLink()" class="btn btn-secondary btn-sm">
                                    <i class="fas fa-plus mr-1"></i> Tambah Link
                                </button>
                            </div>

                            <div class="space-y-3">
                                <template x-for="(link, index) in links" :key="index">
                                    <div class="flex gap-3 items-start p-4 bg-slate-50 rounded-xl border border-slate-200">
                                        <div class="flex-1 grid grid-cols-1 sm:grid-cols-3 gap-3">
                                            <div>
                                                <input type="text" :name="'links[' + index + '][label]'"
                                                    x-model="link.label" class="form-control text-sm"
                                                    placeholder="Label (misal: Repository)" required>
                                            </div>
                                            <div class="sm:col-span-2">
                                                <input type="url" :name="'links[' + index + '][url]'"
                                                    x-model="link.url" class="form-control text-sm"
                                                    placeholder="https://..." required>
                                            </div>
                                        </div>
                                        <button type="button" @click="removeLink(index)" x-show="links.length > 1"
                                            class="btn btn-icon btn-sm text-rose-500 hover:bg-rose-50">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </template>
                            </div>

                            <p class="text-xs text-slate-400">Masukkan link repository, demo, dokumentasi, atau file di
                                cloud storage.</p>
                        </div>

                        <div class="form-group mb-0">
                            <label class="form-label"><i class="fas fa-comment"></i> Catatan Pengumpulan</label>
                            <textarea name="submission_notes" class="form-control" rows="3" placeholder="Tambahkan catatan...">{{ old('submission_notes', $task->submission_notes) }}</textarea>
                        </div>

                        <button type="submit" class="btn btn-primary w-full sm:w-auto">
                            <i class="fas fa-paper-plane mr-2"></i>
                            {{ $task->status === 'revision' ? 'Kirim Revisi' : 'Kumpulkan Tugas' }}
                        </button>
                    </form>
                </div>
            </div>

            <script>
                function linksForm() {
                    return {
                        links: @json($task->submission_links ?? [['label' => '', 'url' => '']]),

                        init() {
                            if (this.links.length === 0) {
                                this.links = [{
                                    label: '',
                                    url: ''
                                }];
                            }
                        },

                        addLink() {
                            this.links.push({
                                label: '',
                                url: ''
                            });
                        },

                        removeLink(index) {
                            if (this.links.length > 1) {
                                this.links.splice(index, 1);
                            }
                        }
                    }
                }
            </script>
        @endif

        <!-- Admin Review Form -->
        @if (auth()->user()->canManage() && $task->status === 'submitted')
            <div class="card p-0 overflow-hidden border-2 border-indigo-100 shadow-lg">
                <div class="p-6 bg-slate-50/50 border-b border-indigo-100">
                    <h3 class="font-bold text-slate-800 text-lg flex items-center gap-3">
                        <div
                            class="w-8 h-8 rounded-lg bg-indigo-100 text-indigo-600 flex items-center justify-center text-sm">
                            <i class="fas fa-gavel"></i>
                        </div>
                        Review & Penilaian
                    </h3>
                </div>

                <form action="{{ route('tasks.review', $task) }}" method="POST" class="p-6 md:p-8">
                    @csrf

                    <div class="mb-8">
                        <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-3">Keputusan
                            Pembimbing</label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- Approve Option -->
                            <label class="cursor-pointer group">
                                <input type="radio" name="action" value="approve" checked
                                    onchange="toggleScoreInput(true)" class="peer hidden" id="radioApprove">
                                <div
                                    class="p-5 rounded-2xl border-2 border-slate-200 bg-white hover:border-emerald-300 peer-checked:border-emerald-500 peer-checked:bg-emerald-50/30 transition-all text-center h-full flex flex-col items-center justify-center gap-3 group-hover:shadow-md">
                                    <div
                                        class="w-12 h-12 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center text-xl transition-transform group-hover:scale-110">
                                        <i class="fas fa-check"></i>
                                    </div>
                                    <div>
                                        <div class="font-bold text-slate-700 peer-checked:text-emerald-700 text-base">
                                            Terima & Nilai</div>
                                        <div class="text-sm text-slate-400 peer-checked:text-emerald-600">Tugas sudah
                                            sesuai</div>
                                    </div>
                                </div>
                            </label>

                            <!-- Revision Option -->
                            <label class="cursor-pointer group">
                                <input type="radio" name="action" value="revision" onchange="toggleScoreInput(false)"
                                    class="peer hidden" id="radioRevision">
                                <div
                                    class="p-5 rounded-2xl border-2 border-slate-200 bg-white hover:border-amber-300 peer-checked:border-amber-500 peer-checked:bg-amber-50/30 transition-all text-center h-full flex flex-col items-center justify-center gap-3 group-hover:shadow-md">
                                    <div
                                        class="w-12 h-12 rounded-full bg-amber-100 text-amber-600 flex items-center justify-center text-xl transition-transform group-hover:scale-110">
                                        <i class="fas fa-redo"></i>
                                    </div>
                                    <div>
                                        <div class="font-bold text-slate-700 peer-checked:text-amber-700 text-base">Minta
                                            Revisi</div>
                                        <div class="text-sm text-slate-400 peer-checked:text-amber-600">Kembalikan ke siswa
                                        </div>
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div id="scoreInputGroup"
                        class="mb-6 p-5 bg-slate-50 border border-slate-200 rounded-2xl animate-fade-in">
                        <label class="font-bold text-slate-700 block mb-2">Berikan Nilai (0-100)</label>
                        <div class="relative max-w-[200px]">
                            <input type="number" name="score" class="form-control text-2xl font-bold py-3 pl-4 pr-16"
                                min="0" max="100" placeholder="0" required>
                            <span class="absolute right-4 top-1/2 -translate-y-1/2 font-bold text-slate-400">/ 100</span>
                        </div>
                    </div>

                    <div class="form-group mb-8">
                        <label class="font-bold text-slate-700 block mb-2">Feedback / Catatan</label>
                        <textarea name="feedback" class="form-control" rows="3" placeholder="Tuliskan masukan untuk siswa..."></textarea>
                    </div>

                    <button type="submit"
                        class="btn btn-primary w-full py-3.5 text-base shadow-lg shadow-indigo-500/20 hover:scale-[1.01] active:scale-[0.99] transition-all">
                        <i class="fas fa-paper-plane mr-2"></i> Kirim Keputusan
                    </button>
                </form>
            </div>

            <script>
                function toggleScoreInput(show) {
                    const group = document.getElementById('scoreInputGroup');
                    const input = group.querySelector('input');
                    if (show) {
                        group.style.display = 'block';
                        input.setAttribute('required', 'required');
                    } else {
                        group.style.display = 'none';
                        input.removeAttribute('required');
                        input.value = '';
                    }
                }
            </script>
        @endif

        <!-- Submitted Task Read-Only Info -->
        @if (in_array($task->status, ['completed', 'submitted', 'revision']))
            <div class="card p-0 overflow-hidden border border-slate-200">
                @php
                    $colors = match ($task->status) {
                        'completed' => [
                            'bg' => 'bg-emerald-50',
                            'text' => 'text-emerald-700',
                            'icon' => 'text-emerald-600',
                            'icon_bg' => 'bg-emerald-100',
                            'border' => 'border-emerald-100',
                        ],
                        'submitted' => [
                            'bg' => 'bg-sky-50',
                            'text' => 'text-sky-700',
                            'icon' => 'text-sky-600',
                            'icon_bg' => 'bg-sky-100',
                            'border' => 'border-sky-100',
                        ],
                        'revision' => [
                            'bg' => 'bg-amber-50',
                            'text' => 'text-amber-700',
                            'icon' => 'text-amber-600',
                            'icon_bg' => 'bg-amber-100',
                            'border' => 'border-amber-100',
                        ],
                        default => [
                            'bg' => 'bg-slate-50',
                            'text' => 'text-slate-700',
                            'icon' => 'text-slate-600',
                            'icon_bg' => 'bg-slate-100',
                            'border' => 'border-slate-100',
                        ],
                    };
                @endphp

                <div class="{{ $colors['bg'] }} p-6 border-b {{ $colors['border'] }} flex items-center gap-4">
                    <div
                        class="w-12 h-12 rounded-xl {{ $colors['icon_bg'] }} {{ $colors['icon'] }} flex items-center justify-center shrink-0">
                        @if ($task->status === 'completed')
                            <i class="fas fa-check-circle text-2xl"></i>
                        @elseif($task->status === 'revision')
                            <i class="fas fa-history text-2xl"></i>
                        @else
                            <i class="fas fa-hourglass-half text-2xl"></i>
                        @endif
                    </div>
                    <div>
                        <h3 class="font-bold text-lg {{ $colors['text'] }}">
                            @if ($task->status === 'completed')
                                Tugas Selesai
                            @elseif($task->status === 'revision')
                                Riwayat Pengumpulan
                            @else
                                Menunggu Review
                            @endif
                        </h3>
                        <div class="text-sm text-slate-500 flex items-center gap-2 mt-0.5">
                            <span><i class="far fa-clock mr-1"></i> Dikumpulkan
                                {{ $task->submitted_at?->diffForHumans() ?? '-' }}</span>
                            @if ($task->is_late)
                                <span
                                    class="badge badge-warning text-[10px] px-2 py-0.5 uppercase tracking-wide">Terlambat</span>
                            @else
                                <span class="badge badge-success text-[10px] px-2 py-0.5 uppercase tracking-wide">Tepat
                                    Waktu</span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="p-6 bg-white space-y-6">
                    @if ($task->submission_links && is_array($task->submission_links) && count($task->submission_links) > 0)
                        <div>
                            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-3">Links
                                Pengumpulan</span>
                            <div class="space-y-2">
                                @foreach ($task->submission_links as $link)
                                    <a href="{{ $link['url'] }}" target="_blank"
                                        class="flex items-center gap-3 p-3 bg-slate-50 border border-slate-200 rounded-xl hover:bg-slate-100 transition-colors group">
                                        <div
                                            class="w-8 h-8 bg-indigo-100 text-indigo-600 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-link"></i>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="font-medium text-slate-700 text-sm">{{ $link['label'] }}</div>
                                            <div class="text-xs text-slate-400 truncate">{{ $link['url'] }}</div>
                                        </div>
                                        <i class="fas fa-external-link-alt text-slate-400 group-hover:text-slate-600"></i>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if ($task->submission_notes)
                        <div>
                            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-2">Catatan
                                Siswa</span>
                            <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-600">
                                {{ $task->submission_notes }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>
@endsection
