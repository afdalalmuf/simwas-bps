<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\PeminjamanArsip;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PeminjamanArsipController extends Controller
{
    /**
     * Daftar semua pengajuan peminjaman
     */
    public function index()
    {
        $peminjamans = PeminjamanArsip::with(['arsip', 'peminjam'])
            ->latest()
            ->get();

        $counts = [
            'menunggu'  => $peminjamans->where('status', 'MENUNGGU')->count(),
            'disetujui' => $peminjamans->where('status', 'DISETUJUI')->count(),
            'ditolak'   => $peminjamans->where('status', 'DITOLAK')->count(),
        ];

        $type_menu = 'peminjaman-arsip'; // ← tambahan fix

        return view('arsiparis.peminjaman-arsip.index', compact('peminjamans', 'counts', 'type_menu'));
    }

    /**
     * Detail satu pengajuan (untuk modal — response JSON)
     */
    public function detail($id)
    {
        $p = PeminjamanArsip::with(['arsip', 'peminjam'])->findOrFail($id);

        return response()->json([
            'id'                => $p->id,
            'id_tampil'         => $p->id_tampil,
            'status'            => $p->status,
            'alasan_peminjaman' => $p->alasan_peminjaman,
            'alasan_penolakan'  => $p->alasan_penolakan,
            'disetujui_pada'    => $p->disetujui_pada?->format('d/m/Y'),
            'berakhir_pada'     => $p->berakhir_pada?->format('d/m/Y'),
            'peminjam' => [
                'nama' => $p->peminjam->name,
                'nip'  => $p->peminjam->nip ?? '-',
                'unit' => $p->peminjam->unit ?? '-',
            ],
            'arsip' => [
                'id_tampil' => 'ARS' . str_pad($p->arsip->id, 3, '0', STR_PAD_LEFT),
                'judul'     => $p->arsip->judul_berkas,
            ],
            'tanggal_pengajuan' => $p->created_at->format('d/m/Y'),
        ]);
    }

    /**
     * Setujui pengajuan peminjaman
     */
    public function setujui($id)
    {
        $p = PeminjamanArsip::findOrFail($id);

        if ($p->status !== 'MENUNGGU') {
            return response()->json(['success' => false, 'message' => 'Pengajuan sudah diproses.'], 422);
        }

        $sekarang     = Carbon::now();
        $berakhirPada = $sekarang->copy()->addDays(7);

        $p->update([
            'status'         => 'DISETUJUI',
            'disetujui_pada' => $sekarang,
            'berakhir_pada'  => $berakhirPada,
            'diproses_oleh'  => Auth::id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pengajuan berhasil disetujui. Akses berlaku hingga ' . $berakhirPada->format('d/m/Y') . '.',
        ]);
    }

    /**
     * Tolak pengajuan peminjaman
     */
    public function tolak(Request $request, $id)
    {
        $request->validate([
            'alasan_penolakan' => 'required|string|min:5|max:500',
        ]);

        $p = PeminjamanArsip::findOrFail($id);

        if ($p->status !== 'MENUNGGU') {
            return response()->json(['success' => false, 'message' => 'Pengajuan sudah diproses.'], 422);
        }

        $p->update([
            'status'           => 'DITOLAK',
            'alasan_penolakan' => $request->alasan_penolakan,
            'diproses_oleh'    => Auth::id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pengajuan berhasil ditolak.',
        ]);
    }
}
