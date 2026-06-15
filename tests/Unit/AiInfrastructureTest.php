<?php

namespace Tests\Unit;

use App\Support\AiInfrastructure;
use Tests\TestCase;

class AiInfrastructureTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['app.debug' => false]);
    }

    public function test_infrastructure_hidden_for_unauthenticated_users(): void
    {
        $this->assertFalse(AiInfrastructure::isVisible());
    }

    public function test_active_label_is_persian(): void
    {
        $this->assertSame('فعال', AiInfrastructure::activeLabel());
    }
}
