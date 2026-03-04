<?php

namespace App\Http\Controllers;

use App\Models\UploadSkp;
use App\Models\User;
use Illuminate\Http\Request;

class BpkpController extends Controller
{
    public function index(Request $request)
    {
        $unit_kerja = [
            '0000' => 'Semua Inspektorat Utama',
            '8010' => 'Bagian Umum Inspektorat Utama',
            '8100' => 'Inspektorat Wilayah I',
            '8200' => 'Inspektorat Wilayah II',
            '8300' => 'Inspektorat Wilayah III',
        ];

        $unitkerja = $request->unitKerja;

        $year = $request->year;

        if ($year == null) {
            $year = date('Y');
        } else {
            $year = $year;
        }

        if ($unitkerja) {
            if ($unitkerja === '0000') {
                $users = User::where('status', 1)
                    ->orderBy('name')
                    ->get();
            } else {
                $users = User::where('status', 1)
                    ->where('unit_kerja', $unitkerja)
                    ->orderBy('name')
                    ->get();
            }
        } else {
            $users = User::where('status', 1)
                ->orderBy('name')
                ->get();
        }

        $skps = UploadSkp::where('tahun', $year)
            ->whereIn('user_id', $users->pluck('id'))
            ->get()
            ->groupBy('user_id');

        $rekap = $users->map(function ($user) use ($skps) {
            $userSkps = $skps->get($user->id, collect());

            $penetapan = $userSkps->firstWhere('jenis', 'penetapan');
            $penilaian = $userSkps->firstWhere('jenis', 'penilaian');

            $bulanan = collect(range(1, 12))->mapWithKeys(function ($i) use ($userSkps) {
                $bulan = str_pad($i, 2, '0', STR_PAD_LEFT);
                $entry = $userSkps->where('jenis', 'bulanan')->firstWhere('bulan', $bulan);
                return [
                    $bulan => [
                        'id' => $entry?->id,
                        'nilai' => $entry?->status,
                        'status' => $entry?->status,
                    ]
                ];
            });

            $count = 0;
            $tidak_ada = 0;

            if ($penetapan && strtolower($penetapan->status) == 'sudah kirim') $count++;
            if ($penilaian && strtolower($penilaian->status) == 'sudah kirim') $count++;

            foreach ($bulanan as $data) {
                if ($data['status'] && strtolower($data['status']) == 'sudah kirim') {
                    $count++;
                }elseif($data['status'] && strtolower($data['status']) == 'tidak ada'){
                    $tidak_ada++;
                }
            }

            $persentase = round(($count / (14 - $tidak_ada)) * 100, 2);

            return [
                'user' => $user->name,
                'user_id' => $user->id,
                'penetapan' => $penetapan ? [
                    'id' => $penetapan->id,
                    'status' => $penetapan->status,
                ] : null,
                'penilaian' => $penilaian ? [
                    'id' => $penilaian->id,
                    'status' => $penilaian->status,
                ] : null,
                'bulanan' => $bulanan,
                'persentase' => $persentase,
            ];
        });        

        return view('bpkp.index', [
            'skp_all' => $rekap,
            'unit_kerja' => $unit_kerja,
        ]);
    }

    public function show(Request $request, $id)
    {

        $skp = UploadSkp::findOrFail($id);

        return view('bpkp.show', [
            'skp' => $skp,
        ]);
    }
}
