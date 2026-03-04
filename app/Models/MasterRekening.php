<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterRekening extends Model
{
    use HasFactory, HasUlids;

    protected $table = "master_rekenings";
    protected $primaryKey = 'id_rekening';

    protected $guarded = ['id_rekening'];
    public $timestamps = true;

    public function pegawai()
    {
        return $this->belongsTo(User::class, 'id_pegawai', 'id');
    }
}
