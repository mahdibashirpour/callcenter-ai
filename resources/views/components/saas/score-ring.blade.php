@props([
    'score',
    'size' => 'md',
    'label' => null,
])

@php
    use App\Support\AgentPerformancePresenter;

    $sizes = [
        'sm' => ['box' => 'min-w-[3.5rem] px-2.5 py-2 text-lg', 'wrapper' => 'gap-1'],
        'md' => ['box' => 'min-w-[5rem] px-3 py-2.5 text-2xl', 'wrapper' => 'gap-1.5'],
        'lg' => ['box' => 'min-w-[6.5rem] px-5 py-4 text-4xl', 'wrapper' => 'gap-2'],
    ];
    $sizeClasses = $sizes[$size] ?? $sizes['md'];
    $displayScore = filled($score) && $score > 0 ? $score : '—';
@endphp

<div {{ $attributes->merge(['class' => "flex flex-col items-center {$sizeClasses['wrapper']}"]) }}>
    <div @class([
        'flex items-center justify-center rounded-md border-2 font-bold tabular-nums shadow-sm',
        $sizeClasses['box'],
        AgentPerformancePresenter::scoreBoxClass($score),
        AgentPerformancePresenter::scoreTextClass($score),
    ])>
        <span>{{ $displayScore }}</span>
    </div>
    @if ($label)
        <p class="text-xs font-medium text-zinc-500">{{ $label }}</p>
    @endif
</div>
