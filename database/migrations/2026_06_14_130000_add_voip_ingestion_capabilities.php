<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('voip_providers', function (Blueprint $table) {
            $table->boolean('supports_webhook')->default(true)->after('adapter_class');
            $table->boolean('supports_polling')->default(false)->after('supports_webhook');
            $table->unsignedSmallInteger('polling_interval_seconds')->default(30)->after('supports_polling');
        });

        Schema::table('organization_voip_connections', function (Blueprint $table) {
            $table->string('ingestion_mode')->default('webhook')->after('is_active');
            $table->boolean('polling_enabled')->default(false)->after('ingestion_mode');
            $table->unsignedSmallInteger('polling_interval_seconds')->nullable()->after('polling_enabled');
            $table->timestamp('last_polled_at')->nullable()->after('polling_interval_seconds');

            $table->index(['polling_enabled', 'last_polled_at']);
        });
    }

    public function down(): void
    {
        Schema::table('organization_voip_connections', function (Blueprint $table) {
            $table->dropIndex(['polling_enabled', 'last_polled_at']);
            $table->dropColumn([
                'ingestion_mode',
                'polling_enabled',
                'polling_interval_seconds',
                'last_polled_at',
            ]);
        });

        Schema::table('voip_providers', function (Blueprint $table) {
            $table->dropColumn([
                'supports_webhook',
                'supports_polling',
                'polling_interval_seconds',
            ]);
        });
    }
};
