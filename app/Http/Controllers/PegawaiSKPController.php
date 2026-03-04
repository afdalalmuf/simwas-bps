<?php

namespace App\Http\Controllers;

use App\Models\TimKerja;
use App\Models\UploadSkp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PegawaiSKPController extends Controller
{
    protected $months = [
        1 => 'Januari',
        2 => 'Februari',
        3 => 'Maret',
        4 => 'April',
        5 => 'Mei',
        6 => 'Juni',
        7 => 'Juli',
        8 => 'Agustus',
        9 => 'September',
        10 => 'Oktober',
        11 => 'November',
        12 => 'Desember'
    ];
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $kategori = [
            'A' => 'Sangat Baik',
            'B' => 'Baik',
            'C' => 'Butuh Perbaikan',
            'D' => 'Kurang',
            'E' => 'Sangat Kurang',
        ];

        $rating = [
            'A' => 'Diatas Ekspektasi',
            'B' => 'Sesuai Ekspektasi',
            'C' => 'Dibawah Ekspektasi',
        ];

        $year = $request->year;

        if ($year == null) {
            $year = date('Y');
        } else {
            $year = $year;
        }

        $tahun = $year;
        $unit = $request->unit;

        $skp_penetapan = UploadSkp::where('tahun', $year)
            ->where('user_id', auth()->user()->id)
            ->where('jenis', 'penetapan')
            ->first();

        $skp_penilaian = UploadSkp::where('tahun', $year)
            ->where('user_id', auth()->user()->id)
            ->where('jenis', 'penilaian')
            ->first();

        $data_skp = UploadSkp::where('tahun', $year)
            ->where('user_id', auth()->user()->id)
            ->where('jenis', 'bulanan')
            ->get()
            ->keyBy('bulan');

        $skp_bulanan = collect(range(1, 12))->mapWithKeys(function ($i) use ($data_skp) {
            $bulanKey = str_pad($i, 2, '0', STR_PAD_LEFT);
            $namaBulan = $this->months[$i];

            if ($data_skp->has($bulanKey)) {
                $item = $data_skp[$bulanKey];
                return [$namaBulan => [
                    'id' => $item->id,
                    // 'rating_hasil_kerja' => number_format((float) $item->rating_hasil_kerja, 2, '.', ''),
                    // 'rating_perilaku_kerja' => number_format((float) $item->rating_perilaku_kerja, 2, '.', ''),
                    'kat_rating_hasil_kerja' => $item->kat_rating_hasil_kerja,
                    'kat_rating_perilaku_kerja' => $item->kat_rating_perilaku_kerja,
                    'predikat_kinerja' => $item->predikat_kinerja,
                    'status' => $item->status,
                    'bulan' => $item->bulan,
                    'catatan' => $item->catatan,
                    'updated_at' => $item->updated_at,
                ]];
            } else {
                return [$namaBulan => [
                    'month' => $bulanKey,
                    'status' => null,
                ]];
            }
        });

        return view('pegawai.upload-skp.index', [
            'type_menu' => 'upload-skp',
            'unit' => $unit,
            'year' => $year,
            'tahun' => $tahun,
            'skp_penetapan' => $skp_penetapan,
            'skp_penilaian' => $skp_penilaian,
            'skp_bulanan' => $skp_bulanan,
            'kategori' => $kategori,
            'rating' => $rating,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = [
            'tahun' => 'required',
            'jenis' => 'required',
            'file' => ['required', 'file', 'mimes:pdf', 'max:10240'],
        ];

        $messages = [
            'required' => ':attribute harus diisi',
            'file.max' => 'Ukuran file maksimal 10MB',
            'mimes' => 'Format file harus pdf'
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $validateData = $request->validate($rules);

        $user_id = auth()->user()->id;
        $jenis = $request->jenis;

        $file = $request->file('file');
        $fileName = time() . '-skp-' . $jenis . '-' . $file->getClientOriginalExtension();
        $path = public_path('storage/skp');
        $file->move($path, $fileName);
        $skp_path = 'storage/skp/' . $fileName;

        if ($jenis == "penetapan") {
            UploadSkp::create([
                'tahun' => $validateData['tahun'],
                'jenis' => $validateData['jenis'],
                'skp_path' => $skp_path,
                'user_id' => $user_id,
                'status' => 'Diperiksa',
            ]);
        } elseif ($jenis == "penilaian") {
            UploadSkp::create([
                'tahun' => $request->tahun,
                'jenis' => $jenis,
                'skp_path' => $skp_path,
                'user_id' => $user_id,
                'status' => 'Diperiksa',
                'kat_rating_hasil_kerja' => $request->rating_hasil_kerja,
                'kat_rating_perilaku_kerja' => $request->rating_perilaku_kerja,
                'predikat_kinerja' => $request->predikat_kinerja,
            ]);
        } else {
            $cek = UploadSkp::where('tahun', $request->tahun)
                ->where('bulan', $request->bulan)
                ->where('user_id', $user_id)
                ->exists();
            if ($cek) {
                return redirect()->back()->with('error', 'Data SKP untuk tahun dan bulan ini sudah ada.');
            }

            UploadSkp::create([
                'tahun' => $request->tahun,
                'jenis' => $jenis,
                'skp_path' => $skp_path,
                'user_id' => $user_id,
                'status' => 'Diperiksa',
                'kat_rating_hasil_kerja' => $request->rating_hasil_kerja,
                'kat_rating_perilaku_kerja' => $request->rating_perilaku_kerja,
                'predikat_kinerja' => $request->predikat_kinerja,
                'bulan' => $request->bulan,
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function viewSKP($id)
    {
        $skp = UploadSkp::findOrFail($id);
        $file = public_path($skp->skp_path);
        if ($file == public_path('')) {
            return redirect()->back()->with('error', 'File tidak ditemukan');
        } else {
            return response()->file($file);
        }
    }

    public function update_penetapan(Request $request, $id)
    {
        $rules = [
            'file' => ['required', 'file', 'mimes:pdf', 'max:10240'],
        ];

        $messages = [
            'file.max' => 'Ukuran file maksimal 10MB',
            'mimes' => 'Format file harus pdf'
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $request->validate($rules);

        $uploadSKP = UploadSkp::findOrFail($id);

        $file = $request->file('file');
        $fileName = time() . '-skp-penetapan-' . $file->getClientOriginalExtension();
        $path = public_path('storage/skp');
        $file->move($path, $fileName);
        $skp_path = 'storage/skp/' . $fileName;

        $uploadSKP->update([
            'skp_path' => $skp_path,
            'status' => 'Diperiksa',
        ]);

        $request->session()->put('status', 'Berhasil menambahkan Tugas Tim Kerja.');
        $request->session()->put('alert-type', 'success');

        return redirect()->back()->with('success', 'SKP Penetapan Berhasil Diedit');
    }

    public function update_penilaian(Request $request, $id)
    {
        $uploadSKP = UploadSkp::findOrFail($id);

        if ($request->file('file')) {
            $file = $request->file('file');
            $fileName = time() . '-skp-penilaian-' . $file->getClientOriginalExtension();
            $path = public_path('storage/skp');
            $file->move($path, $fileName);
            $skp_path = 'storage/skp/' . $fileName;
            $uploadSKP->update([
                'skp_path' => $skp_path,
                'kat_rating_hasil_kerja' => $request->rating_hasil_kerja,
                'kat_rating_perilaku_kerja' => $request->rating_perilaku_kerja,
                'predikat_kinerja' => $request->predikat_kinerja,
                'status' => 'Diperiksa',
            ]);
        } else {
            $uploadSKP->update([
                'kat_rating_hasil_kerja' => $request->rating_hasil_kerja,
                'kat_rating_perilaku_kerja' => $request->rating_perilaku_kerja,
                'predikat_kinerja' => $request->predikat_kinerja,
                'status' => 'Diperiksa',
            ]);
        }

        return redirect()->back()->with('success', 'SKP Penetapan Berhasil Diedit');
    }

    public function skp_tim(Request $request)
    {
        $year = $request->year;

        if ($year == null) {
            $year = date('Y');
        } else {
            $year = $year;
        }

        $tahun = $year;
        $id_unitkerja = auth()->user()->unit_kerja;

        $id = TimKerja::where('id_ketua', auth()->user()->id)->where('tahun', $year)->first();
        $tim = TimKerja::with(['rencanaKerja.pelaksana.user'])->find($id->id_timkerja);

        // Ambil semua user (anggota) dari pelaksana tugas
        $anggota = collect();

        foreach ($tim->rencanakerja as $rk) {
            foreach ($rk->pelaksana as $pt) {
                $anggota->push($pt->user);
            }
        }

        $anggota = $anggota->unique('id')->values();
        $userIds = $anggota->pluck('id');

        $skps = UploadSkp::where('tahun', $year)
            ->whereIn('user_id', $userIds)
            ->get()
            ->groupBy('user_id');

        $rekap = $anggota->map(function ($user) use ($skps) {
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
                        'tgl_upload' => $entry?->updated_at
                    ]
                ];
            });

            $count = 0;
            $tidak_ada = 0;

            if ($penetapan && strtolower($penetapan->status) !== 'belum kirim') $count++;
            if ($penilaian && strtolower($penilaian->status) !== 'belum kirim') $count++;

            foreach ($bulanan as $data) {
                if ($data['status'] && strtolower($data['status']) !== 'tidak ada') {
                    $count++;
                } elseif ($data['status'] && strtolower($data['status']) == 'tidak ada') {
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
                    'tgl_upload' => $penetapan->updated_at,
                ] : null,
                'penilaian' => $penilaian ? [
                    'id' => $penilaian->id,
                    'status' => $penilaian->status,
                    'tgl_upload' => $penilaian->updated_at,
                ] : null,
                'bulanan' => $bulanan,
                'persentase' => $persentase,
            ];
        });

        return view('pegawai.upload-skp.skptim', [
            'skp_all' => $rekap,
            'tahun' => $tahun,
            'tim' => $tim,
        ]);
    }
}
