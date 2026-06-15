<div class="space-y-8">
    <h1 class="text-3xl font-semibold tracking-tight">عملکرد من</h1>
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-5">
        <x-saas.stat-card label="فعلی" :value="$currentScore ?? '—'" />
        <x-saas.stat-card label="هفتگی" :value="$weeklyScore ?: '—'" />
        <x-saas.stat-card label="ماهانه" :value="$monthlyScore ?: '—'" />
        <x-saas.stat-card label="بهترین" :value="$bestScore ?? '—'" />
        <x-saas.stat-card label="تحلیل‌شده" :value="$totalAnalyzed" />
    </div>
    <div class="saas-card">
        <h2 class="font-semibold">تاریخچه امتیاز</h2>
        <div class="mt-4 flex flex-wrap gap-2">
            @foreach ($trend as $item)
                <span class="saas-badge-success">{{ $item->score }} · {{ shamsi($item->analyzed_at, 'month_day') }}</span>
            @endforeach
        </div>
    </div>
</div>
