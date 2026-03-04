<?php

namespace App\Imports;

use App\Models\User;
use App\Models\RencanaDiklat;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithValidation;

class RencanaDiklatImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure, SkipsEmptyRows, WithMultipleSheets
{
    use SkipsFailures;


    private int $successfulRows = 0;
    private int $totalRows = 0;

    public function sheets(): array
    {
        return [
            'impor' => $this // or 0 => $this if using index
        ];
    }

    public function model(array $row)
    {
        if (empty(array_filter($row))) {
            return null;
        }

        $this->successfulRows++;
        // Find the user by NIP
        $user = User::where('nip', $row['nip'])->first();

        // If user doesn't exist, skip this row (or you can handle it another way)
        if (!$user) {
            return null;
        }
        return new RencanaDiklat([
            'id_pegawai'            => $user->id,
            'name'                  => $row['nama_diklat'],
            'start_date' => $this->parseIndonesianDate($row['tanggal_mulai']),
            'end_date'   => $this->parseIndonesianDate($row['tanggal_selesai']),
            'metode'                => $row['metode'],
            'penyelenggara'         => $row['penyelenggara'], // still assuming foreign key or raw string
            'biaya'                 => $row['biaya_diklat'],
            'transport'             => $row['transport'],
            'akomodasi'             => $row['akomodasi'],
            'uang_saku'             => $row['uang_saku'],
            'status'                => $row['status'],
            'keterangan'            => $row['keterangan'],
            'akun_anggaran'         => $row['akun_anggaran'],
            'pembebanan_perjadin'   => $row['pembebanan_perjadin'], // assuming your DB has this column
        ]);
    }

    public function getSuccessfulRowCount(): int
    {
        return $this->successfulRows;
    }

    public function rules(): array
    {
        return [
            '*.nip'             => 'required|exists:users,nip',
            '*.nama_diklat'     => 'required|string',
            '*.tanggal_mulai'   => 'required',
            '*.tanggal_selesai' => 'required',
            '*.metode'          => 'required|string',
            '*.penyelenggara'   => 'required',
            '*.biaya_diklat'    => 'nullable|numeric',
            '*.transport'       => 'nullable|numeric',
            '*.akomodasi'       => 'nullable|numeric',
            '*.uang_saku'       => 'nullable|numeric',
            '*.status'          => 'required',
        ];
    }

    function parseIndonesianDate($value)
    {
        $months = [
            'Januari' => 'January',
            'Februari' => 'February',
            'Maret' => 'March',
            'April' => 'April',
            'Mei' => 'May',
            'Juni' => 'June',
            'Juli' => 'July',
            'Agustus' => 'August',
            'September' => 'September',
            'Oktober' => 'October',
            'November' => 'November',
            'Desember' => 'December',
        ];

        $date = str_ireplace(array_keys($months), array_values($months), $value);
        return \Carbon\Carbon::parse($date)->format('Y-m-d');
    }
}
