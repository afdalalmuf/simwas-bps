<?php

namespace App\Http\Controllers;

use App\Models\KipappSyncs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class KipappSyncsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
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
        $pegawai = session('pegawai');
        if (!$pegawai || !isset($pegawai['nip-lama'])) {
            return response()->json([
                'success' => false,
                'message' => 'nip-lama not found in session.',
            ], 400); // Respond with an error if session data is missing
        }
        $validated = $request->validate([
            'koderk' => 'required|string',
            'id_pelaksana' => 'required|string',
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2020',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'kegiatan' => 'required|string',
            'capaian' => 'required|string',
            'link_form' => 'nullable|url',
        ]);

        $sync = KipappSyncs::updateOrCreate(
            [
                'id_pelaksana' => $validated['id_pelaksana'],
                'month'        => $validated['month'],
                'year'         => $validated['year'],
            ],
            [
                'koderk' => $validated['koderk'],
                'id_pelaksana' => $validated['id_pelaksana'],
                'month' => $validated['month'],
                'year' => $validated['year'],
                'niplama' => $pegawai['nip-lama'],
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'kegiatan' => $validated['kegiatan'],
                'capaian' => $validated['capaian'],
                'link' => $validated['link_form'],
                'capaianSKP' => true,
                'synced' => false,
            ]
        );

        $message = $sync->wasRecentlyCreated
            ? 'Data baru berhasil disimpan.'
            : 'Data berhasil diperbarui.';

        return response()->json([
            'success' => true,
            'message' => $message,
            'data'    => $sync,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\KipappSyncs  $kipappSyncs
     * @return \Illuminate\Http\Response
     */
    public function show(KipappSyncs $kipappSyncs)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\KipappSyncs  $kipappSyncs
     * @return \Illuminate\Http\Response
     */
    public function edit(KipappSyncs $kipappSyncs)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\KipappSyncs  $kipappSyncs
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, KipappSyncs $kipappSyncs)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\KipappSyncs  $kipappSyncs
     * @return \Illuminate\Http\Response
     */
    public function destroy(KipappSyncs $kipappSyncs)
    {
        //
    }

    public function check(Request $request)
    {
        try {
            $data = $request->validate([
                'id_pelaksana' => 'required|string',
                'month'        => 'required|integer|min:1|max:12',
                'year'         => 'required|integer',
            ]);

            $record = KipappSyncs::where('id_pelaksana', $data['id_pelaksana'])
                ->where('month', $data['month'])
                ->where('year',  $data['year'])
                ->first();

            return response()->json(['data' => $record]);
        } catch (\Throwable $e) {
            \Log::error("KipappSyncsController@check failed: " . $e->getMessage());
            return response()->json([
                'message' => 'Server error checking sync'
            ], 500);
        }
    }

    public function sync(Request $request)
    {
        $pegawai = session('pegawai');

        if (!$pegawai || !isset($pegawai['nip-lama'])) {
            return response()->json([
                'success' => false,
                'message' => 'nip-lama not found in session.',
            ], 400);
        }

        $data = $request->validate([
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer',
        ]);

        // Get unsynced records for given user/month/year
        $unsyncedSyncs = KipappSyncs::where([
            'niplama' => $pegawai['nip-lama'],
            'month' => $data['month'],
            'year' => $data['year'],
            'synced' => false,
        ])->get();

        $count = $unsyncedSyncs->count();

        if ($count === 0) {
            return response()->json([
                'success' => true,
                'message' => 'Tidak ada data yang perlu disinkronkan.',
                'count' => 0,
                'data' => [],
            ]);
        }

        \DB::transaction(function () use ($unsyncedSyncs) {
            foreach ($unsyncedSyncs as $sync) {
                $sync->synced = true;
                $sync->save();
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil disinkronkan.',
            'count' => $count,
            'data' => $unsyncedSyncs,
        ]);
    }

    public function getRencanaKinerjaKipapp($year, $month)
    {
        $pegawai = session('pegawai');
        try {
            $url = 'https://webapps.bps.go.id/kipapp/api/simwas/rk';
            $access_token = config('services.kipapp.rk_access_token');
            $response = Http::withToken($access_token)->get($url, [
                'niplama' => $pegawai['nip-lama'],
                'tahun'    => $year,
                'bulan'    => $month,
            ]);

            return response()->json($response->json(), 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mendapatkan data Rencana Kinerja dari KipApp: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function getActivitiesAPI(Request $request)
    {
        // Get the app-level token from the request header or query parameter
        $apiToken = $request->bearerToken() ?: $request->input('api_token');

        // Validate the token
        if ($apiToken !== config('services.activites_api_token')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized, invalid API token.',
            ], 403); // Forbidden
        }

        // Proceed to validate the other required fields
        $data = $request->validate([
            'niplama' => 'required|string',
            'bulan' => 'required|integer|min:1|max:12',
            'tahun' => 'required|integer',
        ]);

        // Fetch the data from the database
        try {
            $record = KipappSyncs::select('koderk', 'start_date', 'end_date', 'kegiatan', 'capaian', 'link', 'capaianSKP')
                ->where('niplama', $data['niplama'])
                ->where('month', $data['bulan'])
                ->where('year', $data['tahun'])
                ->where('synced', true)
                ->get();

            // Check if there are no records
            if ($record->isEmpty()) {
                return response()->json([
                    'message' => 'No activities found for the given criteria.',
                ], 404);
            }

            return response()->json($record, 200);
        } catch (\Throwable $e) {
            \Log::error("KipappSyncsController@getActivities failed: " . $e->getMessage());
            return response()->json([
                'message' => 'Server error fetching activities',
                'error' => $e->getMessage(),  // Include the error message for debugging
            ], 500);
        }
    }
}
