<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('calls', function (Blueprint $table) {
            $table->string('source')->default('voip')->after('provider_code');
            $table->foreignId('uploader_id')->nullable()->after('organization_user_id')->constrained('users')->nullOnDelete();
            $table->string('uploader_type')->nullable()->after('uploader_id');
            $table->string('title')->nullable()->after('metadata');
            $table->string('customer_name')->nullable()->after('title');
            $table->string('customer_phone')->nullable()->after('customer_name');
            $table->text('notes')->nullable()->after('customer_phone');
            $table->string('category')->nullable()->after('notes');
            $table->json('tags')->nullable()->after('category');
            $table->timestamp('conversation_date')->nullable()->after('tags');

            $table->index('source');
            $table->index(['organization_id', 'source']);
        });

        Schema::table('conversation_analyses', function (Blueprint $table) {
            $table->string('source')->default('voip')->after('call_id');
            $table->index('source');
        });

        Schema::create('audio_upload_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('max_file_size_bytes')->default(52_428_800);
            $table->unsignedInteger('max_duration_seconds')->default(3600);
            $table->json('allowed_extensions');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audio_upload_settings');

        Schema::table('conversation_analyses', function (Blueprint $table) {
            $table->dropIndex(['source']);
            $table->dropColumn('source');
        });

        Schema::table('calls', function (Blueprint $table) {
            $table->dropIndex(['organization_id', 'source']);
            $table->dropIndex(['source']);
            $table->dropConstrainedForeignId('uploader_id');
            $table->dropColumn([
                'source',
                'uploader_type',
                'title',
                'customer_name',
                'customer_phone',
                'notes',
                'category',
                'tags',
                'conversation_date',
            ]);
        });
    }
};
