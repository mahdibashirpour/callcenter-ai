<?php

namespace App\Services\Performance;

use App\DTOs\ReportFilter;
use App\Models\OrganizationUser;
use App\Support\JalaliDate;

class PerformanceExecutiveSummaryService
{
    /**
     * @param  array<string, mixed>  $dashboard
     */
    public function teamSummaryFromDashboard(ReportFilter $filter, array $dashboard): string
    {
        $kpis = $dashboard['kpis'];
        $deltas = $dashboard['kpis_delta'];
        $rankings = $dashboard['rankings'];
        $weaknesses = $dashboard['team_weaknesses'];

        $topImproved = $rankings['most_improved'][0] ?? null;
        $topQuality = $rankings['best_quality'][0] ?? null;
        $topWeakness = $weaknesses[0] ?? null;

        $qualityDelta = $deltas['average_quality_score'];
        $qualityTrend = match (true) {
            $qualityDelta === null => 'داده کافی برای مقایسه امتیاز مکالمه تیم با دوره قبل وجود ندارد',
            $qualityDelta > 0 => sprintf('امتیاز کیفیت مکالمه تیم %.1f%% نسبت به دوره قبل بهبود یافته است', $qualityDelta),
            $qualityDelta < 0 => sprintf('امتیاز کیفیت مکالمه تیم %.1f%% نسبت به دوره قبل کاهش یافته است', abs($qualityDelta)),
            default => 'امتیاز کیفیت مکالمه تیم نسبت به دوره قبل ثابت مانده است',
        };

        $parts = [
            sprintf(
                'در بازه %s تا %s، %d تماس برقرار شد و %d کارشناس فعال در تماس بودند. میانگین امتیاز کیفیت مکالمه تیم %s بود.',
                JalaliDate::date($filter->from),
                JalaliDate::date($filter->to),
                $kpis['total_calls'],
                $kpis['active_employees'],
                $kpis['average_quality_score'] ?: '—',
            ),
            $qualityTrend.'.',
        ];

        if ($topQuality) {
            $parts[] = sprintf(
                '%s بالاترین میانگین امتیاز مکالمه (%s) را داشته است.',
                $topQuality['name'],
                $topQuality['average_score'],
            );
        }

        if ($topImproved && ($topImproved['improvement_percent'] ?? 0) > 0) {
            $parts[] = sprintf(
                '%s بیشترین پیشرفت در کیفیت مکالمه را نشان داده است.',
                $topImproved['name'],
            );
        }

        if ($topWeakness) {
            $parts[] = sprintf(
                'شایع‌ترین ضعف در مکالمات تیم: «%s» (%d مورد).',
                $topWeakness['item'],
                $topWeakness['count'],
            );
        }

        if ($kpis['average_sentiment'] >= 60) {
            $parts[] = 'رضایت کلی مشتریان از مکالمات مثبت ارزیابی شده است.';
        }

        return implode(' ', $parts);
    }

    /**
     * @param  array<string, mixed>  $profile
     */
    public function employeeSummaryFromProfile(OrganizationUser $employee, array $profile): string
    {
        $metrics = $profile['metrics'];
        $deltas = $profile['metrics_delta'];
        $coaching = $profile['coaching'];
        $weaknesses = $profile['improvement_areas'];
        $insights = $profile['progress_insights'];

        $parts = [
            sprintf(
                '%s در این بازه %d تماس برقرار کرده (%d مکالمه تحلیل‌شده) و میانگین امتیاز مکالمه %s بوده است.',
                $employee->full_name,
                $metrics['total_calls'],
                $metrics['total_analyzed'],
                $metrics['average_quality_score'] ?: '—',
            ),
        ];

        if ($insights !== []) {
            $parts[] = $insights[0];
        } elseif ($deltas['quality_improvement_percent'] !== null) {
            $parts[] = sprintf('روند امتیاز مکالمه: %s', $deltas['quality_trend']);
        }

        if ($weaknesses !== []) {
            $parts[] = 'اولویت‌های بهبود: '.implode('، ', array_slice($weaknesses, 0, 3)).'.';
        }

        if ($coaching['training_areas'] !== []) {
            $parts[] = 'پیشنهاد آموزشی: '.$coaching['training_areas'][0].'.';
        }

        return implode(' ', $parts);
    }
}
