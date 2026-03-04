<div class="modal fade" id="modal-edit-skp-penetapan" data-backdrop="static" data-keyboard="false" tabindex="-1"
    aria-labelledby="modal-edit-skp-penetapan-label" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-edit-skp-penetapan-label">Form Ubah SKP Tahunan</h5>
                <button type="button" class="close text-danger" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formEditPenetapan" enctype="multipart/form-data" class="">
                <div class="modal-body">
                    <input type="hidden" id="id-skp">
                    <div class="form-group">
                        <label class="form-label" for="tgl_upload">Tanggal Unggah</label>
                        <p id="edit-tgl-upload-penetapan"></p>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="file">Dokumen SKP Tahunan Penetapan <?php echo $tahun ?></label>
                        <div class="">
                            <a target="blank" href="upload-skp/viewSKP/<?php echo $skp_penetapan->id ?>"
                                class="badge btn-primary"><i class="fa fa-download mr-1"></i>
                                Download</a>
                        </div>
                    </div>
                    <div class="form-group" id="catatanPenetapan" style="display: none;">
                        <label class="form-label" for="catatan">Catatan Penetapan</label>
                        <div class="">
                            <textarea id="edit-catatan-penetapan" class="form-control" readonly></textarea>
                        </div>
                    </div> 
                    <div class="form-group">
                        <label class="form-label" for="file">Berkas SKP Tahunan Penetapan <?php echo $tahun ?></label>
                        <div class="">
                            <input type="file" name="file" id="file" class="form-control" accept=".pdf" required>
                            <small>*Jika lebih dari satu dokumen SKP, maka dua dokumen SKP digabung menjadi satu untuk diunggah</small>
                            <small id="error-file" class="text-danger"></small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" id="btn-footer-penetapan">
                    <button type="button" class="btn btn-icon icon-left btn-danger" data-dismiss="modal">
                        <i class="fas fa-exclamation-triangle"></i>Batal
                    </button>
                    <button type="submit" id="btn-edit-submit" class="btn btn-icon icon-left btn-primary update-btn">
                        <i class="fas fa-save"></i>Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>