<?php

namespace Database\Seeders;

use App\Domain\Voip\Enums\VoipProviderCode;
use App\Infrastructure\Voip\Adapters\NovatelVoipAdapter;
use App\Models\IntegrationMetaDefinition;
use App\Models\VoipProvider;
use Illuminate\Database\Seeder;

class VoipProviderSeeder extends Seeder
{
    public function run(): void
    {
        VoipProvider::query()
            ->where('code', '!=', VoipProviderCode::Novatel->value)
            ->each(function (VoipProvider $provider): void {
                IntegrationMetaDefinition::query()
                    ->where('provider_type', VoipProvider::class)
                    ->where('provider_id', $provider->id)
                    ->delete();
                $provider->delete();
            });

        VoipProvider::query()->updateOrCreate(
            ['code' => VoipProviderCode::Novatel->value],
            [
                'name' => 'Navatel',
                'adapter_class' => NovatelVoipAdapter::class,
                'supports_webhook' => true,
                'supports_polling' => true,
                'polling_interval_seconds' => 30,
                'config' => ['default_api_url' => 'https://api.navatel.ir/v1'],
                'is_active' => true,
            ],
        );
    }
}
