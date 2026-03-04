@extends('layouts.app')

@section('title', 'Unggah SKP Tahunan dan Bulanan')

@push('style')
    <!-- CSS Libraries -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- CSS Libraries -->
    <link
        href="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.13.4/af-2.5.3/b-2.3.6/b-colvis-2.3.6/b-html5-2.3.6/b-print-2.3.6/cr-1.6.2/date-1.4.1/fc-4.2.2/fh-3.3.2/kt-2.9.0/r-2.4.1/rg-1.3.1/rr-1.3.3/sc-2.1.1/sb-1.4.2/sp-2.1.2/sl-1.6.2/sr-1.2.2/datatables.min.css"
        rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('library') }}/sweetalert2/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="https://unpkg.com/intro.js/minified/introjs.min.css">
    <style>
        /* Ubah ukuran tooltip */
        .introjs-tooltip {
            max-width: 100px;
            background-color: #000000;
            color: #ffffff;
            border: 1px solid #000000;
            border-radius: 1rem;
        }

        /* Ubah warna tombol */
        .introjs-button {
            background-color: #ffffff;
            color: rgb(0, 0, 0);
            border: none;
            padding: 6px 12px;
            border-radius: 4px;
        }

        .introjs-button:hover {
            background-color: #00aeff;
        }

        /* Tombol “Skip” */
        .introjs-skipbutton {
            color: #ffffff;
        }
    </style>
@endpush

@section('main')
    @include('components.header')
    @include('components.pegawai-sidebar')
    @include('components.master-objek.form.create-skp-penilaian', ['kategori' => $kategori]);
    @include('components.master-objek.form.create-skp-penetapan');
    @if ($skp_penetapan !== null)
        @include('components.master-objek.form.edit-skp-penetapan');
    @endif
    @if ($skp_penilaian !== null || $skp_bulanan !== null)
        @include('components.master-objek.form.edit-skp-penilaian');
    @endif
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Unggah SKP Tahunan dan Bulanan</h1>
                <button id="start-tour-btn" class="tour-icon-btn" data-bs-toggle="tooltip" data-bs-placement="left"
                    title="Mulai Tour Aplikasi">
                    <i class="fa fa-book-open-reader"></i>
                </button>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="/pegawai/dashboard">Dashboard</a></div>
                    <div class="breadcrumb-item">Unggah SKP Tahunan dan Bulanan</div>
                </div>
            </div>

            <div class="section-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="">
                                    <form id="yearForm" action="" method="GET" class="px-0">
                                        @csrf
                                        <div class="form-group" id="step1">
                                            <label for="yearSelect">Pilih Tahun</label>
                                            <select name="year" id="yearSelect" class="form-control select2 col-md-1">
                                                @php
                                                    $currentYear = date('Y');
                                                    $lastThreeYears = range($currentYear, $currentYear - 3);
                                                @endphp

                                                @foreach ($lastThreeYears as $year)
                                                    <option value="{{ $year }}"
                                                        {{ request()->query('year') == $year ? 'selected' : '' }}>
                                                        {{ $year }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </form>
                                    @include('components.flash')
                                    {{ session()->forget(['alert-type', 'status']) }}
                                    <div class="d-flex justify-content-between">
                                        <p class="mt-5 mb-0">
                                            <span class="badge alert-primary mr-2"><i class="fas fa-info"></i></span>
                                            SKP Tahunan
                                        </p>
                                        <div id="download-button">
                                        </div>
                                    </div>
                                    <table class="table table-bordered table-striped display responsive"
                                        id="table-upload-skp-tahunan">
                                        <thead>
                                            <tr>
                                                <th class="text-center">Jenis</th>
                                                <th class="text-center">Status</th>
                                                <th class="text-center">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>SKP Tahunan <b>Penetapan</b></td>
                                                <td>
                                                    @if ($skp_penetapan == null)
                                                        <span class="badge badge-warning">
                                                            Belum Kirim
                                                        </span>
                                                    @else
                                                        @if ($skp_penetapan->status == 'Diperiksa')
                                                            <span class="badge badge-info">
                                                                {{ $skp_penetapan->status }}
                                                            </span>
                                                        @elseif ($skp_penetapan->status == 'Sudah Kirim')
                                                            <span class="badge badge-success">
                                                                {{ $skp_penetapan->status }}
                                                            </span>
                                                        @elseif ($skp_penetapan->status == 'Ditolak')
                                                            <span class="badge badge-danger">
                                                                {{ $skp_penetapan->status }}
                                                            </span>
                                                        @endif
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($skp_penetapan != null)
                                                        <a href="#" class="btn btn-primary edit-skp-penetapan"
                                                            data-toggle="modal" data-target="#modal-edit-skp-penetapan"
                                                            data-id-penetapan="{{ $skp_penetapan->id }}"
                                                            data-catatan="{{ $skp_penetapan->catatan }}"
                                                            data-status="{{ $skp_penetapan->status }}"
                                                            data-tgl-upload="{{ $skp_penetapan->updated_at }}">
                                                            <i class=" fas fa-eye" style="font-size: 11.8px;"></i>
                                                        </a>
                                                    @else
                                                        <a href="#" class="btn btn-success create-skp-penetapan"
                                                            data-toggle="modal" data-target="#modal-create-skp-penetapan"
                                                            data-jenis-penetapan="penetapan"
                                                            data-tahun-penetapan="{{ $tahun }}">
                                                            <i class=" fas fa-pencil" style="font-size: 11.8px;"></i>
                                                        </a>
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>SKP Tahunan <b>Penilaian</b></td>
                                                <td>
                                                    @if ($skp_penilaian == null)
                                                        <span class="badge badge-warning">
                                                            Belum Kirim
                                                        </span>
                                                    @else
                                                        @if ($skp_penilaian->status == 'Diperiksa')
                                                            <span class="badge badge-info">
                                                                {{ $skp_penilaian->status }}
                                                            </span>
                                                        @elseif ($skp_penilaian->status == 'Sudah Kirim')
                                                            <span class="badge badge-success">
                                                                {{ $skp_penilaian->status }}
                                                            </span>
                                                        @elseif ($skp_penilaian->status == 'Ditolak')
                                                            <span class="badge badge-danger">
                                                                {{ $skp_penilaian->status }}
                                                            </span>
                                                        @endif
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($skp_penilaian != null)
                                                        <a href="#" class="btn btn-primary edit-skp-penilaian"
                                                            data-toggle="modal" data-target="#modal-edit-skp-penilaian"
                                                            data-id-penilaian="{{ $skp_penilaian->id }}"
                                                            data-rating-hasil="{{ $skp_penilaian->kat_rating_hasil_kerja }}"
                                                            data-rating-perilaku="{{ $skp_penilaian->kat_rating_perilaku_kerja }}"
                                                            data-predikat="{{ $skp_penilaian->predikat_kinerja }}"
                                                            data-status="{{ $skp_penilaian->status }}"
                                                            data-catatan="{{ $skp_penilaian->catatan }}"
                                                            data-tgl-upload="{{ $skp_penilaian->updated_at }}">
                                                            <i class=" fas fa-eye" style="font-size: 11.8px;"></i>
                                                        </a>
                                                    @else
                                                        <a href="#" class="btn btn-success create-skp-penilaian"
                                                            data-toggle="modal" data-target="#modal-create-skp-penilaian"
                                                            data-jenis-penilaian="penilaian"
                                                            data-tahun-penilaian="{{ $tahun }}">
                                                            <i class=" fas fa-pencil" style="font-size: 11.8px;"></i>
                                                        </a>
                                                    @endif
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <div class="d-flex justify-content-between">
                                        <p class="mt-5 mb-0">
                                            <span class="badge alert-primary mr-2"><i class="fas fa-info"></i></span>
                                            SKP Bulanan
                                        </p>
                                        <div id="download-button">
                                        </div>
                                    </div>
                                    <table class="table table-bordered table-striped display responsive"
                                        id="table-upload-skp-bulanan">
                                        <thead>
                                            <tr>
                                                <th class="text-center">Bulan</th>
                                                <th class="text-center">Status</th>
                                                <th class="text-center">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($skp_bulanan as $bulan => $nilai)
                                                <tr>
                                                    <td>{{ $bulan }}</td>
                                                    <td>
                                                        @if ($nilai['status'] == null)
                                                            <span class="badge badge-warning">
                                                                Belum Kirim
                                                            </span>
                                                        @else
                                                            @if ($nilai['status'] == 'Diperiksa')
                                                                <span class="badge badge-info">
                                                                    {{ $nilai['status'] }}
                                                                </span>
                                                            @elseif ($nilai['status'] == 'Sudah Kirim')
                                                                <span class="badge badge-success">
                                                                    {{ $nilai['status'] }}
                                                                </span>
                                                            @elseif ($nilai['status'] == 'Ditolak')
                                                                <span class="badge badge-danger">
                                                                    {{ $nilai['status'] }}
                                                                </span>
                                                            @endif
                                                        @endif
                                                    </td>
                                                    <td data-intro="Upload SKP pada menu Aksi" data-step="4" >
                                                        @if ($nilai['status'] == null)
                                                            <a href="#" class="btn btn-success create-skp-penilaian"
                                                                data-toggle="modal"
                                                                data-target="#modal-create-skp-penilaian"
                                                                data-jenis-penilaian="bulanan"
                                                                data-tahun-penilaian="{{ $tahun }}"
                                                                data-bulan="{{ $nilai['month'] }}">
                                                                <i class=" fas fa-pencil" style="font-size: 11.8px;"></i>
                                                            </a>
                                                        @elseif ($nilai['status'] === 'Tidak Ada')
                                                        @else
                                                            <a href="#" class="btn btn-primary edit-skp-penilaian"
                                                                data-toggle="modal"
                                                                data-target="#modal-edit-skp-penilaian"
                                                                data-id-penilaian="{{ $nilai['id'] }}"
                                                                data-rating-hasil="{{ $nilai['kat_rating_hasil_kerja'] }}"
                                                                data-rating-perilaku="{{ $nilai['kat_rating_perilaku_kerja'] }}"
                                                                data-predikat="{{ $nilai['predikat_kinerja'] }}"
                                                                data-status="{{ $nilai['status'] }}"
                                                                data-catatan="{{ $nilai['catatan'] }}"
                                                                data-tgl-upload="{{ $nilai['updated_at'] }}">
                                                                <i class=" fas fa-eye" style="font-size: 11.8px;"></i>
                                                            </a>
                                                        @endif
                                                    </td>
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
    <script src="https://unpkg.com/intro.js/minified/intro.min.js"></script>

    <!-- Page Specific JS File -->
    <script src="{{ asset('js') }}/page/pegawai/upload-skp.js?v=1.0.2"></script>
@endpush
