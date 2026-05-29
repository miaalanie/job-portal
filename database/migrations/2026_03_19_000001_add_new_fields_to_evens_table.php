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
            $table->boolean('statusheadline')->default(false)->after('statusaktif');
            $table->decimal('biaya', 15, 2)->nullable()->after('statusheadline');
            $table->boolean('statuspaket')->default(true)->after('biaya');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('evens', function (Blueprint $table) {
            $table->dropColumn(['statusheadline', 'biaya', 'statuspaket']);
        });
    }
};
