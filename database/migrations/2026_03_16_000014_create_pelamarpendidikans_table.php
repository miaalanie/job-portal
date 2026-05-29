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
        Schema::create('pelamarpendidikans', function (Blueprint $table) {
            $table->id();
            $table->string('kategori'); // e.g., SMA, S1
            $table->foreignId('idpelamar')->constrained('pelamars')->onDelete('cascade');
            $table->string('namasekolah');
            $table->year('tahunawal');
            $table->year('tahunselesai');
            $table->string('jurusan')->nullable();
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
        Schema::dropIfExists('pelamarpendidikans');
    }
};
