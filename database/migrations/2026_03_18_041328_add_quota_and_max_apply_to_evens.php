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
            $table->integer('kuota_maksimum')->default(0)->after('status_sesi');
            $table->integer('maksimum_apply')->default(0)->after('kuota_maksimum');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('evens', function (Blueprint $table) {
            $table->dropColumn(['kuota_maksimum', 'maksimum_apply']);
        });
    }
};
