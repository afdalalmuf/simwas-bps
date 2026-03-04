<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\SpjDiklat;
use Illuminate\Http\Request;
use App\Models\RencanaDiklat;
use App\Models\MasterRekening;
use App\Models\SpjVerification;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Validator;

class SpjDiklatController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $year = $request->year;

        if ($year == null) {
            $year = date('Y');
        } else {
            $year = $year;
        }
        $currentYear = date('Y');
        $diklat = RencanaDiklat::where('id_pegawai', auth()->user()->id)
            ->whereIn('status', ['Disetujui', 'Selesai', 'Dibatalkan'])
            ->orderBy('start_date', 'asc')
            ->get();

        return view('pegawai.spj-diklat.index', [
            'year' => $year,
            'diklat' => $diklat,
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
            'file_spd' => 'file|mimes:pdf|max:10240',
            'jumlah_hari' => 'numeric',
            'uh_diklat' => 'numeric',
            'nominal_hotel' => 'numeric',
            'file_hotel' => 'file|mimes:pdf|max:10240',
            // 'file_trans_dalkot_berangkat' => 'file|mimes:pdf|max:10240',
            // 'file_trans_dalkot_pulang' => 'file|mimes:pdf|max:10240',
            'km_berangkat' => 'numeric',
            'nominal_trans_lukot_berangkat' => 'numeric',
            'file_trans_lukot_berangkat' => 'file|mimes:pdf|max:10240',
            'nominal_trans_lukot_pulang' => 'numeric',
            'file_trans_lukot_pulang' => 'file|mimes:pdf|max:10240',
            'file_laporan_perjadin' => 'file|mimes:pdf|max:10240',
            'file_fpp' => 'file|mimes:pdf|max:10240',
            'file_kak' => 'file|mimes:pdf|max:10240',
            'file_pemanggilan' => 'file|mimes:pdf|max:10240',
        ];

        $messages = [
            'required' => ':attribute harus diisi',
            'file.max' => ':attribute ukuran file maksimal 10MB',
            'mimes' => 'Format file harus pdf',
            'numeric' => ':attribute harus angka',
        ];

        $attributes = [
            'file_st' => 'Bukti ST',
            'file_spd' => 'Bukti SPD',
            'nominal_hotel' => 'Nominal Hotel',
            'file_hotel' => 'Bukti Hotel',
            'jumlah_hari' => 'Hari Diklat',
            'uh_diklat' => 'Uang Harian Diklat',
            // 'file_trans_dalkot_berangkat' => 'Bukti Transport Berangkat',
            // 'file_trans_dalkot_pulang' => 'Bukti Transport Pulang',
            'km_berangkat' => 'Jarak',
            'nominal_trans_lukot_berangkat' => 'Nominal Transport Berangkat',
            'nominal_trans_lukot_pulang' => 'Nominal Transport Berangkat',
            'file_trans_lukot_berangkat' => 'Bukti Transport Berangkat',
            'file_trans_lukot_pulang' => 'Bukti Transport Pulang',
            'file_laporan_perjadin' => 'Dokumen Laporan Perjadin',
            'file_fpp' => 'Dokumen FPP',
            'file_kak' => 'Dokumen KAK',
            'file_pemanggilan' => 'Surat Pemanggilan',
        ];

        $validator = Validator::make($request->all(), $rules, $messages, $attributes);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $validateData = $request->validate($rules);

        // Default path values to prevent undefined variable error
        $st_path = null;
        $spd_path = null;
        $hotel_path = null;
        $transport_berangkat_path = null;
        $transport_pulang_path = null;
        $translok_path = null;
        $laporan_perjadin_path = null;
        $fpp_path = null;
        $kak_path = null;
        $surat_pemanggilan_path = null;


        //File Surat Tugas
        if ($request->hasFile('file_st')) {
            $pathST = public_path('storage/spj-diklat/st');
            if (!file_exists($pathST)) {
                mkdir($pathST, 0755, true); // buat folder dengan izin 755, true = recursive
            }
            $fileST = $request->file('file_st');
            $fileSTName = time() . '-Surat-Tugas.' . $fileST->getClientOriginalExtension();
            $fileST->move($pathST, $fileSTName);
            $st_path = 'storage/spj-diklat/st/' . $fileSTName;
        }

        //File SPD
        if ($request->hasFile('file_spd')) {
            $pathSPD = public_path('storage/spj-diklat/spd');
            if (!file_exists($pathSPD)) {
                mkdir($pathSPD, 0755, true); // buat folder dengan izin 755, true = recursive
            }
            $fileSpd = $request->file('file_spd');
            $fileSpdName = time() . '-SPD.' . $fileSpd->getClientOriginalExtension();
            $fileSpd->move($pathSPD, $fileSpdName);
            $spd_path = 'storage/spj-diklat/spd/' . $fileSpdName;
        }

        //File Hotel        
        if ($request->ada_hotel == 'ya') {
            if ($request->hasFile('file_hotel')) {
                $pathHotel = public_path('storage/spj-diklat/hotel');
                if (!file_exists($pathHotel)) {
                    mkdir($pathHotel, 0755, true); // buat folder dengan izin 755, true = recursive
                }
                $fileHotel = $request->file('file_hotel');
                $fileHotelName = time() . '-Hotel.' . $fileHotel->getClientOriginalExtension();
                $fileHotel->move($pathHotel, $fileHotelName);
                $hotel_path = 'storage/spj-diklat/hotel/' . $fileHotelName;
            }
        }

        //File Transportasi
        if ($request->tipe_perjadin == '1') {
            // if ($request->hasFile('file_trans_dalkot_berangkat')) {
            //     $pathTransportBerangkat = public_path('storage/spj-diklat/transport');
            //     if (!file_exists($pathTransportBerangkat)) {
            //         mkdir($pathTransportBerangkat, 0755, true); // buat folder dengan izin 755, true = recursive
            //     }
            //     $fileTransportBerangkat = $request->file('file_trans_dalkot_berangkat');
            //     $fileTransportBerangkatName = time() . '-Transport.' . $fileTransportBerangkat->getClientOriginalExtension();
            //     $fileTransportBerangkat->move($pathTransportBerangkat, $fileTransportBerangkatName);
            //     $transport_berangkat_path = 'storage/spj-diklat/transport/' . $fileTransportBerangkatName;
            // }

            // if ($request->hasFile('file_trans_dalkot_pulang')) {
            //     $pathTransportPulang = public_path('storage/spj-diklat/transport');
            //     $fileTransportPulang = $request->file('file_trans_dalkot_pulang');
            //     $fileTransportPulangName = time() . '-Transport.' . $fileTransportPulang->getClientOriginalExtension();
            //     $fileTransportPulang->move($pathTransportPulang, $fileTransportPulangName);
            //     $transport_pulang_path = 'storage/spj-diklat/transport/' . $fileTransportPulangName;
            // }
            if ($request->hasFile('file_trans_dalkot')) {
                $pathTransportLokal = public_path('storage/spj-diklat/transport');
                if (!file_exists($pathTransportLokal)) {
                    mkdir($pathTransportLokal, 0755, true); // buat folder dengan izin 755, true = recursive
                }
                $fileTransportBerangkat = $request->file('file_trans_dalkot');
                $fileTransportBerangkatName = time() . '-Transport-Lokal.' . $fileTransportBerangkat->getClientOriginalExtension();
                $fileTransportBerangkat->move($pathTransportLokal, $fileTransportBerangkatName);
                $translok_path = 'storage/spj-diklat/transport/' . $fileTransportBerangkatName;
            }
        } elseif ($request->tipe_perjadin == '2') {
            if ($request->hasFile('file_trans_lukot_berangkat')) {
                $pathTransportBerangkat = public_path('storage/spj-diklat/transport');
                if (!file_exists($pathTransportBerangkat)) {
                    mkdir($pathTransportBerangkat, 0755, true); // buat folder dengan izin 755, true = recursive
                }
                $fileTransportBerangkat = $request->file('file_trans_lukot_berangkat');
                $fileTransportBerangkatName = time() . '-Transport.' . $fileTransportBerangkat->getClientOriginalExtension();
                $fileTransportBerangkat->move($pathTransportBerangkat, $fileTransportBerangkatName);
                $transport_berangkat_path = 'storage/spj-diklat/transport/' . $fileTransportBerangkatName;
            }

            if ($request->hasFile('file_trans_lukot_pulang')) {
                $pathTransportPulang = public_path('storage/spj-diklat/transport');
                $fileTransportPulang = $request->file('file_trans_lukot_pulang');
                $fileTransportPulangName = time() . '-Transport.' . $fileTransportPulang->getClientOriginalExtension();
                $fileTransportPulang->move($pathTransportPulang, $fileTransportPulangName);
                $transport_pulang_path = 'storage/spj-diklat/transport/' . $fileTransportPulangName;
            }
        }

        //File Laporan Perjadin
        if ($request->hasFile('file_laporan_perjadin')) {
            $pathLaporanPerjadin = public_path('storage/spj-diklat/laporan');
            if (!file_exists($pathLaporanPerjadin)) {
                mkdir($pathLaporanPerjadin, 0755, true); // buat folder dengan izin 755, true = recursive
            }
            $fileLaporanPerjadin = $request->file('file_laporan_perjadin');
            $fileLaporanPerjadinName = time() . '-Laporan.' . $fileLaporanPerjadin->getClientOriginalExtension();
            $fileLaporanPerjadin->move($pathLaporanPerjadin, $fileLaporanPerjadinName);
            $laporan_perjadin_path = 'storage/spj-diklat/laporan/' . $fileLaporanPerjadinName;
        }

        //File FPP
        if ($request->hasFile('file_fpp')) {
            $pathFPP = public_path('storage/spj-diklat/laporan');
            if (!file_exists($pathFPP)) {
                mkdir($pathFPP, 0755, true); // buat folder dengan izin 755, true = recursive
            }
            $fileFPP = $request->file('file_fpp');
            $fileFPPName = time() . '-Laporan.' . $fileFPP->getClientOriginalExtension();
            $fileFPP->move($pathFPP, $fileFPPName);
            $fpp_path = 'storage/spj-diklat/laporan/' . $fileFPPName;
        }

        //File KAK
        if ($request->hasFile('file_kak')) {
            $pathKAKPerjadin = public_path('storage/spj-diklat/laporan');
            if (!file_exists($pathKAKPerjadin)) {
                mkdir($pathKAKPerjadin, 0755, true); // buat folder dengan izin 755, true = recursive
            }
            $fileKAKPerjadin = $request->file('file_kak');
            $fileKAKPerjadinName = time() . '-Laporan.' . $fileKAKPerjadin->getClientOriginalExtension();
            $fileKAKPerjadin->move($pathKAKPerjadin, $fileKAKPerjadinName);
            $kak_path = 'storage/spj-diklat/laporan/' . $fileKAKPerjadinName;
        }

        //File Surat Pemanggilan
        if ($request->hasFile('file_pemanggilan')) {
            $pathSuratPemanggilan = public_path('storage/spj-diklat/laporan');
            if (!file_exists($pathSuratPemanggilan)) {
                mkdir($pathSuratPemanggilan, 0755, true); // buat folder dengan izin 755, true = recursive
            }
            $fileSuratPemanggilan = $request->file('file_pemanggilan');
            $fileSuratPemanggilanName = time() . '-Laporan.' . $fileSuratPemanggilan->getClientOriginalExtension();
            $fileSuratPemanggilan->move($pathSuratPemanggilan, $fileSuratPemanggilanName);
            $surat_pemanggilan_path = 'storage/spj-diklat/laporan/' . $fileSuratPemanggilanName;
        }

        $data = [
            'rencanaDiklat_id' => $request->id_diklat,
            'no_st' => $request->no_st,
            'tgl_mulai_st' => $request->tgl_mulai,
            'tgl_selesai_st' => $request->tgl_selesai,
            'st_path' => $st_path,
            'no_spd' => $request->no_spd,
            'tgl_spd' => $request->tgl_spd,
            'spd_path' => $spd_path,
            'tipe_perjadin' => $request->tipe_perjadin,
            'hari_diklat' => $request->jumlah_hari,
            'uang_diklat' => $request->uh_diklat,
            'rekening_id' => $request->no_rek,
            'laporan_perjadin_path' => $laporan_perjadin_path,
            'surat_pemanggilan_path' => $surat_pemanggilan_path,
            'kak_path' => $kak_path,
            'fpp_path' => $fpp_path,
            'status' => 'Draft',
        ];

        if ($request->ada_hotel === 'ya') {
            $data['nominal_hotel'] = $request->input('nominal_hotel');
            $data['hotel_path'] = $hotel_path;
        } else {
            $data['nominal_hotel'] = 0;
        }

        if ($request->tipe_perjadin === '1') {
            // $data['nominal_transport_berangkat'] = 170000;
            // $data['nominal_transport_pulang'] = 170000;
            // $data['tgl_transport_berangkat'] = $request->input('tgl_trans_dalkot_berangkat');
            // $data['tgl_transport_pulang'] = $request->input('tgl_trans_dalkot_pulang');
            // $data['transport_berangkat_path'] = $transport_berangkat_path;
            // $data['transport_pulang_path'] = $transport_pulang_path;
            $data['nominal_translok'] = 170000 * ($request->input('jumlah_hari_transport'));
            $data['translok_path'] = $translok_path;
            $data['jarak'] = NULL;
        } else {
            if ($request->kat_lukot === '1') {
                $data['jarak'] = $request->input('km_berangkat');
            }
            $data['nominal_transport_berangkat'] = $request->input('nominal_trans_lukot_berangkat');
            $data['nominal_transport_pulang'] = $request->input('nominal_trans_lukot_pulang');
            $data['tgl_transport_berangkat'] = $request->input('tgl_trans_lukot_berangkat');
            $data['tgl_transport_pulang'] = $request->input('tgl_trans_lukot_pulang');
            $data['transport_berangkat_path'] = $transport_berangkat_path;
            $data['transport_pulang_path'] = $transport_pulang_path;
        }

        if ($request->ada_berangkat === 'ya') {
            $data['uang_harian_berangkat'] = $request->input('uh_h_1');
        } else {
            $data['uang_harian_berangkat'] = 0;
        }

        if ($request->ada_pulang === 'ya') {
            $data['uang_harian_pulang'] = $request->input('uh_h1');
        } else {
            $data['uang_harian_pulang'] = 0;
        }

        $spj_store = SpjDiklat::create($data);

        return response()->json(['success' => true, 'id' => $spj_store->id_spjDiklat]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $tahun_sekarang = date('Y');
        $diklat = RencanaDiklat::where('id', $id)->first();

        //Rekening
        $rekening = MasterRekening::where('id_pegawai', auth()->user()->id)->get();

        return view('pegawai.spj-diklat.create', [
            'tahun_sekarang' => $tahun_sekarang,
            'diklat' => $diklat,
            'rekening' => $rekening,
        ]);
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
        $spj = SpjDiklat::findOrFail($id);
        $rules = [
            'file_spd' => 'file|mimes:pdf|max:10240',
            'jumlah_hari' => 'numeric',
            'uh_diklat' => 'numeric',
            'nominal_hotel' => 'numeric',
            'file_hotel' => 'file|mimes:pdf|max:10240',
            'file_trans_dalkot' => 'file|mimes:pdf|max:10240',
            // 'file_trans_dalkot_pulang' => 'file|mimes:pdf|max:10240',
            'nominal_trans_lukot_berangkat' => 'numeric',
            'file_trans_lukot_berangkat' => 'file|mimes:pdf|max:10240',
            'nominal_trans_lukot_pulang' => 'numeric',
            'file_trans_lukot_pulang' => 'file|mimes:pdf|max:10240',
            'file_laporan_perjadin' => 'file|mimes:pdf|max:10240',
            'file_fpp' => 'file|mimes:pdf|max:10240',
            'file_kak' => 'file|mimes:pdf|max:10240',
            'file_pemanggilan' => 'file|mimes:pdf|max:10240',
        ];

        $messages = [
            'required' => ':attribute harus diisi',
            'file.max' => ':attribute ukuran file maksimal 10MB',
            'mimes' => 'Format file harus pdf',
            'numeric' => ':attribute harus angka',
        ];

        $attributes = [
            'file_st' => 'Bukti ST',
            'file_spd' => 'Bukti SPD',
            'nominal_hotel' => 'Nominal Hotel',
            'file_hotel' => 'Bukti Hotel',
            'jumlah_hari' => 'Hari Diklat',
            'uh_diklat' => 'Uang Harian Diklat',
            'file_trans_dalkot' => 'Bukti Transport Lokal',
            // 'file_trans_dalkot_pulang' => 'Bukti Transport Pulang Dalam Kota',
            'km_berangkat' => 'Jarak',
            'nominal_trans_lukot_berangkat' => 'Nominal Transport Berangkat',
            'nominal_trans_lukot_pulang' => 'Nominal Transport Pulang',
            'file_trans_lukot_berangkat' => 'Bukti Transport Berangkat Luar Kota',
            'file_trans_lukot_pulang' => 'Bukti Transport Pulang Luar Kota',
            'file_laporan_perjadin' => 'Dokumen Laporan Perjadin',
            'file_fpp' => 'Dokumen FPP',
            'file_kak' => 'Dokumen KAK',
            'file_pemanggilan' => 'Surat Pemanggilan',
        ];

        $validator = Validator::make($request->all(), $rules, $messages, $attributes);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $validateData = $request->validate($rules);

        // Default path values to prevent undefined variable error
        $st_path = null;
        $spd_path = null;
        $hotel_path = null;
        $transport_berangkat_path = null;
        $transport_pulang_path = null;
        $translok_path = null;
        $laporan_perjadin_path = null;
        $fpp_path = null;
        $kak_path = null;
        $surat_pemanggilan_path = null;


        //File Surat Tugas
        if ($request->hasFile('file_st')) {
            $pathST = public_path('storage/spj-diklat/st');
            if (!file_exists($pathST)) {
                mkdir($pathST, 0755, true); // buat folder dengan izin 755, true = recursive
            }
            $fileST = $request->file('file_st');
            $fileSTName = time() . '-Surat-Tugas.' . $fileST->getClientOriginalExtension();
            $fileST->move($pathST, $fileSTName);
            $st_path = 'storage/spj-diklat/st/' . $fileSTName;
        } elseif ($spj->st_path) {
            $st_path = $spj->st_path;
        }

        //File SPD
        if ($request->hasFile('file_spd')) {
            $pathSPD = public_path('storage/spj-diklat/spd');
            if (!file_exists($pathSPD)) {
                mkdir($pathSPD, 0755, true); // buat folder dengan izin 755, true = recursive
            }
            $fileSpd = $request->file('file_spd');
            $fileSpdName = time() . '-SPD.' . $fileSpd->getClientOriginalExtension();
            $fileSpd->move($pathSPD, $fileSpdName);
            $spd_path = 'storage/spj-diklat/spd/' . $fileSpdName;
        } elseif ($spj->spd_path) {
            $spd_path = $spj->spd_path;
        }

        //File Hotel
        if ($request->hasFile('file_hotel')) {
            $pathHotel = public_path('storage/spj-diklat/hotel');
            if (!file_exists($pathHotel)) {
                mkdir($pathHotel, 0755, true); // buat folder dengan izin 755, true = recursive
            }
            $fileHotel = $request->file('file_hotel');
            $fileHotelName = time() . '-Hotel.' . $fileHotel->getClientOriginalExtension();
            $fileHotel->move($pathHotel, $fileHotelName);
            $hotel_path = 'storage/spj-diklat/hotel/' . $fileHotelName;
        } elseif ($spj->hotel_path) {
            $hotel_path = $spj->hotel_path;
        }

        //File Transportasi
        if ($request->tipe_perjadin === '1') {
            if ($request->hasFile('file_trans_dalkot')) {
                $pathTransportLokal = public_path('storage/spj-diklat/transport');
                if (!file_exists($pathTransportLokal)) {
                    mkdir($pathTransportLokal, 0755, true); // buat folder dengan izin 755, true = recursive
                }
                $fileTransportBerangkat = $request->file('file_trans_dalkot');
                $fileTransportBerangkatName = time() . '-Transport-Lokal.' . $fileTransportBerangkat->getClientOriginalExtension();
                $fileTransportBerangkat->move($pathTransportLokal, $fileTransportBerangkatName);
                $translok_path = 'storage/spj-diklat/transport/' . $fileTransportBerangkatName;
            } elseif ($spj->translok_path) {
                $translok_path = $spj->translok_path;
            }

            // if ($request->hasFile('file_trans_dalkot_pulang')) {
            //     $pathTransportPulang = public_path('storage/spj-diklat/transport');
            //     $fileTransportPulang = $request->file('file_trans_dalkot_pulang');
            //     $fileTransportPulangName = time() . '-Transport-Pulang.' . $fileTransportPulang->getClientOriginalExtension();
            //     $fileTransportPulang->move($pathTransportPulang, $fileTransportPulangName);
            //     $transport_pulang_path = 'storage/spj-diklat/transport/' . $fileTransportPulangName;
            // } elseif ($spj->transport_pulang_path) {
            //     $transport_pulang_path = $spj->transport_pulang_path;
            // }
        } elseif ($request->tipe_perjadin === '2') {
            if ($request->hasFile('file_trans_lukot_berangkat')) {
                $pathTransportBerangkat = public_path('storage/spj-diklat/transport');
                if (!file_exists($pathTransportBerangkat)) {
                    mkdir($pathTransportBerangkat, 0755, true); // buat folder dengan izin 755, true = recursive
                }
                $fileTransportBerangkat = $request->file('file_trans_lukot_berangkat');
                $fileTransportBerangkatName = time() . '-Transport-Berangkat.' . $fileTransportBerangkat->getClientOriginalExtension();
                $fileTransportBerangkat->move($pathTransportBerangkat, $fileTransportBerangkatName);
                $transport_berangkat_path = 'storage/spj-diklat/transport/' . $fileTransportBerangkatName;
            } elseif ($spj->transport_berangkat_path) {
                $transport_berangkat_path = $spj->transport_berangkat_path;
            }

            if ($request->hasFile('file_trans_lukot_pulang')) {
                $pathTransportPulang = public_path('storage/spj-diklat/transport');
                $fileTransportPulang = $request->file('file_trans_lukot_pulang');
                $fileTransportPulangName = time() . '-Transport-Pulang.' . $fileTransportPulang->getClientOriginalExtension();
                $fileTransportPulang->move($pathTransportPulang, $fileTransportPulangName);
                $transport_pulang_path = 'storage/spj-diklat/transport/' . $fileTransportPulangName;
            } elseif ($spj->transport_pulang_path) {
                $transport_pulang_path = $spj->transport_pulang_path;
            }
        }

        //File Laporan Perjadin
        if ($request->hasFile('file_laporan_perjadin')) {
            $pathLaporanPerjadin = public_path('storage/spj-diklat/laporan');
            if (!file_exists($pathLaporanPerjadin)) {
                mkdir($pathLaporanPerjadin, 0755, true); // buat folder dengan izin 755, true = recursive
            }
            $fileLaporanPerjadin = $request->file('file_laporan_perjadin');
            $fileLaporanPerjadinName = time() . '-Laporan.' . $fileLaporanPerjadin->getClientOriginalExtension();
            $fileLaporanPerjadin->move($pathLaporanPerjadin, $fileLaporanPerjadinName);
            $laporan_perjadin_path = 'storage/spj-diklat/laporan/' . $fileLaporanPerjadinName;
        } elseif ($spj->laporan_perjadin_path) {
            $laporan_perjadin_path = $spj->laporan_perjadin_path;
        }

        //File FPP
        if ($request->hasFile('file_fpp')) {
            $pathFPP = public_path('storage/spj-diklat/laporan');
            if (!file_exists($pathFPP)) {
                mkdir($pathFPP, 0755, true); // buat folder dengan izin 755, true = recursive
            }
            $fileFPP = $request->file('file_fpp');
            $fileFPPName = time() . '-Laporan.' . $fileFPP->getClientOriginalExtension();
            $fileFPP->move($pathFPP, $fileFPPName);
            $fpp_path = 'storage/spj-diklat/laporan/' . $fileFPPName;
        } elseif ($spj->fpp_path) {
            $fpp_path = $spj->fpp_path;
        }

        //File KAK
        if ($request->hasFile('file_kak')) {
            $pathKAKPerjadin = public_path('storage/spj-diklat/laporan');
            if (!file_exists($pathKAKPerjadin)) {
                mkdir($pathKAKPerjadin, 0755, true); // buat folder dengan izin 755, true = recursive
            }
            $fileKAKPerjadin = $request->file('file_kak');
            $fileKAKPerjadinName = time() . '-Laporan.' . $fileKAKPerjadin->getClientOriginalExtension();
            $fileKAKPerjadin->move($pathKAKPerjadin, $fileKAKPerjadinName);
            $kak_path = 'storage/spj-diklat/laporan/' . $fileKAKPerjadinName;
        } elseif ($spj->kak_path) {
            $kak_path = $spj->kak_path;
        }

        //File Surat Pemanggilan
        if ($request->hasFile('file_pemanggilan')) {
            $pathSuratPemanggilan = public_path('storage/spj-diklat/laporan');
            if (!file_exists($pathSuratPemanggilan)) {
                mkdir($pathSuratPemanggilan, 0755, true); // buat folder dengan izin 755, true = recursive
            }
            $fileSuratPemanggilan = $request->file('file_pemanggilan');
            $fileSuratPemanggilanName = time() . '-Laporan.' . $fileSuratPemanggilan->getClientOriginalExtension();
            $fileSuratPemanggilan->move($pathSuratPemanggilan, $fileSuratPemanggilanName);
            $surat_pemanggilan_path = 'storage/spj-diklat/laporan/' . $fileSuratPemanggilanName;
        } elseif ($spj->surat_pemanggilan_path) {
            $surat_pemanggilan_path = $spj->surat_pemanggilan_path;
        }

        $data = [
            'rencanaDiklat_id' => $request->id_diklat,
            'no_st' => $request->no_st,
            'tgl_mulai_st' => $request->tgl_mulai,
            'tgl_selesai_st' => $request->tgl_selesai,
            'st_path' => $st_path,
            'no_spd' => $request->no_spd,
            'tgl_spd' => $request->tgl_spd,
            'spd_path' => $spd_path,
            'tipe_perjadin' => $request->tipe_perjadin,
            'hari_diklat' => $request->jumlah_hari,
            'uang_diklat' => $request->uh_diklat,
            'laporan_perjadin_path' => $laporan_perjadin_path,
            'surat_pemanggilan_path' => $surat_pemanggilan_path,
            'kak_path' => $kak_path,
            'fpp_path' => $fpp_path,
            'status' => 'Draft',
            'rekening_id' => $request->no_rek,
        ];

        if ($request->ada_hotel === 'ya') {
            $data['nominal_hotel'] = $request->input('nominal_hotel');
            $data['hotel_path'] = $hotel_path;
        } else {
            $data['nominal_hotel'] = 0;
            $data['hotel_path'] = NULL;
        }

        if ($request->tipe_perjadin === '1') {
            // $data['nominal_transport_berangkat'] = 170000;
            // $data['nominal_transport_pulang'] = 170000;
            // $data['tgl_transport_berangkat'] = $request->input('tgl_trans_dalkot_berangkat');
            // $data['tgl_transport_pulang'] = $request->input('tgl_trans_dalkot_pulang');
            // $data['transport_berangkat_path'] = $transport_berangkat_path;
            // $data['transport_pulang_path'] = $transport_pulang_path;
            // $data['jarak'] = NULL;
            $data['nominal_translok'] = 170000 * ($request->input('jumlah_hari_transport'));
            $data['translok_path'] = $translok_path;
            $data['jarak'] = NULL;
        } else {
            if ($request->kat_lukot === '1') {
                $data['jarak'] = $request->input('km_berangkat');
                $data['nominal_transport_berangkat'] = $request->input('nominal_trans_lukot_berangkat');
                $data['nominal_transport_pulang'] = $request->input('nominal_trans_lukot_pulang');
                $data['tgl_transport_berangkat'] = $request->input('tgl_trans_lukot_berangkat');
                $data['tgl_transport_pulang'] = $request->input('tgl_trans_lukot_pulang');
                $data['transport_berangkat_path'] = $transport_berangkat_path;
                $data['transport_pulang_path'] = $transport_pulang_path;
            } elseif ($request->kat_lukot === '2') {
                $data['jarak'] = NULL;
                $data['nominal_transport_berangkat'] = $request->input('nominal_trans_lukot_berangkat');
                $data['nominal_transport_pulang'] = $request->input('nominal_trans_lukot_pulang');
                $data['tgl_transport_berangkat'] = $request->input('tgl_trans_lukot_berangkat');
                $data['tgl_transport_pulang'] = $request->input('tgl_trans_lukot_pulang');
                $data['transport_berangkat_path'] = $transport_berangkat_path;
                $data['transport_pulang_path'] = $transport_pulang_path;
            }
        }

        if ($request->ada_berangkat === 'ya') {
            $data['uang_harian_berangkat'] = $request->input('uh_h_1');
        } else {
            $data['uang_harian_berangkat'] = 0;
        }

        if ($request->ada_pulang === 'ya') {
            $data['uang_harian_pulang'] = $request->input('uh_h1');
        } else {
            $data['uang_harian_pulang'] = 0;
        }

        $spj->update($data);

        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect('pegawai/spj-diklat')->with('success', 'Berhasil menyimpan data SPJ Diklat!');
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

    public function edit_spj($id)
    {
        $tahun_sekarang = date('Y');
        $diklat = SpjDiklat::where('id_spjDiklat', $id)->first();

        //Nomor ST
        $no_st = $diklat->no_st;
        $no_st_clean = str_replace('B-', '', $no_st);
        $parts = explode('/', $no_st_clean);

        //Nomor Spd
        $no_spd = $diklat->no_spd;
        $parts_spd = explode('/', $no_spd);

        //Rekening
        $rekening = MasterRekening::where('id_pegawai', auth()->user()->id)->get();

        //Hari Translok
        $hari_translok = ($diklat->nominal_translok) / 170000;

        return view('pegawai.spj-diklat.edit', [
            'tahun_sekarang' => $tahun_sekarang,
            'diklat' => $diklat,
            'input1' => $parts[0] ?? '',
            'input2' => $parts[1] ?? '',
            'input3' => $parts[2] ?? '',
            'input4' => $parts[3] ?? '',
            'input1_spd' => $parts_spd[0] ?? '',
            'input2_spd' => $parts_spd[1] ?? '',
            'input3_spd' => $parts_spd[2] ?? '',
            'input4_spd' => $parts_spd[3] ?? '',
            'input5_spd' => $parts_spd[4] ?? '',
            'rekening' => $rekening,
            'hari_translok' => $hari_translok,
        ]);
    }

    public function kirim_spj(Request $request, $id)
    {
        $spj = SpjDiklat::findOrFail($id);
        $rules = [
            'no_st' => 'required',
            'tgl_mulai' => 'required',
            'tgl_selesai' => 'required',
            'file_st' => ['file', 'mimes:pdf', 'max:10240'],
            'no_spd' => 'required',
            'tgl_spd' => 'required',
            'file_spd' => ['file', 'mimes:pdf', 'max:10240'],
            'jumlah_hari' => 'required',
            'uh_diklat' => 'required',
            'ada_hotel' => 'required',
            'nominal_hotel' => 'required_if:ada_hotel,ya|numeric',
            'file_hotel' => ['file', 'mimes:pdf', 'max:10240'],
            'tipe_perjadin' => 'required',
            'file_trans_dalkot' => 'file|mimes:pdf|max:10240',
            'jumlah_hari_transport' => 'required_if:tipe_perjadin,1',
            // 'tgl_trans_dalkot_berangkat' => 'required_if:tipe_perjadin,1',
            // 'tgl_trans_dalkot_pulang' => 'required_if:tipe_perjadin,1',
            // 'file_trans_dalkot_berangkat' => ['file', 'mimes:pdf', 'max:10240'],
            // 'file_trans_dalkot_pulang' => ['file', 'mimes:pdf', 'max:10240'],
            'kat_lukot' => 'required_if:tipe_perjadin,2',
            'km_berangkat' => 'required_if:kat_lukot,1',
            'nominal_trans_lukot_berangkat' => 'required_if:tipe_perjadin,2',
            'tgl_trans_lukot_berangkat' => 'required_if:tipe_perjadin,2',
            'file_trans_lukot_berangkat' => ['file', 'mimes:pdf', 'max:10240'],
            'nominal_trans_lukot_pulang' => 'required_if:tipe_perjadin,2',
            'tgl_trans_lukot_pulang' => 'required_if:tipe_perjadin,2',
            'file_trans_lukot_pulang' => ['file', 'mimes:pdf', 'max:10240'],
            'file_laporan_perjadin' => ['file', 'mimes:pdf', 'max:10240'],
            'file_fpp' => ['file', 'mimes:pdf', 'max:10240'],
            'file_kak' => ['file', 'mimes:pdf', 'max:10240'],
            'file_pemanggilan' => ['file', 'mimes:pdf', 'max:10240'],
            'no_rek' => 'required',
        ];

        $fileFields = [
            'file_st' => $spj->st_path ?? null,
            'file_spd' => $spj->spd_path ?? null,
            'file_laporan_perjadin' => $spj->laporan_perjadin_path ?? null,
            'file_fpp' => $spj->fpp_path ?? null,
            'file_kak' => $spj->kak_path ?? null,
            'file_pemanggilan' => $spj->surat_pemanggilan_path ?? null,
        ];

        foreach ($fileFields as $field => $existingPath) {
            if (empty($existingPath)) {
                $rules[$field][] = 'required';
            }
        }

        if ($request->ada_hotel === 'ya' && !$spj->hotel_path) {
            $rules['file_hotel'][] = 'required';
        }

        $messages = [
            'required' => ':attribute harus diisi',
            'file.max' => ':attribute ukuran file maksimal 10MB',
            'mimes' => 'Format file harus pdf',
            'numeric' => ':attribute harus angka',
        ];

        $attributes = [
            'no_st' => 'Nomor ST',
            'tgl_mulai' => 'Tanggal Mulai ST',
            'tgl_selesai' => 'Tanggal Selesai ST',
            'file_st' => 'Bukti ST',
            'no_spd' => 'Nomor SPD',
            'tgl_spd' => 'Tanggal SPD',
            'file_spd' => 'Bukti SPD',
            'ada_hotel' => 'Field Hotel',
            'nominal_hotel' => 'Nominal Hotel',
            'file_hotel' => 'Bukti Hotel',
            'tipe_perjadin' => 'Tipe Perjadin',
            'jumlah_hari' => 'Hari Diklat',
            'uh_diklat' => 'Uang Harian Diklat',
            'jumlah_hari_transport' => 'Hari Translok',
            'file_trans_dalkot' => 'Bukti Transport Lokal',
            // 'tgl_trans_dalkot_berangkat' => 'Tanggal Transport Berangkat',
            // 'tgl_trans_dalkot_pulang' => 'Tanggal Transport Pulang',
            // 'file_trans_dalkot_berangkat' => 'Bukti Transport Berangkat',
            // 'file_trans_dalkot_pulang' => 'Bukti Transport Pulang',
            'kat_lukot' => 'Kategori Kendaraan',
            'km_berangkat' => 'Jarak',
            'nominal_trans_lukot_berangkat' => 'Nominal Transport Berangkat',
            'tgl_trans_lukot_berangkat' => 'Tanggal Transport Berangkat',
            'file_trans_lukot_berangkat' => 'Bukti Transport Berangkat',
            'nominal_trans_lukot_pulang' => 'Nominal Transport Pulang',
            'tgl_trans_lukot_pulang' => 'Tanggal Transport Pulang',
            'file_trans_lukot_pulang' => 'Bukti Transport Pulang',
            'file_laporan_perjadin' => 'Dokumen Laporan Perjadin',
            'file_fpp' => 'Dokumen FPP',
            'file_kak' => 'Dokumen KAK',
            'file_pemanggilan' => 'Surat Pemanggilan',
            'no_rek' => 'Rekening',
        ];

        $validator = Validator::make($request->all(), $rules, $messages, $attributes);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $validateData = $request->validate($rules);
        date_default_timezone_set('Asia/Jakarta');

        //File Surat Tugas
        if ($request->hasFile('file_st')) {
            $pathST = public_path('storage/spj-diklat/st');
            if (!file_exists($pathST)) {
                mkdir($pathST, 0755, true); // buat folder dengan izin 755, true = recursive
            }
            $fileST = $request->file('file_st');
            $fileSTName = time() . '-Surat-Tugas.' . $fileST->getClientOriginalExtension();
            $fileST->move($pathST, $fileSTName);
            $st_path = 'storage/spj-diklat/st/' . $fileSTName;
        } elseif ($spj->st_path) {
            $st_path = $spj->st_path;
        }

        //File SPD
        if ($request->hasFile('file_spd')) {
            $pathSPD = public_path('storage/spj-diklat/spd');
            if (!file_exists($pathSPD)) {
                mkdir($pathSPD, 0755, true); // buat folder dengan izin 755, true = recursive
            }
            $fileSpd = $request->file('file_spd');
            $fileSpdName = time() . '-SPD.' . $fileSpd->getClientOriginalExtension();
            $fileSpd->move($pathSPD, $fileSpdName);
            $spd_path = 'storage/spj-diklat/spd/' . $fileSpdName;
        } elseif ($spj->spd_path) {
            $spd_path = $spj->spd_path;
        }

        //File Hotel
        if ($request->hasFile('file_hotel')) {
            $pathHotel = public_path('storage/spj-diklat/hotel');
            if (!file_exists($pathHotel)) {
                mkdir($pathHotel, 0755, true); // buat folder dengan izin 755, true = recursive
            }
            $fileHotel = $request->file('file_hotel');
            $fileHotelName = time() . '-Hotel.' . $fileHotel->getClientOriginalExtension();
            $fileHotel->move($pathHotel, $fileHotelName);
            $hotel_path = 'storage/spj-diklat/hotel/' . $fileHotelName;
        } elseif ($spj->hotel_path) {
            $hotel_path = $spj->hotel_path;
        }

        //File Transportasi
        if ($request->tipe_perjadin === '1') {
            // if ($request->hasFile('file_trans_dalkot_berangkat')) {
            //     $pathTransportBerangkat = public_path('storage/spj-diklat/transport');
            //     if (!file_exists($pathTransportBerangkat)) {
            //         mkdir($pathTransportBerangkat, 0755, true); // buat folder dengan izin 755, true = recursive
            //     }
            //     $fileTransportBerangkat = $request->file('file_trans_dalkot_berangkat');
            //     $fileTransportBerangkatName = time() . '-Transport-Berangkat.' . $fileTransportBerangkat->getClientOriginalExtension();
            //     $fileTransportBerangkat->move($pathTransportBerangkat, $fileTransportBerangkatName);
            //     $transport_berangkat_path = 'storage/spj-diklat/transport/' . $fileTransportBerangkatName;
            // } elseif ($spj->transport_berangkat_path) {
            //     $transport_berangkat_path = $spj->transport_berangkat_path;
            // }

            // if ($request->hasFile('file_trans_dalkot_pulang')) {
            //     $pathTransportPulang = public_path('storage/spj-diklat/transport');
            //     $fileTransportPulang = $request->file('file_trans_dalkot_pulang');
            //     $fileTransportPulangName = time() . '-Transport-Pulang.' . $fileTransportPulang->getClientOriginalExtension();
            //     $fileTransportPulang->move($pathTransportPulang, $fileTransportPulangName);
            //     $transport_pulang_path = 'storage/spj-diklat/transport/' . $fileTransportPulangName;
            // } elseif ($spj->transport_pulang_path) {
            //     $transport_pulang_path = $spj->transport_pulang_path;
            // }
            if ($request->hasFile('file_trans_dalkot')) {
                $pathTransportLokal = public_path('storage/spj-diklat/transport');
                if (!file_exists($pathTransportLokal)) {
                    mkdir($pathTransportLokal, 0755, true); // buat folder dengan izin 755, true = recursive
                }
                $fileTransportBerangkat = $request->file('file_trans_dalkot');
                $fileTransportBerangkatName = time() . '-Transport-Lokal.' . $fileTransportBerangkat->getClientOriginalExtension();
                $fileTransportBerangkat->move($pathTransportLokal, $fileTransportBerangkatName);
                $translok_path = 'storage/spj-diklat/transport/' . $fileTransportBerangkatName;
            } elseif ($spj->translok_path) {
                $translok_path = $spj->translok_path;
            }
        } elseif ($request->tipe_perjadin === '2') {
            if ($request->hasFile('file_trans_lukot_berangkat')) {
                $pathTransportBerangkat = public_path('storage/spj-diklat/transport');
                if (!file_exists($pathTransportBerangkat)) {
                    mkdir($pathTransportBerangkat, 0755, true); // buat folder dengan izin 755, true = recursive
                }
                $fileTransportBerangkat = $request->file('file_trans_lukot_berangkat');
                $fileTransportBerangkatName = time() . '-Transport-Berangkat.' . $fileTransportBerangkat->getClientOriginalExtension();
                $fileTransportBerangkat->move($pathTransportBerangkat, $fileTransportBerangkatName);
                $transport_berangkat_path = 'storage/spj-diklat/transport/' . $fileTransportBerangkatName;
            } elseif ($spj->transport_berangkat_path) {
                $transport_berangkat_path = $spj->transport_berangkat_path;
            }

            if ($request->hasFile('file_trans_lukot_pulang')) {
                $pathTransportPulang = public_path('storage/spj-diklat/transport');
                $fileTransportPulang = $request->file('file_trans_lukot_pulang');
                $fileTransportPulangName = time() . '-Transport-Pulang.' . $fileTransportPulang->getClientOriginalExtension();
                $fileTransportPulang->move($pathTransportPulang, $fileTransportPulangName);
                $transport_pulang_path = 'storage/spj-diklat/transport/' . $fileTransportPulangName;
            } elseif ($spj->transport_pulang_path) {
                $transport_pulang_path = $spj->transport_pulang_path;
            }
        }

        //File Laporan Perjadin
        if ($request->hasFile('file_laporan_perjadin')) {
            $pathLaporanPerjadin = public_path('storage/spj-diklat/laporan');
            if (!file_exists($pathLaporanPerjadin)) {
                mkdir($pathLaporanPerjadin, 0755, true); // buat folder dengan izin 755, true = recursive
            }
            $fileLaporanPerjadin = $request->file('file_laporan_perjadin');
            $fileLaporanPerjadinName = time() . '-Laporan.' . $fileLaporanPerjadin->getClientOriginalExtension();
            $fileLaporanPerjadin->move($pathLaporanPerjadin, $fileLaporanPerjadinName);
            $laporan_perjadin_path = 'storage/spj-diklat/laporan/' . $fileLaporanPerjadinName;
        } elseif ($spj->laporan_perjadin_path) {
            $laporan_perjadin_path = $spj->laporan_perjadin_path;
        }

        //File FPP
        if ($request->hasFile('file_fpp')) {
            $pathFPP = public_path('storage/spj-diklat/laporan');
            if (!file_exists($pathFPP)) {
                mkdir($pathFPP, 0755, true); // buat folder dengan izin 755, true = recursive
            }
            $fileFPP = $request->file('file_fpp');
            $fileFPPName = time() . '-Laporan.' . $fileFPP->getClientOriginalExtension();
            $fileFPP->move($pathFPP, $fileFPPName);
            $fpp_path = 'storage/spj-diklat/laporan/' . $fileFPPName;
        } elseif ($spj->fpp_path) {
            $fpp_path = $spj->fpp_path;
        }

        //File KAK
        if ($request->hasFile('file_kak')) {
            $pathKAKPerjadin = public_path('storage/spj-diklat/laporan');
            if (!file_exists($pathKAKPerjadin)) {
                mkdir($pathKAKPerjadin, 0755, true); // buat folder dengan izin 755, true = recursive
            }
            $fileKAKPerjadin = $request->file('file_kak');
            $fileKAKPerjadinName = time() . '-Laporan.' . $fileKAKPerjadin->getClientOriginalExtension();
            $fileKAKPerjadin->move($pathKAKPerjadin, $fileKAKPerjadinName);
            $kak_path = 'storage/spj-diklat/laporan/' . $fileKAKPerjadinName;
        } elseif ($spj->kak_path) {
            $kak_path = $spj->kak_path;
        }

        //File Surat Pemanggilan
        if ($request->hasFile('file_pemanggilan')) {
            $pathSuratPemanggilan = public_path('storage/spj-diklat/laporan');
            if (!file_exists($pathSuratPemanggilan)) {
                mkdir($pathSuratPemanggilan, 0755, true); // buat folder dengan izin 755, true = recursive
            }
            $fileSuratPemanggilan = $request->file('file_pemanggilan');
            $fileSuratPemanggilanName = time() . '-Laporan.' . $fileSuratPemanggilan->getClientOriginalExtension();
            $fileSuratPemanggilan->move($pathSuratPemanggilan, $fileSuratPemanggilanName);
            $surat_pemanggilan_path = 'storage/spj-diklat/laporan/' . $fileSuratPemanggilanName;
        } elseif ($spj->surat_pemanggilan_path) {
            $surat_pemanggilan_path = $spj->surat_pemanggilan_path;
        }

        $data = [
            'rencanaDiklat_id' => $request->id_diklat,
            'no_st' => $request->no_st,
            'tgl_mulai_st' => $request->tgl_mulai,
            'tgl_selesai_st' => $request->tgl_selesai,
            'st_path' => $st_path,
            'no_spd' => $request->no_spd,
            'tgl_spd' => $request->tgl_spd,
            'spd_path' => $spd_path,
            'tipe_perjadin' => $request->tipe_perjadin,
            'hari_diklat' => $request->jumlah_hari,
            'uang_diklat' => $request->uh_diklat,
            'laporan_perjadin_path' => $laporan_perjadin_path,
            'surat_pemanggilan_path' => $surat_pemanggilan_path,
            'kak_path' => $kak_path,
            'fpp_path' => $fpp_path,
            'status' => 'Terkirim',
            'date_dikirim' => date('Y-m-d'),
        ];

        if ($request->ada_hotel === 'ya') {
            $data['nominal_hotel'] = $request->input('nominal_hotel');
            $data['hotel_path'] = $hotel_path;
        } else {
            $data['nominal_hotel'] = 0;
            $data['hotel_path'] = NULL;
        }
        if ($request->tipe_perjadin === '1') {
            // $data['nominal_transport_berangkat'] = 170000;
            // $data['nominal_transport_pulang'] = 170000;
            // $data['tgl_transport_berangkat'] = $request->input('tgl_trans_dalkot_berangkat');
            // $data['tgl_transport_pulang'] = $request->input('tgl_trans_dalkot_pulang');
            // $data['transport_berangkat_path'] = $transport_berangkat_path;
            // $data['transport_pulang_path'] = $transport_pulang_path;
            // $data['jarak'] = NULL;
            $data['nominal_translok'] = 170000 * ($request->input('jumlah_hari_transport'));
            $data['translok_path'] = $translok_path;
            $data['jarak'] = NULL;
        } else {
            if ($request->kat_lukot === '1') {
                $data['jarak'] = $request->input('km_berangkat');
                $data['nominal_transport_berangkat'] = $request->input('nominal_trans_lukot_berangkat');
                $data['nominal_transport_pulang'] = $request->input('nominal_trans_lukot_pulang');
                $data['tgl_transport_berangkat'] = $request->input('tgl_trans_lukot_berangkat');
                $data['tgl_transport_pulang'] = $request->input('tgl_trans_lukot_pulang');
                $data['transport_berangkat_path'] = $transport_berangkat_path;
                $data['transport_pulang_path'] = $transport_pulang_path;
            } elseif ($request->kat_lukot === '2') {
                $data['jarak'] = NULL;
                $data['nominal_transport_berangkat'] = $request->input('nominal_trans_lukot_berangkat');
                $data['nominal_transport_pulang'] = $request->input('nominal_trans_lukot_pulang');
                $data['tgl_transport_berangkat'] = $request->input('tgl_trans_lukot_berangkat');
                $data['tgl_transport_pulang'] = $request->input('tgl_trans_lukot_pulang');
                $data['transport_berangkat_path'] = $transport_berangkat_path;
                $data['transport_pulang_path'] = $transport_pulang_path;
            }
        }

        if ($request->ada_berangkat === 'ya') {
            $data['uang_harian_berangkat'] = $request->input('uh_h_1');
        } else {
            $data['uang_harian_berangkat'] = 0;
        }

        if ($request->ada_pulang === 'ya') {
            $data['uang_harian_pulang'] = $request->input('uh_h1');
        } else {
            $data['uang_harian_pulang'] = 0;
        }

        $spj->update($data);

        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect('pegawai/spj-diklat')->with('success', 'Berhasil mengirim data SPJ Diklat!');
    }

    public function verifikator_index(Request $request)
    {
        $year = $request->year;

        if ($year == null) {
            $year = date('Y');
        } else {
            $year = $year;
        }
        $currentYear = date('Y');
        $spjDiklats = SpjDiklat::where('status', '!=', 'Draft')->get();

        return view('verifikator.spj-diklat.index', [
            'year' => $year,
            'spjDiklats' => $spjDiklats,
        ]);
    }

    public function verifikator_show($id)
    {
        $tahun_sekarang = date('Y');
        $spjDiklat = SpjDiklat::where('id_spjDiklat', $id)->first();

        //Nomor Surat Tugas
        $no_st = $spjDiklat->no_st;
        $no_st_clean = str_replace('B-', '', $no_st);
        $parts = explode('/', $no_st_clean);

        //Nomor Spd
        $no_spd = $spjDiklat->no_spd;
        $parts_spd = explode('/', $no_spd);
        //Rekening
        $rekening = MasterRekening::where('id_pegawai', auth()->user()->id)->get();

        $verifications = $this->initVerifications($id);

        return view('verifikator.spj-diklat.verifikasi', [
            'tahun_sekarang' => $tahun_sekarang,
            'spjDiklat' => $spjDiklat,
            'rekening' => $rekening,
            'input1_st' => $parts[0] ?? '',
            'input2_st' => $parts[1] ?? '',
            'input3_st' => $parts[2] ?? '',
            'input4_st' => $parts[3] ?? '',
            'input1_spd' => $parts_spd[0] ?? '',
            'input2_spd' => $parts_spd[1] ?? '',
            'input3_spd' => $parts_spd[2] ?? '',
            'input4_spd' => $parts_spd[3] ?? '',
            'input5_spd' => $parts_spd[4] ?? '',
            'verifications' => $verifications->keyBy('document_type'),
        ]);
    }

    public function saveVerification(Request $request, $id)
    {
        $spj = SpjDiklat::where('id_spjDiklat', $id)->firstOrFail();

        $validated = $request->validate([
            'spj.nominal_hotel' => $spj->hotel_path != NULL ? 'required|integer|min:0' : 'nullable',
            'spj.nominal_transport_berangkat' => $spj->tipe_perjadin == 2 ? 'required|integer|min:0' : 'nullable',
            'spj.nominal_transport_pulang' => $spj->tipe_perjadin == 2 ? 'required|integer|min:0' : 'nullable',
            'spj.nominal_translok' => $spj->tipe_perjadin == 1 ? 'required|integer|min:0' : 'nullable',
            'spj.uang_diklat' => 'required|integer|min:0',
            'spj.hari_diklat' => 'required|integer|min:1',
            'spj.tgl_mulai_st' => 'required|date',
            'spj.tgl_selesai_st' => 'required|date|after_or_equal:spj.tgl_mulai_st',
            'spj.no_st' => 'required|string',
            'spj.no_spd' => 'required|string',
            'spj.tgl_spd' => 'required|date',
            'verifications' => 'required|array',
            'verifications.*.document_type' => 'required|string',
            'verifications.*.status' => 'required|in:valid,invalid',
            'verifications.*.comments' => 'nullable|string',
        ]);

        // Update SPJ fields
        $spj->update($request->input('spj'));

        // Update verifications
        foreach ($request->input('verifications', []) as $verificationData) {
            $comments = $verification['comments'] ?? null;
            SpjVerification::updateOrCreate(
                [
                    'spj_diklat_id' => $id,
                    'document_type' => $verificationData['document_type']
                ],
                [
                    'status' => $verificationData['status'],
                    'comments' => $comments,
                    'verifier_id' => auth()->id(),
                    'verified_at' => now()
                ]
            );
        }

        // Determine overall verification status
        $statuses = SpjVerification::where('spj_diklat_id', $id)->pluck('status');

        if ($statuses->contains('invalid')) {
            $spj->status = 'Dikembalikan';
            $spj->date_ditolak = now();
        } elseif ($statuses->every(fn($status) => $status != 'invalid')) {
            $spj->status = 'Disetujui';
            $spj->date_diterima = now();
            // ✅ Set related rencana_diklat status to "Selesai"
            if ($spj->rencanadiklat) {
                $spj->rencanadiklat->status = 'Selesai';
                $spj->rencanadiklat->save();
            }
        }

        $spj->verifikator_id = auth()->id();

        $spj->fill($request->input('spj'))->save();

        return response()->json([
            'message' => 'Data verifikasi berhasil disimpan',
            'status' => $spj->status
        ]);
    }

    private function initVerifications($id)
    {
        $verifications = SpjVerification::where('spj_diklat_id', $id)->pluck('document_type');
        $documents = [
            'surat-tugas',
            'spd',
            'form-permintaan',
            'laporan',
            'kak',
            'surat-pemanggilan',
            'hotel',
            'translok',
            'transport-berangkat',
            'transport-pulang'
        ];

        foreach ($documents as $document) {
            if (!$verifications->contains($document)) {
                SpjVerification::create([
                    'spj_diklat_id' => $id,
                    'document_type' => $document,
                    'status' => null,
                    'verifier_id' => auth()->id(),
                    'verified_at' => now(),
                ]);
            }
        }

        return SpjVerification::where('spj_diklat_id', $id)->get();
    }

    public function generateNominatif($id)
    {

        Carbon::setLocale('id');
        $spj = SpjDiklat::with('rencanadiklat.pegawai')->findOrFail($id);

        $start = Carbon::parse($spj->tgl_mulai_st);
        $end = Carbon::parse($spj->tgl_selesai_st);

        if ($start->format('F Y') === $end->format('F Y')) {
            // Same month and year
            $tanggal_format = $start->format('d') . '–' . $end->translatedFormat('d F Y');
        } elseif ($start->year === $end->year) {
            // Different month, same year
            $tanggal_format = $start->translatedFormat('d F') . ' – ' . $end->translatedFormat('d F Y');
        } else {
            // Different year
            $tanggal_format = $start->translatedFormat('d F Y') . ' – ' . $end->translatedFormat('d F Y');
        }

        if ($spj->rencanadiklat->pembebanan_perjadin === '8300') {
            $anggaran['kegiatan'] = 'PENGAWASAN DAN PENINGKATAN AKUNTABILITAS INSPEKTORAT III  (4205)';
            $anggaran['kro'] = 'LAYANAN MANAJEMEN KINERJA INTERNAL (4205.EBD)';
            $anggaran['ro'] = 'LAYANAN PENGAWASAN INTERNAL INSPEKTORAT WILAYAH III (4205.EBD.U09)';
            $anggaran['komponen'] = 'PERSIAPAN (051.A)';
        } elseif ($spj->rencanadiklat->pembebanan_perjadin === '8200') {
            $anggaran['kegiatan'] = 'PENGAWASAN DAN PENINGKATAN AKUNTABILITAS INSPEKTORAT II  (4204)';
            $anggaran['kro'] = 'LAYANAN MANAJEMEN KINERJA INTERNAL (4204.EBD)';
            $anggaran['ro'] = 'LAYANAN PENGAWASAN INTERNAL INSPEKTORAT WILAYAH II (4204.EBD.U09)';
            $anggaran['komponen'] = 'PERSIAPAN (051.A)';
            $anggaran['sub_komponen'] = 'Persiapan Perjalanan Dinas (051.A.01)';
        } elseif ($spj->rencanadiklat->pembebanan_perjadin === '8100') {
            $anggaran['kegiatan'] = 'PENGAWASAN DAN PENINGKATAN AKUNTABILITAS INSPEKTORAT I  (4203)';
            $anggaran['kro'] = 'LAYANAN MANAJEMEN KINERJA INTERNAL (4203.EBD)';
            $anggaran['ro'] = 'LAYANAN PENGAWASAN INTERNAL INSPEKTORAT WILAYAH I (4203.EBD.U09)';
            $anggaran['komponen'] = 'PERSIAPAN (051.A)';
        }

        if ($spj->rencanadiklat->akun_anggaran === '524111') {
            $anggaran['akun'] = 'BELANJA PERJALANAN DINAS BIASA (524111)';
        } else {
            $anggaran['akun'] = 'BELANJA PERJALANAN DINAS DALAM KOTA (524113)';
        }

        $peserta = [[
            'nama' => $spj->rencanadiklat->pegawai->name,
            'nip' => $spj->rencanadiklat->pegawai->nip,
            'tanggal' => $tanggal_format,
            'lama_perjalanan' => $spj->hari_diklat,
            'tiket_berangkat' => 'Rp' . number_format($spj->nominal_transport_berangkat, 0, ',', '.'),
            'tiket_pulang' => 'Rp' . number_format($spj->nominal_transport_pulang, 0, ',', '.'),
            'tiket_pp' => 'Rp' . number_format($spj->nominal_transport_berangkat + $spj->nominal_transport_pulang, 0, ',', '.'),
            'translok' => 'Rp' . number_format($spj->nominal_translok, 0, ',', '.'),
            'hotel' => 'Rp' . number_format($spj->nominal_hotel, 0, ',', '.'),
            'uang_harian' => 'Rp' . number_format($spj->uang_harian_berangkat + $spj->uang_harian_pulang, 0, ',', '.'),
            'uang_diklat' => 'Rp' . number_format($spj->uang_diklat * $spj->hari_diklat, 0, ',', '.'),
            'jumlah_diterima' => 'Rp' . number_format(
                $spj->nominal_transport_berangkat +
                    $spj->nominal_transport_pulang +
                    $spj->nominal_translok +
                    $spj->nominal_hotel +
                    ($spj->uang_diklat * $spj->hari_diklat) +
                    $spj->uang_harian_berangkat +
                    $spj->uang_harian_pulang,
                0,
                ',',
                '.'
            ),
            'bank' => $spj->rekening->nama_bank,
            'norek' => $spj->rekening->no_rekening, // This should come from pegawai or external table if dynamic
        ]];

        $total = $spj->nominal_transport_berangkat +
            $spj->nominal_transport_pulang +
            $spj->nominal_translok +
            $spj->nominal_hotel +
            ($spj->uang_diklat * $spj->hari_diklat) +
            $spj->uang_harian_berangkat +
            $spj->uang_harian_pulang;

        $pdf = Pdf::loadView('verifikator.spj-diklat.daftar-nominatif', [
            'spj' => $spj,
            'anggaran' => $anggaran,
            'peserta' => $peserta,
            'kota' => $spj->rencanadiklat->penyelenggara_diklat->kota,
            'periode' => $tanggal_format,
            'tanggal' => Carbon::parse($spj->date_diterima)->translatedFormat('d F Y'),
            'totalTiketBerangkat' => $peserta[0]['tiket_berangkat'],
            'totalTiketPulang' => $peserta[0]['tiket_pulang'],
            'totalTranslok' => $peserta[0]['translok'],
            'totalTiketPP' => $peserta[0]['tiket_pp'],
            'totalHotel' => $peserta[0]['hotel'],
            'totalUH' => $peserta[0]['uang_harian'],
            'totalUHDiklat' => $peserta[0]['uang_diklat'],
            'totalDiterima' => $peserta[0]['jumlah_diterima'],
            'terbilang' => $this->terbilang($total), // Use helper if needed
            'verifikator' => $spj->pegawai,
        ])->setPaper('A4', 'landscape');

        return $pdf->stream('daftar-nominatif.pdf');
    }

    private function terbilang($number)
    {
        $number = abs((int)$number); // ensure it's a positive integer
        $huruf = ["", "Satu", "Dua", "Tiga", "Empat", "Lima", "Enam", "Tujuh", "Delapan", "Sembilan", "Sepuluh", "Sebelas"];
        if ($number < 12) {
            return $huruf[$number];
        } elseif ($number < 20) {
            return $this->terbilang($number - 10) . " Belas";
        } elseif ($number < 100) {
            return $this->terbilang(intval($number / 10)) . " Puluh " . $this->terbilang($number % 10);
        } elseif ($number < 200) {
            return "Seratus " . $this->terbilang($number - 100);
        } elseif ($number < 1000) {
            return $this->terbilang(intval($number / 100)) . " Ratus " . $this->terbilang($number % 100);
        } elseif ($number < 2000) {
            return "Seribu " . $this->terbilang($number - 1000);
        } elseif ($number < 1000000) {
            return $this->terbilang(intval($number / 1000)) . " Ribu " . $this->terbilang($number % 1000);
        } elseif ($number < 1000000000) {
            return $this->terbilang(intval($number / 1000000)) . " Juta " . $this->terbilang($number % 1000000);
        }

        return "Angka terlalu besar";
    }
}
