@php
    $processingJob = $call?->processingJob ?? null;
@endphp

@if ($processingJob)
    <div class="saas-card">
        <div class="flex items-start justify-between gap-3">
            <div>
                <h2 class="text-sm font-semibold uppercase tracking-wider text-zinc-500">پردازش</h2>
                <p class="mt-1 text-sm font-medium text-zinc-900 dark:text-white">
                    {{ $processingJob->status->label() }}
                </p>
                <p class="text-xs text-zinc-500">{{ $processingJob->stage->label() }}</p>
            </div>
            @if ($queueUrl ?? null)
                <a href="{{ $queueUrl }}" class="shrink-0 text-xs font-medium text-indigo-600 hover:text-indigo-800 dark:text-indigo-400">صف</a>
            @endif
        </div>

        <div class="mt-4 flex items-center gap-3">
            <div class="h-1.5 flex-1 overflow-hidden rounded-full bg-zinc-100 dark:bg-zinc-800">
                <div class="h-1.5 rounded-full bg-gradient-to-l from-indigo-500 to-violet-500 transition-all" style="width: {{ $processingJob->progress_percentage }}%"></div>
            </div>
            <span class="text-xs font-semibold tabular-nums text-zinc-600 dark:text-zinc-400">{{ $processingJob->progress_percentage }}%</span>
        </div>

        @if ($processingJob->error_message)
            <p class="mt-3 rounded-lg bg-red-50 px-3 py-2 text-xs text-red-700 dark:bg-red-950/30 dark:text-red-300">
                {{ \App\Support\UserFacingError::processing($processingJob->error_message) }}
            </p>
        @endif
    </div>
@elseif ($call && $call->processing_status)
    <div class="saas-card">
        <h2 class="text-sm font-semibold uppercase tracking-wider text-zinc-500">پردازش</h2>
        <p class="mt-2 text-sm font-medium text-zinc-900 dark:text-white">
            {{ $call->processing_status->label() }}
        </p>
        @if ($call->processing_error)
            <p class="mt-2 rounded-lg bg-red-50 px-3 py-2 text-xs text-red-700 dark:bg-red-950/30 dark:text-red-300">
                {{ \App\Support\UserFacingError::processing($call->processing_error) }}
            </p>
        @endif
    </div>
@endif
