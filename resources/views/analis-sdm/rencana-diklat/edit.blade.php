<!-- Edit Modal Container -->
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" id="edit-modal-content">
            <!-- Content loaded dynamically -->
            <form id="form-edit-diklat">
                @csrf

                <div class="modal-header">
                    <h5 class="modal-title">Edit Rencana Diklat</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>

                <div class="modal-body">
                    <input type="hidden" name="id" id="diklat-id">

                    <div class="form-group">
                        <label>Nama Diklat</label>
                        <input type="text" name="name" class="form-control" id="diklat-name" required>
                    </div>

                    <div class="form-group">
                        <label>Pegawai</label>
                        <select name="id_pegawai" class="form-control select2" id="diklat-id-pegawai" required>
                        </select>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Tanggal Mulai</label>
                            <input type="date" name="start_date" class="form-control" id="diklat-start-date"
                                required>
                        </div>

                        <div class="form-group col-md-6">
                            <label>Tanggal Selesai</label>
                            <input type="date" name="end_date" class="form-control" id="diklat-end-date" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Metode</label>
                        <select name="metode" class="form-control select2" id="diklat-metode" required>
                            <option value="" disabled selected>Pilih Metode Diklat</option>
                            <option value="Offline">Offline</option>
                            <option value="PJJ">PJJ</option>
                            <option value="Hybrid">Hybrid</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Penyelenggara</label>
                        <select name="penyelenggara" class="form-control select2" id="diklat-penyelenggara" required>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Biaya Diklat</label>
                        <input type="number" name="biaya" class="form-control" id="diklat-biaya">
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label>Transport</label>
                            <input type="number" name="transport" class="form-control" id="diklat-transport">
                        </div>
                        <div class="form-group col-md-4">
                            <label>Akomodasi</label>
                            <input type="number" name="akomodasi" class="form-control" id="diklat-akomodasi">
                        </div>
                        <div class="form-group col-md-4">
                            <label>Uang Saku</label>
                            <input type="number" name="uang_saku" class="form-control" id="diklat-uang-saku">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Pembebanan Perjadin</label>
                        <select name="pembebanan_perjadin" class="form-control select2" id="diklat-pembebanan" required>
                            <option value="" disabled selected>Pilih Pembebanan Perjadin</option>
                            <option value="_NULL_">Tidak Ada</option>
                            <option value="8100">Inspektorat Wilayah I</option>
                            <option value="8200">Inspektorat Wilayah II</option>
                            <option value="8300">Inspektorat Wilayah III</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Akun Anggaran</label>
                        <select name="akun_anggaran" class="form-control select2" id="diklat-akun-anggaran" required>
                            <option value="" disabled selected>Pilih Akun Perjalanan</option>
                            <option value="_NULL_">Tidak Ada</option>
                            <option value="524111">524111</option>
                            <option value="524113">524113</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" class="form-control select2" id="diklat-status" required>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Keterangan</label>
                        <textarea name="keterangan" class="form-control" id="diklat-keterangan"></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Simpan</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>
