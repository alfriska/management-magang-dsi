<div class="slide-up max-w-5xl mx-auto space-y-5">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-800 mb-1">{{ $pageTitle }}</h2>
            <p class="text-slate-400 text-sm">Isi data lengkap pembimbing magang</p>
        </div>
        <a href="{{ route('supervisors.index') }}" class="btn btn-secondary">
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
                        <p class="text-sm text-slate-400">Credential login untuk pembimbing</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="form-group mb-0">
                        <label class="form-label">Nama Lengkap <span class="text-rose-500">*</span></label>
                        <div class="search-input">
                            <input type="text" wire:model="name"
                                class="form-control @error('name') !border-rose-400 @enderror"
                                placeholder="Contoh: Budi Santoso">
                            <i class="fas fa-user"></i>
                        </div>
                        @error('name')
                            <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group mb-0">
                        <label class="form-label">Email Address <span class="text-rose-500">*</span></label>
                        <div class="search-input">
                            <input type="email" wire:model="email"
                                class="form-control @error('email') !border-rose-400 @enderror"
                                placeholder="email@example.com">
                            <i class="fas fa-envelope"></i>
                        </div>
                        @error('email')
                            <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group mb-0">
                        <label class="form-label">Password {{ $isEditing ? '' : '*' }}</label>
                        <div class="password-wrapper">
                            <input type="password" wire:model="password" id="password"
                                class="form-control @error('password') !border-rose-400 @enderror"
                                placeholder="Minimal 8 karakter">
                            <button type="button" class="password-toggle" onclick="togglePassword('password', this)">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        @if ($isEditing)
                            <p class="text-xs text-slate-400 mt-1.5">Kosongkan jika tidak ingin mengubah password.</p>
                        @endif
                        @error('password')
                            <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group mb-0">
                        <label class="form-label">Konfirmasi Password</label>
                        <div class="password-wrapper">
                            <input type="password" wire:model="password_confirmation" id="password_confirmation"
                                class="form-control" placeholder="Ulangi password">
                            <button type="button" class="password-toggle"
                                onclick="togglePassword('password_confirmation', this)">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Divider -->
            <div class="h-1.5"
                style="background: linear-gradient(90deg, rgba(139,92,246,0.1) 0%, rgba(192,132,252,0.1) 100%);"></div>

            <!-- Section: Data Pribadi -->
            <div class="p-6 sm:p-8">
                <div class="flex items-center gap-3 mb-6 pb-4" style="border-bottom: 1px solid rgba(148,163,184,0.1);">
                    <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center">
                        <i class="fas fa-id-card text-lg"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-800 text-base">Data Pribadi</h4>
                        <p class="text-sm text-slate-400">Informasi tambahan pembimbing</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="form-group mb-0">
                        <label class="form-label">NIP</label>
                        <div class="search-input">
                            <input type="text" wire:model="nip"
                                class="form-control @error('nip') !border-rose-400 @enderror"
                                placeholder="Nomor Induk Pegawai">
                            <i class="fas fa-id-badge"></i>
                        </div>
                        @error('nip')
                            <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group mb-0">
                        <label class="form-label">WhatsApp / Telepon</label>
                        <div class="search-input">
                            <input type="text" wire:model="phone"
                                class="form-control @error('phone') !border-rose-400 @enderror"
                                placeholder="08xxxxxxxxxx">
                            <i class="fas fa-phone"></i>
                        </div>
                        @error('phone')
                            <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group mb-0 md:col-span-2">
                        <label class="form-label">Asal Instansi</label>
                        <div class="search-input">
                            <input type="text" wire:model="institution"
                                class="form-control @error('institution') !border-rose-400 @enderror"
                                placeholder="Nama perusahaan / lembaga">
                            <i class="fas fa-building"></i>
                        </div>
                        @error('institution')
                            <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group mb-0 md:col-span-2">
                        <label class="form-label">Alamat</label>
                        <textarea wire:model="address" class="form-control @error('address') !border-rose-400 @enderror" rows="2"
                            placeholder="Alamat tempat tinggal"></textarea>
                        @error('address')
                            <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Footer Action -->
            <div class="p-6 sm:px-8 flex justify-end gap-3"
                style="background: rgba(248,250,252,0.8); border-top: 1px solid rgba(148,163,184,0.1);">
                <a href="{{ route('supervisors.index') }}" class="btn btn-secondary">Batal</a>
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

    {{-- Styles and Scripts moved inside root div --}}
    <style>
        .password-wrapper {
            position: relative;
        }

        .password-wrapper input {
            padding-right: 40px;
        }

        .password-toggle {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #94a3b8;
            cursor: pointer;
            padding: 4px;
            z-index: 10;
        }

        .password-toggle:hover {
            color: #64748b;
        }
    </style>

    <script>
        function togglePassword(inputId, button) {
            const input = document.getElementById(inputId);
            const icon = button.querySelector('i');

            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    </script>
</div>
