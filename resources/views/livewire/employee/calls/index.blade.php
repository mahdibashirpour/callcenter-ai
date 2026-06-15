<div class="space-y-8">
    <h1 class="text-3xl font-semibold tracking-tight">تماس‌های من</h1>
    <div class="grid gap-4">
        @forelse ($calls as $call)
            <a href="{{ route('employee.calls.show', $call) }}" class="saas-card block transition hover:border-zinc-300">
                <div class="flex justify-between">
                    <div>
                        <p class="font-semibold">امتیاز {{ $call->score }}</p>
                        <p class="text-sm text-zinc-500">{{ shamsi($call->analyzed_at, 'datetime') }}</p>
                    </div>
                    <span class="saas-badge-success">{{ $call->sentiment->label() }}</span>
                </div>
            </a>
        @empty
            <x-saas.empty-state title="تماس تحلیل‌شده‌ای وجود ندارد" description="تماس‌های تحلیل‌شده شما اینجا نمایش داده می‌شوند." />
        @endforelse
    </div>
    {{ $calls->links() }}
</div>
