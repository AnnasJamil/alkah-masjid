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
        Schema::create('konten_webs', function (Blueprint $table) {
            $table->id();
            //relasi dengan kategori konten
            $table->unsignedBigInteger('kategori_konten_id');
            $table->foreign('kategori_konten_id')->references('id')->on('kategori_kontens')->onDelete('cascade');
            //relasi dengan user
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('judul');
            $table->text('isi');
            $table->datetime('tanggal_publish');
            $table->string('gambar')->nullable();
            $table->enum('status', ['draft', 'published'])->default('draft');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('konten_webs');
    }
};
