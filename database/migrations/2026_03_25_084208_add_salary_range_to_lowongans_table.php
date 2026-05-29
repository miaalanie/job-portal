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
        Schema::table('lowongans', function (Blueprint $table) {
            $table->dropColumn('kisaran_gaji');
            $table->decimal('gaji_awal', 15, 2)->nullable()->after('deskripsi');
            $table->decimal('gaji_akhir', 15, 2)->nullable()->after('gaji_awal');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lowongans', function (Blueprint $table) {
            $table->string('kisaran_gaji')->nullable()->after('deskripsi');
            $table->dropColumn(['gaji_awal', 'gaji_akhir']);
        });
    }
};
