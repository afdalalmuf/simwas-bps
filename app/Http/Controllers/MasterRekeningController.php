<?php

namespace App\Http\Controllers;

use App\Models\MasterRekening;
use Illuminate\Http\Request;

class MasterRekeningController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'nama_bank' => 'required|string|max:100',
            'no_rekening' => 'required|string|max:50|unique:master_rekenings,no_rekening',
        ]);

        $rekening = MasterRekening::create([
            'nama_bank' => $request->nama_bank,
            'no_rekening' => $request->no_rekening,
            'id_pegawai' => auth()->user()->id,
            'status' => 1,
        ]);

        return response()->json($rekening);
    }

    public function list()
    {
        return response()->json(MasterRekening::where('id_pegawai', auth()->user()->id)->get());
    }

}
