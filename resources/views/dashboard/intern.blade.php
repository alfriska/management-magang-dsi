@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    @push('styles')
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
            integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    @endpush

    <div class="slide-up max-w-[1600px] mx-auto space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="text-2xl sm:text-3xl font-bold text-slate-800 tracking-tight mb-1">
                    Selamat datang kembali! {{ auth()->user()->name }}
                </h2>
                <p class="text-slate-500 font-medium text-sm sm:text-base">Berikut ringkasan
                    aktivitas magang Anda.</p>
            </div>
            <div class="text-right hidden sm:block">
                <div class="text-sm font-semibold text-slate-600">{{ now()->isoFormat('dddd, D MMMM Y') }}</div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- LEFT COLUMN (2/3) -->
            <div class="lg:col-span-2 space-y-6">

                <!-- ATTENDANCE CARD -->
                <div class="card p-0 overflow-hidden">
                    <div class="p-4 sm:p-6 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                        <h3 class="font-bold text-lg text-slate-800 flex items-center gap-2">
                            <i class="fas fa-map-marked-alt text-indigo-500"></i> Presensi Harian
                        </h3>
                        <div id="gps-status"
                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-slate-100 text-slate-500">
                            <div class="w-1.5 h-1.5 rounded-full bg-slate-400 mr-1.5 animate-pulse"></div> Mencari Lokasi...
                        </div>
                    </div>

                    <div class="p-4 sm:p-6 space-y-4">
                        <div
                            class="relative h-[220px] sm:h-[300px] w-full rounded-xl overflow-hidden border border-slate-200 shadow-inner">
                            <div id="map" class="h-full w-full z-0"></div>
                        </div>

                        <div
                            class="flex flex-col sm:flex-row justify-between items-center gap-4 bg-slate-50 p-4 rounded-xl border border-slate-100">
                            <span class="text-sm font-medium text-slate-600 flex items-center gap-2">
                                <i class="fas fa-building text-slate-400"></i> Kantor: <span
                                    class="font-bold text-slate-800">PT. DUTA SOLUSI INFORMATIKA</span>
                            </span>
                            <span class="text-sm font-medium text-slate-600" id="distance-display">
                                Jarak: <span class="font-mono font-bold text-slate-800">-- m</span>
                            </span>
                        </div>

                        <!-- Action Buttons -->
                        <div>
                            @if (!$todayAttendance)
                                <form action="{{ route('attendance.checkIn') }}" method="POST" id="checkInForm">
                                    @csrf
                                    <input type="hidden" name="latitude" id="lat">
                                    <input type="hidden" name="longitude" id="lon">
                                    <input type="hidden" name="late_reason" id="lateReasonInput">

                                    <button type="submit" id="checkInBtn"
                                        class="btn w-full py-3.5 text-base font-bold shadow-lg shadow-indigo-500/20 disabled:opacity-50 disabled:cursor-not-allowed disabled:bg-slate-300 disabled:text-slate-500 bg-indigo-600 text-white hover:bg-indigo-700 transition-all"
                                        disabled>
                                        <span class="spinner-border spinner-border-sm mr-2" role="status"
                                            aria-hidden="true" style="display:none;" id="loadingSpinner"></span>
                                        <i class="fas fa-location-arrow mr-2" id="btnIcon"></i> Menunggu GPS...
                                    </button>
                                </form>
                            @elseif(!$todayAttendance->check_out && !in_array($todayAttendance->status, ['permission', 'sick']))
                                <div class="text-center mb-4">
                                    <div
                                        class="inline-flex items-center px-4 py-2 rounded-lg bg-emerald-50 text-emerald-700 font-medium text-sm border border-emerald-100">
                                        <i class="fas fa-check-circle mr-2"></i> Anda masuk pukul <strong
                                            class="ml-1">{{ $todayAttendance->check_in }}</strong>
                                    </div>
                                </div>
                                <form action="{{ route('attendance.checkOut') }}" method="POST">
                                    @csrf
                                    <button type="submit"
                                        class="btn w-full py-3.5 text-base font-bold bg-amber-500 text-white hover:bg-amber-600 shadow-lg shadow-amber-500/20 transition-all">
                                        <i class="fas fa-sign-out-alt mr-2"></i> PRESENSI KELUAR SEKARANG
                                    </button>
                                </form>
                            @else
                                <!-- Attendance Completed -->
                                <div class="text-center p-6 bg-emerald-50/50 border border-emerald-100 rounded-xl">
                                    <div
                                        class="w-16 h-16 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center text-3xl mx-auto mb-3 shadow-sm">
                                        <i class="fas fa-check"></i>
                                    </div>
                                    <h4 class="font-bold text-emerald-800 text-lg">Selesai Hari Ini</h4>
                                    <p class="text-emerald-600 font-medium text-sm mt-1">
                                        Status: <span class="capitalize">{{ $todayAttendance->status_label }}</span>
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- TASKS CARD -->
                <div class="card p-0 overflow-hidden">
                    <div class="p-4 sm:p-6 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                        <h3 class="font-bold text-lg text-slate-800 flex items-center gap-2">
                            <i class="fas fa-clipboard-list text-teal-500"></i> Tugas Saya
                        </h3>
                        <a href="{{ route('tasks.index') }}"
                            class="text-xs font-bold text-teal-600 hover:text-teal-700 uppercase tracking-wider hover:underline">
                            Lihat Semua
                        </a>
                    </div>

                    <div class="p-4 sm:p-6 space-y-6">
                        <!-- Task Progress Stats -->
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4">
                            <div
                                class="bg-slate-50 p-3 sm:p-4 rounded-xl border border-slate-100 text-center hover:bg-white hover:shadow-md transition-all">
                                <div class="text-2xl font-black text-slate-700">{{ $totalTasks }}</div>
                                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mt-1">Total</div>
                            </div>
                            <div
                                class="bg-amber-50 p-3 sm:p-4 rounded-xl border border-amber-100 text-center hover:bg-white hover:shadow-md transition-all">
                                <div class="text-2xl font-black text-amber-600">{{ $pendingTasks }}</div>
                                <div class="text-[10px] font-bold text-amber-600/70 uppercase tracking-wider mt-1">Pending
                                </div>
                            </div>
                            <div
                                class="bg-indigo-50 p-3 sm:p-4 rounded-xl border border-indigo-100 text-center hover:bg-white hover:shadow-md transition-all">
                                <div class="text-2xl font-black text-indigo-600">{{ $taskStats['in_progress'] ?? 0 }}</div>
                                <div class="text-[10px] font-bold text-indigo-600/70 uppercase tracking-wider mt-1">Proses
                                </div>
                            </div>
                            <div
                                class="bg-emerald-50 p-3 sm:p-4 rounded-xl border border-emerald-100 text-center hover:bg-white hover:shadow-md transition-all">
                                <div class="text-2xl font-black text-emerald-600">{{ $completedTasks }}</div>
                                <div class="text-[10px] font-bold text-emerald-600/70 uppercase tracking-wider mt-1">Selesai
                                </div>
                            </div>
                        </div>

                        <!-- Progress Bar -->
                        @if ($totalTasks > 0)
                            <div>
                                <div class="flex justify-between items-end mb-2">
                                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Overall
                                        Progress</span>
                                    <span
                                        class="text-sm font-bold text-emerald-600">{{ $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0 }}%</span>
                                </div>
                                <div class="h-2.5 w-full bg-slate-100 rounded-full overflow-hidden">
                                    <div class="h-full bg-gradient-to-r from-emerald-400 to-emerald-600 rounded-full transition-all duration-500 ease-out"
                                        style="width: {{ $totalTasks > 0 ? ($completedTasks / $totalTasks) * 100 : 0 }}%">
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if ($tasks->isEmpty())
                            <div class="text-center py-12 text-slate-400">
                                <i class="fas fa-check-double text-4xl mb-3 opacity-20"></i>
                                <p class="font-medium">Semua tugas sudah selesai! 🎉</p>
                            </div>
                        @else
                            <div class="space-y-3">
                                <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Tugas Aktif</h4>
                                @foreach ($tasks as $task)
                                    @php
                                        $deadlineDays = $task->deadline
                                            ? now()->diffInDays($task->deadline, false)
                                            : null;
                                        $isUrgent = $deadlineDays !== null && $deadlineDays <= 2 && $deadlineDays >= 0;
                                        $isOverdue = $deadlineDays !== null && $deadlineDays < 0;
                                    @endphp
                                    <div
                                        class="group flex items-center justify-between p-4 rounded-xl border {{ $isOverdue ? 'bg-rose-50 border-rose-200' : ($isUrgent ? 'bg-amber-50 border-amber-200' : 'bg-white border-slate-200 hover:border-indigo-200') }} transition-all hover:shadow-md">
                                        <div class="flex-1 min-w-0 mr-4">
                                            <div class="flex items-center gap-2 mb-1.5 flex-wrap">
                                                <span
                                                    class="badge {{ $task->priority == 'high' ? 'bg-rose-100 text-rose-700' : ($task->priority == 'medium' ? 'bg-amber-100 text-amber-700' : 'bg-emerald-100 text-emerald-700') }} text-[10px] px-2 py-0.5 rounded uppercase font-bold tracking-wide">
                                                    {{ ucfirst($task->priority) }}
                                                </span>
                                                <span
                                                    class="badge {{ $task->status_color == 'primary' ? 'bg-indigo-100 text-indigo-700' : ($task->status_color == 'warning' ? 'bg-amber-100 text-amber-700' : 'bg-slate-100 text-slate-600') }} text-[10px] px-2 py-0.5 rounded uppercase font-bold tracking-wide">
                                                    {{ $task->status_label }}
                                                </span>
                                                @if ($isOverdue)
                                                    <span
                                                        class="inline-flex items-center gap-1 bg-rose-100 text-rose-600 text-[10px] px-2 py-0.5 rounded font-bold uppercase animate-pulse">
                                                        <i class="fas fa-exclamation-circle"></i> Terlambat
                                                    </span>
                                                @elseif($isUrgent)
                                                    <span
                                                        class="inline-flex items-center gap-1 bg-amber-100 text-amber-700 text-[10px] px-2 py-0.5 rounded font-bold uppercase">
                                                        <i class="fas fa-clock"></i>
                                                        {{ $deadlineDays == 0 ? 'Hari Ini' : $deadlineDays . ' Hari Lagi' }}
                                                    </span>
                                                @endif
                                            </div>
                                            <h4
                                                class="font-bold text-slate-800 text-sm line-clamp-2 md:truncate group-hover:text-indigo-600 transition-colors">
                                                {{ $task->title }}
                                            </h4>
                                            <div class="text-xs text-slate-500 font-medium mt-1 flex items-center gap-1.5">
                                                <i class="far fa-calendar-alt text-slate-400"></i>
                                                {{ $task->deadline ? $task->deadline->format('d M Y') : 'Tidak ada deadline' }}
                                                @if ($task->deadline_time)
                                                    <span class="text-rose-500"> {{ $task->deadline_time }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        <a href="{{ route('tasks.show', $task) }}"
                                            class="flex-shrink-0 w-8 h-8 rounded-full border border-slate-200 flex items-center justify-center text-slate-400 hover:bg-indigo-50 hover:text-indigo-600 hover:border-indigo-100 transition-all">
                                            <i class="fas fa-arrow-right text-xs"></i>
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

            </div>

            <!-- RIGHT COLUMN (1/3) -->
            <div class="space-y-6">

                <!-- QUICK MENU IZIN -->
                <div class="card p-5">
                    <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4">Menu Izin</h4>

                    @if (!$todayAttendance)
                        <button onclick="openModal('permissionModal')"
                            class="w-full flex items-center justify-between p-4 rounded-xl border border-slate-200 bg-white hover:border-indigo-200 hover:bg-slate-50 hover:shadow-sm transition-all group">
                            <span class="flex items-center gap-3 font-semibold text-slate-700 group-hover:text-indigo-700">
                                <div
                                    class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center">
                                    <i class="fas fa-file-medical"></i>
                                </div>
                                Form Izin / Sakit
                            </span>
                            <i
                                class="fas fa-chevron-right text-xs text-slate-400 group-hover:translate-x-1 transition-transform"></i>
                        </button>
                    @else
                        <div
                            class="text-center p-4 bg-slate-50 rounded-xl border border-slate-100 text-sm text-slate-500 font-medium">
                            <i class="fas fa-info-circle mr-1 text-slate-400"></i> Presensi hari ini sudah tercatat.
                        </div>
                    @endif
                </div>

                <!-- STATS SUMMARY -->
                <div class="card p-0 overflow-hidden">
                    <div class="p-5 border-b border-slate-100">
                        <h3 class="font-bold text-slate-800 text-sm uppercase tracking-wider">Statistik Ringkas</h3>
                    </div>

                    <div class="p-5">
                        <div class="grid grid-cols-2 gap-3 mb-6">
                            <div class="bg-sky-50 p-3 rounded-xl border border-sky-100 text-center">
                                <div class="text-xl font-black text-sky-600">{{ $attendancePercentage }}%</div>
                                <div class="text-[10px] font-bold text-sky-600/70 uppercase">Kehadiran</div>
                            </div>
                            <div class="bg-emerald-50 p-3 rounded-xl border border-emerald-100 text-center">
                                <div class="text-xl font-black text-emerald-600">{{ $completedTasks }}</div>
                                <div class="text-[10px] font-bold text-emerald-600/70 uppercase">Task Done</div>
                            </div>
                        </div>

                        <h5 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Riwayat Terakhir</h5>
                        <div class="space-y-3">
                            @foreach ($attendances->take(3) as $log)
                                <div
                                    class="flex items-center justify-between text-sm py-2 border-b border-dashed border-slate-100 last:border-0 last:pb-0">
                                    <span class="font-medium text-slate-600">{{ $log->date->format('d/m') }}</span>
                                    <span
                                        class="badge {{ $log->status == 'present' ? 'bg-emerald-100 text-emerald-700' : ($log->status == 'late' ? 'bg-amber-100 text-amber-700' : 'bg-slate-100 text-slate-600') }} text-[10px] px-2 py-0.5 rounded font-bold uppercase">
                                        {{ $log->status_label }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- charts -->
                <div class="card p-5">
                    <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4 flex items-center gap-2">
                        <i class="fas fa-chart-pie text-violet-500"></i> Status Tugas
                    </h3>
                    <div class="h-48">
                        <canvas id="taskProgressChart"></canvas>
                    </div>
                </div>

                <div class="card p-5">
                    <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4 flex items-center gap-2">
                        <i class="fas fa-chart-bar text-emerald-500"></i> Kehadiran 7 Hari
                    </h3>
                    <div class="h-40">
                        <canvas id="weeklyAttendanceChart"></canvas>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- MODAL PERMISSION -->
    <div id="permissionModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title"
        role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity modal-backdrop-custom"
                aria-hidden="true" onclick="closeModal('permissionModal')"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div
                class="relative inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full z-50">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="text-center mb-6">
                        <h3 class="text-lg leading-6 font-bold text-slate-900" id="modal-title">Form Izin</h3>
                        <p class="text-sm text-slate-500 mt-1">Silakan isi detail ketidakhadiran Anda.</p>
                    </div>

                    <form action="{{ route('attendance.permission') }}" method="POST" enctype="multipart/form-data"
                        id="permissionForm">
                        @csrf
                        <input type="hidden" name="latitude" id="permLat">
                        <input type="hidden" name="longitude" id="permLon">

                        <div class="mb-5">
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-2">Jenis Izin</label>
                            <div class="grid grid-cols-2 gap-4">
                                <label class="cursor-pointer">
                                    <input type="radio" name="status" value="permission" class="peer sr-only"
                                        checked>
                                    <div
                                        class="px-4 py-3 rounded-xl border-2 border-slate-200 text-slate-600 text-center font-semibold peer-checked:border-indigo-500 peer-checked:text-indigo-600 peer-checked:bg-indigo-50 transition-all">
                                        Izin
                                    </div>
                                </label>
                                <label class="cursor-pointer">
                                    <input type="radio" name="status" value="sick" class="peer sr-only">
                                    <div
                                        class="px-4 py-3 rounded-xl border-2 border-slate-200 text-slate-600 text-center font-semibold peer-checked:border-rose-500 peer-checked:text-rose-600 peer-checked:bg-rose-50 transition-all">
                                        Sakit
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div class="mb-5">
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-2">Bukti Lampiran</label>
                            <input type="file" name="proof_file" accept=".jpg,.jpeg,.png,.pdf"
                                class="block w-full text-sm text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 border border-slate-200 rounded-lg">
                            <p class="text-xs text-slate-400 mt-1">Max: 2MB (PDF/JPG/PNG)</p>
                        </div>

                        <div class="mb-6">
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-2">Keterangan</label>
                            <textarea name="notes" rows="4"
                                class="w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 text-sm" required
                                placeholder="Jelaskan alasan Anda..."></textarea>
                        </div>

                        <div class="flex gap-3">
                            <button type="button" onclick="closeModal('permissionModal')"
                                class="flex-1 btn btn-secondary">Batal</button>
                            <button type="submit" class="flex-1 btn btn-primary">Kirim</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL LATE REASON -->
    <div id="lateReasonModal"
        class="fixed inset-0 z-50 {{ session('show_late_reason_form') ? 'flex' : 'hidden' }} items-center justify-center p-4">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity modal-backdrop-custom"
            onclick="closeModal('lateReasonModal')"></div>

        <!-- Modal Card - Centered -->
        <div class="relative bg-white rounded-2xl shadow-xl transform transition-all w-full max-w-md z-10 mx-auto">
            <div class="px-6 py-6">
                <div class="text-center mb-6">
                    <div class="mx-auto flex items-center justify-center h-14 w-14 rounded-full bg-amber-100 mb-4">
                        <i class="fas fa-exclamation-triangle text-amber-600 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900">Terlambat Presensi Masuk</h3>
                    <p class="text-sm text-slate-500 mt-2">Waktu masuk telah berlalu. Mohon sertakan alasan
                        keterlambatan Anda.</p>
                </div>
                <div class="mb-6">
                    <textarea id="lateReasonText" rows="3"
                        class="w-full rounded-xl border-slate-200 focus:border-amber-500 focus:ring-amber-500 text-sm"
                        placeholder="Tuliskan alasan keterlambatan..."></textarea>
                </div>
                <div class="flex gap-3">
                    <button type="button" onclick="closeModal('lateReasonModal')"
                        class="flex-1 btn btn-secondary">Batal</button>
                    <button type="button" id="submitLateBtn"
                        class="flex-1 btn btn-primary bg-amber-500 hover:bg-amber-600 text-white border-transparent">Simpan
                        & Presensi Masuk</button>
                </div>
            </div>
        </div>
    </div>


    @push('scripts')
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
            integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
        <script>
            // --- MAP & GPS ---
            const officeLat = {{ $officeLat ?? -7.052683 }};
            const officeLon = {{ $officeLon ?? 110.469375 }};
            const maxDist = {{ $maxDist ?? 100 }};

            function initMap() {
                const mapContainer = document.getElementById('map');
                if (!mapContainer) return;

                const map = L.map('map').setView([officeLat, officeLon], 16);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '© OpenStreetMap'
                }).addTo(map);

                // Custom Icon for Office
                const officeIcon = L.divIcon({
                    className: 'custom-div-icon',
                    html: "<div style='background-color:#4f46e5; width: 12px; height: 12px; border-radius: 50%; border: 2px solid white; box-shadow: 0 0 10px rgba(79, 70, 229, 0.5);'></div>",
                    iconSize: [12, 12],
                    iconAnchor: [6, 6]
                });

                L.marker([officeLat, officeLon], {
                    icon: officeIcon
                }).addTo(map).bindPopup("<b>Kantor</b>").openPopup();

                L.circle([officeLat, officeLon], {
                    color: '#4f46e5',
                    fillColor: '#4f46e5',
                    fillOpacity: 0.1,
                    weight: 1,
                    radius: maxDist
                }).addTo(map);

                return map;
            }

            // Delay init to ensure container layout is ready
            setTimeout(() => {
                const map = initMap();

                // GPS Logic
                const checkInBtn = document.getElementById('checkInBtn');
                const latInput = document.getElementById('lat');
                const lonInput = document.getElementById('lon');
                const distDisplay = document.getElementById('distance-display');
                const gpsStatus = document.getElementById('gps-status');
                const btnIcon = document.getElementById('btnIcon');
                let userMarker;

                if (navigator.geolocation && map) {
                    navigator.geolocation.watchPosition((position) => {
                        const lat = position.coords.latitude;
                        const lng = position.coords.longitude;

                        if (latInput) latInput.value = lat;
                        if (lonInput) lonInput.value = lng;

                        if (userMarker) userMarker.setLatLng([lat, lng]);
                        else {
                            const userIcon = L.divIcon({
                                className: 'custom-div-icon',
                                html: "<div style='background-color:#10b981; width: 12px; height: 12px; border-radius: 50%; border: 2px solid white; box-shadow: 0 0 10px rgba(16, 185, 129, 0.5);'></div>",
                                iconSize: [12, 12],
                                iconAnchor: [6, 6]
                            });
                            userMarker = L.marker([lat, lng], {
                                icon: userIcon
                            }).addTo(map);
                        }

                        const dist = map.distance([officeLat, officeLon], [lat, lng]);

                        if (distDisplay) distDisplay.innerHTML =
                            "Jarak: <span class='font-mono font-bold text-slate-800'>" + Math.round(dist) +
                            " m</span>";

                        if (checkInBtn) {
                            if (dist <= maxDist) {
                                checkInBtn.disabled = false;
                                checkInBtn.classList.remove('bg-slate-300', 'text-slate-500');
                                checkInBtn.classList.add('bg-emerald-500', 'text-white', 'hover:bg-emerald-600',
                                    'shadow-emerald-500/20');
                                // Remove indigo classes if present from initial state
                                checkInBtn.classList.remove('bg-indigo-600', 'hover:bg-indigo-700',
                                    'shadow-indigo-500/20');

                                checkInBtn.innerHTML =
                                    '<i class="fas fa-fingerprint mr-2 animate-pulse"></i> PRESENSI MASUK SEKARANG';

                                if (gpsStatus) {
                                    gpsStatus.innerHTML =
                                        '<div class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5"></div> Lokasi Valid';
                                    gpsStatus.className =
                                        "inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-600 border border-emerald-100";
                                }
                            } else {
                                checkInBtn.disabled = true;
                                checkInBtn.classList.add('bg-slate-300', 'text-slate-500');
                                checkInBtn.classList.remove('bg-emerald-500', 'text-white',
                                    'hover:bg-emerald-600', 'shadow-emerald-500/20', 'bg-indigo-600');
                                checkInBtn.innerHTML = '<i class="fas fa-ban mr-2"></i> Terlalu Jauh';

                                if (gpsStatus) {
                                    gpsStatus.innerHTML =
                                        '<div class="w-1.5 h-1.5 rounded-full bg-rose-500 mr-1.5"></div> Diluar Jangkauan';
                                    gpsStatus.className =
                                        "inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-50 text-rose-600 border border-rose-100";
                                }
                            }
                        }
                    }, (error) => {
                        console.error("GPS Error", error);
                        if (gpsStatus) {
                            gpsStatus.innerHTML = '<i class="fas fa-exclamation-circle mr-1"></i> GPS Error';
                            gpsStatus.className =
                                "inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-50 text-rose-600 border border-rose-100";
                        }
                    }, {
                        enableHighAccuracy: true,
                        maximumAge: 10000
                    });
                }
            }, 500);

            // --- MODALS ---
            function openModal(id) {
                document.getElementById(id).classList.remove('hidden');
                // document.getElementById(id).classList.add('flex'); // Check: Removed valid flex toggle to rely on block layout for this specific modal pattern

                // If opening permission modal, try to get GPS
                if (id === 'permissionModal') {
                    if (navigator.geolocation) {
                        navigator.geolocation.getCurrentPosition((position) => {
                            const lat = position.coords.latitude;
                            const lon = position.coords.longitude;
                            document.getElementById('permLat').value = lat;
                            document.getElementById('permLon').value = lon;
                        }, (err) => {
                            console.log("GPS Permission Error: " + err.message);
                        });
                    }
                }
            }

            function closeModal(id) {
                document.getElementById(id).classList.add('hidden');
                // document.getElementById(id).classList.remove('flex');
            }

            // Late Reason Submit
            const lateSubmitBtn = document.getElementById('submitLateBtn');
            if (lateSubmitBtn) {
                lateSubmitBtn.addEventListener('click', function() {
                    const reason = document.getElementById('lateReasonText').value;
                    if (!reason.trim()) {
                        alert("Mohon isi alasan keterlambatan!");
                        return;
                    }
                    document.getElementById('lateReasonInput').value = reason;
                    document.getElementById('checkInForm').submit();
                });
            }

            // Chart Defaults
            Chart.defaults.font.family = "'Inter', sans-serif";
            Chart.defaults.color = '#64748b';

            // Task Progress Donut Chart
            const taskBreakdown = @json($taskBreakdown);
            const taskProgressCtx = document.getElementById('taskProgressChart');
            if (taskProgressCtx) {
                new Chart(taskProgressCtx.getContext('2d'), {
                    type: 'doughnut',
                    data: {
                        labels: ['Pending', 'Proses', 'Submitted', 'Completed', 'Revisi'],
                        datasets: [{
                            data: [
                                taskBreakdown.pending,
                                taskBreakdown.in_progress,
                                taskBreakdown.submitted,
                                taskBreakdown.completed,
                                taskBreakdown.revision
                            ],
                            backgroundColor: ['#fbbf24', '#6366f1', '#a855f7', '#10b981', '#f43f5e'],
                            borderWidth: 0,
                            hoverOffset: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'right',
                                labels: {
                                    usePointStyle: true,
                                    pointStyle: 'circle',
                                    boxWidth: 6,
                                    font: {
                                        size: 10
                                    }
                                }
                            }
                        },
                        cutout: '70%'
                    }
                });
            }

            // Weekly Attendance Bar Chart
            const weeklyAttendance = @json($weeklyAttendance);
            const weeklyAttendanceCtx = document.getElementById('weeklyAttendanceChart');
            if (weeklyAttendanceCtx) {
                new Chart(weeklyAttendanceCtx.getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: weeklyAttendance.days,
                        datasets: [{
                            label: 'Status',
                            data: weeklyAttendance.counts,
                            backgroundColor: weeklyAttendance.colors,
                            borderRadius: 4,
                            barThickness: 16
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                display: false
                            },
                            x: {
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    font: {
                                        size: 10
                                    }
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                display: false
                            }
                        }
                    }
                });
            }
        </script>
    @endpush
@endsection
