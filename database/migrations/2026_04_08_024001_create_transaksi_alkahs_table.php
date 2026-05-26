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
        Schema::create('transaksi_alkahs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('alkah_id')->constrained('alkahs')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->char('kode_transaksi', 10)->unique();
            $table->date('tanggal_pemesanan');
            $table->decimal('total', 10, 2);
            $table->enum('status', ['Pending', 'Lunas', 'Batal'])->default('Pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksi_alkahs');
    }
};
