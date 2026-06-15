@props([
    'row',
    'rank',
    'href' => null,
    'value',
])

@php
    $tag = $href ? 'a' : 'div';
@endphp

<{{ $tag }}
    @if ($href) href="{{ $href }}" @endif
    {{ $attributes->class([
        'flex items-center gap-3 rounded-md px-2 py-2 transition',
        'hover:bg-zinc-50 dark:hover:bg-zinc-800/60' => filled($href),
    ]) }}
>
    <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-md bg-zinc-100 text-xs font-bold text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300">
        {{ $rank }}
    </span>

    <x-saas.avatar
        :name="$row['name']"
        :url="$row['avatar_url'] ?? null"
        size="sm"
        class="shrink-0"
    />

    <span class="min-w-0 flex-1 truncate text-sm font-medium text-zinc-900 dark:text-white">
        {{ $row['name'] }}
    </span>

    <span class="shrink-0 text-sm font-bold tabular-nums text-indigo-600 dark:text-indigo-400">
        {{ $value }}
    </span>
</{{ $tag }}>
