<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('conversation_analyses', function (Blueprint $table) {
            $table->json('lead_quality_json')->nullable()->after('operational_insights_json');
            $table->json('concerns_json')->nullable()->after('lead_quality_json');
        });
    }

    public function down(): void
    {
        Schema::table('conversation_analyses', function (Blueprint $table) {
            $table->dropColumn(['lead_quality_json', 'concerns_json']);
        });
    }
};
