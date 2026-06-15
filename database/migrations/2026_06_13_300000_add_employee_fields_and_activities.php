<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('organization_user', function (Blueprint $table) {
            $table->string('first_name')->nullable()->after('user_id');
            $table->string('last_name')->nullable()->after('first_name');
            $table->string('mobile')->nullable()->after('last_name');
            $table->string('extension_number')->nullable()->after('mobile');
            $table->string('position')->nullable()->after('extension_number');
            $table->string('department')->nullable()->after('position');
            $table->foreignId('organization_crm_connection_id')
                ->nullable()
                ->after('department')
                ->constrained('organization_crm_connections')
                ->nullOnDelete();
            $table->foreignId('organization_voip_connection_id')
                ->nullable()
                ->after('organization_crm_connection_id')
                ->constrained('organization_voip_connections')
                ->nullOnDelete();
            $table->boolean('is_active')->default(true)->after('organization_voip_connection_id');
        });

        Schema::create('organization_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type');
            $table->string('title');
            $table->text('description')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('organization_activities');

        Schema::table('organization_user', function (Blueprint $table) {
            $table->dropConstrainedForeignId('organization_voip_connection_id');
            $table->dropConstrainedForeignId('organization_crm_connection_id');
            $table->dropColumn([
                'first_name',
                'last_name',
                'mobile',
                'extension_number',
                'position',
                'department',
                'is_active',
            ]);
        });
    }
};
