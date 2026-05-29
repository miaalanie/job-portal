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
            $table->boolean('status_sesi')->default(false)->after('statusaktif');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('evens', function (Blueprint $table) {
            $table->dropColumn('status_sesi');
        });
    }
};
