<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('llm_models', function (Blueprint $table) {
            $table->id();
            $table->foreignId('provider_id')->constrained('llm_providers')->cascadeOnDelete();
            $table->string('name');
            $table->string('model_key');
            $table->decimal('input_price_per_million_tokens', 12, 6);
            $table->decimal('output_price_per_million_tokens', 12, 6);
            $table->decimal('cached_input_price_per_million_tokens', 12, 6)->nullable();
            $table->decimal('reasoning_price_per_million_tokens', 12, 6)->nullable();
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['provider_id', 'model_key']);
        });

        Schema::create('platform_ai_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('default_llm_provider_id')->nullable()->constrained('llm_providers')->nullOnDelete();
            $table->foreignId('default_llm_model_id')->nullable()->constrained('llm_models')->nullOnDelete();
            $table->boolean('allow_negative_balance')->default(false);
            $table->string('currency', 3)->default('IRR');
            $table->timestamps();
        });

        Schema::create('organization_ai_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->unique()->constrained()->cascadeOnDelete();
            $table->foreignId('llm_provider_id')->nullable()->constrained('llm_providers')->nullOnDelete();
            $table->foreignId('llm_model_id')->nullable()->constrained('llm_models')->nullOnDelete();
            $table->string('custom_prompt_version')->nullable();
            $table->timestamps();
        });

        Schema::create('organization_wallets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->unique()->constrained()->cascadeOnDelete();
            $table->decimal('balance', 14, 6)->default(0);
            $table->string('currency', 3)->default('IRR');
            $table->timestamps();
        });

        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->string('type');
            $table->decimal('amount', 14, 6);
            $table->decimal('balance_before', 14, 6);
            $table->decimal('balance_after', 14, 6);
            $table->string('reference_type')->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->string('description')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['organization_id', 'created_at']);
            $table->index(['reference_type', 'reference_id']);
        });

        Schema::table('conversation_analyses', function (Blueprint $table) {
            $table->foreignId('llm_model_id')->nullable()->after('model_name')->constrained('llm_models')->nullOnDelete();
            $table->decimal('input_price_snapshot', 12, 6)->nullable()->after('cost');
            $table->decimal('output_price_snapshot', 12, 6)->nullable()->after('input_price_snapshot');
            $table->decimal('cached_input_price_snapshot', 12, 6)->nullable()->after('output_price_snapshot');
            $table->decimal('reasoning_price_snapshot', 12, 6)->nullable()->after('cached_input_price_snapshot');
        });
    }

    public function down(): void
    {
        Schema::table('conversation_analyses', function (Blueprint $table) {
            $table->dropConstrainedForeignId('llm_model_id');
            $table->dropColumn([
                'input_price_snapshot',
                'output_price_snapshot',
                'cached_input_price_snapshot',
                'reasoning_price_snapshot',
            ]);
        });

        Schema::dropIfExists('wallet_transactions');
        Schema::dropIfExists('organization_wallets');
        Schema::dropIfExists('organization_ai_settings');
        Schema::dropIfExists('platform_ai_settings');
        Schema::dropIfExists('llm_models');
    }
};
