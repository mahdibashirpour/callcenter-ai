<?php

namespace Database\Seeders;

use App\Enums\IntegrationMetaFieldType;
use App\Models\CrmProvider;
use App\Models\IntegrationMetaDefinition;
use App\Models\VoipProvider;
use Illuminate\Database\Seeder;

class IntegrationMetaDefinitionSeeder extends Seeder
{
    public function run(): void
    {
        $didar = CrmProvider::query()->where('code', 'didar')->first();
        $novatel = VoipProvider::query()->where('code', 'novatel')->first();

        if ($didar) {
            $this->seedDefinitions($didar, [
                ['name' => 'شناسه کاربر CRM', 'key' => 'crm_user_id', 'field_type' => IntegrationMetaFieldType::Text, 'is_required' => true, 'sort_order' => 1],
                ['name' => 'شماره موبایل', 'key' => 'mobile', 'field_type' => IntegrationMetaFieldType::Tel, 'is_required' => false, 'sort_order' => 2],
                ['name' => 'ایمیل', 'key' => 'email', 'field_type' => IntegrationMetaFieldType::Email, 'is_required' => false, 'sort_order' => 3],
                ['name' => 'شناسه مخاطب خارجی', 'key' => 'external_contact_id', 'field_type' => IntegrationMetaFieldType::Text, 'is_required' => false, 'sort_order' => 4],
                ['name' => 'نام کاربری', 'key' => 'username', 'field_type' => IntegrationMetaFieldType::Text, 'is_required' => false, 'sort_order' => 5],
            ]);
        }

        if ($novatel) {
            $this->seedDefinitions($novatel, [
                ['name' => 'شماره داخلی', 'key' => 'extension', 'field_type' => IntegrationMetaFieldType::Text, 'is_required' => true, 'placeholder' => '101', 'sort_order' => 1],
                ['name' => 'شناسه کاربر داخلی', 'key' => 'internal_user_id', 'field_type' => IntegrationMetaFieldType::Text, 'is_required' => false, 'sort_order' => 2],
                ['name' => 'شماره تلفن', 'key' => 'phone_number', 'field_type' => IntegrationMetaFieldType::Tel, 'is_required' => false, 'sort_order' => 3],
                ['name' => 'کد کارشناس', 'key' => 'agent_code', 'field_type' => IntegrationMetaFieldType::Text, 'is_required' => false, 'sort_order' => 4],
            ]);
        }
    }

    private function seedDefinitions(CrmProvider|VoipProvider $provider, array $definitions): void
    {
        foreach ($definitions as $definition) {
            IntegrationMetaDefinition::query()->updateOrCreate(
                [
                    'provider_type' => $provider::class,
                    'provider_id' => $provider->id,
                    'key' => $definition['key'],
                ],
                $definition,
            );
        }
    }
}
