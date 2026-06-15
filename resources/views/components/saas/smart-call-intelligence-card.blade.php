@props(['href' => null])

@php
    $uploadUrl = $href ?? route('employer.manual-analyses.index');
@endphp

<a
    href="{{ $uploadUrl }}"
    {{ $attributes->merge([
        'class' => 'group relative block overflow-hidden rounded-lg border border-indigo-200/70 bg-gradient-to-br from-indigo-50 via-white to-violet-100/80 p-6 shadow-sm shadow-indigo-500/5 transition duration-300 hover:border-indigo-300 hover:shadow-md hover:shadow-indigo-500/10 focus:outline-none focus:ring-2 focus:ring-indigo-400/50 dark:border-indigo-500/30 dark:from-indigo-950/60 dark:via-zinc-900 dark:to-violet-950/40 dark:shadow-indigo-900/10 dark:hover:border-indigo-400/50 sm:p-8',
    ]) }}
>
    <div class="pointer-events-none absolute -start-16 -top-16 h-48 w-48 rounded-full bg-indigo-400/20 blur-3xl transition duration-500 group-hover:bg-indigo-400/30 dark:bg-indigo-500/10 dark:group-hover:bg-indigo-500/20" aria-hidden="true"></div>
    <div class="pointer-events-none absolute -bottom-20 -end-10 h-56 w-56 rounded-full bg-violet-400/15 blur-3xl transition duration-500 group-hover:bg-violet-400/25 dark:bg-violet-500/10" aria-hidden="true"></div>

    <div class="relative flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-start gap-4 sm:items-center sm:gap-6">
            <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-md bg-gradient-to-br from-indigo-600 to-violet-600 text-white shadow-sm transition duration-300 group-hover:scale-105 sm:h-16 sm:w-16">
                <svg class="h-7 w-7 sm:h-8 sm:w-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 10v4M6 8v8M8 6v12M10 9v6M12 4v16M14 9v6M16 7v10M18 10v4M20 11v2" opacity="0.85" />
                </svg>
            </div>

            <div class="min-w-0">
                <p class="text-xs font-semibold uppercase tracking-wider text-indigo-600 dark:text-indigo-400">هوش مصنوعی تماس</p>
                <h2 class="mt-1 text-2xl font-bold tracking-tight text-zinc-900 dark:text-white sm:text-3xl">تحلیل هوشمند مکالمات</h2>
                <p class="mt-2 max-w-2xl text-sm leading-relaxed text-zinc-600 dark:text-zinc-300 sm:text-base">
                    تماس‌های خود را آپلود کنید و تحلیل دقیق گفتار، عملکرد و کیفیت مکالمه را دریافت کنید
                </p>
            </div>
        </div>

        <span class="saas-btn-secondary shrink-0 self-start bg-white/90 shadow-sm transition duration-300 group-hover:border-indigo-300 group-hover:bg-white group-hover:shadow-md dark:bg-zinc-900/90 dark:group-hover:border-indigo-500/50 sm:self-center">
            آپلود تماس
            <svg class="h-4 w-4 transition-transform duration-300 group-hover:-translate-x-0.5 rtl:rotate-180 rtl:group-hover:translate-x-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
            </svg>
        </span>
    </div>
</a>
