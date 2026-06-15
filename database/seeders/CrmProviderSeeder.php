<?php

namespace Database\Seeders;

use App\Domain\Crm\Enums\CrmProviderCode;
use App\Models\CrmProvider;
use App\Models\IntegrationMetaDefinition;
use Illuminate\Database\Seeder;

class CrmProviderSeeder extends Seeder
{
    public function run(): void
    {
        CrmProvider::query()
            ->where('code', '!=', CrmProviderCode::Didar->value)
            ->each(function (CrmProvider $provider): void {
                IntegrationMetaDefinition::query()
                    ->where('provider_type', CrmProvider::class)
                    ->where('provider_id', $provider->id)
                    ->delete();
                $provider->delete();
            });

        CrmProvider::query()->updateOrCreate(
            ['code' => CrmProviderCode::Didar->value],
            [
                'name' => 'Didar CRM',
                'config' => [
                    'default_api_url' => 'https://app.didar.me/api',
                    'supports_webhooks' => true,
                ],
                'is_active' => true,
            ],
        );
    }
}
