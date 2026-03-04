<?php

namespace App\Http\Controllers;

use App\Models\Sl;
use App\Models\Stp;
use App\Models\Stpd;
use App\Models\User;
use App\Models\Surat;
use App\Models\TimKerja;
use App\Models\StKinerja;
use App\Models\Kompetensi;
use App\Models\NormaHasil;
use App\Models\RencanaKerja;
use Illuminate\Http\Request;
use App\Models\NormaHasilTim;
use App\Models\KendaliMutuTim;
use App\Models\PelaksanaTugas;
use App\Models\MasterUnitKerja;
use App\Models\ObjekPengawasan;
use App\Models\TargetIkuUnitKerja;
use App\Models\UsulanSuratSrikandi;
use App\Models\LaporanObjekPengawasan;
use App\Models\RencanaDiklat;
use App\Models\UploadSkp;
use Carbon\Carbon;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
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

    protected $skp = [
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
        12 => 'Desember',
        13 => 'Penetapan',
        14 => 'Penilaian'
    ];

    protected $master_unit_kerja = [
        '8100' => 'Inspektorat Wilayah I',
        '8200' => 'Inspektorat Wilayah II',
        '8300' => 'Inspektorat Wilayah III',
        '8010' => 'Bagian Umum Inspektorat Utama',
        '8000' => 'Inspektorat Utama'
    ];

    function admin(Request $request)
    {
        $year = $request->year;

        if ($year == null) {
            $year = date('Y');
        } else {
            $year = $year;
        }

        $pegawaiCount = $this->pegawaiCount();
        $objekCount = $this->objekCount();
        $timKerjaCount = $this->adminTimKerjaCount($year);


        if ($year == null) {
            $year = date('Y');
        } else {
            $year = $year;
        }
        return view('admin.index', [
            'type_menu' => 'dashboard',
            'pegawai8000Count' => $pegawaiCount['pegawai8000'],
            'pegawai8010Count' => $pegawaiCount['pegawai8010'],
            'pegawai8100Count' => $pegawaiCount['pegawai8100'],
            'pegawai8200Count' => $pegawaiCount['pegawai8200'],
            'pegawai8300Count' => $pegawaiCount['pegawai8300'],

            'unitKerjaCount' => $objekCount['unitKerjaCount'],
            'satuanKerjaCount' => $objekCount['satuanKerjaCount'],
            'wilayahKerjaCount' => $objekCount['wilayahKerjaCount'],

            'timKerjaTotalCount' => $timKerjaCount['timKerjaTotalCount'],
            'timKerjaPenyusunanCount' => $timKerjaCount['timKerjaPenyusunanCount'],
            'timKerjaDikirimCount' => $timKerjaCount['timKerjaDikirimCount'],
            'timKerjaPercentagePenyusunan' => $timKerjaCount['timKerjaPercentagePenyusunan'],
            'timKerjaPercentageDikirim' => $timKerjaCount['timKerjaPercentageDikirim'],
        ]);
    }


    function pegawai(Request $request)
    {

        $year = $request->year;
        $id_unitkerja = auth()->user()->unit_kerja;

        if ($year == null) {
            $year = date('Y');
        } else {
            $year = $year;
        }

        $suratSrikandiCount = $this->suratSrikandiCount($year);
        $normaHasilCount = $this->usulanNormaHasilCount($year);
        $usulanNormaHasilCount = NormaHasil::with('user', 'normaHasilAccepted')->latest()->whereYear('created_at', $year)->whereHas('rencanaKerja.timkerja', function ($query) {
            $query->where('id_ketua', auth()->user()->id)->where('status_norma_hasil', 'diperiksa');
        })->count();
        $timKerjaCount = $this->ketuaTimKerjaCount($year);

        $skpCount = $this->uploadSKPCount($year);

        $skpTimCount = $this->SKPTimKerjaCount($year);

        $rencanaKerjaDalnis = PelaksanaTugas::where('id_pegawai', auth()->user()->id)
            ->where('pt_jabatan', '1')
            ->pluck('id_rencanakerja');

        $user_dalnis = PelaksanaTugas::whereIn('id_rencanakerja', $rencanaKerjaDalnis)
            ->where('id_pegawai', '!=', auth()->user()->id)
            ->pluck('id_pegawai');

        $usulan = NormaHasil::whereIn('user_id', $user_dalnis)
            ->whereIn('tugas_id', $rencanaKerjaDalnis)
            ->whereYear('created_at', $year)
            ->where('status_norma_hasil', 'diperiksa')
            ->count();
        
        $diklat = RencanaDiklat::where('id_pegawai', auth()->user()->id)->whereYear('start_date', $year)->count();

        

        return view('pegawai.index', [
            'type_menu' => 'usulan-surat-srikandi',
            'tahun' => $year,
            // Surat Srikandi
            'percentage_usulan' => $suratSrikandiCount['percentage_usulan'],
            'percentage_disetujui' => $suratSrikandiCount['percentage_disetujui'],
            'percentage_ditolak' => $suratSrikandiCount['percentage_ditolak'],
            'total_usulan' => $suratSrikandiCount['usulanCount'],
            'usulanCount' => $suratSrikandiCount['usulanCount'],
            'disetujuiCount' => $suratSrikandiCount['disetujuiCount'],
            'ditolakCount' => $suratSrikandiCount['ditolakCount'],
            'suratCount' => $suratSrikandiCount['totalCount'],
            // Norma Hasil
            'normaHasilCount' => $normaHasilCount['usulanCount'],
            'normaHasilDisetujui' => $normaHasilCount['disetujuiCount'],
            'normaHasilDitolak' => $normaHasilCount['ditolakCount'],
            'normaHasilDiperiksa' => $normaHasilCount['diperiksaCount'],
            'normaHasilDibatalkan' => $normaHasilCount['dibatalkanCount'],
            'normaHasilPercentageDiperiksa' => $normaHasilCount['percentage_diperiksa'],
            'normaHasilPercentageDisetujui' => $normaHasilCount['percentage_disetujui'],
            'normaHasilPercentageDitolak' => $normaHasilCount['percentage_ditolak'],
            'normaHasilPercentageDibatalkan' => $normaHasilCount['percentage_dibatalkan'],
            'usulanNormaHasilCount' => $usulanNormaHasilCount,
            'usulanNormaHasiDalnisCount' => $usulan,
            // Tim Kerja - Ketua Tim Kerja
            'timKerjaTotalCount' => $timKerjaCount['timKerjaTotalCount'],
            'timKerjaPenyusunanCount' => $timKerjaCount['timKerjaPenyusunanCount'],
            'timKerjaDikirimCount' => $timKerjaCount['timKerjaDikirimCount'],
            'timKerjaPercentagePenyusunan' => $timKerjaCount['timKerjaPercentagePenyusunan'],
            'timKerjaPercentageDikirim' => $timKerjaCount['timKerjaPercentageDikirim'],
            // Upload SKP
            'SKPDiperiksa' => $skpCount['diperiksaCount'],
            'SKPDitolak' => $skpCount['ditolakCount'],
            'SKPSudahKirim' => $skpCount['sudahKirimCount'],
            'SKPBelumUnggah' => $skpCount['belumUnggahCount'],
            'SKPDiperiksaPersentase' => $skpCount['percentage_diperiksa'],
            'SKPBelumUnggahPersentase' => $skpCount['percentage_belumUnggah'],
            'SKPDitolakPersentase' => $skpCount['percentage_ditolak'],
            'SKPSudahKirimPersentase' => $skpCount['percentage_sudahKirim'],
            'SKPTotal' => $skpCount['totalCount'],
            // SKP Ketua Tim
            'timKerjaSKPTotalCount' => $skpTimCount['totalSKPTim'],
            'timKerjaSKPBelumUnggahCount' => $skpTimCount['belumUnggahSKPCount'],
            'timKerjaSKPDiperiksaCount' => $skpTimCount['diperiksaSKPCount'],
            'timKerjaSKPDitolakCount' => $skpTimCount['ditolakSKPCount'],
            'timKerjaSKPSudahKirimCount' => $skpTimCount['sudahKirimSKPCount'],
            'timKerjaSKPBelumUnggahPersentase' => $skpTimCount['percentage_tim_belumUnggah'],
            'timKerjaSKPDiperiksaPersentase' => $skpTimCount['percentage_tim_diperiksa'],
            'timKerjaSKPDitolakPersentase' => $skpTimCount['percentage_tim_ditolak'],
            'timKerjaSKPSudahKirimPersentase' => $skpTimCount['percentage_tim_sudahKirim'],
            // Diklat
            'diklatCount' => $diklat,
        ]);
    }

    function sekretaris(Request $request)
    {
        $year = $request->year;

        if ($year == null) {
            $year = date('Y');
        } else {
            $year = $year;
        }
        $this->authorize('sekretaris');
        $suratSrikandiCount = $this->sekretarisSuratSrikandiCount($year);


        return view('sekretaris.index', [
            'type_menu' => 'usulan-surat-srikandi',
            'percentage_usulan' => $suratSrikandiCount['percentage_usulan'],
            'percentage_disetujui' => $suratSrikandiCount['percentage_disetujui'],
            'percentage_ditolak' => $suratSrikandiCount['percentage_ditolak'],
            'total_usulan' => $suratSrikandiCount['total_usulan'],
            'usulanCount' => $suratSrikandiCount['usulanCount'],
            'disetujuiCount' => $suratSrikandiCount['disetujuiCount'],
            'ditolakCount' => $suratSrikandiCount['ditolakCount'],

        ]);
    }

    function inspektur()
    {
        $this->authorize('inspektur');
        $unit = $this->master_unit_kerja[auth()->user()->unit_kerja];
        // $stk = StKinerja::where('unit_kerja', auth()->user()->unit_kerja)->count();
        // $stk_sum = StKinerja::whereHas('rencanaKerja.proyek.timkerja', function ($query) {
        //     $query->where('unitkerja', auth()->user()->unit_kerja);
        // })->count();
        // $stk_need_approval = StKinerja::where('status', 0)->whereHas('rencanaKerja.proyek.timkerja', function ($query) {
        //     $query->where('unitkerja', auth()->user()->unit_kerja);
        // })->count();
        // $stp_sum = Stp::where('unit_kerja', auth()->user()->unit_kerja)->count();
        // $stp_need_approval = Stp::where('status', 3)->where('unit_kerja', auth()->user()->unit_kerja)->count();
        // $stpd_sum = Stpd::where('unit_kerja', auth()->user()->unit_kerja)->count();
        // $stpd_need_approval = Stpd::where('status', 0)->where('unit_kerja', auth()->user()->unit_kerja)->count();


        return view('inspektur.index', [
            'unit' => $unit,
            'bulan' => $this->months,
            // "stk_sum" => $stk_sum,
            // "stk_need_approval" => $stk_need_approval,
            // "stp_sum" => $stp_sum,
            // "stp_need_approval" => $stp_need_approval,
            // "stpd_sum" => $stpd_sum,
            // "stpd_need_approval" => $stpd_need_approval
        ]);
    }

    function analis_sdm(Request $request)
    {
        $this->authorize('analis_sdm');
        $pegawai = User::all();
        $kompetensi = Kompetensi::where('status', 1)->get();
        $min_year = date('Y', strtotime($kompetensi->min('tgl_sertifikat')));
        $max_year = date('Y', strtotime($kompetensi->max('tgl_sertifikat')));
        $years = range($min_year, $max_year);
        $diklat_count = $kompetensi->groupBy('pegawai_id')
            ->map->countBy(function ($item) {
                return date("Y", strtotime($item->tgl_sertifikat));
            });
        $jp_count = $kompetensi->groupBy('pegawai_id')
            ->map->groupBy(function ($item) {
                return date("Y", strtotime($item->tgl_sertifikat));
            })
            ->map->map->map->sum('durasi');

        $tahun = $request->input('tahun', now()->year);

        $jumlahPerUnit = $this->jumlahSKPSudahKirimPerBulanPerUnit($tahun);

        // Tambahkan nama bulan dari controller
        $namaBulan = [
            '01' => 'Jan',
            '02' => 'Feb',
            '03' => 'Mar',
            '04' => 'Apr',
            '05' => 'Mei',
            '06' => 'Jun',
            '07' => 'Jul',
            '08' => 'Agu',
            '09' => 'Sep',
            '10' => 'Okt',
            '11' => 'Nov',
            '12' => 'Des',
            '13' => 'Penetapan',
            '14' => 'Penilaian',
        ];

        return view('analis-sdm.index', [
            'pegawai' => $pegawai,
            'kompetensi' => $kompetensi,
            'diklat_count' => $diklat_count,
            'jp_count' => $jp_count,
            'years' => $years,
            'jumlahPerUnit' => $jumlahPerUnit,
            'namaBulan' => $namaBulan,
        ]);
    }


    function ikuUnitKerja($year)
    {
        $targetIkuUnitKerjaCount = TargetIkuUnitKerja::latest()->whereYear('created_at', $year)->where('status', 1)->count();
        $realisasiIkuUnitKerjaCount = TargetIkuUnitKerja::latest()->whereYear('created_at', $year)->where('status', 2)->count();
        $evaluasiIkuUnitKerjaCount = TargetIkuUnitKerja::latest()->whereYear('created_at', $year)->where('status', 3)->count();
        $selesaiIkuUnitKerjaCount = TargetIkuUnitKerja::latest()->whereYear('created_at', $year)->where('status', 4)->count();

        $totalIkuUnitKerjaCount = TargetIkuUnitKerja::latest()->whereYear('created_at', $year)->count();

        $percentageTarget = $targetIkuUnitKerjaCount != 0 ? intval($targetIkuUnitKerjaCount / ($totalIkuUnitKerjaCount) * 100) : 0;
        $percentageRealisasi = $realisasiIkuUnitKerjaCount != 0 ? intval($realisasiIkuUnitKerjaCount / ($totalIkuUnitKerjaCount) * 100) : 0;
        $percentageEvaluasi = $evaluasiIkuUnitKerjaCount != 0 ? intval($evaluasiIkuUnitKerjaCount / ($totalIkuUnitKerjaCount) * 100) : 0;
        $percentageSelesai = $selesaiIkuUnitKerjaCount != 0 ? intval($selesaiIkuUnitKerjaCount / ($totalIkuUnitKerjaCount) * 100) : 0;

        return [
            'totalIkuUnitKerjaCount' => $totalIkuUnitKerjaCount,
            'targetIkuUnitKerjaCount' => $targetIkuUnitKerjaCount,
            'realisasiIkuUnitKerjaCount' => $realisasiIkuUnitKerjaCount,
            'evaluasiIkuUnitKerjaCount' => $evaluasiIkuUnitKerjaCount,
            'selesaiIkuUnitKerjaCount' => $selesaiIkuUnitKerjaCount,
            'percentageTarget' => $percentageTarget,
            'percentageRealisasi' => $percentageRealisasi,
            'percentageEvaluasi' => $percentageEvaluasi,
            'percentageSelesai' => $percentageSelesai,
        ];
    }


    function perencana(Request $request)
    {
        $year = $request->year;

        if ($year == null) {
            $year = date('Y');
        } else {
            $year = $year;
        }

        $ikuUnitKerja = $this->ikuUnitKerja($year);

        $this->authorize('perencana');
        return view('perencana.index', [
            'type_menu' => 'rencana-kerja',
            'totalIkuUnitKerjaCount' => $ikuUnitKerja['totalIkuUnitKerjaCount'],
            'targetIkuUnitKerjaCount' => $ikuUnitKerja['targetIkuUnitKerjaCount'],
            'realisasiIkuUnitKerjaCount' => $ikuUnitKerja['realisasiIkuUnitKerjaCount'],
            'evaluasiIkuUnitKerjaCount' => $ikuUnitKerja['evaluasiIkuUnitKerjaCount'],
            'selesaiIkuUnitKerjaCount' => $ikuUnitKerja['selesaiIkuUnitKerjaCount'],
            'percentageTarget' => $ikuUnitKerja['percentageTarget'],
            'percentageRealisasi' => $ikuUnitKerja['percentageRealisasi'],
            'percentageEvaluasi' => $ikuUnitKerja['percentageEvaluasi'],
            'percentageSelesai' => $ikuUnitKerja['percentageSelesai'],

        ]);
    }

    function arsiparis(Request $request)
    {
        $this->authorize('arsiparis');

        $year = $request->year;

        if ($year == null) {
            $year = date('Y');
        } else {
            $year = $year;
        }

        $data_tim = [];
        $timkerja = TimKerja::where('tahun', $year)->get();

        //data tiap tim
        foreach ($timkerja as $tim) {
            $data_tim[$tim->id_timkerja]['nama'] = $tim->nama;
            $data_tim[$tim->id_timkerja]['pjk'] = $tim->ketua->name;
            //data tiap bulan
            for ($i = 1; $i < 13; $i++) {
                $laporanobjek = LaporanObjekPengawasan::whereRelation('objekPengawasan.rencanakerja.proyek.timkerja', function (Builder $query) use ($tim) {
                    $query->where('id_timkerja', $tim->id_timkerja);
                })->where('month', $i)->where('status', 1)->get();

                //jumlah tugas
                $jumlah_tugas = $laporanobjek->countBy('objekPengawasan.id_rencanakerja')->count();
                if ($jumlah_tugas == 0) {
                    $data_tim[$tim->id_timkerja]['data_bulan'][$i]['jumlah_tugas'] = '-';
                    $data_tim[$tim->id_timkerja]['data_bulan'][$i]['jumlah_st'] = '-';
                    $data_tim[$tim->id_timkerja]['data_bulan'][$i]['target_nh'] = '-';
                    $data_tim[$tim->id_timkerja]['data_bulan'][$i]['jumlah_nh'] = '-';
                    $data_tim[$tim->id_timkerja]['data_bulan'][$i]['jumlah_km'] = '-';
                    continue;
                }
                $data_tim[$tim->id_timkerja]['data_bulan'][$i]['jumlah_tugas'] = $jumlah_tugas;

                $surat_tugas = UsulanSuratSrikandi::whereIn('rencana_kerja_id', $laporanobjek->pluck('objekPengawasan.id_rencanakerja'))
                    // ->where('status', 'disetujui')
                    ->get();
                //jumlah surat tugas masuk
                $data_tim[$tim->id_timkerja]['data_bulan'][$i]['jumlah_st'] = $surat_tugas->count();

                //jumlah target norma hasil
                $data_tim[$tim->id_timkerja]['data_bulan'][$i]['target_nh'] = $laporanobjek->count();

                $norma_hasil = NormaHasilTim::whereRelation('normaHasilAccepted', function (Builder $query) use ($i, $laporanobjek) {
                    // $query->where('status_verifikasi_arsiparis', 'disetujui');
                    $query->whereRelation('normaHasil.laporanPengawasan', function (Builder $q) use ($i, $laporanobjek) {
                        $q->where('month', $i)->where('status', 1);
                        $q->whereRelation('objekPengawasan', function (Builder $q2) use ($laporanobjek) {
                            $q2->whereIn('id_rencanakerja', $laporanobjek->pluck('objekPengawasan.id_rencanakerja'));
                        });
                    });
                })->orWhereRelation('normaHasilDokumen', function (Builder $query) use ($i, $laporanobjek) {
                    // $query->where('status_verifikasi_arsiparis', 'disetujui');
                    $query->whereRelation('laporanPengawasan', function (Builder $q) use ($i, $laporanobjek) {
                        $q->where('month', $i)->where('status', 1);
                        $q->whereRelation('objekPengawasan', function (Builder $q2) use ($laporanobjek) {
                            $q2->whereIn('id_rencanakerja', $laporanobjek->pluck('objekPengawasan.id_rencanakerja'));
                        });
                    });
                })->get();

                //jumlah norma hasil masuk
                $data_tim[$tim->id_timkerja]['data_bulan'][$i]['jumlah_nh'] = $norma_hasil->count();

                $kendali_mutu = KendaliMutuTim::whereRelation('laporanObjekPengawasan', function (Builder $query) use ($i, $laporanobjek) {
                    $query->where('month', $i)->where('status', 1);
                    $query->whereRelation('objekPengawasan', function (Builder $q) use ($laporanobjek) {
                        $q->whereIn('id_rencanakerja', $laporanobjek->pluck('objekPengawasan.id_rencanakerja'));
                    });
                })
                    // ->where('status', 'disetujui')
                    ->get();
                //jumlah kendali mutu
                $data_tim[$tim->id_timkerja]['data_bulan'][$i]['jumlah_km'] = $kendali_mutu->count();
            }
        }

        return view('arsiparis.index', [
            'type_menu'     => 'kinerja-tim',
            'months'    => $this->months
        ])->with('data_tim', $data_tim);
    }

    function detailKinerjaTim($id, $bulan)
    {
        $this->authorize('arsiparis');

        $laporanObjek = LaporanObjekPengawasan::where('month', $bulan)->where('status', 1)
            ->whereRelation('objekPengawasan.rencanakerja.proyek.timkerja', function (Builder $query) use ($id) {
                $query->where('id_timkerja', $id);
            })->get();

        $surat_tugas = UsulanSuratSrikandi::whereIn('rencana_kerja_id', $laporanObjek->pluck('objekPengawasan.id_rencanakerja'))
            // ->where('status', 'disetujui')
            ->get();
        $surat_tugas_arr = [];
        foreach ($surat_tugas as $surat) {
            $surat_tugas_arr[$surat->rencana_kerja_id] = $surat;
        }

        $norma_hasil = NormaHasilTim::whereRelation('normaHasilAccepted', function (Builder $query) use ($laporanObjek) {
            // $query->where('status_verifikasi_arsiparis', 'disetujui');
            $query->whereRelation('normaHasil', function (Builder $q) use ($laporanObjek) {
                $q->whereIn('laporan_pengawasan_id', $laporanObjek->pluck('id'));
            });
        })->orWhereRelation('normaHasilDokumen', function (Builder $query) use ($laporanObjek) {
            $query->whereIn('laporan_pengawasan_id', $laporanObjek->pluck('id'));
            // $query->where('status_verifikasi_arsiparis', 'disetujui');
        })->get();

        $norma_hasil_arr = [];
        foreach ($norma_hasil as $dokumen) {
            if ($dokumen->jenis == 1) {
                $bulan_id = $dokumen->normaHasilAccepted->normaHasil->laporan_pengawasan_id;
                $norma_hasil_arr[$bulan_id] = $dokumen->normaHasilAccepted;
            } else {
                $bulan_id = $dokumen->normaHasilDokumen->laporan_pengawasan_id;
                $norma_hasil_arr[$bulan_id] = $dokumen->normaHasilDokumen;
            }
            $norma_hasil_arr[$bulan_id]['jenis'] = $dokumen->jenis;
        }

        $kendali_mutu = KendaliMutuTim::whereIn('laporan_pengawasan_id', $laporanObjek->pluck('id'))->get();
        // ->where('status', 'disetujui')->get();
        $kendali_mutu_arr = [];
        foreach ($kendali_mutu as $dokumen) {
            $kendali_mutu_arr[$dokumen->laporan_pengawasan_id] = $dokumen;
        }

        return view('arsiparis.show', [
            'type_menu' => 'kinerja-tim',
            'laporanObjek' => $laporanObjek,
            'months' => $this->months,
            'norma_hasil' => $norma_hasil_arr,
            'kendali_mutu' => $kendali_mutu_arr,
            'surat_tugas' => $surat_tugas_arr,
            'bulan' => $bulan
        ]);
    }


    // Admin
    function pegawaiCount()
    {
        $pegawai8000 = User::where('unit_kerja', 8000)->count();
        $pegawai8010 = User::where('unit_kerja', 8010)->count();
        $pegawai8100 = User::where('unit_kerja', 8100)->count();
        $pegawai8200 = User::where('unit_kerja', 8200)->count();
        $pegawai8300 = User::where('unit_kerja', 8300)->count();

        return [
            'pegawai8000' => $pegawai8000,
            'pegawai8010' => $pegawai8010,
            'pegawai8100' => $pegawai8100,
            'pegawai8200' => $pegawai8200,
            'pegawai8300' => $pegawai8300,
        ];
    }

    function objekCount()
    {
        $unitKerjaCount = MasterUnitKerja::where('kategori', '1')->count();
        $satuanKerjaCount = MasterUnitKerja::where('kategori', '2')->count();
        $wilayahKerjaCount = MasterUnitKerja::where('kategori', '3')->count();
        return [
            'unitKerjaCount' => $unitKerjaCount,
            'satuanKerjaCount' => $satuanKerjaCount,
            'wilayahKerjaCount' => $wilayahKerjaCount,
        ];
    }

    function adminTimKerjaCount($year)
    {
        $timKerjaPenyusunanCount = TimKerja::with('ketua', 'iku')->whereIn('status', [0, 1])->where('tahun', $year)->get()->count();
        $timKerjaDikirimCount = TimKerja::with('ketua', 'iku')->whereNotIn('status', [0, 1])->where('tahun', $year)->get()->count();

        $timKerjaTotalCount = TimKerja::with('ketua', 'iku')->where('tahun', $year)->get()->count();

        $timKerjaPercentagePenyusunan = $timKerjaPenyusunanCount != 0 ? intval($timKerjaPenyusunanCount / ($timKerjaTotalCount) * 100) : 0;
        $timKerjaPercentageDikirim = $timKerjaDikirimCount != 0 ? intval($timKerjaDikirimCount / ($timKerjaTotalCount) * 100) : 0;


        return [
            'timKerjaTotalCount' => $timKerjaTotalCount,
            'timKerjaPenyusunanCount' => $timKerjaPenyusunanCount,
            'timKerjaDikirimCount' => $timKerjaDikirimCount,
            'timKerjaPercentagePenyusunan' => $timKerjaPercentagePenyusunan,
            'timKerjaPercentageDikirim' => $timKerjaPercentageDikirim,
        ];
    }


    // Pegawai

    function suratSrikandiCount($year)
    {
        $usulanCount = UsulanSuratSrikandi::with('user')->latest()->where('user_id', auth()->user()->id)->whereYear('created_at', $year)->where('status', 'usulan')->count();
        $disetujuiCount = UsulanSuratSrikandi::with('user')->latest()->where('user_id', auth()->user()->id)->whereYear('created_at', $year)->where('status', 'disetujui')->count();
        $ditolakCount = UsulanSuratSrikandi::with('user')->latest()->where('user_id', auth()->user()->id)->whereYear('created_at', $year)->where('status', 'ditolak')->count();

        if ($usulanCount + $disetujuiCount + $ditolakCount != 0) {
            $percentage_usulan = intval($usulanCount / ($usulanCount + $disetujuiCount + $ditolakCount) * 100);
            $percentage_disetujui = intval($disetujuiCount / ($usulanCount + $disetujuiCount + $ditolakCount) * 100);
            $percentage_ditolak = intval($ditolakCount / ($usulanCount + $disetujuiCount + $ditolakCount) * 100);
        } else {
            $percentage_usulan = 0;
            $percentage_disetujui = 0;
            $percentage_ditolak = 0;
        }

        return [
            'percentage_usulan' => $percentage_usulan,
            'percentage_disetujui' => $percentage_disetujui,
            'percentage_ditolak' => $percentage_ditolak,
            'usulanCount' => $usulanCount,
            'disetujuiCount' => $disetujuiCount,
            'ditolakCount' => $ditolakCount,
            'totalCount' => $usulanCount + $disetujuiCount + $ditolakCount
        ];
    }

    function ketuaTimKerjaCount($year)
    {
        $id_pegawai = auth()->user()->id;
        $timKerjaPenyusunanCount = TimKerja::with('ketua', 'iku')
            ->where(function ($query) use ($id_pegawai) {
                $query->where('id_ketua', $id_pegawai)
                    ->orWhereHas('operatorRencanaKinerja', function ($query) use ($id_pegawai) {
                        $query->where('operator_id', $id_pegawai);
                    });
            })
            ->whereIn('status', [0, 1])
            ->where('tahun', $year)
            ->count();
        $timKerjaDikirimCount = TimKerja::with('ketua', 'iku')
            ->where(function ($query) use ($id_pegawai) {
                $query->where('id_ketua', $id_pegawai)
                    ->orWhereHas('operatorRencanaKinerja', function ($query) use ($id_pegawai) {
                        $query->where('operator_id', $id_pegawai);
                    });
            })
            ->whereNotIn('status', [0, 1])
            ->where('tahun', $year)
            ->count();

        $timKerjaTotalCount = TimKerja::with('ketua', 'iku')
            ->where(function ($query) use ($id_pegawai) {
                $query->where('id_ketua', $id_pegawai)
                    ->orWhereHas('operatorRencanaKinerja', function ($query) use ($id_pegawai) {
                        $query->where('operator_id', $id_pegawai);
                    });
            })
            ->where('tahun', $year)
            ->count();

        $timKerjaPercentagePenyusunan = $timKerjaPenyusunanCount != 0 ? intval($timKerjaPenyusunanCount / ($timKerjaTotalCount) * 100) : 0;
        $timKerjaPercentageDikirim = $timKerjaDikirimCount != 0 ? intval($timKerjaDikirimCount / ($timKerjaTotalCount) * 100) : 0;


        return [
            'timKerjaTotalCount' => $timKerjaTotalCount,
            'timKerjaPenyusunanCount' => $timKerjaPenyusunanCount,
            'timKerjaDikirimCount' => $timKerjaDikirimCount,
            'timKerjaPercentagePenyusunan' => $timKerjaPercentagePenyusunan,
            'timKerjaPercentageDikirim' => $timKerjaPercentageDikirim,
        ];
    }

    function usulanNormaHasilCount($year)
    {
        $usulan = NormaHasil::with('normaHasilAccepted')->where('user_id', auth()->user()->id)->whereYear('created_at', $year)->get();
        $usulanCount = $usulan->count();
        $diperiksaCount = $usulan->where('status_norma_hasil', 'diperiksa')->count();
        $disetujuiCount = $usulan->where('status_norma_hasil', 'disetujui')->count();
        $ditolakCount = $usulan->where('status_norma_hasil', 'ditolak')->count();
        $dibatalkanCount = $usulan->where('status_norma_hasil', 'dibatalkan')->count();
        return [
            'usulanCount' => $usulanCount,
            'disetujuiCount' => $disetujuiCount,
            'ditolakCount' => $ditolakCount,
            'diperiksaCount' => $diperiksaCount,
            'dibatalkanCount' => $dibatalkanCount,

            'percentage_diperiksa' => $diperiksaCount != 0 ? intval($diperiksaCount / ($usulanCount) * 100) : 0,
            'percentage_disetujui' => $disetujuiCount != 0 ? intval($disetujuiCount / ($usulanCount) * 100) : 0,
            'percentage_ditolak' => $ditolakCount != 0 ? intval($ditolakCount / ($usulanCount) * 100) : 0,
            'percentage_dibatalkan' => $dibatalkanCount != 0 ? intval($dibatalkanCount / ($usulanCount) * 100) : 0,
        ];
    }

    function uploadSKPCount($year)
    {
        $skp = UploadSkp::where('user_id', auth()->user()->id)->where('tahun', $year)->get();
        $diperiksaCount = $skp->where('status', 'Diperiksa')->count();
        $ditolakCount = $skp->where('status', 'Ditolak')->count();
        $sudahKirimCount = $skp->where('status', 'Sudah Kirim')->count();
        $tidakAdaCount = $skp->where('status', 'Tidak Ada')->count();
        $total = 14 - $tidakAdaCount;
        $belumUnggahCount = $total - $diperiksaCount - $ditolakCount - $sudahKirimCount;

        return [
            'diperiksaCount' => $diperiksaCount,
            'ditolakCount' => $ditolakCount,
            'sudahKirimCount' => $sudahKirimCount,
            'belumUnggahCount' => $belumUnggahCount,
            'totalCount' => $total,

            'percentage_belumUnggah' => $belumUnggahCount != 0 ? intval($belumUnggahCount / $total * 100) : 0,
            'percentage_diperiksa' => $diperiksaCount != 0 ? intval($diperiksaCount / $total * 100) : 0,
            'percentage_ditolak' => $ditolakCount != 0 ? intval($ditolakCount / $total * 100) : 0,
            'percentage_sudahKirim' => $sudahKirimCount != 0 ? intval($sudahKirimCount / $total * 100) : 0,
        ];
    }

    function SKPTimKerjaCount($year)
    {
        $bulanSekarang = Carbon::now()->month;
        $id = TimKerja::where('id_ketua', auth()->user()->id)->where('tahun', $year)->first();

        if (!$id) {
            return [
                'message' => 'User ini bukan ketua tim.',
                'anggota' => [],
                'diperiksaSKPCount' => 0,
                'ditolakSKPCount' => 0,
                'sudahKirimSKPCount' => 0,
                'belumUnggahSKPCount' => 0,
                'totalSKPTim' => 0,
                'percentage_tim_belumUnggah' => 0,
                'percentage_tim_diperiksa' => 0,
                'percentage_tim_ditolak' => 0,
                'percentage_tim_sudahKirim' => 0,
            ];
        }

        $tim = TimKerja::with(['rencanaKerja.pelaksana.user'])->find($id->id_timkerja);

        // Ambil semua user (anggota) dari pelaksana tugas
        $anggota = collect();

        foreach ($tim->rencanakerja as $rk) {
            foreach ($rk->pelaksana as $pt) {
                $anggota->push($pt->user);
            }
        }

        $anggota = $anggota->unique('id')->values();
        $jumlahAnggota = $anggota->count();
        $totalDokumen = (2 + $bulanSekarang) * $jumlahAnggota;

        $userIds = $anggota->pluck('id');

        $skps = UploadSkp::whereIn('user_id', $userIds)
            ->where('tahun', $year)
            ->get();

        $diperiksaSKPCount = $skps->where('status', 'Diperiksa')->count();
        $ditolakSKPCount = $skps->where('status', 'Ditolak')->count();
        $sudahKirimSKPCount = $skps->where('status', 'Sudah Kirim')->count();
        $tidakAdaSKPCount = $skps->where('status', 'Tidak Ada')->count();
        $total = $totalDokumen - $tidakAdaSKPCount;
        $belumUnggahSKPCount = $total - $diperiksaSKPCount - $ditolakSKPCount - $sudahKirimSKPCount;

        $percentage_diperiksa = $total ? intval($diperiksaSKPCount / $total * 100) : 0;
        $percentage_ditolak = $total ? intval($ditolakSKPCount / $total * 100) : 0;
        $percentage_sudahKirim = $total ? intval($sudahKirimSKPCount / $total * 100) : 0;
        $percentage_belumUnggah = $total ? intval($belumUnggahSKPCount / $total * 100) : 0;


        return [
            'tim' => $tim,
            'anggota' => $anggota,
            'diperiksaSKPCount' => $diperiksaSKPCount,
            'ditolakSKPCount' => $ditolakSKPCount,
            'sudahKirimSKPCount' => $sudahKirimSKPCount,
            'belumUnggahSKPCount' => $belumUnggahSKPCount,
            'totalSKPTim' => $total,

            'percentage_tim_belumUnggah' => $percentage_belumUnggah,
            'percentage_tim_diperiksa' => $percentage_diperiksa,
            'percentage_tim_ditolak' => $percentage_ditolak,
            'percentage_tim_sudahKirim' => $percentage_sudahKirim,
        ];
    }

    // Sekretaris
    function sekretarisSuratSrikandiCount($year)
    {

        if (auth()->user()->is_sekma) {
            $usulanCount = UsulanSuratSrikandi::with('user')->latest()->whereYear('created_at', $year)->where('status', 'usulan')->count();
            $disetujuiCount = UsulanSuratSrikandi::with('user')->latest()->whereYear('created_at', $year)->where('status', 'disetujui')->count();
            $ditolakCount = UsulanSuratSrikandi::with('user')->latest()->whereYear('created_at', $year)->where('status', 'ditolak')->count();
            $totalCount = UsulanSuratSrikandi::with('user')->latest()->whereYear('created_at', $year)->count();
        } else {
            $unitKerja = auth()->user()->unit_kerja;
            $usulanCount = UsulanSuratSrikandi::with('user')->latest()->where('pejabat_penanda_tangan', $unitKerja)->whereYear('created_at', $year)->where('status', 'usulan')->count();
            $disetujuiCount = UsulanSuratSrikandi::with('user')->latest()->where('pejabat_penanda_tangan', $unitKerja)->whereYear('created_at', $year)->where('status', 'disetujui')->count();
            $ditolakCount = UsulanSuratSrikandi::with('user')->latest()->where('pejabat_penanda_tangan', $unitKerja)->whereYear('created_at', $year)->where('status', 'ditolak')->count();
            $totalCount = UsulanSuratSrikandi::with('user')->latest()->where('pejabat_penanda_tangan', $unitKerja)->whereYear('created_at', $year)->count();
        }

        if ($usulanCount + $disetujuiCount + $ditolakCount != 0) {
            $percentage_usulan = intval($usulanCount / ($usulanCount + $disetujuiCount + $ditolakCount) * 100);
            $percentage_disetujui = intval($disetujuiCount / ($usulanCount + $disetujuiCount + $ditolakCount) * 100);
            $percentage_ditolak = intval($ditolakCount / ($usulanCount + $disetujuiCount + $ditolakCount) * 100);
        } else {
            $percentage_usulan = 0;
            $percentage_disetujui = 0;
            $percentage_ditolak = 0;
        }

        return [
            'percentage_usulan' => $percentage_usulan,
            'percentage_disetujui' => $percentage_disetujui,
            'percentage_ditolak' => $percentage_ditolak,
            'usulanCount' => $usulanCount,
            'disetujuiCount' => $disetujuiCount,
            'ditolakCount' => $ditolakCount,
            'total_usulan' => $totalCount,
        ];
    }

    function kinerjaTim(Request $request)
    {
        $months = [
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

        $year = $request->year;

        if ($year == null) {
            $year = date('Y');
        } else {
            $year = $year;
        }

        $tugas = PelaksanaTugas::where('id_pegawai', auth()->user()->id)
            ->whereRelation('rencanaKerja.proyek.timKerja', function (Builder $query) use ($year) {
                $query->where('tahun', $year);
            })->get();

        $surat_tugas = UsulanSuratSrikandi::whereIn('rencana_kerja_id', $tugas->pluck('id_rencanakerja'))->get();
        // ->where('status', 'disetujui')->get();
        $surat_tugas_arr = [];

        foreach ($surat_tugas as $surat) {
            $surat_tugas_arr[$surat->rencana_kerja_id] = $surat;
        }

        $laporanObjek = LaporanObjekPengawasan::whereRelation('objekPengawasan', function (Builder $query) use ($tugas) {
            $query->whereIn('id_rencanakerja', $tugas->pluck('id_rencanakerja'));
        })->where('status', 1)->get();

        $norma_hasil = NormaHasilTim::whereRelation('normaHasilAccepted', function (Builder $query) use ($laporanObjek) {
            $query->where('status_verifikasi_arsiparis', '!=', 'dibatalkan')
                ->whereRelation('normaHasil', function (Builder $q) use ($laporanObjek) {
                    $q->whereIn('laporan_pengawasan_id', $laporanObjek->pluck('id'));
                });
        })->orWhereRelation('normaHasilDokumen', function (Builder $query) use ($laporanObjek) {
            $query->where('status_verifikasi_arsiparis', '!=', 'dibatalkan')
                ->whereIn('laporan_pengawasan_id', $laporanObjek->pluck('id'));
        })->get();

        $norma_hasil_arr = [];

        foreach ($norma_hasil as $dokumen) {
            if ($dokumen->jenis == 1) {
                $bulan_id = $dokumen->normaHasilAccepted->normaHasil->laporan_pengawasan_id;
                $norma_hasil_arr[$bulan_id] = $dokumen->normaHasilAccepted;
            } else {
                $bulan_id = $dokumen->normaHasilDokumen->laporan_pengawasan_id;
                $norma_hasil_arr[$bulan_id] = $dokumen->normaHasilDokumen;
            }
            $norma_hasil_arr[$bulan_id]['jenis'] = $dokumen->jenis;
        }

        $kendali_mutu = KendaliMutuTim::whereIn('laporan_pengawasan_id', $laporanObjek->pluck('id'))->get();
        // ->where('status', 'disetujui')->get();
        $kendali_mutu_arr = [];

        foreach ($kendali_mutu as $dokumen) {
            $kendali_mutu_arr[$dokumen->laporan_pengawasan_id] = $dokumen;
        }

        return view('pegawai.tugas-tim.index', [
            'type_menu' => 'tugas-tim',
            'laporanObjek' => $laporanObjek,
            'months' => $months,
            'norma_hasil' => $norma_hasil_arr,
            'kendali_mutu' => $kendali_mutu_arr,
            'surat_tugas' => $surat_tugas_arr
        ]);
    }

    public function getChartData(Request $request)
    {

        $tahun = $request->input('tahun', now()->year);
        $bulanSekarang = (Carbon::now()->month) - 1;

        if (auth()->user()->is_irwil && auth()->user()->unit_kerja !== '8010') {
            $unitWilayahMap = [
                auth()->user()->unit_kerja => $this->master_unit_kerja[auth()->user()->unit_kerja],
            ];
        } else {
            $unitWilayahMap = [
                '8100' => 'Inspektorat Wilayah I',
                '8200' => 'Inspektorat Wilayah II',
                '8300' => 'Inspektorat Wilayah III',
                '8010' => 'Bagian Umum Inspektorat Utama',
            ];
        }

        $data = [];

        foreach ($unitWilayahMap as $kode => $nama) {
            $userIds = User::where('unit_kerja', $kode)->where('status', 1)->pluck('id');

            // Hitung dokumen: penetapan (1) + penilaian (1) + bulanan (1–bulan sekarang)
            $expectedDocuments = ($userIds->count() * (2 + $bulanSekarang));

            $summary = UploadSkp::where('tahun', $tahun)
                ->whereIn('user_id', $userIds)
                ->where(function ($q) use ($bulanSekarang) {
                    $q->where('jenis', 'penetapan')
                        ->orWhere('jenis', 'penilaian')
                        ->orWhere(function ($sub) use ($bulanSekarang) {
                            $sub->where('jenis', 'bulanan')
                                ->whereRaw('CAST(bulan AS UNSIGNED) <= ?', [$bulanSekarang]);
                        });
                })
                ->selectRaw("
                    SUM(CASE WHEN status = 'Diperiksa' THEN 1 ELSE 0 END) as diperiksa,
                    SUM(CASE WHEN status = 'Sudah Kirim' THEN 1 ELSE 0 END) as sudah_kirim,
                    SUM(CASE WHEN status = 'Ditolak' THEN 1 ELSE 0 END) as ditolak,
                    SUM(CASE WHEN status = 'Tidak Ada' THEN 1 ELSE 0 END) as tidak_ada
                ")
                ->first();

            $totalAda = (int) $summary->diperiksa + (int) $summary->sudah_kirim + (int) $summary->ditolak;
            $belum = $expectedDocuments - $totalAda - (int) $summary->tidak_ada;

            $data[] = [
                'wilayah' => $nama,
                'belum' => round(($belum / ($expectedDocuments - (int) $summary->tidak_ada)) * 100, 2),
                'diperiksa' => round(($summary->diperiksa / ($expectedDocuments - (int) $summary->tidak_ada)) * 100, 2),
                'sudah_kirim' => round(($summary->sudah_kirim / ($expectedDocuments - (int) $summary->tidak_ada)) * 100, 2),
                'ditolak' => round(($summary->ditolak / ($expectedDocuments - (int) $summary->tidak_ada)) * 100, 2),
                // 'tidak_ada2' => (int) $summary->tidak_ada,
                // 'diperiksa2' => $summary->diperiksa,
                // 'sudah_kirim2' => $summary->sudah_kirim,
                // 'ditolak2' => $summary->ditolak,
                // 'belum2' => $belum,
                // 'total' => $expectedDocuments,
            ];
        }

        return response()->json($data);
    }

    public function getChartIrwil1(Request $request)
    {
        $tahun = $request->input('tahun', now()->year);

        $bulan = $this->skp;

        $dataIrwil1 = [];

        foreach ($bulan as $kodeBulan => $namaBulan) {
            $userIds = User::where('unit_kerja', '8100')->where('status', 1)->pluck('id');
            $expectedDocuments = $userIds->count(); // 1 dokumen per user per bulan/jenis

            // Menentukan jenis dokumen berdasarkan "bulan"
            if ($kodeBulan == 13) {
                $jenis = 'penetapan';
                $query = UploadSkp::where('tahun', $tahun)
                    ->whereIn('user_id', $userIds)
                    ->where('jenis', $jenis);
            } elseif ($kodeBulan == 14) {
                $jenis = 'penilaian';
                $query = UploadSkp::where('tahun', $tahun)
                    ->whereIn('user_id', $userIds)
                    ->where('jenis', $jenis);
            } else {
                // untuk bulan 1–12 jenisnya adalah 'bulanan'
                $jenis = 'bulanan';
                $query = UploadSkp::where('tahun', $tahun)
                    ->whereIn('user_id', $userIds)
                    ->where('jenis', $jenis)
                    ->where('bulan', $kodeBulan);
            }

            $summary = $query
                ->selectRaw("
                    SUM(CASE WHEN status = 'Diperiksa' THEN 1 ELSE 0 END) as diperiksa,
                    SUM(CASE WHEN status = 'Sudah Kirim' THEN 1 ELSE 0 END) as sudah_kirim,
                    SUM(CASE WHEN status = 'Ditolak' THEN 1 ELSE 0 END) as ditolak,
                    SUM(CASE WHEN status = 'Tidak Ada' THEN 1 ELSE 0 END) as tidak_ada
                ")->first();

            $totalAda = (int) $summary->diperiksa + (int) $summary->sudah_kirim + (int) $summary->ditolak;
            $tidakAda = (int) $summary->tidak_ada;
            $totalDokumen = $expectedDocuments - $tidakAda;

            $dataIrwil1[] = [
                'bulan' => $namaBulan,
                'belum' => $totalDokumen > 0 ? round((($expectedDocuments - $totalAda - $tidakAda) / $totalDokumen) * 100, 2) : 0,
                'diperiksa' => $totalDokumen > 0 ? round(($summary->diperiksa / $totalDokumen) * 100, 2) : 0,
                'sudah_kirim' => $totalDokumen > 0 ? round(($summary->sudah_kirim / $totalDokumen) * 100, 2) : 0,
                'ditolak' => $totalDokumen > 0 ? round(($summary->ditolak / $totalDokumen) * 100, 2) : 0,
            ];
        }
        return response()->json($dataIrwil1);
    }

    public function getChartIrwil2(Request $request)
    {
        $tahun = $request->input('tahun', now()->year);

        $bulan = $this->skp;

        $dataIrwil2 = [];

        foreach ($bulan as $kodeBulan => $namaBulan) {
            $userIds = User::where('unit_kerja', '8200')->where('status', 1)->pluck('id');
            $expectedDocuments = $userIds->count(); // 1 dokumen per user per bulan/jenis

            // Menentukan jenis dokumen berdasarkan "bulan"
            if ($kodeBulan == 13) {
                $jenis = 'penetapan';
                $query = UploadSkp::where('tahun', $tahun)
                    ->whereIn('user_id', $userIds)
                    ->where('jenis', $jenis);
            } elseif ($kodeBulan == 14) {
                $jenis = 'penilaian';
                $query = UploadSkp::where('tahun', $tahun)
                    ->whereIn('user_id', $userIds)
                    ->where('jenis', $jenis);
            } else {
                // untuk bulan 1–12 jenisnya adalah 'bulanan'
                $jenis = 'bulanan';
                $query = UploadSkp::where('tahun', $tahun)
                    ->whereIn('user_id', $userIds)
                    ->where('jenis', $jenis)
                    ->where('bulan', $kodeBulan);
            }

            $summary = $query
                ->selectRaw("
                    SUM(CASE WHEN status = 'Diperiksa' THEN 1 ELSE 0 END) as diperiksa,
                    SUM(CASE WHEN status = 'Sudah Kirim' THEN 1 ELSE 0 END) as sudah_kirim,
                    SUM(CASE WHEN status = 'Ditolak' THEN 1 ELSE 0 END) as ditolak,
                    SUM(CASE WHEN status = 'Tidak Ada' THEN 1 ELSE 0 END) as tidak_ada
                ")->first();

            $totalAda = (int) $summary->diperiksa + (int) $summary->sudah_kirim + (int) $summary->ditolak;
            $tidakAda = (int) $summary->tidak_ada;
            $totalDokumen = $expectedDocuments - $tidakAda;

            $dataIrwil2[] = [
                'bulan' => $namaBulan,
                'belum' => $totalDokumen > 0 ? round((($expectedDocuments - $totalAda - $tidakAda) / $totalDokumen) * 100, 2) : 0,
                'diperiksa' => $totalDokumen > 0 ? round(($summary->diperiksa / $totalDokumen) * 100, 2) : 0,
                'sudah_kirim' => $totalDokumen > 0 ? round(($summary->sudah_kirim / $totalDokumen) * 100, 2) : 0,
                'ditolak' => $totalDokumen > 0 ? round(($summary->ditolak / $totalDokumen) * 100, 2) : 0,
            ];
        }
        return response()->json($dataIrwil2);
    }

    public function getChartIrwil3(Request $request)
    {
        $tahun = $request->input('tahun', now()->year);

        $bulan = $this->skp;

        $dataIrwil3 = [];

        foreach ($bulan as $kodeBulan => $namaBulan) {
            $userIds = User::where('unit_kerja', '8300')->where('status', 1)->pluck('id');
            $expectedDocuments = $userIds->count(); // 1 dokumen per user per bulan/jenis

            // Menentukan jenis dokumen berdasarkan "bulan"
            if ($kodeBulan == 13) {
                $jenis = 'penetapan';
                $query = UploadSkp::where('tahun', $tahun)
                    ->whereIn('user_id', $userIds)
                    ->where('jenis', $jenis);
            } elseif ($kodeBulan == 14) {
                $jenis = 'penilaian';
                $query = UploadSkp::where('tahun', $tahun)
                    ->whereIn('user_id', $userIds)
                    ->where('jenis', $jenis);
            } else {
                // untuk bulan 1–12 jenisnya adalah 'bulanan'
                $jenis = 'bulanan';
                $query = UploadSkp::where('tahun', $tahun)
                    ->whereIn('user_id', $userIds)
                    ->where('jenis', $jenis)
                    ->where('bulan', $kodeBulan);
            }

            $summary = $query
                ->selectRaw("
                    SUM(CASE WHEN status = 'Diperiksa' THEN 1 ELSE 0 END) as diperiksa,
                    SUM(CASE WHEN status = 'Sudah Kirim' THEN 1 ELSE 0 END) as sudah_kirim,
                    SUM(CASE WHEN status = 'Ditolak' THEN 1 ELSE 0 END) as ditolak,
                    SUM(CASE WHEN status = 'Tidak Ada' THEN 1 ELSE 0 END) as tidak_ada
                ")->first();

            $totalAda = (int) $summary->diperiksa + (int) $summary->sudah_kirim + (int) $summary->ditolak;
            $tidakAda = (int) $summary->tidak_ada;
            $totalDokumen = $expectedDocuments - $tidakAda;

            $dataIrwil3[] = [
                'bulan' => $namaBulan,
                'belum' => $totalDokumen > 0 ? round((($expectedDocuments - $totalAda - $tidakAda) / $totalDokumen) * 100, 2) : 0,
                'diperiksa' => $totalDokumen > 0 ? round(($summary->diperiksa / $totalDokumen) * 100, 2) : 0,
                'sudah_kirim' => $totalDokumen > 0 ? round(($summary->sudah_kirim / $totalDokumen) * 100, 2) : 0,
                'ditolak' => $totalDokumen > 0 ? round(($summary->ditolak / $totalDokumen) * 100, 2) : 0,
            ];
        }
        return response()->json($dataIrwil3);
    }

    public function getChartBuntama(Request $request)
    {
        $tahun = $request->input('tahun', now()->year);

        $bulan = $this->skp;

        $dataBuntama = [];

        foreach ($bulan as $kodeBulan => $namaBulan) {
            $userIds = User::where('unit_kerja', '8010')->where('status', 1)->pluck('id');
            $expectedDocuments = $userIds->count(); // 1 dokumen per user per bulan/jenis

            // Menentukan jenis dokumen berdasarkan "bulan"
            if ($kodeBulan == 13) {
                $jenis = 'penetapan';
                $query = UploadSkp::where('tahun', $tahun)
                    ->whereIn('user_id', $userIds)
                    ->where('jenis', $jenis);
            } elseif ($kodeBulan == 14) {
                $jenis = 'penilaian';
                $query = UploadSkp::where('tahun', $tahun)
                    ->whereIn('user_id', $userIds)
                    ->where('jenis', $jenis);
            } else {
                // untuk bulan 1–12 jenisnya adalah 'bulanan'
                $jenis = 'bulanan';
                $query = UploadSkp::where('tahun', $tahun)
                    ->whereIn('user_id', $userIds)
                    ->where('jenis', $jenis)
                    ->where('bulan', $kodeBulan);
            }

            $summary = $query
                ->selectRaw("
                    SUM(CASE WHEN status = 'Diperiksa' THEN 1 ELSE 0 END) as diperiksa,
                    SUM(CASE WHEN status = 'Sudah Kirim' THEN 1 ELSE 0 END) as sudah_kirim,
                    SUM(CASE WHEN status = 'Ditolak' THEN 1 ELSE 0 END) as ditolak,
                    SUM(CASE WHEN status = 'Tidak Ada' THEN 1 ELSE 0 END) as tidak_ada
                ")->first();

            $totalAda = (int) $summary->diperiksa + (int) $summary->sudah_kirim + (int) $summary->ditolak;
            $tidakAda = (int) $summary->tidak_ada;
            $totalDokumen = $expectedDocuments - $tidakAda;

            $dataBuntama[] = [
                'bulan' => $namaBulan,
                'belum' => $totalDokumen > 0 ? round((($expectedDocuments - $totalAda - $tidakAda) / $totalDokumen) * 100, 2) : 0,
                'diperiksa' => $totalDokumen > 0 ? round(($summary->diperiksa / $totalDokumen) * 100, 2) : 0,
                'sudah_kirim' => $totalDokumen > 0 ? round(($summary->sudah_kirim / $totalDokumen) * 100, 2) : 0,
                'ditolak' => $totalDokumen > 0 ? round(($summary->ditolak / $totalDokumen) * 100, 2) : 0,
            ];
        }
        return response()->json($dataBuntama);
    }

    public function jumlahSKPSudahKirimPerBulanPerUnit($tahun)
    {
        if (auth()->user()->is_irwil && auth()->user()->unit_kerja !== '8010') {
            $unitKerjaList = [
                auth()->user()->unit_kerja => $this->master_unit_kerja[auth()->user()->unit_kerja],
            ];
        } else {
            $unitKerjaList = [
                '8100' => 'Inspektorat Wilayah I',
                '8200' => 'Inspektorat Wilayah II',
                '8300' => 'Inspektorat Wilayah III',
                '8010' => 'Bagian Umum Inspektorat Utama',
            ];
        }

        $data = [];

        foreach ($unitKerjaList as $kode => $nama) {
            $userIds = User::where('unit_kerja', $kode)
                ->where('status', 1)
                ->pluck('id');

            $jumlahPegawai = $userIds->count();

            for ($bulan = 1; $bulan <= 14; $bulan++) {
                if ($bulan === 13) {                    
                    $sudahKirim = UploadSkp::whereIn('user_id', $userIds)
                        ->where('tahun', $tahun)
                        ->where('jenis', 'penetapan')
                        ->where('status', 'Sudah Kirim')
                        ->count();

                    $persentase = $jumlahPegawai > 0 ? round(($sudahKirim / $jumlahPegawai) * 100, 2) : 0;
                    $bulanFormatted = '13';
                } elseif ($bulan === 14) {
                    $sudahKirim = UploadSkp::whereIn('user_id', $userIds)
                        ->where('tahun', $tahun)
                        ->where('jenis', 'penilaian')
                        ->where('status', 'Sudah Kirim')
                        ->count();

                    $persentase = $jumlahPegawai > 0 ? round(($sudahKirim / $jumlahPegawai) * 100, 2) : 0;
                    $bulanFormatted = '14';
                } else {
                    $bulanFormatted = str_pad($bulan, 2, '0', STR_PAD_LEFT);

                    $sudahKirim = UploadSkp::whereIn('user_id', $userIds)
                        ->where('tahun', $tahun)
                        ->where('bulan', $bulanFormatted)
                        ->where('jenis', 'bulanan')
                        ->where('status', 'Sudah Kirim')
                        ->count();

                    $tidakAda = UploadSkp::whereIn('user_id', $userIds)
                        ->where('tahun', $tahun)
                        ->where('bulan', $bulanFormatted)
                        ->where('jenis', 'bulanan')
                        ->where('status', 'Tidak Ada')
                        ->count();

                    $pembagi = $jumlahPegawai - $tidakAda;

                    $persentase = $pembagi > 0 ? round(($sudahKirim / $pembagi) * 100, 2) : 0;
                }
                $data[$nama][$bulanFormatted] = $persentase;
            }
        }        

        return $data;
    }
}
