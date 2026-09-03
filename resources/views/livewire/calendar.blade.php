<div>
    <div class="card p-0 overflow-hidden bg-white border-slate-200 shadow-sm rounded-xl">
        <!-- Calendar Header -->
        <div class="p-4 sm:p-6 border-b border-slate-100 flex flex-col md:flex-row justify-between items-center gap-4 bg-slate-50/50">
            <div class="flex items-center justify-between w-full md:w-auto gap-4">
                <button wire:click="previousMonth" class="p-2 rounded-lg text-slate-500 hover:bg-slate-100 hover:text-slate-700 transition-colors">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <h3 class="text-lg sm:text-xl font-bold text-slate-800 text-center min-w-[150px]">
                    {{ $this->monthName }}
                </h3>
                <button wire:click="nextMonth" class="p-2 rounded-lg text-slate-500 hover:bg-slate-100 hover:text-slate-700 transition-colors">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>

            <!-- View Mode Toggle -->
            <div class="flex w-full md:w-auto bg-slate-100 p-1 rounded-xl">
                <button wire:click="switchMode('attendance')" class="flex-1 md:flex-none px-4 py-2 rounded-lg text-sm font-semibold transition-all flex items-center justify-center gap-2 {{ $viewMode === 'attendance' ? 'bg-white text-indigo-600 shadow-sm' : 'text-slate-500 hover:text-slate-700' }}">
                    <i class="fas fa-calendar-check"></i> <span class="hidden sm:inline">Kehadiran</span>
                </button>
                <button wire:click="switchMode('tasks')" class="flex-1 md:flex-none px-4 py-2 rounded-lg text-sm font-semibold transition-all flex items-center justify-center gap-2 {{ $viewMode === 'tasks' ? 'bg-white text-indigo-600 shadow-sm' : 'text-slate-500 hover:text-slate-700' }}">
                    <i class="fas fa-tasks"></i> <span class="hidden sm:inline">Tugas</span>
                </button>
            </div>
        </div>

        <!-- Calendar Grid -->
        <div class="p-2 sm:p-6">
            <!-- Day Headers -->
            <div class="grid grid-cols-7 gap-1 sm:gap-2 mb-2">
                @foreach(['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'] as $index => $dayName)
                <div class="text-center py-2 text-xs sm:text-sm font-bold text-slate-400 uppercase tracking-wider">
                    <span class="hidden sm:inline">{{ $dayName }}</span>
                    <span class="sm:hidden">{{ substr($dayName, 0, 3) }}</span>
                </div>
                @endforeach
            </div>

            <!-- Calendar Days -->
            <div class="grid grid-cols-7 gap-1 sm:gap-2">
                @foreach($days as $day)
                @if($day === null)
                <div class="min-h-[80px] sm:min-h-[120px] bg-slate-50/30 rounded-lg"></div>
                @else
                @php
                $isToday = $day == now()->day && $currentMonth == now()->month && $currentYear == now()->year;
                $hasEvents = isset($events[$day]) && count($events[$day]) > 0;
                $dayEvents = $events[$day] ?? [];
                $isClickable = $viewMode === 'attendance' && auth()->user()->canManage();
                
                // Check for holidays and Sundays
                $isHoliday = isset($holidays[$day]);
                $holidayName = $holidays[$day] ?? null;
                $isSunday = \Carbon\Carbon::createFromDate($currentYear, $currentMonth, $day)->isSunday();
                $isRedDate = $isHoliday || $isSunday;
                @endphp
                <div @if($isClickable) wire:click="openAttendanceModal({{ $day }})" @endif 
                     @if($holidayName) title="{{ $holidayName }}" @endif
                     class="relative min-h-[80px] sm:min-h-[120px] p-1.5 sm:p-2 border rounded-xl transition-all flex flex-col gap-1
                            {{ $isToday ? 'bg-indigo-50/50 border-indigo-200 ring-1 ring-indigo-200' : ($isRedDate ? 'bg-rose-50/50 border-rose-100' : 'bg-white border-slate-100') }}
                            {{ $isClickable ? 'cursor-pointer hover:border-indigo-300 hover:shadow-md hover:-translate-y-0.5' : '' }}">

                    <!-- Day Number -->
                    <div class="flex justify-between items-start">
                        <span class="text-sm sm:text-base font-semibold {{ $isToday ? 'text-indigo-600 bg-indigo-100 w-6 h-6 sm:w-7 sm:h-7 rounded-lg flex items-center justify-center' : ($isRedDate ? 'text-rose-500 ml-1' : 'text-slate-700 ml-1') }}">
                            {{ $day }}
                        </span>
                        @if($isHoliday)
                        <span class="hidden sm:inline-block text-[9px] bg-rose-100 text-rose-600 px-1.5 py-0.5 rounded font-medium leading-tight text-right" style="max-width: 85px; word-wrap: break-word;">
                            {{ $holidayName }}
                        </span>
                        @endif
                        @if($hasEvents && $viewMode === 'attendance' && !auth()->user()->canManage())
                        @foreach($dayEvents as $event)
                        @if(isset($event['status']))
                        @php
                        $dotColors = [
                        'present' => 'bg-emerald-500',
                        'late' => 'bg-amber-500',
                        'absent' => 'bg-rose-500',
                        'permission' => 'bg-sky-500',
                        'sick' => 'bg-purple-500',
                        ];
                        $dotColor = $dotColors[$event['status']] ?? 'bg-slate-400';
                        @endphp
                        <div class="sm:hidden w-2 h-2 rounded-full {{ $dotColor }}"></div>
                        @endif
                        @endforeach
                        @endif
                    </div>

                    <!-- Events Container -->
                    <div class="flex-1 flex flex-col gap-1 overflow-hidden">
                        @if($viewMode === 'attendance')
                        @if($hasEvents)
                        {{-- Attendance Mode --}}
                        @php
                        $isPersonal = isset($dayEvents[0]['type']) && $dayEvents[0]['type'] === 'attendance';
                        @endphp

                        @if($isPersonal)
                        {{-- Intern's Personal View --}}
                        @foreach($dayEvents as $event)
                        @php
                        $statusConfig = [
                        'present' => ['bg' => 'bg-emerald-500', 'text' => 'text-white', 'icon' => 'fa-check-circle', 'label' => 'Hadir'],
                        'late' => ['bg' => 'bg-amber-500', 'text' => 'text-white', 'icon' => 'fa-clock', 'label' => 'Terlambat'],
                        'absent' => ['bg' => 'bg-rose-500', 'text' => 'text-white', 'icon' => 'fa-times-circle', 'label' => 'Absen'],
                        'permission' => ['bg' => 'bg-sky-500', 'text' => 'text-white', 'icon' => 'fa-file-alt', 'label' => 'Izin'],
                        'sick' => ['bg' => 'bg-purple-500', 'text' => 'text-white', 'icon' => 'fa-notes-medical', 'label' => 'Sakit'],
                        ];
                        $config = $statusConfig[$event['status']] ?? $statusConfig['absent'];
                        @endphp
                        <!-- Desktop Badge -->
                        <div class="hidden sm:flex items-center gap-1.5 px-2 py-1 rounded-md text-[10px] sm:text-xs font-semibold {{ $config['bg'] }} {{ $config['text'] }}">
                            <i class="fas {{ $config['icon'] }} text-[9px]"></i>
                            <span class="truncate">{{ $event['check_in'] ? substr($event['check_in'], 0, 5) : $config['label'] }}</span>
                        </div>
                        @endforeach
                        @else
                        {{-- Admin Summary View --}}
                        <div class="flex flex-wrap gap-1 content-start mt-1">
                            @foreach($dayEvents as $event)
                            @php
                            $statusClasses = [
                            'present' => 'bg-emerald-500',
                            'late' => 'bg-amber-500',
                            'absent' => 'bg-rose-500',
                            'permission' => 'bg-sky-500',
                            'sick' => 'bg-purple-500',
                            ];
                            $bgClass = $statusClasses[$event['status']] ?? 'bg-slate-500';
                            @endphp
                            <div class="w-5 h-5 sm:w-6 sm:h-6 rounded-full {{ $bgClass }} flex items-center justify-center text-[9px] sm:text-[10px] font-bold text-white ring-1 ring-white" title="{{ ucfirst($event['status']) }}: {{ $event['count'] }}">
                                {{ $event['count'] }}
                            </div>
                            @endforeach
                        </div>
                        @endif
                        @endif
                        @else
                        {{-- Tasks Mode: selalu dirender, tidak bergantung ada tugas atau tidak --}}
                        <div class="flex flex-col gap-1">
                            @if($hasEvents)
                            @foreach(array_slice($dayEvents, 0, 3) as $event)
                            @php
                            $priorityConfig = [
                            'high' => ['class' => 'bg-rose-100 text-rose-800 border-rose-200', 'dot' => 'bg-rose-500'],
                            'medium' => ['class' => 'bg-amber-100 text-amber-800 border-amber-200', 'dot' => 'bg-amber-500'],
                            'low' => ['class' => 'bg-emerald-100 text-emerald-800 border-emerald-200', 'dot' => 'bg-emerald-500'],
                            ];
                            $config = $priorityConfig[$event['priority']] ?? $priorityConfig['low'];
                            @endphp

                            @if($event['type'] === 'task')
                            <a href="{{ route('tasks.show', $event['id']) }}" class="hidden sm:block px-1.5 py-0.5 rounded border text-[10px] font-medium truncate {{ $config['class'] }} hover:opacity-80 transition-opacity">
                                {{ $event['title'] }}
                            </a>
                            <div class="sm:hidden w-2 h-2 rounded-full {{ $config['dot'] }} mx-auto"></div>
                            @elseif($event['type'] === 'task_assignment')
                            @php
                            $progress = $event['total'] > 0 ? round(($event['completed'] / $event['total']) * 100) : 0;
                            @endphp
                            <a href="{{ route('task-assignments.show', $event['id']) }}" class="hidden sm:block px-1.5 py-0.5 rounded border text-[10px] font-medium truncate {{ $config['class'] }} hover:opacity-80 transition-opacity" title="{{ $event['title'] }} ({{ $progress }}%)">
                                <div class="flex justify-between items-center gap-1">
                                    <span class="truncate">{{ Str::limit($event['title'], 8) }}</span>
                                    <span class="text-[9px] opacity-75">{{ $progress }}%</span>
                                </div>
                            </a>
                            <div class="sm:hidden w-2 h-2 rounded-full {{ $config['dot'] }} mx-auto"></div>
                            @endif
                            @endforeach

                            @if(count($dayEvents) > 3)
                            <div class="text-[9px] text-slate-400 text-center hidden sm:block">+{{ count($dayEvents) - 3 }} lainnya</div>
                            @endif
                            @endif

                            @if(auth()->user()->isIntern())
                            @php
                            $dailyReportId = $dailyReportMap[$day] ?? null;
                            @endphp
                            @if($dailyReportId)
                            <a href="{{ route('daily-reports.show', $dailyReportId) }}"
                                class="hidden sm:flex items-center gap-1 px-1.5 py-0.5 rounded border text-[9px] font-medium bg-emerald-50 text-emerald-700 border-emerald-200 hover:opacity-80 transition-opacity">
                                <i class="fas fa-check-circle"></i> Report terkirim
                            </a>
                            @elseif($hasEvents)
                            <span class="hidden sm:flex items-center gap-1 px-1.5 py-0.5 rounded border text-[9px] font-medium bg-slate-50 text-slate-500 border-slate-200">
                                <i class="fas fa-circle" style="font-size: 5px;"></i> Belum ada laporan
                            </span>
                            @endif
                            @endif
                        </div>
                        @endif
                    </div>
                </div>
                @endif
                @endforeach
            </div>
        </div>

        <!-- Legend -->
        <div class="p-4 border-t border-slate-100 bg-slate-50/30 flex flex-wrap gap-4 text-xs">
            <!-- Holiday Legend (always visible) -->
            <div class="flex items-center gap-2"><span class="w-3 h-3 rounded bg-rose-50 border border-rose-200"></span> <span class="text-rose-500 font-medium">Libur/Minggu</span></div>
            
            @if($viewMode === 'attendance')
            <div class="flex items-center gap-2"><span class="w-3 h-3 rounded bg-emerald-500"></span> Hadir</div>
            <div class="flex items-center gap-2"><span class="w-3 h-3 rounded bg-amber-500"></span> Terlambat</div>
            <div class="flex items-center gap-2"><span class="w-3 h-3 rounded bg-rose-500"></span> Absen</div>
            <div class="flex items-center gap-2"><span class="w-3 h-3 rounded bg-sky-500"></span> Izin</div>
            <div class="flex items-center gap-2"><span class="w-3 h-3 rounded bg-purple-500"></span> Sakit</div>
            @if(auth()->user()->canManage())
            <div class="ml-auto text-slate-400 hidden sm:block"><i class="fas fa-mouse-pointer mr-1"></i> Klik tanggal untuk detail</div>
            @endif
            @else
            <div class="flex items-center gap-2">
                <div class="w-3 h-3 rounded bg-rose-500"></div> Prioritas Tinggi
            </div>
            <div class="flex items-center gap-2">
                <div class="w-3 h-3 rounded bg-amber-500"></div> Prioritas Sedang
            </div>
            <div class="flex items-center gap-2">
                <div class="w-3 h-3 rounded bg-emerald-500"></div> Prioritas Rendah
            </div>
            @endif
        </div>
        
        <!-- Holiday List for Current Month -->
        @if(count($holidays) > 0)
        <div class="p-4 border-t border-slate-100 bg-white">
            <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-3 flex items-center gap-2">
                <i class="fas fa-calendar-day text-rose-400"></i> Hari Libur Nasional Bulan Ini
            </h4>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2">
                @foreach($holidays as $day => $holidayName)
                <div class="flex items-center gap-3 p-2 bg-rose-50/50 rounded-lg border border-rose-100">
                    <span class="w-8 h-8 bg-rose-100 text-rose-600 rounded-lg flex items-center justify-center font-bold text-sm">
                        {{ $day }}
                    </span>
                    <span class="text-sm text-slate-700 font-medium">{{ $holidayName }}</span>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    <!-- Attendance Stats Modal -->
    @if($showModal && $viewMode === 'attendance')
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6" wire:click.self="closeModal">
        <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity"></div>

        <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-2xl max-h-[90vh] flex flex-col overflow-hidden animate-slide-up">
            <!-- Modal Header -->
            <div class="p-5 border-b border-slate-100 flex justify-between items-start bg-slate-50/50">
                <div>
                    <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                        <i class="fas fa-calendar-day text-indigo-500"></i>
                        Statistik Kehadiran
                    </h3>
                    <p class="text-slate-500 text-sm mt-1">{{ $modalData['date'] ?? '' }}</p>
                </div>
                <button wire:click="closeModal" class="w-8 h-8 rounded-lg bg-slate-100 text-slate-500 flex items-center justify-center hover:bg-rose-50 hover:text-rose-500 transition-colors">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <!-- Stats Grid -->
            <div class="p-6 overflow-y-auto">
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-6">
                    <div class="bg-gradient-to-br from-emerald-50 to-emerald-100/50 p-4 rounded-xl text-center border border-emerald-100">
                        <div class="text-3xl font-black text-emerald-600 mb-1">{{ $modalData['present'] ?? 0 }}</div>
                        <div class="text-xs font-bold text-emerald-600/70 uppercase tracking-wider">Hadir</div>
                    </div>
                    <div class="bg-gradient-to-br from-amber-50 to-amber-100/50 p-4 rounded-xl text-center border border-amber-100">
                        <div class="text-3xl font-black text-amber-600 mb-1">{{ $modalData['late'] ?? 0 }}</div>
                        <div class="text-xs font-bold text-amber-600/70 uppercase tracking-wider">Terlambat</div>
                    </div>
                    <div class="bg-gradient-to-br from-rose-50 to-rose-100/50 p-4 rounded-xl text-center border border-rose-100">
                        <div class="text-3xl font-black text-rose-600 mb-1">{{ $modalData['absent'] ?? 0 }}</div>
                        <div class="text-xs font-bold text-rose-600/70 uppercase tracking-wider">Belum Absen</div>
                    </div>
                    <div class="bg-gradient-to-br from-sky-50 to-sky-100/50 p-4 rounded-xl text-center border border-sky-100">
                        <div class="text-3xl font-black text-sky-600 mb-1">{{ $modalData['permission'] ?? 0 }}</div>
                        <div class="text-xs font-bold text-sky-600/70 uppercase tracking-wider">Izin</div>
                    </div>
                    <div class="bg-gradient-to-br from-purple-50 to-purple-100/50 p-4 rounded-xl text-center border border-purple-100">
                        <div class="text-3xl font-black text-purple-600 mb-1">{{ $modalData['sick'] ?? 0 }}</div>
                        <div class="text-xs font-bold text-purple-600/70 uppercase tracking-wider">Sakit</div>
                    </div>
                    <div class="bg-slate-50 p-4 rounded-xl text-center border border-slate-100">
                        <div class="text-3xl font-black text-slate-600 mb-1">{{ $modalData['total'] ?? 0 }}</div>
                        <div class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Siswa</div>
                    </div>
                </div>

                <!-- Attendance List -->
                @if(!empty($modalData['attendances']))
                <div>
                    <h4 class="text-sm font-bold text-slate-700 uppercase tracking-wider mb-3 flex items-center gap-2">
                        <i class="fas fa-users text-slate-400"></i> Detail Kehadiran
                    </h4>
                    <div class="border border-slate-200 rounded-xl overflow-hidden">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-slate-50 text-slate-500 font-semibold uppercase text-xs">
                                <tr>
                                    <th class="px-4 py-3">Nama Siswa</th>
                                    <th class="px-4 py-3">Status</th>
                                    <th class="px-4 py-3 text-center">Masuk</th>
                                    <th class="px-4 py-3 text-center">Keluar</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($modalData['attendances'] as $att)
                                @php
                                $statusConfigs = [
                                'present' => ['bg' => 'bg-emerald-100', 'text' => 'text-emerald-700', 'label' => 'Hadir'],
                                'late' => ['bg' => 'bg-amber-100', 'text' => 'text-amber-700', 'label' => 'Terlambat'],
                                'permission' => ['bg' => 'bg-sky-100', 'text' => 'text-sky-700', 'label' => 'Izin'],
                                'sick' => ['bg' => 'bg-purple-100', 'text' => 'text-purple-700', 'label' => 'Sakit'],
                                'absent' => ['bg' => 'bg-rose-100', 'text' => 'text-rose-700', 'label' => 'Absen'],
                                ];
                                $config = $statusConfigs[$att['status']] ?? $statusConfigs['absent'];
                                @endphp
                                <tr class="hover:bg-slate-50/50">
                                    <td class="px-4 py-3 font-medium text-slate-700">{{ $att['name'] }}</td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded textxs font-bold {{ $config['bg'] }} {{ $config['text'] }}">
                                            {{ $config['label'] }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-center font-mono text-xs text-slate-500">{{ $att['check_in'] ? substr($att['check_in'], 0, 5) : '-' }}</td>
                                    <td class="px-4 py-3 text-center font-mono text-xs text-slate-500">{{ $att['check_out'] ? substr($att['check_out'], 0, 5) : '-' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @else
                <div class="text-center py-8 text-slate-400">
                    <i class="fas fa-inbox text-3xl mb-2 opacity-50"></i>
                    <p>Tidak ada data kehadiran untuk tanggal ini</p>
                </div>
                @endif
            </div>

            <!-- Modal Footer -->
            <div class="p-4 border-t border-slate-100 bg-slate-50/50 text-right">
                <button wire:click="closeModal" class="px-4 py-2 bg-white border border-slate-200 text-slate-600 rounded-lg hover:bg-slate-50 font-medium text-sm transition-colors shadow-sm">
                    Tutup
                </button>
            </div>
        </div>
    </div>
    @endif
</div>