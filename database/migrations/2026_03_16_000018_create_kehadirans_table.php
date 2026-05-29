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
        Schema::create('kehadirans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('idlamaran')->constrained('lamarans')->onDelete('cascade');
            $table->boolean('statushadir')->default(false);
            $table->time('jam');
            $table->date('tanggal');
            $table->unsignedBigInteger('useradd')->nullable();
            $table->unsignedBigInteger('userupdate')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kehadirans');
    }
};
