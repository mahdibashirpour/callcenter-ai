<?php

namespace App\Services;

use App\Models\ConversationAnalysis;
use App\Models\OrganizationActivity;
use App\Services\Reports\OrganizationCallMetrics;
use App\Support\JalaliDate;

class EmployerDashboardAnalytics
{
    public function __construct(
        private int $organizationId,
        private ?OrganizationCallMetrics $callMetrics = null,
    ) {}

    public static function forOrganization(int $organizationId): self
    {
        return new self($organizationId);
    }

    public function cockpit(): array
    {
        $callMetrics = $this->callMetrics ?? app(OrganizationCallMetrics::class);
        $ai = AiPerformanceAnalytics::forOrganization($this->organizationId);
        $overview = $ai->overview();
        $insights = $ai->organizationInsights();

        $followUps = ConversationAnalysis::query()
            ->where('organization_id', $this->organizationId)
            ->whereNotNull('next_actions_json')
            ->whereMonth('analyzed_at', now()->month)
            ->count();

        return [
            'team_average_score' => $insights['team_average'],
            'top_performers' => $insights['top_performers'],
            'needs_attention' => $insights['lowest_performers'],
            'coaching_opportunities' => $insights['coaching_opportunities'],
            'calls_today' => $callMetrics->countToday($this->organizationId),
            'calls_month' => $callMetrics->countThisMonth($this->organizationId),
            'follow_ups_created' => $followUps,
            'total_analyzed' => $overview['total_analyzed'],
            'average_sentiment' => $overview['average_sentiment'],
            'monthly_improvement' => $overview['monthly_improvement'],
        ];
    }

    public function dailyTrend(int $days = 14): array
    {
        return AiPerformanceAnalytics::forOrganization($this->organizationId)
            ->scoreTrend('day', now()->subDays($days), now());
    }

    public function monthlyTrend(int $months = 6): array
    {
        return AiPerformanceAnalytics::forOrganization($this->organizationId)
            ->scoreTrend('month', now()->subMonths($months), now());
    }

    public function sentimentTrend(int $days = 14): array
    {
        $weights = [
            'positive' => 100, 'mixed' => 60, 'neutral' => 50, 'negative' => 20,
        ];

        $grouped = ConversationAnalysis::query()
            ->where('organization_id', $this->organizationId)
            ->where('analyzed_at', '>=', now()->subDays($days))
            ->orderBy('analyzed_at')
            ->get()
            ->groupBy(fn ($a) => $a->analyzed_at->format('Y-m-d'));

        return $grouped->map(fn ($items, $date) => [
            'period' => $date,
            'sentiment' => round($items->avg(fn ($a) => $weights[$a->sentiment->value] ?? 50), 1),
            'count' => $items->count(),
        ])->values()->all();
    }

    public function teamRanking(): array
    {
        return AiPerformanceAnalytics::forOrganization($this->organizationId)
            ->employeePerformance()
            ->sortByDesc('average_score')
            ->values()
            ->take(8)
            ->all();
    }

    public function activityFeed(int $limit = 10): array
    {
        return OrganizationActivity::query()
            ->where('organization_id', $this->organizationId)
            ->latest()
            ->limit($limit)
            ->get()
            ->map(fn ($a) => [
                'title' => $a->title,
                'description' => $a->description,
                'time' => JalaliDate::ago($a->created_at),
            ])
            ->all();
    }
}
