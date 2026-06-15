@php
    $analysisShowRoute = $analysisShowRoute ?? 'employer.intelligence.show';
    $trendLabels = ['improving' => 'رو به بهبود', 'declining' => 'رو به کاهش', 'stable' => 'پایدار'];
    $concernLabels = ['price' => 'قیمت', 'trust' => 'اعتماد', 'timing' => 'زمان‌بندی', 'technical' => 'فنی', 'other' => 'سایر'];
@endphp

<div class="space-y-8">
    <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <p class="text-sm font-medium text-indigo-600">پروفایل مشتری</p>
            <h1 class="text-3xl font-semibold tracking-tight">{{ $customer->displayName() }}</h1>
            <p class="mt-2 text-zinc-500">{{ $customer->company_name ?: 'شرکت ثبت نشده' }}</p>
        </div>
        <a href="{{ $isEmployer ? route('employer.customers.index') : route('employee.customers.index') }}" class="saas-btn-secondary text-sm">بازگشت به لیست</a>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-saas.stat-card label="شماره تماس" :value="$customer->phone_number ?: '—'" />
        <x-saas.stat-card label="ایمیل" :value="$customer->email ?: '—'" />
        <x-saas.stat-card label="سمت" :value="$customer->job_title ?: '—'" />
        <x-saas.stat-card label="اولین تماس" :value="shamsi($customer->first_contact_at)" />
        <x-saas.stat-card label="آخرین تماس" :value="shamsi($customer->last_contact_at)" />
        <x-saas.stat-card label="کل تماس‌ها" :value="$customer->total_calls" />
        <x-saas.stat-card label="تماس‌های پاسخ‌داده" :value="$customer->total_answered_calls" />
        <x-saas.stat-card label="امتیاز لید" :value="$customer->latest_lead_score ? $customer->latest_lead_score.' ('.($customer->latest_lead_level ?? '—').')' : '—'" />
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="saas-card lg:col-span-2">
            <h2 class="text-lg font-semibold">هوش مشتری</h2>
            <div class="mt-4 grid gap-4 sm:grid-cols-2">
                <div>
                    <p class="text-sm text-zinc-500">تمایل به خرید</p>
                    <p class="font-medium">{{ $customer->purchase_intent ?: '—' }}</p>
                </div>
                <div>
                    <p class="text-sm text-zinc-500">روند مکالمات</p>
                    <p class="font-medium">{{ $trendLabels[$customer->conversation_trend] ?? '—' }}</p>
                </div>
                <div class="sm:col-span-2">
                    <p class="text-sm text-zinc-500">اقدام پیشنهادی بعدی</p>
                    <p class="font-medium">{{ $customer->recommended_next_action ?: '—' }}</p>
                </div>
            </div>
            @if (! empty($customer->common_concerns_json))
                <h3 class="mt-6 font-medium">نگرانی‌های رایج</h3>
                <div class="mt-3 flex flex-wrap gap-2">
                    @foreach ($customer->common_concerns_json as $concern)
                        <span class="saas-badge">{{ $concernLabels[$concern['type']] ?? $concern['type'] }} ({{ $concern['count'] }})</span>
                    @endforeach
                </div>
            @endif
        </div>
        <div class="saas-card">
            <h2 class="text-lg font-semibold">کارشناسان مرتبط</h2>
            <ul class="mt-4 space-y-2">
                @forelse ($employees as $employee)
                    <li>
                        <x-saas.user-cell :employee="$employee" :subtitle="$employee->department" avatar-size="xs" />
                    </li>
                @empty
                    <li class="text-sm text-zinc-500">هنوز کارشناسی اختصاص نیافته</li>
                @endforelse
            </ul>
        </div>
    </div>

    <div class="saas-card">
        <h2 class="text-lg font-semibold">اقدامات بعدی</h2>
        <ul class="mt-4 space-y-2 text-sm text-zinc-700 dark:text-zinc-300">
            @forelse ($nextActions as $action)
                <li>• {{ $action }}</li>
            @empty
                <li class="text-zinc-500">اقدام بعدی ثبت نشده است.</li>
            @endforelse
        </ul>
    </div>

    <div class="saas-card">
        <h2 class="text-lg font-semibold">تاریخچه تماس‌ها</h2>
        <div class="mt-4 space-y-4">
            @forelse ($timeline as $item)
                @php
                    $canViewPerformance = $isEmployer || ($viewerMembershipId && (int) $item['employee_id'] === (int) $viewerMembershipId);
                @endphp
                <div class="rounded-lg border border-zinc-200 p-4 dark:border-zinc-800" wire:key="timeline-{{ $item['call_id'] }}">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                        <div>
                            <p class="font-medium">{{ $item['date'] ?? '—' }}</p>
                            <p class="text-sm text-zinc-500">{{ $item['employee_name'] }} · {{ $item['duration_label'] }}</p>
                        </div>
                        <div class="flex flex-wrap gap-3 text-sm">
                            @if ($canViewPerformance && $item['score'])
                                <span class="font-bold text-emerald-600">امتیاز {{ $item['score'] }}</span>
                            @endif
                            <span>لید: {{ $item['lead_level'] ?? '—' }}</span>
                            <span>احساس: {{ $item['sentiment'] ?? '—' }}</span>
                        </div>
                    </div>
                    @if ($item['summary'])
                        <p class="mt-3 text-sm leading-relaxed text-zinc-600 dark:text-zinc-300">{{ \Illuminate\Support\Str::limit($item['summary'], 280) }}</p>
                    @endif
                    @if (! empty($item['concerns']))
                        <div class="mt-3 flex flex-wrap gap-2">
                            @foreach ($item['concerns'] as $concern)
                                <span class="saas-badge">{{ is_array($concern) ? ($concern['text'] ?? '') : $concern }}</span>
                            @endforeach
                        </div>
                    @endif
                    @if (! empty($item['next_actions']))
                        <div class="mt-3">
                            <p class="text-xs font-medium text-zinc-500">اقدامات بعدی</p>
                            <ul class="mt-1 space-y-1 text-sm">
                                @foreach ($item['next_actions'] as $action)
                                    <li>• {{ is_string($action) ? $action : ($action['action'] ?? '') }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    @if ($item['analysis_id'])
                        <div class="mt-4">
                            <a href="{{ route($analysisShowRoute, $item['analysis_id']) }}" class="text-sm font-medium text-indigo-600 hover:underline">
                                {{ $canViewPerformance ? 'مشاهده تحلیل کامل' : 'مشاهده جزئیات مشتری' }}
                            </a>
                        </div>
                    @endif
                </div>
            @empty
                <x-saas.empty-state title="تماسی ثبت نشده" description="تماس‌های این مشتری پس از تحلیل اینجا نمایش داده می‌شوند." />
            @endforelse
        </div>
    </div>
</div>
