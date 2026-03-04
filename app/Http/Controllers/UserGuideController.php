<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserGuideIndex;
use Illuminate\Http\Request;

class UserGuideController extends Controller
{
    public function index()
    {
        $index = UserGuideIndex::all();

        return view('pegawai.panduan.index', [
            'type_menu' => 'panduan',
            'index' => $index,
        ]);
    }

    public function upload(Request $request)
    {
        $request->validate([
            'pdf' => 'nullable|mimes:pdf|max:20480',
            'indexes' => 'nullable|array',
            'indexes.*.label' => 'required|string',
            'indexes.*.page' => 'required|integer|min:1',
        ]);

        // Replace PDF if uploaded
        if ($request->hasFile('pdf')) {
            $request->file('pdf')->move(public_path('document/panduan'), 'panduan.pdf');
        }

        // Save indices to the database
        UserGuideIndex::truncate(); // Clear existing indices
        
        foreach ($request->indexes as $index) {
            UserGuideIndex::create([
                'section' => $index['label'],
                'page' => $index['page'],
            ]);
        }

        return response()->json(['success' => true]);
    }
}
