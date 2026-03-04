<div class="modal fade" id="modal-edit-skp-penilaian" data-backdrop="static" data-keyboard="false" tabindex="-1"
    aria-labelledby="modal-edit-skp-penilaian-label" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-edit-skp-penilaian-label">Form Ubah Unggah SKP Penilaian</h5>
                <button type="button" class="close text-danger" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formEditPenilaian" enctype="multipart/form-data" class="">
                <div class="modal-body">
                    <input type="hidden" id="edit-id-penilaian" name="id">                   
                    <div class="form-group">
                        <label class="form-label" for="tgl_upload">Tanggal Unggah</label>
                        <p id="edit-tgl-upload-penilaian"></p>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="rating_hasil_kerja">Rating Hasil Kerja</label>
                        <div class="">                            
                            <select class="form-control" name="rating_hasil_kerja" id="edit-rating-hasil-penilaian" required>
                                <option value="">Pilih Rating Hasil Kerja</option>
                                @foreach ($rating as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            <small id="error-rating_hasil_kerja" class="text-danger"></small>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="rating_perilaku_kerja">Rating Perilaku Kerja</label>
                        <div class="">                            
                            <select class="form-control" name="rating_perilaku_kerja" id="edit-rating-perilaku-penilaian" required>
                                <option value="">Pilih Rating Perilaku Kerja</option>
                                @foreach ($rating as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            <small id="error-rating_perilaku_kerja" class="text-danger"></small>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="predikat_kinerja">Predikat Kinerja</label>
                        <div class="">
                            <select class="form-control" name="predikat_kinerja" id="edit-predikat-penilaian" required>
                                <option value="">Pilih Predikat Kinerja</option>
                                @foreach ($kategori as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            <small id="error-predikat_kinerja" class="text-danger"></small>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="file">Dokumen SKP Penilaian</label>
                        <div class="">
                            <a id="myLink" target="blank" href="#"
                                class="badge btn-primary"><i class="fa fa-download mr-1"></i>
                                Download</a>
                        </div>
                    </div>
                    <div class="form-group" id="catatanSection" style="display: none;">
                        <label class="form-label" for="rating_perilaku_kerja">Catatan Penilaian</label>
                        <div class="">
                            <textarea id="edit-catatan-penilaian" class="form-control" readonly></textarea>
                        </div>
                    </div>                    
                    <div class="form-group">
                        <label class="form-label" for="file">Berkas SKP Penilaian</label>
                        <div class="">
                            <input type="file" name="file" id="file" class="form-control" accept=".pdf">
                            <small id="error-file" class="text-danger"></small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" id="btn-footer-penilaian">
                    <button type="button" class="btn btn-icon icon-left btn-danger" data-dismiss="modal">
                        <i class="fas fa-exclamation-triangle"></i>Batal
                    </button>
                    <button type="submit" class="btn btn-icon icon-left btn-primary edit-btn-penilaian">
                        <i class="fas fa-save"></i>Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>