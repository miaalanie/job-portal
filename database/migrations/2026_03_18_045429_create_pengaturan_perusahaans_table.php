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
        Schema::create('pengaturan_perusahaans', function (Blueprint $table) {
            $table->id();
            $table->string('nama_perusahaan');
            $table->text('alamat_lengkap')->nullable();
            $table->string('email')->nullable();
            $table->string('telp')->nullable();
            $table->string('logo')->nullable();
            $table->string('favicon')->nullable();
            $table->string('primary_color')->default('#009ef7');
            $table->string('secondary_color')->default('#f1416c');
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengaturan_perusahaans');
    }
};
