<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ml_request_logs', function (Blueprint $table) {
            $table->id();
            $table->uuid('request_id')->index();
            $table->string('request_type', 50)->index(); // embedding_pelamar, embedding_lowongan, match, rank_applicants, cf_recommendation
            $table->string('endpoint', 255)->nullable();
            $table->string('status', 20)->default('success')->index(); // success, error
            $table->integer('status_code')->nullable();
            $table->float('duration_ms', 10, 2)->nullable();

            // Common foreign keys
            $table->unsignedBigInteger('pelamar_id')->nullable()->index();
            $table->unsignedBigInteger('lowongan_id')->nullable()->index();

            // Embedding-specific
            $table->boolean('is_update')->nullable();
            $table->integer('total_skills')->nullable();
            $table->integer('total_pendidikans')->nullable();
            $table->integer('total_pengalamans')->nullable();
            $table->integer('total_jurusans')->nullable();
            $table->integer('total_records_embedded')->nullable();

            // Match (CBF) specific
            $table->integer('total_lowongans_sent')->nullable();
            $table->integer('total_recommendations')->nullable();

            // Rank specific
            $table->integer('total_pelamars_sent')->nullable();
            $table->integer('total_ranked')->nullable();

            // CF specific
            $table->integer('user_items_count')->nullable();
            $table->integer('result_count')->nullable();

            // ML model info
            $table->string('model_version', 255)->nullable();
            $table->integer('embedding_dimension')->nullable();

            // Error & extra
            $table->text('error')->nullable();
            $table->json('extra')->nullable();

            $table->timestamp('created_at')->useCurrent();

            // Indexes for dashboard queries
            $table->index(['request_type', 'created_at']);
            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ml_request_logs');
    }
};