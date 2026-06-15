@props(['title', 'items' => [], 'empty' => 'موردی ثبت نشده است.', 'tone' => 'neutral'])

@php
    $toneClasses = match ($tone) {
        'positive' => 'border-emerald-200/80 bg-emerald-50/40 dark:border-emerald-500/20 dark:bg-emerald-950/20',
        'warning' => 'border-amber-200/80 bg-amber-50/40 dark:border-amber-500/20 dark:bg-amber-950/20',
        'action' => 'border-indigo-200/80 bg-indigo-50/40 dark:border-indigo-500/20 dark:bg-indigo-950/20',
        default => 'border-zinc-200/80 bg-zinc-50/40 dark:border-zinc-800 dark:bg-zinc-900/40',
    };
    $dotClasses = match ($tone) {
        'positive' => 'text-emerald-500',
        'warning' => 'text-amber-500',
        'action' => 'text-indigo-500',
        default => 'text-zinc-400',
    };
@endphp

<div {{ $attributes->merge(['class' => "rounded-lg border p-5 {$toneClasses}"]) }}>
    <h3 class="text-sm font-semibold text-zinc-900 dark:text-white">{{ $title }}</h3>
    <ul class="mt-3 space-y-2">
        @forelse ($items as $item)
            <li class="flex gap-2 text-sm leading-relaxed text-zinc-700 dark:text-zinc-300">
                <span @class(['mt-1.5 h-1.5 w-1.5 shrink-0 rounded-full bg-current', $dotClasses]) aria-hidden="true"></span>
                <span>{{ is_array($item) ? ($item['text'] ?? json_encode($item, JSON_UNESCAPED_UNICODE)) : $item }}</span>
            </li>
        @empty
            <li class="text-sm text-zinc-500">{{ $empty }}</li>
        @endforelse
    </ul>
</div>
