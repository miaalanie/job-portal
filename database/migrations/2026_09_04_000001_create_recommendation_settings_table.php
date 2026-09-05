<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recommendation_settings', function (Blueprint $table) {
            $table->id();
            $table->decimal('weight_semantic', 5, 4)->default(0.25);
            $table->decimal('weight_skill', 5, 4)->default(0.25);
            $table->decimal('weight_education', 5, 4)->default(0.25);
            $table->decimal('weight_experience', 5, 4)->default(0.25);
            $table->decimal('skill_threshold', 5, 4)->default(0.50);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recommendation_settings');
    }
};
