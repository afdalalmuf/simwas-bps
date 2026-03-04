<?php

namespace App\Http\Controllers;

use App\Models\NormaHasil;
use App\Http\Requests\StoreNormaHasilRequest;
use App\Http\Requests\UpdateNormaHasilRequest;
use App\Models\MasterLaporan;
use App\Models\NormaHasilAccepted;
use App\Models\NormaHasilDokumen;
use App\Models\NormaHasilTim;
use App\Models\ObjekNormaHasil;
use App\Models\StKinerja;
use Illuminate\Support\Facades\Storage;
use App\Models\RencanaKerja;
use Illuminate\Http\Request;

class NormaHasilController extends Controller
{


    private $kodeHasilPengawasan = [
        "110" => 'LHA',
        "120" => 'LHK',
        "130" => 'LHT',
        "140" => 'LHI',
        "150" => 'LHR',
        "160" => 'LHE',
        "170" => 'LHP',
        "180" => 'LHN',
        "190" => 'LTA',
        "200" => 'LTR',
        "210" => 'LTE',
        "220" => 'LKP',
        "230" => 'LKS',
        "240" => 'LKB',
        "500" => 'EHP',
        "510" => 'LTS',
        "520" => 'PHP',
        "530" => 'QAP'
    ];
    private $hasilPengawasan = [
        "110" => "Laporan Hasil Audit Kepatuhan",
        "120" => "Laporan Hasil Audit Kinerja",
        "130" => "Laporan Hasil Audit ADTT",
        "140" => "Laporan Hasil Audit Investigasi",
        "150" => "Laporan Hasil Reviu",
        "160" => "Laporan Hasil Evaluasi",
        "170" => "Laporan Hasil Pemantauan",
        "180" => "Laporan Hasil Penelaahan",
        "190" => "Laporan Hasil Monitoring Tindak Lanjut Hasil Audit",
        "200" => "Laporan Hasil Monitoring Tindak Lanjut Hasil Reviu",
        "210" => "Laporan Hasil Monitoring Tindak Lanjut Hasil Evaluasi",
        "220" => "Laporan Pendampingan",
        "230" => "Laporan Sosialisasi",
        "240" => "Laporan Bimbingan Teknis",
        "500" => "Evaluasi Hasil Pengawasan",
        "510" => "Telaah Sejawat",
        "520" => "Pengolahan Hasil Pengawasan",
        "530" => "Penjaminan Kualitas Pengawasan"
    ];
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

        $usulan = NormaHasil::with('normaHasilAccepted', 'masterLaporan')
            ->where('user_id', auth()->user()->id)
            ->whereYear('created_at', $year)
            ->latest()
            ->get();
        // get year from created_at distinct
        $year = NormaHasil::selectRaw('YEAR(created_at) as year')->distinct()->orderBy('year', 'desc')->get();

        $yearValues = $year->pluck('year')->toArray();

        if (!in_array($currentYear, $yearValues)) {
            // If the current year is not in the array, add it
            $year->push((object) ['year' => $currentYear]);
            $yearValues[] = $currentYear; // Update the year values array
        }

        $year = $year->sortByDesc('year');



        return view('pegawai.norma-hasil.index', [
            'usulan' => $usulan,
            'kodeHasilPengawasan' => $this->kodeHasilPengawasan,
            'jenisNormaHasil' => $this->hasilPengawasan,
            'year' => $year,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $rencanaKerja = RencanaKerja::latest()->whereHas('timkerja', function ($query) {
            $query->whereNot('status', 0);
        })->whereHas('pelaksana', function ($query) {
            $query->where('id_pegawai', auth()->user()->id);
        })->get();
        $masterLaporan = MasterLaporan::where('is_aktif', 1)->get();
        // $stks = StKinerja::latest()->where('user_id', auth()->user()->id)->where('status', 5)->get();
        return view('pegawai.norma-hasil.create', [
            // "stks" => $stks
            'rencanaKerja' => $rencanaKerja,
            'hasilPengawasan' => $this->hasilPengawasan,
            'masterLaporan' => $masterLaporan,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreNormaHasilRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreNormaHasilRequest $request)
    {
        $user_id = auth()->user()->id;
        $rencanaKerja = RencanaKerja::find($request->rencana_id);
        $unit_kerja = '0' . $rencanaKerja->timkerja->unitkerja;
        $tanggal = date('Y-m-d');
        // dd($request->all());

        NormaHasil::create([
            'user_id' => $user_id,
            'unit_kerja' => $unit_kerja,
            'tugas_id' => $request->rencana_id,
            'jenis_norma_hasil_id' => $request->jenis_norma_hasil,
            'document_path' => $request->url_norma_hasil,
            'nama_dokumen' => $request->nama_dokumen,
            'tanggal' => $tanggal,
            'status_norma_hasil' => 'diperiksa',
            'bulan_pelaporan' => $request->bulan_pelaporan,
            'laporan_pengawasan_id' => $request->bulan_pelaporan,
        ]);

        $norma_hasil_id = NormaHasil::latest()->first()->id;


        ObjekNormaHasil::create([
            'objek_id' => $request->objek_kegiatan,
            'norma_hasil_id' => $norma_hasil_id
        ]);

        return redirect('pegawai/norma-hasil')->with('success', 'Berhasil mengajukan usulan norma hasil!');
    }



    /**
     * Display the specified resource.
     *
     * @param  \App\Models\NormaHasil  $normaHasil
     * @return \Illuminate\Http\Response
     */
    public function show(NormaHasil $norma_hasil)
    {
        $month = [
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
        $objek = ObjekNormaHasil::where('norma_hasil_id', $norma_hasil->id)->get();

        return view('pegawai.norma-hasil.show', [
            "usulan" => $norma_hasil,
            "objek" => $objek,
            "month" => $month,
            "kodeHasilPengawasan" => $this->kodeHasilPengawasan,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\NormaHasil  $normaHasil
     * @return \Illuminate\Http\Response
     */
    public function edit(NormaHasil $norma_hasil)
    {
        $stks = StKinerja::latest()->where('user_id', auth()->user()->id)->where('status', 5)->get();
        return view('pegawai.norma-hasil.edit', [
            "usulan" => $norma_hasil,
            "stks" => $stks
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateNormaHasilRequest  $request
     * @param  \App\Models\NormaHasil  $normaHasil
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateNormaHasilRequest $request, NormaHasil $norma_hasil)
    {
        // dd($request->all());
        if ($request->status_norma_hasil === 'dibatalkan') {
            $norma_hasil->update([
                'status_norma_hasil' => $request->status_norma_hasil,
                'catatan_norma_hasil' => $request->alasan
            ]);
            NormaHasilAccepted::where('id_norma_hasil', $request->id)
                ->update([
                    'status_verifikasi_arsiparis' => 'dibatalkan',
                ]);
            
            $laporan = NormaHasilAccepted::where('id_norma_hasil', $request->id)->first();
            $id_laporan = $laporan->id;            
            $normahasil_tim = NormaHasilTim::where('laporan_id', $id_laporan)->first();
            if ($normahasil_tim) {
                $normahasil_tim->delete();
            }

            // return back with success message
            return redirect()->back()->with('success', 'Nomor Norma Hasil Dibatalkan!');
        } else {
            $norma_hasil->update([
                'status_norma_hasil' => 'ditolak',
                'catatan_norma_hasil' => $request->alasan
            ]);

            // return back with success message
            return redirect()->back()->with('success', 'Usulan Norma Hasil Berhasil Ditolak');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\NormaHasil  $normaHasil
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $normahasil_tim = NormaHasil::findOrFail($id);
        $normahasil_laporan = NormaHasilAccepted::where('id_norma_hasil', $id)->first();

        if ($normahasil_laporan === NULL) {
            $normahasil_tim->delete();
            $request->session()->flash('success', 'Berhasil menghapus data norma hasil.');
            $request->session()->flash('alert-type', 'success');
            return response()->json([
                'success' => true,
                'message' => 'Berhasil menghapus data norma hasil',
            ]);
        } else {
            $request->session()->flash('danger', 'Ada Norma Hasil.');
            $request->session()->flash('alert-type', 'danger');
        };
    }
}
