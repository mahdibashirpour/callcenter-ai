<?php

namespace App\Support;

use App\DTOs\ReportFilter;
use App\Services\Reports\EmployerReportsAnalytics;
use Illuminate\Support\Facades\View;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Writer\CSV\Writer as CsvWriter;
use OpenSpout\Writer\XLSX\Writer as XlsxWriter;
use Symfony\Component\HttpFoundation\StreamedResponse;

class EmployerReportExporter
{
    public static function downloadCsv(ReportFilter $filter): StreamedResponse
    {
        return response()->streamDownload(function () use ($filter): void {
            $writer = new CsvWriter;
            $writer->openToFile('php://output');
            $writer->addRow(Row::fromValues(self::headers()));

            foreach (self::rows($filter) as $row) {
                $writer->addRow(Row::fromValues($row));
            }

            $writer->close();
        }, self::filename($filter, 'csv'));
    }

    public static function downloadExcel(ReportFilter $filter): StreamedResponse
    {
        return response()->streamDownload(function () use ($filter): void {
            $writer = new XlsxWriter;
            $writer->openToFile('php://output');
            $writer->addRow(Row::fromValues(self::headers()));

            foreach (self::rows($filter) as $row) {
                $writer->addRow(Row::fromValues($row));
            }

            $writer->close();
        }, self::filename($filter, 'xlsx'));
    }

    public static function downloadPdf(ReportFilter $filter): StreamedResponse
    {
        $analytics = app(EmployerReportsAnalytics::class);
        $html = View::make('exports.employer-report-pdf', [
            'filter' => $filter,
            'kpis' => $analytics->kpis($filter),
            'summary' => $analytics->executiveSummary($filter),
            'employees' => $analytics->employeeComparison($filter),
            'concerns' => app(\App\Services\Reports\LeadConcernsAnalytics::class)->concernsByType($filter),
            'rows' => array_slice($analytics->exportRows($filter), 0, 100),
        ])->render();

        return response()->streamDownload(
            fn () => print($html),
            self::filename($filter, 'html'),
            ['Content-Type' => 'text/html; charset=UTF-8'],
        );
    }

    /** @return list<string> */
    private static function headers(): array
    {
        return [
            'تاریخ',
            'کارشناس',
            'امتیاز کیفیت',
            'سطح لید',
            'امتیاز لید',
            'تعداد نگرانی',
            'احساس',
            'هزینه',
            'توکن',
        ];
    }

    /** @return list<list<string|int|float>> */
    private static function rows(ReportFilter $filter): array
    {
        return collect(app(EmployerReportsAnalytics::class)->exportRows($filter))
            ->map(fn (array $row) => [
                $row['date'],
                $row['employee'],
                $row['score'],
                $row['lead_level'],
                $row['lead_score'],
                $row['concerns_count'],
                $row['sentiment'],
                $row['cost'],
                $row['tokens'],
            ])
            ->all();
    }

    private static function filename(ReportFilter $filter, string $extension): string
    {
        return sprintf(
            'employer-report-%s-%s.%s',
            $filter->from->format('Y-m-d'),
            $filter->to->format('Y-m-d'),
            $extension,
        );
    }
}
