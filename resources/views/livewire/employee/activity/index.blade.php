<div class="space-y-8">
    <h1 class="text-3xl font-semibold tracking-tight">فعالیت</h1>
    <div class="grid gap-6 lg:grid-cols-2">
        <div class="saas-card">
            <h2 class="font-semibold">تماس‌های اخیر</h2>
            <div class="mt-4 space-y-3">
                @forelse ($recentCalls as $call)
                    <a href="{{ route('employee.calls.show', $call) }}" class="block text-sm">
                        <span class="font-medium">امتیاز {{ $call->score }}</span>
                        <span class="text-zinc-500"> · {{ shamsi($call->analyzed_at, 'ago') }}</span>
                    </a>
                @empty
                    <p class="text-sm text-zinc-500">تماس اخیری وجود ندارد.</p>
                @endforelse
            </div>
        </div>
        <div class="saas-card">
            <h2 class="font-semibold">بازخورد اخیر</h2>
            <div class="mt-4 space-y-3">
                @forelse ($feedback as $item)
                    <p class="text-sm text-zinc-600 dark:text-zinc-300">{{ Str::limit($item->overall_evaluation ?? $item->summary, 160) }}</p>
                @empty
                    <p class="text-sm text-zinc-500">هنوز بازخوردی ثبت نشده است.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
