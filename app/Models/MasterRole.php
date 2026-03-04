<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterRole extends Model
{
    use HasFactory, HasUlids;

    protected $primaryKey = 'id';
    protected $guarded = ['id_role'];
    
    public function users()
    {
        return $this->belongsToMany(User::class,  'role_pegawais', 'id_role', 'id_pegawai');
    }
}
