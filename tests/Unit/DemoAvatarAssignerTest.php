<?php

namespace Tests\Unit;

use App\Support\Seeding\DemoAvatarAssigner;
use App\Support\Seeding\DemoCatalog;
use PHPUnit\Framework\TestCase;

class DemoAvatarAssignerTest extends TestCase
{
    public function test_assigns_unique_gender_appropriate_avatars(): void
    {
        $assigner = new DemoAvatarAssigner;

        $this->assertSame('/img/avatar1.webp', $assigner->assign('male'));
        $this->assertSame('/img/avatar2.webp', $assigner->assign('male'));
        $this->assertSame('/img/avatar3.webp', $assigner->assign('female'));
        $this->assertSame('/img/avatar4.webp', $assigner->assign('female'));
        $this->assertSame(
            DemoCatalog::MALE_AVATARS,
            array_values(array_intersect($assigner->usedPaths(), DemoCatalog::MALE_AVATARS)),
        );
        $this->assertSame(
            DemoCatalog::FEMALE_AVATARS,
            array_values(array_intersect($assigner->usedPaths(), DemoCatalog::FEMALE_AVATARS)),
        );
    }

    public function test_does_not_reuse_avatar_until_pool_is_exhausted(): void
    {
        $assigner = new DemoAvatarAssigner;

        $firstFemale = $assigner->assign('female');
        $secondFemale = $assigner->assign('female');

        $this->assertNotSame($firstFemale, $secondFemale);
    }
}
