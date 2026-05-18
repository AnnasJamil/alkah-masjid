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
        Schema::create('jurnal_kas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pembayaran_alkah_id')->nullable()->constrained('pembayaran_alkahs')->onDelete('cascade');
            $table->foreignId('infaq_id')->nullable()->constrained('infaqs')->onDelete('cascade');
            $table->enum('jenis_kas', ['Masuk', 'Keluar']);
            $table->datetime('tanggal');
            $table->text('keterangan');
            //nomial input kas masuk dan keluar
            $table->decimal('nominal', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jurnal_kas');
    }
};
