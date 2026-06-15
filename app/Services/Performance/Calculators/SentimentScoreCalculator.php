<?php

namespace App\Services\Performance\Calculators;

use App\Domain\Llm\Enums\AnalysisSentiment;
use App\Models\ConversationAnalysis;
use Illuminate\Support\Collection;

class SentimentScoreCalculator
{
    /** @var array<string, int> */
    private const WEIGHTS = [
        AnalysisSentiment::Positive->value => 100,
        AnalysisSentiment::Mixed->value => 60,
        AnalysisSentiment::Neutral->value => 50,
        AnalysisSentiment::Negative->value => 20,
    ];

    /** @param  Collection<int, ConversationAnalysis>  $analyses */
    public function average(Collection $analyses): float
    {
        if ($analyses->isEmpty()) {
            return 0.0;
        }

        $total = $analyses->sum(
            fn (ConversationAnalysis $analysis) => self::WEIGHTS[$analysis->sentiment->value] ?? 50,
        );

        return round($total / $analyses->count(), 1);
    }
}
