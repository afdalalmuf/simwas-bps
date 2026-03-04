<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Arsip;
use App\Models\DokumenArsip;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ArsipController extends Controller
{
    public function index()
    {
        $arsips = Arsip::withCount('dokumens')
            ->orderByDesc('created_at')
            ->get();

        return view('arsiparis.kelola-arsip.index', compact('arsips'))
            ->with('type_menu', 'kelola-arsip'); // tambahkan ini
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        // Set timeout khusus untuk upload berat
        set_time_limit(300); // 5 menit
        ini_set('memory_limit', '256M');

        // Cek ukuran total request sebelum validasi
        $totalSize = 0;
        if ($request->hasFile('dokumen')) {
            foreach ($request->file('dokumen') as $file) {
                $totalSize += $file->getSize();
            }

            // Batas 50MB total (dalam bytes)
            if ($totalSize > 50 * 1024 * 1024) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->with('error', 'Total ukuran file tidak boleh lebih dari 50MB');
            }
        }

        $request->validate([
            'kode_klasifikasi' => 'required',
            'judul_berkas'     => 'required',
            'skkaa'            => 'required',
            'unit_cipta'       => 'required',
            'masa_retensi'     => 'required|integer|min:1',
            'dokumen.*'        => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
        ]);

        try {
            DB::transaction(function () use ($request) {
                $status = $request->action === 'draft' ? 'DRAFT' : 'AKTIF';

                $arsip = Arsip::create([
                    'kode_klasifikasi' => $request->kode_klasifikasi,
                    'judul_berkas'     => $request->judul_berkas,
                    'uraian'           => $request->uraian,
                    'skkaa'            => $request->skkaa,
                    'unit_cipta'       => $request->unit_cipta,
                    'masa_retensi'     => $request->masa_retensi,
                    'status'           => $status,
                    'tanggal_dibuat'   => $status === 'AKTIF' ? now() : null,
                    'berakhir_pada'    => $status === 'AKTIF'
                        ? now()->addYears($request->masa_retensi)
                        : null,
                ]);

                if ($request->hasFile('dokumen')) {
                    foreach ($request->file('dokumen') as $i => $file) {
                        $path = $file->store('dokumen_arsip', 'public');

                        DokumenArsip::create([
                            'arsip_id'      => $arsip->id,
                            'judul_dokumen' => $request->judul_dokumen[$i] ?? null,
                            'nama_file'     => $file->getClientOriginalName(),
                            'path_file'     => $path,
                            'ukuran'        => $file->getSize(),
                        ]);
                    }
                }
            });

            return redirect()
                ->route('arsiparis.kelola-arsip.index')
                ->with('success', 'Arsip berhasil disimpan');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat menyimpan arsip');
        }
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        $arsip = Arsip::with('dokumens')->findOrFail($id);

        if ($arsip->status !== 'DRAFT') {
            return redirect()
                ->route('arsiparis.kelola-arsip.index')
                ->with('error', 'Hanya arsip berstatus DRAFT yang dapat dilengkapi');
        }

        $arsips = Arsip::withCount('dokumens')
            ->orderByDesc('created_at')
            ->get();

        return view('arsiparis.kelola-arsip.index', [
            'arsips'     => $arsips,
            'editArsip'  => $arsip,
            'activeTab'  => 'edit',
            'type_menu'  => 'kelola-arsip', // tambahkan ini
        ]);
    }

    public function update(Request $request, $id)
    {
        // Set timeout khusus untuk upload berat
        set_time_limit(300); // 5 menit
        ini_set('memory_limit', '256M');

        $arsip = Arsip::findOrFail($id);

        $request->validate([
            'kode_klasifikasi' => 'required',
            'judul_berkas'     => 'required',
            'skkaa'            => 'required',
            'unit_cipta'       => 'required',
            'masa_retensi'     => 'required|integer|min:1',
            'dokumen.*'        => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
        ]);

        try {
            DB::transaction(function () use ($request, $arsip) {
                $status = $request->action === 'draft' ? 'DRAFT' : 'AKTIF';

                $arsip->update([
                    'kode_klasifikasi' => $request->kode_klasifikasi,
                    'judul_berkas'     => $request->judul_berkas,
                    'uraian'           => $request->uraian,
                    'skkaa'            => $request->skkaa,
                    'unit_cipta'       => $request->unit_cipta,
                    'masa_retensi'     => $request->masa_retensi,
                    'status'           => $status,
                    'tanggal_dibuat'   => $status === 'AKTIF' ? now() : $arsip->tanggal_dibuat,
                    'berakhir_pada'    => $status === 'AKTIF'
                        ? now()->addYears($request->masa_retensi)
                        : $arsip->berakhir_pada,
                ]);

                if ($request->hasFile('dokumen')) {
                    foreach ($request->file('dokumen') as $i => $file) {
                        $path = $file->store('dokumen_arsip', 'public');

                        DokumenArsip::create([
                            'arsip_id'      => $arsip->id,
                            'judul_dokumen' => $request->judul_dokumen[$i] ?? null,
                            'nama_file'     => $file->getClientOriginalName(),
                            'path_file'     => $path,
                            'ukuran'        => $file->getSize(),
                        ]);
                    }
                }
            });

            return redirect()
                ->route('arsiparis.kelola-arsip.index')
                ->with('success', 'Arsip berhasil diperbarui');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat memperbarui arsip');
        }
    }

    public function destroy($id)
    {
        //
    }

    public function nonaktifkan($id)
    {
        Arsip::findOrFail($id)->update([
            'status' => 'NONAKTIF'
        ]);

        return back()->with('success', 'Arsip dinonaktifkan');
    }

    public function detail($id)
    {
        $arsip = Arsip::with('dokumens')->findOrFail($id);

        return response()->json([
            'id' => $arsip->id,
            'judul_berkas' => $arsip->judul_berkas,
            'uraian' => $arsip->uraian,
            'status' => $arsip->status,
            'kode_klasifikasi' => $arsip->kode_klasifikasi,
            'unit_cipta' => $arsip->unit_cipta,
            'masa_retensi' => $arsip->masa_retensi,
            'skkaa' => $arsip->skkaa,
            'dokumens' => $arsip->dokumens
        ]);
    }

    public function hapusDokumen($id)
    {
        try {
            $dokumen = DokumenArsip::findOrFail($id);

            // Hapus file dari storage
            if (Storage::disk('public')->exists($dokumen->path_file)) {
                Storage::disk('public')->delete($dokumen->path_file);
            }

            // Hapus record dari database
            $dokumen->delete();

            return response()->json([
                'success' => true,
                'message' => 'Dokumen berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus dokumen'
            ], 500);
        }
    }
}
