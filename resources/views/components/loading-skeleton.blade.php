{{-- Loading Skeleton Component --}}
<div {{ $attributes->merge(['class' => 'space-y-4']) }}>
    @for ($i = 0; $i < ($rows ?? 5); $i++)
        <div class="bg-slate-200 animate-pulse rounded-lg h-12"></div>
    @endfor
</div>
