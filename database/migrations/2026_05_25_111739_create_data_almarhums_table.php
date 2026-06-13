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
        Schema::create('data_almarhums', function (Blueprint $table) {
            $table->id();
            $table->foreignId('alkah_id')->unique()->constrained()->onDelete('cascade');
            $table->string('nama_almarhum');
            $table->date('tanggal_lahir');
            $table->date('tanggal_wafat');
            $table->string('umur');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_almarhums');
    }
};
