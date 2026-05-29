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
        Schema::create('lowongans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('idregister')->constrained('registers')->onDelete('cascade');
            $table->string('namalowongan');
            $table->text('deskripsi');
            $table->string('status');
            $table->string('kisaran_gaji')->nullable();
            $table->string('kategorilokasi'); // Dalam Negeri, Luar Negeri
            $table->integer('kuota');
            $table->foreignId('idkategorilowongan')->constrained('kategorilowongans')->onDelete('cascade');
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
        Schema::dropIfExists('lowongans');
    }
};
