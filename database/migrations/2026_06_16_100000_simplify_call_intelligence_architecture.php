<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('calls', function (Blueprint $table) {
            $table->string('processing_status')->default('pending')->after('status');
            $table->text('processing_error')->nullable()->after('processing_status');
        });

        Schema::table('conversation_analyses', function (Blueprint $table) {
            $table->longText('transcript')->nullable()->after('summary');
            $table->unsignedBigInteger('call_transcript_id')->nullable()->change();
        });

        Schema::table('crm_pipeline_syncs', function (Blueprint $table) {
            $table->dropForeign(['pipeline_execution_id']);
            $table->unsignedBigInteger('pipeline_execution_id')->nullable()->change();
            $table->foreign('pipeline_execution_id')->references('id')->on('pipeline_executions')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('crm_pipeline_syncs', function (Blueprint $table) {
            $table->dropForeign(['pipeline_execution_id']);
            $table->unsignedBigInteger('pipeline_execution_id')->nullable(false)->change();
            $table->foreign('pipeline_execution_id')->references('id')->on('pipeline_executions')->cascadeOnDelete();
        });

        Schema::table('conversation_analyses', function (Blueprint $table) {
            $table->dropColumn('transcript');
        });

        Schema::table('calls', function (Blueprint $table) {
            $table->dropColumn(['processing_status', 'processing_error']);
        });
    }
};
