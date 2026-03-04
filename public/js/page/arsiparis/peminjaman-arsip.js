/**
 * peminjaman.js
 * Halaman: Arsiparis > Persetujuan Peminjaman Arsip
 */

const CSRF = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

/* ==========================================================================
   FILTER TABEL
   ========================================================================== */

const filterEl = document.getElementById('filterStatusPeminjaman');
if (filterEl) {
    filterEl.addEventListener('change', function () {
        const val = this.value;
        document.querySelectorAll('.peminjaman-row').forEach(row => {
            row.style.display = (!val || row.dataset.status === val) ? '' : 'none';
        });
    });
}

/* ==========================================================================
   MODAL: DETAIL PEMINJAMAN
   ========================================================================== */

let _activePeminjamanId = null;

function lihatDetailPeminjaman(id) {
    _activePeminjamanId = id;

    // Reset state modal
    document.getElementById('form-alasan-tolak').classList.add('d-none');
    document.getElementById('input-alasan-tolak').value = '';

    fetch(`/arsiparis/peminjaman-arsip/${id}/detail`)
        .then(res => res.json())
        .then(data => {
            _isiModal(data);
            $('#modalDetailPeminjaman').modal('show');
        })
        .catch(() => Swal.fire({ icon: 'error', title: 'Gagal!', text: 'Gagal memuat detail peminjaman.' }));
}

function _isiModal(data) {
    // Badge status
    const badge = document.getElementById('modal-badge-status');
    const badgeStyle = {
        MENUNGGU  : { bg: '#fff3cd', color: '#856404' },
        DISETUJUI : { bg: '#d4edda', color: '#155724' },
        DITOLAK   : { bg: '#fde8e7', color: '#721c24' },
    }[data.status] || { bg: '#e2e8f0', color: '#4a5568' };

    badge.innerText         = data.status;
    badge.style.background  = badgeStyle.bg;
    badge.style.color       = badgeStyle.color;

    // ID
    document.getElementById('modal-id-tampil').innerText = 'ID: ' + data.id_tampil;

    // Peminjam
    document.getElementById('modal-nama').innerText    = data.peminjam.nama;
    document.getElementById('modal-nip').innerText     = data.peminjam.nip;
    document.getElementById('modal-unit').innerText    = data.peminjam.unit;
    document.getElementById('modal-tanggal').innerText = data.tanggal_pengajuan;

    // Arsip
    document.getElementById('modal-arsip-id').innerText    = data.arsip.id_tampil;
    document.getElementById('modal-arsip-judul').innerText = data.arsip.judul;

    // Alasan
    document.getElementById('modal-alasan').innerText = data.alasan_peminjaman;

    // Sembunyikan semua info tambahan dulu
    document.getElementById('modal-info-disetujui').classList.add('d-none');
    document.getElementById('modal-info-ditolak').classList.add('d-none');

    if (data.status === 'DISETUJUI') {
        document.getElementById('modal-info-disetujui').classList.remove('d-none');
        document.getElementById('modal-tgl-disetujui').innerText = data.disetujui_pada;
        document.getElementById('modal-tgl-berakhir').innerText  = data.berakhir_pada;
    } else if (data.status === 'DITOLAK') {
        document.getElementById('modal-info-ditolak').classList.remove('d-none');
        document.getElementById('modal-alasan-tolak').innerText = data.alasan_penolakan || '-';
    }

    // Footer tombol aksi
    _renderFooter(data.status);
}

function _renderFooter(status) {
    const footer = document.getElementById('modal-footer-aksi');

    if (status === 'MENUNGGU') {
        footer.innerHTML = `
            <button type="button" class="btn btn-danger mr-auto" id="btn-tolak-confirm"
                onclick="_toggleFormTolak()" style="border-radius: 8px; min-width: 110px;">
                <i class="fas fa-times mr-1"></i> Tolak
            </button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal" style="border-radius: 8px;">
                Batal
            </button>
            <button type="button" class="btn btn-success" id="btn-setujui"
                onclick="_setujuiPeminjaman()" style="border-radius: 8px; min-width: 110px;">
                <i class="fas fa-check mr-1"></i> Setujui
            </button>`;
    } else {
        footer.innerHTML = `
            <button type="button" class="btn btn-secondary ml-auto" data-dismiss="modal"
                style="border-radius: 8px;">
                <i class="fas fa-times mr-1"></i> Tutup
            </button>`;
    }
}

function _toggleFormTolak() {
    const form   = document.getElementById('form-alasan-tolak');
    const btnTolak = document.getElementById('btn-tolak-confirm');
    const isShown  = !form.classList.contains('d-none');

    if (isShown) {
        // Sembunyikan kembali
        form.classList.add('d-none');
        btnTolak.innerHTML = '<i class="fas fa-times mr-1"></i> Tolak';
        btnTolak.onclick   = _toggleFormTolak;
    } else {
        // Tampilkan form alasan
        form.classList.remove('d-none');
        document.getElementById('input-alasan-tolak').focus();
        btnTolak.innerHTML = '<i class="fas fa-paper-plane mr-1"></i> Kirim Penolakan';
        btnTolak.onclick   = _tolakPeminjaman;
    }
}

async function _setujuiPeminjaman() {
    const result = await Swal.fire({
        title: 'Setujui Peminjaman?',
        text: 'Peminjam akan mendapatkan akses dokumen selama 7 hari.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#1cc88a',
        cancelButtonColor : '#6c757d',
        confirmButtonText : 'Ya, Setujui!',
        cancelButtonText  : 'Batal',
        reverseButtons    : true,
    });

    if (!result.isConfirmed) return;

    Swal.fire({ title: 'Memproses...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

    try {
        const res  = await fetch(`/arsiparis/peminjaman-arsip/${_activePeminjamanId}/setujui`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json', 'Content-Type': 'application/json' },
        });
        const data = await res.json();

        if (!data.success) throw new Error(data.message);

        await Swal.fire({ icon: 'success', title: 'Berhasil!', text: data.message, timer: 3000, showConfirmButton: false });
        $('#modalDetailPeminjaman').modal('hide');
        location.reload();
    } catch (err) {
        Swal.fire({ icon: 'error', title: 'Gagal!', text: err.message });
    }
}

async function _tolakPeminjaman() {
    const alasan = document.getElementById('input-alasan-tolak').value.trim();

    if (alasan.length < 5) {
        Swal.fire({ toast: true, position: 'top-end', icon: 'warning',
            title: 'Alasan penolakan minimal 5 karakter', showConfirmButton: false, timer: 3000 });
        return;
    }

    Swal.fire({ title: 'Memproses...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

    try {
        const res  = await fetch(`/arsiparis/peminjaman-arsip/${_activePeminjamanId}/tolak`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json', 'Content-Type': 'application/json' },
            body: JSON.stringify({ alasan_penolakan: alasan }),
        });
        const data = await res.json();

        if (!data.success) throw new Error(data.message);

        await Swal.fire({ icon: 'success', title: 'Ditolak', text: data.message, timer: 3000, showConfirmButton: false });
        $('#modalDetailPeminjaman').modal('hide');
        location.reload();
    } catch (err) {
        Swal.fire({ icon: 'error', title: 'Gagal!', text: err.message });
    }
}