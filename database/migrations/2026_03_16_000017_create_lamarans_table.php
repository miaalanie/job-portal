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
        Schema::create('lamarans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('idpelamar')->constrained('pelamars')->onDelete('cascade');
            $table->foreignId('idlowongan')->constrained('lowongans')->onDelete('cascade');
            $table->foreignId('ideven')->constrained('evens')->onDelete('cascade');
            $table->date('tanggalmelamar');
            $table->string('statusditerima')->default('Pending');
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
        Schema::dropIfExists('lamarans');
    }
};
