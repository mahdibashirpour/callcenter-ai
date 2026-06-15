<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('call_recordings', function (Blueprint $table) {
            $table->timestamp('uploaded_at')->nullable()->after('downloaded_at');
            $table->timestamp('expires_at')->nullable()->after('uploaded_at');
            $table->boolean('is_expired')->default(false)->after('expires_at');
            $table->timestamp('expired_at')->nullable()->after('is_expired');

            $table->index(['is_expired', 'expires_at']);
        });

        $retentionDays = (int) env('RECORDINGS_RETENTION_DAYS', 10);

        DB::table('call_recordings')
            ->orderBy('id')
            ->lazyById()
            ->each(function (object $recording) use ($retentionDays): void {
                $uploadedAt = $recording->uploaded_at ?? $recording->created_at ?? now()->toDateTimeString();

                DB::table('call_recordings')
                    ->where('id', $recording->id)
                    ->update([
                        'uploaded_at' => $uploadedAt,
                        'expires_at' => \Illuminate\Support\Carbon::parse($uploadedAt)->addDays($retentionDays)->toDateTimeString(),
                    ]);
            });
    }

    public function down(): void
    {
        Schema::table('call_recordings', function (Blueprint $table) {
            $table->dropIndex(['is_expired', 'expires_at']);
            $table->dropColumn(['uploaded_at', 'expires_at', 'is_expired', 'expired_at']);
        });
    }
};
