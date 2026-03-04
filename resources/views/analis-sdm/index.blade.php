@extends('layouts.app')

@section('title', 'Analis SDM')

@push('style')
    <!-- CSS Libraries -->
    <link rel="stylesheet" href="{{ asset('library/jqvmap/dist/jqvmap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('library/summernote/dist/summernote-bs4.min.css') }}">
    <link rel="stylesheet"
        href="{{ asset('https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/css/bootstrap.css') }}">
    <link rel="stylesheet" href="{{ asset('https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap4.min.css') }}">
@endpush

@section('main')
    @include('components.analis-sdm-header')
    @include('components.analis-sdm-sidebar')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Analis SDM Dashboard</h1>
            </div>
            <div class="row">
                <div class="col-lg-6 col-md-6 col-sm-6 col-6">
                    <h2 class="font-weight-normal text-dark h5 mb-3">Dashboard Kompetensi</h2>
                    <div class="row">
                        <div class="col-lg-12 col-md-6 col-sm-6 col-12">
                            <div class="card">
                                <div class="card-body p-0">
                                    <select class="form-control select2" id="filterPegawai" autocomplete="off">
                                        <option value="" selected>Pilih Pegawai</option>
                                        @foreach ($pegawai as $p)
                                            <option value="{{ $p->id }}">{{ $p->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4>Jumlah Pelatihan Teknis</h4>
                                </div>
                                <div class="card-body" style="padding-top: 3px;">
                                    <canvas id="diklatChart" height="140"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4>Total Durasi Pelatihan Teknis (JP)</h4>
                                </div>
                                <div class="card-body" style="padding-top: 3px;">
                                    <canvas id="JPChart" height="140"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-6 col-6">
                    <h2 class="font-weight-normal text-dark h5 mb-3">Dashboard Upload SKP</h2>
                    <div class="row">
                        <div class="col-lg-12 col-md-6 col-sm-6 col-12">
                            <div class="card">
                                <div class="card-body p-0">
                                    <select class="form-control select2" id="tahunFilter" autocomplete="off">
                                        <option value="" selected>Pilih Tahun</option>
                                        @php
                                            $currentYear = date('Y');
                                            $lastThreeYears = range($currentYear, $currentYear - 3);
                                            $selectedYear = request()->query('year') ?? $currentYear;
                                        @endphp

                                        @foreach ($lastThreeYears as $year)
                                            <option value="{{ $year }}"
                                                {{ $selectedYear == $year ? 'selected' : '' }}>
                                                {{ $year }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4>Persentase Dokumen SKP Menurut Status dan Unit Kerja</h4>
                                </div>
                                <div class="card-body" style="padding-top: 5px;">
                                    <div class="d-flex justify-content-between">
                                        <div class="d-flex flex-row align-items-center">
                                        </div>
                                        <a href="/analis-sdm/cek-skp" class="arrow-button-card" type="button"
                                            class="rounded-circle"><i class="fa-solid fa-arrow-right"></i></a>
                                    </div>
                                    <canvas id="skpChart" height="250px"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Tabel Persentase Dokumen SKP Menurut Unit Kerja</h4>
                        </div>
                        <div class="card-body" style="padding-top: 5px;">
                            <table class="table table-bordered table-striped display responsive" id="table-cek-skp">
                                <thead class="text-center">
                                    <tr>
                                        <th>Unit Kerja</th>
                                        @foreach ($namaBulan as $kode => $label)
                                            <th>{{ $label }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($jumlahPerUnit as $unit => $bulanData)
                                        <tr>
                                            <td>{{ $unit }}</td>
                                            @for ($i = 1; $i <= 14; $i++)
                                                @php $key = str_pad($i, 2, '0', STR_PAD_LEFT); @endphp
                                                <td class="text-right">{{ $bulanData[$key] ?? 0 }}%</td>
                                            @endfor
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
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
    <script src="{{ asset('library/chart.js/dist/Chart.min.js') }}"></script>

    <!-- Page Specific JS File -->
    <script>
        var years = @json($years);
        var diklat_count = @json($diklat_count);
        var jp_count = @json($jp_count);
    </script>
    <script src="{{ asset('js') }}/page/kompetensi-chart.js"></script>
    {{-- <script src="{{ asset('js') }}/page/kompetensi.js"></script> --}}
@endpush
