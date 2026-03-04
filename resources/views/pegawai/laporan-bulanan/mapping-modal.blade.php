<!-- Sync Form Modal -->
<div class="modal fade" id="mappingFormModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="mappingForm" method="POST" action="">
                @csrf
                <input type="hidden" name="month" id="formMonth">
                <input type="hidden" name="year" id="formYear">
                <input type="hidden" name="id_pelaksana" id="formIdPelaksana">
                <input type="hidden" name="start_date" id="formStartDate">
                <input type="hidden" name="end_date" id="formEndDate">
                <div class="modal-header">
                    <h5 class="modal-title">Sinkronisasi ke KipApp</h5>
                    <span id="statusFlag" class="ml-2 badge"></span>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="form-group">
                        <button type="button" class="btn btn-link px-0" id="toggleEventList">
                            <span id="toggleEventListIcon">▼</span> Tampilkan Daftar Aktivitas
                        </button>
                        <ul id="eventList" class="list-group d-none" style="margin-top:10px;">
                            <!-- Populated via jQuery -->
                        </ul>
                    </div>

                    <div class="form-group">
                        <label for="kipapp_rencana">Rencana Kinerja KipApp</label>
                        <select class="form-control select2" name="koderk" id="kipappRencana" required>
                            <option value="" selected disabled>Memuat rencana kinerja...</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="formKegiatan">Kegiatan</label>
                        <textarea class="form-control" name="kegiatan" id="formKegiatan" rows="3" required></textarea>
                    </div>

                    <div class="form-group">
                        <label for="formCapaian">Capaian</label>
                        <textarea class="form-control" name="capaian" id="formCapaian" rows="3" required></textarea>
                    </div>

                    <div class="form-group">
                        <label for="formLink">Link Data Dukung</label>
                        <input type="url" class="form-control" name="link_form" id="formLink"
                            placeholder="https://">
                    </div>
                </div>
                <div class="modal-footer">
                    <button id="submitSync" type="submit" class="btn btn-primary">Simpan</button>
                    {{-- <button id="kipappSync-button" type="button" class="btn btn-success">Kirim</button> --}}
                </div>
            </form>
        </div>
    </div>
</div>
