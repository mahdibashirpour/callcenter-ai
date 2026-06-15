<?php

namespace Tests\Unit;

use App\Services\WeaknessEvaluationFilter;
use PHPUnit\Framework\TestCase;

class WeaknessEvaluationFilterTest extends TestCase
{
    private WeaknessEvaluationFilter $filter;

    protected function setUp(): void
    {
        parent::setUp();
        $this->filter = new WeaknessEvaluationFilter;
    }

    public function test_filters_english_technical_terms(): void
    {
        $filtered = $this->filter->filter([
            'CRM',
            'file',
            'API login',
            'عدم پیگیری درخواست مشتری',
        ]);

        $this->assertSame(['عدم پیگیری درخواست مشتری'], $filtered);
    }

    public function test_filters_persian_english_usage_weaknesses(): void
    {
        $filtered = $this->filter->filter([
            'استفاده از کلمات انگلیسی مانند file',
            'گفتار نامفهوم هنگام توضیح مراحل',
        ]);

        $this->assertSame(['گفتار نامفهوم هنگام توضیح مراحل'], $filtered);
    }

    public function test_filters_response_weaknesses(): void
    {
        $filtered = $this->filter->filterResponse([
            'weaknesses' => ['CRM', 'عدم پیگیری درخواست مشتری'],
            'score' => 80,
        ]);

        $this->assertSame(['عدم پیگیری درخواست مشتری'], $filtered['weaknesses']);
        $this->assertSame(80, $filtered['score']);
    }
}
