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
        Schema::create('perusahaans', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->text('alamatlengkap');
            $table->foreignId('idkelurahan')->constrained('kelurahans')->onDelete('cascade');
            $table->foreignId('idkategori')->constrained('kategoriperusahaans')->onDelete('cascade');
            $table->string('bentuk'); // PT, CV etc
            $table->string('logo')->nullable();
            $table->text('gambaranumum')->nullable();
            $table->string('telp');
            $table->string('email');
            $table->string('namapimpinan');
            $table->string('pic');
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
        Schema::dropIfExists('perusahaans');
    }
};
