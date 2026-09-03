<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Magang Management') - InternHub</title>

    <!-- PWA Meta Tags -->
    <meta name="theme-color" content="#8b5cf6">
    <meta name="description" content="Sistem Manajemen Magang">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="InternHub">
    <meta name="application-name" content="InternHub">
    <meta name="msapplication-TileColor" content="#8b5cf6">
    <meta name="msapplication-tap-highlight" content="no">

    <!-- PWA Manifest -->
    <link rel="manifest" href="/manifest.json">

    <!-- PWA Icons -->
    <link rel="icon" type="image/png" sizes="32x32" href="/icons/icon-96x96.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/icons/icon-72x72.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/icons/icon-192x192.png">
    <link rel="mask-icon" href="/icons/icon.svg" color="#8b5cf6">

    <!-- Google Fonts - Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&display=swap"
        rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Chart.js & SweetAlert -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Early sidebar state restoration (prevent flashing) -->
    <script>
        (function() {
            if (localStorage.getItem('sidebar-collapsed') === 'true') {
                document.documentElement.classList.add('sidebar-collapsed-init');
            }
        })();
    </script>
    <style>
        @media (min-width: 1024px) {
            html.sidebar-collapsed-init .sidebar {
                width: 4rem;
            }

            html.sidebar-collapsed-init .sidebar-brand-text {
                display: none;
            }

            html.sidebar-collapsed-init .nav-section-title {
                opacity: 0;
                height: 0;
                margin-bottom: 0;
                overflow: hidden;
            }

            html.sidebar-collapsed-init .nav-link {
                justify-content: center;
                padding-left: 0;
                padding-right: 0;
            }

            html.sidebar-collapsed-init .nav-link span {
                opacity: 0;
                width: 0;
                overflow: hidden;
            }

            html.sidebar-collapsed-init .main-content {
                margin-left: 4rem;
            }

            html.sidebar-collapsed-init .sidebar-brand {
                flex-direction: column;
                justify-content: center;
                align-items: center;
                padding-left: 0;
                padding-right: 0;
            }

            html.sidebar-collapsed-init .sidebar-brand-icon {
                width: 2.25rem;
                height: 2.25rem;
                font-size: 1.125rem;
            }

            html.sidebar-collapsed-init .sidebar-collapse-btn {
                position: static;
                transform: translateY(0);
                margin-left: 0;
                width: 2rem;
                height: 2rem;
            }

            html.sidebar-collapsed-init .sidebar-collapse-btn i {
                transform: rotate(0deg);
            }
        }
    </style>

    @livewireStyles
    @stack('styles')
</head>

<body>
    <div class="app-container">
        <!-- Sidebar Overlay (Mobile) -->
        <div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <!-- Mobile Close Button -->
            <button class="sidebar-close lg:hidden" onclick="closeSidebar()">
                <i class="fas fa-times"></i>
            </button>

            <div class="sidebar-brand">
                <div class="sidebar-brand-icon">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <div class="sidebar-brand-text">
                    <h1>InternHub</h1>
                    <span>Management</span>
                </div>
                <button class="sidebar-collapse-btn hidden lg:flex" onclick="toggleSidebarCollapse()">
                    <i class="fas fa-chevron-left"></i>
                </button>
            </div>

            <nav class="sidebar-nav">
                <div class="nav-section">
                    <div class="nav-section-title">Menu</div>
                    <ul class="space-y-0.5">
                        <li class="nav-item">
                            <a href="{{ route('dashboard') }}"
                                class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                                <i class="fas fa-home"></i>
                                <span>Dashboard</span>
                            </a>
                        </li>

                        @if (auth()->user()->canManage())
                            <li class="nav-item">
                                <a href="{{ route('interns.index') }}"
                                    class="nav-link {{ request()->routeIs('interns.*') ? 'active' : '' }}">
                                    <i class="fas fa-users"></i>
                                    <span>Anggota Magang</span>
                                </a>
                            </li>
                        @endif

                        @if (auth()->user()->role === 'admin')
                            <li class="nav-item">
                                <a href="{{ route('supervisors.index') }}"
                                    class="nav-link {{ request()->routeIs('supervisors.*') ? 'active' : '' }}">
                                    <i class="fas fa-user-tie"></i>
                                    <span>Pembimbing</span>
                                </a>
                            </li>
                        @endif

                        <li class="nav-item">
                            <a href="{{ route('tasks.index') }}"
                                class="nav-link {{ request()->routeIs('tasks.*') ? 'active' : '' }}">
                                <i class="fas fa-tasks"></i>
                                <span>Daftar Penugasan</span>
                            </a>
                        </li>

                        @if (auth()->user()->canManage())
                            <li class="nav-item">
                                <a href="{{ route('task-assignments.index') }}"
                                    class="nav-link {{ request()->routeIs('task-assignments.*') ? 'active' : '' }}">
                                    <i class="fas fa-layer-group"></i>
                                    <span>Daftar Tugas</span>
                                </a>
                            </li>
                        @endif

                        <li class="nav-item">
                            <a href="{{ route('attendances.index') }}"
                                class="nav-link {{ request()->routeIs('attendances.*') ? 'active' : '' }}">
                                <i class="fas fa-calendar-check"></i>
                                <span>Presensi</span>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('calendar') }}"
                                class="nav-link {{ request()->routeIs('calendar') ? 'active' : '' }}">
                                <i class="fas fa-calendar-alt"></i>
                                <span>Kalender</span>
                            </a>
                        </li>
                    </ul>
                </div>

                @if (auth()->user()->canManage())
                    <div class="nav-section">
                        <div class="nav-section-title">Evaluasi</div>
                        <ul class="space-y-0.5">
                            <li class="nav-item">
                                <a href="{{ route('reports.index') }}"
                                    class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                                    <i class="fas fa-file-alt"></i>
                                    <span>Laporan</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('assessments.index') }}"
                                    class="nav-link {{ request()->routeIs('assessments.*') ? 'active' : '' }}">
                                    <i class="fas fa-star"></i>
                                    <span>Penilaian</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                @endif

                @if (auth()->user()->role === 'admin')
                    <div class="nav-section">
                        <div class="nav-section-title">Sistem</div>
                        <ul class="space-y-0.5">
                            <li class="nav-item">
                                <a href="{{ route('settings.index') }}"
                                    class="nav-link {{ request()->routeIs('settings.*') ? 'active' : '' }}">
                                    <i class="fas fa-cog"></i>
                                    <span>Pengaturan</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                @endif

                <div class="nav-section">
                    <div class="nav-section-title">Akun</div>
                    <ul class="space-y-0.5">
                        <li class="nav-item">
                            <a href="{{ route('profile.show') }}"
                                class="nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}">
                                <i class="fas fa-user-circle"></i>
                                <span>Profil</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Header -->
            <header class="header">
                <div class="flex items-center gap-3">
                    <button class="menu-toggle" onclick="toggleSidebar()">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h2 class="header-title">@yield('title', 'Dashboard')</h2>
                </div>

                <div class="header-actions">
                    <!-- Notification Bell -->
                    @php
                        $unreadNotifications = auth()->user()->notifications()->unread()->latest()->take(5)->get();
                        $unreadCount = auth()->user()->notifications()->unread()->count();
                    @endphp
                    <div class="dropdown">
                        <button class="btn btn-icon btn-secondary relative" data-toggle="dropdown">
                            <i class="fas fa-bell"></i>
                            @if ($unreadCount > 0)
                                <span
                                    class="absolute -top-1 -right-1 min-w-[16px] h-4 rounded-full bg-rose-500 text-white text-[9px] font-bold flex items-center justify-center px-1">
                                    {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                                </span>
                            @endif
                        </button>
                        <div class="dropdown-menu dropdown-menu-right" style="width: 300px;">
                            <div class="px-4 py-3 flex justify-between items-center"
                                style="border-bottom: 1px solid rgba(148,163,184,0.1);">
                                <span class="font-bold text-slate-700 text-sm">Notifikasi</span>
                                @if ($unreadCount > 0)
                                    <a href="{{ route('notifications.markAllRead') }}"
                                        class="text-[10px] text-violet-500 hover:text-violet-600 no-underline font-medium">Tandai
                                        dibaca</a>
                                @endif
                            </div>
                            <div class="max-h-72 overflow-y-auto">
                                @forelse($unreadNotifications as $notification)
                                    <a href="{{ $notification->link ?? route('notifications.index') }}"
                                        class="flex gap-3 px-4 py-3 no-underline transition-colors hover:bg-slate-50/80 {{ !$notification->read_at ? 'bg-violet-50/30' : '' }}"
                                        style="border-bottom: 1px solid rgba(148,163,184,0.06);">
                                        <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0"
                                            style="background: rgba(248,250,252,0.8);">
                                            <i class="{{ $notification->icon_class }}"
                                                style="color: {{ $notification->color }};"></i>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="font-semibold text-slate-700 text-xs mb-0.5">
                                                {{ $notification->title }}</div>
                                            <div class="text-slate-400 text-[11px] truncate">
                                                {{ Str::limit($notification->message, 45) }}</div>
                                            <div class="text-slate-300 text-[10px] mt-1">
                                                {{ $notification->created_at->diffForHumans() }}</div>
                                        </div>
                                    </a>
                                @empty
                                    <div class="py-8 text-center text-slate-400">
                                        <i class="fas fa-bell-slash text-2xl mb-2 opacity-40 block"></i>
                                        <span class="text-xs">Tidak ada notifikasi</span>
                                    </div>
                                @endforelse
                            </div>
                            <a href="{{ route('notifications.index') }}"
                                class="block py-3 text-center text-xs font-semibold text-violet-500 hover:text-violet-600 no-underline hover:bg-slate-50/50"
                                style="border-top: 1px solid rgba(148,163,184,0.1);">
                                Lihat Semua
                            </a>
                        </div>
                    </div>

                    <!-- User Menu -->
                    <div class="user-menu">
                        @if (auth()->user()->avatar)
                            <img src="{{ Str::startsWith(auth()->user()->avatar, ['http', 'https']) ? auth()->user()->avatar : asset('storage/avatars/' . auth()->user()->avatar) }}"
                                alt="Avatar" class="user-avatar" referrerpolicy="no-referrer">
                        @else
                            <div class="user-avatar">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </div>
                        @endif
                        <div class="user-info">
                            <div class="user-name">{{ auth()->user()->name }}</div>
                            <div class="user-role">{{ auth()->user()->role }}</div>
                        </div>
                        <i class="fas fa-chevron-down text-slate-400 text-[10px] ml-1"></i>

                        <div class="dropdown-menu">
                            <a href="{{ route('profile.show') }}" class="dropdown-item">
                                <i class="fas fa-user text-slate-400"></i>
                                <span>Profil Saya</span>
                            </a>
                            <a href="{{ route('profile.edit') }}" class="dropdown-item">
                                <i class="fas fa-cog text-slate-400"></i>
                                <span>Pengaturan</span>
                            </a>
                            <div class="dropdown-divider"></div>
                            <a href="#"
                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                                class="dropdown-item text-rose-500">
                                <i class="fas fa-sign-out-alt"></i>
                                <span>Logout</span>
                            </a>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Content -->
            <div class="content fade-in">
                @if (isset($slot))
                    {{ $slot }}
                @else
                    @yield('content')
                @endif
            </div>
        </main>
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            sidebar.classList.toggle('active');
            overlay.classList.toggle('active');
            document.body.classList.toggle('sidebar-open');
        }

        function closeSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            sidebar.classList.remove('active');
            overlay.classList.remove('active');
            document.body.classList.remove('sidebar-open');
        }

        // Desktop Sidebar Collapse
        function toggleSidebarCollapse() {
            const appContainer = document.querySelector('.app-container');
            const html = document.documentElement;
            const isCollapsed = appContainer.classList.toggle('sidebar-collapsed');

            // Also toggle on html for persistence
            if (isCollapsed) {
                html.classList.add('sidebar-collapsed-init');
            } else {
                html.classList.remove('sidebar-collapsed-init');
            }

            localStorage.setItem('sidebar-collapsed', isCollapsed);

            // Update button icon
            const btn = document.querySelector('.sidebar-collapse-btn i');
            if (btn) {
                btn.className = isCollapsed ? 'fas fa-chevron-right' : 'fas fa-chevron-left';
            }
        }

        // Restore sidebar state on page load
        document.addEventListener('DOMContentLoaded', function() {
            if (localStorage.getItem('sidebar-collapsed') === 'true') {
                document.querySelector('.app-container').classList.add('sidebar-collapsed');
                const btn = document.querySelector('.sidebar-collapse-btn i');
                if (btn) btn.className = 'fas fa-chevron-right';
            }
        });

        // Close sidebar on nav link click (mobile)
        document.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', () => {
                if (window.innerWidth <= 1024) {
                    closeSidebar();
                }
            });
        });

        // SweetAlert2
        const swalConfig = {
            background: 'rgba(255,255,255,0.95)',
            backdrop: 'rgba(0,0,0,0.2)',
            customClass: {
                popup: 'rounded-2xl',
                confirmButton: 'rounded-xl px-6 py-2.5 font-semibold',
            }
        };

        @if (session('success'))
            Swal.fire({
                ...swalConfig,
                icon: 'success',
                title: 'Berhasil!',
                text: "{{ session('success') }}",
                showConfirmButton: false,
                timer: 2000
            });
        @endif

        @if (session('error'))
            Swal.fire({
                ...swalConfig,
                icon: 'error',
                title: 'Gagal',
                text: "{{ session('error') }}",
                confirmButtonColor: '#f43f5e'
            });
        @endif

        @if (session('warning'))
            Swal.fire({
                ...swalConfig,
                icon: 'warning',
                title: 'Peringatan',
                text: "{{ session('warning') }}",
                confirmButtonColor: '#f59e0b'
            });
        @endif

        @if ($errors->any())
            Swal.fire({
                ...swalConfig,
                icon: 'error',
                title: 'Kesalahan Input',
                html: '<ul style="text-align:left;margin:0;padding-left:20px;">@foreach ($errors->all() as $error)<li style="margin-bottom:4px;">{{ $error }}</li>@endforeach</ul>',
                confirmButtonColor: '#f43f5e'
            });
        @endif

        // Dropdown
        document.addEventListener('click', function(e) {
            if (e.target.closest('[data-toggle="dropdown"]')) {
                const dropdown = e.target.closest('.dropdown');
                dropdown.classList.toggle('show');
                document.querySelectorAll('.dropdown.show').forEach(d => {
                    if (d !== dropdown) d.classList.remove('show');
                });
                e.preventDefault();
            } else if (!e.target.closest('.dropdown')) {
                document.querySelectorAll('.dropdown.show').forEach(d => d.classList.remove('show'));
            }
        });
    </script>

    @livewireScripts
    @stack('scripts')
    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden" wire:ignore>
        @csrf
    </form>

    <!-- PWA Service Worker Registration -->
    <script>
        // Register Service Worker
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js')
                    .then((registration) => {
                        console.log('[PWA] Service Worker registered successfully:', registration.scope);

                        // Check for updates
                        registration.addEventListener('updatefound', () => {
                            const newWorker = registration.installing;
                            newWorker.addEventListener('statechange', () => {
                                if (newWorker.state === 'installed' && navigator.serviceWorker
                                    .controller) {
                                    // New content available, show update notification
                                    showUpdateAvailable();
                                }
                            });
                        });
                    })
                    .catch((error) => {
                        console.error('[PWA] Service Worker registration failed:', error);
                    });
            });
        }

        // PWA Install Prompt
        let deferredPrompt;
        const installBanner = document.getElementById('pwa-install-banner');

        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            deferredPrompt = e;

            // Check if user has already dismissed the banner
            if (!localStorage.getItem('pwa-install-dismissed')) {
                showInstallBanner();
            }
        });

        function showInstallBanner() {
            // Create install banner if it doesn't exist
            if (!document.getElementById('pwa-install-banner')) {
                const banner = document.createElement('div');
                banner.id = 'pwa-install-banner';
                banner.innerHTML = `
                    <div class="pwa-install-content">
                        <div class="pwa-install-icon">
                            <i class="fas fa-download"></i>
                        </div>
                        <div class="pwa-install-text">
                            <strong>Install InternHub</strong>
                            <span>Akses lebih cepat dari home screen</span>
                        </div>
                        <div class="pwa-install-actions">
                            <button class="pwa-install-btn" onclick="installPWA()">Install</button>
                            <button class="pwa-dismiss-btn" onclick="dismissInstallBanner()">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                `;
                document.body.appendChild(banner);

                // Add styles dynamically
                const style = document.createElement('style');
                style.textContent = `
                    #pwa-install-banner {
                        position: fixed;
                        bottom: 20px;
                        left: 50%;
                        transform: translateX(-50%);
                        background: linear-gradient(135deg, #1e293b, #0f172a);
                        border: 1px solid rgba(139, 92, 246, 0.3);
                        border-radius: 16px;
                        padding: 12px 16px;
                        z-index: 9999;
                        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3), 0 0 30px rgba(139, 92, 246, 0.2);
                        animation: slideUp 0.4s ease-out;
                        max-width: calc(100% - 40px);
                    }
                    @keyframes slideUp {
                        from { transform: translateX(-50%) translateY(100px); opacity: 0; }
                        to { transform: translateX(-50%) translateY(0); opacity: 1; }
                    }
                    .pwa-install-content {
                        display: flex;
                        align-items: center;
                        gap: 12px;
                    }
                    .pwa-install-icon {
                        width: 44px;
                        height: 44px;
                        background: linear-gradient(135deg, #8b5cf6, #7c3aed);
                        border-radius: 12px;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        color: white;
                        font-size: 18px;
                    }
                    .pwa-install-text {
                        display: flex;
                        flex-direction: column;
                        gap: 2px;
                    }
                    .pwa-install-text strong {
                        color: #f8fafc;
                        font-size: 14px;
                    }
                    .pwa-install-text span {
                        color: #94a3b8;
                        font-size: 12px;
                    }
                    .pwa-install-actions {
                        display: flex;
                        align-items: center;
                        gap: 8px;
                        margin-left: 8px;
                    }
                    .pwa-install-btn {
                        background: linear-gradient(135deg, #8b5cf6, #7c3aed);
                        color: white;
                        border: none;
                        padding: 8px 20px;
                        border-radius: 8px;
                        font-size: 13px;
                        font-weight: 600;
                        cursor: pointer;
                        transition: all 0.2s;
                    }
                    .pwa-install-btn:hover {
                        transform: translateY(-1px);
                        box-shadow: 0 4px 12px rgba(139, 92, 246, 0.4);
                    }
                    .pwa-dismiss-btn {
                        background: transparent;
                        border: none;
                        color: #64748b;
                        padding: 8px;
                        cursor: pointer;
                        border-radius: 6px;
                        transition: all 0.2s;
                    }
                    .pwa-dismiss-btn:hover {
                        background: rgba(255, 255, 255, 0.1);
                        color: #94a3b8;
                    }
                    @media (max-width: 480px) {
                        #pwa-install-banner {
                            bottom: 10px;
                            padding: 10px 12px;
                        }
                        .pwa-install-text span {
                            display: none;
                        }
                    }
                `;
                document.head.appendChild(style);
            }
        }

        function installPWA() {
            if (deferredPrompt) {
                deferredPrompt.prompt();
                deferredPrompt.userChoice.then((choiceResult) => {
                    if (choiceResult.outcome === 'accepted') {
                        console.log('[PWA] User accepted the install prompt');
                    }
                    deferredPrompt = null;
                    dismissInstallBanner();
                });
            }
        }

        function dismissInstallBanner() {
            const banner = document.getElementById('pwa-install-banner');
            if (banner) {
                banner.style.animation = 'slideDown 0.3s ease-in forwards';
                setTimeout(() => banner.remove(), 300);
            }
            localStorage.setItem('pwa-install-dismissed', 'true');
        }

        // Show update available notification
        function showUpdateAvailable() {
            Swal.fire({
                icon: 'info',
                title: 'Update Tersedia',
                text: 'Versi baru InternHub tersedia. Muat ulang untuk memperbarui?',
                showCancelButton: true,
                confirmButtonText: 'Muat Ulang',
                cancelButtonText: 'Nanti',
                confirmButtonColor: '#8b5cf6'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.reload();
                }
            });
        }

        // Online/Offline detection
        window.addEventListener('online', () => {
            console.log('[PWA] Back online');
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'success',
                    title: 'Kembali Online',
                    text: 'Koneksi internet telah pulih',
                    timer: 2000,
                    showConfirmButton: false,
                    toast: true,
                    position: 'top-end'
                });
            }
        });

        window.addEventListener('offline', () => {
            console.log('[PWA] Gone offline');
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Mode Offline',
                    text: 'Anda sedang offline. Beberapa fitur mungkin tidak tersedia.',
                    timer: 3000,
                    showConfirmButton: false,
                    toast: true,
                    position: 'top-end'
                });
            }
        });
    </script>
</body>

</html>
