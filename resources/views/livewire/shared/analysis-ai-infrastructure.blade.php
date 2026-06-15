@php
    $showAiInfrastructure = \App\Support\AiInfrastructure::isVisible();
@endphp

@if ($showAiInfrastructure)
    @isset($analysis)
        <div class="flex items-start justify-between gap-3">
            <dt class="text-zinc-500">مدل</dt>
            <dd class="text-end font-medium">{{ $analysis->model_name }}</dd>
        </div>
        @if (! empty($analysis->llm_provider))
            <div class="flex items-start justify-between gap-3">
                <dt class="text-zinc-500">ارائه‌دهنده</dt>
                <dd class="text-end font-medium">{{ $analysis->llm_provider }}</dd>
            </div>
        @endif
    @endisset
@else
    <div class="flex items-start justify-between gap-3">
        <dt class="text-zinc-500">هوش مصنوعی</dt>
        <dd class="text-end font-medium">{{ \App\Support\AiInfrastructure::activeLabel() }}</dd>
    </div>
@endif
