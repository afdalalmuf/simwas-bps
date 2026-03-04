<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Arsip;
use App\Models\PeminjamanArsip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CariArsipController extends Controller
{
    /**
     * Halaman cari arsip — tampilkan daftar arsip AKTIF
     */
    public function index()
    {
        // Hanya arsip AKTIF yang bisa dicari/dipinjam pegawai
        $arsips = Arsip::withCount('dokumens')
            ->where('status', 'AKTIF')
            ->get();

        // Ambil semua peminjaman user ini yang masih aktif / menunggu
        // Dipakai untuk menentukan status tombol per arsip
        $peminjamanAktif = PeminjamanArsip::where('user_id', Auth::id())
            ->whereIn('status', ['MENUNGGU', 'DISETUJUI'])
            ->get()
            ->keyBy('arsip_id'); // [arsip_id => PeminjamanArsip]

        return view('pegawai.cari-arsip.index', compact('arsips', 'peminjamanAktif'));
    }

    /**
     * Ajukan peminjaman arsip
     */
    public function ajukan(Request $request)
    {
        $request->validate([
            'arsip_id'         => 'required|exists:arsips,id',
            'alasan_peminjaman' => 'required|string|min:5|max:500',
        ]);

        $arsipId = $request->arsip_id;
        $userId  = Auth::id();

        // Cek apakah sudah ada pengajuan menunggu atau akses aktif
        $existing = PeminjamanArsip::where('user_id', $userId)
            ->where('arsip_id', $arsipId)
            ->whereIn('status', ['MENUNGGU', 'DISETUJUI'])
            ->first();

        if ($existing) {
            $pesan = $existing->status === 'MENUNGGU'
                ? 'Anda sudah memiliki pengajuan yang sedang menunggu persetujuan.'
                : 'Anda sudah memiliki akses aktif untuk arsip ini.';

            return response()->json(['success' => false, 'message' => $pesan], 422);
        }

        PeminjamanArsip::create([
            'arsip_id'         => $arsipId,
            'user_id'          => $userId,
            'alasan_peminjaman' => $request->alasan_peminjaman,
            'status'           => 'MENUNGGU',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pengajuan peminjaman berhasil dikirim. Menunggu persetujuan arsiparis.',
        ]);
    }

    /**
     * Halaman dokumen arsip yang dipinjam (hanya bisa diakses jika peminjaman aktif)
     */
    public function aksesDokumen($arsipId)
    {
        $userId = Auth::id();

        // Validasi: harus ada peminjaman aktif
        $peminjaman = PeminjamanArsip::where('user_id', $userId)
            ->where('arsip_id', $arsipId)
            ->aktif()
            ->firstOrFail();

        $arsip = Arsip::with('dokumens')->findOrFail($arsipId);

        return view('pegawai.cari-arsip.dokumen', compact('arsip', 'peminjaman'));
    }
}
