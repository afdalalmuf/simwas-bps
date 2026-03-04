@extends('layouts.app')

@section('title', 'Kelola Kompetensi Pegawai')

@push('style')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- CSS Libraries -->
    <link
        href="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.13.4/af-2.5.3/b-2.3.6/b-colvis-2.3.6/b-html5-2.3.6/b-print-2.3.6/cr-1.6.2/date-1.4.1/fc-4.2.2/fh-3.3.2/kt-2.9.0/r-2.4.1/rg-1.3.1/rr-1.3.3/sc-2.1.1/sb-1.4.2/sp-2.1.2/sl-1.6.2/sr-1.2.2/datatables.min.css"
        rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('library') }}/sweetalert2/dist/sweetalert2.min.css">
@endpush

@section('main')
    @include('components.analis-sdm-header')
    @include('components.analis-sdm-sidebar')

    <div class="main-content">
        <!-- Modal -->
        <div class="modal fade" id="staticBackdrop" data-backdrop="static" data-keyboard="false" tabindex="-1"
            aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="staticBackdropLabel">Tolak Kompetensi</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form>
                        <div class="modal-body">
                            @csrf
                            <input type="hidden" name="data-id" id="data-id">
                            <div class="form-group">
                                <label for="draft">Alasan Penolakan</label>
                                <input placeholder="Berikan Alasan Penolakan" required type="text" class="form-control"
                                    name="catatan" id="catatan">
                                <small id="error-catatan" class="text-danger"></small>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-icon icon-left btn-danger" data-dismiss="modal">
                                <i class="fas fa-exclamation-triangle"></i>Batal
                            </button>
                            <button type="submit" class="btn btn-icon icon-left btn-primary submit-btn" id="tolak-submit">
                                <i class="fas fa-save"></i>Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @include('components.kelola-kompetensi.create');
        @include('components.kelola-kompetensi.edit');
        <section class="section">
            <div class="section-header">
                <h1>Kelola Kompetensi Pegawai</h1>
            </div>
            <div class="row">
                <div class=" col-md-12">
                    <div class="card">
                        <div class="card-body">
                            @include('components.flash')
                            {{ session()->forget(['alert-type', 'status']) }}
                            <form action="" name="filterForm" id="filterForm">
                                <div class="d-flex mb-2 row" style="gap:10px">
                                    <div class="form-group col pr-0" style="margin-bottom: 0;">
                                        <label for="filterUnitKerja" style="margin-bottom: 0;">Unit Kerja</label>
                                        <select class="form-control select2" id="filterUnitKerja" autocomplete="off">
                                            <option value="all">Semua</option>
                                            <option value="8000">Inspektorat Utama</option>
                                            <option value="8010">Bagian Umum Inspektorat Utama</option>
                                            <option value="8100">Inspektorat Wilayah I</option>
                                            <option value="8200">Inspektorat Wilayah II</option>
                                            <option value="8300">Inspektorat Wilayah III</option>
                                        </select>
                                    </div>
                                    <div class="form-group col pl-0" style="margin-bottom: 0;">
                                        <label for="filterKat" style="margin-bottom: 0;">Kategori Kompetensi</label>
                                        <select class="form-control select2" id="filterKat" name="filterKat">
                                            <option value="all">Semua</option>
                                            @foreach ($kategori as $k)
                                                <option value="{{ $k->id }}">{{ $k->nama }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </form>
                            <div class="d-flex">
                                <div class="d-flex justify-content-end my-2">
                                    <button type="button" id="create-btn" class="btn btn-primary mr-2" data-toggle="modal"
                                        data-target="#modal-create-kompetensi">
                                        <i class="fas fa-plus-circle"></i>
                                        Tambah
                                    </button>
                                    <div id="exportWrapper"></div>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table id="table-kompetensi" class="table table-bordered w-100" style="table-layout: auto;">
                                    <thead>
                                        <tr>
                                            <th>Pegawai</th>
                                            <th>Kategori</th>
                                            <th>Jenis</th>
                                            <th>Teknis</th>
                                            <th>Pelatihan</th>
                                            <th>Mulai</th>
                                            <th>Sertifikat</th>
                                            <th>Status</th>
                                            <th>Tgl Disetujui</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
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

    <!-- Page Specific JS File -->
    <script src="{{ asset('js/page/kompetensi.js') }}"></script>
@endpush
