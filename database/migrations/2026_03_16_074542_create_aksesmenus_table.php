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
        Schema::create('aksesmenus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('idmenu')->constrained('menus')->onDelete('cascade');
            $table->foreignId('idrole')->constrained('roles')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aksesmenus');
    }
};
