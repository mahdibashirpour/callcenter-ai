<div class="saas-page">
    <div class="flex flex-col gap-6 sm:flex-row sm:items-start sm:justify-between">
        <div class="flex items-center gap-5">
            <x-saas.avatar :employee="$employee" size="xl" ring />
            <div>
                <h1 class="text-3xl font-bold tracking-tight">{{ $employee->full_name }}</h1>
                <p class="mt-1 text-zinc-500">{{ $employee->user?->email }}</p>
                @if ($employee->department || $employee->position)
                    <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">
                        {{ collect([$employee->position, $employee->department])->filter()->implode(' · ') }}
                    </p>
                @endif
            </div>
        </div>
        <a href="{{ route('employer.employees.edit', $employee) }}" class="saas-btn-primary shrink-0">ویرایش</a>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <x-saas.stat-card label="بخش" :value="$employee->department ?? '—'" />
        <x-saas.stat-card label="سمت" :value="$employee->position ?? '—'" />
        <x-saas.stat-card label="تحلیل‌ها" :value="$employee->conversationAnalyses->count()" />
        <x-saas.stat-card label="وضعیت" :value="$employee->is_active ? 'فعال' : 'غیرفعال'" />
    </div>

    @if ($employee->conversationAnalyses->isNotEmpty())
        <div class="saas-card">
            <h2 class="saas-section-title">تحلیل‌های اخیر AI</h2>
            <div class="mt-4 space-y-2">
                @foreach ($employee->conversationAnalyses->take(5) as $analysis)
                    <a href="{{ route('employer.intelligence.show', $analysis) }}" class="flex items-center justify-between rounded-lg border border-zinc-200/80 px-4 py-3 transition hover:border-zinc-300 hover:bg-zinc-50 dark:border-zinc-800 dark:hover:border-zinc-700 dark:hover:bg-zinc-800/50">
                        <div>
                            <p class="font-medium">امتیاز {{ $analysis->score }}</p>
                            <p class="text-sm text-zinc-500">{{ shamsi($analysis->analyzed_at, 'datetime') }}</p>
                        </div>
                        <span class="saas-badge-success">{{ $analysis->sentiment->label() }}</span>
                    </a>
                @endforeach
            </div>
        </div>
    @endif
</div>
