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
        Schema::table('pengaturan_perusahaans', function (Blueprint $table) {
            $table->string('fb')->nullable()->after('telp');
            $table->string('ig')->nullable()->after('fb');
            $table->string('website')->nullable()->after('ig');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengaturan_perusahaans', function (Blueprint $table) {
            $table->dropColumn(['fb', 'ig', 'website']);
        });
    }
};
