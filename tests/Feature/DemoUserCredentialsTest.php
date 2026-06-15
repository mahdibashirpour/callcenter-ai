<?php

namespace Tests\Feature;

use App\Models\User;
use App\Support\Seeding\DemoCatalog;
use Database\Seeders\DemoSeeder;
use Database\Seeders\PlatformFoundationSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DemoUserCredentialsTest extends TestCase
{
    use RefreshDatabase;

    public function test_demo_users_are_seeded_with_shared_password(): void
    {
        $this->seed(PlatformFoundationSeeder::class);
        $this->seed(DemoSeeder::class);

        $demoUser = User::query()
            ->where('email', DemoCatalog::exampleEmployerLogin())
            ->firstOrFail();

        $this->assertTrue(Hash::check(DemoCatalog::DEMO_PASSWORD, $demoUser->password));

        $productionUser = User::factory()->employer()->create([
            'email' => 'real-employer@example.com',
            'password' => Hash::make('password'),
        ]);

        $this->assertFalse(Hash::check(DemoCatalog::DEMO_PASSWORD, $productionUser->password));
    }
}
