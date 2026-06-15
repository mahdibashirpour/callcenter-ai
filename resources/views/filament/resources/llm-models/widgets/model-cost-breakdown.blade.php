<x-filament-widgets::widget>
    @php $summary = $this->getSummary(); @endphp

    @if (! empty($summary))
        <x-filament::section>
            <x-slot name="heading">{{ __('filament.cost_estimator.cost_estimates_approximate') }}</x-slot>
            <p class="mb-4 text-sm text-gray-500">
                {{ __('filament.cost_estimator.planning_estimates_only') }}
            </p>

            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <div class="rounded-lg border border-gray-200 p-3 dark:border-gray-700">
                    <p class="text-sm text-gray-500">{{ __('filament.fields.input_per_million') }}</p>
                    <p class="text-lg font-semibold">{{ $this->formatMoney($summary['input_price']) }}</p>
                </div>
                <div class="rounded-lg border border-gray-200 p-3 dark:border-gray-700">
                    <p class="text-sm text-gray-500">{{ __('filament.fields.output_per_million') }}</p>
                    <p class="text-lg font-semibold">{{ $this->formatMoney($summary['output_price']) }}</p>
                </div>
                <div class="rounded-lg border border-gray-200 p-3 dark:border-gray-700">
                    <p class="text-sm text-gray-500">{{ __('filament.fields.avg_cost_per_minute') }}</p>
                    <p class="text-lg font-semibold">{{ $this->formatMoney($summary['cost_per_minute']) }}</p>
                </div>
                <div class="rounded-lg border border-gray-200 p-3 dark:border-gray-700">
                    <p class="text-sm text-gray-500">{{ __('filament.fields.est_cost_per_hour') }}</p>
                    <p class="text-lg font-semibold">{{ $this->formatMoney($summary['cost_per_hour']) }}</p>
                </div>
            </div>

            <div class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ([1 => __('filament.cost_estimator.one_minute'), 10 => __('filament.cost_estimator.ten_minutes'), 30 => __('filament.cost_estimator.thirty_minutes'), 60 => __('filament.cost_estimator.sixty_minutes')] as $minutes => $label)
                    <div class="rounded-md bg-gray-50 p-3 text-sm dark:bg-gray-900">
                        <p class="text-gray-500">{{ $label }}</p>
                        <p class="font-semibold">~{{ $this->formatMoney($summary['duration_estimates'][$minutes]['cost'] ?? 0) }}</p>
                    </div>
                @endforeach
            </div>
        </x-filament::section>
    @endif
</x-filament-widgets::widget>
