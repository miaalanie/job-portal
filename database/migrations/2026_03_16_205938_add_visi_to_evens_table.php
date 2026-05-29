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
            $table->text('visi')->nullable()->after('namaperiode');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('evens', function (Blueprint $table) {
            $table->dropColumn('visi');
        });
    }
};
