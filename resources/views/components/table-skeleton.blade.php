{{-- Table Loading Skeleton Component --}}
<div class="space-y-3">
    @for ($i = 0; $i < ($rows ?? 5); $i++)
        <div class="flex gap-4 p-4 bg-slate-100 rounded-lg animate-pulse">
            <div class="w-10 h-10 bg-slate-200 rounded"></div>
            <div class="flex-1 space-y-2">
                <div class="h-4 bg-slate-200 rounded w-3/4"></div>
                <div class="h-3 bg-slate-200 rounded w-1/2"></div>
            </div>
            <div class="w-20 h-8 bg-slate-200 rounded"></div>
        </div>
    @endfor
</div>
