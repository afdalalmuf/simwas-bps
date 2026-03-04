<form
    action="{{ isset($editArsip) ? route('arsiparis.kelola-arsip.update', $editArsip->id) : route('arsiparis.kelola-arsip.store') }}"
    method="POST" enctype="multipart/form-data">
    @csrf
    @if (isset($editArsip))
        @method('PUT')
    @endif

    <div class="row">

        {{-- ===== KIRI: FORM INPUT ===== --}}
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">

                    @if (isset($editArsip))
                        <div class="alert alert-info mb-3">
                            <i class="fas fa-info-circle mr-2"></i>
                            Anda sedang melengkapi arsip draft: <strong>{{ $editArsip->judul_berkas }}</strong>
                        </div>
                    @endif

                    {{-- BARIS 1: Kode Klasifikasi & SKKAA --}}
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Kode Klasifikasi <span class="text-danger">*</span></label>
                            <input type="text" id="kode_klasifikasi" name="kode_klasifikasi" class="form-control"
                                value="{{ old('kode_klasifikasi', $editArsip->kode_klasifikasi ?? '') }}"
                                placeholder="Contoh: PW.120">
                        </div>

                        <div class="form-group col-md-6">
                            <label>SKKAA (Sifat Keamanan) <span class="text-danger">*</span></label>
                            <select id="skkaa" name="skkaa" class="form-control">
                                <option value="">-- Pilih --</option>
                                <option value="BIASA"
                                    {{ old('skkaa', $editArsip->skkaa ?? '') == 'BIASA' ? 'selected' : '' }}>Biasa
                                </option>
                                <option value="TERBATAS"
                                    {{ old('skkaa', $editArsip->skkaa ?? '') == 'TERBATAS' ? 'selected' : '' }}>Terbatas
                                </option>
                                <option value="RAHASIA"
                                    {{ old('skkaa', $editArsip->skkaa ?? '') == 'RAHASIA' ? 'selected' : '' }}>Rahasia
                                </option>
                            </select>
                        </div>
                    </div>

                    {{-- BARIS 2: Judul Berkas --}}
                    <div class="form-group">
                        <label>Judul Berkas <span class="text-danger">*</span></label>
                        <input type="text" id="judul_berkas" name="judul_berkas" class="form-control"
                            value="{{ old('judul_berkas', $editArsip->judul_berkas ?? '') }}"
                            placeholder="Contoh: Laporan Pengawasan Triwulan I">
                    </div>

                    {{-- BARIS 3: Uraian --}}
                    <div class="form-group">
                        <label>Uraian</label>
                        <textarea id="uraian" name="uraian" class="form-control" rows="8" placeholder="Deskripsi singkat isi arsip">{{ old('uraian', $editArsip->uraian ?? '') }}</textarea>
                    </div>

                    {{-- BARIS 4: Unit Cipta --}}
                    <div class="form-group">
                        <label>Unit Cipta <span class="text-danger">*</span></label>
                        <select id="unit_cipta" name="unit_cipta" class="form-control">
                            <option value="">-- Pilih Unit Cipta --</option>
                            @foreach (['Inspektorat Utama', 'Bagian Umum Inspektorat Utama', 'Inspektorat Wilayah I', 'Inspektorat Wilayah II', 'Inspektorat Wilayah III'] as $unit)
                                <option value="{{ $unit }}"
                                    {{ old('unit_cipta', $editArsip->unit_cipta ?? '') == $unit ? 'selected' : '' }}>
                                    {{ $unit }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- BARIS 5: Masa Retensi --}}
                    <div class="form-group">
                        <label>Masa Retensi (Tahun) <span class="text-danger">*</span></label>
                        <input type="number" id="masa_retensi" name="masa_retensi" class="form-control"
                            value="{{ old('masa_retensi', $editArsip->masa_retensi ?? 0) }}">
                        <small class="text-muted">
                            Arsip akan dapat dinonaktifkan setelah masa retensi berakhir.
                        </small>
                    </div>

                    {{-- BARIS 6: Dokumen yang sudah ada (mode edit) --}}
                    @if (isset($editArsip) && $editArsip->dokumens->count() > 0)
                        <div class="form-group">
                            <label>Dokumen yang Sudah Ada ({{ $editArsip->dokumens->count() }})</label>
                            <div class="border rounded p-3 bg-light mb-3" id="existing-documents">
                                @foreach ($editArsip->dokumens as $dokumen)
                                    @php
                                        $extension = pathinfo($dokumen->nama_file, PATHINFO_EXTENSION);
                                        $iconClass = 'fa-file-alt text-secondary';
                                        if ($extension === 'pdf') {
                                            $iconClass = 'fa-file-pdf text-danger';
                                        } elseif (in_array($extension, ['doc', 'docx'])) {
                                            $iconClass = 'fa-file-word text-primary';
                                        } elseif (in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])) {
                                            $iconClass = 'fa-file-image text-info';
                                        }

                                        $fileUrl = asset('storage/' . $dokumen->path_file);
                                        $fileExists = Storage::disk('public')->exists($dokumen->path_file);
                                    @endphp

                                    <div class="bg-white rounded p-3 mb-2 shadow-sm dokumen-item"
                                        id="dokumen-{{ $dokumen->id }}">
                                        <div class="row align-items-center">
                                            <div class="col-md-6">
                                                <div class="d-flex align-items-start">
                                                    <i class="fas {{ $iconClass }} fa-2x mr-3 mt-1"></i>
                                                    <div>
                                                        <h6 class="mb-1 font-weight-bold">
                                                            {{ $dokumen->judul_dokumen ?: 'Tanpa Judul' }}
                                                        </h6>
                                                        <p class="mb-1 text-muted small">
                                                            <i class="fas fa-file mr-1"></i>{{ $dokumen->nama_file }}
                                                        </p>
                                                        <p class="mb-0 text-muted small">
                                                            <i class="fas fa-hdd mr-1"></i>
                                                            {{ number_format($dokumen->ukuran / 1024, 2) }} KB
                                                            <span class="mx-2">•</span>
                                                            <i class="fas fa-calendar mr-1"></i>
                                                            {{ $dokumen->created_at->format('d M Y') }}
                                                        </p>
                                                        @if (!$fileExists)
                                                            <div class="badge badge-danger mt-1">
                                                                <i class="fas fa-exclamation-triangle"></i> File Tidak
                                                                Ditemukan
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="d-flex justify-content-end">
                                                    @if ($fileExists)
                                                        <button type="button"
                                                            class="btn btn-sm btn-outline-primary mr-2"
                                                            onclick="lihatDokumen('{{ $fileUrl }}', '{{ $dokumen->nama_file }}', '{{ $extension }}')"
                                                            title="Lihat dokumen">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                    @else
                                                        <span class="badge badge-warning mr-2">File Hilang</span>
                                                    @endif
                                                    <button type="button" class="btn btn-sm btn-outline-danger"
                                                        onclick="hapusDokumen({{ $dokumen->id }})"
                                                        title="Hapus dokumen">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="alert alert-info mb-3">
                                <i class="fas fa-info-circle mr-2"></i>
                                <strong>Total: <span id="existing-doc-count">{{ $editArsip->dokumens->count() }}</span>
                                    dokumen</strong>
                            </div>
                        </div>
                    @endif

                    {{-- BARIS 7: Upload Dokumen Baru --}}
                    <div class="form-group">
                        <label>
                            {{ isset($editArsip) ? 'Tambah Dokumen Baru' : 'Dokumen yang Dipilih' }}
                            @if (!isset($editArsip))
                                <span class="text-danger">*</span>
                            @endif
                        </label>

                        <div id="preview-dokumen" class="border rounded p-4 bg-light">
                            <div id="empty-dokumen" class="text-center text-muted">
                                <i class="far fa-file-alt fa-2x mb-2"></i>
                                <p class="mb-0">Belum ada dokumen yang dipilih</p>
                            </div>
                            <div id="list-dokumen" class="d-none"></div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-6 mb-2">
                                <button type="button" class="btn btn-primary btn-block">
                                    <i class="fas fa-list"></i> Pilih dari Daftar Dokumen
                                </button>
                            </div>
                            <div class="col-md-6 mb-2">
                                <input type="file" id="upload-dokumen" name="dokumen[]" multiple class="d-none"
                                    accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                <button type="button" class="btn btn-outline-primary btn-block"
                                    onclick="document.getElementById('upload-dokumen').click()">
                                    <i class="fas fa-upload mr-1"></i> Upload Dokumen Baru
                                </button>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        {{-- ===== KANAN: RINGKASAN ===== --}}
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h4>Ringkasan</h4>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Judul Berkas</span>
                        <strong id="ringkasan-judul">{{ $editArsip->judul_berkas ?? '-' }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Jumlah Dokumen</span>
                        <strong
                            id="ringkasan-jumlah">{{ isset($editArsip) ? $editArsip->dokumens->count() : 0 }}</strong>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Kode Klasifikasi</span>
                        <span id="ringkasan-kode">{{ $editArsip->kode_klasifikasi ?? '-' }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>SKKAA</span>
                        <span id="ringkasan-skkaa"
                            class="badge
                            {{ isset($editArsip) && $editArsip->skkaa === 'BIASA' ? 'badge-success' : '' }}
                            {{ isset($editArsip) && $editArsip->skkaa === 'TERBATAS' ? 'badge-warning' : '' }}
                            {{ isset($editArsip) && $editArsip->skkaa === 'RAHASIA' ? 'badge-danger' : '' }}">
                            {{ $editArsip->skkaa ?? '-' }}
                        </span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Unit Cipta</span>
                        <strong id="ringkasan-unit">{{ $editArsip->unit_cipta ?? '-' }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-4">
                        <span>Masa Retensi</span>
                        <strong id="ringkasan-retensi">{{ $editArsip->masa_retensi ?? 0 }} Tahun</strong>
                    </div>

                    @if (isset($editArsip))
                        <button type="submit" name="action" value="draft"
                            class="btn btn-secondary btn-block mb-2">
                            <i class="fas fa-save"></i> Simpan sebagai Draft
                        </button>
                        <button type="submit" name="action" value="submit" class="btn btn-success btn-block">
                            <i class="fas fa-folder-open"></i> Close File
                        </button>
                    @else
                        <button type="submit" name="action" value="draft"
                            class="btn btn-secondary btn-block mb-2">
                            <i class="fas fa-save"></i> Simpan Draft
                        </button>
                        <button type="submit" name="action" value="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-folder-plus"></i> Buat Arsip
                        </button>
                    @endif
                </div>
            </div>
        </div>

    </div>
</form>
