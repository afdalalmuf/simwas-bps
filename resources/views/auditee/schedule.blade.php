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
    <script src="https://cdn.dhtmlx.com/gantt/edge/dhtmlxgantt.js"></script>
    <link href="https://cdn.dhtmlx.com/gantt/edge/dhtmlxgantt.css" rel="stylesheet">
    <style>
        .gantt_task_line.task-upcoming {
            background-color: #ffc107 !important;
            /* yellow */
            border: 1px solid #ff9800;
        }

        .gantt_task_line.task-current {
            background-color: #2196f3 !important;
            /* blue */
            border: 1px solid #1976d2;
        }

        .gantt_task_line.task-past {
            background-color: #4caf50 !important;
            /* green */
            border: 1px solid #2e7d32;
        }

        .gantt-legend-inline {
            display: flex;
            flex-wrap: wrap;
            gap: 16px;
            /* ⬅️ increase spacing between legend items */
            font-size: 14px;
            padding-top: 8px;
            border-top: 1px solid #eee;
            justify-content: flex-start;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 8px;
            /* ⬅️ spacing between box and label */
        }

        .legend-box {
            width: 16px;
            height: 16px;
            border-radius: 4px;
            border: 1px solid #bbb;
        }
    </style>
@endpush

@section('main')
    @include('components.auditee-header')
    @include('components.auditee-sidebar')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Jadwal Pengawasan</h1>
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

                        @foreach ($lastThreeYears as $year)
                            <option value="{{ $year }}" {{ request()->query('year') == $year ? 'selected' : '' }}>
                                {{ $year }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </form>
            <div class="dashboard-card my-4 p-3">
                <div class="d-flex flex-column">
                    <div id="gantt_here" style="width: 100%; height: 600px; position: relative;"></div>

                    <div class="gantt-legend-inline d-flex flex-wrap gap-3 mt-3">
                        <div class="legend-item d-flex align-items-center gap-2">
                            <span class="legend-box" style="background-color: #4caf50;"></span>
                            <span>Sudah Berjalan</span>
                        </div>
                        <div class="legend-item d-flex align-items-center gap-2">
                            <span class="legend-box" style="background-color: #2196f3;"></span>
                            <span>Sedang Berjalan</span>
                        </div>
                        <div class="legend-item d-flex align-items-center gap-2">
                            <span class="legend-box" style="background-color:#ffc107;"></span>
                            <span>Belum Berjalan</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    <script>
        window.routes = {
            ganttDataUrl: "{{ route('auditee.schedule.get') }}"
        };
    </script>
    <script src="{{ asset('js') }}/page/auditee/schedule-page.js"></script>
@endpush
