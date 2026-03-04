{{--
    File ini sebelumnya bernama daftar.blade.php
    Diganti menjadi show.blade.php agar konsisten dengan konvensi Laravel (index/create/show/edit)
--}}

<div class="card-body">

    {{-- ===== FILTER & SEARCH ===== --}}
    <div class="row mb-4">
        <div class="col-md-4 mb-3 mb-md-0">
            <div class="input-group"
                style="border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                <div class="input-group-prepend">
                    <span class="input-group-text"
                        style="background: linear-gradient(135deg, #0069d9 0%, #0056b3 100%); border: none;">
                        <i class="fas fa-search" style="color: white;"></i>
                    </span>
                </div>
                <input type="text" class="form-control" id="searchArsip"
                    placeholder="Cari berdasarkan ID atau kode..." style="border: none; padding: 12px;">
            </div>
        </div>

        <div class="col-md-2 mb-3 mb-md-0">
            <div class="input-group"
                style="border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                <div class="input-group-prepend">
                    <span class="input-group-text" style="background: #f8f9fc; border: none; color: #858796;">
                        <i class="fas fa-filter"></i>
                    </span>
                </div>
                <select class="form-control" id="filterStatus"
                    style="border: none; padding: 12px; color: #5a5c69; font-weight: 600;">
                    <option value="">Semua Status</option>
                    <option value="AKTIF">AKTIF</option>
                    <option value="DRAFT">DRAFT</option>
                </select>
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

        <div class="col-md-3">
            <button class="btn btn-block" id="exportExcel"
                style="background: linear-gradient(135deg, #1cc88a 0%, #13855c 100%); border: none; color: white; padding: 12px; border-radius: 8px; font-weight: 600; box-shadow: 0 2px 8px rgba(28,200,138,0.3);">
                <i class="fas fa-file-excel mr-2"></i> Ekspor ke Excel
            </button>
        </div>
    </div>

    {{-- ===== STATISTIK CARDS ===== --}}
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow" style="border-radius: 8px; border-left: 4px solid #0069d9;">
                <div class="card-body" style="padding: 20px;">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p
                                style="color: #858796; margin-bottom: 8px; font-size: 11px; font-weight: 700; text-transform: uppercase;">
                                Total Arsip</p>
                            <h3 style="color: #5a5c69; margin: 0; font-weight: 700; font-size: 28px;" id="totalArsip">
                                {{ $arsips->count() }}</h3>
                        </div>
                        <div
                            style="width: 60px; height: 60px; background: linear-gradient(135deg, #0069d9, #0056b3); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-archive" style="font-size: 24px; color: white;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow" style="border-radius: 8px; border-left: 4px solid #1cc88a;">
                <div class="card-body" style="padding: 20px;">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p
                                style="color: #858796; margin-bottom: 8px; font-size: 11px; font-weight: 700; text-transform: uppercase;">
                                Arsip Aktif</p>
                            <h3 style="color: #5a5c69; margin: 0; font-weight: 700; font-size: 28px;" id="arsipAktif">
                                {{ $arsips->where('status', 'AKTIF')->count() }}</h3>
                        </div>
                        <div
                            style="width: 60px; height: 60px; background: linear-gradient(135deg, #1cc88a, #13855c); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-check-circle" style="font-size: 24px; color: white;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow" style="border-radius: 8px; border-left: 4px solid #858796;">
                <div class="card-body" style="padding: 20px;">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p
                                style="color: #858796; margin-bottom: 8px; font-size: 11px; font-weight: 700; text-transform: uppercase;">
                                Draft</p>
                            <h3 style="color: #5a5c69; margin: 0; font-weight: 700; font-size: 28px;" id="arsipDraft">
                                {{ $arsips->where('status', 'DRAFT')->count() }}</h3>
                        </div>
                        <div
                            style="width: 60px; height: 60px; background: linear-gradient(135deg, #858796, #60616f); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-edit" style="font-size: 24px; color: white;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow" style="border-radius: 8px; border-left: 4px solid #f6c23e;">
                <div class="card-body" style="padding: 20px;">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p
                                style="color: #858796; margin-bottom: 8px; font-size: 11px; font-weight: 700; text-transform: uppercase;">
                                Siap Nonaktif</p>
                            @php
                                $siapNonaktif = $arsips
                                    ->where('status', 'AKTIF')
                                    ->filter(
                                        fn($a) => $a->berakhir_pada &&
                                            \Carbon\Carbon::parse($a->berakhir_pada)->lte(now()),
                                    )
                                    ->count();
                            @endphp
                            <h3 style="color: #5a5c69; margin: 0; font-weight: 700; font-size: 28px;" id="siapNonaktif">
                                {{ $siapNonaktif }}</h3>
                        </div>
                        <div
                            style="width: 60px; height: 60px; background: linear-gradient(135deg, #f6c23e, #d4a017); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-clock" style="font-size: 24px; color: white;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== DAFTAR ARSIP (CARD GRID) ===== --}}
    <div class="row" id="arsipContainer">
        @forelse ($arsips as $arsip)
            @php
                $sisaHari = 999;
                if ($arsip->berakhir_pada) {
                    $sisaHari = \Carbon\Carbon::now()->diffInDays(\Carbon\Carbon::parse($arsip->berakhir_pada), false);
                }
            @endphp

            <div class="col-lg-4 col-md-6 mb-4 arsip-item" data-status="{{ $arsip->status }}"
                data-skkaa="{{ $arsip->skkaa }}"
                data-search="{{ strtolower($arsip->kode_klasifikasi . ' ' . $arsip->judul_berkas) }}">
                <div class="card border-0 shadow-sm h-100"
                    style="border-radius: 12px; overflow: hidden; transition: all 0.3s ease;"
                    onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 8px 25px rgba(0,0,0,0.15)';"
                    onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='';">

                    {{-- Card Header --}}
                    <div
                        style="background: linear-gradient(135deg, {{ $arsip->status === 'AKTIF' ? '#0069d9, #0056b3' : '#858796, #60616f' }}); padding: 20px; position: relative;">
                        <div style="position: absolute; right: 15px; top: 15px; opacity: 0.2;">
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
                                background: {{ $sisaHari <= 0 ? '#fee' : ($sisaHari <= 30 ? '#fff3cd' : '#d4edda') }};
                                color: {{ $sisaHari <= 0 ? '#e74a3b' : ($sisaHari <= 30 ? '#f6c23e' : '#1cc88a') }};
                                margin-right: 6px;">
                                {{ $arsip->status }}
                            </span>
                            <span
                                style="display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 600;
                                background: {{ $arsip->skkaa === 'BIASA' ? '#d4edda' : ($arsip->skkaa === 'TERBATAS' ? '#fff3cd' : '#fee') }};
                                color: {{ $arsip->skkaa === 'BIASA' ? '#1cc88a' : ($arsip->skkaa === 'TERBATAS' ? '#f6c23e' : '#e74a3b') }};">
                                {{ $arsip->skkaa }}
                            </span>
                        </div>
                    </div>

                    <div class="card-body" style="padding: 20px;">
                        <div class="mb-3"
                            style="background: #f8f9fc; padding: 12px; border-radius: 8px; border-left: 3px solid #0069d9;">
                            <small
                                style="color: #858796; font-size: 11px; font-weight: 600; text-transform: uppercase;">Kode
                                Klasifikasi</small>
                            <strong class="d-block"
                                style="color: #5a5c69; padding-left: 0; margin-top: 4px;">{{ $arsip->kode_klasifikasi }}</strong>
                        </div>
                        <div class="mb-3"
                            style="background: #f8f9fc; padding: 12px; border-radius: 8px; border-left: 3px solid #1cc88a;">
                            <small
                                style="color: #858796; font-size: 11px; font-weight: 600; text-transform: uppercase;">Unit
                                Cipta</small>
                            <strong class="d-block"
                                style="color: #5a5c69; margin-top: 4px;">{{ $arsip->unit_cipta }}</strong>
                        </div>
                        <div class="row mb-3">
                            <div class="col-6">
                                <div
                                    style="background: #f8f9fc; padding: 12px; border-radius: 8px; text-align: center;">
                                    <i class="fas fa-file-alt d-block mb-1"
                                        style="color: #0069d9; font-size: 20px;"></i>
                                    <small style="color: #858796; font-size: 10px; display: block;">Dokumen</small>
                                    <strong
                                        style="color: #5a5c69; font-size: 18px;">{{ $arsip->dokumens_count }}</strong>
                                </div>
                            </div>
                            <div class="col-6">
                                <div
                                    style="background: #f8f9fc; padding: 12px; border-radius: 8px; text-align: center;">
                                    <i class="fas fa-clock d-block mb-1" style="color: #f6c23e; font-size: 20px;"></i>
                                    <small style="color: #858796; font-size: 10px; display: block;">Retensi</small>
                                    <strong
                                        style="color: #5a5c69; font-size: 18px;">{{ $arsip->masa_retensi }}</strong>
                                    <small style="color: #858796; font-size: 9px; display: block;">Tahun</small>
                                </div>
                            </div>
                        </div>

                        {{-- Tombol Aksi --}}
                        @if ($arsip->status === 'DRAFT')
                            <div class="row">
                                <div class="col-6 pr-1">
                                    <a href="{{ route('arsiparis.kelola-arsip.edit', $arsip->id) }}"
                                        class="btn btn-block btn-sm btn-warning" style="border-radius: 8px;">
                                        <i class="fas fa-edit"></i> Lengkapi
                                    </a>
                                </div>
                                <div class="col-6 pl-1">
                                    <button class="btn btn-block btn-sm btn-primary" data-id="{{ $arsip->id }}"
                                        onclick="lihatDetail(this)" style="border-radius: 8px;">
                                        <i class="fas fa-eye"></i> Lihat
                                    </button>
                                </div>
                            </div>
                        @elseif ($arsip->status === 'AKTIF')
                            <div class="row">
                                <div class="col-6 pr-1">
                                    <button class="btn btn-block btn-sm btn-primary" data-id="{{ $arsip->id }}"
                                        onclick="lihatDetail(this)" style="border-radius: 8px;">
                                        <i class="fas fa-eye"></i> Detail
                                    </button>
                                </div>
                                <div class="col-6 pl-1">
                                    <button class="btn btn-block btn-sm btn-secondary"
                                        onclick="nonaktifkanArsip({{ $arsip->id }})" style="border-radius: 8px;">
                                        <i class="fas fa-archive"></i> Nonaktif
                                    </button>
                                </div>
                            </div>
                        @else
                            <button class="btn btn-block btn-sm btn-primary" data-id="{{ $arsip->id }}"
                                onclick="lihatDetail(this)" style="border-radius: 8px;">
                                <i class="fas fa-eye"></i> Lihat Detail
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <div style="background: #f8f9fc; border-radius: 12px; padding: 60px 20px;">
                    <i class="fas fa-inbox mb-3" style="font-size: 60px; color: #d1d3e2;"></i>
                    <p style="color: #858796; font-size: 16px; margin: 0;">Belum ada arsip</p>
                </div>
            </div>
        @endforelse
    </div>

    {{-- ===== PAGINATION ===== --}}
    <div class="d-flex justify-content-between align-items-center mt-4 flex-wrap" style="gap: 10px;"
        id="pagination-wrapper">

        {{-- Info: menampilkan X-Y dari Z --}}
        <small class="text-muted" id="pagination-info"></small>

        {{-- Tombol navigasi --}}
        <nav>
            <ul class="pagination pagination-sm mb-0" id="pagination-controls"></ul>
        </nav>

    </div>

</div>
