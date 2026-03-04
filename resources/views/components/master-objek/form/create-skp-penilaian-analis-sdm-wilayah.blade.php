<div class="modal fade" id="modal-create-skp-penilaian-wilayah" data-backdrop="static" data-keyboard="false" tabindex="-1"
    aria-labelledby="modal-create-skp-penilaian-wilayah-label" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-create-skp-penilaian-wilayah-label">Form Unggah SKP Penilaian</h5>
                <button type="button" class="close text-danger" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formPenilaianAnalisSDMWilayah" enctype="multipart/form-data" class="">
                <div class="modal-body">
                    <input type="hidden" id="create-tahun-penilaian-wilayah" name="tahun">
                    <input type="hidden" id="create-jenis-penilaian-wilayah" name="jenis">
                    <input type="hidden" id="create-bulan-penilaian-wilayah" name="bulan">
                    <input type="hidden" id="create-user-id-penilaian-wilayah" name="user_id">                    
                    <div class="form-group">
                        <label class="form-label" for="rating_hasil_kerja">Rating Hasil Kerja</label>
                        <div class="">
                            <select class="form-control" name="rating_hasil_kerja" required>
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
                            <select class="form-control" name="rating_perilaku_kerja" required>
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
                            <select class="form-control" name="predikat_kinerja" required>
                                <option value="">Pilih Predikat Kinerja</option>
                                @foreach ($kategori as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            <small id="error-predikat_kinerja" class="text-danger"></small>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="file">Berkas SKP Tahunan Penilaian</label>
                        <div class="">
                            <input type="file" name="file" id="file" class="form-control" accept=".pdf"
                                required>
                            <small id="error-file" class="text-danger"></small>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-icon icon-left btn-danger" data-dismiss="modal">
                        <i class="fas fa-exclamation-triangle"></i>Batal
                    </button>
                    <button type="submit" id="btn-edit-submit"
                        class="btn btn-icon icon-left btn-primary submit-btn-penilaian-wilayah">
                        <i class="fas fa-save"></i>Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
