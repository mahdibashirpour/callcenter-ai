<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="utf-8">
    <title>گزارش مدیریتی</title>
    <style>
        body { font-family: Tahoma, sans-serif; font-size: 12px; color: #111; margin: 24px; }
        h1 { font-size: 20px; margin-bottom: 4px; }
        .meta { color: #666; margin-bottom: 20px; }
        .summary { background: #f4f4f5; padding: 12px; border-radius: 8px; margin-bottom: 20px; line-height: 1.8; }
        .kpis { display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; margin-bottom: 24px; }
        .kpi { border: 1px solid #e4e4e7; border-radius: 8px; padding: 10px; }
        .kpi-label { color: #71717a; font-size: 11px; }
        .kpi-value { font-size: 18px; font-weight: bold; margin-top: 4px; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th, td { border: 1px solid #e4e4e7; padding: 6px 8px; text-align: right; }
        th { background: #fafafa; }
        @media print { body { margin: 12px; } }
    </style>
</head>
<body>
    <h1>گزارش مدیریتی</h1>
    <p class="meta">{{ shamsi($filter->from) }} — {{ shamsi($filter->to) }}</p>

    <div class="summary">{{ $summary }}</div>

    <div class="kpis">
        @foreach ([
            'کل تماس‌ها' => $kpis['total_calls'],
            'تحلیل‌شده' => $kpis['total_analyzed'],
            'میانگین کیفیت' => $kpis['average_quality_score'],
            'میانگین لید' => $kpis['average_lead_quality_score'],
            'لیدهای با کیفیت' => $kpis['high_quality_leads'],
            'نگرانی‌ها' => $kpis['total_concerns'],
        ] as $label => $value)
            <div class="kpi">
                <div class="kpi-label">{{ $label }}</div>
                <div class="kpi-value">{{ $value }}</div>
            </div>
        @endforeach
    </div>

    <h2>عملکرد کارشناسان</h2>
    <table>
        <thead>
            <tr>
                <th>کارشناس</th>
                <th>امتیاز کیفیت</th>
                <th>امتیاز لید</th>
                <th>تحلیل‌شده</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($employees as $employee)
                <tr>
                    <td>{{ $employee['name'] }}</td>
                    <td>{{ $employee['average_score'] }}</td>
                    <td>{{ $employee['average_lead_score'] }}</td>
                    <td>{{ $employee['total_analyzed'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h2>جزئیات تحلیل‌ها</h2>
    <table>
        <thead>
            <tr>
                <th>تاریخ</th>
                <th>کارشناس</th>
                <th>امتیاز</th>
                <th>لید</th>
                <th>نگرانی</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($rows as $row)
                <tr>
                    <td>{{ $row['date'] }}</td>
                    <td>{{ $row['employee'] }}</td>
                    <td>{{ $row['score'] }}</td>
                    <td>{{ $row['lead_level'] }}</td>
                    <td>{{ $row['concerns_count'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
