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
        Schema::create('blok_alkahs', function (Blueprint $table) {
            $table->id();
            //char 5 karakter, contoh: A001, B002, dst
            $table->char('kode_blok', 3)->unique();
            //status enum: tersedia, penuh
            $table->enum('status', ['Tersedia', 'Penuh'])->default('Tersedia');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blok_alkahs');
    }
};
