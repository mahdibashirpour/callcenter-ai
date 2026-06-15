<?php

use App\Models\OrganizationCrmConnection;
use App\Models\OrganizationUser;
use App\Models\OrganizationVoipConnection;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('integration_meta_definitions', function (Blueprint $table) {
            $table->id();
            $table->morphs('provider');
            $table->string('name');
            $table->string('key');
            $table->string('field_type')->default('text');
            $table->boolean('is_required')->default(false);
            $table->string('placeholder')->nullable();
            $table->text('help_text')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['provider_type', 'provider_id', 'key']);
        });

        Schema::create('employee_integration_meta', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_user_id')->constrained('organization_user')->cascadeOnDelete();
            $table->morphs('integratable');
            $table->string('key');
            $table->text('value')->nullable();
            $table->timestamps();

            $table->unique(
                ['organization_user_id', 'integratable_type', 'integratable_id', 'key'],
                'employee_integration_meta_unique',
            );
        });

        $this->migrateLegacyPivotData();

        Schema::table('organization_user', function (Blueprint $table) {
            $table->dropConstrainedForeignId('organization_voip_connection_id');
            $table->dropConstrainedForeignId('organization_crm_connection_id');
            $table->dropColumn('extension_number');
        });
    }

    public function down(): void
    {
        Schema::table('organization_user', function (Blueprint $table) {
            $table->string('extension_number')->nullable()->after('mobile');
            $table->foreignId('organization_crm_connection_id')
                ->nullable()
                ->constrained('organization_crm_connections')
                ->nullOnDelete();
            $table->foreignId('organization_voip_connection_id')
                ->nullable()
                ->constrained('organization_voip_connections')
                ->nullOnDelete();
        });

        Schema::dropIfExists('employee_integration_meta');
        Schema::dropIfExists('integration_meta_definitions');
    }

    private function migrateLegacyPivotData(): void
    {
        if (! Schema::hasColumn('organization_user', 'extension_number')) {
            return;
        }

        $memberships = DB::table('organization_user')->get();

        foreach ($memberships as $membership) {
            if ($membership->organization_voip_connection_id) {
                if ($membership->extension_number) {
                    DB::table('employee_integration_meta')->insert([
                        'organization_user_id' => $membership->id,
                        'integratable_type' => OrganizationVoipConnection::class,
                        'integratable_id' => $membership->organization_voip_connection_id,
                        'key' => 'extension',
                        'value' => $membership->extension_number,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            if ($membership->organization_crm_connection_id && $membership->mobile) {
                DB::table('employee_integration_meta')->insert([
                    'organization_user_id' => $membership->id,
                    'integratable_type' => OrganizationCrmConnection::class,
                    'integratable_id' => $membership->organization_crm_connection_id,
                    'key' => 'mobile',
                    'value' => $membership->mobile,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
};
