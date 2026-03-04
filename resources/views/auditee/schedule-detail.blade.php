@extends('layouts.app')

@section('title', 'Detail Audit')

@section('main')
    @include('components.auditee-header')
    @include('components.auditee-sidebar')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Detail Pengawasan</h1>
            </div>
            <div class="card">
                <div class="card-body">
                    @include('components.flash')
                    {{ session()->forget(['alert-type', 'status']) }}
                    <div class="row mb-4 pb-0">
                        <div class="col-md-8">
                            <a class="btn btn-primary" href="{{route('auditee.schedule')}}">
                                <i class="fas fa-chevron-circle-left"></i> Kembali
                            </a>
                        </div>
                    </div>
                    <table class="mb-4 table table-striped responsive">
                            <tr>
                                <th>Pengawasan</th>
                                <td>{{ $audit->nama_laporan }}</td>
                            </tr>
                        <tr>
                            <th>Mulai</th>
                            <td>{{ $audit->start_date }}</td>
                        </tr>
                        <tr>
                            <th>Selesai</th>
                            <td>{{ $audit->end_date }}</td>
                        </tr>
                        <tr>
                            <th>Laporan</th>
                            <td>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </section>
    </div>
@endsection
