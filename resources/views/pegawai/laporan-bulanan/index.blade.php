@extends('layouts.app')

@section('title', 'Laporan Bulanan')

@push('style')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- CSS Libraries -->
    <link
        href="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.13.4/af-2.5.3/b-2.3.6/b-colvis-2.3.6/b-html5-2.3.6/b-print-2.3.6/cr-1.6.2/date-1.4.1/fc-4.2.2/fh-3.3.2/kt-2.9.0/r-2.4.1/rg-1.3.1/rr-1.3.3/sc-2.1.1/sb-1.4.2/sp-2.1.2/sl-1.6.2/sr-1.2.2/datatables.min.css"
        rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('library') }}/sweetalert2/dist/sweetalert2.min.css">
    <style>
        #table-laporan {
            width: 100% !important;
        }
    </style>
@endpush

@section('main')
    @include('components.header')
    @include('components.pegawai-sidebar')
    @include('pegawai.laporan-bulanan.activity-modal');
    @include('pegawai.laporan-bulanan.mapping-modal');

    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Laporan Bulanan</h1>
            </div>
            <div class="row">
                <div class=" col-md-12">
                    <div class="card">
                        <div class="card-body">
                            @include('components.flash')
                            {{ session()->forget(['alert-type', 'status']) }}
                            <div class="d-flex mb-2 row" style="gap:10px">
                                <div class="form-group col pr-0" style="margin-bottom: 0;">
                                    <label for="filterBulan" style="margin-bottom: 0;">Bulan Pelaporan</label>
                                    <select class="form-control select2" id="filterBulan" name="filterBulan">
                                        <option value="all" {{ $selectedMonth == 'all' ? 'selected' : '' }}>Semua Bulan
                                        </option>
                                        @foreach ([1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'] as $num => $label)
                                            <option value="{{ $num }}"
                                                {{ (int) $selectedMonth === $num ? 'selected' : '' }}>{{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col pl-0" style="margin-bottom: 0;">
                                    <label for="filterTahun" style="margin-bottom: 0;">Tahun</label>
                                    <select class="form-control select2" id="filterTahun" name="filterTahun">
                                        <?php $currentYear = date('Y'); ?>
                                        @for ($i = $currentYear - 3; $i <= $currentYear; $i++)
                                            <option value="{{ $i }}" {{ $i == $currentYear ? 'selected' : '' }}>
                                                {{ $i }}
                                            </option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                            <div class="d-flex">

                            </div>

                            <div class="table-responsive mt-4">
                                <table class="table table-striped" id="table-laporan">
                                    <thead>
                                        <tr>
                                            <th>Proyek</th>
                                            <th>Tugas</th>
                                            <th>Kegiatan</th>
                                            <th>Capaian</th>
                                            <th>Target (jam)</th>
                                            <th>Realisasi (jam)</th>
                                            <th>Total Aktivitas</th>
                                            <th>Status</th> <!-- NEW -->
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($tugasSaya as $ts)
                                            <tr>
                                                <td>{{ $ts->rencanaKerja->proyek->nama_proyek ?? '-' }}</td>
                                                <td>{{ $ts->rencanaKerja->tugas ?? '-' }}</td>
                                                <td>{{ $ts->matched_kinerja->kegiatan ?? '-' }}</td>
                                                <td>{{ $ts->matched_kinerja->capaian ?? '-' }}</td>
                                                <td>{{ number_format($ts->target_hours, 2) }}</td>
                                                <td>{{ number_format($ts->activity_hours, 2) }}</td>
                                                <td>
                                                    <button class="btn btn-link p-0 activity-detail"
                                                        data-rencana="{{ $ts->id_rencanakerja }}"
                                                        data-target="{{ $ts->target_hours }}"
                                                        data-month="{{ $selectedMonth }}" data-year="{{ $selectedYear }}">
                                                        {{ $ts->total_activity }}
                                                    </button>
                                                </td>
                                                <td></td>
                                                <td>
                                                    <button class="btn btn-sm btn-outline-primary btn-mapping-form"
                                                        data-aktivitas = "{{ $ts->total_activity }}"
                                                        data-rencana="{{ $ts->id_rencanakerja }}"
                                                        data-kegiatan="{{ $ts->matched_kinerja->kegiatan }}"
                                                        data-capaian="{{ $ts->matched_kinerja->capaian }}"
                                                        data-pelaksana="{{ $ts->id_pelaksana }}" data-toggle="modal"
                                                        data-target="#mappingFormModal"
                                                        @if ($ts->total_activity == 0) disabled @endif>
                                                        <i class="fas fa-random"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-end">
                                @php
                                    $hasSynced = $tugasSaya->contains(function ($item) {
                                        return $item->status === 'sinkron';
                                    });
                                @endphp

                                <button class="btn btn-success" id="syncAllBtn"
                                    @if ($hasSynced) disabled @endif
                                    title="{{ $hasSynced ? 'Terdapat data yang sudah sinkron' : '' }}">
                                    <i class="fas fa-sync-alt"></i> Sinkronkan Semua
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <div class="modal fade" id="confirmSyncModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Konfirmasi Sinkronisasi</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p id="syncConfirmText">Anda akan menyinkronkan <strong>0 aktivitas</strong>.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-success" id="confirmSyncBtn">Konfirmasi &amp; Sinkronkan</button>
                </div>
            </div>
        </div>
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
    <script>
        const checkSyncUrl = "{{ route('pegawai.kipappsyncs.check') }}";
    </script>

    <!-- Page Specific JS File -->
    <script src="{{ asset('js/page/pegawai/laporan-bulanan.js') }}"></script>
@endpush
