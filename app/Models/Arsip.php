<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Arsip extends Model
{
    protected $fillable = [
        'kode_klasifikasi',
        'judul_berkas',
        'uraian',
        'skkaa',
        'unit_cipta',
        'masa_retensi',
        'status',
        'tanggal_dibuat',
        'berakhir_pada',
    ];

    protected $dates = [
        'tanggal_dibuat',
        'berakhir_pada'
    ];

    public function dokumens()
    {
        return $this->hasMany(DokumenArsip::class);
    }

    /* ===== LOGIKA STATUS ===== */

    public function isLewatRetensi()
    {
        return $this->status === 'AKTIF'
            && $this->berakhir_pada
            && now()->greaterThan($this->berakhir_pada);
    }
}
