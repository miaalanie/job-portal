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
        Schema::create('pelamarpengalamen', function (Blueprint $table) {
            $table->id();
            $table->foreignId('idpelamar')->constrained('pelamars')->onDelete('cascade');
            $table->string('namaperusahaan');
            $table->string('posisi');
            $table->year('tahunawal');
            $table->year('tahunselesai')->nullable();
            $table->boolean('aktif')->default(false);
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
        Schema::dropIfExists('pelamarpengalamen');
    }
};
