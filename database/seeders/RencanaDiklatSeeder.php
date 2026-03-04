<?php

namespace Database\Seeders;

use App\Models\RencanaDiklat;
use Illuminate\Database\Seeder;
use Symfony\Component\Uid\Ulid;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RencanaDiklatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $rencanaDiklats = [
            [
                'id' => Ulid::generate(),
                'name' => 'PJJ Audit Kinerja Berbasis Risiko bagi APIP K/L',
                'id_pegawai' => '01j0x0h602dj359cj66yrrep0h',
                'start_date' => '2025-05-14',
                'end_date' => '2025-05-19',
                'metode' => 'PJJ',
                'penyelenggara' => '01j3y4w351qv6754xzf9kgzpha',
                'biaya' =>  2340000 ,
                'transport' => 0,
                'akomodasi' => 0,
                'uang_saku' => 0,
                'pembebanan_perjadin' => NULL,
                'akun_anggaran' => NULL,
                'status' => 'approved',
                'keterangan' => NULL,
            ],
            [
                'id' => Ulid::generate(),
                'name' => 'Reviu Laporan Kinerja',
                'id_pegawai' => '01j121d94c4kjm92tv2jnpj1yk',
                'start_date' => '2025-08-04',
                'end_date' => '2025-08-08',
                'metode' => 'Tatap Muka',
                'penyelenggara' => '01j3y4w351qv6754xzf9kgzpha',
                'biaya' => 3900000,
                'transport' => 300000,
                'akomodasi' => 750000,
                'uang_saku' => 1510000,
                'pembebanan_perjadin' => 'Inspektorat Wilayah I',
                'akun_anggaran' => '524111',
                'status' => 'pending',
                'keterangan' => NULL,
            ],
            [
                'id' => Ulid::generate(),
                'name' => 'PPK Tipe A',
                'id_pegawai' => '01j0wth04qxczgzntf10dhd5sg',
                'start_date' => '2025-10-12',
                'end_date' => '2025-10-19',
                'metode' => 'Hybrid',
                'penyelenggara' => '01jh4wtn1h1k123y00dw94bxw2',
                'biaya' => 11850000,
                'transport' => 680000,
                'akomodasi' => 0,
                'uang_saku' => 640000,
                'pembebanan_perjadin' => 'Inspektorat Wilayah III',
                'akun_anggaran' => '524113',
                'status' => 'approved',
                'keterangan' => NULL,
            ]
            // Add more records as needed
        ];
        foreach ($rencanaDiklats as $rencana) {
            RencanaDiklat::create($rencana);
        }
        $this->command->info('Rencana Diklat table seeded!');
    }
}
