<?php

namespace Tests\Unit;

use App\Support\AvatarPresenter;
use PHPUnit\Framework\TestCase;

class AvatarPresenterTest extends TestCase
{
    public function test_initials_from_persian_name(): void
    {
        $this->assertSame('عا', AvatarPresenter::initials('علی احمدی'));
    }

    public function test_initials_from_single_name(): void
    {
        $this->assertSame('SA', AvatarPresenter::initials('Sara'));
    }

    public function test_for_name_returns_consistent_gradient(): void
    {
        $first = AvatarPresenter::forName('رضا کریمی');
        $second = AvatarPresenter::forName('رضا کریمی');

        $this->assertSame($first['gradient'], $second['gradient']);
        $this->assertSame('رضا کریمی', $first['name']);
        $this->assertNull($first['url']);
    }

    public function test_size_classes_include_xs(): void
    {
        $sizes = AvatarPresenter::sizeClasses('xs');

        $this->assertArrayHasKey('box', $sizes);
        $this->assertStringContainsString('h-7', $sizes['box']);
    }
}
