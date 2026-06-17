@props([
    'title' => 'یکپارچه‌سازی در حال راه‌اندازی است',
    'description' => 'تنظیمات اتصال هنوز کامل نشده است.',
])

<div class="saas-card border border-amber-200 bg-amber-50/60 dark:border-amber-900/50 dark:bg-amber-950/30">
    <div class="flex items-start gap-4">
        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-md bg-amber-100 text-amber-700 dark:bg-amber-900/50 dark:text-amber-200">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>
        <div class="space-y-2">
            <h2 class="text-lg font-semibold text-amber-950 dark:text-amber-50">{{ $title }}</h2>
            <p class="text-sm leading-6 text-amber-900/90 dark:text-amber-100/90">{{ $description }}</p>
            <p class="text-xs text-amber-700 dark:text-amber-300">پس از تکمیل راه‌اندازی، تنظیمات و وضعیت اتصال این بخش نمایش داده می‌شود.</p>
        </div>
    </div>
</div>
