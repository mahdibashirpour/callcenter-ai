<?php

namespace App\Services;

use App\Domain\AiUsage\Enums\UsageAggregationPeriod;
use App\Domain\Llm\DTOs\AnalysisResultData;
use App\Models\AiUsageDailySnapshot;
use App\Models\ConversationAnalysis;
use App\Models\Organization;
use App\Models\OrganizationUser;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class AiUsageAnalyticsService
{
    public function recordAnalysis(AnalysisResultData $result): void
    {
        $date = ($result->analyzedAt ?? now())->format('Y-m-d');

        $this->incrementSnapshot(
            organizationId: $result->organizationId,
            organizationUserId: null,
            periodDate: $date,
            result: $result,
        );

        if ($result->organizationUserId) {
            $this->incrementSnapshot(
                organizationId: $result->organizationId,
                organizationUserId: $result->organizationUserId,
                periodDate: $date,
                result: $result,
            );
        }

        $this->bustCaches($result->organizationId, $result->organizationUserId);
    }

    public function platformOverview(?Carbon $from = null, ?Carbon $to = null): array
    {
        $query = $this->analysisQuery($from, $to);

        return [
            'input_tokens' => (int) (clone $query)->sum('input_tokens'),
            'output_tokens' => (int) (clone $query)->sum('output_tokens'),
            'total_tokens' => (int) (clone $query)->sum('total_tokens'),
            'total_cost' => round((float) (clone $query)->sum('cost'), 4),
            'analyses_count' => (clone $query)->count(),
            'organizations_count' => (clone $query)->distinct('organization_id')->count('organization_id'),
        ];
    }

    public function platformTrend(
        UsageAggregationPeriod $period = UsageAggregationPeriod::Daily,
        int $days = 30,
        ?Carbon $from = null,
        ?Carbon $to = null,
    ): array {
        $cacheKey = "ai_usage:platform:trend:{$period->value}:{$days}";

        return Cache::remember($cacheKey, 300, function () use ($period, $days, $from, $to) {
            $from ??= now()->subDays($days)->startOfDay();
            $to ??= now()->endOfDay();

            $snapshots = AiUsageDailySnapshot::query()
                ->whereNull('organization_user_id')
                ->whereBetween('period_date', [$from->toDateString(), $to->toDateString()])
                ->orderBy('period_date')
                ->get();

            if ($snapshots->isNotEmpty()) {
                return $this->groupSnapshots($snapshots, $period);
            }

            return $this->aggregateAnalysesTrend(
                ConversationAnalysis::query()->whereBetween('analyzed_at', [$from, $to]),
                $period,
            );
        });
    }

    public function organizationOverview(int $organizationId, ?Carbon $from = null, ?Carbon $to = null): array
    {
        $query = $this->analysisQuery($from, $to)->where('organization_id', $organizationId);
        $count = (clone $query)->count();

        return [
            'input_tokens' => (int) (clone $query)->sum('input_tokens'),
            'output_tokens' => (int) (clone $query)->sum('output_tokens'),
            'total_tokens' => (int) (clone $query)->sum('total_tokens'),
            'total_cost' => round((float) (clone $query)->sum('cost'), 4),
            'analyses_count' => $count,
            'average_cost_per_analysis' => $count > 0
                ? round((float) (clone $query)->sum('cost') / $count, 6)
                : 0,
            'average_score' => round((float) (clone $query)->avg('score'), 1),
        ];
    }

    public function organizationTrend(
        int $organizationId,
        UsageAggregationPeriod $period = UsageAggregationPeriod::Daily,
        int $days = 30,
    ): array {
        $cacheKey = "ai_usage:org:{$organizationId}:trend:{$period->value}:{$days}";

        return Cache::remember($cacheKey, 300, function () use ($organizationId, $period, $days) {
            $from = now()->subDays($days)->startOfDay();
            $to = now()->endOfDay();

            $snapshots = AiUsageDailySnapshot::query()
                ->where('organization_id', $organizationId)
                ->whereNull('organization_user_id')
                ->whereBetween('period_date', [$from->toDateString(), $to->toDateString()])
                ->orderBy('period_date')
                ->get();

            if ($snapshots->isNotEmpty()) {
                return $this->groupSnapshots($snapshots, $period);
            }

            return $this->aggregateAnalysesTrend(
                ConversationAnalysis::query()
                    ->where('organization_id', $organizationId)
                    ->whereBetween('analyzed_at', [$from, $to]),
                $period,
            );
        });
    }

    public function employeeOverview(int $organizationUserId, ?Carbon $from = null, ?Carbon $to = null): array
    {
        $query = $this->analysisQuery($from, $to)->where('organization_user_id', $organizationUserId);
        $count = (clone $query)->count();

        return [
            'input_tokens' => (int) (clone $query)->sum('input_tokens'),
            'output_tokens' => (int) (clone $query)->sum('output_tokens'),
            'total_tokens' => (int) (clone $query)->sum('total_tokens'),
            'total_cost' => round((float) (clone $query)->sum('cost'), 4),
            'analyses_count' => $count,
            'average_score' => round((float) (clone $query)->avg('score'), 1),
        ];
    }

    public function employeeTrend(
        int $organizationUserId,
        UsageAggregationPeriod $period = UsageAggregationPeriod::Daily,
        int $days = 30,
    ): array {
        $cacheKey = "ai_usage:employee:{$organizationUserId}:trend:{$period->value}:{$days}";

        return Cache::remember($cacheKey, 300, function () use ($organizationUserId, $period, $days) {
            $from = now()->subDays($days)->startOfDay();
            $to = now()->endOfDay();

            $employee = OrganizationUser::query()->find($organizationUserId);

            $snapshots = AiUsageDailySnapshot::query()
                ->where('organization_user_id', $organizationUserId)
                ->whereBetween('period_date', [$from->toDateString(), $to->toDateString()])
                ->orderBy('period_date')
                ->get();

            if ($snapshots->isNotEmpty()) {
                return $this->groupSnapshots($snapshots, $period);
            }

            return $this->aggregateAnalysesTrend(
                ConversationAnalysis::query()
                    ->where('organization_user_id', $organizationUserId)
                    ->whereBetween('analyzed_at', [$from, $to]),
                $period,
            );
        });
    }

    public function organizationsWithUsageQuery(?Carbon $from = null, ?Carbon $to = null): Builder
    {
        $analysisConstraint = function (Builder $query) use ($from, $to): void {
            if ($from) {
                $query->where('analyzed_at', '>=', $from);
            }
            if ($to) {
                $query->where('analyzed_at', '<=', $to);
            }
        };

        return Organization::query()
            ->withCount('memberships as total_employees')
            ->withCount(['conversationAnalyses as total_analyses' => $analysisConstraint])
            ->withSum(['conversationAnalyses as total_input_tokens' => $analysisConstraint], 'input_tokens')
            ->withSum(['conversationAnalyses as total_output_tokens' => $analysisConstraint], 'output_tokens')
            ->withSum(['conversationAnalyses as total_tokens_sum' => $analysisConstraint], 'total_tokens')
            ->withSum(['conversationAnalyses as total_ai_cost' => $analysisConstraint], 'cost')
            ->withMax(['conversationAnalyses as last_analysis_at' => $analysisConstraint], 'analyzed_at');
    }

    public function employeesWithUsageQuery(int $organizationId, ?Carbon $from = null, ?Carbon $to = null): Builder
    {
        $analysisConstraint = function (Builder $query) use ($from, $to): void {
            if ($from) {
                $query->where('analyzed_at', '>=', $from);
            }
            if ($to) {
                $query->where('analyzed_at', '<=', $to);
            }
        };

        return OrganizationUser::query()
            ->where('organization_id', $organizationId)
            ->withCount(['conversationAnalyses as total_analyses' => $analysisConstraint])
            ->withSum(['conversationAnalyses as total_input_tokens' => $analysisConstraint], 'input_tokens')
            ->withSum(['conversationAnalyses as total_output_tokens' => $analysisConstraint], 'output_tokens')
            ->withSum(['conversationAnalyses as total_tokens_sum' => $analysisConstraint], 'total_tokens')
            ->withSum(['conversationAnalyses as total_ai_cost' => $analysisConstraint], 'cost')
            ->withAvg(['conversationAnalyses as average_score' => $analysisConstraint], 'score')
            ->withMax(['conversationAnalyses as last_analysis_at' => $analysisConstraint], 'analyzed_at');
    }

    public function rebuildAllSnapshots(): int
    {
        AiUsageDailySnapshot::query()->delete();

        $processed = 0;

        ConversationAnalysis::query()
            ->orderBy('id')
            ->chunkById(500, function (Collection $analyses) use (&$processed): void {
                foreach ($analyses as $analysis) {
                    $this->incrementSnapshot(
                        organizationId: $analysis->organization_id,
                        organizationUserId: null,
                        periodDate: $analysis->analyzed_at->format('Y-m-d'),
                        inputTokens: $analysis->input_tokens,
                        outputTokens: $analysis->output_tokens,
                        totalTokens: $analysis->total_tokens,
                        cost: (float) $analysis->cost,
                        processingDurationMs: $analysis->processing_duration_ms,
                        score: $analysis->score,
                        llmProvider: $analysis->llm_provider,
                        modelName: $analysis->model_name,
                    );

                    if ($analysis->organization_user_id) {
                        $this->incrementSnapshot(
                            organizationId: $analysis->organization_id,
                            organizationUserId: $analysis->organization_user_id,
                            periodDate: $analysis->analyzed_at->format('Y-m-d'),
                            inputTokens: $analysis->input_tokens,
                            outputTokens: $analysis->output_tokens,
                            totalTokens: $analysis->total_tokens,
                            cost: (float) $analysis->cost,
                            processingDurationMs: $analysis->processing_duration_ms,
                            score: $analysis->score,
                            llmProvider: $analysis->llm_provider,
                            modelName: $analysis->model_name,
                        );
                    }

                    $processed++;
                }
            });

        Cache::flush();

        return $processed;
    }

    private function analysisQuery(?Carbon $from, ?Carbon $to): Builder
    {
        return ConversationAnalysis::query()
            ->when($from, fn (Builder $q) => $q->where('analyzed_at', '>=', $from))
            ->when($to, fn (Builder $q) => $q->where('analyzed_at', '<=', $to));
    }

    private function incrementSnapshot(
        int $organizationId,
        ?int $organizationUserId,
        string $periodDate,
        ?AnalysisResultData $result = null,
        int $inputTokens = 0,
        int $outputTokens = 0,
        int $totalTokens = 0,
        float $cost = 0,
        int $processingDurationMs = 0,
        int $score = 0,
        ?string $llmProvider = null,
        ?string $modelName = null,
    ): void {
        if ($result) {
            $inputTokens = $result->inputTokens;
            $outputTokens = $result->outputTokens;
            $totalTokens = $result->totalTokens();
            $cost = $result->cost;
            $processingDurationMs = $result->processingDurationMs;
            $score = $result->score;
            $llmProvider = $result->llmProvider;
            $modelName = $result->modelName;
        }

        $snapshot = $this->resolveDailySnapshot(
            organizationId: $organizationId,
            organizationUserId: $organizationUserId,
            periodDate: $periodDate,
            llmProvider: $llmProvider,
            modelName: $modelName,
        );

        $snapshot->increment('analyses_count');
        $snapshot->increment('input_tokens', $inputTokens);
        $snapshot->increment('output_tokens', $outputTokens);
        $snapshot->increment('total_tokens', $totalTokens);
        $snapshot->increment('total_processing_duration_ms', $processingDurationMs);

        DB::table('ai_usage_daily_snapshots')
            ->where('id', $snapshot->id)
            ->update([
                'total_cost' => DB::raw('total_cost + '.(float) $cost),
                'llm_provider' => $llmProvider ?? $snapshot->llm_provider,
                'model_name' => $modelName ?? $snapshot->model_name,
                'updated_at' => now(),
            ]);
    }

    private function resolveDailySnapshot(
        int $organizationId,
        ?int $organizationUserId,
        string $periodDate,
        ?string $llmProvider,
        ?string $modelName,
    ): AiUsageDailySnapshot {
        $dateString = Carbon::parse($periodDate)->toDateString();

        $snapshot = $this->findDailySnapshot($organizationId, $organizationUserId, $dateString);

        if ($snapshot) {
            return $snapshot;
        }

        try {
            return AiUsageDailySnapshot::query()->create([
                'organization_id' => $organizationId,
                'organization_user_id' => $organizationUserId,
                'period_date' => $dateString,
                'analyses_count' => 0,
                'input_tokens' => 0,
                'output_tokens' => 0,
                'total_tokens' => 0,
                'total_cost' => 0,
                'total_processing_duration_ms' => 0,
                'llm_provider' => $llmProvider,
                'model_name' => $modelName,
            ]);
        } catch (UniqueConstraintViolationException) {
            $snapshot = $this->findDailySnapshot($organizationId, $organizationUserId, $dateString);

            if ($snapshot) {
                return $snapshot;
            }

            throw new \RuntimeException('Unable to resolve AI usage daily snapshot.');
        }
    }

    private function findDailySnapshot(
        int $organizationId,
        ?int $organizationUserId,
        string $periodDate,
    ): ?AiUsageDailySnapshot {
        return AiUsageDailySnapshot::query()
            ->where('organization_id', $organizationId)
            ->when(
                $organizationUserId === null,
                fn (Builder $query) => $query->whereNull('organization_user_id'),
                fn (Builder $query) => $query->where('organization_user_id', $organizationUserId),
            )
            ->whereDate('period_date', $periodDate)
            ->first();
    }

    /** @param Collection<int, AiUsageDailySnapshot> $snapshots */
    private function groupSnapshots(Collection $snapshots, UsageAggregationPeriod $period): array
    {
        $grouped = $snapshots->groupBy(fn (AiUsageDailySnapshot $s) => $this->periodKey($s->period_date, $period));

        return $grouped->map(fn (Collection $items, string $key) => [
            'period' => $key,
            'input_tokens' => (int) $items->sum('input_tokens'),
            'output_tokens' => (int) $items->sum('output_tokens'),
            'total_tokens' => (int) $items->sum('total_tokens'),
            'total_cost' => round((float) $items->sum('total_cost'), 4),
            'analyses_count' => (int) $items->sum('analyses_count'),
        ])->values()->all();
    }

    private function aggregateAnalysesTrend(Builder $query, UsageAggregationPeriod $period): array
    {
        $analyses = $query->get(['analyzed_at', 'input_tokens', 'output_tokens', 'total_tokens', 'cost']);

        $grouped = $analyses->groupBy(fn (ConversationAnalysis $a) => $this->periodKey($a->analyzed_at, $period));

        return $grouped->map(fn (Collection $items, string $key) => [
            'period' => $key,
            'input_tokens' => (int) $items->sum('input_tokens'),
            'output_tokens' => (int) $items->sum('output_tokens'),
            'total_tokens' => (int) $items->sum('total_tokens'),
            'total_cost' => round((float) $items->sum('cost'), 4),
            'analyses_count' => $items->count(),
        ])->values()->all();
    }

    private function periodKey(Carbon|string $date, UsageAggregationPeriod $period): string
    {
        $carbon = $date instanceof Carbon ? $date : Carbon::parse($date);

        return match ($period) {
            UsageAggregationPeriod::Weekly => $carbon->format('Y-\WW'),
            UsageAggregationPeriod::Monthly => $carbon->format('Y-m'),
            UsageAggregationPeriod::Daily => $carbon->format('Y-m-d'),
        };
    }

    private function bustCaches(int $organizationId, ?int $organizationUserId): void
    {
        Cache::forget('ai_usage:platform:trend:daily:30');
        Cache::forget('ai_usage:platform:trend:weekly:30');
        Cache::forget('ai_usage:platform:trend:monthly:30');
        Cache::forget("ai_usage:org:{$organizationId}:trend:daily:30");

        if ($organizationUserId) {
            Cache::forget("ai_usage:employee:{$organizationUserId}:trend:daily:30");
        }
    }
}
