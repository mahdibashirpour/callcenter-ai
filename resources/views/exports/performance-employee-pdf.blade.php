<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="utf-8">
    <title>گزارش عملکرد {{ $employee->full_name }}</title>
    <style>
        body { font-family: Tahoma, sans-serif; font-size: 12px; color: #111; margin: 24px; }
        h1 { font-size: 20px; }
        .meta { color: #666; margin-bottom: 16px; }
        .summary { background: #f4f4f5; padding: 12px; border-radius: 8px; margin-bottom: 20px; line-height: 1.8; }
        .kpis { display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; margin-bottom: 24px; }
        .kpi { border: 1px solid #e4e4e7; border-radius: 8px; padding: 10px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #e4e4e7; padding: 6px 8px; text-align: right; }
        th { background: #fafafa; }
        ul { margin: 8px 0; padding-right: 20px; }
    </style>
</head>
<body>
    <h1>پروفایل عملکرد: {{ $employee->full_name }}</h1>
    <p class="meta">{{ shamsi($filter->from) }} — {{ shamsi($filter->to) }}</p>
    <div class="summary">{{ $profile['executive_summary'] }}</div>

    <div class="kpis">
        @foreach ([
            'تماس‌های برقرارشده' => $profile['metrics']['total_calls'],
            'مکالمات تحلیل‌شده' => $profile['metrics']['total_analyzed'],
            'میانگین امتیاز مکالمه' => $profile['metrics']['average_quality_score'],
            'میانگین کیفیت لید فروش' => $profile['metrics']['average_lead_score'],
            'شاخص رضایت مشتری' => $profile['metrics']['average_sentiment'],
            'امتیاز اثربخشی مکالمه' => $profile['metrics']['effectiveness_score'],
        ] as $label => $value)
            <div class="kpi"><strong>{{ $label }}:</strong> {{ $value }}</div>
        @endforeach
    </div>

    <h2>نقاط ضعف شناسایی‌شده</h2>
    <ul>
        @foreach ($profile['weaknesses'] as $w)
            <li>{{ $w }}</li>
        @endforeach
    </ul>

    <h2>آخرین مکالمات تحلیل‌شده</h2>
    <table>
        <thead>
            <tr><th>تاریخ</th><th>مشتری</th><th>امتیاز مکالمه</th><th>امتیاز لید</th><th>خلاصه</th></tr>
        </thead>
        <tbody>
            @foreach ($profile['recent_calls'] as $call)
                <tr>
                    <td>{{ $call['date'] }}</td>
                    <td>{{ $call['customer'] }}</td>
                    <td>{{ $call['quality_score'] ?? '—' }}</td>
                    <td>{{ $call['lead_score'] ?? '—' }}</td>
                    <td>{{ \Illuminate\Support\Str::limit($call['summary'] ?? '—', 80) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
