<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\TimKerja;
use App\Models\MasterObjek;
use Illuminate\Http\Request;
use App\Models\MasterUnitKerja;
use App\Models\ObjekPengawasan;
use App\Http\Controllers\Controller;

class AuditeeController extends Controller
{

    protected $unit_kerja = [
        '8000' => 'Inspektorat Utama',
        '8010' => 'Bagian Umum Inspektorat Utama',
        '8100' => 'Inspektorat Wilayah I',
        '8200' => 'Inspektorat Wilayah II',
        '8300' => 'Inspektorat Wilayah III'
    ];

    public function index(Request $request)
    {
        $year = $request->year;
        if ($year == null) {
            $year = date('Y');
        } else {
            $year = $year;
        }
        $auditeeCount = $this->auditeeCount($year);
        $objekCount = $this->objekCount();

        $unitKerja = $this->unit_kerja;

        return view(
            'auditee.index',
            [
                'unitKerja' => $unitKerja,
                'auditeeCount' => $auditeeCount['auditeeCount'],

                'unitKerjaCount' => $objekCount['unitKerjaCount'],
                'satuanKerjaCount' => $objekCount['satuanKerjaCount'],
                'wilayahKerjaCount' => $objekCount['wilayahKerjaCount'],
                'year' => $year,

            ]
        );
    }

    public function schedule()
    {
        $unitKerja = $this->unit_kerja;
        return view(
            'auditee.schedule',
            [
                'type_menu' => 'auditee-schedule',
                'unitKerja' => $unitKerja,
            ]
        );
    }

    public function getSchedule(Request $request)
    {
        $user = auth()->user();

        if ($user->satuan_kerja == '0000') {
            $object_id = MasterObjek::where('kode_unitkerja', $user->unit_kerja)->pluck('id_objek')->toArray();
        } else {
            $object_id = MasterObjek::where('kode_satuankerja', $user->satuan_kerja)->pluck('id_objek')->toArray();
        }

        // return response()->json([$objek_id]);

        $year = $request->year;

        if ($year == null) {
            $year = date('Y');
        } else {
            $year = $year;
        }
        try {
            if (auth()->user()->is_admin) {
                $audits = ObjekPengawasan::whereYear('end_date', $year)->get()->map(function ($task) {
                    $start = $task->start_date instanceof \Carbon\Carbon
                        ? $task->start_date
                        : \Carbon\Carbon::parse($task->start_date);

                    $end = $task->end_date instanceof \Carbon\Carbon
                        ? $task->end_date
                        : \Carbon\Carbon::parse($task->end_date);

                    $duration = $start->diffInDays($end);

                    return [
                        'id' => $task->id_opengawasan, // ✅ Add this
                        'text' => $task->nama_laporan,
                        'start_date' => $start->format('Y-m-d H:i:s'),
                        'end_date' => $end->format('Y-m-d H:i:s'),
                        'duration' => max(1, $duration),
                        'progress' => $task->progress / 100,
                    ];
                })->values()->all();
            } else {
                $audits = ObjekPengawasan::whereYear('end_date', $year)->whereIn('id_objek', $object_id)->get()->map(function ($task) {
                    $start = $task->start_date instanceof \Carbon\Carbon
                        ? $task->start_date
                        : \Carbon\Carbon::parse($task->start_date);

                    $end = $task->end_date instanceof \Carbon\Carbon
                        ? $task->end_date
                        : \Carbon\Carbon::parse($task->end_date);

                    $duration = $start->diffInDays($end);

                    return [
                        'id' => $task->id_opengawasan, // ✅ Add this
                        'text' => $task->nama_laporan,
                        'start_date' => $start->format('Y-m-d H:i:s'),
                        'end_date' => $end->format('Y-m-d H:i:s'),
                        'duration' => max(1, $duration),
                        'progress' => $task->progress / 100,
                    ];
                })->values()->all();
            }
            return response()->json(['data' => $audits]);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function getScheduleByYear(Request $request)
    {
        $user = auth()->user();

        if ($user->satuan_kerja == '0000') {
            $object_id = MasterObjek::where('kode_unitkerja', $user->unit_kerja)->pluck('id_objek')->toArray();
        } else {
            $object_id = MasterObjek::where('kode_satuankerja', $user->satuan_kerja)->pluck('id_objek')->toArray();
        }

        // return response()->json([$objek_id]);

        $year = $request->year;

        if ($year == null) {
            $year = date('Y');
        } else {
            $year = $year;
        }
        try {
            if (auth()->user()->is_admin) {
                $audits = ObjekPengawasan::whereYear('end_date', $year)->get()->map(function ($task) {
                    $start = $task->start_date instanceof \Carbon\Carbon
                        ? $task->start_date
                        : \Carbon\Carbon::parse($task->start_date);

                    $end = $task->end_date instanceof \Carbon\Carbon
                        ? $task->end_date
                        : \Carbon\Carbon::parse($task->end_date);

                    $duration = $start->diffInDays($end);

                    return [
                        'id' => $task->id_opengawasan, // ✅ Add this
                        'text' => $task->nama_laporan,
                        'start_date' => $start->format('Y-m-d H:i:s'),
                        'end_date' => $end->format('Y-m-d H:i:s'),
                        'duration' => max(1, $duration),
                        'progress' => $task->progress / 100,
                    ];
                })->values()->all();
            } else {
                $audits = ObjekPengawasan::whereYear('end_date', $year)->whereIn('id_objek', $object_id)->get()->map(function ($task) {
                    $start = $task->start_date instanceof \Carbon\Carbon
                        ? $task->start_date
                        : \Carbon\Carbon::parse($task->start_date);

                    $end = $task->end_date instanceof \Carbon\Carbon
                        ? $task->end_date
                        : \Carbon\Carbon::parse($task->end_date);

                    $duration = $start->diffInDays($end);

                    return [
                        'id' => $task->id_opengawasan, // ✅ Add this
                        'text' => $task->nama_laporan,
                        'start_date' => $start->format('Y-m-d H:i:s'),
                        'end_date' => $end->format('Y-m-d H:i:s'),
                        'duration' => max(1, $duration),
                        'progress' => $task->progress / 100,
                    ];
                })->values()->all();
            }
            return response()->json(['data' => $audits]);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function auditDetail($id)
    {
        $audit = ObjekPengawasan::findOrFail($id);
        return view('auditee.schedule-detail', [
            'audit' =>  $audit,
            'type_menu' => 'auditee-schedule',
        ]);
    }

    private function auditeeCount($year)
    {
        $user = auth()->user();

        if ($user->satuan_kerja == '0000') {
            $object_id = MasterObjek::where('kode_unitkerja', $user->unit_kerja)->pluck('id_objek')->toArray();
        } else {
            $object_id = MasterObjek::where('kode_satuankerja', $user->satuan_kerja)->pluck('id_objek')->toArray();
        }
        try {
            if (auth()->user()->is_admin) {
                $auditeeCount = ObjekPengawasan::whereYear('end_date', $year)->get()->count();
            } else {
                $auditeeCount = ObjekPengawasan::whereYear('end_date', $year)->whereIn('id_objek', $object_id)->get()->count();
            }
        } catch (\Throwable $th) {
            throw $th;
        }

        return [
            'auditeeCount' => $auditeeCount,
        ];
    }

    private function objekCount()
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
}
