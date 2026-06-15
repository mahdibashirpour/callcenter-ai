<div class="space-y-8">
    <h1 class="text-3xl font-semibold tracking-tight">تحلیل‌ها</h1>
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-saas.stat-card label="میانگین امتیاز تیم" :value="$overview['average_score']" />
        <x-saas.stat-card label="کل توکن‌ها" :value="number_format($overview['total_tokens'])" />
        <x-saas.stat-card label="هزینه AI" :value="\App\Models\PlatformAiSettings::formatMoney($overview['total_cost'])" />
        <x-saas.stat-card label="گفتگوها" :value="$overview['total_analyzed']" />
    </div>
    <div class="grid gap-6 lg:grid-cols-2">
        <div class="saas-card">
            <h2 class="font-semibold">برترین‌ها</h2>
            <ul class="mt-4 space-y-2">
                @foreach ($insights['top_performers'] as $p)
                    <li class="flex justify-between text-sm"><span>{{ $p['name'] }}</span><span class="font-medium">{{ $p['average_score'] }}</span></li>
                @endforeach
            </ul>
        </div>
        <div class="saas-card">
            <h2 class="font-semibold">فرصت‌های مربیگری</h2>
            <ul class="mt-4 space-y-2">
                @foreach ($insights['coaching_opportunities'] as $item)
                    <li class="text-sm">{{ $item['weakness'] }} <span class="text-zinc-400">({{ $item['count'] }})</span></li>
                @endforeach
            </ul>
        </div>
    </div>
</div>
