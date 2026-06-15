<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('platform_ai_settings')->where('currency', 'USD')->update(['currency' => 'IRR']);
        DB::table('organization_wallets')->where('currency', 'USD')->update(['currency' => 'IRR']);
    }

    public function down(): void
    {
        DB::table('platform_ai_settings')->where('currency', 'IRR')->update(['currency' => 'USD']);
        DB::table('organization_wallets')->where('currency', 'IRR')->update(['currency' => 'USD']);
    }
};
