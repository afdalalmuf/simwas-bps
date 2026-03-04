$(".submit-btn").on("click", function (e) {
    e.preventDefault();

    var form = document.getElementById('formPenetapan');
    var formData = new FormData(form);
    let token = $("meta[name='csrf-token']").attr("content");
    formData.append('_token', token);

    $.ajax({
        url: `/pegawai/upload-skp/store`,
        type: "POST",
        cache: false,
        data: formData,
        contentType: false,
        processData: false,
        success: function (response) {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: 'Data berhasil diperbarui.',
                confirmButtonText: 'OK'
            }).then(() => {
                location.reload();
            });
        },
        error: function (error) {
            if (error.status === 422) {
                let errorResponses = error.responseJSON;
                let errors = Object.entries(errorResponses.errors);

                errors.forEach(([key, value]) => {
                    let errorMessage = document.getElementById(`error-${key}`);
                    errorMessage.innerText = `${value}`;
                });

                Swal.fire({
                    icon: 'error',
                    title: 'Validasi Gagal!',
                    text: 'Silakan periksa input Anda.',
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Terjadi Kesalahan!',
                    text: 'Silakan coba beberapa saat lagi.',
                });
            }
        },
    });
});

$(".update-btn").on("click", function (e) {
    e.preventDefault();

    var form = document.getElementById('formEditPenetapan');
    var formData = new FormData(form);
    let token = $("meta[name='csrf-token']").attr("content");
    formData.append('_token', token);
    let id_skp = $("#id-skp").val();
    console.log(id_skp);

    $.ajax({
        url: `/pegawai/upload-skp/update-penetapan/${id_skp}`,
        type: "POST",
        cache: false,
        data: formData,
        contentType: false,
        processData: false,
        success: function (response) {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: 'Data berhasil diperbarui.',
                confirmButtonText: 'OK'
            }).then(() => {
                location.reload();
            });
        },
        error: function (error) {
            if (error.status === 422) {
                let errorResponses = error.responseJSON;
                let errors = Object.entries(errorResponses.errors);

                errors.forEach(([key, value]) => {
                    let errorMessage = document.getElementById(`error-${key}`);
                    errorMessage.innerText = `${value}`;
                });

                Swal.fire({
                    icon: 'error',
                    title: 'Validasi Gagal!',
                    text: 'Silakan periksa input Anda.',
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Terjadi Kesalahan!',
                    text: 'Silakan coba beberapa saat lagi.',
                });
            }
        },
    });
});

$(".submit-btn-penilaian").on("click", function (e) {
    e.preventDefault();

    var form = document.getElementById('formPenilaian');
    var formData = new FormData(form);
    let token = $("meta[name='csrf-token']").attr("content");
    formData.append('_token', token);

    $.ajax({
        url: `/pegawai/upload-skp/store`,
        type: "POST",
        cache: false,
        data: formData,
        contentType: false,
        processData: false,
        success: function (response) {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: 'Data berhasil diperbarui.',
                confirmButtonText: 'OK'
            }).then(() => {
                location.reload();
            });
        },
        error: function (error) {
            if (error.status === 422) {
                let errorResponses = error.responseJSON;
                let errors = Object.entries(errorResponses.errors);

                errors.forEach(([key, value]) => {
                    let errorMessage = document.getElementById(`error-${key}`);
                    errorMessage.innerText = `${value}`;
                });

                Swal.fire({
                    icon: 'error',
                    title: 'Validasi Gagal!',
                    text: 'Silakan periksa input Anda.',
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Terjadi Kesalahan!',
                    text: 'Silakan coba beberapa saat lagi.',
                });
            }
        },
    });
});

$(".edit-btn-penilaian").on("click", function (e) {
    e.preventDefault();

    var form = document.getElementById('formEditPenilaian');
    var formData = new FormData(form);
    let token = $("meta[name='csrf-token']").attr("content");
    formData.append('_token', token);
    let id_skp = $("#edit-id-penilaian").val();

    $.ajax({
        url: `/pegawai/upload-skp/update-penilaian/${id_skp}`,
        type: "POST",
        cache: false,
        data: formData,
        contentType: false,
        processData: false,
        success: function (response) {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: 'Data berhasil diperbarui.',
                confirmButtonText: 'OK'
            }).then(() => {
                location.reload();
            });
        },
        error: function (error) {
            if (error.status === 422) {
                let errorResponses = error.responseJSON;
                let errors = Object.entries(errorResponses.errors);

                errors.forEach(([key, value]) => {
                    let errorMessage = document.getElementById(`error-${key}`);
                    errorMessage.innerText = `${value}`;
                });

                Swal.fire({
                    icon: 'error',
                    title: 'Validasi Gagal!',
                    text: 'Silakan periksa input Anda.',
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Terjadi Kesalahan!',
                    text: 'Silakan coba beberapa saat lagi.',
                });
            }
        },
    });
});

var datatable = $('#table-upload-skp-tahunan').dataTable({
    dom: "Bfrtip",
    responsive: false,
    lengthChange: false,
    autoWidth: false,
    scrollX: true,
    buttons: [],
    ordering: false,
    searching: false,
    paging: false,
    info: false,
}).api();

var datatable = $('#table-upload-skp-bulanan').dataTable({
    dom: "Bfrtip",
    responsive: false,
    lengthChange: false,
    autoWidth: false,
    scrollX: true,
    buttons: [],
    ordering: false,
    pageLength: 12,
    searching: false,
    paging: false,
    info: false,
}).api();

//update ukuran tabel saat ukuran sidebar berubah
$('.nav-link').on("click", function () {
    setTimeout(function () {
        datatable.columns.adjust();
    }, 500);
});

$('#yearSelect').on('change', function () {
    let year = $(this).val();
    $('#yearForm').attr('action', `?year=${year}`);
    $('#yearForm').find('[name="_token"]').remove();
    $('#yearForm').submit();
});

$(".detail").attr('href', function (_, el) {
    return el.replace(/\/[^\/]*$/, '/' + $('#yearSelect').val());
});

$('.unduh').on('click', function () {
    window.location.href = `/inspektur/rencana-jam-kerja/export/${mode}/${$('#yearSelect').val()}`;
})

$('#table-upload-skp-tahunan').on('draw.dt', function () {
    $(".detail").attr('href', function (_, el) {
        return el.replace(/\/[^\/]*$/, '/' + $('#yearSelect').val());
    });
});
$('#table-upload-skp-bulanan').on('draw.dt', function () {
    $(".detail").attr('href', function (_, el) {
        return el.replace(/\/[^\/]*$/, '/' + $('#yearSelect').val());
    });
});

$(document).on('click', '.create-skp-penetapan', function () {
    $('#create-tahun-penetapan').val($(this).data('tahun-penetapan'));
    $('#create-jenis-penetapan').val($(this).data('jenis-penetapan'));
});

$(document).on('click', '.edit-skp-penetapan', function () {
    var catatan = $(this).data('catatan');
    var status = $(this).data('status');
    const tgl_upload = $(this).data('tgl-upload'); // contoh: "2025-04-29 03:38:38"
    const dateObj = new Date(tgl_upload);

    const day = String(dateObj.getDate()).padStart(2, '0');
    const month = String(dateObj.getMonth() + 1).padStart(2, '0'); // getMonth() 0-indexed
    const year = dateObj.getFullYear();
    const formatted = `${day}/${month}/${year}`;
    $('#id-skp').val($(this).data('id-penetapan'));
    $('#edit-catatan-penetapan').val(catatan);
    $('#edit-tgl-upload-penetapan').val($(this).data('tgl-upload'));
    $('#edit-tgl-upload-penetapan').text(formatted);

    if (catatan !== '' && status == 'Ditolak') {
        $('#catatanPenetapan').show();
    } else {
        $('#catatanPenetapan').hide();
    }

    if (status === 'Sudah Kirim') {
        $('#formEditPenetapan').find('input, select').attr('readonly', true).attr('disabled',
            true);
        $('#btn-footer-penetapan').hide();
    } else {
        $('#formEditPenetapan').find('input, select').removeAttr('readonly').removeAttr(
            'disabled');
        $('#btn-footer-penetapan').show();
    }
});

$(document).on('click', '.create-skp-penilaian', function () {
    $('#create-tahun-penilaian').val($(this).data('tahun-penilaian'));
    $('#create-jenis-penilaian').val($(this).data('jenis-penilaian'));
    $('#create-bulan').val($(this).data('bulan'));
});

$(document).on('click', '.edit-skp-penilaian', function () {
    var catatan = $(this).data('catatan');
    var status = $(this).data('status');
    const tgl_upload = $(this).data('tgl-upload'); // contoh: "2025-04-29 03:38:38"
    const dateObj = new Date(tgl_upload);

    const day = String(dateObj.getDate()).padStart(2, '0');
    const month = String(dateObj.getMonth() + 1).padStart(2, '0'); // getMonth() 0-indexed
    const year = dateObj.getFullYear();
    const formatted = `${day}/${month}/${year}`;
    $('#edit-id-penilaian').val($(this).data('id-penilaian'));
    $('#edit-rating-hasil-penilaian').val($(this).data('rating-hasil'));
    $('#edit-rating-perilaku-penilaian').val($(this).data('rating-perilaku'));
    $('#edit-predikat-penilaian').val($(this).data('predikat'));
    $('#myLink').attr('href', 'upload-skp/viewSKP/' + $('#edit-id-penilaian').val());
    $('#edit-catatan-penilaian').val(catatan);
    $('#edit-tgl-upload-penilaian').text(formatted);

    if (catatan !== '' && status == 'Ditolak') {
        $('#catatanSection').show();
    } else {
        $('#catatanSection').hide();
    }

    if (status === 'Sudah Kirim') {
        $('#formEditPenilaian').find('input, select').attr('readonly', true).attr('disabled',
            true);
        $('#btn-footer-penilaian').hide();
    } else {
        $('#formEditPenilaian').find('input, select').removeAttr('readonly').removeAttr(
            'disabled');
        $('#btn-footer-penilaian').show();
    }
});

document.addEventListener("DOMContentLoaded", function () {
    // Aktifkan tooltip Bootstrap
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (el) {
        return new bootstrap.Tooltip(el);
    });

    function openModal(modalId, callback) {
        const modalEl = document.getElementById(modalId);
        if (!$(modalEl).hasClass('show')) {
            $(modalEl).modal('show');
            $(modalEl).on('shown.bs.modal', function () {
                $(modalEl).off('shown.bs.modal');
                if (callback) callback();
            });
        } else {
            if (callback) callback();
        }
    }

    document.getElementById('start-tour-btn')?.addEventListener('click', function () {
        const steps = [
            {
                element: document.getElementById('step1'),
                intro: "Pilih Tahun SKP"
            },
            {
                element: document.getElementById('table-upload-skp-tahunan'),
                intro: "Tabel SKP Tahunan"
            },
            {
                element: document.getElementById('table-upload-skp-bulanan'),
                intro: "Tabel SKP Bulanan"
            },
            {
                element: document.querySelector('.create-skp-penilaian'),
                intro: "Klik tombol ini untuk menambah SKP Penilaian"
            },
            {
                element: document.getElementById('rating-hasil-kerja-tour-guide'),
                intro: "Masukkan nilai rating hasil kerja di sini."
            },
            {
                element: document.getElementById('rating-perilaku-kerja-tour-guide'),
                intro: "Masukkan nilai perilaku hasil kerja di sini."
            },
            {
                element: document.getElementById('predikat-kinerja-tour-guide'),
                intro: "Masukkan predikat kinerja di sini."
            },
            {
                element: document.getElementById('dok-skp-tour-guide'),
                intro: "Upload dokumen SKP penilaian di sini."
            },
            {
                element: document.querySelector('.submit-btn-penilaian'),
                intro: "Tekan tombol simpan."
            }
        ];

        const tour = introJs();
        tour.setOptions({
            steps: steps,
            showStepNumbers: true,
            disableInteraction: false,
            exitOnOverlayClick: false
        });

        // Deteksi kapan user masuk ke step dalam modal
        tour.onbeforechange(function (targetElement) {
            if (targetElement?.id === 'rating-hasil-kerja-tour-guide') {
                return new Promise(resolve => {
                    openModal('modal-create-skp-penilaian', () => {
                        setTimeout(resolve, 100); // Tunggu modal benar-benar stabil
                    });
                });
            }
        });

        tour.start();
    });
});




