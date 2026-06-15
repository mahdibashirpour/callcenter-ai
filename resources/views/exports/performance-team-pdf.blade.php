<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="utf-8">
    <title>گزارش عملکرد کارشناسان فروش</title>
    <style>
        body { font-family: Tahoma, sans-serif; font-size: 12px; color: #111; margin: 24px; }
        h1 { font-size: 20px; margin-bottom: 4px; }
        .meta { color: #666; margin-bottom: 20px; }
        .summary { background: #f4f4f5; padding: 12px; border-radius: 8px; margin-bottom: 20px; line-height: 1.8; }
        .kpis { display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; margin-bottom: 24px; }
        .kpi { border: 1px solid #e4e4e7; border-radius: 8px; padding: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th, td { border: 1px solid #e4e4e7; padding: 6px 8px; text-align: right; }
        th { background: #fafafa; }
    </style>
</head>
<body>
    <h1>گزارش عملکرد کارشناسان فروش</h1>
    <p class="meta">{{ shamsi($filter->from) }} — {{ shamsi($filter->to) }}</p>
    <div class="summary">{{ $dashboard['executive_summary'] }}</div>

    <div class="kpis">
        @foreach ([
            'کارشناسان فعال' => $dashboard['kpis']['active_employees'],
            'تماس‌های برقرارشده' => $dashboard['kpis']['total_calls'],
            'میانگین امتیاز مکالمه' => $dashboard['kpis']['average_quality_score'],
            'میانگین کیفیت لید فروش' => $dashboard['kpis']['average_lead_score'],
        ] as $label => $value)
            <div class="kpi"><strong>{{ $label }}:</strong> {{ $value }}</div>
        @endforeach
    </div>

    <h2>عملکرد کارشناسان</h2>
    <table>
        <thead>
            <tr>
                <th>کارشناس</th>
                <th>بخش</th>
                <th>تماس‌ها</th>
                <th>مکالمات تحلیل‌شده</th>
                <th>امتیاز مکالمه</th>
                <th>امتیاز لید</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($dashboard['employees'] as $row)
                <tr>
                    <td>{{ $row['name'] }}</td>
                    <td>{{ $row['department'] ?? '—' }}</td>
                    <td>{{ $row['total_calls'] }}</td>
                    <td>{{ $row['total_analyzed'] }}</td>
                    <td>{{ $row['average_score'] }}</td>
                    <td>{{ $row['average_lead_score'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
