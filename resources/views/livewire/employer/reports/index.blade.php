@php
    $kpis = $dashboard['kpis'];
    $deltas = $dashboard['kpis_delta'];
    $callTrend = $dashboard['call_activity_trend'];
    $qualityTrend = $dashboard['quality_trend'];
    $leadDist = $dashboard['lead_distribution'];
    $concerns = $dashboard['concerns_breakdown'];
    $employees = $dashboard['employee_comparison'];
    $leaderboards = $dashboard['leaderboards'];
    $aiTrend = $dashboard['ai_usage_trend'];

    $callChart = [
        'labels' => collect($callTrend)->pluck('label')->all(),
        'datasets' => [[
            'label' => 'تماس‌ها',
            'data' => collect($callTrend)->pluck('count')->all(),
            'backgroundColor' => 'rgba(99, 102, 241, 0.7)',
            'borderColor' => 'rgb(99, 102, 241)',
            'borderWidth' => 1,
        ]],
    ];

    $qualityChart = [
        'labels' => collect($qualityTrend)->pluck('label')->all(),
        'datasets' => [[
            'label' => 'میانگین امتیاز',
            'data' => collect($qualityTrend)->pluck('avg_score')->all(),
            'borderColor' => 'rgb(16, 185, 129)',
            'backgroundColor' => 'rgba(16, 185, 129, 0.15)',
            'fill' => true,
            'tension' => 0.3,
        ]],
    ];

    $leadChart = [
        'labels' => ['بالا', 'متوسط', 'پایین'],
        'datasets' => [[
            'data' => [$leadDist['high'], $leadDist['medium'], $leadDist['low']],
            'backgroundColor' => ['#10b981', '#f59e0b', '#f43f5e'],
        ]],
    ];

    $concernChart = [
        'labels' => collect($concerns)->pluck('label')->all(),
        'datasets' => [[
            'label' => 'نگرانی‌ها',
            'data' => collect($concerns)->pluck('count')->all(),
            'backgroundColor' => 'rgba(245, 158, 11, 0.8)',
        ]],
    ];

    $employeeChart = [
        'labels' => collect($employees)->pluck('name')->all(),
        'datasets' => [
            [
                'label' => 'کیفیت تماس',
                'data' => collect($employees)->pluck('average_score')->all(),
                'backgroundColor' => 'rgba(99, 102, 241, 0.8)',
            ],
            [
                'label' => 'کیفیت لید',
                'data' => collect($employees)->pluck('average_lead_score')->all(),
                'backgroundColor' => 'rgba(16, 185, 129, 0.8)',
            ],
        ],
    ];

    $aiChart = [
        'labels' => collect($aiTrend)->pluck('label')->all(),
        'datasets' => [
            [
                'label' => 'تحلیل‌ها',
                'data' => collect($aiTrend)->pluck('analyses')->all(),
                'borderColor' => 'rgb(99, 102, 241)',
                'yAxisID' => 'y',
            ],
            [
                'label' => 'هزینه',
                'data' => collect($aiTrend)->pluck('cost')->all(),
                'borderColor' => 'rgb(245, 158, 11)',
                'yAxisID' => 'y1',
            ],
        ],
        'options' => [
            'scales' => [
                'y' => ['type' => 'linear', 'position' => 'left', 'beginAtZero' => true],
                'y1' => ['type' => 'linear', 'position' => 'right', 'beginAtZero' => true, 'grid' => ['drawOnChartArea' => false]],
            ],
        ],
    ];
@endphp

<div class="space-y-6" wire:loading.class="opacity-60">
    {{-- Header --}}
    <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
        <div>
            <h1 class="text-3xl font-semibold tracking-tight">گزارش‌های مدیریتی</h1>
            <p class="mt-2 text-zinc-500">داشبورد تصمیم‌گیری برای مدیران — عملکرد تیم، لیدها، نگرانی‌ها و مصرف هوش مصنوعی.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <button type="button" wire:click="export('csv')" class="saas-btn-secondary text-sm">CSV</button>
            <button type="button" wire:click="export('xlsx')" class="saas-btn-secondary text-sm">Excel</button>
            <button type="button" wire:click="export('pdf')" class="saas-btn-secondary text-sm">PDF</button>
        </div>
    </div>

    {{-- Filters --}}
    <div class="saas-card sticky top-0 z-10 space-y-4 shadow-sm">
        <div class="flex flex-wrap gap-2">
            @foreach ($presets as $preset)
                <button
                    type="button"
                    wire:click="$set('datePreset', '{{ $preset->value }}')"
                    @class([
                        'rounded-lg px-3 py-1.5 text-sm font-medium transition',
                        'bg-zinc-900 text-white dark:bg-white dark:text-zinc-900' => $datePreset === $preset->value,
                        'bg-zinc-100 text-zinc-700 hover:bg-zinc-200 dark:bg-zinc-800 dark:text-zinc-200' => $datePreset !== $preset->value,
                    ])
                >{{ $preset->label() }}</button>
            @endforeach
        </div>

        @if ($datePreset === 'custom')
            <div class="flex flex-wrap items-center gap-3">
                <label class="text-sm text-zinc-500">از</label>
                <x-saas.jalali-date-input wire:model.live="customFrom" class="text-sm" />
                <label class="text-sm text-zinc-500">تا</label>
                <x-saas.jalali-date-input wire:model.live="customTo" class="text-sm" />
            </div>
        @endif

        <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
            <div class="flex flex-wrap items-center gap-2">
                <span class="text-sm font-medium text-zinc-500">کارشناسان:</span>
                <button type="button" wire:click="clearEmployeeFilter" @class([
                    'rounded-md px-3 py-1 text-xs font-medium',
                    'bg-indigo-600 text-white' => $selectedEmployeeIds === [],
                    'bg-zinc-100 text-zinc-600 dark:bg-zinc-800' => $selectedEmployeeIds !== [],
                ])>همه</button>
                @foreach ($filterEmployees as $employee)
                    <x-saas.agent-chip
                        :employee="$employee"
                        wire:click="toggleEmployee({{ $employee->id }})"
                        :active="in_array($employee->id, $selectedEmployeeIds)"
                    />
                @endforeach
            </div>
            <label class="flex items-center gap-2 text-sm text-zinc-600">
                <input type="checkbox" wire:model.live="compareMode" class="rounded border-zinc-300">
                مقایسه کارشناسان
            </label>
        </div>
    </div>

    {{-- Executive Summary --}}
    <div class="saas-card border border-indigo-200 bg-indigo-50/50 dark:border-indigo-900/50 dark:bg-indigo-950/20">
        <h2 class="text-sm font-semibold uppercase tracking-wider text-indigo-600">خلاصه مدیریتی</h2>
        <p class="mt-3 leading-7 text-zinc-700 dark:text-zinc-200">{{ $dashboard['executive_summary'] }}</p>
    </div>

    {{-- KPI Grid --}}
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-5">
        <x-saas.stat-card label="کل تماس‌ها" :value="$kpis['total_calls']" />
        <x-saas.stat-card label="تحلیل‌شده" :value="$kpis['total_analyzed']" :trend="$deltas['total_analyzed']" />
        <x-saas.stat-card label="میانگین کیفیت" :value="$kpis['average_quality_score'] ?: '—'" :trend="$deltas['average_quality_score']" />
        <x-saas.stat-card label="میانگین کیفیت لید" :value="$kpis['average_lead_quality_score'] ?: '—'" :trend="$deltas['average_lead_quality_score']" />
        <x-saas.stat-card label="لیدهای با کیفیت" :value="$kpis['high_quality_leads']" :trend="$deltas['high_quality_leads']" />
        <x-saas.stat-card label="کل لیدها" :value="$kpis['total_leads']" />
        <x-saas.stat-card label="نگرانی‌های ثبت‌شده" :value="$kpis['total_concerns']" />
        <x-saas.stat-card label="میانگین مدت تماس" :value="$kpis['average_call_duration_label']" />
        <x-saas.stat-card label="هزینه AI" :value="$kpis['total_ai_cost']" />
        <x-saas.stat-card label="برترین کارشناس" :value="$kpis['top_employee']" :hint="'امتیاز: '.$kpis['top_employee_score']" />
    </div>

    {{-- Charts Row 1 --}}
    <div class="grid gap-6 lg:grid-cols-2">
        <div class="saas-card">
            <h2 class="text-lg font-semibold">روند فعالیت تماس</h2>
            <div class="mt-4 h-64" wire:ignore>
                <canvas
                    id="chart-call-activity"
                    data-report-chart
                    data-type="bar"
                    data-drilldown="period"
                    data-drilldown-values='@json(collect($callTrend)->pluck('period')->all())'
                    data-config='@json($callChart)'
                ></canvas>
            </div>
        </div>
        <div class="saas-card">
            <h2 class="text-lg font-semibold">روند کیفیت تماس</h2>
            <div class="mt-4 h-64" wire:ignore>
                <canvas
                    id="chart-quality-trend"
                    data-report-chart
                    data-type="line"
                    data-drilldown="period"
                    data-drilldown-values='@json(collect($qualityTrend)->pluck('period')->all())'
                    data-config='@json($qualityChart)'
                ></canvas>
            </div>
        </div>
    </div>

    {{-- Charts Row 2 --}}
    <div class="grid gap-6 lg:grid-cols-2">
        <div class="saas-card">
            <h2 class="text-lg font-semibold">توزیع کیفیت لید</h2>
            <div class="mt-4 mx-auto h-64 max-w-xs" wire:ignore>
                <canvas
                    id="chart-lead-dist"
                    data-report-chart
                    data-type="doughnut"
                    data-drilldown="lead_level"
                    data-drilldown-values='["high","medium","low"]'
                    data-config='@json($leadChart)'
                ></canvas>
            </div>
        </div>
        <div class="saas-card">
            <h2 class="text-lg font-semibold">دسته‌بندی نگرانی‌ها</h2>
            <div class="mt-4 h-64" wire:ignore>
                <canvas
                    id="chart-concerns"
                    data-report-chart
                    data-type="bar"
                    data-drilldown="concern_type"
                    data-drilldown-values='@json(collect($concerns)->pluck('type')->all())'
                    data-config='@json(array_merge($concernChart, ['options' => ['indexAxis' => 'y']]))'
                ></canvas>
            </div>
        </div>
    </div>

    {{-- Employee Comparison --}}
    <div class="saas-card">
        <h2 class="text-lg font-semibold">مقایسه عملکرد کارشناسان</h2>
        <div class="mt-4 h-72" wire:ignore>
            <canvas
                id="chart-employees"
                data-report-chart
                data-type="bar"
                data-drilldown="employee"
                data-drilldown-values='@json(collect($employees)->pluck('id')->all())'
                data-config='@json($employeeChart)'
            ></canvas>
        </div>
    </div>

    {{-- Leaderboards --}}
    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        @foreach ([
            'best_quality' => 'بهترین کیفیت تماس',
            'most_analyzed' => 'بیشترین تحلیل',
            'highest_lead' => 'بالاترین کیفیت لید',
            'overall' => 'عملکرد کلی',
        ] as $key => $title)
            <div class="saas-card">
                <h3 class="font-semibold">{{ $title }}</h3>
                <ol class="mt-3 space-y-2">
                    @forelse ($leaderboards[$key] ?? [] as $i => $row)
                        <li>
                            <button
                                type="button"
                                wire:click="drilldown('employee', '{{ $row['id'] }}')"
                                class="flex w-full items-center justify-between rounded-lg bg-zinc-50 px-3 py-2 text-right transition hover:bg-zinc-100 dark:bg-zinc-900 dark:hover:bg-zinc-800"
                            >
                                <span class="flex items-center gap-2">
                                    <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-md bg-zinc-200 text-xs font-bold dark:bg-zinc-700">{{ $i + 1 }}</span>
                                    <x-saas.avatar :name="$row['name']" size="xs" />
                                    <span class="text-sm font-medium">{{ $row['name'] }}</span>
                                </span>
                                <span class="text-sm font-bold text-indigo-600">
                                    {{ $row['composite_score'] ?? $row['average_score'] ?? $row['average_lead_score'] ?? $row['total_analyzed'] }}
                                </span>
                            </button>
                        </li>
                    @empty
                        <li class="text-sm text-zinc-500">داده‌ای وجود ندارد</li>
                    @endforelse
                </ol>
            </div>
        @endforeach
    </div>

    {{-- AI Usage --}}
    <div class="saas-card">
        <h2 class="text-lg font-semibold">مصرف هوش مصنوعی</h2>
        <div class="mt-4 h-64" wire:ignore>
            <canvas id="chart-ai-usage" data-report-chart data-type="line" data-config='@json($aiChart)'></canvas>
        </div>
    </div>

    {{-- Drill-down panel --}}
    @if ($showDrilldown && $drilldownAnalyses)
        <div class="fixed inset-0 z-50 flex items-end justify-center bg-black/40 p-4 sm:items-center" wire:click.self="closeDrilldown">
            <div class="max-h-[80vh] w-full max-w-3xl overflow-auto rounded-lg bg-white p-6 shadow-xl dark:bg-zinc-900">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold">جزئیات</h2>
                    <button type="button" wire:click="closeDrilldown" class="text-zinc-500 hover:text-zinc-800">&times;</button>
                </div>
                <table class="saas-table mt-4">
                    <thead>
                        <tr>
                            <th>تاریخ</th>
                            <th>کارشناس</th>
                            <th>امتیاز</th>
                            <th>لید</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($drilldownAnalyses as $analysis)
                            <tr wire:key="drill-{{ $analysis->id }}">
                                <td>{{ shamsi($analysis->analyzed_at, 'datetime') }}</td>
                                <td>
                                    @if ($analysis->employee)
                                        <x-saas.user-cell :employee="$analysis->employee" avatar-size="xs" />
                                    @else
                                        —
                                    @endif
                                </td>
                                <td>{{ $analysis->score }}</td>
                                <td>{{ $analysis->lead_quality_json['level'] ?? '—' }}</td>
                                <td>
                                    <a href="{{ route('employer.intelligence.show', $analysis) }}" class="text-sm text-indigo-600 hover:underline">مشاهده</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center text-zinc-500">رکوردی یافت نشد</td></tr>
                        @endforelse
                    </tbody>
                </table>
                @if ($drilldownAnalyses->count() >= 20)
                    <p class="mt-3 text-center text-sm text-zinc-500">
                        <a href="{{ route('employer.intelligence.index') }}" class="text-indigo-600 hover:underline">مشاهده همه در هوش تماس</a>
                    </p>
                @endif
            </div>
        </div>
    @endif
</div>

@script
<script>
    $wire.on('report-drilldown', ({ dimension, value }) => {
        $wire.drilldown(dimension, value);
    });
</script>
@endscript
