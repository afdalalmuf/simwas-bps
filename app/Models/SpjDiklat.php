<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpjDiklat extends Model
{
    use HasFactory, HasUlids;
    
    protected $table = "spj_diklat";
    protected $primaryKey = 'id_spjDiklat';
    protected $guarded = ['id_spjDiklat'];

    public function rencanadiklat()
    {
        return $this->belongsTo(RencanaDiklat::class, 'rencanaDiklat_id', 'id');
    }

    public function pegawai()
    {
        return $this->belongsTo(User::class, 'verifikator_id', 'id');
    }

    public function verifications()
    {
        return $this->hasMany(SpjVerification::class, 'spj_diklat_id', 'id_spjDiklat');
    }

    public function rekening()
    {
        return $this->belongsTo(MasterRekening::class, 'rekening_id', 'id_rekening');
    }
}
