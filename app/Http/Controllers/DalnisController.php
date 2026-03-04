<?php

namespace App\Http\Controllers;

use App\Models\NormaHasil;
use App\Models\ObjekNormaHasil;
use App\Models\PelaksanaTugas;
use Illuminate\Http\Request;

class DalnisController extends Controller
{
    protected $month = [
        0 => '',
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

    public function index(Request $request)
    {
        $id_pegawai = auth()->user()->id;
        $year = request()->year;
        if ($year == null) {
            $year = date('Y');
        } else {
            $year = $year;
        }
        $rencanaKerjaDalnis = PelaksanaTugas::where('id_pegawai', $id_pegawai)
            ->where('pt_jabatan', '1')
            ->pluck('id_rencanakerja');

        $user_dalnis = PelaksanaTugas::whereIn('id_rencanakerja', $rencanaKerjaDalnis)
            ->where('id_pegawai', '!=', $id_pegawai)
            ->pluck('id_pegawai');

        $usulan = NormaHasil::whereIn('user_id', $user_dalnis)
            ->whereIn('tugas_id', $rencanaKerjaDalnis)
            ->whereYear('created_at', $year)
            ->get();        
        $year = NormaHasil::selectRaw('YEAR(created_at) as year')->distinct()->orderBy('year', 'desc')->get();

        $currentYear = date('Y');

        $yearValues = $year->pluck('year')->toArray();

        if (!in_array($currentYear, $yearValues)) {
            // If the current year is not in the array, add it
            $year->push((object)['year' => $currentYear]);
            $yearValues[] = $currentYear; // Update the year values array
        }

        $year = $year->sortByDesc('year');

        return view('pegawai.usulan-norma-hasil-dalnis.index', [
            'usulan' => $usulan,
            'kodeHasilPengawasan' => $this->kodeHasilPengawasan,
            'jenisNormaHasil' => $this->hasilPengawasan,
            'type_menu' => 'rencana-kinerja',
            'year' => $year,
        ]);
    }

    public function show($id)
    {
        $usulan = NormaHasil::find($id);
        $objek = ObjekNormaHasil::where('norma_hasil_id', $id)->get();

        return view('pegawai.usulan-norma-hasil.show', [
            'usulan' => $usulan,
            'objek' => $objek,
            'type_menu' => 'rencana-kinerja',
            'kodeHasilPengawasan' => $this->kodeHasilPengawasan,
            'month' => $this->month,
            'dalnis' => '1'
        ]);
    }
}
