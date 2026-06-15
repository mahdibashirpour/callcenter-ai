<?php

use App\Support\JalaliDate;

if (! function_exists('shamsi')) {
    /**
     * Format a Gregorian date/time for Persian (Jalali) display.
     *
     * Uses App\Support\JalaliDate — safe for null values and Carbon instances.
     * Named shamsi() to avoid conflict with ariaieboy/jalali's jalali() helper.
     */
    function shamsi(
        \DateTimeInterface|string|int|null $value,
        string $preset = 'date',
        ?string $empty = '—',
    ): string {
        return match ($preset) {
            'datetime', 'datetime_short' => JalaliDate::datetime($value, $empty),
            'datetime_seconds', 'datetime_full' => JalaliDate::datetimeSeconds($value, $empty),
            'time' => JalaliDate::time($value, $empty),
            'month_day', 'short' => JalaliDate::monthDay($value, $empty),
            'ago', 'relative' => JalaliDate::ago($value, $empty),
            default => JalaliDate::date($value, $empty),
        };
    }
}
