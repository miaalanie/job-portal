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
        Schema::create('even_sesis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('even_id')->constrained('evens')->onDelete('cascade');
            $table->string('nama_sesi');
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->integer('kuota');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('even_sesis');
    }
};
