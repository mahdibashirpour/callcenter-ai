<?php

namespace App\Services;

use App\Domain\Voip\Enums\CallStatus;
use App\DTOs\AnalysisListFilter;
use App\Models\ConversationAnalysis;
use App\Services\Reports\CallMetricsAnalytics;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class AnalysisListQuery
{
    public function __construct(private Reports\CallMetricsAnalytics $callMetrics) {}

    /** @return Builder<ConversationAnalysis> */
    public function baseQuery(AnalysisListFilter $filter): Builder
    {
        $query = ConversationAnalysis::query()
            ->leftJoin('calls', 'conversation_analyses.call_id', '=', 'calls.id')
            ->leftJoin('voip_call_logs', 'conversation_analyses.voip_call_log_id', '=', 'voip_call_logs.id')
            ->leftJoin('organization_user', 'conversation_analyses.organization_user_id', '=', 'organization_user.id')
            ->select('conversation_analyses.*');

        $filter->apply($query);
        $filter->applySort($query);

        return $query;
    }

    public function paginate(AnalysisListFilter $filter, int $perPage = 20): LengthAwarePaginator
    {
        return $this->baseQuery($filter)
            ->with(['employee.user', 'call', 'callLog'])
            ->paginate($perPage);
    }

    /** @return array{total: int, average_score: float, average_duration_seconds: int, average_duration_label: string, missed_count: int, inbound_count: int, outbound_count: int} */
    public function overview(AnalysisListFilter $filter): array
    {
        $query = $this->baseQuery($filter);

        $total = (clone $query)->count();
        $avgScore = round((float) (clone $query)->avg('conversation_analyses.score'), 1);

        $avgDuration = (int) round((float) (clone $query)
            ->avg(\Illuminate\Support\Facades\DB::raw('COALESCE(calls.duration_seconds, voip_call_logs.duration, 0)')));

        $missedCount = (clone $query)->where(function (Builder $inner) {
            $inner->where('calls.status', CallStatus::Missed->value)
                ->orWhere('voip_call_logs.status', CallStatus::Missed->value);
        })->count();

        $inboundCount = (clone $query)->where(function (Builder $inner) {
            $inner->where('calls.direction', 'inbound')
                ->orWhere('voip_call_logs.direction', 'inbound');
        })->count();

        $outboundCount = (clone $query)->where(function (Builder $inner) {
            $inner->where('calls.direction', 'outbound')
                ->orWhere('voip_call_logs.direction', 'outbound');
        })->count();

        return [
            'total' => $total,
            'average_score' => $avgScore,
            'average_duration_seconds' => $avgDuration,
            'average_duration_label' => $this->callMetrics->formatDuration($avgDuration),
            'missed_count' => $missedCount,
            'inbound_count' => $inboundCount,
            'outbound_count' => $outboundCount,
        ];
    }
}
