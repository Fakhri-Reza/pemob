<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
    Schema::create('campaign', function (Blueprint $table) {
        $table->id('id_campaign');
        $table->unsignedBigInteger('id_user');
        $table->unsignedBigInteger('id_kategori');
        $table->string('judul_campaign');
        $table->text('deskripsi');
        $table->decimal('target_donasi', 15, 2);
        $table->decimal('dana_terkumpul', 15, 2)->default(0);
        $table->date('tanggal_mulai');
        $table->date('tanggal_selesai');
        $table->enum('status', ['aktif', 'selesai', 'ditutup'])->default('aktif');
        $table->string('gambar_campaign')->nullable();
        $table->timestamps();

        $table->foreign('id_kategori')->references('id_kategori')->on('kategori_campaign')->onDelete('cascade');
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campaigns');
    }
};
