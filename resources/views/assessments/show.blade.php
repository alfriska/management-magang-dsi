@extends('layouts.app')

@section('title', 'Detail Penilaian')

@section('content')
<div class="slide-up max-w-[1200px] mx-auto space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div class="flex items-center gap-4">
            <a href="{{ route('assessments.index') }}" class="btn btn-secondary btn-icon">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h2 class="text-2xl font-bold text-slate-800 tracking-tight mb-1">
                    Penilaian - {{ $assessment->intern->user->name }}
                </h2>
                <div class="flex items-center gap-2 text-slate-500 text-sm font-medium">
                    <span class="bg-indigo-50 text-indigo-700 px-2 py-0.5 rounded text-xs font-bold uppercase tracking-wider">
                        {{ $assessment->task->title ?? 'Penilaian Umum' }}
                    </span>
                    <span>|</span>
                    <i class="far fa-calendar-alt"></i> {{ $assessment->created_at->format('d M Y') }}
                </div>
            </div>
        </div>
        <a href="{{ route('assessments.edit', $assessment) }}" class="btn btn-warning shadow-sm self-end sm:self-auto">
            <i class="fas fa-edit mr-2"></i> Edit Penilaian
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Score Card -->
        <div class="card p-0 overflow-hidden lg:col-span-1 border border-indigo-100 shadow-indigo-100/50">
            <div class="bg-gradient-to-br from-indigo-600 to-violet-600 p-8 text-center text-white relative overflow-hidden">
                <div class="absolute top-0 right-0 p-4 opacity-10">
                    <i class="fas fa-award text-9xl transform rotate-12"></i>
                </div>
                
                <div class="relative z-10">
                    <div class="text-sm font-bold uppercase tracking-widest text-indigo-200 mb-4">Total Nilai</div>
                    <div class="inline-block relative">
                         @php
                            $gradeColor = match($assessment->grade) {
                                'A' => 'text-emerald-400',
                                'B' => 'text-sky-400',
                                'C' => 'text-amber-400',
                                default => 'text-rose-400'
                            };
                        @endphp
                        <span class="text-8xl font-black {{ $gradeColor }} drop-shadow-md">
                            {{ $assessment->grade }}
                        </span>
                    </div>
                    <div class="text-4xl font-bold mt-2">{{ $assessment->average_score }}<span class="text-xl text-indigo-300">/100</span></div>
                    <div class="mt-8 pt-6 border-t border-indigo-500/30 flex items-center justify-center gap-3">
                        <div class="text-indigo-200 text-xs uppercase font-bold tracking-wider">Dinilai Oleh</div>
                        <div class="flex items-center gap-2 bg-white/10 px-3 py-1.5 rounded-full backdrop-blur-sm">
                             <div class="w-6 h-6 rounded-full bg-indigo-200 text-indigo-700 flex items-center justify-center text-xs font-bold">
                                {{ strtoupper(substr($assessment->assessedBy->name ?? 'N', 0, 1)) }}
                            </div>
                            <span class="font-bold text-sm">{{ $assessment->assessedBy->name }}</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Radar Chart Container -->
            <div class="p-6 bg-white min-h-[300px] flex flex-col">
                <h3 class="text-sm font-bold text-slate-400 uppercase tracking-wider mb-4 text-center">Analisis Radar</h3>
                <div class="relative flex-1">
                    <canvas id="radarChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Detail Skills -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Skill Progress Bars -->
            <div class="card p-6 border border-slate-100">
                <h3 class="font-bold text-slate-800 text-lg mb-6 flex items-center gap-2">
                    <i class="fas fa-chart-bar text-indigo-500"></i> Detail Kompetensi
                </h3>
                
                <div class="space-y-6">
                    <!-- Item -->
                    <div class="group">
                        <div class="flex justify-between mb-2">
                            <span class="font-semibold text-slate-700">Kualitas Kerja</span>
                            <span class="font-bold text-slate-800">{{ $assessment->quality_score }}<span class="text-slate-400 text-sm">/100</span></span>
                        </div>
                        <div class="h-3 bg-slate-100 rounded-full overflow-hidden">
                            <div class="h-full bg-gradient-to-r from-indigo-500 to-indigo-400 rounded-full transition-all duration-1000 ease-out" style="width: 0%" data-width="{{ $assessment->quality_score }}%"></div>
                        </div>
                    </div>
                    
                    <div class="group">
                        <div class="flex justify-between mb-2">
                            <span class="font-semibold text-slate-700">Kecepatan</span>
                            <span class="font-bold text-slate-800">{{ $assessment->speed_score }}<span class="text-slate-400 text-sm">/100</span></span>
                        </div>
                        <div class="h-3 bg-slate-100 rounded-full overflow-hidden">
                             <div class="h-full bg-gradient-to-r from-emerald-500 to-emerald-400 rounded-full transition-all duration-1000 ease-out delay-100" style="width: 0%" data-width="{{ $assessment->speed_score }}%"></div>
                        </div>
                    </div>

                    <div class="group">
                        <div class="flex justify-between mb-2">
                            <span class="font-semibold text-slate-700">Inisiatif</span>
                            <span class="font-bold text-slate-800">{{ $assessment->initiative_score }}<span class="text-slate-400 text-sm">/100</span></span>
                        </div>
                        <div class="h-3 bg-slate-100 rounded-full overflow-hidden">
                             <div class="h-full bg-gradient-to-r from-amber-500 to-amber-400 rounded-full transition-all duration-1000 ease-out delay-200" style="width: 0%" data-width="{{ $assessment->initiative_score }}%"></div>
                        </div>
                    </div>

                    <div class="group">
                        <div class="flex justify-between mb-2">
                            <span class="font-semibold text-slate-700">Kerjasama Tim</span>
                            <span class="font-bold text-slate-800">{{ $assessment->teamwork_score }}<span class="text-slate-400 text-sm">/100</span></span>
                        </div>
                        <div class="h-3 bg-slate-100 rounded-full overflow-hidden">
                             <div class="h-full bg-gradient-to-r from-cyan-500 to-cyan-400 rounded-full transition-all duration-1000 ease-out delay-300" style="width: 0%" data-width="{{ $assessment->teamwork_score }}%"></div>
                        </div>
                    </div>

                    <div class="group">
                        <div class="flex justify-between mb-2">
                            <span class="font-semibold text-slate-700">Komunikasi</span>
                            <span class="font-bold text-slate-800">{{ $assessment->communication_score }}<span class="text-slate-400 text-sm">/100</span></span>
                        </div>
                        <div class="h-3 bg-slate-100 rounded-full overflow-hidden">
                             <div class="h-full bg-gradient-to-r from-purple-500 to-purple-400 rounded-full transition-all duration-1000 ease-out delay-400" style="width: 0%" data-width="{{ $assessment->communication_score }}%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quantitative Feedback -->
            @if($assessment->strengths || $assessment->improvements)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @if($assessment->strengths)
                    <div class="card p-6 bg-emerald-50/50 border border-emerald-100 h-full">
                        <h4 class="font-bold text-emerald-800 mb-3 flex items-center gap-2">
                            <i class="fas fa-thumbs-up text-emerald-500"></i> Kelebihan
                        </h4>
                        <p class="text-slate-700 text-sm leading-relaxed">{{ $assessment->strengths }}</p>
                    </div>
                    @endif
                    
                    @if($assessment->improvements)
                    <div class="card p-6 bg-amber-50/50 border border-amber-100 h-full">
                        <h4 class="font-bold text-amber-800 mb-3 flex items-center gap-2">
                            <i class="fas fa-lightbulb text-amber-500"></i> Area Perbaikan
                        </h4>
                        <p class="text-slate-700 text-sm leading-relaxed">{{ $assessment->improvements }}</p>
                    </div>
                    @endif
                </div>
            @endif

            @if($assessment->comments)
            <div class="card p-6 border border-slate-100">
                <h4 class="font-bold text-slate-800 mb-3 flex items-center gap-2">
                    <i class="fas fa-comment-alt text-indigo-500"></i> Komentar Tambahan
                </h4>
                <div class="p-4 bg-slate-50 rounded-xl text-slate-700 text-sm italic border border-slate-100 relative">
                    <i class="fas fa-quote-left text-slate-200 text-4xl absolute -top-2 -left-2"></i>
                    <p class="relative z-10">{{ $assessment->comments }}</p>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Animate Progress Bars
    document.addEventListener('DOMContentLoaded', () => {
        setTimeout(() => {
            document.querySelectorAll('[data-width]').forEach(el => {
                el.style.width = el.getAttribute('data-width');
            });
        }, 300);
    });

    const ctx = document.getElementById('radarChart').getContext('2d');
    new Chart(ctx, {
        type: 'radar',
        data: {
            labels: ['Kualitas', 'Kecepatan', 'Inisiatif', 'Kerjasama', 'Komunikasi'],
            datasets: [{
                label: 'Skor',
                data: [
                    {{ $assessment->quality_score }},
                    {{ $assessment->speed_score }},
                    {{ $assessment->initiative_score }},
                    {{ $assessment->teamwork_score }},
                    {{ $assessment->communication_score }}
                ],
                backgroundColor: 'rgba(99, 102, 241, 0.2)',
                borderColor: 'rgba(99, 102, 241, 0.8)',
                borderWidth: 2,
                pointBackgroundColor: 'rgba(99, 102, 241, 1)',
                pointBorderColor: '#fff',
                pointHoverBackgroundColor: '#fff',
                pointHoverBorderColor: 'rgba(99, 102, 241, 1)'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                r: {
                    angleLines: { color: 'rgba(0, 0, 0, 0.05)' },
                    grid: { color: 'rgba(0, 0, 0, 0.05)' },
                    pointLabels: {
                        color: '#64748b',
                        font: { family: "'Plus Jakarta Sans', sans-serif", size: 11, weight: '600' }
                    },
                    ticks: { display: false }
                }
            },
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: 'rgba(15, 23, 42, 0.9)',
                    titleFont: { family: "'Plus Jakarta Sans', sans-serif" },
                    bodyFont: { family: "'Plus Jakarta Sans', sans-serif" },
                    padding: 10,
                    cornerRadius: 8,
                    displayColors: false
                }
            }
        }
    });
</script>
@endpush
@endsection
