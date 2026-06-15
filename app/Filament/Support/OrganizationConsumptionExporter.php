<?php

namespace App\Filament\Support;

use App\Models\Organization;
use App\Services\AiUsageAnalyticsService;
use Carbon\Carbon;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Writer\CSV\Writer as CsvWriter;
use OpenSpout\Writer\XLSX\Writer as XlsxWriter;
use Symfony\Component\HttpFoundation\StreamedResponse;

class OrganizationConsumptionExporter
{
    public static function downloadCsv(?Carbon $from = null, ?Carbon $to = null): StreamedResponse
    {
        return response()->streamDownload(function () use ($from, $to): void {
            $writer = new CsvWriter;
            $writer->openToFile('php://output');
            $writer->addRow(Row::fromValues(self::headers()));

            self::query($from, $to)->each(function (Organization $org) use ($writer): void {
                $writer->addRow(Row::fromValues(self::row($org)));
            });

            $writer->close();
        }, 'organization-ai-consumption-'.now()->format('Y-m-d').'.csv');
    }

    public static function downloadExcel(?Carbon $from = null, ?Carbon $to = null): StreamedResponse
    {
        return response()->streamDownload(function () use ($from, $to): void {
            $writer = new XlsxWriter;
            $writer->openToFile('php://output');
            $writer->addRow(Row::fromValues(self::headers()));

            self::query($from, $to)->each(function (Organization $org) use ($writer): void {
                $writer->addRow(Row::fromValues(self::row($org)));
            });

            $writer->close();
        }, 'organization-ai-consumption-'.now()->format('Y-m-d').'.xlsx');
    }

    /** @return \Illuminate\Database\Eloquent\Builder<Organization> */
    private static function query(?Carbon $from, ?Carbon $to)
    {
        return app(AiUsageAnalyticsService::class)
            ->organizationsWithUsageQuery($from, $to)
            ->orderBy('title');
    }

    /** @return list<string> */
    private static function headers(): array
    {
        return [
            __('filament.export.organization'),
            __('filament.export.total_employees'),
            __('filament.export.conversations_analyzed'),
            __('filament.export.input_tokens'),
            __('filament.export.output_tokens'),
            __('filament.export.total_tokens'),
            __('filament.export.total_ai_cost'),
            __('filament.export.last_analysis_date'),
        ];
    }

    /** @return list<string|int|float|null> */
    private static function row(Organization $org): array
    {
        return [
            $org->title,
            $org->total_employees ?? 0,
            $org->total_analyses ?? 0,
            $org->total_input_tokens ?? 0,
            $org->total_output_tokens ?? 0,
            $org->total_tokens_sum ?? 0,
            round((float) ($org->total_ai_cost ?? 0), 6),
            $org->last_analysis_at ? Carbon::parse($org->last_analysis_at)->toDateTimeString() : null,
        ];
    }
}
