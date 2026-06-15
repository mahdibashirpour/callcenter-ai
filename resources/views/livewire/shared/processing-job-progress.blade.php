@if ($job && $job->isActive())
    <div class="mt-3">
        <div class="flex items-center justify-between text-xs text-zinc-500">
            <span>{{ $job->stage->label() }}</span>
            <span>{{ $job->progress_percentage }}%</span>
        </div>
        <div class="mt-1 h-1.5 w-full rounded-full bg-zinc-100 dark:bg-zinc-800">
            <div class="h-1.5 rounded-full bg-indigo-500 transition-all" style="width: {{ $job->progress_percentage }}%"></div>
        </div>
    </div>
@endif
