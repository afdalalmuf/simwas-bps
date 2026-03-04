<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Daftar Pembayaran Perjalanan Dinas</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        table { border-collapse: collapse; width: 100%; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 5px; text-align: center; }
        .no-border { border: none !important; }
        .text-left { text-align: left; }
        .bold { font-weight: bold; }
    </style>
</head>
<body>
    <h3 style="text-align:center;">DAFTAR PEMBAYARAN PERJALANAN DINAS</h3>
    <h3 style="text-align:center;">DALAM RANGKA MENGIKUTI DIKLAT {{Str::upper( $spj->rencanadiklat->name)}} DI {{Str::upper( $spj->rencanadiklat->penyelenggara_diklat->penyelenggara)}}</h3>
    <p style="text-align:center;">{{$kota}}, {{ $periode }}</p>

    <table class="no-border" style="margin-bottom: 15px;">
        <tr><td class="text-left no-border">PROGRAM</td><td class="text-right no-border">:</td><td class="text-left no-border"> PROGRAM DUKUNGAN MANAJEMEN (054.01.WA)</td></tr>
        <tr><td class="text-left no-border">KEGIATAN</td><td class="text-right no-border">:</td><td class="text-left no-border"> {{$anggaran['kegiatan']}}</td></tr>
        <tr><td class="text-left no-border">KRO</td><td class="text-right no-border">:</td><td class="text-left no-border">  {{$anggaran['kro']}}</td></tr>
        <tr><td class="text-left no-border">RINCIAN OUTPUT</td><td class="text-right no-border">:</td><td class="text-left no-border">  {{$anggaran['ro']}}</td></tr>
        <tr><td class="text-left no-border">KOMPONEN</td><td class="text-right no-border">:</td><td class="text-left no-border">  {{$anggaran['komponen']}}</td></tr>
        <tr><td class="text-left no-border">AKUN</td><td class="text-right no-border">:</td><td class="text-left no-border">  {{$anggaran['akun']}}</td></tr>
    </table>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th  style="width: 100px;">Nama</th>
                <th>NIP</th>
                <th>Tanggal</th>
                <th>Lama<br>Perjalanan<br>(hari)</th>
                <th>Tiket<br>Berangkat</th>
                <th>Tiket<br>Pulang</th>
                <th>Tiket PP<br></th>
                <th>Translok<br></th>
                <th>Hotel</th>
                <th>Uang Harian</th>
                <th>Uang Harian Diklat</th>
                <th>Jumlah Diterima</th>
                <th>Bank</th>
                <th>No Rek</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($peserta as $index => $p)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td class="text-left">{{ $p['nama'] }}</td>
                    <td>{{ $p['nip'] }}</td>
                    <td>{{ $p['tanggal'] }}</td>
                    <td>{{ $p['lama_perjalanan'] }}</td>
                    <td>{{ $p['tiket_berangkat'] }}</td>
                    <td>{{ $p['tiket_pulang'] }}</td>
                    <td>{{ $p['tiket_pp'] }}</td>
                    <td>{{ $p['translok'] }}</td>
                    <td>{{ $p['hotel'] }}</td>
                    <td>{{ $p['uang_harian'] }}</td>
                    <td>{{ $p['uang_diklat'] }}</td>
                    <td>{{ $p['jumlah_diterima'] }}</td>
                    <td>{{ $p['bank'] }}</td>
                    <td>{{ $p['norek'] }}</td>
                </tr>
            @endforeach
            <tr class="bold">
                <td colspan="5">JUMLAH</td>
                <td>{{ $totalTiketBerangkat }}</td>
                <td>{{ $totalTiketPulang }}</td>
                <td>{{ $totalTiketPP }}</td>
                <td>{{ $totalTranslok }}</td>
                <td>{{ $totalHotel }}</td>
                <td>{{ $totalUH }}</td>
                <td>{{ $totalUHDiklat }}</td>
                <td>{{ $totalDiterima }}</td>
                <td colspan="2"></td>
            </tr>
        </tbody>
    </table>

    <p style="margin-top: 10px;"><strong>Terbilang :</strong> {{ $terbilang }} Rupiah</p>

    <br><br>
    <table class="no-border">
        <tr>
            <td class="no-border" style="text-align:center;">
                Lunas pada tgl :
                <br>Bendahara Pengeluaran
                <br><br><br><br><br>
                <strong>SEPTI ADIYATI BANANINGTIYAS</strong>
                <br>NIP. 198509072006042001
            </td>
            <td class="no-border" style="text-align:center;">
                Jakarta, {{ $tanggal }}
                <br>Setuju Dibayar
                <br>Pejabat Pembuat Komitmen
                <br><br><br><br>
                <strong>ACHMAD FADLY</strong>
                <br>NIP. 198511282006041002
            </td>
            <td class="no-border" style="text-align:center;">
                Pembuat Daftar,
                <br><br><br><br><br>
                <strong>{{strtoupper($verifikator->name)}}</strong>
                <br>NIP. {{$verifikator->nip}}
            </td>
        </tr>
    </table>
</body>
</html>
