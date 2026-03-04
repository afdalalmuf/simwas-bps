<div class="card-body">

    {{-- Statistik Cards --}}
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
            <div class="card border-0 shadow"
                style="border-radius: 8px; overflow: hidden; border-left: 4px solid #4e73df; transition: transform 0.2s ease;"
                onmouseover="this.style.transform='translateY(-3px)';" onmouseout="this.style.transform='translateY(0)';">
                <div class="card-body" style="padding: 20px;">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p
                                style="color: #858796; margin-bottom: 8px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">
                                Total Arsip</p>
                            <h3 style="color: #5a5c69; margin: 0; font-weight: 700; font-size: 28px;">
                                {{ $arsips->whereIn('status', ['AKTIF', 'NONAKTIF'])->count() }}</h3>
                        </div>
                        <div
                            style="width: 60px; height: 60px; background: linear-gradient(135deg, #4e73df 0%, #224abe 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-archive" style="font-size: 24px; color: white;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
            <div class="card border-0 shadow"
                style="border-radius: 8px; overflow: hidden; border-left: 4px solid #1cc88a; transition: transform 0.2s ease;"
                onmouseover="this.style.transform='translateY(-3px)';"
                onmouseout="this.style.transform='translateY(0)';">
                <div class="card-body" style="padding: 20px;">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p
                                style="color: #858796; margin-bottom: 8px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">
                                Arsip Aktif</p>
                            <h3 style="color: #5a5c69; margin: 0; font-weight: 700; font-size: 28px;">
                                {{ $arsips->where('status', 'AKTIF')->count() }}</h3>
                        </div>
                        <div
                            style="width: 60px; height: 60px; background: linear-gradient(135deg, #1cc88a 0%, #13855c 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-check-circle" style="font-size: 24px; color: white;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
            <div class="card border-0 shadow"
                style="border-radius: 8px; overflow: hidden; border-left: 4px solid #f6c23e; transition: transform 0.2s ease;"
                onmouseover="this.style.transform='translateY(-3px)';"
                onmouseout="this.style.transform='translateY(0)';">
                <div class="card-body" style="padding: 20px;">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p
                                style="color: #858796; margin-bottom: 8px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">
                                Siap Nonaktif</p>
                            @php
                                $siapNonaktif = $arsips
                                    ->where('status', 'AKTIF')
                                    ->filter(function ($arsip) {
                                        if (!$arsip->berakhir_pada) {
                                            return false;
                                        }
                                        return \Carbon\Carbon::parse($arsip->berakhir_pada)->lte(\Carbon\Carbon::now());
                                    })
                                    ->count();
                            @endphp
                            <h3 style="color: #5a5c69; margin: 0; font-weight: 700; font-size: 28px;">
                                {{ $siapNonaktif }}</h3>
                        </div>
                        <div
                            style="width: 60px; height: 60px; background: linear-gradient(135deg, #f6c23e 0%, #d4a017 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-exclamation-circle" style="font-size: 24px; color: white;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
            <div class="card border-0 shadow"
                style="border-radius: 8px; overflow: hidden; border-left: 4px solid #858796; transition: transform 0.2s ease;"
                onmouseover="this.style.transform='translateY(-3px)';"
                onmouseout="this.style.transform='translateY(0)';">
                <div class="card-body" style="padding: 20px;">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p
                                style="color: #858796; margin-bottom: 8px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">
                                Nonaktif</p>
                            <h3 style="color: #5a5c69; margin: 0; font-weight: 700; font-size: 28px;">
                                {{ $arsips->where('status', 'NONAKTIF')->count() }}</h3>
                        </div>
                        <div
                            style="width: 60px; height: 60px; background: linear-gradient(135deg, #858796 0%, #60616f 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-archive" style="font-size: 24px; color: white;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Daftar Arsip Aktif --}}
    <div class="row">
        @php
            $arsipsAktif = $arsips->where('status', 'AKTIF')->sortBy(function ($arsip) {
                if (!$arsip->berakhir_pada) {
                    return PHP_INT_MAX;
                }
                return \Carbon\Carbon::parse($arsip->berakhir_pada)->timestamp;
            });
        @endphp

        @forelse($arsipsAktif as $arsip)
            @php
                // Hitung sisa hari dan persentase
                if ($arsip->tanggal_dibuat && $arsip->berakhir_pada) {
                    $totalDays = \Carbon\Carbon::parse($arsip->tanggal_dibuat)->diffInDays(
                        \Carbon\Carbon::parse($arsip->berakhir_pada),
                    );
                    $sisaHari = \Carbon\Carbon::parse($arsip->berakhir_pada)->diffInDays(\Carbon\Carbon::now(), false);
                    $percentage = $totalDays > 0 ? (($totalDays + $sisaHari) / $totalDays) * 100 : 0;
                    $percentage = min(100, max(0, $percentage));
                } else {
                    $totalDays = 0;
                    $sisaHari = 0;
                    $percentage = 0;
                }
            @endphp

            <div class="col-12 mb-3">
                <div class="card border-0 shadow-sm"
                    style="border-radius: 8px; border-left: 4px solid {{ $sisaHari >= 0 ? '#e74a3b' : ($sisaHari > -30 ? '#f6c23e' : '#1cc88a') }}; transition: transform 0.2s ease, box-shadow 0.2s ease;"
                    onmouseover="this.style.transform='translateX(5px)'; this.style.boxShadow='0 0.5rem 1.5rem rgba(0,0,0,0.15)';"
                    onmouseout="this.style.transform='translateX(0)'; this.style.boxShadow='0 0.15rem 1.75rem 0 rgba(58,59,69,0.15)';">
                    <div class="card-body" style="padding: 24px;">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center flex-wrap mb-2">
                                    <h5 class="mb-0 mr-3" style="color: #2c3e50; font-weight: 700;">
                                        {{ $arsip->judul_berkas }}
                                    </h5>
                                    <div>
                                        <span
                                            style="display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 600; background: {{ $sisaHari >= 0 ? '#fee' : ($sisaHari > -30 ? '#fff3cd' : '#d4edda') }}; color: {{ $sisaHari >= 0 ? '#e74a3b' : ($sisaHari > -30 ? '#f6c23e' : '#1cc88a') }}; margin-right: 6px;">
                                            {{ $arsip->status }}
                                        </span>
                                        <span
                                            style="display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 600; background: {{ $arsip->skkaa === 'BIASA' ? '#d4edda' : ($arsip->skkaa === 'TERBATAS' ? '#fff3cd' : '#fee') }}; color: {{ $arsip->skkaa === 'BIASA' ? '#1cc88a' : ($arsip->skkaa === 'TERBATAS' ? '#f6c23e' : '#e74a3b') }};">
                                            {{ $arsip->skkaa }}
                                        </span>
                                    </div>
                                </div>
                                <p class="mb-0" style="color: #858796; font-size: 13px;">
                                    <i class="fas fa-tag" style="width: 16px;"></i> {{ $arsip->kode_klasifikasi }}
                                    <span style="margin: 0 8px;">•</span>
                                    <i class="fas fa-building" style="width: 16px;"></i> {{ $arsip->unit_cipta }}
                                </p>
                            </div>
                            <button class="btn btn-sm ml-3" data-id="{{ $arsip->id }}" onclick="lihatDetail(this)"
                                style="background: linear-gradient(135deg, #4e73df 0%, #224abe 100%); border: none; color: white; padding: 8px 20px; border-radius: 6px; font-weight: 600; font-size: 13px; transition: all 0.2s ease;"
                                onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 8px rgba(78,115,223,0.3)';"
                                onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none';">
                                <i class="fas fa-eye mr-1"></i> Lihat Detail
                            </button>
                        </div>

                        <div class="row mt-3" style="padding-top: 16px; border-top: 1px solid #e3e6f0;">
                            <div class="col-md-3 mb-3 mb-md-0">
                                <div style="background: #f8f9fc; padding: 12px; border-radius: 6px;">
                                    <small
                                        style="color: #858796; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; display: block; margin-bottom: 6px;">Jumlah
                                        Dokumen</small>
                                    <p class="mb-0" style="color: #5a5c69; font-weight: 700; font-size: 16px;">
                                        <i class="fas fa-file-alt text-primary mr-1"></i> {{ $arsip->dokumens_count }}
                                        Dokumen
                                    </p>
                                </div>
                            </div>

                            <div class="col-md-3 mb-3 mb-md-0">
                                <div style="background: #f8f9fc; padding: 12px; border-radius: 6px;">
                                    <small
                                        style="color: #858796; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; display: block; margin-bottom: 6px;">Tanggal
                                        Dibuat</small>
                                    <p class="mb-0" style="color: #5a5c69; font-weight: 700; font-size: 16px;">
                                        <i class="fas fa-calendar-plus text-success mr-1"></i>
                                        {{ $arsip->tanggal_dibuat ? \Carbon\Carbon::parse($arsip->tanggal_dibuat)->format('d/m/Y') : 'Invalid Date' }}
                                    </p>
                                </div>
                            </div>

                            <div class="col-md-3 mb-3 mb-md-0">
                                <div style="background: #f8f9fc; padding: 12px; border-radius: 6px;">
                                    <small
                                        style="color: #858796; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; display: block; margin-bottom: 6px;">Masa
                                        Retensi</small>
                                    <p class="mb-0" style="color: #5a5c69; font-weight: 700; font-size: 16px;">
                                        <i class="fas fa-clock text-info mr-1"></i> {{ $arsip->masa_retensi }} Tahun
                                    </p>
                                </div>
                            </div>

                            <div class="col-md-3 mb-3 mb-md-0">
                                <div style="background: #f8f9fc; padding: 12px; border-radius: 6px;">
                                    <small
                                        style="color: #858796; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; display: block; margin-bottom: 6px;">Berakhir
                                        Pada</small>
                                    <p class="mb-0" style="color: #5a5c69; font-weight: 700; font-size: 16px;">
                                        <i class="fas fa-calendar-times text-danger mr-1"></i>
                                        {{ $arsip->berakhir_pada ? \Carbon\Carbon::parse($arsip->berakhir_pada)->format('d/m/Y') : 'Invalid Date' }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        {{-- Progress Bar --}}
                        <div class="mt-3" style="padding-top: 16px;">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <small style="color: #858796; font-weight: 600; font-size: 12px;">
                                    <i class="fas fa-chart-line mr-1"></i> Progress Masa Retensi
                                </small>
                                <span
                                    style="display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 700; background: {{ $sisaHari >= 0 ? '#fee' : ($sisaHari > -30 ? '#fff3cd' : '#d4edda') }}; color: {{ $sisaHari >= 0 ? '#e74a3b' : ($sisaHari > -30 ? '#f6c23e' : '#1cc88a') }};">
                                    @if ($sisaHari >= 0)
                                        <i class="fas fa-exclamation-circle mr-1"></i> Kadaluarsa {{ abs($sisaHari) }}
                                        hari yang lalu
                                    @else
                                        <i class="fas fa-check-circle mr-1"></i> {{ abs($sisaHari) }} hari tersisa
                                    @endif
                                </span>
                            </div>
                            <div class="progress" style="height: 10px; border-radius: 10px; background: #e3e6f0;">
                                <div class="progress-bar"
                                    style="background: linear-gradient(90deg, {{ $sisaHari >= 0 ? '#e74a3b, #c0392b' : ($sisaHari > -30 ? '#f6c23e, #d4a017' : '#1cc88a, #13855c') }}); border-radius: 10px; width: {{ $percentage }}%;"
                                    role="progressbar" aria-valuenow="{{ $percentage }}" aria-valuemin="0"
                                    aria-valuemax="100">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                        <p class="text-muted mb-0">Tidak ada arsip aktif</p>
                    </div>
                </div>
            </div>
        @endforelse
    </div>

</div>
