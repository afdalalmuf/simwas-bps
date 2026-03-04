{{-- Modal Detail Pengajuan Peminjaman --}}
<div class="modal fade" id="modalDetailPeminjaman" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 480px;">
        <div class="modal-content"
            style="border-radius: 12px; overflow: hidden; border: none; box-shadow: 0 10px 40px rgba(0,0,0,0.2);">

            {{-- Header --}}
            <div class="modal-header border-0 pb-0 px-4 pt-4">
                <div class="d-flex justify-content-between align-items-start w-100">
                    <div>
                        {{-- Badge status --}}
                        <span id="modal-badge-status" class="badge mb-2"
                            style="padding: 6px 14px; border-radius: 20px; font-size: 12px; font-weight: 700;"></span>
                        <h5 class="modal-title mb-0" style="color: #2d3748; font-weight: 700;">
                            Detail Pengajuan Peminjaman
                        </h5>
                    </div>
                    <div class="d-flex flex-column align-items-end">
                        <button type="button" class="close mb-2" data-dismiss="modal" style="font-size: 20px;">
                            <span>&times;</span>
                        </button>
                        <small id="modal-id-tampil" class="text-muted"
                            style="font-size: 12px; font-weight: 600;"></small>
                    </div>
                </div>
            </div>

            <div class="modal-body px-4 pb-0">

                {{-- Informasi Peminjam --}}
                <div class="mb-3 p-3" style="background: #f8f9fc; border-radius: 10px;">
                    <p class="mb-2"
                        style="font-size: 12px; font-weight: 700; color: #858796; text-transform: uppercase; letter-spacing: 0.5px;">
                        Informasi Peminjam
                    </p>
                    <div class="row">
                        <div class="col-6">
                            <small class="text-muted d-block">Nama Lengkap</small>
                            <strong id="modal-nama" style="color: #2d3748; font-size: 14px;"></strong>
                        </div>
                        <div class="col-6">
                            <small class="text-muted d-block">NIP</small>
                            <strong id="modal-nip" style="color: #2d3748; font-size: 14px;"></strong>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-6">
                            <small class="text-muted d-block">Unit Kerja</small>
                            <strong id="modal-unit" style="color: #2d3748; font-size: 14px;"></strong>
                        </div>
                        <div class="col-6">
                            <small class="text-muted d-block">Tanggal Pengajuan</small>
                            <strong id="modal-tanggal" style="color: #2d3748; font-size: 14px;"></strong>
                        </div>
                    </div>
                </div>

                {{-- Arsip yang Dipinjam --}}
                <div class="mb-3 p-3" style="background: #f8f9fc; border-radius: 10px;">
                    <p class="mb-2"
                        style="font-size: 12px; font-weight: 700; color: #858796; text-transform: uppercase; letter-spacing: 0.5px;">
                        Arsip yang Dipinjam
                    </p>
                    <strong id="modal-arsip-id" style="color: #0069d9; font-size: 18px;"></strong>
                    <small id="modal-arsip-judul" class="text-muted d-block mt-1"></small>
                </div>

                {{-- Alasan Peminjaman --}}
                <div class="mb-3">
                    <p class="mb-1"
                        style="font-size: 12px; font-weight: 700; color: #858796; text-transform: uppercase; letter-spacing: 0.5px;">
                        Alasan Peminjaman
                    </p>
                    <p id="modal-alasan" class="mb-0" style="color: #4a5568; font-size: 14px;"></p>
                </div>

                {{-- Info persetujuan (tampil jika disetujui) --}}
                <div id="modal-info-disetujui" class="mb-3 p-3 d-none"
                    style="background: #d4edda; border-radius: 10px; border: 1px solid #c3e6cb;">
                    <div class="d-flex align-items-center mb-1">
                        <i class="fas fa-check-circle text-success mr-2"></i>
                        <strong style="color: #155724; font-size: 14px;">Pengajuan Disetujui</strong>
                    </div>
                    <small class="text-muted d-block">Disetujui pada: <strong id="modal-tgl-disetujui"></strong></small>
                    <small class="text-muted d-block">Berakhir pada: <strong id="modal-tgl-berakhir"></strong></small>
                </div>

                {{-- Info penolakan (tampil jika ditolak) --}}
                <div id="modal-info-ditolak" class="mb-3 p-3 d-none"
                    style="background: #fde8e7; border-radius: 10px; border: 1px solid #f5c6cb;">
                    <div class="d-flex align-items-center mb-1">
                        <i class="fas fa-times-circle text-danger mr-2"></i>
                        <strong style="color: #721c24; font-size: 14px;">Pengajuan Ditolak</strong>
                    </div>
                    <small class="text-muted d-block">Alasan: <span id="modal-alasan-tolak"
                            class="text-danger"></span></small>
                </div>

                {{-- Form alasan penolakan (tampil saat menekan tolak) --}}
                <div id="form-alasan-tolak" class="mb-3 d-none">
                    <label style="font-weight: 600; font-size: 13px; color: #5a5c69;">
                        Alasan Penolakan <span class="text-danger">*</span>
                    </label>
                    <textarea id="input-alasan-tolak" rows="3" class="form-control" placeholder="Masukkan alasan penolakan..."
                        style="border-radius: 8px; resize: none; font-size: 13px;"></textarea>
                </div>

            </div>

            {{-- Footer Tombol --}}
            <div class="modal-footer border-0 px-4 pb-4 pt-2" id="modal-footer-aksi">
                {{-- Diisi dinamis oleh JS --}}
            </div>

        </div>
    </div>
</div>
