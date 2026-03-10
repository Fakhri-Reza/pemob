<?php
// app/Models/Campaign.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    // Konfigurasi sesuai database kamu
    protected $table = 'campaign';
    protected $primaryKey = 'id_campaign';
    public $incrementing = true; // Karena id_campaign adalah auto-increment
    protected $keyType = 'int';
    public $timestamps = true;

    protected $fillable = [
        'id_user', 'id_kategori', 'judul_campaign', 'deskripsi', 
        'target_donasi', 'dana_terkumpul', 'tanggal_mulai', 
        'tanggal_selesai', 'status', 'gambar_campaign'
    ];

    // --- TAMBAHKAN RELASI INI ---

    // Relasi ke Tabel Users
    // Asumsi: Tabel 'users' primary key-nya 'id'
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'id_user', 'id_user');
    }

    // Relasi ke Tabel Kategori Campaign
    // Perhatikan: references('id_kategori') karena foreign key kamu merujuk ke id_kategori, bukan id biasa
    public function kategori()
    {
        return $this->belongsTo(\App\Models\KategoriCampaign::class, 'id_kategori', 'id_kategori');
    }
}