<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('llm_providers', function (Blueprint $table) {
            $table->text('api_key')->nullable()->after('config');
            $table->string('base_url')->nullable()->after('api_key');
            $table->foreignId('default_llm_model_id')->nullable()->after('base_url')->constrained('llm_models')->nullOnDelete();
        });

        $settings = \DB::table('platform_ai_settings')->first();

        if ($settings?->default_llm_model_id) {
            $model = \DB::table('llm_models')->where('id', $settings->default_llm_model_id)->first();

            if ($model) {
                \DB::table('llm_providers')
                    ->where('id', $model->provider_id)
                    ->whereNull('default_llm_model_id')
                    ->update(['default_llm_model_id' => $model->id]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('llm_providers', function (Blueprint $table) {
            $table->dropConstrainedForeignId('default_llm_model_id');
            $table->dropColumn(['api_key', 'base_url']);
        });
    }
};
