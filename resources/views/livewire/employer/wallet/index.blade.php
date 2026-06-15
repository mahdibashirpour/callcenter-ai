<div class="space-y-8">
    <div>
        <h1 class="text-3xl font-semibold tracking-tight">کیف پول هوش مصنوعی</h1>
        <p class="mt-2 text-zinc-500">نظارت بر اعتبار، مصرف و تحلیل‌های هوش مصنوعی.</p>
    </div>

    @if ($lowBalance)
        <div class="rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900 dark:border-amber-900/50 dark:bg-amber-950/40 dark:text-amber-100">
            <strong>هشدار موجودی کم.</strong> موجودی کیف پول هوش مصنوعی شما در حال اتمام است. لطفاً قبل از شروع تحلیل‌های جدید با مدیر خود برای شارژ اعتبار تماس بگیرید.
        </div>
    @endif

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-5">
        <x-saas.stat-card
            label="اعتبار باقی‌مانده"
            :value="\Illuminate\Support\Number::currency($overview['balance'], $overview['currency'])"
            :hint="$overview['currency']"
        />
        <x-saas.stat-card
            label="تحلیل‌های این ماه"
            :value="number_format($overview['month_analyses'])"
        />
        <x-saas.stat-card
            label="هزینه مصرف‌شده"
            :value="\Illuminate\Support\Number::currency($overview['month_cost'], $overview['currency'])"
            hint="این ماه"
        />
        <x-saas.stat-card
            label="میزان مصرف"
            :value="number_format($overview['month_tokens'])"
            hint="توکن · این ماه"
        />
        @if ($showAiInfrastructure)
            <x-saas.stat-card
                label="مدل فعلی"
                :value="$overview['model']['model_name'] ?? '—'"
            />
            <x-saas.stat-card
                label="ارائه‌دهنده فعلی"
                :value="$overview['model']['provider_name'] ?? '—'"
            />
        @else
            <x-saas.stat-card
                label="هوش مصنوعی"
                value="فعال"
            />
            <x-saas.stat-card
                label="آخرین استفاده"
                :value="shamsi($overview['last_analysis_at'], 'datetime')"
            />
        @endif
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        <div class="saas-card">
            <h2 class="text-lg font-semibold">مصرف روزانه توکن</h2>
            <p class="mt-1 text-sm text-zinc-500">۳۰ روز گذشته</p>
            <div class="mt-6 space-y-3">
                @php $maxTokens = max(1, collect($dailyTrend)->max('total_tokens') ?? 1); @endphp
                @forelse (array_slice($dailyTrend, -14) as $point)
                    <div>
                        <div class="mb-1 flex justify-between text-xs text-zinc-500">
                            <span>{{ $point['period'] }}</span>
                            <span>{{ number_format($point['total_tokens']) }} توکن</span>
                        </div>
                        <div class="h-2 rounded-full bg-zinc-100 dark:bg-zinc-800">
                            <div class="h-2 rounded-full bg-indigo-500" style="width: {{ min(100, ($point['total_tokens'] / $maxTokens) * 100) }}%"></div>
                        </div>
                    </div>
                @empty
                    <x-saas.empty-state title="هنوز مصرفی ثبت نشده" description="مصرف توکن پس از تحلیل‌های هوش مصنوعی نمایش داده می‌شود." />
                @endforelse
            </div>
        </div>

        <div class="saas-card">
            <h2 class="text-lg font-semibold">هزینه روزانه</h2>
            <p class="mt-1 text-sm text-zinc-500">۳۰ روز گذشته</p>
            <div class="mt-6 space-y-3">
                @php $maxCost = max(0.01, collect($dailyTrend)->max('total_cost') ?? 0.01); @endphp
                @forelse (array_slice($dailyTrend, -14) as $point)
                    <div>
                        <div class="mb-1 flex justify-between text-xs text-zinc-500">
                            <span>{{ $point['period'] }}</span>
                            <span>{{ \Illuminate\Support\Number::currency($point['total_cost'], $overview['currency']) }}</span>
                        </div>
                        <div class="h-2 rounded-full bg-zinc-100 dark:bg-zinc-800">
                            <div class="h-2 rounded-full bg-emerald-500" style="width: {{ min(100, ($point['total_cost'] / $maxCost) * 100) }}%"></div>
                        </div>
                    </div>
                @empty
                    <x-saas.empty-state title="هنوز داده هزینه‌ای وجود ندارد" description="هزینه‌ها پس از تکمیل تحلیل‌ها نمایش داده می‌شوند." />
                @endforelse
            </div>
        </div>
    </div>

    <div class="saas-card">
        <h2 class="text-lg font-semibold">هزینه ماهانه</h2>
        <p class="mt-1 text-sm text-zinc-500">۶ ماه گذشته</p>
        <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @php $maxMonthly = max(0.01, collect($monthlyTrend)->max('total_cost') ?? 0.01); @endphp
            @forelse (array_slice($monthlyTrend, -6) as $point)
                <div class="rounded-lg border border-zinc-100 p-4 dark:border-zinc-800">
                    <p class="text-sm text-zinc-500">{{ $point['period'] }}</p>
                    <p class="mt-1 text-2xl font-semibold">{{ \Illuminate\Support\Number::currency($point['total_cost'], $overview['currency']) }}</p>
                    <p class="mt-1 text-xs text-zinc-400">{{ number_format($point['total_tokens']) }} توکن · {{ $point['analyses_count'] }} تحلیل</p>
                    <div class="mt-3 h-2 rounded-full bg-zinc-100 dark:bg-zinc-800">
                        <div class="h-2 rounded-full bg-violet-500" style="width: {{ min(100, ($point['total_cost'] / $maxMonthly) * 100) }}%"></div>
                    </div>
                </div>
            @empty
                <x-saas.empty-state title="هنوز داده ماهانه‌ای وجود ندارد" description="خلاصه‌های ماهانه به مرور زمان شکل می‌گیرند." />
            @endforelse
        </div>
    </div>

    <div class="saas-card">
        <h2 class="text-lg font-semibold">نرخ مصرف توکن</h2>
        @if ($showAiInfrastructure)
            <p class="mt-2 text-sm text-zinc-500">
                مدل اختصاص‌یافته: <strong>{{ $overview['model']['model_name'] ?? '—' }}</strong>
                ({{ $overview['model']['model_key'] ?? '—' }})
            </p>
        @else
            <p class="mt-2 text-sm text-zinc-500">نرخ مصرف بر اساس تحلیل‌های انجام‌شده محاسبه می‌شود.</p>
        @endif
        <div class="mt-4 grid gap-4 sm:grid-cols-2">
            <div class="rounded-lg bg-zinc-50 p-4 dark:bg-zinc-900">
                <p class="text-sm text-zinc-500">توکن‌های ورودی</p>
                <p class="text-xl font-semibold">{{ \Illuminate\Support\Number::currency($overview['model']['input_price_per_million'] ?? 0, $overview['currency']) }} / 1M</p>
            </div>
            <div class="rounded-lg bg-zinc-50 p-4 dark:bg-zinc-900">
                <p class="text-sm text-zinc-500">توکن‌های خروجی</p>
                <p class="text-xl font-semibold">{{ \Illuminate\Support\Number::currency($overview['model']['output_price_per_million'] ?? 0, $overview['currency']) }} / 1M</p>
            </div>
        </div>
    </div>
</div>
