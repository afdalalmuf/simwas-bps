@extends('layouts.app')

@section('title', 'Detail SKP')

@push('style')
    <!-- CSS Libraries -->
    <link rel="stylesheet" href="{{ asset('library/jqvmap/dist/jqvmap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('library/summernote/dist/summernote-bs4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('library/datatables/media/css/jquery.dataTables.min.css') }}">
@endpush

@section('main')
    @php
        $rating = [
            'A' => 'Diatas Ekspektasi',
            'B' => 'Sesuai Ekspektasi',
            'C' => 'Dibawah Ekspektasi',
        ];

        $kategori = [
            'A' => 'Sangat Baik',
            'B' => 'Baik',
            'C' => 'Butuh Perbaikan',
            'D' => 'Kurang',
            'E' => 'Sangat Kurang',
        ];

        $bulan = [
            '01' => 'Januari',
            '02' => 'Februari',
            '03' => 'Maret',
            '04' => 'April',
            '05' => 'Mei',
            '06' => 'Juni',
            '07' => 'Juli',
            '08' => 'Agustus',
            '09' => 'September',
            '10' => 'Oktober',
            '11' => 'November',
            '12' => 'Desember',
        ];
    @endphp
    {{-- Modal --}}
    @include('components.bpkp-header')
    @include('components.bpkp-sidebar')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Detail Unggah SKP</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="/bpkp">Dasbor</a></div>
                    <div class="breadcrumb-item">Detail</div>
                </div>
            </div>

            @if (session()->has('success'))
                <div class="alert alert-success" role="alert">
                    {{ session('success') }}
                </div>
            @endif

            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row mb-0 pb-0">
                                <div class="col-md-4">
                                    <a class="btn btn-outline-primary" href="/bpkp">
                                        <i class="fas fa-arrow-left mr-1"></i> Kembali
                                    </a>
                                </div>
                            </div>
                            <h1 class="h4 text-dark mb-4 header-card" style="margin-top: 50px">Dokumen SKP
                                {{ ucwords($skp->jenis) }}</h1>
                            <div class="form-group">
                                <div class="row">
                                    @if ($skp->bulan == null)
                                        <div class="col-lg-6">
                                            <label for="rating_hasil_kerja">Jenis</label>
                                            <input type="text" class="form-control" value="{{ ucwords($skp->jenis) }}"
                                                readonly>
                                        </div>
                                    @else
                                        <div class="col-lg-6">
                                            <label for="rating_hasil_kerja">Bulan</label>
                                            <input type="text" class="form-control" value="{{ $bulan[$skp->bulan] }}"
                                                readonly>
                                        </div>
                                    @endif
                                    <div class="col-lg-6">
                                        <label for="rating_perilaku_kerja">Tahun</label>
                                        <input type="text" class="form-control" value="{{ $skp->tahun }}" readonly>
                                    </div>
                                </div>
                            </div>
                            {{-- PDF Viewer --}}
                            <div class="form-group">
                                <label for="preview">Tampilan Dokumen </label>
                                <iframe src="/{{ $skp->skp_path }}" width="100%" height="500px" frameborder="0"></iframe>
                            </div>
                            @if ($skp->bulan !== null)                            
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-lg-4">
                                            <label for="rating_hasil_kerja">Rating Hasil Kerja:</label>
                                            <input type="text" class="form-control"
                                                value="{{ $rating[$skp->kat_rating_hasil_kerja] }}" readonly>
                                        </div>
                                        <div class="col-lg-4">
                                            <label for="rating_perilaku_kerja">Rating Perilaku Kerja:</label>
                                            <input type="text" class="form-control"
                                                value="{{ $rating[$skp->kat_rating_perilaku_kerja] }}" readonly>
                                        </div>
                                        <div class="col-lg-4">
                                            <label for="predikat_kinerja">Predikat:</label>
                                            <input type="text" class="form-control"
                                                value="{{ $kategori[$skp->predikat_kinerja] }}" readonly>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    <!-- JS Libraies -->
    {{-- <script src="assets/modules/datatables/DataTables-1.10.16/js/dataTables.bootstrap4.min.js"></script>
    <script src="assets/modules/datatables/Select-1.2.4/js/dataTables.select.min.js"></script> --}}
    <script src="{{ asset('library/datatables/media/js/jquery.dataTables.min.js') }}"></script>
    {{-- <script src="{{ asset() }}"></script> --}}
    {{-- <script src="{{ asset() }}"></script> --}}
    <script src="{{ asset('library/jquery-ui-dist/jquery-ui.min.js') }}"></script>

    <!-- Page Specific JS File -->
    <script src="{{ asset('js/page/modules-datatables.js') }}"></script>
    <script>
        $('#status').on('change', function() {
            if ($(this).val() === 'ditolak') {
                $('#catatan').prop('required', true);
            } else {
                $('#catatan').prop('required', false);
            }
        });
    </script>
@endpush
