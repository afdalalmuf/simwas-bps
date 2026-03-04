<!-- Activity Detail Modal -->
<div class="modal fade" id="syncModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Aktivitas Rencana Kerja</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="px-4 pt-2">
                <p class="fs-5 fw-bold text-primary mb-1">
                    <strong>Target Jam Aktivitas:
                        <span id="targetHours">-</span>
                    </strong>
                </p>
                <p class="fs-5 fw-bold text-primary mb-1">
                    <strong>Realisasi Jam Aktivitas:
                        <span id="totalHours">-</span>
                    </strong>
                </p>
                <p class="fs-5 fw-bold">
                    <strong>Pencapaian:
                        <span id="completionRate">-</span>
                    </strong>
                </p>
            </div>

            <div class="modal-body overflow-auto" style="max-height: 60vh;">
                <ul id="activityList" class="list-group">
                    <li class="list-group-item text-muted">Memuat data...</li>
                </ul>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>