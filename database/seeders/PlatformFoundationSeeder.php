<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\PlatformAiSettings;
use App\Models\User;
use Illuminate\Database\Seeder;

class PlatformFoundationSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(CrmProviderSeeder::class);
        $this->call(VoipProviderSeeder::class);
        $this->call(IntegrationMetaDefinitionSeeder::class);
        $this->call(LlmProviderSeeder::class);
        $this->call(LlmModelSeeder::class);

        PlatformAiSettings::current();

        User::query()->updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Super Admin',
                'password' => bcrypt('password'),
                'role' => UserRole::SuperAdmin,
                'email_verified_at' => now(),
            ],
        );

        User::query()->updateOrCreate(
            ['email' => 'admin.user@example.com'],
            [
                'name' => 'Admin User',
                'password' => bcrypt('password'),
                'role' => UserRole::Admin,
                'email_verified_at' => now(),
            ],
        );
    }
}
