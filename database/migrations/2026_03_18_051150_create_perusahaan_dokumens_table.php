<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('perusahaan_dokumens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('idperusahaan')->constrained('perusahaans')->onDelete('cascade');
            $table->string('nama_dokumen'); // NIB, NPWP, SIUP, etc
            $table->string('file_path');
            $table->string('status')->default('Pending'); // Pending, Approved, Rejected
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('perusahaan_dokumens');
    }
};
