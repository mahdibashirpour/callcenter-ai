<?php

namespace App\Services;

class WeaknessEvaluationFilter
{
    /** @var list<string> */
    private const ENGLISH_USAGE_PATTERNS = [
        '/\b(?:english|انگلیسی|زبان انگلیسی)\b/ui',
        '/\b(?:mixed[\s-]?language|دو[\s-]?زبانه|ترکیب فارسی و انگلیسی)\b/ui',
        '/\b(?:کلمات?|واژگان?|اصطلاحات?) انگلیسی\b/ui',
        '/\b(?:استفاده|بکارگیری|به[\s-]?کارگیری|گفتن) از (?:کلمات?|واژگان?) انگلیسی\b/ui',
    ];

    /** @param list<mixed> $weaknesses
     * @return list<string>
     */
    public function filter(array $weaknesses): array
    {
        return array_values(array_filter(
            $weaknesses,
            fn (mixed $weakness) => is_string($weakness) && ! $this->shouldRemove($weakness),
        ));
    }

    /** @param array<string, mixed> $response
     * @return array<string, mixed>
     */
    public function filterResponse(array $response): array
    {
        $response['weaknesses'] = $this->filter((array) ($response['weaknesses'] ?? []));

        return $response;
    }

    public function shouldRemove(string $weakness): bool
    {
        $trimmed = trim($weakness);

        if ($trimmed === '') {
            return true;
        }

        if (preg_match('/^[A-Za-z][A-Za-z0-9\s\.\-\/]{0,80}$/', $trimmed)) {
            return true;
        }

        foreach (self::ENGLISH_USAGE_PATTERNS as $pattern) {
            if (preg_match($pattern, $trimmed)) {
                return true;
            }
        }

        return false;
    }
}
