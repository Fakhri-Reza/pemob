<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KategoriCampaign extends Model
{
    protected $table = 'kategori_campaign';
    protected $primaryKey = 'id_kategori';
    public $timestamps = true;
    protected $fillable = ['nama_kategori'];
}