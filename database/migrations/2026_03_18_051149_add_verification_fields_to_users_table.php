<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('activation_token')->nullable()->after('password');
            $table->boolean('is_active')->default(false)->after('activation_token');
            $table->dateTime('activated_at')->nullable()->after('is_active');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['activation_token', 'is_active', 'activated_at']);
        });
    }
};
