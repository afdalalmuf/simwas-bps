<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RolePegawai extends Model
{
    use HasFactory, HasUlids;    
    protected $primaryKey = 'id_role_pegawai';
    protected $guarded = ['id_role_pegawai'];
    public $timestamps = true;

}
