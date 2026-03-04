@extends('layouts.app')

@section('title', 'Kelola Aktivitas Harian')

@push('clockpicker')
    <link rel="stylesheet" href="{{ asset('library') }}/clockpicker/jquery-clockpicker.min.css">
@endpush

@push('style')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- CSS Libraries -->
    <link
        href="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.13.4/af-2.5.3/b-2.3.6/b-colvis-2.3.6/b-html5-2.3.6/b-print-2.3.6/cr-1.6.2/date-1.4.1/fc-4.2.2/fh-3.3.2/kt-2.9.0/r-2.4.1/rg-1.3.1/rr-1.3.3/sc-2.1.1/sb-1.4.2/sp-2.1.2/sl-1.6.2/sr-1.2.2/datatables.min.css"
        rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('library') }}/sweetalert2/dist/sweetalert2.min.css">
    <style>
        @media (max-width: 768px) {
            .modal-dialog {
                max-width: 100%;
                margin: 0;
                height: 100%;
            }

            .modal-content {
                height: 100%;
                border-radius: 0;
                overflow-y: auto;
                display: flex;
                flex-direction: column;
            }

            .modal-body {
                flex: 1 1 auto;
                overflow-y: auto;
            }

            .fc .fc-button {
                font-size: 0.7rem;
                padding: 0.3rem 0.4rem;
            }

            .fc .fc-button.fc-prev-button,
            .fc .fc-button.fc-next-button {
                font-size: 1rem;
                padding: 0.1rem 0.5rem;
            }
        }
    </style>
@endpush

@section('main')
    @include('components.header')
    @include('components.pegawai-sidebar')

    <div class="main-content">
        <!-- Modal -->

        @include('pegawai.aktivitas-harian.edit');
        <section class="section">
            <div class="section-header">
                <h1>Kelola Aktivitas Harian</h1>
            </div>
            <div class="row">
                <div class=" col-md-12">
                    <div class="card">
                        <div class="card-body">
                            @include('components.flash')
                            {{ session()->forget(['alert-type', 'status']) }}
                            <div class="d-flex align-items-center justify-content-between mb-3">

                                <!-- Center: Legend -->
                                <div class="mx-auto">
                                    <ul class="legend d-flex gap-3 mb-0">
                                        <li><span class="badge jingga">Sedang Dikerjakan</span></li>
                                        <li><span class="badge hijau">Selesai</span></li>
                                        <li><span class="badge merah">Dibatalkan</span></li>
                                        <li><span class="badge hitam">Tidak Selesai</span></li>
                                    </ul>
                                </div>

                                <!-- Right: Empty placeholder to balance flex (optional) -->
                                <div class="me-auto" style="visibility: hidden;">
                                    <button class="btn btn-primary"><i class="fas fa-sync-alt"></i> Sinkronkan</button>
                                </div>
                            </div>
                            <div id='calendar' style="90%"></div>
                        </div>
                    </div>
                </div>
        </section>
    </div>

    @include('pegawai.aktivitas-harian.create');
@endsection

@push('scripts')
    <!-- JS Libraies -->
    {{-- <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.js"></script> --}}
    <script src="{{ asset('library') }}/fullcalendar-6.1.10/dist/index.global.min.js"></script>
    <script src="{{ asset('library') }}/moment/min/moment-with-locales.min.js"></script>
    {{-- <script src="https://cdn.datatables.net/v/dt/dt-1.13.4/b-2.3.6/b-colvis-2.3.6/datatables.min.js"></script>
    <script src="{{ asset('js') }}/plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="{{ asset('js') }}/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script src="{{ asset('js') }}/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
    <script src="{{ asset('js') }}/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
    <script src="{{ asset('js') }}/plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
    <script src="{{ asset('js') }}/plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script> --}}
    {{-- <script src="{{ asset('js') }}/plugins/jszip/jszip.min.js"></script>
    <script src="{{ asset('js') }}/plugins/pdfmake/pdfmake.min.js"></script>
    <script src="{{ asset('js') }}/plugins/pdfmake/vfs_fonts.js"></script>
    <script src="{{ asset('js') }}/plugins/datatables-buttons/js/buttons.html5.min.js"></script>
    <script src="{{ asset('js') }}/plugins/datatables-buttons/js/buttons.print.min.js"></script>
    <script src="{{ asset('js') }}/plugins/datatables-buttons/js/buttons.colVis.min.js"></script> --}}
    <script src="{{ asset('library') }}/sweetalert2/dist/sweetalert2.min.js"></script>
    <script src="{{ asset('library') }}/clockpicker/jquery-clockpicker.js"></script>
    {{-- <script type="module" src="{{ asset('library') }}/tooltip.js/dist/tooltip.min.js"></script> --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Page Specific JS File -->
    <script>
        var events = @json($events);
    </script>
    <script src="{{ asset('js/page/pegawai/aktivitas-harian.js?v=1.0.2') }}"></script>
@endpush
