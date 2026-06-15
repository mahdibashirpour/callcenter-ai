<?php

namespace Database\Seeders;

use App\Models\Organization;
use App\Models\OrganizationWallet;
use App\Models\PlatformAiSettings;
use Illuminate\Database\Seeder;

class AiBillingSeeder extends Seeder
{
    public function run(): void
    {
        $currency = PlatformAiSettings::currencyCode();

        Organization::query()
            ->where('is_demo', false)
            ->each(function (Organization $organization) use ($currency): void {
                OrganizationWallet::query()->firstOrCreate(
                    ['organization_id' => $organization->id],
                    ['balance' => 5_000_000, 'currency' => $currency],
                );
            });
    }
}
