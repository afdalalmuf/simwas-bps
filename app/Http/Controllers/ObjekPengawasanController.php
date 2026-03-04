<?php

namespace App\Http\Controllers;

use App\Models\LaporanObjekPengawasan;
use Illuminate\Http\Request;
use App\Models\ObjekPengawasan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ObjekPengawasanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        // dd($request->all());
        $rules = [
            'id_rencanakerja' => 'required',
            'objek' => 'required',
            'kategori_objek' => 'required',
            'nama' => 'required',
            'date_range' => 'required',
            'namaLaporan' => 'required',
            'januari' => 'required',
            'februari' => 'required',
            'maret' => 'required',
            'april' => 'required',
            'mei' => 'required',
            'juni' => 'required',
            'juli' => 'required',
            'agustus' => 'required',
            'september' => 'required',
            'oktober' => 'required',
            'november' => 'required',
            'desember' => 'required'
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $validateData = $request->validate($rules);

        $range = explode(" - ", $validateData['date_range']);
        $start = date('Y-m-d', strtotime($range[0]));
        $end = date('Y-m-d', strtotime($range[1]));
        ObjekPengawasan::create([
            'id_rencanakerja' => $validateData['id_rencanakerja'],
            'id_objek' => $validateData['objek'],
            'kategori_objek' => $validateData['kategori_objek'],
            'nama' => $validateData['nama'],
            'nama_laporan' => $validateData['namaLaporan'],
            'start_date' => $start,
            'end_date' => $end,
        ]);
        // get last id
        // get last id_opengawasan
        $lastId = ObjekPengawasan::latest()->first()->id_opengawasan;

        $month = [
            1 => 'januari',
            2 => 'februari',
            3 => 'maret',
            4 => 'april',
            5 => 'mei',
            6 => 'juni',
            7 => 'juli',
            8 => 'agustus',
            9 => 'september',
            10 => 'oktober',
            11 => 'november',
            12 => 'desember'
        ];

        foreach ($month as $key => $value) {
            LaporanObjekPengawasan::create([
                'id_objek_pengawasan' => $lastId,
                'month' => $key,
                'status' => $validateData[$value]
            ]);
        }

        $request->session()->put('status', 'Berhasil menambahkan Objek Pengawasan.');
        $request->session()->put('alert-type', 'success');

        return response()->json([
            'success' => true,
            'message' => 'Berhasil menambahkan Objek Pengawasan',
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ObjekPengawasan  $objekPengawasan
     * @return \Illuminate\Http\Response
     */
    public function show(ObjekPengawasan $objekPengawasan)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ObjekPengawasan  $objekPengawasan
     * @return \Illuminate\Http\Response
     */
    public function edit(ObjekPengawasan $objekPengawasan)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ObjekPengawasan  $objekPengawasan
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $rules = [
            'objek' => 'required',
            'kategori_objek' => 'required',
            'nama' => 'required',
            'date_range' => 'required',
            'namaLaporan' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $range = explode(" - ", $request->date_range);
        $start = date('Y-m-d', strtotime($range[0]));
        $end = date('Y-m-d', strtotime($range[1]));

        try {
            ObjekPengawasan::where('id_opengawasan', $request->id_opengawasan)
                ->update([
                    'id_objek' => $request->objek,
                    'kategori_objek' => $request->kategori_objek,
                    'nama' => $request->nama,
                    'nama_laporan' => $request->namaLaporan,
                    'start_date' => $start,
                    'end_date' => $end
                ]);
            // delete laporan objek pengawasan
            LaporanObjekPengawasan::where('id_objek_pengawasan', $request->id_opengawasan)->delete();
            // create laporan objek pengawasan
            $month = [
                1 => 'januari',
                2 => 'februari',
                3 => 'maret',
                4 => 'april',
                5 => 'mei',
                6 => 'juni',
                7 => 'juli',
                8 => 'agustus',
                9 => 'september',
                10 => 'oktober',
                11 => 'november',
                12 => 'desember'
            ];
            $data_check = array();
            foreach ($month as $key => $value) {
                $single_data = LaporanObjekPengawasan::create([
                    'id_objek_pengawasan' => $request->id_opengawasan,
                    'month' => $key,
                    'status' => $request->$value
                ]);
                array_push($data_check, $single_data);
            }

            $request->session()->put('status', 'Berhasil memperbarui Objek Pengawasan.');
            $request->session()->put('alert-type', 'success');

            return response()->json([
                'success' => true,
                'message' => 'Berhasil memperbarui Objek Pengawasan',
                'data' => $data_check,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui Objek Pengawasan, karena telah dilakukan pengisian norma hasil.',
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ObjekPengawasan  $objekPengawasan
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {

        ObjekPengawasan::where('id_opengawasan', $id)->delete();

        $request->session()->put('status', 'Berhasil menghapus Objek Pengawasan.');
        $request->session()->put('alert-type', 'success');

        return response()->json([
            'success' => true,
            'message' => 'Berhasil menghapus Objek Pengawasan',
        ]);
    }

    public function getObjekPengawasan()
    {
        $rencana_id = request()->rencana_id;
        $objek_pengawasan = ObjekPengawasan::with('laporanObjekPengawasan')->where('id_rencanakerja', $rencana_id)->get();
        // get hasil kerja from rencanaKerja and hasilKerja
        $objek_pengawasan->load('rencanaKerja.hasilKerja');
        return response()->json([
            'success' => true,
            'data' => $objek_pengawasan
        ]);
    }

    public function detailObjekPengawasan($id)
    {
        $objek_pengawasan = ObjekPengawasan::with('laporanObjekPengawasan', 'masterObjek')->where('id_opengawasan', $id)->first();
        return response()->json([
            'success' => true,
            'data' => $objek_pengawasan
        ]);
    }

    public function update_objek(Request $request)
    {
        $rules = [
            'objek' => 'required',
            'kategori_objek' => 'required',
            'nama' => 'required',
            'namaLaporan' => 'required',
            'date_range' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $range = explode(" - ", $request->date_range);
        $start = date('Y-m-d', strtotime($range[0]));
        $end = date('Y-m-d', strtotime($range[1]));

        try {
            ObjekPengawasan::where('id_opengawasan', $request->id_opengawasan)
                ->update([
                    'id_objek' => $request->objek,
                    'kategori_objek' => $request->kategori_objek,
                    'nama' => $request->nama,
                    'nama_laporan' => $request->namaLaporan,
                    'start_date' => $start,
                    'end_date' => $end
                ]);

            $month = [
                1 => 'januari',
                2 => 'februari',
                3 => 'maret',
                4 => 'april',
                5 => 'mei',
                6 => 'juni',
                7 => 'juli',
                8 => 'agustus',
                9 => 'september',
                10 => 'oktober',
                11 => 'november',
                12 => 'desember'
            ];

            $data_check = [];

            foreach ($month as $key => $value) {
                // cari laporan objek pengawasan yang sudah ada
                $laporan = LaporanObjekPengawasan::where('id_objek_pengawasan', $request->id_opengawasan)
                    ->where('month', $key)
                    ->first();

                if ($laporan) {
                    // cek apakah laporan ini sudah dipakai di tabel norma_hasil
                    $used = DB::table('norma_hasils')
                        ->where('laporan_pengawasan_id', $laporan->id)
                        ->where('status_norma_hasil' ,  'diperiksa')
                        ->where('status_norma_hasil' ,  'disetujui')
                        ->exists();
                        
                    if ($used) {
                        // skip update jika sudah dipakai
                        continue;
                    }

                    // jika belum dipakai, update status-nya
                    $laporan->update([
                        'status' => $request->$value
                    ]);
                } else {
                    // jika belum ada, buat baru
                    $laporan = LaporanObjekPengawasan::create([
                        'id_objek_pengawasan' => $request->id_opengawasan,
                        'month' => $key,
                        'status' => $request->$value
                    ]);
                }

                $data_check[] = $laporan;
            }

            $request->session()->put('status', 'Berhasil memperbarui Objek Pengawasan.');
            $request->session()->put('alert-type', 'success');

            return response()->json([
                'success' => true,
                'message' => 'Berhasil memperbarui Objek Pengawasan',
                'data' => $data_check,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui Objek Pengawasan, karena telah dilakukan pengisian norma hasil.',
            ], 500);
        }
    }
}
