<div class="saas-page space-y-8">
    <x-saas.page-header
        title="مشتریان"
        description="پروفایل خودکار مشتریان از روی تماس‌های تحلیل‌شده — تاریخچه و بینش در یک نگاه."
        data-tour="page-header"
    />

    <div class="saas-card" data-tour="customers-search">
        <input wire:model.live.debounce.300ms="search" type="search" placeholder="جستجو در نام یا شماره مشتری..." class="saas-input max-w-md">
    </div>

    <div class="grid gap-4 lg:grid-cols-2" data-tour="customers-grid">
        @forelse ($customers as $customer)
            <x-saas.customer-card
                :customer="$customer"
                :href="route('employee.customers.show', $customer)"
                wire:key="customer-{{ $customer->id }}"
            />
        @empty
            <div class="col-span-full">
                <x-saas.empty-state
                    title="@lang('ui.empty.no_customers.title')"
                    description="@lang('ui.empty.no_customers.description')"
                />
            </div>
        @endforelse
    </div>

    {{ $customers->links() }}
</div>
