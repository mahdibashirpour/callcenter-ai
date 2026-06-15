<?php

namespace App\Services\Performance\Support;

class ProgressInsightFormatter
{
    /** @return list<string> */
    public function teamInsights(array $deltas, ?array $mostImproved): array
    {
        $insights = [];

        if ($deltas['average_quality_score'] !== null) {
            $insights[] = $this->formatProgressMessage('امتیاز کیفیت مکالمه تیم', $deltas['average_quality_score']);
        }

        if ($deltas['average_lead_score'] !== null) {
            $insights[] = $this->formatProgressMessage('امتیاز کیفیت لید فروش', $deltas['average_lead_score']);
        }

        if ($deltas['average_sentiment'] !== null) {
            $insights[] = $this->formatSentimentProgressMessage($deltas['average_sentiment']);
        }

        if ($mostImproved && ($mostImproved['improvement_percent'] ?? 0) > 0) {
            $insights[] = sprintf(
                '%s بیشترین پیشرفت را با %.1f%% بهبود در امتیاز مکالمه نشان داده است.',
                $mostImproved['name'],
                $mostImproved['improvement_percent'],
            );
        }

        return $insights;
    }

    /** @return list<string> */
    public function employeeInsights(array $deltas): array
    {
        $insights = [];

        if ($deltas['quality_improvement_percent'] !== null) {
            $insights[] = $this->formatProgressMessage('امتیاز کیفیت مکالمه', $deltas['quality_improvement_percent']);
        }

        if ($deltas['lead_improvement_percent'] !== null) {
            $insights[] = $this->formatProgressMessage('امتیاز کیفیت لید فروش', $deltas['lead_improvement_percent']);
        }

        if ($deltas['sentiment_improvement_percent'] !== null) {
            $insights[] = $this->formatSentimentProgressMessage($deltas['sentiment_improvement_percent']);
        }

        return $insights;
    }

    private function formatProgressMessage(string $metric, ?float $delta): string
    {
        if ($delta === null) {
            return sprintf('%s: داده کافی برای مقایسه با دوره قبل وجود ندارد.', $metric);
        }

        if ($delta > 0) {
            return sprintf('%s %.1f%% نسبت به دوره قبل بهبود یافته است.', $metric, $delta);
        }

        if ($delta < 0) {
            return sprintf('%s %.1f%% نسبت به دوره قبل کاهش یافته است.', $metric, abs($delta));
        }

        return sprintf('%s نسبت به دوره قبل ثابت مانده است.', $metric);
    }

    private function formatSentimentProgressMessage(?float $delta): string
    {
        if ($delta === null) {
            return 'شاخص رضایت مشتری: داده کافی برای مقایسه با دوره قبل وجود ندارد.';
        }

        if ($delta > 5) {
            return 'رضایت مشتری از مکالمات به‌طور محسوسی بهبود یافته است.';
        }

        if ($delta < -5) {
            return 'رضایت مشتری از مکالمات نسبت به دوره قبل کاهش یافته است.';
        }

        return 'رضایت مشتری از مکالمات نسبتاً پایدار بوده است.';
    }
}
