/**
 * kelola-arsip.js
 * Halaman: Arsiparis > Kelola Arsip
 *
 * Sections:
 *  1.  State
 *  2.  Inisialisasi & restore form (DOMContentLoaded)
 *  3.  Form persistence (localStorage)
 *  4.  Upload & manajemen file
 *  5.  Render daftar file
 *  6.  Filter daftar arsip
 *  7.  Modal: Detail arsip
 *  8.  Modal: Preview dokumen
 *  9.  Hapus dokumen (yang sudah tersimpan)
 *  10. Submit form (loading overlay + clear storage)
 */

/* ==========================================================================
   1. STATE
   ========================================================================== */

let fileBuffer = new DataTransfer();

// Key localStorage berbeda antara mode create dan mode edit
const _isEditMode = () => !!document.querySelector('input[name="_method"][value="PUT"]');
const STORAGE_KEY = () => _isEditMode() ? 'kelola_arsip_edit' : 'kelola_arsip_create';

/* ==========================================================================
   2. INISIALISASI & RESTORE FORM
   ========================================================================== */

document.addEventListener('DOMContentLoaded', function () {
    const kode    = document.getElementById('kode_klasifikasi');
    const judul   = document.getElementById('judul_berkas');
    const uraian  = document.getElementById('uraian');
    const skkaa   = document.getElementById('skkaa');
    const unit    = document.getElementById('unit_cipta');
    const retensi = document.getElementById('masa_retensi');

    // Hanya jalankan jika elemen form ada
    if (!kode) return;

    // Restore isian dari localStorage
    _restoreForm({ kode, judul, uraian, skkaa, unit, retensi });

    // Update semua ringkasan setelah restore
    _updateSemuaRingkasan({ kode, judul, skkaa, unit, retensi });

    // Live update ringkasan + auto-save saat user mengetik
    kode.addEventListener('input', () => {
        document.getElementById('ringkasan-kode').innerText = kode.value || '-';
        _saveForm();
    });
    judul.addEventListener('input', () => {
        document.getElementById('ringkasan-judul').innerText = judul.value || '-';
        _saveForm();
    });
    uraian.addEventListener('input', _saveForm);
    unit.addEventListener('change', () => {
        document.getElementById('ringkasan-unit').innerText = unit.value || '-';
        _saveForm();
    });
    retensi.addEventListener('input', () => {
        document.getElementById('ringkasan-retensi').innerText = (retensi.value || 0) + ' Tahun';
        _saveForm();
    });
    skkaa.addEventListener('change', () => {
        _updateBadgeSkkaa(document.getElementById('ringkasan-skkaa'), skkaa.value);
        _saveForm();
    });

    // Tombol reset: hapus storage juga
    const resetBtn = document.querySelector('button[type="reset"], input[type="reset"]');
    if (resetBtn) {
        resetBtn.addEventListener('click', () => {
            localStorage.removeItem(STORAGE_KEY());
            setTimeout(() => _updateSemuaRingkasan({ kode, judul, skkaa, unit, retensi }), 50);
        });
    }
});

/* ==========================================================================
   3. FORM PERSISTENCE (localStorage)
   ========================================================================== */

/**
 * Simpan semua nilai field teks ke localStorage
 */
function _saveForm() {
    const val = (id) => document.getElementById(id)?.value ?? '';

    localStorage.setItem(STORAGE_KEY(), JSON.stringify({
        kode_klasifikasi : val('kode_klasifikasi'),
        judul_berkas     : val('judul_berkas'),
        uraian           : val('uraian'),
        skkaa            : val('skkaa'),
        unit_cipta       : val('unit_cipta'),
        masa_retensi     : val('masa_retensi'),
    }));
}

/**
 * Restore nilai form dari localStorage.
 * Catatan: file input tidak bisa dipulihkan (browser security),
 * hanya field teks/select yang dipulihkan.
 */
function _restoreForm({ kode, judul, uraian, skkaa, unit, retensi }) {
    const raw = localStorage.getItem(STORAGE_KEY());
    if (!raw) return;

    let data;
    try {
        data = JSON.parse(raw);
    } catch {
        localStorage.removeItem(STORAGE_KEY());
        return;
    }

    // Overwrite hanya jika ada nilai tersimpan
    if (data.kode_klasifikasi) kode.value    = data.kode_klasifikasi;
    if (data.judul_berkas)     judul.value   = data.judul_berkas;
    if (data.uraian)           uraian.value  = data.uraian;
    if (data.skkaa)            skkaa.value   = data.skkaa;
    if (data.unit_cipta)       unit.value    = data.unit_cipta;
    if (data.masa_retensi)     retensi.value = data.masa_retensi;

    _tampilkanBannerRestore();
}

/**
 * Tampilkan banner pemberitahuan bahwa isian form dipulihkan
 */
function _tampilkanBannerRestore() {
    const form = document.querySelector('form');
    if (!form || document.getElementById('restore-banner')) return;

    const banner       = document.createElement('div');
    banner.id          = 'restore-banner';
    banner.className   = 'alert alert-info alert-dismissible fade show mb-3';
    banner.innerHTML   = `
        <i class="fas fa-history mr-2"></i>
        <strong>Isian form dipulihkan</strong> dari sesi sebelumnya.
        <button type="button" class="btn btn-sm btn-outline-danger ml-2" onclick="_hapusIsianForm()">
            <i class="fas fa-trash mr-1"></i> Hapus & Mulai Baru
        </button>
        <button type="button" class="close" data-dismiss="alert">
            <span>&times;</span>
        </button>`;

    // Sisipkan di paling atas form (setelah alert info edit jika ada)
    const alertEdit = form.querySelector('.alert-info');
    if (alertEdit) {
        alertEdit.after(banner);
    } else {
        form.insertBefore(banner, form.firstChild);
    }
}

/**
 * Hapus storage dan kosongkan semua field
 */
function _hapusIsianForm() {
    localStorage.removeItem(STORAGE_KEY());

    ['kode_klasifikasi', 'judul_berkas', 'uraian', 'skkaa', 'unit_cipta', 'masa_retensi']
        .forEach(id => {
            const el = document.getElementById(id);
            if (el) el.value = '';
        });

    _updateSemuaRingkasan({
        kode    : document.getElementById('kode_klasifikasi'),
        judul   : document.getElementById('judul_berkas'),
        skkaa   : document.getElementById('skkaa'),
        unit    : document.getElementById('unit_cipta'),
        retensi : document.getElementById('masa_retensi'),
    });

    document.getElementById('restore-banner')?.remove();
}

/**
 * Helper: update semua teks ringkasan sekaligus
 */
function _updateSemuaRingkasan({ kode, judul, skkaa, unit, retensi }) {
    document.getElementById('ringkasan-kode').innerText    = kode.value    || '-';
    document.getElementById('ringkasan-judul').innerText   = judul.value   || '-';
    document.getElementById('ringkasan-unit').innerText    = unit.value    || '-';
    document.getElementById('ringkasan-retensi').innerText = (retensi.value || 0) + ' Tahun';
    _updateBadgeSkkaa(document.getElementById('ringkasan-skkaa'), skkaa.value);
}

/**
 * Helper: update warna badge SKKAA
 */
function _updateBadgeSkkaa(badgeEl, value) {
    if (!badgeEl) return;
    badgeEl.innerText  = value || '-';
    badgeEl.className  = 'badge';
    if (value === 'BIASA')    badgeEl.classList.add('badge-success');
    if (value === 'TERBATAS') badgeEl.classList.add('badge-warning');
    if (value === 'RAHASIA')  badgeEl.classList.add('badge-danger');
}

/* ==========================================================================
   4. UPLOAD & MANAJEMEN FILE
   ========================================================================== */

const uploadInput = document.getElementById('upload-dokumen');

if (uploadInput) {
    uploadInput.addEventListener('change', function () {
        const MAX_TOTAL = 50 * 1024 * 1024; // 50MB
        let totalSize   = 0;

        for (const file of this.files) totalSize += file.size;

        if (totalSize > MAX_TOTAL) {
            alert('Total ukuran file tidak boleh lebih dari 50MB!');
            this.value = '';
            return;
        }

        _tambahFileBaru(this.files);
    });
}

function _tambahFileBaru(newFiles) {
    const newBuffer      = new DataTransfer();
    let   duplicateFound = false;

    Array.from(fileBuffer.files).forEach(f => newBuffer.items.add(f));
    Array.from(newFiles).forEach(f => {
        if (_isDuplicate(f, fileBuffer.files)) {
            duplicateFound = true;
        } else {
            newBuffer.items.add(f);
        }
    });

    if (duplicateFound) {
        Swal.fire({
            toast: true, position: 'bottom-end', icon: 'warning',
            title: 'Dokumen duplikat tidak ditambahkan',
            showConfirmButton: false, timer: 3500, timerProgressBar: true,
            background: '#fdecea', color: '#856404', iconColor: '#dc3545',
        });
    }

    fileBuffer        = newBuffer;
    uploadInput.files = fileBuffer.files;
    renderFileList();
}

function _isDuplicate(newFile, existingFiles) {
    return Array.from(existingFiles).some(f =>
        f.name === newFile.name && f.size === newFile.size && f.lastModified === newFile.lastModified
    );
}

function removeFile(index) {
    const newBuffer = new DataTransfer();
    Array.from(fileBuffer.files).forEach((f, i) => { if (i !== index) newBuffer.items.add(f); });
    fileBuffer = newBuffer;
    uploadInput.files = fileBuffer.files;
    renderFileList();
}

function triggerReplace(index) {
    document.getElementById(`replace-file-${index}`).click();
}

function replaceFile(event, index) {
    const newFile = event.target.files[0];
    if (!newFile) return;

    const filesExcept = Array.from(fileBuffer.files).filter((_, i) => i !== index);
    if (_isDuplicate(newFile, filesExcept)) {
        Swal.fire({
            toast: true, position: 'bottom-end', icon: 'warning',
            title: 'Dokumen duplikat tidak ditambahkan',
            showConfirmButton: false, timer: 3500, timerProgressBar: true,
            background: '#fdecea', color: '#856404', iconColor: '#dc3545',
        });
        event.target.value = '';
        return;
    }

    const newBuffer = new DataTransfer();
    Array.from(fileBuffer.files).forEach((f, i) => newBuffer.items.add(i === index ? newFile : f));
    fileBuffer = newBuffer;
    uploadInput.files = fileBuffer.files;
    renderFileList();
}

/* ==========================================================================
   5. RENDER DAFTAR FILE (PREVIEW)
   ========================================================================== */

function renderFileList() {
    _updateRingkasanJumlah();

    const emptyState = document.getElementById('empty-dokumen');
    const list       = document.getElementById('list-dokumen');
    if (!list) return;

    list.innerHTML = '';

    if (fileBuffer.files.length === 0) {
        emptyState.classList.remove('d-none');
        list.classList.add('d-none');
        return;
    }

    emptyState.classList.add('d-none');
    list.classList.remove('d-none');

    Array.from(fileBuffer.files).forEach((file, index) => {
        list.innerHTML += `
            <div class="border rounded p-2 mb-2 bg-white">
                <div class="form-row align-items-center">
                    <div class="col-md-4">
                        <input type="text" name="judul_dokumen[]"
                            class="form-control form-control-sm" placeholder="Judul dokumen">
                    </div>
                    <div class="col-md-1 text-center">
                        <button type="button" class="btn btn-sm btn-outline-primary"
                            onclick="triggerReplace(${index})" title="Ganti file">
                            <i class="fas fa-file-upload"></i>
                        </button>
                        <input type="file" class="d-none" id="replace-file-${index}"
                            onchange="replaceFile(event, ${index})">
                    </div>
                    <div class="col-md-5 small d-flex align-items-center" style="overflow:hidden;" title="${file.name}">
                        <i class="fas ${_getFileIcon(file.type)} mr-2 flex-shrink-0"></i>
                        <span class="text-truncate d-inline-block" style="max-width:260px;">${file.name}</span>
                        <span class="ml-3 text-muted small flex-shrink-0">${_formatFileSize(file.size)}</span>
                    </div>
                    <div class="col-md-2 text-right">
                        <button type="button" class="btn btn-sm btn-light text-danger"
                            onclick="removeFile(${index})" title="Hapus">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>`;
    });

    uploadInput.files = fileBuffer.files;
}

function _getFileIcon(mimeType) {
    if (mimeType.includes('pdf'))   return 'fa-file-pdf text-danger';
    if (mimeType.includes('image')) return 'fa-file-image text-info';
    if (mimeType.includes('word'))  return 'fa-file-word text-primary';
    return 'fa-file-alt text-secondary';
}
// Versi berdasarkan ekstensi (dipakai di modal detail karena data dari API bukan File object)
function _getDetailFileIcon(ext) {
    if (ext === 'pdf')                                              return 'fa-file-pdf text-danger';
    if (['jpg','jpeg','png','gif','bmp','webp'].includes(ext))      return 'fa-file-image text-info';
    if (['doc','docx'].includes(ext))                               return 'fa-file-word text-primary';
    if (['xls','xlsx'].includes(ext))                               return 'fa-file-excel text-success';
    return 'fa-file-alt text-secondary';
}

function _formatFileSize(bytes) {
    const kb = (bytes / 1024).toFixed(1);
    return kb > 1024 ? (kb / 1024).toFixed(2) + ' MB' : kb + ' KB';
}

function _updateRingkasanJumlah() {
    const total = document.querySelectorAll('.dokumen-item').length + fileBuffer.files.length;
    const el    = document.getElementById('ringkasan-jumlah');
    if (el) el.innerText = total;
}

/* ==========================================================================
   6. FILTER DAFTAR ARSIP
   ========================================================================== */

const searchInput  = document.getElementById('searchArsip');
const filterStatus = document.getElementById('filterStatus');
const filterSKKAA  = document.getElementById('filterSKKAA');

if (searchInput)  searchInput.addEventListener('input',   () => { _currentPage = 1; filterArsip(); });
if (filterStatus) filterStatus.addEventListener('change', () => { _currentPage = 1; filterArsip(); });
if (filterSKKAA)  filterSKKAA.addEventListener('change',  () => { _currentPage = 1; filterArsip(); });

// ===== STATE PAGINATION =====
const ITEMS_PER_PAGE = 12;
let   _currentPage   = 1;
let   _filteredItems = []; // cache item yang lolos filter

function filterArsip() {
    const search = searchInput  ? searchInput.value.toLowerCase() : '';
    const status = filterStatus ? filterStatus.value : '';
    const skkaa  = filterSKKAA  ? filterSKKAA.value  : '';

    // Kumpulkan semua item yang lolos filter
    _filteredItems = Array.from(document.querySelectorAll('.arsip-item')).filter(item =>
        item.dataset.search.includes(search) &&
        (!status || item.dataset.status === status) &&
        (!skkaa  || item.dataset.skkaa  === skkaa)
    );

    _renderPage(_currentPage);
}

/**
 * Tampilkan item sesuai halaman aktif
 */
function _renderPage(page) {
    const totalItems = _filteredItems.length;
    const totalPages = Math.max(1, Math.ceil(totalItems / ITEMS_PER_PAGE));

    // Pastikan page tidak keluar batas
    _currentPage = Math.min(Math.max(1, page), totalPages);

    const start = (_currentPage - 1) * ITEMS_PER_PAGE;
    const end   = start + ITEMS_PER_PAGE;

    // Sembunyikan semua item dulu
    document.querySelectorAll('.arsip-item').forEach(item => item.style.display = 'none');

    // Tampilkan hanya item di halaman ini
    _filteredItems.forEach((item, i) => {
        item.style.display = (i >= start && i < end) ? '' : 'none';
    });

    // Empty state
    const container  = document.getElementById('arsipContainer');
    let   emptyState = container?.querySelector('.filter-empty-state');

    if (totalItems === 0) {
        if (!emptyState && container) {
            emptyState           = document.createElement('div');
            emptyState.className = 'col-12 text-center text-muted py-5 filter-empty-state';
            emptyState.innerHTML = `<i class="fas fa-search fa-3x mb-3"></i><p>Tidak ada arsip yang sesuai dengan filter</p>`;
            container.appendChild(emptyState);
        }
    } else {
        emptyState?.remove();
    }

    _renderPaginationControls(totalItems, totalPages);
}

/**
 * Render tombol navigasi pagination
 */
function _renderPaginationControls(totalItems, totalPages) {
    const controls = document.getElementById('pagination-controls');
    const info     = document.getElementById('pagination-info');
    const wrapper  = document.getElementById('pagination-wrapper');

    if (!controls) return;

    // Sembunyikan pagination jika hanya 1 halaman
    if (wrapper) wrapper.style.display = totalPages <= 1 ? 'none' : 'flex';

    // Info teks
    if (info) {
        const start = Math.min((_currentPage - 1) * ITEMS_PER_PAGE + 1, totalItems);
        const end   = Math.min(_currentPage * ITEMS_PER_PAGE, totalItems);
        info.textContent = `Menampilkan ${start}–${end} dari ${totalItems} arsip`;
    }

    controls.innerHTML = '';

    // Tombol Sebelumnya
    controls.innerHTML += `
        <li class="page-item ${_currentPage === 1 ? 'disabled' : ''}">
            <a class="page-link" href="javascript:void(0)"
                onclick="goToPage(${_currentPage - 1})" aria-label="Sebelumnya">
                <i class="fas fa-chevron-left"></i>
            </a>
        </li>`;

    // Nomor halaman (tampilkan maks 5 nomor di sekitar halaman aktif)
    const delta = 2;
    let   pages = [];

    for (let i = 1; i <= totalPages; i++) {
        if (i === 1 || i === totalPages || (i >= _currentPage - delta && i <= _currentPage + delta)) {
            pages.push(i);
        }
    }

    let prev = null;
    pages.forEach(p => {
        if (prev !== null && p - prev > 1) {
            controls.innerHTML += `<li class="page-item disabled"><span class="page-link">…</span></li>`;
        }
        controls.innerHTML += `
            <li class="page-item ${p === _currentPage ? 'active' : ''}">
                <a class="page-link" href="javascript:void(0)" onclick="goToPage(${p})">${p}</a>
            </li>`;
        prev = p;
    });

    // Tombol Berikutnya
    controls.innerHTML += `
        <li class="page-item ${_currentPage === totalPages ? 'disabled' : ''}">
            <a class="page-link" href="javascript:void(0)"
                onclick="goToPage(${_currentPage + 1})" aria-label="Berikutnya">
                <i class="fas fa-chevron-right"></i>
            </a>
        </li>`;
}

/**
 * Pindah ke halaman tertentu
 */
function goToPage(page) {
    _renderPage(page);

    // Scroll ke atas container arsip
    const container = document.getElementById('arsipContainer');
    if (container) container.scrollIntoView({ behavior: 'smooth', block: 'start' });
}

// Inisialisasi pagination saat tab show aktif pertama kali
document.addEventListener('DOMContentLoaded', function () {
    // Isi _filteredItems dengan semua item
    _filteredItems = Array.from(document.querySelectorAll('.arsip-item'));
    if (_filteredItems.length > 0) _renderPage(1);
});

/* ==========================================================================
   7. MODAL: DETAIL ARSIP
   ========================================================================== */

function lihatDetail(btn) {
    fetch(`/arsiparis/arsip/${btn.dataset.id}/detail`)
        .then(res => res.json())
        .then(data => {
            document.getElementById('detail-judul').innerText       = data.judul_berkas;
            document.getElementById('detail-kode-arsip').innerText  = (data.status === 'DRAFT' ? 'DRAFT' : 'ARS') + '-' + data.id;
            document.getElementById('detail-status').innerText      = data.status;
            document.getElementById('detail-skkaa').innerText       = data.skkaa;
            document.getElementById('detail-uraian').innerText      = data.uraian || '-';
            document.getElementById('detail-kode').innerText        = data.kode_klasifikasi;
            document.getElementById('detail-unit').innerText        = data.unit_cipta;
            document.getElementById('detail-jumlah').innerText      = data.dokumens.length + ' Dokumen';
            document.getElementById('detail-retensi').innerText     = data.masa_retensi + ' Tahun';

            const daftarEl = document.getElementById('daftar-dokumen');

            if (data.dokumens.length === 0) {
                daftarEl.innerHTML = `
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-folder-open fa-2x mb-2 d-block"></i>
                        <p class="mb-0">Tidak ada dokumen</p>
                    </div>`;
            } else {
                daftarEl.innerHTML = data.dokumens.map(d => {
                    const ext         = d.nama_file.split('.').pop().toLowerCase();
                    const fileUrl     = `/storage/${d.path_file}`;
                    const icon        = _getDetailFileIcon(ext);
                    const size        = (d.ukuran / 1024 / 1024).toFixed(2);
                    const judul       = d.judul_dokumen || 'Tanpa Judul';
                    const canPreview  = ['pdf','jpg','jpeg','png','gif','bmp','webp'].includes(ext);

                    return `
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center" style="min-width:0; overflow:hidden;">
                                    <i class="fas ${icon} fa-lg mr-3 flex-shrink-0"></i>
                                    <div style="min-width:0;">
                                        <div class="font-weight-bold text-truncate" title="${judul}">${judul}</div>
                                    </div>
                                </div>
                                <div class="d-flex flex-shrink-0 ml-3" style="gap:6px;">
                                    ${canPreview ? `
                                    <button type="button"
                                        class="btn btn-sm btn-outline-primary"
                                        onclick="lihatDokumen('${fileUrl}', '${d.nama_file}', '${ext}')"
                                        title="Preview">
                                        <i class="fas fa-eye"></i>
                                    </button>` : ''}
                                    <a href="${fileUrl}" download="${d.nama_file}"
                                        class="btn btn-sm btn-primary" title="Unduh">
                                        <i class="fas fa-download"></i>
                                    </a>
                                </div>
                            </div>
                        </div>`;
                }).join('');
            }

            $('#detailModal').modal('show');
        })
        .catch(() => alert('Gagal memuat detail arsip'));
}

/* ==========================================================================
   8. MODAL: PREVIEW DOKUMEN
   ========================================================================== */

function lihatDokumen(url, filename, extension) {
    const ext        = extension.toLowerCase();
    const modalTitle = document.getElementById('previewModalTitle');
    const modalBody  = document.getElementById('previewModalBody');
    const btnTabBaru = document.getElementById('btn-buka-tab-baru');

    modalTitle.innerHTML = `<i class="fas fa-eye mr-2"></i>${filename}`;

    // Update href tombol "Buka di Tab Baru" di footer modal
    if (btnTabBaru) {
        btnTabBaru.href = url;
    }
    modalBody.innerHTML  = `
        <div class="d-flex justify-content-center align-items-center" style="min-height:400px;">
            <div class="spinner-border text-primary"><span class="sr-only">Loading...</span></div>
        </div>`;

    $('#previewModal').modal('show');

    setTimeout(() => {
        if (ext === 'pdf') {
            modalBody.innerHTML = `
                <div style="width:100%;height:70vh;">
                    <iframe src="${url}" style="width:100%;height:100%;border:none;" title="${filename}"></iframe>
                </div>`;
        } else if (['jpg','jpeg','png','gif','bmp','webp'].includes(ext)) {
            modalBody.innerHTML = `
                <div style="display:flex;justify-content:center;align-items:center;background:#f8f9fa;padding:20px;max-height:70vh;overflow:auto;">
                    <img src="${url}" class="img-fluid" alt="${filename}"
                        style="max-width:100%;max-height:65vh;object-fit:contain;"
                        onerror="this.parentElement.innerHTML='<p class=\\'text-danger\\'>Gagal memuat gambar</p>'">
                </div>`;
        } else if (['doc','docx'].includes(ext)) {
            modalBody.innerHTML = _downloadOnlyTemplate('fa-file-word text-primary', 'Dokumen Microsoft Word', url, filename);
        } else if (['xls','xlsx'].includes(ext)) {
            modalBody.innerHTML = _downloadOnlyTemplate('fa-file-excel text-success', 'Dokumen Microsoft Excel', url, filename);
        } else {
            modalBody.innerHTML = `
                <div class="text-center py-5">
                    <i class="fas fa-file fa-5x text-secondary mb-4"></i>
                    <h5>Preview Tidak Tersedia</h5>
                    <p class="text-muted">Tipe file <strong>${ext.toUpperCase()}</strong> tidak dapat di-preview.</p>
                    <a href="${url}" download="${filename}" class="btn btn-primary btn-lg">
                        <i class="fas fa-download mr-2"></i>Download File
                    </a>
                </div>`;
        }
    }, 100);
}

function _downloadOnlyTemplate(iconClass, label, url, filename) {
    return `
        <div class="text-center py-5">
            <i class="fas ${iconClass} fa-5x mb-4"></i>
            <h5 class="mb-2">${label}</h5>
            <p class="text-muted mb-4">Preview tidak tersedia. Silakan download untuk melihat isinya.</p>
            <a href="${url}" download="${filename}" class="btn btn-primary btn-lg">
                <i class="fas fa-download mr-2"></i>Download Dokumen
            </a>
        </div>`;
}

/* ==========================================================================
   9. HAPUS DOKUMEN (YANG SUDAH TERSIMPAN DI SERVER)
   ========================================================================== */

function hapusDokumen(dokumenId) {
    Swal.fire({
        title: 'Hapus Dokumen?', icon: 'warning',
        text: 'Dokumen yang dihapus tidak dapat dikembalikan!',
        showCancelButton: true,
        confirmButtonColor: '#d33', cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus!', cancelButtonText: 'Batal', reverseButtons: true,
    }).then(result => {
        if (!result.isConfirmed) return;

        Swal.fire({ title: 'Menghapus...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

        fetch(`/arsiparis/arsip/dokumen/${dokumenId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN'  : document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept'        : 'application/json',
                'Content-Type'  : 'application/json',
            },
        })
        .then(res => res.json())
        .then(data => {
            if (!data.success) throw new Error(data.message || 'Gagal menghapus dokumen');

            const elDok = document.getElementById(`dokumen-${dokumenId}`);
            if (elDok) {
                elDok.style.transition = 'opacity 0.3s';
                elDok.style.opacity    = '0';
                setTimeout(() => { elDok.remove(); _updateExistingDocCount(); }, 300);
            }

            Swal.fire({ toast: true, position: 'top-end', icon: 'success',
                title: 'Dokumen berhasil dihapus', showConfirmButton: false, timer: 3000 });
        })
        .catch(err => Swal.fire({ icon: 'error', title: 'Gagal!', text: err.message }));
    });
}

function _updateExistingDocCount() {
    const count = document.querySelectorAll('.dokumen-item').length;
    const el    = document.getElementById('existing-doc-count');
    if (el) el.textContent = count;

    const ringkasan = document.getElementById('ringkasan-jumlah');
    if (ringkasan) ringkasan.textContent = count + fileBuffer.files.length;
}

/* ==========================================================================
   10. SUBMIT FORM (LOADING OVERLAY + CLEAR LOCALSTORAGE)
   ========================================================================== */

const mainForm = document.querySelector('form');
if (mainForm) {
    mainForm.addEventListener('submit', function (e) {
        // Hapus data draft setelah form berhasil disubmit
        localStorage.removeItem(STORAGE_KEY());

        const submitBtn     = e.submitter;
        submitBtn.disabled  = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sedang menyimpan...';

        const overlay     = document.createElement('div');
        overlay.id        = 'upload-overlay';
        overlay.innerHTML = `
            <div style="position:fixed;top:0;left:0;width:100%;height:100%;
                background:rgba(0,0,0,0.7);z-index:9999;
                display:flex;align-items:center;justify-content:center;">
                <div style="background:white;padding:30px;border-radius:10px;text-align:center;">
                    <i class="fas fa-spinner fa-spin fa-3x text-primary mb-3"></i>
                    <h5>Sedang mengupload dokumen...</h5>
                    <p class="text-muted">Mohon tunggu, jangan tutup halaman ini</p>
                </div>
            </div>`;
        document.body.appendChild(overlay);
    });
}