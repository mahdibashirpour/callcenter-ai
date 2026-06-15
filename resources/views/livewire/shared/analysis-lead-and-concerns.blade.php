@php
    use App\Support\AnalysisInsightPresenter;

    $leadQuality = $analysis->lead_quality_json ?? null;
    $concerns = $analysis->concerns_json ?? [];
    $buyingSignals = $leadQuality['buying_intent_signals'] ?? [];
    $leadReason = $leadQuality['reason'] ?? null;
@endphp

@if ($leadReason || ! empty($buyingSignals))
    <div class="saas-card">
        <h2 class="text-lg font-semibold">سیگنال‌های لید</h2>
        @if ($leadReason)
            <p class="mt-3 text-sm leading-relaxed text-zinc-600 dark:text-zinc-300">{{ $leadReason }}</p>
        @endif
        @if (! empty($buyingSignals))
            <x-saas.analysis-insight-list
                title="نشانه‌های تمایل به خرید"
                :items="$buyingSignals"
                tone="positive"
                class="mt-4 !border-emerald-200/60 !bg-emerald-50/30 dark:!border-emerald-500/20 dark:!bg-emerald-950/10"
            />
        @endif
    </div>
@endif

@if (! empty($concerns))
    <div class="saas-card">
        <h2 class="text-lg font-semibold">دغدغه‌ها و اعتراضات</h2>
        <div class="mt-4 space-y-3">
            @foreach ($concerns as $concern)
                <div class="rounded-lg border border-zinc-200/80 bg-zinc-50/50 px-4 py-3 dark:border-zinc-800 dark:bg-zinc-900/40">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="saas-badge bg-zinc-100 text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300">
                            {{ AnalysisInsightPresenter::concernTypeLabel($concern['type'] ?? null) }}
                        </span>
                        <span @class(['rounded-md px-2 py-0.5 text-xs font-medium', AnalysisInsightPresenter::severityBadgeClass($concern['severity'] ?? null)])>
                            شدت {{ AnalysisInsightPresenter::severityLabel($concern['severity'] ?? null) }}
                        </span>
                    </div>
                    <p class="mt-2 text-sm leading-relaxed text-zinc-700 dark:text-zinc-300">{{ $concern['text'] ?? '' }}</p>
                </div>
            @endforeach
        </div>
    </div>
@endif
