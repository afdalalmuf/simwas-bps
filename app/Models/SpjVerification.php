<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpjVerification extends Model
{
    use HasFactory;
    protected $fillable = [
        'spj_diklat_id',
        'status',
        'comments',
        'verifier_id',
        'verified_at',
        'document_type', // or document_type if you renamed it
    ];

    // 🔗 Relationship to SPJ Diklat
    public function spjDiklat()
    {
        return $this->belongsTo(SpjDiklat::class, 'spj_diklat_id', 'id_spjDiklat');
    }

    // 🔗 Relationship to Verifier (User)
    public function verifier()
    {
        return $this->belongsTo(User::class, 'verifier_id');
    }
}
