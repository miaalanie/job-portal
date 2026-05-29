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
        Schema::create('pelamars', function (Blueprint $table) {
            $table->id();
            $table->string('noktp')->unique();
            $table->string('nokartu_kuning')->nullable();
            $table->string('namalengkap');
            $table->text('alamatlengkap');
            $table->foreignId('idkelurahan')->constrained('kelurahans')->onDelete('cascade');
            $table->string('foto')->nullable();
            $table->text('deskripsidiri')->nullable();
            $table->string('tempatlahir');
            $table->date('tanggallahir');
            $table->string('jeniskelamin');
            $table->integer('tinggibadan')->nullable();
            $table->integer('beratbadan')->nullable();
            $table->unsignedBigInteger('useradd')->nullable();
            $table->unsignedBigInteger('userupdate')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pelamars');
    }
};
