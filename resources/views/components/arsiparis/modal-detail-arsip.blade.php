<!-- MODAL DETAIL ARSIP -->
<div class="modal fade" id="detailModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content" style="border-radius: 12px; border: none;">

            <!-- HEADER -->
            <div class="modal-header border-0"
                style="background: linear-gradient(135deg, #0069d9 0%, #004a99 100%); padding: 24px; border-radius: 12px 12px 0 0;">
                <div style="flex: 1;">
                    <h5 class="mb-2" style="color: white; font-weight: 700; font-size: 18px;" id="detail-kode-arsip">
                        ARS-</h5>
                    <h6 class="mb-3" style="color: rgba(255,255,255,0.9); font-size: 16px;" id="detail-judul"></h6>
                    <div>
                        <span class="mr-2"
                            style="display: inline-block; padding: 6px 14px; border-radius: 20px; font-size: 11px; font-weight: 700; background: rgba(255,255,255,0.3); color: white; backdrop-filter: blur(10px);"
                            id="detail-status"></span>
                        <span
                            style="display: inline-block; padding: 6px 14px; border-radius: 20px; font-size: 11px; font-weight: 700; background: rgba(255,255,255,0.3); color: white; backdrop-filter: blur(10px);"
                            id="detail-skkaa"></span>
                    </div>
                </div>

                <button type="button" class="close" data-dismiss="modal"
                    style="color: white; opacity: 1; text-shadow: none; font-size: 28px;">
                    <span>&times;</span>
                </button>
            </div>

            <!-- BODY -->
            <div class="modal-body" style="padding: 24px; background: #f8f9fc;">

                <!-- URAIAN -->
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 10px; border-left: 4px solid #0069d9;">
                    <div class="card-body" style="padding: 20px;">
                        <small
                            style="color: #858796; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; display: block; margin-bottom: 10px;">
                            <i class="fas fa-align-left mr-2"></i> Uraian
                        </small>
                        <p class="mb-0" style="color: #5a5c69; line-height: 1.6;" id="detail-uraian"></p>
                    </div>
                </div>

                <!-- INFORMASI ARSIP -->
                <div class="row mb-4">
                    <div class="col-md-6 mb-3">
                        <div class="card border-0 shadow-sm" style="border-radius: 10px;">
                            <div class="card-body" style="padding: 16px;">
                                <small
                                    style="color: #858796; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; display: block; margin-bottom: 8px;">
                                    <i class="fas fa-tags mr-2" style="color: #0069d9;"></i> Kode Klasifikasi
                                </small>
                                <div style="color: #5a5c69; font-weight: 700; font-size: 16px;" id="detail-kode">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <div class="card border-0 shadow-sm" style="border-radius: 10px;">
                            <div class="card-body" style="padding: 16px;">
                                <small
                                    style="color: #858796; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; display: block; margin-bottom: 8px;">
                                    <i class="fas fa-building mr-2 text-success"></i> Unit Cipta
                                </small>
                                <div style="color: #5a5c69; font-weight: 700; font-size: 16px;" id="detail-unit">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <div class="card border-0 shadow-sm" style="border-radius: 10px;">
                            <div class="card-body" style="padding: 16px;">
                                <small
                                    style="color: #858796; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; display: block; margin-bottom: 8px;">
                                    <i class="fas fa-file-alt mr-2 text-info"></i> Jumlah Dokumen
                                </small>
                                <div style="color: #5a5c69; font-weight: 700; font-size: 16px;" id="detail-jumlah">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <div class="card border-0 shadow-sm" style="border-radius: 10px;">
                            <div class="card-body" style="padding: 16px;">
                                <small
                                    style="color: #858796; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; display: block; margin-bottom: 8px;">
                                    <i class="fas fa-clock mr-2 text-warning"></i> Masa Retensi
                                </small>
                                <div style="color: #5a5c69; font-weight: 700; font-size: 16px;" id="detail-retensi">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- DAFTAR DOKUMEN -->
                <div class="mb-3">
                    <h6 style="color: #5a5c69; font-weight: 700; font-size: 16px; margin-bottom: 16px;">
                        <i class="fas fa-file-invoice mr-2"></i> Daftar Dokumen
                    </h6>
                </div>
                <div class="list-group" id="daftar-dokumen" style="border-radius: 10px; overflow: hidden;"></div>

            </div>
        </div>
    </div>
</div>
