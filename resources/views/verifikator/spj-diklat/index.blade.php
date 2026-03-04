@extends('layouts.app')

@section('title', 'SPJ Diklat')

@push('style')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    {{-- <meta name="base-url" content="{{ route('master-pegawai.destroy', ':id') }}"> --}}
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
                <h1>SPJ Perjadin Diklat</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="/pegawai/dashboard">Dashboard</a></div>
                    <div class="breadcrumb-item">SPJ Perjadin Diklat</div>
                </div>
            </div>

            @if (session()->has('success'))
                <div class="alert alert-success" role="alert">
                    {{ session('success') }}
                </div>
            @endif

            <div class="section-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">

                                <div class="d-flex justify-content-between">
                                    <p class="mb-3">
                                        <span class="badge alert-primary mr-2"><i class="fas fa-info"></i></span>
                                        Halaman ini menampilkan daftar spj perjadin diklat pegawai.
                                    </p>
                                    <div id="download-button">
                                    </div>
                                </div>


                                <div class="d-flex justify-content-between flex-wrap my-2 mb-3" style="gap:10px">
                                    <div class="form-group flex-grow-1" style="margin-bottom: 0;">
                                        <div id="filter-search-wrapper">
                                        </div>
                                    </div>
                                    {{-- tahun from $tahun --}}
                                    <form id="yearForm" action="" method="GET">
                                        @csrf
                                        <div class="form-group" style="margin-bottom: 0; max-width: 200px;">
                                            <label for="filter-tahun" style="margin-bottom: 0;">Tahun</label>
                                            <select name="year" id="yearSelect" class="form-control select2">
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
                                    <div class="form-group
                                    {{ request()->status ? 'd-none' : '' }}"
                                        style="margin-bottom: 0; max-width: 200px;">
                                        <label for="filter-status" style="margin-bottom: 0;">Status</label>
                                        <select name="status" id="filter-status" class="form-control select2">
                                            <option value="">Semua</option>
                                            <option value="Terkirim"
                                                {{ request()->status == 'Terkirim' ? 'selected' : '' }}>
                                                Belum Diverifikasi
                                            </option>
                                            <option value="Dikembalikan"
                                                {{ request()->status == 'Dikembalikan' ? 'selected' : '' }}>
                                                Dikembalikan
                                            </option>
                                            <option value="Disetujui"
                                                {{ request()->status == 'Disetujui' ? 'selected' : '' }}>
                                                Disetujui
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div>
                                    <table class="table table-bordered table-striped display responsive"
                                        id="table-spj-diklat">
                                        <thead>
                                            <tr>
                                                <th style="width: 10px; text-align:center">No</th>
                                                <th  style="width: 300px;">Pegawai</th>
                                                <th>Nama Diklat</th>
                                                <th>Metode</th>
                                                <th>Tanggal Mulai</th>
                                                <th>Tanggal Selesai</th>
                                                <th>Penyelenggara</th>
                                                <th>Status</th>
                                                <th class="text-center">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($spjDiklats as $spjDiklat)
                                                <tr>
                                                    <td class="text-center" style="width: 10px; text-align:center"
                                                        scope="row">{{ $loop->iteration }}</td>
                                                    <td>{{ $spjDiklat->rencanadiklat->pegawai->name }}</td>
                                                    <td>{{ $spjDiklat->rencanadiklat->name }}</td>
                                                    <td>{{ $spjDiklat->rencanadiklat->metode }}</td>
                                                    <td>{{ date('d F Y', strtotime($spjDiklat->rencanadiklat->start_date)) }}
                                                    </td>
                                                    <td>{{ date('d F Y', strtotime($spjDiklat->rencanadiklat->end_date)) }}
                                                    </td>
                                                    <td>{{ $spjDiklat->rencanadiklat->penyelenggara_diklat->penyelenggara }}
                                                    </td>
                                                    <td>
                                                        @if ($spjDiklat->rencanadiklat->spj_diklat?->status === 'Disetujui')
                                                            <span
                                                                class="badge badge-success">{{ $spjDiklat->rencanadiklat->spj_diklat?->status }}</span>
                                                        @elseif ($spjDiklat->rencanadiklat->spj_diklat?->status === 'Dikembalikan')
                                                            <span
                                                                class="badge badge-danger">{{ $spjDiklat->rencanadiklat->spj_diklat?->status }}</span>
                                                        @else
                                                            <span class="badge badge-warning">Belum Diverifikasi</span>
                                                        @endif

                                                    </td>
                                                    <td class="text-center">
                                                        @if ($spjDiklat->rencanadiklat->spj_diklat?->status === 'Terkirim')
                                                            <a href="/verifikator-spj/spj-diklat/show/{{ $spjDiklat->rencanadiklat->spj_diklat?->id_spjDiklat }}"
                                                                class="btn btn-primary btn-sm">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                        @elseif ($spjDiklat->rencanadiklat->spj_diklat?->status === 'Disetujui')
                                                            <a target="_blank" href="{{ route('verifikator.spj-diklat.download-nominatif', $spjDiklat->rencanadiklat->spj_diklat->id_spjDiklat) }}"
                                                                class="btn btn-success btn-sm" data-toggle="tooltip">
                                                                <i class="fas fa-file-alt"></i>
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
    <script src="{{ asset('js') }}/page/pegawai/spj-diklat-index.js"></script>
@endpush
