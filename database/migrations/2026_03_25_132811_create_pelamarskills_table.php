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
        Schema::create('pelamarskills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('idpelamar')->constrained('pelamars')->onDelete('cascade');
            $table->string('namaskill');
            $table->enum('keterangan', ['Kurang', 'Cukup', 'Baik', 'Sangat Baik']);
            $table->unsignedBigInteger('useradd')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pelamarskills');
    }
};
