<div class="modal fade" id="modal-create-skp-penilaian" data-backdrop="static" data-keyboard="false" tabindex="-1"
    aria-labelledby="modal-create-skp-penilaian-label" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-create-skp-penilaian-label">Form Unggah SKP Penilaian</h5>
                <button type="button" class="close text-danger" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formPenilaianAnalisSDM" enctype="multipart/form-data" class="">
                <div class="modal-body">
                    <input type="hidden" id="create-tahun-penilaian" name="tahun">
                    <input type="hidden" id="create-jenis-penilaian" name="jenis">
                    <input type="hidden" id="create-bulan-penilaian" name="bulan">
                    <input type="hidden" id="create-user-id-penilaian" name="user_id">
                    <div class="form-group">
                        <label class="form-label d-block">Status SKP Bulanan</label>
                        <label class="custom-switch">
                            <input type="checkbox" name="status_skp" class="custom-switch-input" id="statusToggle"
                                value="aktif">
                            <span class="custom-switch-indicator"></span>
                            <span class="custom-switch-description">Tidak Aktif</span>
                        </label>
                    </div>

                    {{-- Form lainnya dibungkus --}}
                    <div id="formContainer">
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
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-icon icon-left btn-danger" data-dismiss="modal">
                        <i class="fas fa-exclamation-triangle"></i>Batal
                    </button>
                    <button type="submit" id="btn-edit-submit"
                        class="btn btn-icon icon-left btn-primary submit-btn-penilaian">
                        <i class="fas fa-save"></i>Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
