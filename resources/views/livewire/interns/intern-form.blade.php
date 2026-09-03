<div class="slide-up max-w-5xl mx-auto space-y-5">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-800 mb-1">{{ $pageTitle }}</h2>
            <p class="text-slate-400 text-sm">Isi data lengkap anggota magang baru</p>
        </div>
        <a href="{{ route('interns.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="card overflow-hidden p-0">
        <form wire:submit="save">

            <!-- Section: Informasi Akun -->
            <div class="p-6 sm:p-8">
                <div class="flex items-center gap-3 mb-6 pb-4" style="border-bottom: 1px solid rgba(148,163,184,0.1);">
                    <div class="w-10 h-10 rounded-xl bg-violet-100 text-violet-600 flex items-center justify-center">
                        <i class="fas fa-user-shield text-lg"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-800 text-base">Informasi Akun</h4>
                        <p class="text-sm text-slate-400">Credential login untuk siswa magang</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="form-group mb-0">
                        <label class="form-label">Nama Lengkap</label>
                        <div class="search-input">
                            <input type="text" wire:model="name" class="form-control @error('name') !border-rose-400 @enderror" placeholder="Contoh: Ahmad Fauzi">
                            <i class="fas fa-user"></i>
                        </div>
                        @error('name') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group mb-0">
                        <label class="form-label">Email Address</label>
                        <div class="search-input">
                            <input type="email" wire:model="email" class="form-control @error('email') !border-rose-400 @enderror" placeholder="email@sekolah.com">
                            <i class="fas fa-envelope"></i>
                        </div>
                        @error('email') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    @if(!$isEditing)
                    <div class="form-group mb-0 md:col-span-2">
                        <label class="form-label">Password</label>
                        <div class="search-input">
                            <input type="password" wire:model="password" class="form-control @error('password') !border-rose-400 @enderror" placeholder="Minimal 8 karakter">
                            <i class="fas fa-lock"></i>
                        </div>
                        <p class="text-xs text-slate-400 mt-1.5">Gunakan kombinasi huruf dan angka untuk keamanan.</p>
                        @error('password') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    @endif
                </div>
            </div>

            <div class="h-2" style="background: rgba(248,250,252,0.8);"></div>

            <!-- Section: Data Profil -->
            <div class="p-6 sm:p-8">
                <div class="flex items-center gap-3 mb-6 pb-4" style="border-bottom: 1px solid rgba(148,163,184,0.1);">
                    <div class="w-10 h-10 rounded-xl bg-violet-100 text-violet-600 flex items-center justify-center">
                        <i class="fas fa-id-card text-lg"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-800 text-base">Data Pribadi</h4>
                        <p class="text-sm text-slate-400">Informasi detail siswa magang</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="form-group mb-0">
                        <label class="form-label">NISN / NIM</label>
                        <input type="text" wire:model="nis" class="form-control @error('nis') !border-rose-400 @enderror" placeholder="Nomor Induk">
                        @error('nis') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group mb-0">
                        <label class="form-label">WhatsApp / Telepon</label>
                        <div class="search-input">
                            <input type="text" wire:model="phone" class="form-control @error('phone') !border-rose-400 @enderror" placeholder="08xxxxxxxxxx">
                            <i class="fas fa-phone"></i>
                        </div>
                        @error('phone') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group mb-0">
                        <label class="form-label">Asal Sekolah / Kampus</label>
                        <div class="search-input">
                            <input type="text" wire:model="school" class="form-control @error('school') !border-rose-400 @enderror" placeholder="Nama Instansi Pendidikan">
                            <i class="fas fa-school"></i>
                        </div>
                        @error('school') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group mb-0">
                        <label class="form-label">Jurusan / Bidang Studi</label>
                        <div class="search-input">
                            <input type="text" wire:model="department" class="form-control @error('department') !border-rose-400 @enderror" placeholder="Contoh: RPL, TKJ">
                            <i class="fas fa-graduation-cap"></i>
                        </div>
                        @error('department') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group mb-0 md:col-span-2">
                        <label class="form-label">Alamat</label>
                        <textarea wire:model="address" class="form-control @error('address') !border-rose-400 @enderror" rows="2" placeholder="Alamat tempat tinggal saat ini"></textarea>
                        @error('address') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <div class="h-2" style="background: rgba(248,250,252,0.8);"></div>

            <!-- Section: Periode Magang -->
            <div class="p-6 sm:p-8">
                <div class="flex items-center gap-3 mb-6 pb-4" style="border-bottom: 1px solid rgba(148,163,184,0.1);">
                    <div class="w-10 h-10 rounded-xl bg-violet-100 text-violet-600 flex items-center justify-center">
                        <i class="fas fa-clock text-lg"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-800 text-base">Periode Magang</h4>
                        <p class="text-sm text-slate-400">Durasi dan pembimbing lapangan</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="form-group mb-0">
                        <label class="form-label">Tanggal Mulai</label>
                        <input type="date" wire:model="start_date" class="form-control @error('start_date') !border-rose-400 @enderror">
                        @error('start_date') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group mb-0">
                        <label class="form-label">Tanggal Selesai</label>
                        <input type="date" wire:model="end_date" class="form-control @error('end_date') !border-rose-400 @enderror">
                        @error('end_date') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group mb-0 {{ !$isEditing ? 'md:col-span-2' : '' }}">
                        <label class="form-label">Pembimbing Lapangan</label>
                        <div class="search-input">
                            <select wire:model="supervisor_id" class="form-control @error('supervisor_id') !border-rose-400 @enderror">
                                <option value="">Pilih Pembimbing</option>
                                @foreach($supervisors as $supervisor)
                                    <option value="{{ $supervisor->id }}">{{ $supervisor->name }}</option>
                                @endforeach
                            </select>
                            <i class="fas fa-chalkboard-teacher"></i>
                        </div>
                        @error('supervisor_id') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    @if($isEditing)
                        <div class="form-group mb-0">
                            <label class="form-label">Status Magang</label>
                            <select wire:model="status" class="form-control">
                                <option value="active">Aktif</option>
                                <option value="completed">Selesai</option>
                                <option value="cancelled">Dibatalkan/Berhenti</option>
                            </select>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Footer Action -->
            <div class="p-6 sm:px-8 flex justify-end gap-3" style="background: rgba(248,250,252,0.8); border-top: 1px solid rgba(148,163,184,0.1);">
                <a href="{{ route('interns.index') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                    <span wire:loading.remove>
                        <i class="fas fa-save mr-1"></i> {{ $isEditing ? 'Simpan Perubahan' : 'Simpan Data' }}
                    </span>
                    <span wire:loading>
                        <i class="fas fa-spinner fa-spin mr-1"></i> Menyimpan...
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>
