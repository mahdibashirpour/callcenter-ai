<x-filament-panels::page>
    @php
        $estimate = $this->getCurrentEstimate();
        $modelSummaries = $this->getModelSummaries();
        $simulation = $this->getSimulationTable();
        $durations = \App\Services\AiCostEstimatorService::SIMULATION_DURATIONS;
        $settings = \App\Models\PlatformAiSettings::current();
    @endphp

    <x-filament::section>
        <x-slot name="heading">{{ __('filament.cost_estimator.important_notice') }}</x-slot>
        <p class="text-sm text-gray-600 dark:text-gray-400">
            {!! __('filament.cost_estimator.notice_body') !!}
        </p>
    </x-filament::section>

    <form wire:submit.prevent class="mt-6">
        {{ $this->form }}
    </form>

    @if (! empty($estimate))
        <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <x-filament::section>
                <p class="text-sm text-gray-500">{{ __('filament.fields.estimated_input_tokens') }}</p>
                <p class="mt-1 text-2xl font-semibold">{{ number_format($estimate['input_tokens']) }}</p>
            </x-filament::section>
            <x-filament::section>
                <p class="text-sm text-gray-500">{{ __('filament.fields.estimated_output_tokens') }}</p>
                <p class="mt-1 text-2xl font-semibold">{{ number_format($estimate['output_tokens']) }}</p>
            </x-filament::section>
            <x-filament::section>
                <p class="text-sm text-gray-500">{{ __('filament.fields.estimated_total_tokens') }}</p>
                <p class="mt-1 text-2xl font-semibold">{{ number_format($estimate['total_tokens']) }}</p>
            </x-filament::section>
            <x-filament::section>
                <p class="text-sm text-gray-500">{{ __('filament.misc.estimated_cost') }}</p>
                <p class="mt-1 text-2xl font-semibold text-primary-600">{{ $this->formatMoney($estimate['cost']) }}</p>
            </x-filament::section>
        </div>
    @endif

    <x-filament::section class="mt-8">
        <x-slot name="heading">{{ __('filament.cost_estimator.estimation_formula') }}</x-slot>
        <div class="grid gap-4 text-sm text-gray-600 dark:text-gray-400 md:grid-cols-2">
            <div>
                <p class="font-medium text-gray-900 dark:text-gray-100">{{ __('filament.fields.input_tokens_label') }}</p>
                <p class="mt-1">{{ __('filament.cost_estimator.input_tokens_formula', ['words' => number_format($settings->estimation_words_per_minute), 'tokens' => $settings->estimation_tokens_per_word]) }}</p>
            </div>
            <div>
                <p class="font-medium text-gray-900 dark:text-gray-100">{{ __('filament.fields.output_tokens_label') }}</p>
                <p class="mt-1">{{ __('filament.cost_estimator.output_tokens_formula') }}</p>
            </div>
        </div>
        <p class="mt-3 text-xs text-gray-500">
            {{ __('filament.misc.configure_ratios') }}
        </p>
    </x-filament::section>

    <x-filament::section class="mt-8">
        <x-slot name="heading">{{ __('filament.cost_estimator.model_cost_reference') }}</x-slot>
        <div class="space-y-4">
            @foreach ($modelSummaries as $entry)
                @php
                    $model = $entry['model'];
                    $summary = $entry['summary'];
                @endphp
                <div class="rounded-lg border border-gray-200 p-4 dark:border-gray-700">
                    <div class="flex flex-wrap items-start justify-between gap-2">
                        <div>
                            <h3 class="text-lg font-semibold">{{ $model->name }}</h3>
                            <p class="text-sm text-gray-500">{{ $model->provider?->name }} · {{ $model->model_key }}</p>
                        </div>
                        <div class="text-right text-sm">
                            <p><span class="text-gray-500">{{ __('filament.misc.per_minute') }}:</span> <strong>{{ $this->formatMoney($summary['cost_per_minute']) }}</strong></p>
                            <p><span class="text-gray-500">{{ __('filament.misc.per_hour') }}:</span> <strong>{{ $this->formatMoney($summary['cost_per_hour']) }}</strong></p>
                        </div>
                    </div>
                    <div class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                        <div class="rounded-md bg-gray-50 p-3 text-sm dark:bg-gray-900">
                            <p class="text-gray-500">{{ __('filament.fields.input_per_million') }}</p>
                            <p class="font-semibold">{{ $this->formatMoney($summary['input_price']) }}</p>
                        </div>
                        <div class="rounded-md bg-gray-50 p-3 text-sm dark:bg-gray-900">
                            <p class="text-gray-500">{{ __('filament.fields.output_per_million') }}</p>
                            <p class="font-semibold">{{ $this->formatMoney($summary['output_price']) }}</p>
                        </div>
                        <div class="rounded-md bg-gray-50 p-3 text-sm dark:bg-gray-900">
                            <p class="text-gray-500">{{ __('filament.fields.one_min_audio') }}</p>
                            <p class="font-semibold">~{{ $this->formatMoney($summary['duration_estimates'][1]['cost'] ?? 0) }}</p>
                        </div>
                        <div class="rounded-md bg-gray-50 p-3 text-sm dark:bg-gray-900">
                            <p class="text-gray-500">{{ __('filament.fields.sixty_min_audio') }}</p>
                            <p class="font-semibold">~{{ $this->formatMoney($summary['duration_estimates'][60]['cost'] ?? 0) }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </x-filament::section>

    <x-filament::section class="mt-8">
        <x-slot name="heading">{{ __('filament.cost_estimator.cost_simulation_all_models') }}</x-slot>
        <p class="mb-4 text-sm text-gray-500">{{ __('filament.cost_estimator.simulation_description', ['type' => $this->getConversationType()->label()]) }}</p>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-200 text-left dark:border-gray-700">
                        <th class="px-3 py-2 font-medium">{{ __('filament.fields.model') }}</th>
                        @foreach ($durations as $minutes)
                            <th class="px-3 py-2 font-medium text-right">{{ $minutes }}m</th>
                        @endforeach
                        <th class="px-3 py-2 font-medium text-right">{{ __('filament.misc.per_hour') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($simulation as $row)
                        <tr class="border-b border-gray-100 dark:border-gray-800">
                            <td class="px-3 py-2 font-medium">{{ $row['model']->name }}</td>
                            @foreach ($durations as $minutes)
                                <td class="px-3 py-2 text-right">{{ $this->formatMoney($row['durations'][$minutes] ?? 0) }}</td>
                            @endforeach
                            <td class="px-3 py-2 text-right font-medium">{{ $this->formatMoney($row['cost_per_hour']) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-filament::section>
</x-filament-panels::page>
