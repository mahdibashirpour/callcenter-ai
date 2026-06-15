<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('conversation_analyses', function (Blueprint $table) {
            $table->json('customer_identity_json')->nullable()->after('concerns_json');
        });
    }

    public function down(): void
    {
        Schema::table('conversation_analyses', function (Blueprint $table) {
            $table->dropColumn('customer_identity_json');
        });
    }
};
