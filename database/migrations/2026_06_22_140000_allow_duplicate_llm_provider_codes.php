<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('llm_providers', function (Blueprint $table) {
            $table->dropUnique(['code']);
            $table->index('code');
        });
    }

    public function down(): void
    {
        Schema::table('llm_providers', function (Blueprint $table) {
            $table->dropIndex(['code']);
            $table->unique('code');
        });
    }
};
