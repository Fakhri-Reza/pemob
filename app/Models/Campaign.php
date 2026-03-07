<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    protected $table = 'campaign';
    protected $primaryKey = 'id_campaign';
    public $timestamps = true;
    protected $fillable = [
        'id_user', 
        'id_kategori', 
        'judul_campaign', 
        'deskripsi', 
        'target_donasi', 
        'dana_terkumpul', 
        'tanggal_mulai', 
        'tanggal_selesai', 
        'status', 
        'gambar_campaign'
    ];
}