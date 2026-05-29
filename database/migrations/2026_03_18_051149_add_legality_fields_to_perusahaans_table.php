<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('perusahaans', function (Blueprint $table) {
            $table->string('npwp')->nullable()->after('email');
            $table->string('nib')->nullable()->after('npwp');
            $table->string('website')->nullable()->after('nib');
            $table->integer('jumlah_karyawan')->nullable()->after('website');
            $table->string('tahun_berdiri')->nullable()->after('jumlah_karyawan');
            $table->boolean('is_verified')->default(false)->after('pic');
            $table->dateTime('verified_at')->nullable()->after('is_verified');
        });
    }

    public function down(): void
    {
        Schema::table('perusahaans', function (Blueprint $table) {
            $table->dropColumn(['npwp', 'nib', 'website', 'jumlah_karyawan', 'tahun_berdiri', 'is_verified', 'verified_at']);
        });
    }
};
