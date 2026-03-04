@extends('layouts.app')

@section('title', 'Verifikasi SPJ Perjadin Diklat')

@push('style')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- CSS Libraries -->
    <link
        href="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.13.4/af-2.5.3/b-2.3.6/b-colvis-2.3.6/b-html5-2.3.6/b-print-2.3.6/cr-1.6.2/date-1.4.1/fc-4.2.2/fh-3.3.2/kt-2.9.0/r-2.4.1/rg-1.3.1/rr-1.3.3/sc-2.1.1/sb-1.4.2/sp-2.1.2/sl-1.6.2/sr-1.2.2/datatables.min.css"
        rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('library') }}/sweetalert2/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="{{ asset('library') }}/bs-stepper/dist/css/bs-stepper.min.css">
@endpush

@section('main')
    @include('components.header')
    @include('components.pegawai-sidebar')

    <div class="main-content">
        <!-- Modal Summary -->
        <div class="modal fade" id="modalSummary" tabindex="-1" role="dialog" aria-labelledby="modalSummaryLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Ringkasan Verifikasi</h5>
                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <ul class="nav nav-tabs" id="summaryTabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="biaya-tab" data-toggle="tab" href="#biaya"
                                    role="tab">Total Biaya</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="verifikasi-tab" data-toggle="tab" href="#verifikasi"
                                    role="tab">Status Verifikasi</a>
                            </li>
                        </ul>
                        <div class="tab-content mt-3">
                            <!-- Biaya Tab -->
                            <div class="tab-pane fade show active" id="biaya" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-6">
                                        <p>Biaya Hotel</p>
                                        <p>Transport Berangkat</p>
                                        <p>Transport Pulang</p>
                                        <p>Transport Lokal</p>
                                        <p>Total Uang Harian</p>
                                        <p>Uang Harian H-1</p>
                                        <p>Uang Harian H+1</p>
                                        <hr>
                                        <p><strong>Total Seluruh Biaya</strong></p>
                                    </div>
                                    <div class="col-md-6 text-right">
                                        <p id="summaryHotel">-</p>
                                        <p id="summaryTransportBerangkat">-</p>
                                        <p id="summaryTransportPulang">-</p>
                                        <p id="summaryTranslok">-</p>
                                        <p id="summaryTotalUH">-</p>
                                        <p id="summaryUHB">-</p>
                                        <p id="summaryUHP">-</p>
                                        <hr>
                                        <p><strong id="summaryGrandTotal">-</strong></p>
                                    </div>
                                </div>
                            </div>

                            <!-- Verifikasi Tab -->
                            <div class="tab-pane fade" id="verifikasi" role="tabpanel">
                                <div id="documentVerifications">
                                    <!-- Generated dynamically -->
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-success" id="save-verification-button">
                            <i class="fa fa-save mr-1"></i> Simpan Verifikasi
                        </button>
                        <button class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>


        <section class="section">
            <div class="section-header">
                <h1>Verifikasi SPJ Diklat</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="/pegawai/dashboard">Dashboard</a></div>
                    <div class="breadcrumb-item active"><a href="/pegawai/spj-diklat">SPJ Perjadin Diklat</a>
                    </div>
                    <div class="breadcrumb-item">Formulir Verifikasi</div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            @include('components.flash-error')
                            <h4>{{ 'Pelatihan: ' . $spjDiklat->rencanadiklat->name }}</h3>
                                <h6>{{ 'Penyelenggara: ' . $spjDiklat->rencanadiklat->penyelenggara_diklat->penyelenggara }}
                                </h6>
                                <div id="stepper1" class="bs-stepper">
                                    <div class="bs-stepper-header" role="tablist">
                                        <div class="step" data-target="#test-l-1">
                                            <button type="button" class="step-trigger" role="tab" id="test-l-1-trigger"
                                                aria-controls="test-l-1">
                                                <span class="bs-stepper-circle"><i
                                                        class="fa-solid fa-circle-info"></i></span>
                                                <span class="bs-stepper-label">Surat Tugas</span>
                                            </button>
                                        </div>
                                        <div class="line"></div>
                                        <div class="step" data-target="#test-l-2">
                                            <button type="button" class="step-trigger" role="tab" id="test-l-2-trigger"
                                                aria-controls="test-l-2">
                                                <span class="bs-stepper-circle"><i
                                                        class="fa-solid fa-file-invoice-dollar"></i></span>
                                                <span class="bs-stepper-label">SPD</span>
                                            </button>
                                        </div>
                                        <div class="line"></div>
                                        <div class="step" data-target="#test-l-3">
                                            <button type="button" class="step-trigger" role="tab"
                                                id="test-l-3-trigger" aria-controls="test-l-3">
                                                <span class="bs-stepper-circle"><i class="fa-solid fa-hotel"></i></span>
                                                <span class="bs-stepper-label">Penginapan</span>
                                            </button>
                                        </div>
                                        <div class="line"></div>
                                        <div class="step" data-target="#test-l-4">
                                            <button type="button" class="step-trigger" role="tab"
                                                id="test-l-4-trigger" aria-controls="test-l-4">
                                                <span class="bs-stepper-circle"><i class="fa-solid fa-bus"></i></span>
                                                <span class="bs-stepper-label">Transportasi</span>
                                            </button>
                                        </div>
                                        <div class="line"></div>
                                        <div class="step" data-target="#test-l-5">
                                            <button type="button" class="step-trigger" role="tab"
                                                id="test-l-5-trigger" aria-controls="test-l-5">
                                                <span class="bs-stepper-circle"><i
                                                        class="fa-solid fa-hand-holding-dollar"></i></span>
                                                <span class="bs-stepper-label">Uang Harian</span>
                                            </button>
                                        </div>
                                        <div class="line"></div>
                                        <div class="step" data-target="#test-l-6">
                                            <button type="button" class="step-trigger" role="tab"
                                                id="test-l-6-trigger" aria-controls="test-l-6">
                                                <span class="bs-stepper-circle"><i class="fa-solid fa-book"></i></span>
                                                <span class="bs-stepper-label">Dokumen</span>
                                            </button>
                                        </div>
                                    </div>
                                    <form id="spj-diklat-update"
                                        action="/pegawai/spj-diklat/{{ $spjDiklat->id_spjDiklat }}" method="POST"
                                        class="needs-validation" novalidate enctype="multipart/form-data">
                                        @csrf
                                        @method('PUT')
                                        <div class="bs-stepper-content">
                                            <div id="test-l-1" class="content">
                                                @csrf
                                                <div class="row">
                                                    <!-- Left: Display Data -->
                                                    <div class="col-md-8 border-right pr-4">
                                                        <iframe src="{{ asset($spjDiklat->st_path) }}" width="100%"
                                                            height="600px" style="border:1px solid #ccc;">
                                                        </iframe>
                                                    </div>

                                                    <!-- Right: Input Form -->
                                                    <div class="col-md-4">
                                                        <input type="hidden" name="id_diklat"
                                                            value="{{ $spjDiklat->rencanaDiklat_id }}">
                                                        <input type="hidden" name="id_spj" id="id_spj"
                                                            value="{{ $spjDiklat->id_spjDiklat }}">
                                                        <div class="form-group">
                                                            <label for="no_st">Nomor Surat Tugas</label>
                                                            <div
                                                                class="d-flex flex-md-row flex-column align-items-md-center align-items-start">
                                                                <span class="me-1 mr-2">B-</span>
                                                                <input type="text" class="form-control me-1 mr-2"
                                                                    id="input1" style="width: 80px;"
                                                                    value="{{ old('input1', $input1_st) }}" required>
                                                                <span class="me-1"> / </span>
                                                                <input type="text" class="form-control me-1 mr-2 ml-2"
                                                                    id="input2" style="width: 80px;"
                                                                    value="{{ old('input2', $input2_st) }}" required>
                                                                <span class="me-1"> / </span>
                                                                <input type="text" class="form-control me-1 mr-2 ml-2"
                                                                    id="input3" style="width: 80px;"
                                                                    value="{{ old('input3', $input3_st) }}" required>
                                                                <span class="me-1"> / </span>
                                                                <input type="text" class="form-control me-1 mr-2 ml-2"
                                                                    id="input4" style="width: 80px;"
                                                                    value="{{ old('input4', $input4_st) }}" required>
                                                            </div>
                                                            <input type="hidden" name="no_st" id="no-st-create"
                                                                value="{{ $spjDiklat->no_st }}">
                                                            <small for="no_st">Contoh : B-1/0800/PW.000/2025</small>
                                                            @error('no_st')
                                                                <div class="invalid-feedback d-block">{{ $message }}
                                                                </div>
                                                            @enderror
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="tgl_mulai_diklat">Tanggal Mulai Penugasan</label>
                                                            <input type="date" name="tgl_mulai" id="tgl_mulai_st"
                                                                class="form-control"
                                                                value="{{ $spjDiklat->tgl_mulai_st }}" required>
                                                            <small>Sesuai dengan surat tugas</small>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="tgl_selesai_diklat">Tanggal Selesai
                                                                Penugasan</label>
                                                            <input type="date" name="tgl_selesai" id="tgl_selesai_st"
                                                                class="form-control"
                                                                value="{{ $spjDiklat->tgl_selesai_st }}" required>
                                                            <small>Sesuai dengan surat tugas</small>
                                                        </div>

                                                        <x-spj-verification-field :document-type="'surat-tugas'" :verification="$verifications['surat-tugas'] ?? null" />
                                                    </div>
                                                </div>
                                                <div class="text-right">
                                                    <button onclick="stepper1.next()" type="button"
                                                        class="btn btn-primary" id="next-form">Selanjutnya
                                                        <i class="fa-solid fa-arrow-right ml-2"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <div id="test-l-2" class="content">
                                            <div id="surat-tugas-wrapper">
                                                <div class="row">
                                                    <!-- Left: Display Data -->
                                                    <div class="col-md-8 border-right pr-4">
                                                        <iframe src="{{ asset($spjDiklat->spd_path) }}" width="100%"
                                                            height="600px" style="border:1px solid #ccc;">
                                                        </iframe>
                                                    </div>
                                                    <!-- Right: Input Form -->
                                                    <div class="col-md-4">

                                                        <div class="form-group">
                                                            <label for="no_spd">Nomor Surat Perjalanan Dinas</label>
                                                            <div
                                                                class="d-flex flex-md-row flex-column align-items-md-center align-items-start">
                                                                <input type="text" class="form-control me-1 mr-2"
                                                                    id="input_spd1" style="width: 80px;"
                                                                    value="{{ old('input_spd1', $input1_spd) }}">
                                                                <span class="me-1"> / </span>
                                                                <input type="text" class="form-control me-1 mr-2 ml-2"
                                                                    id="input_spd2" style="width: 80px;"
                                                                    value="{{ old('input_spd2', $input2_spd) }}">
                                                                <span class="me-1"> / </span>
                                                                <input type="text" class="form-control me-1 mr-2 ml-2"
                                                                    id="input_spd3" style="width: 80px;"
                                                                    value="{{ old('input_spd3', $input3_spd) }}">
                                                                <span class="me-1"> / </span>
                                                                <input type="text" class="form-control me-1 mr-2 ml-2"
                                                                    id="input_spd4" style="width: 80px;"
                                                                    value="{{ old('input_spd4', $input4_spd) }}">
                                                                <span class="me-1"> / </span>
                                                                <input type="text" class="form-control me-1 mr-2 ml-2"
                                                                    id="input_spd5" style="width: 80px;"
                                                                    value="{{ old('input_spd5', $input5_spd) }}">
                                                            </div>
                                                            <input type="hidden" name="no_spd" id="no-spd-create"
                                                                value="{{ $spjDiklat->no_spd }}">
                                                            <small for="no_spd">Contoh :
                                                                085/429332-92000/SPPD-PPIS2910/08/2025</small>
                                                            <small id="error-no_spd" class="text-danger"></small>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="tgl_spd">Tanggal SPD</label>
                                                            <input type="date" name="tgl_spd" id="tgl_spd_create"
                                                                class="form-control" value="2025-07-01">
                                                        </div>

                                                        <x-spj-verification-field :document-type="'spd'" :verification="$verifications['spd'] ?? null" />
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="text-right">

                                                <button type="button" class=" btn btn-outline-primary"
                                                    onclick="stepper1.previous()">
                                                    <i class="fa-solid fa-arrow-left mr-2"></i>Sebelumnya</button>
                                                <button onclick="stepper1.next()" type="button" class="btn btn-primary"
                                                    id="next-form">
                                                    Selanjutnya <i class="fa-solid fa-arrow-right ml-2"></i>
                                                </button>
                                            </div>
                                        </div>

                                        <div id="test-l-3" class="content">
                                            <div class="row">
                                                @if ($spjDiklat->hotel_path != null)
                                                    <!-- Left: Display Data -->
                                                    <div class="col-md-8 border-right pr-4">
                                                        <iframe src="{{ asset($spjDiklat->hotel_path) }}" width="100%"
                                                            height="600px" style="border:1px solid #ccc;">
                                                        </iframe>
                                                    </div>
                                                    <!-- Right: Input Form -->
                                                    <div class="col-md-4">
                                                        <div id="form-invoice-hotel">
                                                            <div class="form-group">
                                                                <label for="nominal_hotel">Total Nominal Biaya
                                                                    Penginapan</label>
                                                                <input type="number" id="nominal_hotel_create"
                                                                    name="nominal_hotel" class="form-control"
                                                                    value="{{ $spjDiklat->nominal_hotel }}">
                                                            </div>
                                                        </div>
                                                        <x-spj-verification-field :document-type="'hotel'" :verification="$verifications['hotel'] ?? null" />
                                                    </div>
                                                @else
                                                <div>
                                                    <h5>Penginapan</h5>
                                                    <p>Tidak ada dokumen penginapan yang diunggah.</p>
                                                </div>
                                                    <select name="verifikasi_hotel" id="verifikasi_hotel"
                                                        class="form-control" hidden>
                                                        <option value="valid" selected>
                                                            ✅ Sesuai</option>
                                                    </select >
                                                @endif
                                            </div>
                                            <div class="text-right">

                                                <button type="button" class=" btn btn-outline-primary"
                                                    onclick="stepper1.previous()">
                                                    <i class="fa-solid fa-arrow-left mr-2"></i>Sebelumnya</button>
                                                <button onclick="stepper1.next()" type="button" class="btn btn-primary"
                                                    id="next-form">
                                                    Selanjutnya <i class="fa-solid fa-arrow-right ml-2"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div id="test-l-4" class="content">

                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label>Tipe perjalanan dinas?</label>
                                                        <div>
                                                            <p>{{ $spjDiklat->tipe_perjadin == 1 ? 'Dalam Kota' : 'Luar Kota' }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label>Moda Transportasi</label>
                                                        <div>
                                                            <p>{{ $spjDiklat->jarak !== null && $spjDiklat->tipe_perjadin == 2 ? 'Kendaraan Pribadi' : 'Kendaraan Umum' }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    @if ($spjDiklat->jarak !== null && $spjDiklat->tipe_perjadin == 2)
                                                        <div class="form-group" id="jarak-berangkat">
                                                            <label for="km_berangkat">Jarak Rumah ke Tempat Diklat
                                                                (km)</label>
                                                            <input type="number" name="km_berangkat"
                                                                id="km_berangkat_create" class="form-control"
                                                                value="{{ $spjDiklat->jarak }}">
                                                            <small>Apabila pulang-pergi(PP) menggunakan kendaraan
                                                                pribadi maka
                                                                jarak
                                                                diisi (2 x jarak)</small>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="line mb-3"></div>
                                            @if ($spjDiklat->tipe_perjadin == 1)
                                                <div class="row">
                                                    <!-- Left: Display Data -->
                                                    <div class="col-md-8 border-right pr-4">
                                                        <div>
                                                            <h5>Transportasi Lokal</h5>
                                                            <iframe src="{{ asset($spjDiklat->translok_path) }}"
                                                                width="100%" height="600px"
                                                                style="border:1px solid #ccc;">
                                                            </iframe>
                                                        </div>
                                                        <br>
                                                    </div>

                                                    <!-- Right: Input Form -->
                                                    <div class="col-md-4">
                                                        <div id="kat-trans-lokal">
                                                            <div id="trans-lokal">
                                                                <div class="row">
                                                                    <div class="col-md-6 border-right pr-4">
                                                                        <div class="form-group">
                                                                            <label for="hari_translok">Jumlah
                                                                                Hari
                                                                                Translok</label>
                                                                            <input type="number" name="hari_translok"
                                                                                id="hari_translok" class="form-control"
                                                                                value="{{ $spjDiklat->nominal_translok / 170000 }}">
                                                                        </div>
                                                                    </div>

                                                                    <div class="col-md-6 border-right pr-4">
                                                                        <div class="form-group">
                                                                            <label for="nominal_translok">Total
                                                                                Translok</label>
                                                                            <input type="number" name="nominal_translok"
                                                                                id="nominal_translok" class="form-control"
                                                                                value="" readonly>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <x-spj-verification-field :document-type="'translok'"
                                                                    :verification="$verifications['translok'] ??
                                                                        null" />
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @else
                                                <div class="line mb-3"></div>
                                                <div class="row">
                                                    <!-- Left: Display Data -->
                                                    <div class="col-md-8 border-right pr-4">
                                                        <div>
                                                            <h5>Transportasi Berangkat</h5>
                                                            <iframe
                                                                src="{{ asset($spjDiklat->transport_berangkat_path) }}"
                                                                width="100%" height="600px"
                                                                style="border:1px solid #ccc;">
                                                            </iframe>
                                                        </div>
                                                        <br>
                                                    </div>
                                                    <!-- Right: Input Form -->
                                                    <div class="col-md-4">

                                                        <div id="kat-luar-kota">
                                                            <div id="trans-luar-kota">
                                                                <div class="form-group">
                                                                    <label for="nominal_transport_berangkat">Nominal
                                                                        Transportasi
                                                                        Berangkat</label>
                                                                    <input type="number"
                                                                        name="nominal_transport_berangkat"
                                                                        id="nominal_transport_berangkat"
                                                                        class="form-control"
                                                                        value="{{ $spjDiklat->nominal_transport_berangkat }}">
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="tgl_transport_berangkat">Tanggal
                                                                        Transportasi
                                                                        Berangkat</label>
                                                                    <input type="date" name="tgl_transport_berangkat"
                                                                        id="tgl_transport_berangkat" class="form-control"
                                                                        value="{{ $spjDiklat->tgl_transport_berangkat }}">
                                                                </div>
                                                                <x-spj-verification-field :document-type="'transport-berangkat'"
                                                                    :verification="$verifications['transport-berangkat'] ??
                                                                        null" />

                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="line mb-3"></div>

                                                <div class="row">
                                                    <!-- Left: Display Data -->
                                                    <div class="col-md-8 border-right pr-4">
                                                        <div>
                                                            <h5>Transportasi Pulang</h5>
                                                            <iframe src="{{ asset($spjDiklat->transport_pulang_path) }}"
                                                                width="100%" height="600px"
                                                                style="border:1px solid #ccc;">
                                                            </iframe>
                                                        </div>
                                                    </div>
                                                    <!-- Right: Input Form -->
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label for="nominal_transport_pulang">Nominal
                                                                Transportasi
                                                                Pulang</label>
                                                            <input type="number" name="nominal_transport_pulang"
                                                                id="nominal_transport_pulang" class="form-control"
                                                                value="{{ $spjDiklat->nominal_transport_pulang }}">
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="tgl_transport_pulang">Tanggal Transportasi
                                                                Pulang</label>
                                                            <input type="date" name="tgl_transport_pulang"
                                                                id="tgl_transport_pulang" class="form-control"
                                                                value="{{ $spjDiklat->tgl_transport_pulang }}">
                                                        </div>
                                                        <x-spj-verification-field :document-type="'transport-pulang'" :verification="$verifications['transport-pulang'] ?? null" />
                                                    </div>
                                                </div>
                                            @endif
                                            <div class="text-right">

                                                <button type="button" class=" btn btn-outline-primary"
                                                    onclick="stepper1.previous()">
                                                    <i class="fa-solid fa-arrow-left mr-2"></i>Sebelumnya</button>
                                                <button onclick="stepper1.next()" type="button" class="btn btn-primary"
                                                    id="next-form">
                                                    Selanjutnya <i class="fa-solid fa-arrow-right ml-2"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div id="test-l-5" class="content">
                                            <div class="form-row">
                                                <div class="form-group col-md-4">
                                                    <label for="jumlah_hari">Jumlah Hari Diklat</label>
                                                    <input type="number" id="jumlah_hari" name="jumlah_hari"
                                                        class="form-control" min="1"
                                                        value="{{ $spjDiklat->hari_diklat }}">
                                                </div>

                                                <div class="form-group col-md-4">
                                                    <label for="uh_diklat">Uang Harian Diklat (per hari)</label>
                                                    <input type="number" name="uh_diklat" id="uh_diklat"
                                                        class="form-control" min="0"
                                                        value="{{ $spjDiklat->uang_diklat }}">
                                                </div>

                                                <div class="form-group col-md-4">
                                                    <label for="total_uh">Total Uang Harian Diklat</label>
                                                    <input type="text" id="total_uh" class="form-control" readonly>

                                                </div>
                                            </div>
                                            <div class="form-row">
                                                <div class="form-group col-md-6" id="form-harian-berangkat">
                                                    <label for="uh_berangkat">Nominal Uang Harian H-1</label>
                                                    <input type="number" name="uh_berangkat" id="uh_berangkat"
                                                        class="form-control"
                                                        value="{{ $spjDiklat->uang_harian_berangkat }}">
                                                </div>

                                                <div class="form-group col-md-6" id="form-harian-pulang">
                                                    <label for="uh_pulang">Nominal Uang Harian H+1</label>
                                                    <input type="number" name="uh_pulang" id="uh_pulang"
                                                        class="form-control"
                                                        value="{{ $spjDiklat->uang_harian_pulang }}">
                                                </div>
                                            </div>

                                            <div class="text-right">

                                                <button type="button" class=" btn btn-outline-primary"
                                                    onclick="stepper1.previous()">
                                                    <i class="fa-solid fa-arrow-left mr-2"></i>Sebelumnya</button>
                                                <button onclick="stepper1.next()" type="button" class="btn btn-primary"
                                                    id="next-form">
                                                    Selanjutnya <i class="fa-solid fa-arrow-right ml-2"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div id="test-l-6" class="content">
                                            <div class="row">
                                                <!-- Left: Display Data -->
                                                <div class="col-md-8 border-right pr-4">
                                                    <h5>Laporan Perjalanan Dinas</h5>
                                                    <iframe src="{{ asset($spjDiklat->laporan_perjadin_path) }}"
                                                        width="100%" height="600px" style="border:1px solid #ccc;">
                                                    </iframe>
                                                </div>
                                                <!-- Right: Input Form -->
                                                <div class="col-md-4">
                                                    <x-spj-verification-field :document-type="'laporan'" :verification="$verifications['laporan'] ?? null" />
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-8 border-right pr-4">
                                                    <h5>Form Permintaan Pembayaran</h5>
                                                    <iframe src="{{ asset($spjDiklat->fpp_path) }}" width="100%"
                                                        height="600px" style="border:1px solid #ccc;">
                                                    </iframe>
                                                </div>
                                                <div class="col-md-4">
                                                    <x-spj-verification-field :document-type="'form-permintaan'" :verification="$verifications['form-permintaan'] ?? null" />
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-8 border-right pr-4">
                                                    <h5>KAK</h5>
                                                    <iframe src="{{ asset($spjDiklat->kak_path) }}" width="100%"
                                                        height="600px" style="border:1px solid #ccc;">
                                                    </iframe>
                                                </div>
                                                <div class="col-md-4">
                                                    <x-spj-verification-field :document-type="'kak'" :verification="$verifications['kak'] ?? null" />
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-8 border-right pr-4">
                                                    <h5>Surat Pemanggilan Diklat</h5>
                                                    <iframe src="{{ asset($spjDiklat->surat_pemanggilan_path) }}"
                                                        width="100%" height="600px" style="border:1px solid #ccc;">
                                                    </iframe>
                                                </div>
                                                <div class="col-md-4">
                                                    <x-spj-verification-field :document-type="'surat-pemanggilan'" :verification="$verifications['surat-pemanggilan'] ?? null" />
                                                </div>
                                            </div>

                                            <div class="text-right">
                                                <button type="button" class=" btn btn-outline-primary"
                                                    onclick="stepper1.previous()">
                                                    <i class="fa-solid fa-arrow-left mr-2"></i>Sebelumnya</button>
                                                <button type="button" class="btn btn-primary"
                                                    id="verification-button"><i class="fa-regular fa-paper-plane"></i>
                                                    Verifikasi</button>
                                            </div>
                                        </div>
                                    </form>
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
    <script src="{{ asset('library') }}/sweetalert2/dist/sweetalert2.min.js"></script>
    <script src="{{ asset('library') }}/bs-stepper/dist/js/bs-stepper.min.js"></script>
    <script src="{{ asset('js') }}/page/verifikator/spj-diklat.js?v=1.2"></script>
    @if ($spjDiklat->tipe_perjadin == 1)
        <script>
            const documentTypes = [
                'surat-tugas', 'spd', 'form-permintaan', 'laporan', 'kak',
                'surat-pemanggilan', 'hotel', 'translok'
            ];
        </script>
    @else
        <script>
            const documentTypes = [
                'surat-tugas', 'spd', 'form-permintaan', 'laporan', 'kak',
                'surat-pemanggilan', 'hotel', 'transport-berangkat', 'transport-pulang'
            ];
        </script>
    @endif

    <!-- Page Specific JS File -->
@endpush
