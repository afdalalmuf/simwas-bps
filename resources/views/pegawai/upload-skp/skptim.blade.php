@extends('layouts.app')

@section('title', 'Rekap Dokumen SKP Tim')

@push('style')
    <!-- CSS Libraries -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- CSS Libraries -->
    <link
        href="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.13.4/af-2.5.3/b-2.3.6/b-colvis-2.3.6/b-html5-2.3.6/b-print-2.3.6/cr-1.6.2/date-1.4.1/fc-4.2.2/fh-3.3.2/kt-2.9.0/r-2.4.1/rg-1.3.1/rr-1.3.3/sc-2.1.1/sb-1.4.2/sp-2.1.2/sl-1.6.2/sr-1.2.2/datatables.min.css"
        rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('library') }}/sweetalert2/dist/sweetalert2.min.css">
@endpush

@section('main')
    @include('components.header')
    @include('components.pegawai-sidebar')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Rekap Dokumen SKP</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="/pegawai/dashboard">Dasbor</a></div>
                    <div class="breadcrumb-item">Rekap Dokumen SKP Tim</div>
                </div>
            </div>

            <div class="section-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <div>
                                    <div class="d-flex justify-content-between">
                                        <p class="mt-2 mb-5">
                                            <span class="badge alert-primary mr-2"><i class="fas fa-info"></i></span>
                                            {{ $tim->nama }}
                                        </p>
                                    </div>
                                    <table class="table table-bordered table-striped display responsive" id="table-cek-skp">
                                        <thead class="text-center">
                                            <tr>
                                                <th rowspan="2" class="align-middle">No.</th>
                                                <th rowspan="2" class="align-middle">Pegawai</th>
                                                <th rowspan="2" class="align-middle">Persentase</th>
                                                <th colspan="12" class="text-center" id="title">Bulanan</th>
                                                <th colspan="2" class="text-center" id="skp_tahunan">Tahunan</th>
                                            </tr>
                                            <tr>
                                                <th>Jan</th>
                                                <th>Feb</th>
                                                <th>Mar</th>
                                                <th>Apr</th>
                                                <th>Mei</th>
                                                <th>Jun</th>
                                                <th>Jul</th>
                                                <th>Agu</th>
                                                <th>Sep</th>
                                                <th>Okt</th>
                                                <th>Nov</th>
                                                <th>Des</th>
                                                <th>Penetapan</th>
                                                <th>Penilaian</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($skp_all as $row)
                                                <tr>
                                                    <td></td>
                                                    <td>{{ $row['user'] }}</td>
                                                    @if ($row['persentase'] < 50)
                                                        <td class="text-right"><span class="badge badge-danger">
                                                                {{ $row['persentase'] }}%
                                                            </span></td>
                                                    @elseif ($row['persentase'] < 100)
                                                        <td class="text-right"><span class="badge badge-warning">
                                                                {{ $row['persentase'] }}%
                                                            </span></td>
                                                    @else
                                                        <td class="text-right"><span class="badge badge-danger">
                                                                {{ $row['persentase'] }}%
                                                            </span></td>
                                                    @endif
                                                    @foreach ($row['bulanan'] as $bulan => $data)
                                                        @if ($data['status'] == null)
                                                            <td>
                                                                <span class="badge badge-warning">
                                                                    Belum Kirim
                                                                </span>
                                                            </td>
                                                        @elseif ($data['status'] == 'Diperiksa')
                                                            <td>
                                                                <span class="badge badge-info">
                                                                    {{ $data['status'] }}
                                                                </span>
                                                            </td>
                                                        @elseif ($data['status'] == 'Sudah Kirim')
                                                            <td>
                                                                <span class="badge badge-success">
                                                                    {{ $data['tgl_upload']->format('d/m/Y') }}
                                                                </span>
                                                            </td>
                                                        @elseif ($data['status'] == 'Ditolak')
                                                            <td>
                                                                <span class="badge badge-danger">
                                                                    {{ $data['status'] }}
                                                                </span>
                                                            </td>
                                                        @else
                                                            <td class="text-center">
                                                                <span class="badge">
                                                                    -
                                                                </span>
                                                            </td>
                                                        @endif
                                                    @endforeach
                                                    {{-- Baris Penetapan --}}
                                                    @if ($row['penetapan'] == null)
                                                        <td>
                                                            <span class="badge badge-warning">
                                                                Belum Kirim
                                                            </span>
                                                        </td>
                                                    @elseif ($row['penetapan']['status'] == 'Diperiksa')
                                                        <td>
                                                            <span class="badge badge-info">
                                                                {{ $row['penetapan']['status'] }}
                                                            </span>
                                                        </td>
                                                    @elseif ($row['penetapan']['status'] == 'Sudah Kirim')
                                                        <td>
                                                            <span class="badge badge-success">
                                                                {{ $row['penetapan']['tgl_upload']->format('d/m/Y') }}
                                                            </span>
                                                        </td>
                                                    @else
                                                        <td>
                                                            <span class="badge badge-danger">
                                                                {{ $row['penetapan']['status'] }}
                                                            </span>
                                                        </td>
                                                    @endif
                                                    {{-- Baris Penilaian --}}
                                                    @if ($row['penilaian'] == null)
                                                        <td>
                                                            <span class="badge badge-warning">
                                                                Belum Kirim
                                                            </span>
                                                        </td>
                                                    @elseif ($row['penilaian']['status'] == 'Diperiksa')
                                                        <td>
                                                            <span class="badge badge-info">
                                                                {{ $row['penilaian']['status'] }}
                                                            </span>
                                                        </td>
                                                    @elseif ($row['penilaian']['status'] == 'Sudah Kirim')
                                                        <td>
                                                            <span class="badge badge-success">
                                                                {{ $row['penilaian']['tgl_upload']->format('d/m/Y') }}
                                                            </span>
                                                        </td>
                                                    @else
                                                        <td>
                                                            <span class="badge badge-danger">
                                                                {{ $row['penilaian']['status'] }}
                                                            </span>
                                                        </td>
                                                    @endif
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    <!-- JS Libraies -->
    {{-- <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.js"></script> --}}
    <script src="https://cdn.datatables.net/v/dt/dt-1.13.4/b-2.3.6/b-colvis-2.3.6/datatables.min.js"></script>
    <script src="{{ asset('js') }}/plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="{{ asset('js') }}/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script src="{{ asset('js') }}/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
    <script src="{{ asset('js') }}/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
    <script src="{{ asset('js') }}/plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
    <script src="{{ asset('js') }}/plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
    <script src="{{ asset('js') }}/plugins/jszip/jszip.min.js"></script>
    <script src="{{ asset('js') }}/plugins/pdfmake/pdfmake.min.js"></script>
    <script src="{{ asset('js') }}/plugins/pdfmake/vfs_fonts.js"></script>
    <script src="{{ asset('js') }}/plugins/datatables-buttons/js/buttons.html5.min.js"></script>
    <script src="{{ asset('js') }}/plugins/datatables-buttons/js/buttons.print.min.js"></script>
    <script src="{{ asset('js') }}/plugins/datatables-buttons/js/buttons.colVis.min.js"></script>
    <script src="{{ asset('library') }}/sweetalert2/dist/sweetalert2.min.js"></script>

    <!-- Page Specific JS File -->
    <script src="{{ asset('js') }}/page/pegawai/cek-skp.js"></script>
@endpush
