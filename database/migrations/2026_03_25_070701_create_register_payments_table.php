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
        Schema::create('register_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('idregister');
            $table->string('bank_asal')->nullable();
            $table->string('nama_pengirim')->nullable();
            $table->decimal('jumlah_bayar', 15, 2);
            $table->string('bukti_bayar'); // File path
            $table->date('tanggal_bayar');
            $table->string('status')->default('Menunggu Verifikasi'); // Menunggu Verifikasi, Terverifikasi, Ditolak
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->foreign('idregister')->references('id')->on('registers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('register_payments');
    }
};
