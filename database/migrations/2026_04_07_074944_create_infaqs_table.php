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
        Schema::create('infaqs', function (Blueprint $table) {
            $table->id();
            $table->string('nama_penginfaq')->default('Hamba Allah');
            $table->decimal('nominal', 10, 2);
            $table->string('tujuan_infaq');
            $table->string('bukti_infaq');
            $table->enum('status', ['Menunggu Diterima', 'Diterima', 'Ditolak'])->default('Menunggu Diterima');
            $table->datetime('tanggal_infaq');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('infaqs');
    }
};
