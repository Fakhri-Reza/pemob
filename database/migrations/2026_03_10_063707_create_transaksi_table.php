<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('transaksi', function (Blueprint $table) {
            $table->id('id_transaksi');
            $table->unsignedBigInteger('id_user');
            $table->unsignedBigInteger('id_campaign');
            $table->string('kode_transaksi')->unique(); // Kode unik transaksi (INV-20240310-001)
            $table->decimal('jumlah_donasi', 15, 2);
            $table->decimal('biaya_admin', 15, 2)->default(0);
            $table->decimal('total_bayar', 15, 2); // jumlah_donasi + biaya_admin
            $table->enum('status', ['pending', 'berhasil', 'gagal', 'kadaluarsa'])->default('pending');
            $table->string('metode_pembayaran')->nullable();
            $table->string('bukti_pembayaran')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamp('tanggal_bayar')->nullable();
            $table->timestamps();

            // Foreign Keys
            $table->foreign('id_user')->references('id_user')->on('users')->onDelete('cascade');
            $table->foreign('id_campaign')->references('id_campaign')->on('campaign')->onDelete('cascade');
            
            // Index untuk performa pencarian
            $table->index('kode_transaksi');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaksi');
    }
};