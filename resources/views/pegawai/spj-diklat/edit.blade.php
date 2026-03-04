@extends('layouts.app')

@section('title', 'Pengajuan SPJ Perjadin Diklat')

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
        <div class="modal fade" id="modalSummary" tabindex="-1" role="dialog" aria-labelledby="modalSummaryLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Catatan Verifikasi</h5>
                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <div id="documentVerifications">
                            <!-- Filled dynamically -->
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Modal Preview -->
        <div class="modal fade" id="modalPreview" tabindex="-1" role="dialog" aria-labelledby="modalPreviewLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-xl" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalPreviewLabel">Lihat Data</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <h5 class="mb-4">{{ $diklat->name }}</h5>
                        <!-- Tabs -->
                        <ul class="nav nav-pills" id="previewTabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="tab-surat" data-toggle="tab" href="#preview-surat"
                                    role="tab">Surat Tugas</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="tab-spd" data-toggle="tab" href="#preview-spd"
                                    role="tab">SPD</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="tab-penginapan" data-toggle="tab" href="#preview-penginapan"
                                    role="tab">Penginapan</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="tab-transport" data-toggle="tab" href="#preview-transport"
                                    role="tab">Transportasi</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="tab-uang-harian" data-toggle="tab" href="#preview-uang-harian"
                                    role="tab">Uang Harian</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="tab-dokumen" data-toggle="tab" href="#preview-dokumen"
                                    role="tab">Dokumen</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="tab-rekap" data-toggle="tab" href="#preview-rekap"
                                    role="tab">Rekap Nominal Perjadin</a>
                            </li>
                        </ul>
                        <div class="tab-content mt-3" id="previewTabContent">
                            <div class="tab-pane fade show active" id="preview-surat" role="tabpanel"></div>
                            <div class="tab-pane fade" id="preview-spd" role="tabpanel"></div>
                            <div class="tab-pane fade" id="preview-penginapan" role="tabpanel"></div>
                            <div class="tab-pane fade" id="preview-transport" role="tabpanel"></div>
                            <div class="tab-pane fade" id="preview-uang-harian" role="tabpanel"></div>
                            <div class="tab-pane fade" id="preview-dokumen" role="tabpanel"></div>
                            <div class="tab-pane fade" id="preview-rekap" role="tabpanel"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-success" id="updateFormBtn"><i
                                class="fa-regular fa-floppy-disk"></i> Simpan</button>
                    </div>
                </div>
            </div>
        </div>
        {{-- Modal tambah rekening --}}
        <div class="modal fade" id="modalTambahRekening" tabindex="-1"role="dialog" aria-labelledby="modalPreviewLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalPreviewLabel">Tambah Rekening</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="formTambahRekening">
                            @csrf
                            <input type="text" name="nama_bank" class="form-control" placeholder="Nama Bank"
                                required>
                            <input type="text" name="no_rekening" class="form-control mt-2"
                                placeholder="Nomor Rekening" required>
                            <div class="text-danger small mt-1" id="errorTambahRekening"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </div>
                </form>
            </div>
        </div>

        <!-- Modal Disclaimer -->
        <!-- Modal Konfirmasi Kirim -->
        <div class="modal fade" id="modalKirimSpj" tabindex="-1"role="dialog" aria-labelledby="modalKirimSpjLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">PERHATIAN SEBELUM MENGIRIM SPJ</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="ml-2">
                            <p>Sebelum Anda mengirimkan data SPJ Diklat ke Admin Bagian Umum Inspektorat Utama, harap
                                <b>periksa kembali seluruh dokumen dan data yang telah Anda unggah di aplikasi SIMWAS.</b>
                                Pastikan
                                bahwa dokumen yang dikirim sudah lengkap dan sesuai, yaitu:
                            </p>
                            <ul>
                                <li>Surat Tugas</li>
                                <li>SPD</li>
                                <li>Laporan Perjalanan Dinas</li>
                                <li>FPP</li>
                                <li>KAK</li>
                                <li>Surat Pemanggilan Peserta</li>
                                <li>Bukti Penginapan dan/atau Transportasi <i>(jika ada)</i></li>
                            </ul>
                        </div>
                        <p class="text-center">
                        <p>Dengan ini saya menyatakan bahwa:</p>
                        <ul>
                            <li>Semua data dan dokumen yang saya input dalam aplikasi SIMWAS adalah <b>benar dan sah</b>
                            </li>
                            <li>Segala nilai yang muncul dalam aplikasi akan digunakan sebagai dasar pelaporan adalah
                                <b>sesuai dengan dokumen yang telah diunggah</b>
                            </li>
                            <li>Saya bertanggung jawab penuh atas keabsahan data dan dokumen yang dikirim.</li>
                        </ul>
                        </p>
                        <b>Ketik <strong>SETUJU</strong> untuk mengirim SPJ Anda ke BUNTAMA:</b>
                        <input type="text" id="konfirmasiSetujuInput" class="form-control" autocomplete="off">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="button" id="btnModalKirim" class="btn btn-danger" disabled>Kirim</button>
                    </div>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="section-header">
                <h1>Pengajuan SPJ Diklat</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="/pegawai/dashboard">Dashboard</a></div>
                    <div class="breadcrumb-item active"><a href="/pegawai/spj-diklat">SPJ Perjadin Diklat</a>
                    </div>
                    <div class="breadcrumb-item">Formulir Pengajuan</div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            @include('components.flash-error')
                            <h3>{{ $diklat->rencanadiklat->name }}</h3>
                            <h6>{{ $diklat->rencanadiklat->penyelenggara_diklat->penyelenggara }}</h6>
                            <div id="stepper1" class="bs-stepper">
                                <div class="bs-stepper-header" role="tablist">
                                    <div class="step" data-target="#test-l-1">
                                        <button type="button" class="step-trigger" role="tab" id="test-l-1-trigger"
                                            aria-controls="test-l-1">
                                            <span class="bs-stepper-circle"><i class="fa-solid fa-circle-info"></i></span>
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
                                        <button type="button" class="step-trigger" role="tab" id="test-l-3-trigger"
                                            aria-controls="test-l-3">
                                            <span class="bs-stepper-circle"><i class="fa-solid fa-hotel"></i></span>
                                            <span class="bs-stepper-label">Penginapan</span>
                                        </button>
                                    </div>
                                    <div class="line"></div>
                                    <div class="step" data-target="#test-l-4">
                                        <button type="button" class="step-trigger" role="tab" id="test-l-4-trigger"
                                            aria-controls="test-l-4">
                                            <span class="bs-stepper-circle"><i class="fa-solid fa-bus"></i></span>
                                            <span class="bs-stepper-label">Transportasi</span>
                                        </button>
                                    </div>
                                    <div class="line"></div>
                                    <div class="step" data-target="#test-l-5">
                                        <button type="button" class="step-trigger" role="tab" id="test-l-5-trigger"
                                            aria-controls="test-l-5">
                                            <span class="bs-stepper-circle"><i
                                                    class="fa-solid fa-hand-holding-dollar"></i></span>
                                            <span class="bs-stepper-label">Uang Harian</span>
                                        </button>
                                    </div>
                                    <div class="line"></div>
                                    <div class="step" data-target="#test-l-6">
                                        <button type="button" class="step-trigger" role="tab" id="test-l-6-trigger"
                                            aria-controls="test-l-6">
                                            <span class="bs-stepper-circle"><i class="fa-solid fa-book"></i></span>
                                            <span class="bs-stepper-label">Dokumen</span>
                                        </button>
                                    </div>
                                </div>
                                <form id="spj-diklat-update" action="/pegawai/spj-diklat/{{ $diklat->id_spjDiklat }}"
                                    method="POST" class="needs-validation" novalidate enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')
                                    <div class="bs-stepper-content">
                                        <div id="test-l-1" class="content">
                                            @csrf
                                            <input type="hidden" name="id_diklat"
                                                value="{{ $diklat->rencanaDiklat_id }}">
                                            <input type="hidden" name="id_spj" id="id_spj"
                                                value="{{ $diklat->id_spjDiklat }}">
                                            <div class="form-group">
                                                <label for="no_st">Nomor Surat Tugas</label>
                                                <div class="d-flex align-items-center">
                                                    <span class="me-1 mr-2">B-</span>
                                                    <input type="text" class="form-control me-1 mr-2" id="input1"
                                                        style="width: 100px;" value="{{ old('input1', $input1) }}"
                                                        required>
                                                    <span class="me-1"> / </span>
                                                    <input type="text" class="form-control me-1 mr-2 ml-2"
                                                        id="input2" style="width: 100px;"
                                                        value="{{ old('input2', $input2) }}" required>
                                                    <span class="me-1"> / </span>
                                                    <input type="text" class="form-control me-1 mr-2 ml-2"
                                                        id="input3" style="width: 100px;"
                                                        value="{{ old('input3', $input3) }}" required>
                                                    <span class="me-1"> / </span>
                                                    <input type="text" class="form-control me-1 mr-2 ml-2"
                                                        id="input4" style="width: 100px;"
                                                        value="{{ old('input4', $input4) }}" required>
                                                </div>
                                                <input type="hidden" name="no_st" id="no-st-create"
                                                    value="{{ $diklat->no_st }}">
                                                <small for="no_st">Contoh : B-1/0800/PW.000/2025</small>
                                                @error('no_st')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="form-group">
                                                <label for="tgl_mulai_diklat">Tanggal Mulai Penugasan</label>
                                                <input type="date" name="tgl_mulai" id="tgl_mulai_st"
                                                    class="form-control" value="{{ $diklat->tgl_mulai_st }}" required>
                                                <small>Sesuai dengan surat tugas</small>
                                            </div>
                                            <div class="form-group">
                                                <label for="tgl_selesai_diklat">Tanggal Selesai Penugasan</label>
                                                <input type="date" name="tgl_selesai" id="tgl_selesai_st"
                                                    class="form-control" value="{{ $diklat->tgl_selesai_st }}" required>
                                                <small>Sesuai dengan surat tugas</small>
                                            </div>
                                            <div class="form-group">
                                                <label class="form-label" for="file_st">Dokumen Surat Tugas</label>
                                                <div class="">
                                                    @if (isset($diklat->st_path))
                                                        <input type="file" name="file_st" id="file_st"
                                                            class="form-control" accept=".pdf">
                                                        <small id="error-file_st" class="text-danger"></small>
                                                        <small class="text-muted">
                                                            File surat tugas: <a href="{{ asset($diklat->st_path) }}"
                                                                target="_blank">Lihat File</a>
                                                        </small>
                                                        <input type="hidden" name="st_path" id="st_path"
                                                            value="{{ $diklat->st_path }}">
                                                    @else
                                                        <input type="file" id="file_st" class="form-control"
                                                            accept=".pdf" name="file_st">
                                                        <small id="error-file_st" class="text-danger"></small>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                @if ($diklat->status == 'Dikembalikan')
                                                    <button type="button" class="btn btn-danger catatan-button" data-spj-id="{{ $diklat->id_spjDiklat}}">
                                                        <i class="fa-solid fa-info ml-2"></i> Lihat Catatan
                                                    </button>
                                                @endif
                                                <button type="button" class="btn btn-warning preview-button">
                                                    <i class="fa-solid fa-eye ml-2"></i> Lihat Isian
                                                </button>
                                                <button onclick="stepper1.next()" type="button" class="btn btn-primary"
                                                    id="next-form">Selanjutnya
                                                    <i class="fa-solid fa-arrow-right ml-2"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="test-l-2" class="content">
                                        <div id="surat-tugas-wrapper">
                                            <div class="form-group">
                                                <label for="no_spd">Nomor Surat Perjalanan Dinas</label>
                                                <div class="d-flex align-items-center">
                                                    <input type="text" class="form-control me-1 mr-2" id="input_spd1"
                                                        style="width: 100px;"
                                                        value="{{ old('input_spd1', $input1_spd) }}">
                                                    <span class="me-1"> / </span>
                                                    <input type="text" class="form-control me-1 mr-2 ml-2"
                                                        id="input_spd2" style="width: 100px;"
                                                        value="{{ old('input_spd2', $input2_spd) }}">
                                                    <span class="me-1"> / </span>
                                                    <input type="text" class="form-control me-1 mr-2 ml-2"
                                                        id="input_spd3" style="width: 100px;"
                                                        value="{{ old('input_spd3', $input3_spd) }}">
                                                    <span class="me-1"> / </span>
                                                    <input type="text" class="form-control me-1 mr-2 ml-2"
                                                        id="input_spd4" style="width: 100px;"
                                                        value="{{ old('input_spd4', $input4_spd) }}">
                                                    <span class="me-1"> / </span>
                                                    <input type="text" class="form-control me-1 mr-2 ml-2"
                                                        id="input_spd5" style="width: 100px;"
                                                        value="{{ old('input_spd5', $input5_spd) }}">
                                                </div>
                                                <input type="hidden" name="no_spd" id="no-spd-create"
                                                    value="{{ $diklat->no_spd }}">
                                                <small for="no_spd">Contoh :
                                                    085/429332-92000/SPPD-PPIS2910/08/2025</small>
                                                <small id="error-no_spd" class="text-danger"></small>
                                            </div>
                                            <div class="form-group">
                                                <label for="tgl_spd">Tanggal SPD</label>
                                                <input type="date" name="tgl_spd" id="tgl_spd_create"
                                                    class="form-control" value="{{ $diklat->tgl_spd }}">
                                            </div>
                                            <div class="form-group">
                                                <label class="form-label" for="file_spd">Dokumen Surat Perjalanan
                                                    Dinas</label>
                                                <div class="">
                                                    @if (isset($diklat->spd_path))
                                                        <input type="file" name="file_spd" id="file_spd"
                                                            class="form-control" accept=".pdf">
                                                        <small id="error-file-spd" class="text-danger"></small>
                                                        <small class="text-muted">
                                                            File spd: <a href="{{ asset($diklat->spd_path) }}"
                                                                target="_blank">Lihat File</a>
                                                        </small>
                                                        <input type="hidden" id="spd_path"
                                                            value="{{ $diklat->spd_path }}">
                                                    @else
                                                        <input type="file" name="file_spd" id="file_spd"
                                                            class="form-control" accept=".pdf">
                                                        <small id="error-file_spd" class="text-danger"></small>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            @if ($diklat->status == 'Dikembalikan')
                                                <button type="button" class="btn btn-danger catatan-button" data-spj-id="{{ $diklat->id_spjDiklat}}">
                                                    <i class="fa-solid fa-info ml-2"></i> Lihat Catatan
                                                </button>
                                            @endif
                                            <button type="button" class="btn btn-warning preview-button">
                                                <i class="fa-solid fa-eye ml-2"></i> Lihat Isian
                                            </button>
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
                                        <div class="form-group">
                                            <label>Apakah ada invoice hotel?</label>
                                            <div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="ada_hotel"
                                                        id="hotel_ya" value="ya"
                                                        {{ $diklat->nominal_hotel > 0 ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="hotel_ya">Ya</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="ada_hotel"
                                                        id="hotel_tidak" value="tidak"
                                                        {{ $diklat->nominal_hotel == 0 ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="hotel_tidak">Tidak</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div id="form-invoice-hotel" style="display: none;">
                                            <div class="form-group">
                                                <label for="nominal_hotel">Total Nominal Biaya</label>
                                                <input type="number" id="nominal_hotel_create" name="nominal_hotel"
                                                    class="form-control" value="{{ $diklat->nominal_hotel }}">
                                            </div>
                                            <div class="form-group">
                                                <label class="form-label" for="file_spd">Dokumen Invoice
                                                    Penginapan</label>
                                                <div class="">
                                                    @if (isset($diklat->hotel_path))
                                                        <input type="file" name="file_hotel" id="file_hotel"
                                                            class="form-control" accept=".pdf">
                                                        <small id="error-file_hotel" class="text-danger"></small>
                                                        <small class="text-muted">
                                                            File invoice hotel: <a href="{{ asset($diklat->hotel_path) }}"
                                                                target="_blank">Lihat File</a>
                                                        </small>
                                                        <input type="hidden" name="hotel_path" id="hotel_path"
                                                            value="{{ $diklat->hotel_path }}">
                                                    @else
                                                        <input type="file" name="file_hotel" id="file_hotel"
                                                            class="form-control" accept=".pdf">
                                                        <small id="error-file_hotel" class="text-danger"></small>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            @if ($diklat->status == 'Dikembalikan')
                                                <button type="button" class="btn btn-danger catatan-button" data-spj-id="{{ $diklat->id_spjDiklat}}">
                                                    <i class="fa-solid fa-info ml-2"></i> Lihat Catatan
                                                </button>
                                            @endif
                                            <button type="button" class="btn btn-warning preview-button">
                                                <i class="fa-solid fa-eye ml-2"></i> Lihat Isian
                                            </button>
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
                                        <div class="form-group">
                                            <label>Tipe perjalanan dinas?</label>
                                            <div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="tipe_perjadin"
                                                        id="dalkot" value="1"
                                                        {{ $diklat->tipe_perjadin == 1 ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="dalkot">Dalam Kota</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="tipe_perjadin"
                                                        id="lukot" value="2"
                                                        {{ $diklat->tipe_perjadin == 2 ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="lukot">Luar Kota</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div id="trans-dalam-kota" style="display: none;">
                                            <h5>Transport Lokal</h5>
                                            <div class="line mb-3"></div>
                                            <div class="form-group">
                                                <label for="jumlah_hari_transport">Jumlah Hari</label>
                                                <input type="number" id="jumlah_hari_transport"
                                                    name="jumlah_hari_transport" class="form-control" min="1"
                                                    value="{{ $hari_translok }}">
                                            </div>
                                            <div class="form-group">
                                                <label class="form-label" for="file_trans_dalkot">Dokumen
                                                    Transport Lokal</label>
                                                <div class="">
                                                    @if (isset($diklat->translok_path) && $diklat->tipe_perjadin == 1)
                                                        <input type="file" name="file_trans_dalkot"
                                                            id="file_trans_dalkot" class="form-control"
                                                            accept=".pdf">
                                                        <small id="error-file-trans-berangkat"
                                                            class="text-danger"></small>
                                                        <small class="text-muted">
                                                            File transport berangkat: <a
                                                                href="{{ asset($diklat->translok_path) }}"
                                                                target="_blank">Lihat File</a>
                                                        </small>
                                                        <input type="hidden" id="file_trans_dalkot_path"
                                                            value="{{ $diklat->translok_path }}">
                                                    @else
                                                        <input type="file" name="file_trans_dalkot"
                                                            id="file_trans_dalkot" class="form-control"
                                                            accept=".pdf">
                                                        <small>Daftar Pengeluaran Riil</small>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div id="kat-luar-kota" style="display: none;">
                                            <div class="form-group">
                                                <label>Pilihan Moda Transportasi</label>
                                                <div>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio" name="kat_lukot"
                                                            id="pribadi" value="1"
                                                            {{ $diklat->jarak !== null && $diklat->tipe_perjadin == 2 ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="pribadi">Kendaraan
                                                            Pribadi</label>
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio" name="kat_lukot"
                                                            id="umum" value="2"
                                                            {{ $diklat->jarak == null && $diklat->tipe_perjadin == 2 ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="umum">Kendaraan
                                                            Umum</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div id="trans-luar-kota" style="display: none;">
                                                <div class="form-group" id="jarak-berangkat">
                                                    <label for="km_berangkat">Jarak Rumah ke Tempat Diklat (km)</label>
                                                    <input type="number" name="km_berangkat" id="km_berangkat_create"
                                                        class="form-control" value="{{ $diklat->jarak }}">
                                                    <small>Apabila pulang-pergi(PP) menggunakan kendaraan pribadi maka jarak
                                                        diisi (2 x jarak)</small>
                                                </div>
                                                <h5>Transportasi Berangkat</h5>
                                                <div class="line mb-3"></div>
                                                <div class="form-group">
                                                    <label for="nominal_trans_lukot_berangkat">Nominal Transportasi
                                                        Berangkat</label>
                                                    <input type="number" name="nominal_trans_lukot_berangkat"
                                                        id="nominal_trans_lukot_berangkat_create" class="form-control"
                                                        value="{{ $diklat->nominal_transport_berangkat ?? 0}}">
                                                </div>
                                                <div class="form-group">
                                                    <label for="tgl_trans_lukot_berangkat">Tanggal Transportasi
                                                        Berangkat</label>
                                                    <input type="date" name="tgl_trans_lukot_berangkat"
                                                        id="tgl_trans_lukot_berangkat_create" class="form-control"
                                                        value="{{ $diklat->tgl_transport_berangkat }}">
                                                </div>
                                                <div class="form-group">
                                                    <label class="form-label" for="file_trans_lukot_berangkat">Dokumen
                                                        Transportasi
                                                        Berangkat</label>
                                                    <div class="">
                                                        @if (isset($diklat->transport_berangkat_path) && $diklat->tipe_perjadin == 2)
                                                            <input type="file" name="file_trans_lukot_berangkat"
                                                                id="file_trans_lukot_berangkat" class="form-control"
                                                                accept=".pdf">
                                                            <small id="catatan-pribadi" style="display: none;"><i>Cth:
                                                                    Screenshot gmaps, struk bensin, struk tol</i></small>
                                                            <small id="error-file-trans-berangkat"
                                                                class="text-danger"></small>
                                                            <small class="text-muted">
                                                                File transport berangkat: <a
                                                                    href="{{ asset($diklat->transport_berangkat_path) }}"
                                                                    target="_blank">Lihat File</a>
                                                            </small>
                                                            <input type="hidden" id="file_trans_lukot_berangkat_path"
                                                                value="{{ $diklat->transport_berangkat_path }}">
                                                        @else
                                                            <input type="file" name="file_trans_lukot_berangkat"
                                                                id="file_trans_lukot_berangkat" class="form-control"
                                                                accept=".pdf">
                                                            <small id="catatan-pribadi" style="display: none;"><i>Cth:
                                                                    Screenshot gmaps, struk bensin, struk tol</i></small>
                                                            <small id="error-file-trans-berangkat"
                                                                class="text-danger"></small>
                                                        @endif
                                                    </div>
                                                </div>
                                                <h5 class="mt-5">Transportasi Pulang</h5>
                                                <div class="line mb-3"></div>
                                                <div class="form-group">
                                                    <label for="nominal_trans_lukot_pulang">Nominal Transportasi
                                                        Pulang</label>
                                                    <input type="number" name="nominal_trans_lukot_pulang"
                                                        id="nominal_trans_lukot_pulang_create" class="form-control"
                                                        value="{{ $diklat->nominal_transport_pulang ?? 0}}">
                                                </div>
                                                <div class="form-group">
                                                    <label for="tgl_trans_lukot_pulang">Tanggal Transportasi Pulang</label>
                                                    <input type="date" name="tgl_trans_lukot_pulang"
                                                        id="tgl_trans_lukot_pulang_create" class="form-control"
                                                        value="{{ $diklat->tgl_transport_pulang }}">
                                                </div>
                                                <div class="form-group">
                                                    <label class="form-label" for="file_trans_lukot_pulang">Dokumen
                                                        Transportasi
                                                        Pulang</label>
                                                    <div class="">
                                                        @if (isset($diklat->transport_pulang_path) && $diklat->tipe_perjadin == 2)
                                                            <input type="file" name="file_trans_lukot_pulang"
                                                                id="file_trans_lukot_pulang" class="form-control"
                                                                accept=".pdf">
                                                            <small id="catatan-pribadi2" style="display: none;"><i>Cth:
                                                                    Screenshot gmaps, struk bensin, struk tol</i></small>
                                                            <small id="error-file-trans-pulang"
                                                                class="text-danger"></small>
                                                            <small class="text-muted">
                                                                File transport pulang: <a
                                                                    href="{{ asset($diklat->transport_pulang_path) }}"
                                                                    target="_blank">Lihat File</a>
                                                            </small>
                                                            <input type="hidden" id="file_trans_lukot_pulang_path"
                                                                value="{{ $diklat->transport_pulang_path }}">
                                                        @else
                                                            <input type="file" name="file_trans_lukot_pulang"
                                                                id="file_trans_lukot_pulang" class="form-control"
                                                                accept=".pdf">
                                                            <small id="catatan-pribadi2" style="display: none;"><i>Cth:
                                                                    Screenshot gmaps, struk bensin, struk tol</i></small>
                                                            <small id="error-file-trans-pulang"
                                                                class="text-danger"></small>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            @if ($diklat->status == 'Dikembalikan')
                                                <button type="button" class="btn btn-danger catatan-button" data-spj-id="{{ $diklat->id_spjDiklat}}">
                                                    <i class="fa-solid fa-info ml-2"></i> Lihat Catatan
                                                </button>
                                            @endif
                                            <button type="button" class="btn btn-warning preview-button">
                                                <i class="fa-solid fa-eye ml-2"></i> Lihat Isian
                                            </button>
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
                                        <div class="form-group">
                                            <label for="jumlah_hari">Jumlah Hari Diklat</label>
                                            <input type="number" id="jumlah_hari" name="jumlah_hari"
                                                class="form-control" min="1" value="{{ $diklat->hari_diklat }}">
                                        </div>

                                        <div class="form-group">
                                            <label for="uh_diklat">Uang Harian Diklat (per hari)</label>
                                            <input type="number" name="uh_diklat" id="uh_diklat_create"
                                                class="form-control" min="0" value="{{ $diklat->uang_diklat }}">
                                        </div>

                                        <div class="form-group">
                                            <label for="total_uh">Total Uang Harian Diklat</label>
                                            <input type="text" id="total_uh" class="form-control" readonly>

                                        </div>

                                        <div class="form-group">
                                            <label>Apakah mengajukan uang harian H-1 diklat?</label>
                                            <div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="ada_berangkat"
                                                        id="berangkat_ya" value="ya"
                                                        {{ $diklat->uang_harian_berangkat > 0 ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="berangkat_ya">Ya</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="ada_berangkat"
                                                        id="berangkat_tidak" value="tidak"
                                                        {{ $diklat->uang_harian_berangkat == 0 ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="berangkat_tidak">Tidak</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group" id="form-harian-berangkat" style="display: none;">
                                            <label for="uh_h_1">Nominal Uang Harian H-1</label>
                                            <input type="number" name="uh_h_1" id="uh_h_1_create" class="form-control"
                                                value="{{ $diklat->uang_harian_berangkat }}">
                                        </div>
                                        <div class="form-group">
                                            <label>Apakah mengajukan uang harian H+1 diklat?</label>
                                            <div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="ada_pulang"
                                                        id="pulang_ya" value="ya"
                                                        {{ $diklat->uang_harian_pulang > 0 ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="pulang_ya">Ya</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="ada_pulang"
                                                        id="pulang_tidak" value="tidak"
                                                        {{ $diklat->uang_harian_pulang == 0 ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="pulang_tidak">Tidak</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group" id="form-harian-pulang" style="display: none;">
                                            <label for="uh_h1">Nominal Uang Harian H+1</label>
                                            <input type="number" name="uh_h1" id="uh_h1_create" class="form-control"
                                                value="{{ $diklat->uang_harian_pulang }}">
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label" for="rekening">Nomor Rekening</label>
                                            <div class="">
                                                <select class="form-control" name="no_rek" id="no_rek">
                                                    <option value="" disabled selected>Pilih Rekening</option>
                                                    @foreach ($rekening as $r)
                                                        <option value="{{ $r->id_rekening }}"
                                                            {{ old('no_rek', $diklat->rekening_id ?? '') == $r->id_rekening ? 'selected' : '' }}>
                                                            {{ $r->nama_bank }} -
                                                            {{ $r->no_rekening }}</option>
                                                    @endforeach
                                                    <option value="tambah">+ Tambah Rekening Baru</option>
                                                </select>
                                                <small id="error-no_rek" class="text-danger"></small>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            @if ($diklat->status == 'Dikembalikan')
                                                <button type="button" class="btn btn-danger catatan-button" data-spj-id="{{ $diklat->id_spjDiklat}}">
                                                    <i class="fa-solid fa-info ml-2"></i> Lihat Catatan
                                                </button>
                                            @endif
                                            <button type="button" class="btn btn-warning preview-button">
                                                <i class="fa-solid fa-eye ml-2"></i> Lihat Isian
                                            </button>
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
                                        <div class="form-group">
                                            <label class="form-label" for="file_laporan_perjadin">Dokumen
                                                Laporan Perjalanan Dinas</label>
                                            <div class="">
                                                @if (isset($diklat->laporan_perjadin_path))
                                                    <input type="file" name="file_laporan_perjadin"
                                                        id="file_laporan_perjadin" class="form-control" accept=".pdf">
                                                    <small id="error-file-laporan-perjadin" class="text-danger"></small>
                                                    <small class="text-muted">
                                                        File laporan perjadin: <a
                                                            href="{{ asset($diklat->laporan_perjadin_path) }}"
                                                            target="_blank">Lihat File</a>
                                                    </small>
                                                    <input type="hidden" id="laporan_perjadin_path"
                                                        value="{{ $diklat->laporan_perjadin_path }}">
                                                @else
                                                    <input type="file" name="file_laporan_perjadin"
                                                        id="file_laporan_perjadin" class="form-control" accept=".pdf">
                                                    <small id="error-file-laporan-perjadin" class="text-danger"></small>
                                                @endif
                                                <small id="error-file_laporan_perjadin" class="text-danger"></small>

                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label" for="file_fpp">Dokumen
                                                Form Permintaan Pembayaran (FPP)</label>
                                            <div class="">
                                                @if (isset($diklat->fpp_path))
                                                    <input type="file" name="file_fpp" id="file_fpp"
                                                        class="form-control" accept=".pdf">
                                                    <small id="error-file-fpp" class="text-danger"></small>
                                                    <small class="text-muted">
                                                        File FPP: <a href="{{ asset($diklat->fpp_path) }}"
                                                            target="_blank">Lihat File</a>
                                                    </small>
                                                    <input type="hidden" id="fpp_path"
                                                        value="{{ $diklat->fpp_path }}">
                                                @else
                                                    <input type="file" name="file_fpp" id="file_fpp"
                                                        class="form-control" accept=".pdf">
                                                    <small id="error-file-fpp" class="text-danger"></small>
                                                @endif
                                                <small id="error-file_fpp" class="text-danger"></small>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label" for="file_kak">Dokumen KAK</label>
                                            <div class="">
                                                @if (isset($diklat->kak_path))
                                                    <input type="file" name="file_kak" id="file_kak"
                                                        class="form-control" accept=".pdf">
                                                    <small id="error-file-kak" class="text-danger"></small>
                                                    <small class="text-muted">
                                                        File KAK: <a href="{{ asset($diklat->kak_path) }}"
                                                            target="_blank">Lihat File</a>
                                                    </small>
                                                    <input type="hidden" id="kak_path"
                                                        value="{{ $diklat->kak_path }}">
                                                @else
                                                    <input type="file" name="file_kak" id="file_kak"
                                                        class="form-control" accept=".pdf">
                                                    <small id="error-file-kak" class="text-danger"></small>
                                                @endif
                                                <small id="error-file_kak" class="text-danger"></small>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label" for="file_pemanggilan">Dokumen Surat Pemanggilan
                                                Diklat</label>
                                            <div class="">
                                                @if (isset($diklat->surat_pemanggilan_path))
                                                    <input type="file" name="file_pemanggilan" id="file_pemanggilan"
                                                        class="form-control" accept=".pdf">
                                                    <small id="error-file-pemanggilan" class="text-danger"></small>
                                                    <small class="text-muted">
                                                        File Surat Pemanggilan: <a
                                                            href="{{ asset($diklat->surat_pemanggilan_path) }}"
                                                            target="_blank">Lihat File</a>
                                                    </small>
                                                    <input type="hidden" id="surat_pemanggilan_path"
                                                        value="{{ $diklat->surat_pemanggilan_path }}">
                                                @else
                                                    <input type="file" name="file_pemanggilan" id="file_pemanggilan"
                                                        class="form-control" accept=".pdf">
                                                    <small id="error-file-pemanggilan" class="text-danger"></small>
                                                @endif
                                                <small id="error-file_pemanggilan" class="text-danger"></small>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <button type="button" class=" btn btn-outline-primary"
                                                onclick="stepper1.previous()">
                                                <i class="fa-solid fa-arrow-left mr-2"></i>Sebelumnya</button>
                                            @if ($diklat->status == 'Dikembalikan')
                                                <button type="button" class="btn btn-danger catatan-button" data-spj-id="{{ $diklat->id_spjDiklat}}">
                                                    <i class="fa-solid fa-info ml-2"></i> Lihat Catatan
                                                </button>
                                            @endif
                                            <button type="button" class="btn btn-warning preview-button">
                                                <i class="fa-solid fa-eye ml-2"></i> Lihat Isian
                                            </button>
                                            <button type="button" class="btn btn-danger" id="kirimFormBtn"><i
                                                    class="fa-regular fa-paper-plane"></i> Kirim</button>
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
    <script src="{{ asset('js') }}/page/pegawai/spj-diklat.js?v=1.0.3"></script>
    <!-- Page Specific JS File -->
@endpush
