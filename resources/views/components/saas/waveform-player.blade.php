@props(['url'])

<div
    {{ $attributes->merge(['class' => 'waveform-player']) }}
    data-waveform-player
    data-url="{{ $url }}"
    dir="ltr"
>
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:gap-4">
        <button
            type="button"
            data-waveform-play
            class="inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-md border border-zinc-200 bg-white text-zinc-900 transition hover:bg-zinc-50 focus:outline-none focus:ring-2 focus:ring-zinc-400/50 disabled:cursor-not-allowed disabled:opacity-50 dark:border-zinc-700 dark:bg-zinc-900 dark:text-white dark:hover:bg-zinc-800"
            aria-label="پخش یا توقف"
            disabled
        >
            <svg data-waveform-icon-play class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                <path d="M8 5.14v14.72a1 1 0 0 0 1.5.86l11.04-7.36a1 1 0 0 0 0-1.72L9.5 4.28A1 1 0 0 0 8 5.14Z" />
            </svg>
            <svg data-waveform-icon-pause class="hidden h-5 w-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                <path d="M6 5h4v14H6V5Zm8 0h4v14h-4V5Z" />
            </svg>
        </button>

        <div class="min-w-0 flex-1">
            <div class="relative">
                <div
                    data-waveform-loading
                    class="absolute inset-0 z-10 flex items-center justify-center rounded-md bg-zinc-50/90 px-3 dark:bg-zinc-900/90"
                >
                    <div class="flex items-center gap-2 text-sm text-zinc-500">
                        <span class="waveform-player__spinner" aria-hidden="true"></span>
                        <span>در حال بارگذاری موج صوتی…</span>
                    </div>
                </div>

                <div
                    data-waveform-canvas-wrap
                    class="overflow-hidden rounded-md border border-zinc-200/80 bg-zinc-50 px-2 py-2 transition-opacity dark:border-zinc-800 dark:bg-zinc-950/60"
                >
                    <div data-waveform-canvas class="w-full touch-manipulation"></div>
                </div>
            </div>
        </div>

        <div class="flex shrink-0 items-center justify-center gap-1 text-xs font-medium tabular-nums text-zinc-500 sm:min-w-[5.5rem] sm:justify-end">
            <span data-waveform-current>0:00</span>
            <span aria-hidden="true">/</span>
            <span data-waveform-duration>0:00</span>
        </div>
    </div>
</div>
