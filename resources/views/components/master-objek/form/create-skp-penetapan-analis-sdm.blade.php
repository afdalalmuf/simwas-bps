<div class="modal fade" id="modal-create-skp-penetapan" data-backdrop="static" data-keyboard="false" tabindex="-1"
    aria-labelledby="modal-create-skp-penetapan-label" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-create-skp-penetapan-label">Form Unggah SKP Tahunan</h5>
                <button type="button" class="close text-danger" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formPenetapanAnalisSDM" enctype="multipart/form-data" class="">
                <div class="modal-body">
                    <input type="hidden" id="create-tahun-penetapan" name="tahun">
                    <input type="hidden" id="create-jenis-penetapan" name="jenis">
                    <input type="hidden" id="create-user-id-penetapan" name="user_id">
                    <div class="form-group">
                        <label class="form-label" for="file">Berkas SKP Tahunan Penetapan <?php echo $tahun; ?></label>
                        <div class="">
                            <input type="file" name="file" id="file" class="form-control" accept=".pdf"
                                required>
                            <small>*Jika lebih dari satu dokumen SKP, maka dua dokumen SKP digabung menjadi satu untuk diunggah</small>
                            <small id="error-file" class="text-danger"></small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-icon icon-left btn-danger" data-dismiss="modal">
                        <i class="fas fa-exclamation-triangle"></i>Batal
                    </button>
                    <button type="submit" class="btn btn-icon icon-left btn-primary submit-btn">
                        <i class="fas fa-save"></i>Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
