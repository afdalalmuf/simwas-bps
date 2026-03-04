<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DokumenArsip extends Model
{
    use HasFactory;

    protected $table = 'dokumen_arsips';

    protected $fillable = [
        'arsip_id',
        'judul_dokumen',
        'nama_file',
        'path_file',
        'ukuran'
    ];

    public function arsip()
    {
        return $this->belongsTo(Arsip::class);
    }
}
