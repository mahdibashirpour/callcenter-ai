<?php

namespace App\Support;

use App\Models\OrganizationUser;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class AvatarPresenter
{
    /** @var array<string, string> */
    private const GRADIENTS = [
        'indigo' => 'from-indigo-500 to-violet-600',
        'emerald' => 'from-emerald-500 to-teal-600',
        'sky' => 'from-sky-500 to-cyan-600',
        'amber' => 'from-amber-500 to-orange-600',
        'rose' => 'from-rose-500 to-pink-600',
        'violet' => 'from-violet-500 to-purple-600',
        'cyan' => 'from-cyan-500 to-blue-600',
    ];

    public static function forUser(?User $user, string $size = 'md'): array
    {
        if (! $user) {
            return self::forName('?', $size);
        }

        return self::forName($user->name, $size, $user->avatarUrl());
    }

    public static function forEmployee(?OrganizationUser $employee, string $size = 'md'): array
    {
        if (! $employee) {
            return self::forName('?', $size);
        }

        return self::forName($employee->full_name, $size, $employee->avatarUrl());
    }

    public static function forName(string $name, string $size = 'md', ?string $url = null): array
    {
        $normalized = trim($name) ?: '?';

        return [
            'name' => $normalized,
            'initials' => self::initials($normalized),
            'url' => filled($url) ? $url : null,
            'gradient' => self::gradientClass($normalized),
            'size' => $size,
        ];
    }

    public static function initials(string $name): string
    {
        $name = trim($name);

        if ($name === '' || $name === '?') {
            return '?';
        }

        $parts = preg_split('/\s+/u', $name) ?: [];

        if (count($parts) >= 2) {
            return mb_strtoupper(mb_substr($parts[0], 0, 1).mb_substr($parts[1], 0, 1));
        }

        return mb_strtoupper(mb_substr($name, 0, min(2, mb_strlen($name))));
    }

    public static function gradientClass(string $name): string
    {
        $keys = array_keys(self::GRADIENTS);
        $index = abs(crc32(mb_strtolower(trim($name) ?: '?'))) % count($keys);

        return self::GRADIENTS[$keys[$index]];
    }

    /** @return array<string, string> */
    public static function sizeClasses(string $size): array
    {
        return match ($size) {
            'xs' => ['box' => 'h-7 w-7 text-[10px]', 'ring' => 'ring-1'],
            'sm' => ['box' => 'h-9 w-9 text-xs', 'ring' => 'ring-2'],
            'lg' => ['box' => 'h-14 w-14 text-lg', 'ring' => 'ring-2'],
            'xl' => ['box' => 'h-20 w-20 text-2xl', 'ring' => 'ring-4'],
            default => ['box' => 'h-11 w-11 text-sm', 'ring' => 'ring-2'],
        };
    }

    public static function publicUrl(?string $path): ?string
    {
        if (! filled($path)) {
            return null;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        if (str_starts_with($path, '/')) {
            return $path;
        }

        return Storage::disk('public')->url($path);
    }
}
