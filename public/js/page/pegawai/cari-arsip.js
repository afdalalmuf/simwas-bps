/**
 * cari-arsip.js
 * Halaman: Pegawai > Cari Arsip
 */

const CSRF      = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
const ajukanUrl = document.getElementById('js-config')?.dataset.ajukanUrl; // ← FIX

/* ==========================================================================
   FILTER & PAGINATION
   ========================================================================== */

const searchInput = document.getElementById('searchArsip');
const filterSKKAA = document.getElementById('filterSKKAA');
const filterUnit  = document.getElementById('filterUnit');

const ITEMS_PER_PAGE = 12;
let _currentPage     = 1;
let _filteredItems   = [];

if (searchInput) searchInput.addEventListener('input',  () => { _currentPage = 1; filterArsip(); });
if (filterSKKAA) filterSKKAA.addEventListener('change', () => { _currentPage = 1; filterArsip(); });
if (filterUnit)  filterUnit.addEventListener('change',  () => { _currentPage = 1; filterArsip(); });

function filterArsip() {
    const search = searchInput ? searchInput.value.toLowerCase() : '';
    const skkaa  = filterSKKAA ? filterSKKAA.value : '';
    const unit   = filterUnit  ? filterUnit.value  : '';

    _filteredItems = Array.from(document.querySelectorAll('.arsip-item')).filter(item =>
        item.dataset.search.includes(search) &&
        (!skkaa || item.dataset.skkaa === skkaa) &&
        (!unit  || item.dataset.unit  === unit)
    );

    _renderPage(1);
}

function _renderPage(page) {
    const totalItems = _filteredItems.length;
    const totalPages = Math.max(1, Math.ceil(totalItems / ITEMS_PER_PAGE));
    _currentPage     = Math.min(Math.max(1, page), totalPages);

    const start = (_currentPage - 1) * ITEMS_PER_PAGE;
    const end   = start + ITEMS_PER_PAGE;

    document.querySelectorAll('.arsip-item').forEach(item => item.style.display = 'none');
    _filteredItems.forEach((item, i) => {
        item.style.display = (i >= start && i < end) ? '' : 'none';
    });

    const container  = document.getElementById('arsipContainer');
    let   emptyState = container?.querySelector('.filter-empty-state');

    if (totalItems === 0) {
        if (!emptyState && container) {
            emptyState           = document.createElement('div');
            emptyState.className = 'col-12 text-center text-muted py-5 filter-empty-state';
            emptyState.innerHTML = `<i class="fas fa-search fa-3x mb-3 d-block"></i><p>Tidak ada arsip yang sesuai</p>`;
            container.appendChild(emptyState);
        }
    } else {
        emptyState?.remove();
    }

    _renderPagination(totalItems, totalPages);
}

function _renderPagination(totalItems, totalPages) {
    const controls = document.getElementById('pagination-controls');
    const info     = document.getElementById('pagination-info');
    const wrapper  = document.getElementById('pagination-wrapper');

    if (!controls) return;
    if (wrapper) wrapper.style.display = totalPages <= 1 ? 'none' : 'flex';

    if (info) {
        const start = Math.min((_currentPage - 1) * ITEMS_PER_PAGE + 1, totalItems);
        const end   = Math.min(_currentPage * ITEMS_PER_PAGE, totalItems);
        info.textContent = `Menampilkan ${start}–${end} dari ${totalItems} arsip`;
    }

    controls.innerHTML = '';
    controls.innerHTML += `<li class="page-item ${_currentPage === 1 ? 'disabled' : ''}">
        <a class="page-link" href="javascript:void(0)" onclick="goToPage(${_currentPage - 1})">
            <i class="fas fa-chevron-left"></i></a></li>`;

    const delta = 2;
    let prev = null;
    for (let i = 1; i <= totalPages; i++) {
        if (i === 1 || i === totalPages || (i >= _currentPage - delta && i <= _currentPage + delta)) {
            if (prev !== null && i - prev > 1)
                controls.innerHTML += `<li class="page-item disabled"><span class="page-link">…</span></li>`;
            controls.innerHTML += `<li class="page-item ${i === _currentPage ? 'active' : ''}">
                <a class="page-link" href="javascript:void(0)" onclick="goToPage(${i})">${i}</a></li>`;
            prev = i;
        }
    }

    controls.innerHTML += `<li class="page-item ${_currentPage === totalPages ? 'disabled' : ''}">
        <a class="page-link" href="javascript:void(0)" onclick="goToPage(${_currentPage + 1})">
            <i class="fas fa-chevron-right"></i></a></li>`;
}

function goToPage(page) {
    _renderPage(page);
    document.getElementById('arsipContainer')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
}

document.addEventListener('DOMContentLoaded', function () {
    _filteredItems = Array.from(document.querySelectorAll('.arsip-item'));
    if (_filteredItems.length > 0) _renderPage(1);
});

/* ==========================================================================
   MODAL: AJUKAN PINJAMAN
   ========================================================================== */

let _activeArsipId = null;

function ajukanPinjaman(arsipId, judulArsip) {
    _activeArsipId = arsipId;
    document.getElementById('modal-judul-arsip').innerText = judulArsip;
    document.getElementById('input-alasan').value = '';
    $('#modalAjukanPinjaman').modal('show');
}

document.addEventListener('DOMContentLoaded', function () {
    const btnKirim = document.getElementById('btn-kirim-ajuan');
    if (!btnKirim) return;

    btnKirim.addEventListener('click', async function () {
        const alasan = document.getElementById('input-alasan').value.trim();

        if (alasan.length < 5) {
            Swal.fire({ toast: true, position: 'top-end', icon: 'warning',
                title: 'Alasan minimal 5 karakter', showConfirmButton: false, timer: 3000 });
            return;
        }

        btnKirim.disabled  = true;
        btnKirim.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Mengirim...';

        try {
            const res  = await fetch(ajukanUrl, { // ← FIX: pakai variable, bukan Blade syntax
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN' : CSRF,
                    'Accept'       : 'application/json',
                    'Content-Type' : 'application/json',
                },
                body: JSON.stringify({ arsip_id: _activeArsipId, alasan_peminjaman: alasan }),
            });
            const data = await res.json();

            if (!data.success) throw new Error(data.message);

            $('#modalAjukanPinjaman').modal('hide');
            await Swal.fire({ icon: 'success', title: 'Pengajuan Terkirim!', text: data.message,
                timer: 3500, showConfirmButton: false });
            location.reload();
        } catch (err) {
            Swal.fire({ icon: 'error', title: 'Gagal!', text: err.message });
        } finally {
            btnKirim.disabled  = false;
            btnKirim.innerHTML = '<i class="fas fa-paper-plane mr-1"></i> Kirim Pengajuan';
        }
    });
});