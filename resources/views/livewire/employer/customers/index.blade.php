<div class="space-y-8">
    <div>
        <h1 class="text-3xl font-semibold tracking-tight">مشتریان</h1>
        <p class="mt-2 text-zinc-500">پایگاه هوش مشتری — ساخته‌شده خودکار از تحلیل تماس‌ها.</p>
    </div>

    <div class="saas-card">
        <input wire:model.live.debounce.300ms="search" type="search" placeholder="جستجو بر اساس نام، شرکت یا شماره..." class="saas-input max-w-md">
    </div>

    <div class="grid gap-4">
        @forelse ($customers as $customer)
            <a href="{{ route('employer.customers.show', $customer) }}" class="saas-card block transition hover:border-zinc-300 dark:hover:border-zinc-600" wire:key="customer-{{ $customer->id }}">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="text-lg font-semibold">{{ $customer->displayName() }}</h2>
                        <p class="text-sm text-zinc-500">{{ $customer->company_name ?: '—' }} · {{ $customer->phone_number ?: '—' }}</p>
                    </div>
                    <div class="flex flex-wrap gap-4 text-sm">
                        <span>{{ $customer->total_calls }} تماس</span>
                        <span>لید: {{ $customer->latest_lead_level ?: '—' }}</span>
                        <span>آخرین تماس: {{ shamsi($customer->last_contact_at) }}</span>
                    </div>
                </div>
            </a>
        @empty
            <x-saas.empty-state title="هنوز مشتری ثبت نشده" description="پس از تحلیل تماس‌ها، مشتریان به‌صورت خودکار اینجا نمایش داده می‌شوند." />
        @endforelse
    </div>

    {{ $customers->links() }}
</div>
