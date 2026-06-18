<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('llm_models', function (Blueprint $table) {
            $table->decimal('input_price_per_million_tokens', 18, 6)->change();
            $table->decimal('output_price_per_million_tokens', 18, 6)->change();
            $table->decimal('cached_input_price_per_million_tokens', 18, 6)->nullable()->change();
            $table->decimal('reasoning_price_per_million_tokens', 18, 6)->nullable()->change();
        });

        Schema::table('conversation_analyses', function (Blueprint $table) {
            $table->decimal('cost', 18, 6)->default(0)->change();
            $table->decimal('input_price_snapshot', 18, 6)->nullable()->change();
            $table->decimal('output_price_snapshot', 18, 6)->nullable()->change();
            $table->decimal('cached_input_price_snapshot', 18, 6)->nullable()->change();
            $table->decimal('reasoning_price_snapshot', 18, 6)->nullable()->change();
        });

        Schema::table('llm_analysis_logs', function (Blueprint $table) {
            $table->decimal('cost', 18, 6)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('llm_analysis_logs', function (Blueprint $table) {
            $table->decimal('cost', 12, 6)->nullable()->change();
        });

        Schema::table('conversation_analyses', function (Blueprint $table) {
            $table->decimal('cost', 12, 6)->default(0)->change();
            $table->decimal('input_price_snapshot', 12, 6)->nullable()->change();
            $table->decimal('output_price_snapshot', 12, 6)->nullable()->change();
            $table->decimal('cached_input_price_snapshot', 12, 6)->nullable()->change();
            $table->decimal('reasoning_price_snapshot', 12, 6)->nullable()->change();
        });

        Schema::table('llm_models', function (Blueprint $table) {
            $table->decimal('input_price_per_million_tokens', 12, 6)->change();
            $table->decimal('output_price_per_million_tokens', 12, 6)->change();
            $table->decimal('cached_input_price_per_million_tokens', 12, 6)->nullable()->change();
            $table->decimal('reasoning_price_per_million_tokens', 12, 6)->nullable()->change();
        });
    }
};
