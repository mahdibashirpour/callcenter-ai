@props(['title', 'description' => null, 'action' => null, 'actionLabel' => null])

<div {{ $attributes->merge(['class' => 'saas-empty']) }}>
    <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-md bg-zinc-100 dark:bg-zinc-800">
        <svg class="h-6 w-6 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10.5 11.25h3M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
        </svg>
    </div>
    <h3 class="text-lg font-semibold text-zinc-900 dark:text-white">{{ $title }}</h3>
    @if ($description)
        <p class="mt-2 max-w-sm text-sm text-zinc-500">{{ $description }}</p>
    @endif
    @if ($action && $actionLabel)
        <a href="{{ $action }}" class="saas-btn-primary mt-6">{{ $actionLabel }}</a>
    @endif
    {{ $slot }}
</div>
