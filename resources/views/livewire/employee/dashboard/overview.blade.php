<div class="saas-page">
    <section class="saas-hero">
        <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
            <div class="flex items-center gap-5">
                <x-saas.avatar :employee="$membership" size="xl" ring />
                <div>
                    <p class="text-sm font-medium uppercase tracking-wider text-indigo-600">داشبورد عملکرد</p>
                    <h1 class="text-3xl font-bold tracking-tight">خوش آمدید، {{ $membership->first_name }}</h1>
                    <p class="mt-2 text-zinc-500">مرکز فرمان شخصی شما برای رشد و اقدام.</p>
                </div>
            </div>
            <div class="flex items-center gap-3 rounded-md bg-gradient-to-r from-indigo-600 to-violet-600 px-5 py-3 text-white shadow-sm">
            <div>
                <p class="text-xs uppercase opacity-80">امتیاز عملکرد</p>
                <p class="text-3xl font-bold">{{ $cockpit['performance_score'] ?: '—' }}</p>
            </div>
            @if ($cockpit['weekly_delta'] > 0)
                <span class="rounded-md bg-white/20 px-2 py-1 text-xs">+{{ $cockpit['weekly_delta'] }} این هفته</span>
            @endif
        </div>
        </div>
    </section>

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-saas.stat-card label="پیشرفت هفتگی" :value="$cockpit['weekly_progress'] ?: '—'" :hint="($cockpit['weekly_delta'] >= 0 ? '+' : '').$cockpit['weekly_delta'].' نسبت به هفته قبل'" />
        <x-saas.stat-card label="پیشرفت ماهانه" :value="$cockpit['monthly_progress'] ?: '—'" :hint="($cockpit['monthly_delta'] >= 0 ? '+' : '').$cockpit['monthly_delta'].' نسبت به ماه قبل'" />
        <x-saas.stat-card label="تعداد تماس" :value="$cockpit['call_count']" :hint="$cockpit['analyzed_count'].' تحلیل‌شده'" />
        <x-saas.stat-card label="میانگین امتیاز تماس" :value="$cockpit['average_call_score'] ?: '—'" />
        <x-saas.stat-card label="رضایت مشتری" :value="$cockpit['customer_satisfaction'] ? $cockpit['customer_satisfaction'].'%' : '—'" />
        <x-saas.stat-card label="روند بهبود" :value="($cockpit['monthly_delta'] >= 0 ? '+' : '').$cockpit['monthly_delta']" hint="تغییر امتیاز ماهانه" />
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        <div class="saas-card">
            <h2 class="text-lg font-semibold">روند عملکرد</h2>
            <p class="text-sm text-zinc-500">میانگین امتیاز روزانه</p>
            <div class="mt-4 space-y-2">
                @php $max = max(1, collect($cockpit['score_trend'])->max('avg_score') ?? 1); @endphp
                @forelse (array_slice($cockpit['score_trend'], -10) as $point)
                    <div>
                        <div class="mb-1 flex justify-between text-xs text-zinc-500">
                            <span>{{ $point['period'] }}</span>
                            <span>{{ $point['avg_score'] }} ({{ $point['count'] }} تماس)</span>
                        </div>
                        <div class="h-2 rounded-full bg-zinc-100 dark:bg-zinc-800">
                            <div class="h-2 rounded-full bg-indigo-500" style="width: {{ min(100, ($point['avg_score'] / $max) * 100) }}%"></div>
                        </div>
                    </div>
                @empty
                    <x-saas.empty-state title="هنوز داده روندی وجود ندارد" description="با تحلیل تماس‌ها، امتیازها اینجا نمایش داده می‌شوند." />
                @endforelse
            </div>
        </div>

        <div class="saas-card">
            <h2 class="text-lg font-semibold">روند رضایت مشتری</h2>
            <div class="mt-4 space-y-2">
                @php $maxSat = max(1, collect($cockpit['sentiment_trend'])->max('satisfaction') ?? 1); @endphp
                @forelse (array_slice($cockpit['sentiment_trend'], -10) as $point)
                    <div>
                        <div class="mb-1 flex justify-between text-xs text-zinc-500">
                            <span>{{ $point['period'] }}</span>
                            <span>{{ $point['satisfaction'] }}%</span>
                        </div>
                        <div class="h-2 rounded-full bg-zinc-100 dark:bg-zinc-800">
                            <div class="h-2 rounded-full bg-emerald-500" style="width: {{ min(100, ($point['satisfaction'] / $maxSat) * 100) }}%"></div>
                        </div>
                    </div>
                @empty
                    <x-saas.empty-state title="داده رضایت وجود ندارد" description="روند احساسات به مرور زمان شکل می‌گیرد." />
                @endforelse
            </div>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="saas-card lg:col-span-2">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold">عملکرد من</h2>
                <a href="{{ route('employee.performance') }}" class="text-sm text-indigo-600 hover:underline">مشاهده جزئیات</a>
            </div>
            <div class="mt-4 grid gap-4 sm:grid-cols-3">
                <div class="rounded-lg bg-zinc-50 p-4 dark:bg-zinc-900">
                    <p class="text-sm text-zinc-500">بهترین اخیر</p>
                    <p class="text-2xl font-bold">{{ collect($cockpit['score_trend'])->max('avg_score') ?: '—' }}</p>
                </div>
                <div class="rounded-lg bg-zinc-50 p-4 dark:bg-zinc-900">
                    <p class="text-sm text-zinc-500">تماس‌های تحلیل‌شده</p>
                    <p class="text-2xl font-bold">{{ $cockpit['analyzed_count'] }}</p>
                </div>
                <div class="rounded-lg bg-zinc-50 p-4 dark:bg-zinc-900">
                    <p class="text-sm text-zinc-500">توکن مصرف‌شده</p>
                    <p class="text-2xl font-bold">{{ number_format($cockpit['token_usage']) }}</p>
                </div>
            </div>
        </div>

        <div class="saas-card">
            <h2 class="text-lg font-semibold">دستاوردهای من</h2>
            <div class="mt-4 space-y-3">
                @foreach ($achievements as $badge)
                    <div class="flex items-start gap-3 rounded-lg border border-amber-100 bg-amber-50/50 px-3 py-2 dark:border-amber-900/30 dark:bg-amber-950/20">
                        <span class="text-lg">🏆</span>
                        <div>
                            <p class="font-medium text-sm">{{ $badge['title'] }}</p>
                            <p class="text-xs text-zinc-500">{{ $badge['description'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        <div class="saas-card">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold">تماس‌های من</h2>
                <a href="{{ route('employee.calls') }}" class="text-sm text-indigo-600 hover:underline">همه تماس‌ها</a>
            </div>
            <div class="mt-4 space-y-3">
                @forelse ($recentCalls as $call)
                    <a href="{{ route('employee.calls.show', $call['id']) }}" class="block rounded-lg border border-zinc-200 px-4 py-3 transition hover:bg-zinc-50 dark:border-zinc-800 dark:hover:bg-zinc-800/50">
                        <div class="flex justify-between">
                            <span class="font-medium">امتیاز {{ $call['score'] }}</span>
                            <span class="text-sm text-zinc-500">{{ $call['date'] }}</span>
                        </div>
                        <p class="mt-1 text-sm text-zinc-500">{{ Str::limit($call['summary'], 100) }}</p>
                    </a>
                @empty
                    <x-saas.empty-state title="هنوز تماسی نیست" />
                @endforelse
            </div>
        </div>

        <div class="saas-card">
            <h2 class="text-lg font-semibold">پیگیری‌های من</h2>
            <ul class="mt-4 space-y-2">
                @forelse ($followUps as $item)
                    <li class="flex items-start gap-2 rounded-lg bg-zinc-50 px-3 py-2 text-sm dark:bg-zinc-900">
                        <span class="mt-1 h-2 w-2 shrink-0 rounded-full bg-violet-500"></span>
                        <span>{{ $item['action'] }} <span class="text-zinc-400">· {{ $item['date'] }}</span></span>
                    </li>
                @empty
                    <x-saas.empty-state title="پیگیری‌ای وجود ندارد" description="پس از تحلیل تماس‌ها، هوش مصنوعی اقدامات را پیشنهاد می‌دهد." />
                @endforelse
            </ul>
        </div>
    </div>

    <div class="saas-card">
        <h2 class="text-lg font-semibold">پیشنهادهای من</h2>
        <p class="mt-1 text-sm text-zinc-500">حوزه‌های تمرکز مربیگری بر اساس گفتگوهای اخیر شما</p>
        <div class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
            @forelse ($recommendations as $rec)
                <div class="rounded-lg border border-zinc-200 p-4 dark:border-zinc-800">
                    <span class="rounded-md px-2 py-0.5 text-xs {{ $rec['priority'] === 'high' ? 'bg-red-100 text-red-700' : ($rec['priority'] === 'medium' ? 'bg-amber-100 text-amber-700' : 'bg-zinc-100 text-zinc-600') }}">{{ match($rec['priority']) { 'high' => 'بالا', 'medium' => 'متوسط', 'low' => 'پایین', default => $rec['priority'] } }}</span>
                    <p class="mt-2 font-medium">{{ $rec['topic'] }}</p>
                    <p class="mt-1 text-sm text-zinc-500">{{ $rec['tip'] }}</p>
                </div>
            @empty
                <x-saas.empty-state title="هنوز پیشنهادی نیست" description="برای دریافت بینش‌های مربیگری به تحلیل تماس‌ها ادامه دهید." />
            @endforelse
        </div>
    </div>
</div>
