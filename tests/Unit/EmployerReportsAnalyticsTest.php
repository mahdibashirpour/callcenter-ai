<?php

namespace Tests\Unit;

use App\Domain\Call\Enums\ConversationSource;
use App\Domain\Llm\Enums\AnalysisSentiment;
use App\DTOs\ReportFilter;
use App\Enums\ReportDatePreset;
use App\Models\ConversationAnalysis;
use App\Models\Organization;
use App\Models\OrganizationUser;
use App\Models\User;
use App\Services\Reports\EmployerReportsAnalytics;
use App\Services\Reports\LeadConcernsAnalytics;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmployerReportsAnalyticsTest extends TestCase
{
    use RefreshDatabase;

    public function test_kpis_aggregate_lead_quality_and_concerns(): void
    {
        $organization = Organization::factory()->create();
        $user = User::factory()->create();
        $employee = OrganizationUser::query()->create([
            'organization_id' => $organization->id,
            'user_id' => $user->id,
            'first_name' => 'Ali',
            'last_name' => 'Test',
            'is_active' => true,
        ]);

        ConversationAnalysis::query()->create($this->analysisAttributes($organization->id, [
            'organization_user_id' => $employee->id,
            'score' => 80,
            'cost' => 0.5,
            'total_tokens' => 100,
            'lead_quality_json' => ['score' => 90, 'level' => 'high', 'reason' => 'test'],
            'concerns_json' => [
                ['type' => 'price', 'text' => 'گران است', 'severity' => 'high'],
            ],
        ]));

        $filter = ReportFilter::make($organization->id, ReportDatePreset::Last30);
        $kpis = app(EmployerReportsAnalytics::class)->kpis($filter);

        $this->assertSame(1, $kpis['total_analyzed']);
        $this->assertSame(80.0, $kpis['average_quality_score']);
        $this->assertSame(1, $kpis['high_quality_leads']);
        $this->assertSame(1, $kpis['total_concerns']);
    }

    public function test_concerns_breakdown_groups_by_type(): void
    {
        $organization = Organization::factory()->create();

        ConversationAnalysis::query()->create($this->analysisAttributes($organization->id, [
            'score' => 70,
            'sentiment' => AnalysisSentiment::Neutral,
            'concerns_json' => [
                ['type' => 'price', 'text' => 'a', 'severity' => 'low'],
                ['type' => 'trust', 'text' => 'b', 'severity' => 'medium'],
            ],
        ]));

        $filter = ReportFilter::make($organization->id, ReportDatePreset::Last30);
        $breakdown = app(LeadConcernsAnalytics::class)->concernsByType($filter);

        $this->assertSame(1, collect($breakdown)->firstWhere('type', 'price')['count']);
        $this->assertSame(1, collect($breakdown)->firstWhere('type', 'trust')['count']);
    }

    public function test_report_filter_presets_resolve_dates(): void
    {
        [$from, $to] = ReportDatePreset::Last7->resolve();

        $this->assertTrue($from->lessThanOrEqualTo(now()));
        $this->assertTrue($to->greaterThanOrEqualTo($from));
    }

    /** @param  array<string, mixed>  $overrides */
    private function analysisAttributes(int $organizationId, array $overrides = []): array
    {
        return array_merge([
            'organization_id' => $organizationId,
            'source' => ConversationSource::ManualUpload,
            'llm_provider' => 'openai',
            'model_name' => 'gpt-4o-mini',
            'score' => 75,
            'summary' => 'خلاصه تست',
            'sentiment' => AnalysisSentiment::Positive,
            'strengths_json' => [],
            'weaknesses_json' => [],
            'next_actions_json' => [],
            'analyzed_at' => now(),
        ], $overrides);
    }
}
