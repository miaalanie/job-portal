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
        Schema::create('registers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('idperusahaan')->constrained('perusahaans')->onDelete('cascade');
            $table->foreignId('idperiode')->constrained('evens')->onDelete('cascade');
            $table->string('namapaket');
            $table->date('tanggalregister');
            $table->boolean('aktivasi')->default(false);
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
        Schema::dropIfExists('registers');
    }
};
