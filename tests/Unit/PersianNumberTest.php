<?php

namespace Tests\Unit;

use App\Support\PersianNumber;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PersianNumberTest extends TestCase
{
    #[Test]
    public function test_it_formats_integers_with_persian_locale(): void
    {
        $formatted = PersianNumber::format(1234567, 0);

        $this->assertNotNull($formatted);
        $this->assertStringContainsString('۱', $formatted);
        $this->assertStringContainsString('۲', $formatted);
    }

    #[Test]
    public function test_it_parses_formatted_numbers_back_to_raw_values(): void
    {
        $this->assertSame(1234567, PersianNumber::parse('۱٬۲۳۴٬۵۶۷'));
        $this->assertSame(1234.56, PersianNumber::parse('۱٬۲۳۴٫۵۶'));
        $this->assertSame(150, PersianNumber::parse('150'));
    }

    #[Test]
    public function test_it_formats_decimals_without_thousands_separator(): void
    {
        $formatted = PersianNumber::format(1500.5, null, 6);

        $this->assertNotNull($formatted);
        $this->assertStringNotContainsString('٬', $formatted);
        $this->assertStringContainsString('٫', $formatted);
    }

    #[Test]
    public function test_it_round_trips_decimal_prices_for_edit_forms(): void
    {
        $formatted = PersianNumber::format(0.15, null, 6);
        $parsed = PersianNumber::parse($formatted);

        $this->assertSame(0.15, $parsed);
        $this->assertNotSame(0, $parsed);
    }
    #[Test]
    public function test_it_formats_currency_with_persian_locale(): void
    {
        $formatted = PersianNumber::currency(250000, 'IRR');

        $this->assertNotNull($formatted);
        $this->assertStringContainsString('۲۵۰', $formatted);
    }
}
