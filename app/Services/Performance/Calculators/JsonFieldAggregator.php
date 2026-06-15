<?php

namespace App\Services\Performance\Calculators;

use App\Models\ConversationAnalysis;
use Illuminate\Support\Collection;

class JsonFieldAggregator
{
    /**
     * @param  Collection<int, ConversationAnalysis>  $analyses
     * @return array<string, int>
     */
    public function countItems(Collection $analyses, string $column, int $limit = 100): array
    {
        $counts = [];

        foreach ($analyses->take($limit) as $analysis) {
            foreach ($analysis->{$column} ?? [] as $item) {
                $text = is_string($item) ? $item : ($item['text'] ?? $item['title'] ?? null);
                if ($text) {
                    $counts[$text] = ($counts[$text] ?? 0) + 1;
                }
            }
        }

        arsort($counts);

        return $counts;
    }

    /**
     * @param  Collection<int, ConversationAnalysis>  $analyses
     * @return list<string>
     */
    public function topItems(Collection $analyses, string $column, int $limit = 5): array
    {
        return array_keys(array_slice($this->countItems($analyses, $column), 0, $limit, true));
    }

    /**
     * @param  Collection<int, ConversationAnalysis>  $analyses
     * @return list<array{item: string, count: int}>
     */
    public function rankedItems(Collection $analyses, string $column, int $limit = 8): array
    {
        return collect($this->countItems($analyses, $column))
            ->take($limit)
            ->map(fn (int $count, string $item) => ['item' => $item, 'count' => $count])
            ->values()
            ->all();
    }
}
