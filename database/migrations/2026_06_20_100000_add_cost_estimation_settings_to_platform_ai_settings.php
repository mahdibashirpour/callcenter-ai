<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('platform_ai_settings', function (Blueprint $table) {
            $table->unsignedSmallInteger('estimation_words_per_minute')->default(150)->after('currency');
            $table->decimal('estimation_tokens_per_word', 4, 2)->default(1.30)->after('estimation_words_per_minute');
            $table->json('estimation_conversation_ratios')->nullable()->after('estimation_tokens_per_word');
        });
    }

    public function down(): void
    {
        Schema::table('platform_ai_settings', function (Blueprint $table) {
            $table->dropColumn([
                'estimation_words_per_minute',
                'estimation_tokens_per_word',
                'estimation_conversation_ratios',
            ]);
        });
    }
};
