@props([
    'value' => null,
    'preset' => 'date',
    'format' => null,
    'empty' => '—',
])

{{ $format ? \App\Support\JalaliDate::format($value, $format, $empty) : shamsi($value, $preset, $empty) }}
