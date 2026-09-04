<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('embeddings', function (Blueprint $table) {
            $table->id();
            $table->string('embeddable_type', 50);
            $table->unsignedBigInteger('embeddable_id');
            $table->json('vector')->nullable();
            $table->char('source_hash', 64);
            $table->string('model_version', 150);
            $table->enum('status', ['pending', 'processing', 'done', 'failed'])
                ->default('pending');
            $table->timestamps();

            $table->unique(
                ['embeddable_type', 'embeddable_id', 'model_version'],
                'embeddings_source_model_unique'
            );
            $table->index(['status', 'updated_at'], 'embeddings_status_updated_at_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('embeddings');
    }
};