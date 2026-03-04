<?php

namespace App\Exports;

use App\Models\Kompetensi;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class KompetensiExport implements FromView
{
    protected $unit_kerja;
    protected $kategori;

    public function __construct($unit_kerja, $kategori)
    {
        $this->unit_kerja = $unit_kerja;
        $this->kategori = $kategori;
    }

    public function view(): View
    {
        $query = Kompetensi::with(['pegawai', 'teknis.jenis.kategori']);

        if ($this->unit_kerja !== 'all') {
            $query->whereHas('pegawai', fn($q) => $q->where('unit_kerja', $this->unit_kerja));
        }

        if ($this->kategori !== 'all') {
            $query->whereHas('teknis.jenis.kategori', fn($q) => $q->where('id', $this->kategori));
        }

        $data = $query->get();

        return view('analis-sdm.kelola-kompetensi.export', ['data' => $data]);
    }
}
