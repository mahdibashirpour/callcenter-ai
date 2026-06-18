<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('llm_models', function (Blueprint $table) {
            $table->boolean('sends_audio_file')->default(true)->after('is_active');
        });
    }

    public function down(): void
    {
        Schema::table('llm_models', function (Blueprint $table) {
            $table->dropColumn('sends_audio_file');
        });
    }
};
