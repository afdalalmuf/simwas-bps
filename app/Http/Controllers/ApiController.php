<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Kompetensi;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApiController extends Controller
{

    public function getKompetensiAPI(Request $request)
    {
        // Get the app-level token from the request header or query parameter
        $apiToken = $request->bearerToken() ?: $request->input('api_token');

        // Validate the token
        if ($apiToken !== config('services.kompetensi_api_token')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized, invalid API token.',
            ], 403); // Forbidden
        }

        // Proceed to validate the other required fields
        // $data = $request->validate([
        //     'niplama' => 'required|string',
        //     'tahun' => 'required|integer',
        // ]);

        // Fetch the data from the database
        try {
            $year = $request->year;
            $tgl_awal = $request->tgl_awal . ' 00:00:00';
            $tgl_akhir = $request->tgl_akhir . ' 23:59:59';

            if ($year == null) {
                $year = date('Y');
            } else {
                $year = $year;
            }

            // 2) Aman pakai nama tabel dari model
            $k = (new Kompetensi)->getTable();  // mis. 'kompetensi' / 'kompetensis'
            $u = 'users';
            $t = 'teknis_kompetensis';
            $j = 'jenis_kompetensis';
            $kat = 'kategori_kompetensis';
            $p = 'master_penyelenggaras';

            // 3) Join & select flat fields
            $record = Kompetensi::query()
                ->leftJoin($u, "$u.id", '=', "$k.pegawai_id")
                ->leftJoin($p, "$p.id", '=', "$k.penyelenggara")
                ->leftJoin($t, "$t.id", '=', "$k.teknis_id")
                ->leftJoin($j, "$j.id", '=', "$t.jenis_id")
                ->leftJoin($kat, "$kat.id", '=', "$j.kategori_id")
                ->select([
                    DB::raw("COALESCE($u.email, '')         as email"),
                    DB::raw("COALESCE($u.nip_lama, '')         as nip_lama"),
                    "$k.skala",
                    "$k.bentuk",
                    DB::raw("COALESCE($kat.kat_simpeg,'')as kategori"),
                    DB::raw("COALESCE($j.jenis_simpeg, '')    as jenis"),
                    "$k.nama_pelatihan",
                    "$k.tgl_mulai",
                    "$k.tgl_selesai",
                    "$k.durasi",
                    DB::raw("
                        CASE 
                            WHEN $p.penyelenggara = 'BPS' THEN '1' 
                            WHEN $p.penyelenggara = 'BPKP' THEN '1' 
                            ELSE 2 
                        END as flaginstansi
                    "),
                    DB::raw("
                        CASE 
                            WHEN $p.penyelenggara = 'BPS' THEN '101' 
                            WHEN $p.penyelenggara = 'BPKP' THEN '150' 
                            ELSE 999 
                        END as kdinstansi
                    "),
                    DB::raw("
                        CASE 
                            WHEN $p.penyelenggara = 'BPKP' THEN 'Badan Pengawasan Keuangan dan Pembangunan (BPKP)' 
                            ELSE $p.penyelenggara 
                        END as nminstansi
                    "),
                    "$k.jumlah_peserta",
                    "$k.ranking",
                    DB::raw("CONCAT('" . url('document/sertifikat') . "/', $k.sertifikat) as sertifikat_url"),
                    "$k.tgl_approve",
                ])
                ->whereBetween("$k.tgl_approve", [$tgl_awal, $tgl_akhir])
                ->where("$kat.id", "!=", "13")
                ->get();

            // Check if there are no records
            if ($record->isEmpty()) {
                return response()->json([
                    'message' => 'Tidak ada diklat pada rentang tersebut',
                ], 405);
            }

            return response()->json($record, 200);
        } catch (\Throwable $e) {
            \Log::error("KipappSyncsController@getActivities failed: " . $e->getMessage());
            return response()->json([
                'message' => 'Server error fetching activities',
                'error' => $e->getMessage(),  // Include the error message for debugging
            ], 500);
        }
    }
}
