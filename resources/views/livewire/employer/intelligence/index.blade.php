@php
    use App\Support\AnalysisCallPresenter;
@endphp

<div class="space-y-6" wire:loading.class="opacity-70">
    <x-saas.page-header
        title="تحلیل تماس‌ها"
        description="داشبورد هوش تماس برای پایش، فیلتر و بررسی تحلیل مکالمات تیم."
    >
        <x-slot:actions>
            <a href="{{ route('employer.intelligence.performance') }}" class="saas-btn-secondary">عملکرد کارشناسان</a>
            <a href="{{ route('employer.reports.index') }}" class="saas-btn-secondary">گزارش‌های مدیریتی</a>
        </x-slot:actions>
    </x-saas.page-header>

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-saas.stat-card label="تحلیل‌های فیلترشده" :value="number_format($overview['total'])" />
        <x-saas.stat-card label="میانگین امتیاز" :value="$overview['average_score']" />
        <x-saas.stat-card label="میانگین مدت تماس" :value="$overview['average_duration_label']" />
        <x-saas.stat-card
            label="تماس از دست رفته"
            :value="number_format($overview['missed_count'])"
            :hint="'ورودی: '.number_format($overview['inbound_count']).' · خروجی: '.number_format($overview['outbound_count'])"
        />
    </div>

    @include('livewire.employer.intelligence.partials.analysis-filters', [
        'primaryDatePresets' => $primaryDatePresets,
        'moreDatePresets' => $moreDatePresets,
        'employees' => $employees,
        'callStatuses' => $callStatuses,
        'directions' => $directions,
        'filter' => $filter,
    ])

    <div class="saas-card overflow-hidden p-0">
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-zinc-200/80 px-6 py-4 dark:border-zinc-800">
            <div>
                <h2 class="text-lg font-semibold">لیست تحلیل مکالمات</h2>
                <p class="mt-1 text-sm text-zinc-500">
                    {{ number_format($analyses->total()) }} نتیجه
                    @if ($filter->hasActiveFilters())
                        · فیلتر فعال
                    @endif
                </p>
            </div>
            <input
                wire:model.live.debounce.300ms="search"
                type="search"
                placeholder="جستجو در خلاصه، مشتری یا کارشناس..."
                class="saas-input max-w-xs text-sm"
            >
        </div>

        <div wire:loading.flex wire:target="search,datePreset,customFrom,customTo,filterEmployeeId,callStatus,directionFilter,durationMin,durationMax,applyQuickFilter,setDatePreset,toggleCustomDateRange,toggleMoreDatePresets,clearDateFilter,clearFilters,sortByColumn,filterByAgent" class="hidden flex-col gap-3 p-6">
            @for ($i = 0; $i < 6; $i++)
                <div class="animate-pulse rounded-lg border border-zinc-200/80 p-4 dark:border-zinc-800">
                    <div class="flex gap-4">
                        <div class="h-10 w-10 rounded-full bg-zinc-200 dark:bg-zinc-800"></div>
                        <div class="flex-1 space-y-2">
                            <div class="h-4 w-1/3 rounded bg-zinc-200 dark:bg-zinc-800"></div>
                            <div class="h-3 w-2/3 rounded bg-zinc-100 dark:bg-zinc-900"></div>
                        </div>
                        <div class="h-8 w-12 rounded bg-zinc-200 dark:bg-zinc-800"></div>
                    </div>
                </div>
            @endfor
        </div>

        <div wire:loading.remove wire:target="search,datePreset,customFrom,customTo,filterEmployeeId,callStatus,directionFilter,durationMin,durationMax,applyQuickFilter,setDatePreset,toggleCustomDateRange,toggleMoreDatePresets,clearDateFilter,clearFilters,sortByColumn,filterByAgent">
            @if ($analyses->isEmpty())
                <div class="p-8">
                    @if ($filter->hasActiveFilters())
                        <x-saas.empty-state
                            title="نتیجه‌ای یافت نشد"
                            description="با فیلترهای فعلی هیچ تحلیلی پیدا نشد. فیلترها را تغییر دهید یا پاک کنید."
                        >
                            <button type="button" wire:click="clearFilters" class="saas-btn-primary mt-4">پاک کردن فیلترها</button>
                        </x-saas.empty-state>
                    @else
                        <x-saas.empty-state
                            title="هنوز تحلیلی وجود ندارد"
                            description="تحلیل‌های گفتگو پس از پردازش تماس‌ها اینجا نمایش داده می‌شوند."
                        />
                    @endif
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="saas-table">
                        <thead>
                            <tr class="bg-zinc-50/80 dark:bg-zinc-900/50">
                                @php
                                    $sortIcon = fn (string $column) => $sortBy === $column
                                        ? ($sortDir === 'asc' ? '↑' : '↓')
                                        : '↕';
                                @endphp
                                <th>
                                    <button type="button" wire:click="sortByColumn('analyzed_at')" class="inline-flex items-center gap-1 font-semibold text-zinc-700 hover:text-zinc-900 dark:text-zinc-300">
                                        تاریخ و زمان <span class="text-xs text-zinc-400">{{ $sortIcon('analyzed_at') }}</span>
                                    </button>
                                </th>
                                <th>
                                    <button type="button" wire:click="sortByColumn('agent')" class="inline-flex items-center gap-1 font-semibold text-zinc-700 hover:text-zinc-900 dark:text-zinc-300">
                                        کارشناس <span class="text-xs text-zinc-400">{{ $sortIcon('agent') }}</span>
                                    </button>
                                </th>
                                <th>خلاصه</th>
                                <th>
                                    <button type="button" wire:click="sortByColumn('duration')" class="inline-flex items-center gap-1 font-semibold text-zinc-700 hover:text-zinc-900 dark:text-zinc-300">
                                        مدت <span class="text-xs text-zinc-400">{{ $sortIcon('duration') }}</span>
                                    </button>
                                </th>
                                <th>
                                    <button type="button" wire:click="sortByColumn('status')" class="inline-flex items-center gap-1 font-semibold text-zinc-700 hover:text-zinc-900 dark:text-zinc-300">
                                        وضعیت تماس <span class="text-xs text-zinc-400">{{ $sortIcon('status') }}</span>
                                    </button>
                                </th>
                                <th>جهت</th>
                                <th>
                                    <button type="button" wire:click="sortByColumn('score')" class="inline-flex items-center gap-1 font-semibold text-zinc-700 hover:text-zinc-900 dark:text-zinc-300">
                                        امتیاز <span class="text-xs text-zinc-400">{{ $sortIcon('score') }}</span>
                                    </button>
                                </th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($analyses as $analysis)
                                @php
                                    $status = AnalysisCallPresenter::status($analysis);
                                    $direction = AnalysisCallPresenter::direction($analysis);
                                @endphp
                                <tr wire:key="analysis-{{ $analysis->id }}" class="group transition hover:bg-zinc-50/80 dark:hover:bg-zinc-900/40">
                                    <td class="whitespace-nowrap">
                                        <p class="font-medium text-zinc-900 dark:text-white">{{ shamsi($analysis->analyzed_at) }}</p>
                                        <p class="text-xs text-zinc-500">{{ shamsi($analysis->analyzed_at, 'time') }}</p>
                                    </td>
                                    <td class="whitespace-nowrap">
                                        @if ($analysis->employee)
                                            <button
                                                type="button"
                                                wire:click.stop="filterByAgent({{ $analysis->organization_user_id }})"
                                                class="text-start"
                                                title="فیلتر بر اساس این کارشناس"
                                            >
                                                <x-saas.user-cell
                                                    :employee="$analysis->employee"
                                                    :subtitle="$analysis->employee->department"
                                                    avatar-size="xs"
                                                />
                                            </button>
                                        @else
                                            <span class="text-zinc-400">بدون اختصاص</span>
                                        @endif
                                    </td>
                                    <td class="max-w-md">
                                        <p class="line-clamp-2 text-sm text-zinc-600 dark:text-zinc-300">{{ $analysis->summary }}</p>
                                        <p class="mt-1 text-xs text-zinc-400">{{ $analysis->source?->label() ?? 'VoIP' }}</p>
                                    </td>
                                    <td class="whitespace-nowrap tabular-nums text-sm">{{ AnalysisCallPresenter::durationLabel($analysis) }}</td>
                                    <td class="whitespace-nowrap">
                                        @if ($status)
                                            <span @class(['saas-badge', AnalysisCallPresenter::statusBadgeClass($status)])>{{ $status->label() }}</span>
                                        @else
                                            <span class="text-zinc-400">—</span>
                                        @endif
                                    </td>
                                    <td class="whitespace-nowrap">
                                        @if ($direction)
                                            <span class="saas-badge bg-zinc-100 text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300">{{ $direction->label() }}</span>
                                        @else
                                            <span class="text-zinc-400">—</span>
                                        @endif
                                    </td>
                                    <td class="whitespace-nowrap">
                                        <span @class([
                                            'text-lg font-bold tabular-nums',
                                            'text-emerald-600' => $analysis->score >= 85,
                                            'text-amber-600' => $analysis->score >= 70 && $analysis->score < 85,
                                            'text-red-600' => $analysis->score < 70,
                                        ])>{{ $analysis->score }}</span>
                                    </td>
                                    <td class="whitespace-nowrap text-end">
                                        <a href="{{ route('employer.intelligence.show', $analysis) }}" class="saas-btn-secondary py-1.5 text-xs opacity-0 transition group-hover:opacity-100">
                                            مشاهده
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="border-t border-zinc-200/80 px-6 py-4 dark:border-zinc-800">
                    {{ $analyses->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
