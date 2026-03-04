@extends('layouts.app')

@section('title', 'Dokumen Arsip')

@section('content')
    <div class="section-header">
        <h1>Dokumen Arsip</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="#">Pegawai</a></div>
            <div class="breadcrumb-item"><a href="{{ route('pegawai.cari-arsip.index') }}">Cari Arsip</a></div>
            <div class="breadcrumb-item">Dokumen</div>
        </div>
    </div>

    <div class="section-body">

        {{-- Info akses --}}
        <div class="alert alert-success d-flex align-items-center mb-4" style="border-radius: 10px;">
            <i class="fas fa-check-circle fa-2x mr-3"></i>
            <div>
                <strong>Akses Dokumen Aktif</strong>
                <p class="mb-0">Anda memiliki akses hingga
                    <strong>{{ \Carbon\Carbon::parse($peminjaman->berakhir_pada)->format('d/m/Y') }}</strong>
                    ({{ \Carbon\Carbon::now()->diffForHumans($peminjaman->berakhir_pada, ['parts' => 1]) }} lagi).
                </p>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h4><i class="fas fa-folder-open mr-2 text-primary"></i>{{ $arsip->judul_berkas }}</h4>
            </div>
            <div class="card-body">

                {{-- Info arsip --}}
                <div class="row mb-4">
                    <div class="col-md-3">
                        <small class="text-muted d-block">Kode Klasifikasi</small>
                        <strong>{{ $arsip->kode_klasifikasi }}</strong>
                    </div>
                    <div class="col-md-3">
                        <small class="text-muted d-block">Unit Cipta</small>
                        <strong>{{ $arsip->unit_cipta }}</strong>
                    </div>
                    <div class="col-md-3">
                        <small class="text-muted d-block">SKKAA</small>
                        <strong>{{ $arsip->skkaa }}</strong>
                    </div>
                    <div class="col-md-3">
                        <small class="text-muted d-block">Jumlah Dokumen</small>
                        <strong>{{ $arsip->dokumens->count() }} Dokumen</strong>
                    </div>
                </div>

                <hr>

                {{-- Daftar Dokumen --}}
                <h5 class="mb-3"><i class="fas fa-file-alt mr-2"></i>Daftar Dokumen</h5>

                @forelse ($arsip->dokumens as $dok)
                    @php
                        $ext = pathinfo($dok->nama_file, PATHINFO_EXTENSION);
                        $fileUrl = asset('storage/' . $dok->path_file);
                        $canPreview = in_array(strtolower($ext), ['pdf', 'jpg', 'jpeg', 'png', 'gif', 'webp']);
                    @endphp
                    <div class="border rounded p-3 mb-3 d-flex justify-content-between align-items-center"
                        style="background: #f8f9fc; border-radius: 8px !important;">
                        <div class="d-flex align-items-center">
                            <i
                                class="fas {{ strtolower($ext) === 'pdf' ? 'fa-file-pdf text-danger' : (in_array(strtolower($ext), ['jpg', 'jpeg', 'png', 'gif', 'webp']) ? 'fa-file-image text-info' : (in_array(strtolower($ext), ['doc', 'docx']) ? 'fa-file-word text-primary' : 'fa-file-alt text-secondary')) }} fa-2x mr-3"></i>
                            <div>
                                <strong>{{ $dok->judul_dokumen ?: 'Tanpa Judul' }}</strong>
                            </div>
                        </div>
                        <div class="d-flex" style="gap: 8px;">
                            @if ($canPreview)
                                <a href="{{ $fileUrl }}" target="_blank" class="btn btn-sm btn-outline-primary"
                                    style="border-radius: 6px;">
                                    <i class="fas fa-eye mr-1"></i> Preview
                                </a>
                            @endif
                            <a href="{{ $fileUrl }}" download="{{ $dok->nama_file }}" class="btn btn-sm btn-primary"
                                style="border-radius: 6px;">
                                <i class="fas fa-download mr-1"></i> Unduh
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-folder-open fa-3x mb-2 d-block"></i>
                        Belum ada dokumen
                    </div>
                @endforelse

            </div>
            <div class="card-footer">
                <a href="{{ route('pegawai.cari-arsip.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali ke Cari Arsip
                </a>
            </div>
        </div>
    </div>
@endsection
