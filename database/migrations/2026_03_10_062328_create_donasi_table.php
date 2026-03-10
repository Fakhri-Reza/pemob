<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('donasi', function (Blueprint $table) {
            $table->id('id_donasi');
            $table->unsignedBigInteger('id_user');
            $table->unsignedBigInteger('id_campaign');
            $table->decimal('jumlah_donasi', 15, 2);
            $table->string('pesan_donasi')->nullable();
            $table->enum('status_pembayaran', ['pending', 'berhasil', 'gagal'])->default('pending');
            $table->string('metode_pembayaran')->nullable(); // transfer, qris, dll
            $table->string('bukti_pembayaran')->nullable(); // upload foto bukti
            $table->string('kode_unik')->nullable(); // untuk verifikasi pembayaran
            $table->timestamps();

            // Foreign Keys
            $table->foreign('id_user')->references('id_user')->on('users')->onDelete('cascade');
            $table->foreign('id_campaign')->references('id_campaign')->on('campaign')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('donasi');
    }
};