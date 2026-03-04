<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\RencanaDiklat;
use App\Models\MasterPenyelenggara;
use App\Imports\RencanaDiklatImport;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\HeadingRowImport;
use App\Http\Requests\StoreRencanaDiklatRequest;
use App\Http\Requests\UpdateRencanaDiklatRequest;

Carbon::setLocale('id');

class RencanaDiklatController extends Controller
{
    protected $unit_kerja = [
        '8000' => 'Inspektorat Utama',
        '8010' => 'Bagian Umum Inspektorat Utama',
        '8100' => 'Inspektorat Wilayah I',
        '8200' => 'Inspektorat Wilayah II',
        '8300' => 'Inspektorat Wilayah III',
    ];
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Authorize the user for the 'analis_sdm' permission
        $this->authorize('analis_sdm');
        // Get the year from the request, default to current year if not provided
        $year = $request->input('tahun', now()->year);
        $rencanaDiklat = RencanaDiklat::whereYear('start_date', $year)
            ->get();

        foreach ($rencanaDiklat as $diklat) {
            $start = Carbon::parse($diklat->start_date);
            $end = Carbon::parse($diklat->end_date);

            if ($start->format('F Y') === $end->format('F Y')) {
                // Same month and year
                $diklat->tanggal_format = $start->format('d') . '–' . $end->translatedFormat('d F Y');
            } else {
                // Different month or year
                $diklat->tanggal_format = $start->translatedFormat('d F Y') . ' – ' . $end->translatedFormat('d F Y');
            }
        }

        // Pass the rencanaDiklat data to the view
        return view('analis-sdm.rencana-diklat.index', [
            'type_menu' => 'rencana-diklat',
            'unit_kerja'    => $this->unit_kerja,
            'rencanaDiklat' => $rencanaDiklat,
            'penyelenggara' => MasterPenyelenggara::all(),
            'pegawai'  => User::where('status', 1)->get(),
            'currentYear' => $year,
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
     * @param  \App\Http\Requests\StoreRencanaDiklatRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRencanaDiklatRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\RencanaDiklat  $rencanaDiklat
     * @return \Illuminate\Http\Response
     */
    public function show(RencanaDiklat $rencanaDiklat)
    {
        // Authorize the user for the 'analis_sdm' permission
        try {
            $rencanaDiklat->load(['pegawai', 'penyelenggara_diklat']);
            return response()->json($rencanaDiklat, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Invalid Rencana Diklat ID'], 400);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\RencanaDiklat  $rencanaDiklat
     * @return \Illuminate\Http\Response
     */
    public function edit(RencanaDiklat $rencanaDiklat)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateRencanaDiklatRequest  $request
     * @param  \App\Models\RencanaDiklat  $rencanaDiklat
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRencanaDiklatRequest $request, RencanaDiklat $rencanaDiklat)
    {
        // Authorize the user for the 'analis_sdm' permission
        // $this->authorize('analis_sdm');
        $validated = $request->validated();

        // Convert _NULL_ to actual null
        $validated['pembebanan_perjadin'] = $validated['pembebanan_perjadin'] === '_NULL_' ? null : $validated['pembebanan_perjadin'];
        $validated['akun_anggaran'] = $validated['akun_anggaran'] === '_NULL_' ? null : $validated['akun_anggaran'];

        $rencanaDiklat->update($validated);

        return response()->json(['message' => 'Rencana Diklat berhasil diperbarui.']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\RencanaDiklat  $rencanaDiklat
     * @return \Illuminate\Http\Response
     */
    public function destroy(RencanaDiklat $rencanaDiklat)
    {
        //
    }

    /**
     * Import Rencana Diklat from an Excel file.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function import(Request $request)
    {
        $validateFile = $request->validate([
            'file' => 'required|mimes:xls,xlsx'
        ]);

        $requiredHeaders = [
            'nip',
            'nama_diklat',
            'tanggal_mulai',
            'tanggal_selesai',
            'metode',
            'penyelenggara',
            'biaya_diklat',
            'transport',
            'akomodasi',
            'uang_saku',
            'pembebanan_perjadin',
            'akun_anggaran',
            'status',
            'keterangan',
        ];

        $header = (new HeadingRowImport)->toArray($request->file('file'));

        $actualHeaders = array_map('strtolower', $header[0][0]); // normalize to lowercase

        foreach ($requiredHeaders as $required) {
            if (!in_array($required, $actualHeaders)) {
                return back()
                    ->with('status', 'Format file tidak sesuai. Silakan unduh template resmi.')
                    ->with('alert-type', 'danger');
            }
        }

        $import = new RencanaDiklatImport;
        Excel::import($import, $request->file('file'));

        $failures = collect($import->failures());

        $success = $import->getSuccessfulRowCount();
        $failed = $failures->map(fn($failure) => $failure->row())->unique()->count();

        $customMessages = $failures
            ->groupBy(fn($f) => $f->row())
            ->map(function ($failures, $row) {
                $values = $failures->first()->values(); // row data
                $name = $values['name'] ?? 'Baris ' . $row;

                $messages = [];

                foreach ($failures as $failure) {
                    foreach ($failure->errors() as $error) {
                        // Customize message pattern
                        $messages[] = "Data atas nama {$name}, {$error}";
                    }
                }

                return $messages;
            })->flatten(); // merge into a single list

        if ($success > 0) {
            if ($failed > 0) {
                return back()->with([
                    'status' => "$success baris berhasil diimpor, $failed baris gagal.",
                    'failures' => $customMessages,
                    'alert-type' => 'warning',
                ]);
            }
            return back()->with([
                'status' => "Semua data berhasil diimpor ($success baris berhasil).",
                'alert-type' => 'success',
            ]);
        } else {
            // all failed
            return back()->with([
                'status' => "Semua data gagal diimpor ($failed baris gagal).",
                'failures' => $customMessages,
                'alert-type' => 'danger',
            ]);
        }
    }

    public function storeViaAjax(StoreRencanaDiklatRequest $request)
    {
        try {
            $rules = [
                'name' => 'required|string|max:255',
                'id_pegawai' => 'required|exists:users,id',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
                'metode' => 'required|in:Offline,PJJ,Hybrid',
                'penyelenggara' => 'required|exists:master_penyelenggaras,id',
                'biaya' => 'nullable|numeric|min:0',
                'transport' => 'nullable|numeric|min:0',
                'akomodasi' => 'nullable|numeric|min:0',
                'uang_saku' => 'nullable|numeric|min:0',
                'pembebanan_perjadin' => 'nullable',
                'akun_anggaran' => 'nullable',
                'status' => 'required',
                'keterangan' => 'nullable|string',
            ];

            $data = $request->validate($rules);
            $data['pembebanan_perjadin'] = $request['pembebanan_perjadin'] === '_NULL_' ? null : $request['pembebanan_perjadin'];
            $data['akun_anggaran'] = $request['akun_anggaran'] === '_NULL_' ? null : $request['akun_anggaran'];

            RencanaDiklat::create($data);
            return response()->json(['message' => 'Rencana Diklat berhasil ditambahkan.'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'trace' => $e->getTrace()], 500);
        }
    }

    public function updateViaAjax(UpdateRencanaDiklatRequest $request, $id)
    {
        try {
            $validated = $request->only([
                'start_date',
                'end_date',
                'status',
                'keterangan'
            ]);

            $rencanaDiklat = RencanaDiklat::findOrFail($id);
            $rencanaDiklat->update($validated);

            return response()->json(['message' => 'Rencana Diklat berhasil diperbarui.']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'trace' => $e->getTrace()], 500);
        }
    }
}
