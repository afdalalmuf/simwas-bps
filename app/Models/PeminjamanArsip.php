<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class PeminjamanArsip extends Model
{
    protected $table = 'peminjaman_arsips';

    protected $fillable = [
        'arsip_id',
        'user_id',
        'alasan_peminjaman',
        'status',
        'alasan_penolakan',
        'disetujui_pada',
        'berakhir_pada',
        'diproses_oleh',
    ];

    protected $casts = [
        'disetujui_pada' => 'datetime',
        'berakhir_pada'  => 'datetime',
    ];

    // -------------------------------------------------------------------------
    // RELASI
    // -------------------------------------------------------------------------

    public function arsip(): BelongsTo
    {
        return $this->belongsTo(Arsip::class);
    }

    public function peminjam(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function prosesOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diproses_oleh');
    }

    // -------------------------------------------------------------------------
    // ACCESSOR / HELPER
    // -------------------------------------------------------------------------

    /**
     * Apakah peminjaman masih aktif (disetujui & belum kadaluarsa)
     */
    public function getAktifAttribute(): bool
    {
        return $this->status === 'DISETUJUI'
            && $this->berakhir_pada
            && Carbon::now()->lt($this->berakhir_pada);
    }

    /**
     * Generate ID tampilan: PNJ001, PNJ002, dst.
     */
    public function getIdTampilAttribute(): string
    {
        return 'PNJ' . str_pad($this->id, 3, '0', STR_PAD_LEFT);
    }

    // -------------------------------------------------------------------------
    // SCOPE
    // -------------------------------------------------------------------------

    public function scopeMenunggu($query)
    {
        return $query->where('status', 'MENUNGGU');
    }

    public function scopeDisetujui($query)
    {
        return $query->where('status', 'DISETUJUI');
    }

    public function scopeDitolak($query)
    {
        return $query->where('status', 'DITOLAK');
    }

    /**
     * Peminjaman yang sedang aktif (disetujui & belum kadaluarsa)
     */
    public function scopeAktif($query)
    {
        return $query->where('status', 'DISETUJUI')
            ->where('berakhir_pada', '>', now());
    }
}
