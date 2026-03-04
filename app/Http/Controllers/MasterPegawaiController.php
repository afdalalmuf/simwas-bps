<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use App\Imports\UserImport;
use App\Models\MasterObjek;
use App\Models\MasterRole;
use Illuminate\Http\Request;
use App\Models\ObjekPengawasan;
use App\Models\RoleAdhoc;
use App\Models\RolePegawai;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\HeadingRowImport;
use Illuminate\Support\Facades\Validator;

class MasterPegawaiController extends Controller
{
    protected $pangkat = [
        'II/a' => 'Pengatur Muda',
        'II/b' => 'Pengatur Muda Tingkat I',
        'II/c' => 'Pengatur',
        'II/d' => 'Pengatur Tingkat I',
        'III/a' => 'Penata Muda',
        'III/b' => 'Penata Muda Tingkat I',
        'III/c' => 'Penata',
        'III/d' => 'Penata Tingkat I',
        'IV/a' => 'Pembina',
        'IV/b' => 'Pembina Tingkat I',
        'IV/c' => 'Pembina Muda',
        'IV/d' => 'Pembina Madya',
        'IV/e' => 'Pembina Utama',
        'IX' => 'IX'
    ];

    protected $satuan_kerja;

    public function __construct()
    {
        $this->satuan_kerja = MasterObjek::where('kode_satuankerja', '!=', '')->get()->toArray();
    }

    protected $unit_kerja = [
        '8000' => 'Inspektorat Utama',
        '8010' => 'Bagian Umum Inspektorat Utama',
        '8100' => 'Inspektorat Wilayah I',
        '8200' => 'Inspektorat Wilayah II',
        '8300' => 'Inspektorat Wilayah III',
        '9200' => 'BPS Provinsi',
        '9210' => 'Bagian Umum BPS Provinsi',
        '9280' => 'BPS Kabupaten/Kota',
        '9281' => 'Subbagian Umum BPS Kabupaten/Kota',
        '9999' => 'BPKP'
    ];

    protected $jabatan = [
        '0' => '-',
        '10' => 'Inspektur Utama',
        '11' => 'Inspektur Wilayah I',
        '12' => 'Inspektur wilayah II',
        '13' => 'Inspektur wilayah III',
        '14' => 'Kepala Bagian Umum',
        '21' => 'Auditor Utama',
        '22' => 'Auditor Madya',
        '23' => 'Auditor Muda',
        '24' => 'Auditor Pertama',
        '25' => 'Auditor Penyelia',
        '26' => 'Auditor Pelaksana Lanjutan',
        '27' => 'Auditor Pelaksana',
        '31' => 'Perencana Madya',
        '32' => 'Perencana Muda',
        '33' => 'Perencana Pertama',
        '41' => 'Analis Kepegawaian Madya',
        '42' => 'Analis Kepegawaian Muda',
        '43' => 'Analis Kepegawaian Pertama',
        '51' => 'Analis Pengelolaan Keuangan APBN Madya',
        '52' => 'Analis Pengelolaan Keuangan APBN Muda',
        '53' => 'Analis Pengelolaan Keuangan APBN Pertama',
        '61' => 'Pranata Komputer Madya',
        '62' => 'Pranata Komputer Muda',
        '63' => 'Pranata Komputer Pratama',
        '71' => 'Arsiparis Madya',
        '72' => 'Arsiparis Muda',
        '73' => 'Arsiparis Pertama',
        '81' => 'Analis Hukum Madya',
        '82' => 'Analis Hukum Muda',
        '83' => 'Analis Hukum Pertama',
        '91' => 'Penatalaksana Barang',
        '90' => 'Fungsional Umum'
    ];

    protected $role = [
        'is_admin' => 'Admin',
        'is_sekma' => 'Sekretaris Utama',
        'is_sekwil' => 'Sekretaris Wilayah',
        'is_perencana' => 'Perencana',
        'is_apkapbn' => 'APK-APBN',
        'is_opwil' => 'Operator Wilayah',
        'is_analissdm' => 'Analis SDM',
        'is_arsiparis' => 'Arsiparis',
        'is_aktif' => 'Inspektur Utama',
        'is_irwil' => 'Inspektur Wilayah',
        'is_pjk' => 'PJ Kegiatan',
        'is_auditee' => 'Auditi',
        'is_bpkp' => 'BPKP',
    ];

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->authorize('admin');
        $users = User::all();
        return view('admin.master-pegawai.index', [
            'type_menu' => 'master-pegawai',
            'pangkat' => $this->pangkat,
            'satuan_kerja' => $this->satuan_kerja,
            'unit_kerja' => $this->unit_kerja,
            'jabatan' => $this->jabatan,
            'role' => $this->role
        ])->with('users', $users);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function getAllPegawai()
    {
        $url_base = 'https://sso.bps.go.id/auth/';
        $url_token = $url_base . 'realms/pegawai-bps/protocol/openid-connect/token';
        $url_api = $url_base . 'realms/pegawai-bps/api-pegawai';
        $client_id = config('services.sso.client_id');
        $client_secret = config('services.sso.client_secret');
        $ch = curl_init($url_token);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
        curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials");
        curl_setopt($ch, CURLOPT_USERPWD, $client_id . ":" . $client_secret);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response_token = curl_exec($ch);
        if (curl_errno($ch)) {
            throw new Exception(curl_error($ch));
        }
        curl_close($ch);
        $json_token = json_decode($response_token, true);
        $access_token = $json_token['access_token'];

        $kodeOrganisasi = ['000000080100', '000000081000', '000000082000', '000000083000', '000000080000'];

        $allPegawai = [];

        foreach ($kodeOrganisasi as $kode) {
            $query_search = '/unit/' . $kode;

            $ch = curl_init($url_api . $query_search);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Authorization: Bearer ' . $access_token));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($ch);
            if (curl_errno($ch)) {
                throw new Exception(curl_error($ch));
            }
            curl_close($ch);
            $json = json_decode($response, true);
            $allPegawai = array_merge($allPegawai, $json);
        }
        $pegawaiDatabase = User::all();
        // filter allpegawai when ["attributes"]["attribute-nip"][0] not in nip pegawaidatabase
        $allPegawai = array_filter($allPegawai, function ($pegawai) use ($pegawaiDatabase) {
            $nip = $pegawai["attributes"]["attribute-nip"][0];
            $isExist = $pegawaiDatabase->contains('nip', $nip);
            return !$isExist;
        });
        return $allPegawai;
    }

    public function create()
    {
        $allPegawai = $this->getAllPegawai();
        return view(
            'admin.master-pegawai.create',
            [
                'type_menu' => 'master-pegawai',
                'pangkat' => $this->pangkat,
                'satuan_kerja' => $this->satuan_kerja,
                'unit_kerja' => $this->unit_kerja,
                'jabatan' => $this->jabatan,
                'role' => $this->role,
                'allPegawai' => $allPegawai,
            ]
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // protected $role = [
        //     'is_admin'      => 'Admin',
        //     'is_sekma'      => 'Sekretaris Utama',
        //     'is_sekwil'     => 'Sekretaris Wilayah',
        //     'is_perencana'  => 'Perencana',
        //     'is_apkapbn'    => 'APK-APBN',
        //     'is_opwil'      => 'Operator Wilayah',
        //     'is_analissdm'  => 'Analis SDM'
        // ];
        // dd($request);
        try {
            $validateData = $request->validate([
                'name' => 'required',
                'email' => 'required|unique:users|max:255',
                'nip' => 'required|max:18',
                'pangkat' => 'required',
                'satuan_kerja' => 'required',
                'unit_kerja' => 'required',
                'jabatan' => 'required',
                'is_admin' => 'required',
                'is_sekma' => 'required',
                'is_sekwil' => 'required',
                'is_perencana' => 'required',
                'is_apkapbn' => 'required',
                'is_opwil' => 'required',
                'is_analissdm' => 'required',
                'is_arsiparis' => 'required',
                'is_aktif' => 'required',
                'is_irwil' => 'required',
                'is_pjk' => 'required',
                'is_auditee' => 'required',
                'is_bpkp' => 'required',
            ]);

            $validateData["password"] = bcrypt($request->password);

            User::create($validateData);

            return redirect('/admin/master-pegawai')
                ->with('status', 'Berhasil menambahkan data pegawai.')
                ->with('alert-type', 'success');
        } catch (\Throwable $th) {
            throw $th;
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
        $user = User::findOrfail($id);

        return view('admin.master-pegawai.show', [
            'type_menu' => 'master-pegawai',
            'pangkat' => $this->pangkat,
            'satuan_kerja' => $this->satuan_kerja,
            'unit_kerja' => $this->unit_kerja,
            'jabatan' => $this->jabatan,
            'role' => $this->role
        ])
            ->with('user', $user);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = User::findOrfail($id);
        $master_role = MasterRole::all();        
        $pegawai_roles = RolePegawai::where('id_pegawai', $id)->pluck('id_role')->toArray();
        return view('admin.master-pegawai.edit', [
            'type_menu' => 'master-pegawai',
            'pangkat' => $this->pangkat,
            'satuan_kerja' => $this->satuan_kerja,
            'unit_kerja' => $this->unit_kerja,
            'jabatan' => $this->jabatan,
            'role' => $this->role,
            'master_role' => $master_role,
            'pegawai_roles' => $pegawai_roles,
        ])
            ->with('user', $user);
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
        $user = User::findOrfail($id);

        $rules = [
            'name' => 'required',
            // 'email'         => 'required|unique:users|max:255',
            // 'password'      => 'required',
            'nip' => 'required',
            'pangkat' => 'required',
            'satuan_kerja' => 'required',
            'unit_kerja' => 'required',
            'jabatan' => 'required',
            'is_admin' => 'required',
            'is_sekma' => 'required',
            'is_sekwil' => 'required',
            'is_perencana' => 'required',
            'is_apkapbn' => 'required',
            'is_opwil' => 'required',
            'is_analissdm' => 'required',
            'is_arsiparis' => 'required',
            'is_aktif' => 'required',
            'is_irwil' => 'required',
            'is_pjk' => 'required',
            'is_auditee' => 'required',
            'is_bpkp' => 'required',
        ];

        // if($request->password != ""){
        //     $rules['password'] = 'required';
        //     $request['password'] = $user->password;
        // }else{
        //     $request['password'] = bcrypt($request->password);
        // }

        if ($request->email != $user->email) {
            $rules['email'] = 'required|unique:users|max:255';
        }

        $validateData = $request->validate($rules);

        User::where('id', $id)->update($validateData);

        $master_role = MasterRole::all();        
        foreach ($master_role as $key => $value) {
            $cek_role = RolePegawai::where('id_pegawai', $id)->where('id_role', $value->id)->first();
            $id_role = $value->id;
            if ($request->$id_role == "1") {                
                if ($cek_role === null) {                    
                    RolePegawai::create(
                        [                            
                            'id_pegawai' => $id,
                            'id_role' => $value->id,
                        ]
                    );
                }
            } else {                
                if ($cek_role !== null) {                    
                    RolePegawai::where('id_role_pegawai', $cek_role->id_role_pegawai)->delete();
                }
            }
        }

        return redirect('/admin/master-pegawai')
            ->with('status', 'Berhasil memperbarui data pegawai.')
            ->with('alert-type', 'success');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        try {
            $user = User::where('id', $id)->first();
            $user->update(['status' => 0]);
            return response()->json([
                'success' => true,
                'message' => 'Pegawai berhasil dinonaktifkan!',
            ]);
        } catch (\Throwable $th) {
            if ($th->errorInfo[1] == 1451) {
                return response()->json([
                    'success' => false,
                    'message' => "Data masih terhubung dengan data lain!"
                ], 409);
            }
            return response()->json([
                'success' => false,
                'message' => 'Data Gagal Dihapus!',
            ], 409);
        }
    }

    public function activate(Request $request, $id)
    {
        try {
            User::findOrfail($id)->update(['status' => 1]);
            return response()->json([
                'success' => true,
                'message' => 'Pegawai berhasil diaktifkan!',
            ]);
        } catch (\Throwable $th) {
            if ($th->errorInfo[1] == 1451) {
                return response()->json([
                    'success' => false,
                    'message' => "Data masih terhubung dengan data lain!"
                ], 409);
            }
            return response()->json([
                'success' => false,
                'message' => 'Data Gagal Dihapus!',
            ], 409);
        }
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function import(Request $request)
    {
        $validateFile = $request->validate([
            'file' => 'required|mimes::xls,xlsx'
        ]);


        $file = $request->file('file');
        $file_name = rand() . $file->getClientOriginalName();
        $file->move(storage_path('/document/upload/'), $file_name);

        $requiredHeaders = [
            'email',
            'nip',
            'name',
            'pangkat',
            'jabatan',
            'unit_kerja',
            'is_admin',
            'is_sekma',
            'is_sekwil',
            'is_perencana',
            'is_apkapbn',
            'is_opwil',
            'is_analissdm',
            'is_arsiparis',
            'is_irtama',
            'is_irwil',
            'is_pjk',
            'is_auditee',
            'is_bpkp'
        ];

        $header = (new HeadingRowImport)->toArray(storage_path('/document/upload/') . $file_name);

        $actualHeaders = array_map('strtolower', $header[0][0]); // normalize to lowercase

        foreach ($requiredHeaders as $required) {
            if (!in_array($required, $actualHeaders)) {
                return back()
                    ->with('status', 'Format file tidak sesuai. Silakan unduh template resmi.')
                    ->with('alert-type', 'danger');
            }
        }

        $import = new UserImport;

        Excel::import($import, storage_path('/document/upload/') . $file_name);

        $failures = collect($import->failures());

        $success = $import->getSuccessfulRowCount();
        $failed = $failures->map(fn($failure) => $failure->row())->unique()->count();

        $filePath = storage_path('/document/upload/') . $file_name;

        if (file_exists($filePath)) {
            unlink($filePath); // deletes the file
        }

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
                'status' => "Semua data berhasil diimpor ($success baris gagal).",
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

    public function getPegawai($nip)
    {
        $url_base = 'https://sso.bps.go.id/auth/';
        $url_token = $url_base . 'realms/pegawai-bps/protocol/openid-connect/token';
        $url_api = $url_base . 'realms/pegawai-bps/api-pegawai';
        $client_id = config('services.sso.client_id');
        $client_secret = config('services.sso.client_secret');
        $ch = curl_init($url_token);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
        curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials");
        curl_setopt($ch, CURLOPT_USERPWD, $client_id . ":" . $client_secret);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response_token = curl_exec($ch);
        if (curl_errno($ch)) {
            throw new Exception(curl_error($ch));
        }
        curl_close($ch);
        $json_token = json_decode($response_token, true);
        $access_token = $json_token['access_token'];
        // dd($access_token);

        $query_search = '/nipbaru/' . $nip;

        $ch = curl_init($url_api . $query_search);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Authorization: Bearer ' . $access_token));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            throw new Exception(curl_error($ch));
        }
        curl_close($ch);
        $json = json_decode($response, true);
        // dd($json);
        return response()->json($json);
    }

    public function getPegawaibyNIP($nip)
    {
        $url_base = 'https://sso.bps.go.id/auth/';
        $url_token = $url_base . 'realms/pegawai-bps/protocol/openid-connect/token';
        $url_api = $url_base . 'realms/pegawai-bps/api-pegawai';
        $client_id = config('services.sso.client_id');
        $client_secret = config('services.sso.client_secret');
        $ch = curl_init($url_token);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
        curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials");
        curl_setopt($ch, CURLOPT_USERPWD, $client_id . ":" . $client_secret);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response_token = curl_exec($ch);
        if (curl_errno($ch)) {
            throw new Exception(curl_error($ch));
        }
        curl_close($ch);
        $json_token = json_decode($response_token, true);
        $access_token = $json_token['access_token'];
        // dd($access_token);

        $query_search = '/nip/' . $nip;

        $ch = curl_init($url_api . $query_search);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Authorization: Bearer ' . $access_token));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            throw new Exception(curl_error($ch));
        }
        curl_close($ch);
        $json = json_decode($response, true);
        // dd($json);
        return response()->json($json);
    }
}
