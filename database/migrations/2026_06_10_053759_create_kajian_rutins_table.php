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
        Schema::create('kajian_rutins', function (Blueprint $table) {
            $table->id();
            $table->string('pemateri');
            $table->string('judul');
            $table->string('jadwal');
            $table->string('jam');
            $table->string('lokasi');
            $table->string('gambar')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kajian_rutins');
    }
};
