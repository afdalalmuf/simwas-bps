@extends('layouts.app')

@section('title', 'Inspektur')

@push('style')
    <!-- CSS Libraries -->
    <link rel="stylesheet" href="{{ asset('library/jqvmap/dist/jqvmap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('library/summernote/dist/summernote-bs4.min.css') }}">
@endpush

@section('main')
    @include('components.inspektur-header')
    @include('components.inspektur-sidebar')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Inspektur Dashboard</h1>
            </div>
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-12">
                    <h2 class="font-weight-normal text-dark h5 mb-3">Dashboard Upload SKP</h2>
                    <div class="row">
                        <div class="col-lg-4 col-md-4 col-sm-4 col-4">
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
                        <div class="col-6">
                            <div class="card">
                                <div class="card-header">
                                    @if (auth()->user()->unit_kerja == '8010')
                                        <h4>Persentase Dokumen SKP Bulan Berjalan Inspektorat Utama</h4>
                                    @else
                                        <h4>Persentase Dokumen SKP Bulan Berjalan {{ $unit }}</h4>
                                    @endif
                                </div>
                                <div class="card-body" style="padding-top: 5px;">
                                    <div class="d-flex justify-content-between">
                                        <div class="d-flex flex-row align-items-center">
                                        </div>
                                        <a href="/inspektur/cek-skp" class="arrow-button-card" type="button"
                                            class="rounded-circle"><i class="fa-solid fa-arrow-right"></i></a>
                                    </div>
                                    <canvas id="skpChart" height="200px"></canvas>
                                </div>
                            </div>
                        </div>
                        @if (auth()->user()->unit_kerja == '8100' || auth()->user()->unit_kerja == '8010' || auth()->user()->unit_kerja == '8000')
                            <div class="col-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h4>Persentase Dokumen SKP per Bulan Inspektorat Wilayah 1</h4>
                                    </div>
                                    <div class="card-body" style="padding-top: 5px;">
                                        <div class="d-flex justify-content-between">
                                            <div class="d-flex flex-row align-items-center">
                                            </div>
                                            <a href="/inspektur/cek-skp" class="arrow-button-card" type="button"
                                                class="rounded-circle"><i class="fa-solid fa-arrow-right"></i></a>
                                        </div>
                                        <canvas id="skpIrwil1Chart" height="200px"></canvas>
                                    </div>
                                </div>
                            </div>
                        @endif
                        @if (auth()->user()->unit_kerja == '8200' || auth()->user()->unit_kerja == '8010' || auth()->user()->unit_kerja == '8000')
                            <div class="col-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h4>Persentase Dokumen SKP per Bulan Inspektorat Wilayah 2</h4>
                                    </div>
                                    <div class="card-body" style="padding-top: 5px;">
                                        <div class="d-flex justify-content-between">
                                            <div class="d-flex flex-row align-items-center">
                                            </div>
                                            <a href="/inspektur/cek-skp" class="arrow-button-card" type="button"
                                                class="rounded-circle"><i class="fa-solid fa-arrow-right"></i></a>
                                        </div>
                                        <canvas id="skpIrwil2Chart" height="200px"></canvas>
                                    </div>
                                </div>
                            </div>
                        @endif
                        @if (auth()->user()->unit_kerja == '8300' || auth()->user()->unit_kerja == '8010' || auth()->user()->unit_kerja == '8000')
                            <div class="col-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h4>Persentase Dokumen SKP per Bulan Inspektorat Wilayah 3</h4>
                                    </div>
                                    <div class="card-body" style="padding-top: 5px;">
                                        <div class="d-flex justify-content-between">
                                            <div class="d-flex flex-row align-items-center">
                                            </div>
                                            <a href="/inspektur/cek-skp" class="arrow-button-card" type="button"
                                                class="rounded-circle"><i class="fa-solid fa-arrow-right"></i></a>
                                        </div>
                                        <canvas id="skpIrwil3Chart" height="200px"></canvas>
                                    </div>
                                </div>
                            </div>
                        @endif
                        @if (auth()->user()->unit_kerja == '8010' || auth()->user()->unit_kerja == '8000')
                            <div class="col-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h4>Persentase Dokumen SKP per Bulan Bagian Umum Inspektorat Utama</h4>
                                    </div>
                                    <div class="card-body" style="padding-top: 5px;">
                                        <div class="d-flex justify-content-between">
                                            <div class="d-flex flex-row align-items-center">
                                            </div>
                                            <a href="/inspektur/cek-skp" class="arrow-button-card" type="button"
                                                class="rounded-circle"><i class="fa-solid fa-arrow-right"></i></a>
                                        </div>
                                        <canvas id="skpBuntamaChart" height="200px"></canvas>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    <!-- JS Libraies -->
    <script src="{{ asset('library/simpleweather/jquery.simpleWeather.min.js') }}"></script>
    <script src="{{ asset('library/chart.js/dist/Chart.min.js') }}"></script>
    <script src="{{ asset('library/jqvmap/dist/jquery.vmap.min.js') }}"></script>
    <script src="{{ asset('library/jqvmap/dist/maps/jquery.vmap.world.js') }}"></script>
    <script src="{{ asset('library/summernote/dist/summernote-bs4.min.js') }}"></script>
    <script src="{{ asset('library/chocolat/dist/js/jquery.chocolat.min.js') }}"></script>

    <!-- Page Specific JS File -->
    <script src="{{ asset('js/page/index-0.js') }}"></script>
@endpush
