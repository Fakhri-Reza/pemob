<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    protected $table = 'transaksi';
    protected $primaryKey = 'id_transaksi';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = true;

    protected $fillable = [
        'id_user',
        'id_campaign',
        'kode_transaksi',
        'jumlah_donasi',
        'biaya_admin',
        'total_bayar',
        'status',
        'metode_pembayaran',
        'bukti_pembayaran',
        'catatan',
        'tanggal_bayar',
    ];

    protected $casts = [
        'jumlah_donasi' => 'decimal:2',
        'biaya_admin' => 'decimal:2',
        'total_bayar' => 'decimal:2',
        'tanggal_bayar' => 'datetime',
    ];

    // Relasi ke User
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    // Relasi ke Campaign
    public function campaign()
    {
        return $this->belongsTo(Campaign::class, 'id_campaign', 'id_campaign');
    }
}