@php
    use App\Support\AgentPerformancePresenter;

    $kpis = $dashboard['kpis'];
    $deltas = $dashboard['kpis_delta'];
    $employees = $dashboard['employees'];
    $qualityTrend = $dashboard['quality_trend'];

    $topCount = collect($employees)->where('tier', 'top')->count();
    $attentionCount = collect($employees)->where('tier', 'attention')->count();

    $qualityChart = [
        'labels' => collect($qualityTrend)->pluck('label')->all(),
        'datasets' => [[
            'label' => 'امتیاز کیفیت مکالمه',
            'data' => collect($qualityTrend)->pluck('avg_score')->all(),
            'borderColor' => 'rgb(16, 185, 129)',
            'backgroundColor' => 'rgba(16, 185, 129, 0.12)',
            'fill' => true,
            'tension' => 0.35,
        ]],
    ];

    $employeeChart = [
        'labels' => collect($employees)->pluck('name')->all(),
        'datasets' => [
            ['label' => 'امتیاز مکالمه', 'data' => collect($employees)->pluck('average_score')->all(), 'backgroundColor' => 'rgba(99, 102, 241, 0.85)', 'borderRadius' => 6],
            ['label' => 'امتیاز لید', 'data' => collect($employees)->pluck('average_lead_score')->all(), 'backgroundColor' => 'rgba(16, 185, 129, 0.75)', 'borderRadius' => 6],
        ],
    ];

    $profileUrl = fn (array $agent) => route('employer.intelligence.performance.show', $agent['id']).'?preset='.$datePreset.'&from='.$customFrom.'&to='.$customTo;
@endphp

<div class="space-y-6" wire:loading.class="opacity-60">
    <x-saas.page-header
        title="عملکرد کارشناسان"
        description="مقایسه، رتبه‌بندی و شناسایی فرصت‌های مربیگری تیم تماس."
    >
        <x-slot:actions>
            <button type="button" wire:click="export('csv')" class="saas-btn-secondary text-sm">CSV</button>
            <button type="button" wire:click="export('xlsx')" class="saas-btn-secondary text-sm">Excel</button>
            <button type="button" wire:click="export('pdf')" class="saas-btn-secondary text-sm">PDF</button>
        </x-slot:actions>
    </x-saas.page-header>

    @include('livewire.employer.intelligence.partials.performance-filters')

    <section class="saas-hero saas-hero--accent">
        <p class="text-xs font-semibold uppercase tracking-wider text-indigo-600 dark:text-indigo-400">جمع‌بندی مدیریتی</p>
        <p class="mt-3 text-base leading-8 text-zinc-700 dark:text-zinc-200">{{ $dashboard['executive_summary'] }}</p>
    </section>

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-saas.stat-card label="کارشناسان فعال" :value="$kpis['active_employees']" />
        <x-saas.stat-card label="تماس‌های تحلیل‌شده" :value="$kpis['total_analyzed']" :trend="$deltas['total_analyzed']" />
        <x-saas.stat-card label="میانگین امتیاز مکالمه" :value="$kpis['average_quality_score'] ?: '—'" :trend="$deltas['average_quality_score']" />
        <x-saas.stat-card label="میانگین رضایت مشتری" :value="$kpis['average_sentiment'] ? $kpis['average_sentiment'].'%' : '—'" :trend="$deltas['average_sentiment']" />
    </div>

    <section class="space-y-4" x-data="{ filter: 'all' }">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-semibold">کارت‌های عملکرد</h2>
                <p class="text-sm text-zinc-500">برای مشاهده جزئیات، روی هر کارشناس کلیک کنید.</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <button type="button" @click="filter = 'all'" :class="filter === 'all' ? 'bg-zinc-900 text-white dark:bg-white dark:text-zinc-900' : 'bg-zinc-100 text-zinc-600'" class="rounded-lg px-3 py-1.5 text-sm font-medium">همه</button>
                <button type="button" @click="filter = 'top'" :class="filter === 'top' ? 'bg-emerald-600 text-white' : 'bg-emerald-50 text-emerald-700'" class="rounded-lg px-3 py-1.5 text-sm font-medium">برترین‌ها ({{ $topCount }})</button>
                <button type="button" @click="filter = 'attention'" :class="filter === 'attention' ? 'bg-amber-600 text-white' : 'bg-amber-50 text-amber-700'" class="rounded-lg px-3 py-1.5 text-sm font-medium">نیازمند توجه ({{ $attentionCount }})</button>
            </div>
        </div>

        <div class="grid gap-4 lg:grid-cols-2">
            @forelse ($employees as $agent)
                <div x-show="filter === 'all' || filter === '{{ $agent['tier'] }}'" x-cloak>
                    <x-saas.agent-performance-card :agent="$agent" :href="$profileUrl($agent)" />
                </div>
            @empty
                <div class="col-span-full rounded-lg border border-dashed border-zinc-300 p-10 text-center text-sm text-zinc-500 dark:border-zinc-700">
                    در این بازه فعالیتی ثبت نشده است.
                </div>
            @endforelse
        </div>
    </section>

    <div class="grid gap-6 lg:grid-cols-2">
        <div class="saas-card">
            <h2 class="text-lg font-semibold">روند کیفیت مکالمه</h2>
            <div class="mt-4 h-64" wire:ignore>
                <canvas id="perf-quality-trend" data-report-chart data-type="line" data-config='@json($qualityChart)'></canvas>
            </div>
        </div>
        <div class="saas-card">
            <h2 class="text-lg font-semibold">مقایسه امتیاز کارشناسان</h2>
            <div class="mt-4 h-64" wire:ignore>
                <canvas id="perf-employee-compare" data-report-chart data-type="bar" data-config='@json(array_merge($employeeChart, ["options" => ["plugins" => ["legend" => ["position" => "bottom"]]]]))'></canvas>
            </div>
        </div>
    </div>

    @if (! empty($dashboard['team_weaknesses']))
        <div class="saas-card">
            <h2 class="text-lg font-semibold">ضعف‌های پرتکرار تیم</h2>
            <div class="mt-4 flex flex-wrap gap-2">
                @foreach (array_slice($dashboard['team_weaknesses'], 0, 8) as $weakness)
                    <span class="rounded-md bg-red-50 px-3 py-1 text-sm text-red-700 dark:bg-red-950/30 dark:text-red-300">{{ $weakness['item'] }} ({{ $weakness['count'] }})</span>
                @endforeach
            </div>
        </div>
    @endif

    <div class="grid gap-4 md:grid-cols-3 xl:grid-cols-5">
        @foreach ([
            'best_quality' => 'بالاترین امتیاز',
            'best_lead' => 'بهترین لید',
            'most_improved' => 'بیشترین پیشرفت',
            'most_calls' => 'بیشترین تماس',
            'best_sentiment' => 'بالاترین رضایت',
        ] as $key => $title)
            <div class="saas-widget">
                <h3 class="text-sm font-semibold text-zinc-700 dark:text-zinc-300">{{ $title }}</h3>
                <ol class="mt-3 space-y-1">
                    @forelse (array_slice($dashboard['rankings'][$key] ?? [], 0, 3) as $i => $row)
                        <li>
                            @php
                                $metricValue = match ($key) {
                                    'most_calls' => $row['total_calls'],
                                    'most_improved' => (($row['improvement_percent'] ?? 0) > 0 ? '+' : '').($row['improvement_percent'] ?? 0).'%',
                                    'best_sentiment' => $row['average_sentiment'] ?? '—',
                                    'best_lead' => $row['average_lead_score'] ?? '—',
                                    default => $row['average_score'] ?? '—',
                                };
                            @endphp
                            <x-saas.agent-rank-row
                                :row="$row"
                                :rank="$i + 1"
                                :href="$profileUrl($row)"
                                :value="$metricValue"
                            />
                        </li>
                    @empty
                        <li class="text-xs text-zinc-500">—</li>
                    @endforelse
                </ol>
            </div>
        @endforeach
    </div>
</div>
