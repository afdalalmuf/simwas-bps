<table>
    <thead>
        <tr>
            <th>Pegawai</th>
            <th>Kategori</th>
            <th>Jenis</th>
            <th>Teknis</th>
            <th>Pelatihan</th>
            <th>Mulai</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data as $k)
            <tr>
                <td>{{ optional($k->pegawai)->name }}</td>
                <td>{{ optional($k->teknis->jenis->kategori)->nama }}</td>
                <td>{{ optional($k->teknis->jenis)->nama }}</td>
                <td>{{ optional($k->teknis)->nama }}</td>
                <td>{{ $k->nama_pelatihan }}</td>
                <td>{{ \Carbon\Carbon::parse($k->tgl_mulai)->format('d-m-Y') }}</td>
                <td>{{ $k->status }}</td>
            </tr>
        @endforeach
    </tbody>
</table>