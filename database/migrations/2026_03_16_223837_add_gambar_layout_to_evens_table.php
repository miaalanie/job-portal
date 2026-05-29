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
        Schema::table('evens', function (Blueprint $table) {
            $table->string('gambar_layout')->nullable()->after('gambar');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('evens', function (Blueprint $table) {
            $table->dropColumn('gambar_layout');
        });
    }
};
