<?php

namespace App\Services\Reports;

use App\DTOs\ReportFilter;
use App\Support\JalaliDate;
use Illuminate\Support\Facades\Cache;

class ReportExecutiveSummaryService
{
    public function generate(ReportFilter $filter, EmployerReportsAnalytics $analytics, bool $useCache = true): string
    {
        $builder = function () use ($filter, $analytics) {
                $kpis = $analytics->kpis($filter);
                $deltas = $analytics->kpiDeltas($filter);
                $leaderboards = $analytics->leaderboards($filter);
                $concerns = app(LeadConcernsAnalytics::class)->concernsByType($filter);

                $topPerformer = $leaderboards['best_quality'][0] ?? null;
                $topConcern = collect($concerns)->sortByDesc('count')->first();

                $qualityDelta = $deltas['average_quality_score'];
                $qualityTrend = match (true) {
                    $qualityDelta === null => 'داده کافی برای مقایسه با دوره قبل وجود ندارد',
                    $qualityDelta > 0 => sprintf('کیفیت تماس %.1f%% نسبت به دوره قبل بهبود یافته است', $qualityDelta),
                    $qualityDelta < 0 => sprintf('کیفیت تماس %.1f%% نسبت به دوره قبل کاهش یافته است', abs($qualityDelta)),
                    default => 'کیفیت تماس نسبت به دوره قبل ثابت مانده است',
                };

                $parts = [
                    sprintf(
                        'در بازه %s تا %s، %d تماس تحلیل شد و میانگین امتیاز کیفیت %s بود.',
                        JalaliDate::date($filter->from),
                        JalaliDate::date($filter->to),
                        $kpis['total_analyzed'],
                        $kpis['average_quality_score'] ?: '—',
                    ),
                    $qualityTrend.'.',
                ];

                if ($topPerformer) {
                    $parts[] = sprintf(
                        '%s با میانگین امتیاز %s بالاترین کیفیت تماس را داشته است.',
                        $topPerformer['name'],
                        $topPerformer['average_score'],
                    );
                }

                if ($kpis['high_quality_leads'] > 0) {
                    $parts[] = sprintf(
                        '%d لید با کیفیت بالا شناسایی شد (از %d لید کل).',
                        $kpis['high_quality_leads'],
                        $kpis['total_leads'],
                    );
                }

                if ($topConcern && $topConcern['count'] > 0) {
                    $parts[] = sprintf(
                        'رایج‌ترین نگرانی مشتریان مربوط به «%s» بود (%d مورد).',
                        $topConcern['label'],
                        $topConcern['count'],
                    );
                }

                if ($kpis['total_concerns'] > 0) {
                    $parts[] = sprintf(
                        'در مجموع %d نگرانی مشتری در مکالمات ثبت شد.',
                        $kpis['total_concerns'],
                    );
                }

                return implode(' ', $parts);
            };

        if (! $useCache) {
            return $builder();
        }

        return Cache::remember(
            'employer_reports:summary:'.$filter->cacheKey(),
            3600,
            $builder,
        );
    }
}
