<?php

namespace App\Http\Controllers;

use App\Models\UploadSkp;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class AnalisCekSKPController extends Controller
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

        $unit_kerja = [
            '0000' => 'Semua Inspektorat Utama',
            '8010' => 'Bagian Umum Inspektorat Utama',
            '8100' => 'Inspektorat Wilayah I',
            '8200' => 'Inspektorat Wilayah II',
            '8300' => 'Inspektorat Wilayah III',
        ];

        $this->authorize('analis_sdm');
        $id_unitkerja = auth()->user()->unit_kerja;

        $year = $request->year;

        if ($year == null) {
            $year = date('Y');
        } else {
            $year = $year;
        }

        $tahun = $year;
        $id_unitkerja = auth()->user()->unit_kerja;

        if ($id_unitkerja == '8010') {
            if ($request->unitKerja) {
                if ($request->unitKerja === '0000') {
                    $users = User::where('status', 1)
                        ->orderBy('name')
                        ->get();
                } else {
                    $users = User::where('status', 1)
                        ->where('unit_kerja', $request->unitKerja)
                        ->orderBy('name')
                        ->get();
                }
            } else {
                $users = User::where('status', 1)
                    ->orderBy('name')
                    ->get();
            }
        } else {
            $users = User::where('unit_kerja', $id_unitkerja)
                ->where('status', 1)
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
                        'tgl_upload' => $entry?->updated_at
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

        return view('analis-sdm.cek-skp.index', [
            'skp_all' => $rekap,
            'kategori' => $kategori,
            'rating' => $rating,
            'tahun' => $tahun,
            'unit_kerja' => $unit_kerja,
        ]);
    }

    public function store(Request $request)
    {
        $status_skp = $request->input('status_skp', 'tidak aktif');

        if (auth()->user()->unit_kerja === '8010') {
            $rules = [
                'tahun' => 'required',
                'jenis' => 'required',
            ];

            if ($status_skp === 'aktif') {
                if ($request->jenis == 'penetapan') {
                    $rules['file'] = ['required', 'file', 'mimes:pdf', 'max:10240'];
                } else {
                    $rules['file'] = ['required', 'file', 'mimes:pdf', 'max:10240'];
                    $rules['rating_hasil_kerja'] = 'required';
                    $rules['rating_perilaku_kerja'] = 'required';
                    $rules['predikat_kinerja'] = 'required';
                }
            } else {
                if ($request->jenis == 'penetapan') {
                    $rules['file'] = ['required', 'file', 'mimes:pdf', 'max:10240'];
                } elseif ($request->jenis == 'penilaian') {
                    $rules['file'] = ['required', 'file', 'mimes:pdf', 'max:10240'];
                    $rules['rating_hasil_kerja'] = 'required';
                    $rules['rating_perilaku_kerja'] = 'required';
                    $rules['predikat_kinerja'] = 'required';
                } else {
                    $rules['file'] = ['nullable', 'file', 'mimes:pdf', 'max:10240'];
                }
            }
            $messages = [
                'required' => ':attribute harus diisi',
                'file.max' => 'Ukuran file maksimal 10MB',
                'mimes' => 'Format file harus pdf'
            ];
        } else {
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
        }

        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $validateData = $request->validate($rules);

        $user_id = $request->user_id;
        $jenis = $request->jenis;

        if ($request->file('file')) {
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
                    'status' => 'Sudah Kirim',
                ]);
            } elseif ($jenis == "penilaian") {
                UploadSkp::create([
                    'tahun' => $request->tahun,
                    'jenis' => $jenis,
                    'skp_path' => $skp_path,
                    'user_id' => $user_id,
                    'status' => 'Sudah Kirim',
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
                    'status' => 'Sudah Kirim',
                    'kat_rating_hasil_kerja' => $request->rating_hasil_kerja,
                    'kat_rating_perilaku_kerja' => $request->rating_perilaku_kerja,
                    'predikat_kinerja' => $request->predikat_kinerja,
                    'bulan' => $request->bulan,
                ]);
            }
        } else {
            UploadSkp::create([
                'tahun' => $request->tahun,
                'jenis' => $jenis,
                'skp_path' => '-',
                'user_id' => $user_id,
                'status' => 'Tidak Ada',
                'bulan' => $request->bulan,
            ]);
        }
    }

    public function show(Request $request, $id)
    {

        $skp = UploadSkp::findOrFail($id);

        return view('analis-sdm.cek-skp.show', [
            'skp' => $skp,
        ]);
    }

    public function verifikasi(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:Sudah Kirim,Ditolak',
            'catatan' => 'nullable|string|max:255',
            'file' => ['file', 'mimes:pdf', 'max:10240'],
        ]);

        // Jika ditolak, catatan wajib
        if ($request->status == 'Ditolak' && !$request->catatan) {
            return back()->withErrors(['catatan' => 'Catatan wajib diisi jika dokumen ditolak.']);
        }

        $skp = UploadSkp::findOrFail($id);

        if ($request->file('file')) {
            $jenis = $request->jenis;
            $file = $request->file('file');
            $fileName = time() . '-skp-' . $jenis . '-' . $file->getClientOriginalExtension();
            $path = public_path('storage/skp');
            $file->move($path, $fileName);
            $skp_path = 'storage/skp/' . $fileName;

            $skp->skp_path = $skp_path;
            $skp->status = $request->status;
            $skp->save();
        } else {
            $skp->status = $request->status;
            $skp->catatan = $request->catatan;
            $skp->save();
        }

        return redirect()->route('analis-sdm.cek-skp.index')->with('success', 'Verifikasi berhasil disimpan.');
    }

    public function export_skp(Request $request)
    {
        $year = $request->year;
        $unit_kerja = $request->unitKerja;

        if ($year == null) {
            $year = date('Y');
        } else {
            $year = $year;
        }

        if ($unit_kerja == null) {
            $unit_kerja = '0000';
        } else {
            $unit_kerja = $unit_kerja;
        }

        // Ambil semua user + relasi SKP untuk tahun tertentu
        $users = User::with(['skp' => function ($q) use ($year) {
            $q->where('tahun', $year);
        }])->where('status', '1')
            ->whereIn('unit_kerja', ['8010', '8100', '8200', '8300'])
            ->orderBy('unit_kerja', 'asc')
            ->orderBy('name', 'asc')
            ->get();

        // List bulan
        $bulanList = [
            '01' => 'Januari',
            '02' => 'Februari',
            '03' => 'Maret',
            '04' => 'April',
            '05' => 'Mei',
            '06' => 'Juni',
            '07' => 'Juli',
            '08' => 'Agustus',
            '09' => 'September',
            '10' => 'Oktober',
            '11' => 'November',
            '12' => 'Desember',
        ];

        //List Kategori dan Rating
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

        // Buat header Excel
        $header = ['No', 'Nama Pegawai', 'Unit Kerja'];
        foreach ($bulanList as $kode => $namaBulan) {
            $header[] = $namaBulan . ' - Hasil Kerja';
            $header[] = $namaBulan . ' - Perilaku Kerja';
            $header[] = $namaBulan . ' - Predikat';
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // ==== HEADER ====
        // Kolom pertama: No + Nama Pegawai (row 1 & 2 digabung ke bawah)
        $sheet->setCellValue('A1', 'No');
        $sheet->mergeCells('A1:A2');

        $sheet->setCellValue('B1', 'Nama Pegawai');
        $sheet->mergeCells('B1:B2');

        $sheet->setCellValue('C1', 'Unit Kerja');
        $sheet->mergeCells('C1:C2');

        // Mulai dari kolom C
        $colIndex = 4;

        foreach ($bulanList as $kode => $namaBulan) {
            // Tentukan kolom awal & akhir untuk bulan ini
            $colStart = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex);
            $colEnd   = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex + 2);

            // Merge kolom bulan
            $sheet->setCellValue($colStart . '1', $namaBulan);
            $sheet->mergeCells("{$colStart}1:{$colEnd}1");

            // Isi sub-kolom (row 2)
            $sheet->setCellValue($colStart . '2', 'Hasil Kerja');
            $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex + 1) . '2', 'Perilaku Kerja');
            $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex + 2) . '2', 'Predikat');

            $colIndex += 3;
        }

        // ==== DATA ====
        $rowIndex = 3;
        $no = 1;

        foreach ($users as $user) {
            $sheet->setCellValue('A' . $rowIndex, $no);
            $sheet->setCellValue('B' . $rowIndex, $user->name);
            $sheet->setCellValue('C' . $rowIndex, $user->unit_kerja);

            $colIndex = 4;
            foreach (array_keys($bulanList) as $bulan) {
                $skp = $user->skp->firstWhere('bulan', $bulan);
                $hasil   = $skp->kat_rating_hasil_kerja ?? null;
                $perilaku = $skp->kat_rating_perilaku_kerja ?? null;
                $predikat = $skp->predikat_kinerja ?? null;

                if ($skp) {
                    $sheet->setCellValueByColumnAndRow($colIndex, $rowIndex, $kategori[$hasil] ?? '-');
                    $sheet->setCellValueByColumnAndRow($colIndex + 1, $rowIndex, $kategori[$perilaku] ?? '-');
                    $sheet->setCellValueByColumnAndRow($colIndex + 2, $rowIndex, $rating[$predikat] ?? '-');
                } else {
                    $sheet->setCellValueByColumnAndRow($colIndex, $rowIndex, 'Belum upload');
                    $sheet->setCellValueByColumnAndRow($colIndex + 1, $rowIndex, 'Belum upload');
                    $sheet->setCellValueByColumnAndRow($colIndex + 2, $rowIndex, 'Belum upload');
                }

                $colIndex += 3;
            }

            $rowIndex++;
            $no++;
        }

        // Autosize
        foreach (range('A', $sheet->getHighestColumn()) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Export file
        $timestamp = now()->format('Y-m-d_H-i-s');
        $filename = "Rekap_Nilai_SKP_{$year}_{$timestamp}.xlsx";

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment;filename=\"{$filename}\"");
        header('Cache-Control: max-age=0');

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        die;
    }
}
