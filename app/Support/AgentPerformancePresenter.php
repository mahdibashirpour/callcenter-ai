<?php

namespace App\Support;

class AgentPerformancePresenter
{
    public static function tier(array $employee): string
    {
        $score = (float) ($employee['average_score'] ?? 0);
        $trend = $employee['trend'] ?? 'stable';
        $rank = (int) ($employee['rank'] ?? 99);

        if ($score >= 70 && $rank <= 3) {
            return 'top';
        }

        if ($score > 0 && ($score < 65 || $trend === 'declining')) {
            return 'attention';
        }

        return 'normal';
    }

    public static function tierLabel(string $tier): string
    {
        return match ($tier) {
            'top' => 'برتر',
            'attention' => 'نیازمند توجه',
            default => '',
        };
    }

    public static function tierBorderClass(string $tier): string
    {
        return match ($tier) {
            'top' => 'border-emerald-300/80 ring-1 ring-emerald-200/60 dark:border-emerald-700 dark:ring-emerald-900/40',
            'attention' => 'border-amber-300/80 ring-1 ring-amber-200/60 dark:border-amber-700 dark:ring-amber-900/40',
            default => 'border-zinc-200/80 dark:border-zinc-800',
        };
    }

    public static function scoreBoxClass(?float $score): string
    {
        $score = (float) ($score ?? 0);

        return match (true) {
            $score >= 85 => 'border-emerald-200 bg-emerald-50 dark:border-emerald-800 dark:bg-emerald-950/40',
            $score >= 70 => 'border-indigo-200 bg-indigo-50 dark:border-indigo-800 dark:bg-indigo-950/40',
            $score >= 50 => 'border-amber-200 bg-amber-50 dark:border-amber-800 dark:bg-amber-950/40',
            $score > 0 => 'border-red-200 bg-red-50 dark:border-red-800 dark:bg-red-950/40',
            default => 'border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-800/50',
        };
    }

    public static function scoreRingClass(?float $score): string
    {
        $score = (float) ($score ?? 0);

        return match (true) {
            $score >= 85 => 'from-emerald-500 to-emerald-600',
            $score >= 70 => 'from-indigo-500 to-violet-600',
            $score >= 50 => 'from-amber-500 to-amber-600',
            $score > 0 => 'from-red-500 to-red-600',
            default => 'from-zinc-400 to-zinc-500',
        };
    }

    public static function scoreTextClass(?float $score): string
    {
        $score = (float) ($score ?? 0);

        return match (true) {
            $score >= 85 => 'text-emerald-600 dark:text-emerald-400',
            $score >= 70 => 'text-indigo-600 dark:text-indigo-400',
            $score >= 50 => 'text-amber-600 dark:text-amber-400',
            $score > 0 => 'text-red-600 dark:text-red-400',
            default => 'text-zinc-400',
        };
    }

    public static function trendLabel(?string $trend): string
    {
        return match ($trend) {
            'improving' => 'رو به بهبود',
            'declining' => 'رو به افت',
            default => 'پایدار',
        };
    }

    public static function trendBadgeClass(?string $trend): string
    {
        return match ($trend) {
            'improving' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/40 dark:text-emerald-300',
            'declining' => 'bg-red-100 text-red-800 dark:bg-red-950/40 dark:text-red-300',
            default => 'bg-zinc-100 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-400',
        };
    }

    public static function trendIcon(?string $trend): string
    {
        return match ($trend) {
            'improving' => '↑',
            'declining' => '↓',
            default => '→',
        };
    }
}
