@extends('layouts.app')

@section('title', 'Buat Tugas Baru')

@section('content')
    <div class="max-w-5xl mx-auto space-y-8">
        <!-- Header Section -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-slate-900 tracking-tight">Buat Tugas Baru</h1>
                <p class="text-slate-500 mt-1">Delegasikan tugas kepada peserta magang untuk memulai produktivitas.</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('tasks.index') }}" 
                   class="inline-flex items-center px-4 py-2 bg-white border border-slate-200 rounded-xl text-sm font-medium text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition-all shadow-sm">
                    <i class="fas fa-arrow-left mr-2"></i> Kembali
                </a>
            </div>
        </div>

        @if($errors->any())
            <div class="bg-rose-50 border border-rose-100 rounded-2xl p-4 flex gap-3 animate-fade-in-up">
                <div class="shrink-0 text-rose-500">
                    <i class="fas fa-exclamation-circle text-xl"></i>
                </div>
                <div>
                    <h3 class="font-bold text-rose-900 text-sm">Terdapat kesalahan pada inputan</h3>
                    <ul class="list-disc list-inside text-sm text-rose-600 mt-1 space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <form action="{{ route('tasks.store') }}" method="POST" x-data="taskForm()" class="space-y-8">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
                
                <!-- Left Column: Main Details -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Section 1: Task Information -->
                    <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-xl shadow-slate-200/40">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-10 h-10 rounded-2xl bg-indigo-50 flex items-center justify-center text-indigo-600">
                                <i class="fas fa-layer-group text-lg"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-slate-900 text-lg">Detail Tugas</h3>
                                <p class="text-xs text-slate-500">Informasi utama mengenai tugas yang diberikan</p>
                            </div>
                        </div>

                        <div class="space-y-5">
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">
                                    Judul Tugas <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" name="title" value="{{ old('title') }}" 
                                       class="w-full px-4 py-3 rounded-xl border-slate-200 bg-slate-50/50 focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all"
                                       placeholder="Contoh: Implementasi Fitur Login" required>
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Deskripsi</label>
                                <div class="relative group">
                                    <textarea name="description" rows="6" 
                                              class="w-full px-4 py-3 rounded-xl border-slate-200 bg-slate-50/50 focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all resize-none"
                                              placeholder="Jelaskan detail tugas yang harus dikerjakan...">{{ old('description') }}</textarea>
                                    <div class="absolute bottom-3 right-3 text-xs text-slate-400 bg-white/80 backdrop-blur px-2 py-1 rounded-md border border-slate-100 shadow-sm pointer-events-none">
                                        Markdown Supported
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section 2: Target & Priority -->
                    <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-xl shadow-slate-200/40">
                         <div class="flex items-center gap-3 mb-6">
                            <div class="w-10 h-10 rounded-2xl bg-amber-50 flex items-center justify-center text-amber-600">
                                <i class="fas fa-users-cog text-lg"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-slate-900 text-lg">Target & Pengaturan</h3>
                                <p class="text-xs text-slate-500">Kepada siapa tugas ini diberikan dan prioritasnya</p>
                            </div>
                        </div>

                        <div class="space-y-6">
                            <!-- Assignment Target -->
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-3">Target Penugasan</label>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <label class="cursor-pointer group">
                                        <input type="radio" name="assign_to" value="all" class="peer sr-only" x-model="assignType">
                                        <div class="relative p-4 rounded-xl border-2 border-slate-100 bg-slate-50/30 hover:border-indigo-100 hover:bg-white transition-all peer-checked:border-indigo-500 peer-checked:bg-indigo-50/20 group-hover:shadow-md">
                                            <div class="flex items-center gap-3">
                                                <div class="w-10 h-10 rounded-full bg-white border border-slate-100 flex items-center justify-center text-slate-400 peer-checked:bg-indigo-500 peer-checked:text-white peer-checked:border-indigo-500 transition-all">
                                                    <i class="fas fa-users"></i>
                                                </div>
                                                <div>
                                                    <div class="font-bold text-slate-700 peer-checked:text-indigo-700">Semua Siswa</div>
                                                    <div class="text-xs text-slate-500">Tugaskan ke semua siswa aktif</div>
                                                </div>
                                            </div>
                                            <div class="absolute top-4 right-4 text-indigo-500 opacity-0 peer-checked:opacity-100 transition-opacity">
                                                <i class="fas fa-check-circle"></i>
                                            </div>
                                        </div>
                                    </label>

                                    <label class="cursor-pointer group">
                                        <input type="radio" name="assign_to" value="selected" class="peer sr-only" x-model="assignType">
                                        <div class="relative p-4 rounded-xl border-2 border-slate-100 bg-slate-50/30 hover:border-indigo-100 hover:bg-white transition-all peer-checked:border-indigo-500 peer-checked:bg-indigo-50/20 group-hover:shadow-md">
                                            <div class="flex items-center gap-3">
                                                <div class="w-10 h-10 rounded-full bg-white border border-slate-100 flex items-center justify-center text-slate-400 peer-checked:bg-indigo-500 peer-checked:text-white peer-checked:border-indigo-500 transition-all">
                                                    <i class="fas fa-user-check"></i>
                                                </div>
                                                <div>
                                                    <div class="font-bold text-slate-700 peer-checked:text-indigo-700">Pilih Siswa</div>
                                                    <div class="text-xs text-slate-500">Pilih beberapa siswa spesifik</div>
                                                </div>
                                            </div>
                                            <div class="absolute top-4 right-4 text-indigo-500 opacity-0 peer-checked:opacity-100 transition-opacity">
                                                <i class="fas fa-check-circle"></i>
                                            </div>
                                        </div>
                                    </label>
                                </div>

                                <!-- Intern Search & Selection -->
                                <div x-show="assignType === 'selected'" 
                                     x-transition:enter="transition ease-out duration-300"
                                     x-transition:enter-start="opacity-0 -translate-y-2 scale-95"
                                     x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                     class="mt-4 p-5 bg-slate-50 rounded-2xl border border-slate-200/60 shadow-inner">
                                    
                                    <!-- Search Bar -->
                                    <div class="mb-4">
                                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2 block">Cari Siswa</label>
                                        <div class="relative">
                                            <input type="text" x-model="searchQuery" @input.debounce.300ms="searchInterns()"
                                                   class="w-full pl-10 pr-4 py-2.5 rounded-xl border-slate-200 bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all text-sm"
                                                   placeholder="Cari nama, sekolah, atau jurusan...">
                                            <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                            <div x-show="isSearching" class="absolute right-3.5 top-1/2 -translate-y-1/2">
                                                <i class="fas fa-spinner fa-spin text-indigo-500"></i>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Selected interns -->
                                    <div x-show="selectedInterns.length > 0" class="mb-4">
                                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2 block">
                                            Siswa Terpilih (<span x-text="selectedInterns.length"></span>)
                                        </label>
                                        <div class="flex flex-wrap gap-2">
                                            <template x-for="intern in selectedInterns" :key="intern.id">
                                                <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-indigo-100 text-indigo-700 rounded-lg text-sm font-medium">
                                                    <span x-text="intern.name"></span>
                                                    <button type="button" @click="removeIntern(intern.id)" class="hover:text-indigo-900">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                    <input type="hidden" name="intern_ids[]" :value="intern.id">
                                                </div>
                                            </template>
                                        </div>
                                    </div>

                                    <!-- Search Results -->
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 max-h-60 overflow-y-auto pr-2 custom-scrollbar">
                                        <template x-for="intern in filteredInterns" :key="intern.id">
                                            <label class="flex items-center gap-3 p-3 bg-white rounded-xl border border-slate-200 hover:border-indigo-300 cursor-pointer transition-all hover:shadow-sm group"
                                                   :class="{'border-indigo-500 bg-indigo-50/50': isSelected(intern.id)}">
                                                <div class="relative flex items-center justify-center w-5 h-5">
                                                    <input type="checkbox" :checked="isSelected(intern.id)" @change="toggleIntern(intern)"
                                                           class="peer appearance-none w-5 h-5 border-2 border-slate-300 rounded-md checked:bg-indigo-500 checked:border-indigo-500 transition-colors">
                                                    <i class="fas fa-check text-white text-[10px] absolute opacity-0 peer-checked:opacity-100 pointer-events-none transition-opacity"></i>
                                                </div>
                                                <div class="flex items-center gap-3 min-w-0">
                                                    <div class="w-8 h-8 rounded-full bg-indigo-50 flex items-center justify-center text-xs font-bold text-indigo-600 shrink-0 border border-indigo-100"
                                                         x-text="intern.name.charAt(0).toUpperCase()"></div>
                                                    <div class="truncate">
                                                        <div class="font-bold text-slate-700 text-sm truncate group-hover:text-indigo-700 transition-colors" x-text="intern.name"></div>
                                                        <div class="text-[10px] text-slate-400 truncate" x-text="intern.school + ' - ' + intern.department"></div>
                                                    </div>
                                                </div>
                                            </label>
                                        </template>
                                    </div>
                                    
                                    <div x-show="filteredInterns.length === 0 && !isSearching" class="text-center py-4 text-slate-400 text-sm">
                                        <i class="fas fa-search mb-2 text-lg"></i>
                                        <p>Tidak ada siswa ditemukan</p>
                                    </div>
                                    
                                    @error('intern_ids')
                                        <p class="text-rose-500 text-xs mt-2 flex items-center gap-1"><i class="fas fa-times-circle"></i> {{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Priority -->
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-2">Tingkat Prioritas</label>
                                    <div class="flex p-1 bg-slate-100 rounded-xl">
                                        <label class="flex-1 cursor-pointer">
                                            <input type="radio" name="priority" value="low" class="peer sr-only" {{ old('priority') == 'low' ? 'checked' : '' }}>
                                            <div class="py-2 rounded-lg text-center text-xs font-bold text-slate-500 hover:bg-white hover:shadow-sm peer-checked:bg-white peer-checked:text-emerald-600 peer-checked:shadow-sm transition-all flex items-center justify-center gap-2">
                                                <i class="fas fa-arrow-down"></i> Low
                                            </div>
                                        </label>
                                        <label class="flex-1 cursor-pointer">
                                            <input type="radio" name="priority" value="medium" class="peer sr-only" {{ old('priority', 'medium') == 'medium' ? 'checked' : '' }}>
                                            <div class="py-2 rounded-lg text-center text-xs font-bold text-slate-500 hover:bg-white hover:shadow-sm peer-checked:bg-white peer-checked:text-amber-600 peer-checked:shadow-sm transition-all flex items-center justify-center gap-2">
                                                <i class="fas fa-minus"></i> Med
                                            </div>
                                        </label>
                                        <label class="flex-1 cursor-pointer">
                                            <input type="radio" name="priority" value="high" class="peer sr-only" {{ old('priority') == 'high' ? 'checked' : '' }}>
                                            <div class="py-2 rounded-lg text-center text-xs font-bold text-slate-500 hover:bg-white hover:shadow-sm peer-checked:bg-white peer-checked:text-rose-600 peer-checked:shadow-sm transition-all flex items-center justify-center gap-2">
                                                <i class="fas fa-arrow-up"></i> High
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Sidebar Settings -->
                <div class="space-y-6">
                    <!-- Timeline Card -->
                    <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-xl shadow-slate-200/40">
                         <div class="flex items-center gap-3 mb-6">
                            <div class="w-10 h-10 rounded-2xl bg-emerald-50 flex items-center justify-center text-emerald-600">
                                <i class="fas fa-calendar-alt text-lg"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-slate-900 text-lg">Timeline</h3>
                                <p class="text-xs text-slate-500">Jadwal penugasan</p>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase mb-1.5 flex justify-between">
                                    <span>Tanggal Mulai</span>
                                    <span class="text-[10px] bg-blue-100 px-1.5 py-0.5 rounded text-blue-600">Jadwalkan</span>
                                </label>
                                <div class="relative">
                                    <input type="date" name="start_date" value="{{ old('start_date', date('Y-m-d')) }}" 
                                           class="w-full pl-10 pr-4 py-2.5 rounded-xl border-slate-200 bg-slate-50 focus:bg-white focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all text-sm"
                                           min="{{ date('Y-m-d') }}">
                                    <i class="fas fa-play-circle absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                </div>
                                <p class="text-[10px] text-slate-400 mt-1">Notifikasi dikirim pada tanggal ini</p>
                            </div>

                            <hr class="border-slate-100">

                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase mb-1.5">Tanggal Deadline</label>
                                <div class="relative">
                                    <input type="date" name="deadline" value="{{ old('deadline') }}" 
                                           class="w-full pl-10 pr-4 py-2.5 rounded-xl border-slate-200 bg-slate-50 focus:bg-white focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all text-sm"
                                           min="{{ date('Y-m-d') }}">
                                    <i class="fas fa-calendar-day absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase mb-1.5">Jam Deadline</label>
                                <div class="relative">
                                    <input type="time" name="deadline_time" value="{{ old('deadline_time', '23:59') }}" 
                                           class="w-full pl-10 pr-4 py-2.5 rounded-xl border-slate-200 bg-slate-50 focus:bg-white focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all text-sm font-mono">
                                    <i class="fas fa-clock absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Info Card -->
                    <div class="bg-blue-50 rounded-2xl p-4 border border-blue-100">
                        <div class="flex gap-3">
                            <div class="shrink-0 text-blue-500">
                                <i class="fas fa-info-circle text-lg"></i>
                            </div>
                            <div class="text-sm text-blue-700">
                                <p class="font-medium mb-1">Pengumpulan via Link</p>
                                <p class="text-xs text-blue-600">Siswa dapat memasukkan beberapa link (repo, demo, dokumentasi, dll) saat mengumpulkan tugas.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Action Card -->
                    <div class="bg-indigo-600 rounded-3xl p-6 text-white shadow-xl shadow-indigo-500/30 overflow-hidden relative group">
                        <div class="absolute top-0 right-0 -mr-8 -mt-8 w-32 h-32 bg-white opacity-10 rounded-full blur-2xl group-hover:scale-150 transition-transform duration-700"></div>
                        
                        <h3 class="font-bold text-lg mb-2 relative z-10">Siap Publikasikan?</h3>
                        <p class="text-indigo-100 text-sm mb-6 relative z-10">Pastikan semua data sudah benar sebelum mengirim tugas ke siswa.</p>
                        
                        <button type="submit" class="w-full py-3.5 bg-white text-indigo-600 rounded-xl font-bold hover:bg-indigo-50 active:scale-95 transition-all shadow-lg flex items-center justify-center gap-2 relative z-10">
                            <i class="fas fa-paper-plane"></i> Buat Tugas Sekarang
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    @push('styles')
    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>
    @endpush

    @push('scripts')
    <script>
        function taskForm() {
            return {
                assignType: 'all',
                searchQuery: '',
                isSearching: false,
                allInterns: @json($internsJson),
                filteredInterns: [],
                selectedInterns: [],
                
                init() {
                    this.filteredInterns = this.allInterns.slice(0, 20);
                },
                
                searchInterns() {
                    if (!this.searchQuery) {
                        this.filteredInterns = this.allInterns.slice(0, 20);
                        return;
                    }
                    
                    this.isSearching = true;
                    const query = this.searchQuery.toLowerCase();
                    
                    this.filteredInterns = this.allInterns.filter(intern => {
                        return intern.name.toLowerCase().includes(query) ||
                               intern.school.toLowerCase().includes(query) ||
                               intern.department.toLowerCase().includes(query);
                    }).slice(0, 20);
                    
                    this.isSearching = false;
                },
                
                isSelected(id) {
                    return this.selectedInterns.some(i => i.id === id);
                },
                
                toggleIntern(intern) {
                    if (this.isSelected(intern.id)) {
                        this.removeIntern(intern.id);
                    } else {
                        this.selectedInterns.push(intern);
                    }
                },
                
                removeIntern(id) {
                    this.selectedInterns = this.selectedInterns.filter(i => i.id !== id);
                }
            }
        }
    </script>
    @endpush
@endsection
