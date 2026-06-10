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
        Schema::create('pembayaran_alkahs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaksi_alkah_id')->constrained('transaksi_alkahs')->onDelete('cascade');
            $table->string('bukti_pembayaran')->nullable();
            $table->decimal('total_bayar', 10, 2);
            $table->text('catatan')->nullable();
            $table->enum('status', ['Menunggu Pembayaran', 'Menunggu Verifikasi', 'Diverifikasi'])->default('Menunggu Pembayaran');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembayaran_alkahs');
    }
};
