@extends('layouts.app')

@section('title', 'Peminjaman Arsip')

@section('main')
    @include('components.arsiparis-header')
    @include('components.arsiparis-sidebar')
    @include('components.arsiparis.modal-detail-peminjaman')

    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Peminjaman Arsip</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="/arsiparis">Dashboard</a></div>
                    <div class="breadcrumb-item">Peminjaman Arsip</div>
                </div>
            </div>

            <div class="section-body">

                {{-- ===== STATISTIK CARDS ===== --}}
                <div class="row mb-4">
                    <div class="col-md-4 mb-3">
                        <div class="card border-0 shadow" style="border-radius: 10px; border-left: 4px solid #f6c23e;">
                            <div class="card-body py-3 d-flex justify-content-between align-items-center">
                                <div>
                                    <p class="mb-1"
                                        style="color: #858796; font-size: 12px; font-weight: 700; text-transform: uppercase;">
                                        Menunggu Persetujuan</p>
                                    <h3 class="mb-0" style="color: #5a5c69; font-weight: 700;">{{ $counts['menunggu'] }}
                                    </h3>
                                </div>
                                <div
                                    style="width: 50px; height: 50px; background: #fff3cd; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-hourglass-half" style="color: #f6c23e; font-size: 20px;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="card border-0 shadow" style="border-radius: 10px; border-left: 4px solid #1cc88a;">
                            <div class="card-body py-3 d-flex justify-content-between align-items-center">
                                <div>
                                    <p class="mb-1"
                                        style="color: #858796; font-size: 12px; font-weight: 700; text-transform: uppercase;">
                                        Disetujui</p>
                                    <h3 class="mb-0" style="color: #5a5c69; font-weight: 700;">
                                        {{ $counts['disetujui'] }}</h3>
                                </div>
                                <div
                                    style="width: 50px; height: 50px; background: #d4edda; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-check-circle" style="color: #1cc88a; font-size: 20px;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="card border-0 shadow" style="border-radius: 10px; border-left: 4px solid #e74a3b;">
                            <div class="card-body py-3 d-flex justify-content-between align-items-center">
                                <div>
                                    <p class="mb-1"
                                        style="color: #858796; font-size: 12px; font-weight: 700; text-transform: uppercase;">
                                        Ditolak</p>
                                    <h3 class="mb-0" style="color: #5a5c69; font-weight: 700;">{{ $counts['ditolak'] }}
                                    </h3>
                                </div>
                                <div
                                    style="width: 50px; height: 50px; background: #fde8e7; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-times-circle" style="color: #e74a3b; font-size: 20px;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ===== TABEL PEMINJAMAN ===== --}}
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">Daftar Pengajuan Peminjaman</h4>
                        {{-- Filter status --}}
                        <select id="filterStatusPeminjaman" class="form-control form-control-sm" style="width: auto;">
                            <option value="">Semua Status</option>
                            <option value="MENUNGGU">Menunggu</option>
                            <option value="DISETUJUI">Disetujui</option>
                            <option value="DITOLAK">Ditolak</option>
                        </select>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0" id="tabelPeminjaman">
                                <thead style="background: #f8f9fc;">
                                    <tr>
                                        <th
                                            style="padding: 14px 16px; font-size: 12px; font-weight: 700; text-transform: uppercase; color: #858796; border: none;">
                                            ID Pengajuan</th>
                                        <th
                                            style="padding: 14px 16px; font-size: 12px; font-weight: 700; text-transform: uppercase; color: #858796; border: none;">
                                            Peminjam</th>
                                        <th
                                            style="padding: 14px 16px; font-size: 12px; font-weight: 700; text-transform: uppercase; color: #858796; border: none;">
                                            ID Arsip</th>
                                        <th
                                            style="padding: 14px 16px; font-size: 12px; font-weight: 700; text-transform: uppercase; color: #858796; border: none;">
                                            Unit</th>
                                        <th
                                            style="padding: 14px 16px; font-size: 12px; font-weight: 700; text-transform: uppercase; color: #858796; border: none;">
                                            Tanggal Pengajuan</th>
                                        <th
                                            style="padding: 14px 16px; font-size: 12px; font-weight: 700; text-transform: uppercase; color: #858796; border: none;">
                                            Status</th>
                                        <th
                                            style="padding: 14px 16px; font-size: 12px; font-weight: 700; text-transform: uppercase; color: #858796; border: none;">
                                            Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($peminjamans as $p)
                                        <tr class="peminjaman-row" data-status="{{ $p->status }}"
                                            style="border-bottom: 1px solid #f0f0f0;">
                                            <td
                                                style="padding: 14px 16px; font-weight: 700; color: #5a5c69; vertical-align: middle;">
                                                {{ $p->id_tampil }}
                                            </td>
                                            <td style="padding: 14px 16px; vertical-align: middle;">
                                                <div style="font-weight: 600; color: #5a5c69;">{{ $p->peminjam->name }}
                                                </div>
                                                <small style="color: #858796;">{{ $p->peminjam->nip ?? '' }}</small>
                                            </td>
                                            <td style="padding: 14px 16px; vertical-align: middle;">
                                                <span style="color: #0069d9; font-weight: 700;">
                                                    ARS{{ str_pad($p->arsip->id, 3, '0', STR_PAD_LEFT) }}
                                                </span>
                                            </td>
                                            <td style="padding: 14px 16px; vertical-align: middle; color: #5a5c69;">
                                                {{ $p->peminjam->unit ?? $p->arsip->unit_cipta }}
                                            </td>
                                            <td style="padding: 14px 16px; vertical-align: middle; color: #858796;">
                                                {{ $p->created_at->format('d/m/Y') }}
                                            </td>
                                            <td style="padding: 14px 16px; vertical-align: middle;">
                                                @if ($p->status === 'MENUNGGU')
                                                    <span class="badge"
                                                        style="background: #fff3cd; color: #856404; padding: 6px 12px; border-radius: 20px; font-size: 11px; font-weight: 700;">
                                                        MENUNGGU
                                                    </span>
                                                @elseif ($p->status === 'DISETUJUI')
                                                    <span class="badge"
                                                        style="background: #d4edda; color: #155724; padding: 6px 12px; border-radius: 20px; font-size: 11px; font-weight: 700;">
                                                        DISETUJUI
                                                    </span>
                                                @else
                                                    <span class="badge"
                                                        style="background: #fde8e7; color: #721c24; padding: 6px 12px; border-radius: 20px; font-size: 11px; font-weight: 700;">
                                                        DITOLAK
                                                    </span>
                                                @endif
                                            </td>
                                            <td style="padding: 14px 16px; vertical-align: middle;">
                                                <button class="btn btn-sm btn-primary" style="border-radius: 6px;"
                                                    onclick="lihatDetailPeminjaman({{ $p->id }})">
                                                    <i class="fas fa-eye mr-1"></i> Detail
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center py-5 text-muted">
                                                <i class="fas fa-inbox fa-3x d-block mb-2"></i>
                                                Belum ada pengajuan peminjaman
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/page/arsiparis/peminjaman.js') }}"></script>
@endpush
