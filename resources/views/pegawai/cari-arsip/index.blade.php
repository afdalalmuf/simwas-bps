@extends('layouts.app')

@section('title', 'Cari Arsip')

@section('main')
    @include('components.header')
    @include('components.pegawai-sidebar')

    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Pencarian Arsip</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="/arsiparis">Dashboard</a></div>
                    <div class="breadcrumb-item">Pencarian Arsip</div>
                </div>
            </div>

            <div class="section-body">

                <div id="js-config" data-ajukan-url="{{ route('pegawai.cari-arsip.ajukan') }}" style="display:none;"></div>

                {{-- Flash message --}}
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show">
                        <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                    </div>
                @endif

                {{-- ===== FILTER & SEARCH ===== --}}
                <div class="row mb-4">
                    <div class="col-md-5 mb-3 mb-md-0">
                        <div class="input-group"
                            style="border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                            <div class="input-group-prepend">
                                <span class="input-group-text"
                                    style="background: linear-gradient(135deg, #0069d9 0%, #0056b3 100%); border: none;">
                                    <i class="fas fa-search" style="color: white;"></i>
                                </span>
                            </div>
                            <input type="text" class="form-control" id="searchArsip"
                                placeholder="Cari berdasarkan kode atau judul arsip..."
                                style="border: none; padding: 12px;">
                        </div>
                    </div>

                    <div class="col-md-3 mb-3 mb-md-0">
                        <div class="input-group"
                            style="border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                            <div class="input-group-prepend">
                                <span class="input-group-text" style="background: #f8f9fc; border: none; color: #858796;">
                                    <i class="fas fa-shield-alt"></i>
                                </span>
                            </div>
                            <select class="form-control" id="filterSKKAA"
                                style="border: none; padding: 12px; color: #5a5c69; font-weight: 600;">
                                <option value="">Semua SKKAA</option>
                                <option value="BIASA">BIASA</option>
                                <option value="TERBATAS">TERBATAS</option>
                                <option value="RAHASIA">RAHASIA</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="input-group"
                            style="border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                            <div class="input-group-prepend">
                                <span class="input-group-text" style="background: #f8f9fc; border: none; color: #858796;">
                                    <i class="fas fa-building"></i>
                                </span>
                            </div>
                            <select class="form-control" id="filterUnit"
                                style="border: none; padding: 12px; color: #5a5c69; font-weight: 600;">
                                <option value="">Semua Unit</option>
                                <option value="Inspektorat Utama">Inspektorat Utama</option>
                                <option value="Inspektorat Wilayah I">Inspektorat Wilayah I</option>
                                <option value="Inspektorat Wilayah II">Inspektorat Wilayah II</option>
                                <option value="Inspektorat Wilayah III">Inspektorat Wilayah III</option>
                                <option value="Bagian Umum">Bagian Umum</option>
                            </select>
                        </div>
                    </div>
                </div>

                {{-- ===== DAFTAR ARSIP (CARD GRID) ===== --}}
                <div class="row" id="arsipContainer">
                    @forelse ($arsips as $arsip)
                        @php
                            $peminjaman = $peminjamanAktif[$arsip->id] ?? null;
                            $statusPinjam = $peminjaman?->status; // null | MENUNGGU | DISETUJUI
                            $aksesAktif =
                                $peminjaman &&
                                $statusPinjam === 'DISETUJUI' &&
                                $peminjaman->berakhir_pada &&
                                \Carbon\Carbon::now()->lt($peminjaman->berakhir_pada);
                        @endphp

                        <div class="col-lg-4 col-md-6 mb-4 arsip-item" data-skkaa="{{ $arsip->skkaa }}"
                            data-unit="{{ $arsip->unit_cipta }}"
                            data-search="{{ strtolower($arsip->kode_klasifikasi . ' ' . $arsip->judul_berkas) }}">
                            <div class="card border-0 shadow-sm h-100"
                                style="border-radius: 12px; overflow: hidden; transition: all 0.3s ease;"
                                onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 8px 25px rgba(0,0,0,0.15)';"
                                onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='';">

                                {{-- Card Header --}}
                                <div
                                    style="background: linear-gradient(135deg, #0069d9, #0056b3); padding: 20px; position: relative;">
                                    <div style="position: absolute; right: 15px; top: 15px; opacity: 0.15;">
                                        <i class="fas fa-archive" style="font-size: 60px; color: white;"></i>
                                    </div>
                                    <h6 class="mb-2 text-truncate"
                                        style="color: white; font-weight: 700; position: relative; z-index: 1;"
                                        title="{{ $arsip->judul_berkas }}">
                                        {{ $arsip->judul_berkas }}
                                    </h6>
                                    <div style="position: relative; z-index: 1;">
                                        <span
                                            style="display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 600;
                                        background: {{ $arsip->skkaa === 'BIASA' ? '#d4edda' : ($arsip->skkaa === 'TERBATAS' ? '#fff3cd' : '#fee') }};
                                        color: {{ $arsip->skkaa === 'BIASA' ? '#1cc88a' : ($arsip->skkaa === 'TERBATAS' ? '#f6c23e' : '#e74a3b') }};">
                                            {{ $arsip->skkaa }}
                                        </span>
                                    </div>
                                </div>

                                {{-- Card Body --}}
                                <div class="card-body" style="padding: 20px;">
                                    <div class="mb-3"
                                        style="background: #f8f9fc; padding: 12px; border-radius: 8px; border-left: 3px solid #0069d9;">
                                        <small
                                            style="color: #858796; font-size: 11px; font-weight: 600; text-transform: uppercase;">Kode
                                            Klasifikasi</small>
                                        <strong class="d-block"
                                            style="color: #5a5c69; margin-top: 4px;">{{ $arsip->kode_klasifikasi }}</strong>
                                    </div>
                                    <div class="mb-3"
                                        style="background: #f8f9fc; padding: 12px; border-radius: 8px; border-left: 3px solid #1cc88a;">
                                        <small
                                            style="color: #858796; font-size: 11px; font-weight: 600; text-transform: uppercase;">Unit
                                            Cipta</small>
                                        <strong class="d-block"
                                            style="color: #5a5c69; margin-top: 4px;">{{ $arsip->unit_cipta }}</strong>
                                    </div>
                                    <div class="mb-3"
                                        style="background: #f8f9fc; padding: 12px; border-radius: 8px; text-align: center;">
                                        <i class="fas fa-file-alt mb-1" style="color: #0069d9; font-size: 18px;"></i>
                                        <small style="color: #858796; font-size: 10px; display: block;">Jumlah
                                            Dokumen</small>
                                        <strong
                                            style="color: #5a5c69; font-size: 20px;">{{ $arsip->dokumens_count }}</strong>
                                        <small style="color: #858796; font-size: 10px; display: block;">dokumen</small>
                                    </div>

                                    {{-- Tombol Aksi berdasarkan status peminjaman --}}
                                    @if ($aksesAktif)
                                        {{-- Sudah disetujui & masih aktif --}}
                                        <a href="{{ route('pegawai.cari-arsip.dokumen', $arsip->id) }}"
                                            class="btn btn-block btn-sm btn-success mb-1" style="border-radius: 8px;">
                                            <i class="fas fa-folder-open"></i> Akses Dokumen
                                        </a>
                                        <small class="text-muted d-block text-center" style="font-size: 10px;">
                                            <i class="fas fa-clock mr-1"></i>Akses berakhir:
                                            {{ \Carbon\Carbon::parse($peminjaman->berakhir_pada)->format('d/m/Y') }}
                                        </small>
                                    @elseif ($statusPinjam === 'MENUNGGU')
                                        {{-- Sedang menunggu --}}
                                        <button class="btn btn-block btn-sm btn-warning" disabled
                                            style="border-radius: 8px;">
                                            <i class="fas fa-hourglass-half"></i> Menunggu Persetujuan
                                        </button>
                                    @else
                                        {{-- Belum ada peminjaman atau sebelumnya ditolak --}}
                                        <button class="btn btn-block btn-sm btn-primary"
                                            onclick="ajukanPinjaman({{ $arsip->id }}, '{{ addslashes($arsip->judul_berkas) }}')"
                                            style="border-radius: 8px;">
                                            <i class="fas fa-hand-paper"></i> Ajukan Pinjaman
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12 text-center py-5">
                            <div style="background: #f8f9fc; border-radius: 12px; padding: 60px 20px;">
                                <i class="fas fa-inbox mb-3" style="font-size: 60px; color: #d1d3e2;"></i>
                                <p style="color: #858796; font-size: 16px; margin: 0;">Belum ada arsip aktif</p>
                            </div>
                        </div>
                    @endforelse
                </div>

                {{-- ===== PAGINATION ===== --}}
                <div class="d-flex justify-content-between align-items-center mt-4 flex-wrap" style="gap: 10px;"
                    id="pagination-wrapper">
                    <small class="text-muted" id="pagination-info"></small>
                    <nav>
                        <ul class="pagination pagination-sm mb-0" id="pagination-controls"></ul>
                    </nav>
                </div>

            </div>
        </section>
    </div>
    {{-- ===== MODAL AJUKAN PINJAMAN ===== --}}
    <div class="modal fade" id="modalAjukanPinjaman" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content" style="border-radius: 12px; overflow: hidden;">

                <div class="modal-header" style="background: linear-gradient(135deg, #0069d9, #0056b3);">
                    <h5 class="modal-title text-white">
                        <i class="fas fa-hand-paper mr-2"></i>Ajukan Pinjaman Arsip
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <div class="modal-body p-4">
                    {{-- Info arsip yang dipilih --}}
                    <div class="alert alert-light border mb-4" style="border-radius: 8px;">
                        <small class="text-muted d-block"
                            style="font-size: 11px; font-weight: 600; text-transform: uppercase;">Arsip yang
                            Dipinjam</small>
                        <strong id="modal-judul-arsip" class="text-primary"></strong>
                    </div>

                    <div class="form-group mb-0">
                        <label style="font-weight: 600; color: #5a5c69;">
                            Alasan Peminjaman <span class="text-danger">*</span>
                        </label>
                        <textarea id="input-alasan" rows="4" class="form-control"
                            placeholder="Jelaskan keperluan peminjaman arsip ini..." style="border-radius: 8px; resize: none;"></textarea>
                        <small class="text-muted">Minimal 5 karakter</small>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i> Batal
                    </button>
                    <button type="button" class="btn btn-primary" id="btn-kirim-ajuan">
                        <i class="fas fa-paper-plane mr-1"></i> Kirim Pengajuan
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/page/pegawai/cari-arsip.js') }}"></script>
@endpush
