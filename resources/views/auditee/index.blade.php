@extends('layouts.app')

@section('title', 'Jadwal Audit')

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
    @include('components.auditee-header')
    @include('components.auditee-sidebar')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Dasbor Auditi</h1>
            </div>
            <form id="yearForm" action="" method="GET" class="col-md-1 px-0">
                @csrf
                <div class="form-group">
                    <label for="yearSelect">Pilih Tahun</label>
                    <select name="year" id="yearSelect" class="form-control select2">
                        @php
                        $currentYear = date('Y');
                        $lastThreeYears = range($currentYear, $currentYear - 3);
                        @endphp
    
                        @foreach ($lastThreeYears as $yearOption)
                        <option value="{{ $yearOption }}" {{ $year == $yearOption ? 'selected' : '' }}>{{ $yearOption }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </form>
            @include('auditee.dashboard-card')
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
<script>
    $('#yearSelect').on('change', function() {
        let year = $(this).val();
        $('#yearForm').attr('action', `?year=${year}`);
        $('#yearForm').find('[name="_token"]').remove();
        $('#yearForm').submit();
    });
</script>
@endpush
