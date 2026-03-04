<!-- Sync Modal -->
<div class="modal fade" id="syncModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Sinkronisasi Aktivitas Bulan Ini</h5>

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="px-4 pt-2">
                <p class="fs-3 fw-bold text-primary">
                    <strong>Target Jam Aktivitas:
                        <span id="targetHours">-</span>
                    </strong>
                    <br>
                    <strong>Realisasi Jam Aktivitas:
                        <span id="totalHours">-</span>
                    </strong>
                    <br>
                    <strong>Pencapaian:
                        <span id="completionRate">-</span>
                    </strong>
                </p>
            </div>
            <div class="modal-body overflow-auto" style="max-height: 60vh;">
                <div class="d-flex justify-content-end mb-2 me-3">
                    <div class="form-check form-check-inline">
                        <label class="form-check-label text-muted small me-2" for="selectAllCheckbox">
                            Pilih Semua Aktivitas
                        </label>
                        &nbsp;
                        <input class="form-check-input" type="checkbox" id="selectAllCheckbox">
                    </div>
                </div>
                <ul id="activityList" class="list-group">
                    <!-- populated via jQuery -->
                </ul>
            </div>
            <div class="modal-footer">
                <form id="syncForm" method="POST" action="{{ route('pegawai.update.sync') }}">
                    @csrf
                    <input type="hidden" name="month" id="hiddenMonth">
                    <div id="selectedAktivitas"></div> {{-- optional dynamic field preview --}}
                    <button type="submit" class="btn btn-success">Sync Data Terpilih</button>
                </form>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
