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
        Schema::create('even_pakets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ideven')->constrained('evens')->onDelete('cascade');
            $table->string('nama_paket');
            $table->text('fasilitas')->nullable();
            $table->decimal('harga', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('even_pakets');
    }
};
