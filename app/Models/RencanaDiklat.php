<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RencanaDiklat extends Model
{
    use HasFactory, HasUlids;

    protected $primaryKey = 'id';
    protected $guarded = ['id'];
    
    public function penyelenggara_diklat()
    {
        return $this->belongsTo(MasterPenyelenggara::class, 'penyelenggara', 'id');
    }

    public function pegawai()
    {
        return $this->belongsTo(User::class, 'id_pegawai', 'id');
    }

    public function spj_diklat()
    {
        return $this->hasOne(SpjDiklat::class, 'rencanaDiklat_id', 'id');
    }

}
