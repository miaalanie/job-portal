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
        Schema::create('evens', function (Blueprint $table) {
            $table->id();
            $table->string('namaperiode');
            $table->date('tanggalawal');
            $table->date('tanggalselesai');
            $table->string('lokasi');
            $table->boolean('statusaktif')->default(true);
            $table->text('keterangan')->nullable();
            $table->string('gambar')->nullable();
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
        Schema::dropIfExists('evens');
    }
};
