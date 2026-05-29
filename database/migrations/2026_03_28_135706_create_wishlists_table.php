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
        Schema::create('wishlists', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('idpelamar');
            $table->unsignedBigInteger('idlowongan');
            $table->timestamps();

            // Cegah duplikasi wishlist pada pelamar dan lowongan yg sama
            $table->unique(['idpelamar', 'idlowongan']);
            
            $table->foreign('idpelamar')->references('id')->on('pelamars')->onDelete('cascade');
            $table->foreign('idlowongan')->references('id')->on('lowongans')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wishlists');
    }
};
