<?php

use App\Support\Seeding\DemoCatalog;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('organizations', function (Blueprint $table) {
            $table->boolean('is_demo')->default(false)->after('disabled');
            $table->index('is_demo');
        });

        DB::table('organizations')
            ->whereIn('user_id', function ($query): void {
                $query->select('id')
                    ->from('users')
                    ->where('email', 'like', 'demo-employer-%@'.DemoCatalog::EMAIL_DOMAIN);
            })
            ->update(['is_demo' => true]);
    }

    public function down(): void
    {
        Schema::table('organizations', function (Blueprint $table) {
            $table->dropIndex(['is_demo']);
            $table->dropColumn('is_demo');
        });
    }
};
