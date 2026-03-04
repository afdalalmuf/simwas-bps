<?php

namespace App\Imports;

use Exception;
use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class UserImport implements
    ToModel,
    WithHeadingRow,
    WithValidation,
    SkipsOnFailure
    
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */

    use SkipsFailures;

    private int $successfulRows = 0;
    private int $totalRows = 0;

    public function model(array $row)
    {
        if (empty(array_filter($row))) {
            return null;
        }

        $this->successfulRows++;

        return new User([
            'email'         => $row['email'],
            'nip'           => $row['nip'],
            'name'          => $row['name'],
            'pangkat'       => $row['pangkat'],
            'jabatan'       => $row['jabatan'],
            'satuan_kerja'  => $row['satuan_kerja'],
            'unit_kerja'    => $row['unit_kerja'],
            'is_admin'      => $row['is_admin'],
            'is_sekma'      => $row['is_sekma'],
            'is_sekwil'     => $row['is_sekwil'],
            'is_perencana'  => $row['is_perencana'],
            'is_apkapbn'    => $row['is_apkapbn'],
            'is_opwil'      => $row['is_opwil'],
            'is_analissdm'  => $row['is_analissdm'],
            'is_arsiparis'  => $row['is_arsiparis'],
            'is_aktif'      => $row['is_irtama'],
            'is_irwil'      => $row['is_irwil'],
            'is_pjk'        => $row['is_pjk'],
            'is_auditee'    => $row['is_auditee'],
            'is_bpkp'       => $row['is_bpkp'],
        ]);
    }

    public function getSuccessfulRowCount(): int
    {
        return $this->successfulRows;
    }


    public function rules(): array
    {
        return [
            '*.email' => 'required|email|unique:users,email',
            '*.nip'   => 'required|unique:users,nip',
            '*.name'  => 'required',
            '*.pangkat' => 'required',
            '*.jabatan' => 'required',
            '*.satuan_kerja' => 'required',
            '*.unit_kerja' => 'required',
            // Add any additional validation rules as needed
        ];
    }

    public function customValidationMessages()
    {
        return [
            '*.email.required' => 'Kolom email wajib diisi.',
            '*.email.email' => 'Format email tidak valid.',
            '*.email.unique' => 'Email sudah digunakan.',
            '*.nip.required' => 'NIP harus diisi.',
            '*.nip.unique' => 'NIP sudah digunakan.',
            '*.name.required' => 'Nama tidak boleh kosong.',
            '*.satuan_kerja.required' => 'Satuan Kerja tidak boleh kosong.',
            '*.unit_kerja.required' => 'Unit Kerja tidak boleh kosong.',
            // Tambahkan pesan lainnya sesuai kebutuhan...
        ];
    }
}
