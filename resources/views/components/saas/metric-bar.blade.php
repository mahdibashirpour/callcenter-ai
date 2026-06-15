@props(['label', 'value', 'max' => 100, 'suffix' => null])

@php
    $numeric = is_numeric($value) ? (float) $value : 0;
    $percent = $max > 0 ? min(100, max(0, ($numeric / $max) * 100)) : 0;
@endphp

<div {{ $attributes }}>
    <div class="mb-1 flex items-center justify-between gap-2 text-xs">
        <span class="text-zinc-500">{{ $label }}</span>
        <span class="font-semibold tabular-nums text-zinc-800 dark:text-zinc-200">
            {{ $value ?: '—' }}{{ $suffix }}
        </span>
    </div>
    <div class="h-1.5 overflow-hidden rounded-full bg-zinc-100 dark:bg-zinc-800">
        <div
            class="h-1.5 rounded-full bg-gradient-to-l from-indigo-500 to-violet-500 transition-all"
            style="width: {{ $percent }}%"
        ></div>
    </div>
</div>
