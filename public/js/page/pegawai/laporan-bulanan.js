let laporanTable;

function initTable() {
    laporanTable = $('#table-laporan').DataTable({
        responsive: true,
        dom: 'Bfrtip',
        pageLength: 15,
        buttons: [
            {
                extend: "excel",
                className: "btn-success",
                text: '<i class="fas fa-file-excel"></i> Excel',
            },
            {
                extend: "pdf",
                className: "btn-danger",
                text: '<i class="fas fa-file-pdf"></i> PDF',
            },
        ],
        // buttons: ['excel', 'pdf', 'print'],
        language: {
            search: "Cari:",
            lengthMenu: "Tampilkan _MENU_ entri",
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
            paginate: {
                first: "Pertama",
                last: "Terakhir",
                next: "›",
                previous: "‹"
            }
        }
    });
}

function reloadTableData() {
    Swal.fire({
        title: "Memuat Data",
        html: "Mohon tunggu sebentar",
        timerProgressBar: true,
        didOpen: () => {
            Swal.showLoading();
        },
        allowOutsideClick: false
    });

    const month = $('#filterBulan').val();
    const year = $('#filterTahun').val();

    $.ajax({
        url: '/pegawai/laporan-bulanan/fetch',
        method: 'GET',
        data: { month, year },
        success: function (response) {
            // Clear existing data
            laporanTable.clear();

            let hasSyncedData = false; // <-- Track sync status

            const statusMap = {
                'tidak ada': '<span class="badge badge-secondary">Tidak Ada</span>',
                'belum mapping': '<span class="badge badge-danger">Belum Mapping</span>',
                'belum sinkron': '<span class="badge badge-warning">Belum Sinkron</span>',
                'sinkron': '<span class="badge badge-success">Sinkron</span>',
            };
            // Add new rows
            for (const key in response.data) {
                if (response.data.hasOwnProperty(key)) {
                    const ts = response.data[key];

                    const proyek = ts.rencana_kerja && ts.rencana_kerja.proyek ? ts.rencana_kerja.proyek.nama_proyek : '-';
                    const tugas = ts.rencana_kerja ? ts.rencana_kerja.tugas : '-';
                    const kegiatan = ts.matched_kinerja ? ts.matched_kinerja.kegiatan : '-';
                    const capaian = ts.matched_kinerja ? ts.matched_kinerja.capaian : '-';
                    const target_hours = Number(ts.target_hours).toFixed(2);
                    const activity_hours = Number(ts.activity_hours).toFixed(2);
                    const total_activity = ts.total_activity || 0;
                    const id_rencanakerja = ts.id_rencanakerja;
                    const id_pelaksana = ts.id_pelaksana;
                    const disabledAttr = total_activity === 0 ? 'disabled' : '';
                    const statusText = ts.status || '';
                    const statusBadge = statusMap[statusText] || '<span>' + statusText + '</span>';

                    // ⛔ Check if ANY data is already synced
                    if (ts.status === 'sinkron') {
                        hasSyncedData = true;
                    }

                    // Compose the button HTML carefully (avoid jQuery data attributes in string for safety)
                    const activityBtn = `<button class="btn btn-link p-0 activity-detail" 
                        data-rencana="${id_rencanakerja}" 
                        data-target="${target_hours}" 
                        data-month="${month}" data-year="${year}">${total_activity}</button>`;

                    const syncBtn = `<button class="btn btn-sm btn-outline-primary btn-mapping-form" 
                        data-aktivitas="${total_activity}" 
                        data-rencana="${id_rencanakerja}" 
                        data-kegiatan="${kegiatan}" 
                        data-capaian="${capaian}" 
                        data-pelaksana="${id_pelaksana}" 
                        data-toggle="modal" 
                        data-target="#mappingFormModal" ${disabledAttr}>
                        <i class="fas fa-random	"></i></button>`;


                    laporanTable.row.add([
                        proyek,
                        tugas,
                        kegiatan,
                        capaian,
                        target_hours,
                        activity_hours,
                        activityBtn,
                        statusBadge,
                        syncBtn
                    ]);
                }
            }

            // Redraw the table to show new data
            laporanTable.draw();
            swal.close();

            // 🔒 Disable Sync All button if ANY data is already synced
            $('#syncAllBtn').prop('disabled', hasSyncedData);
        },
        error: function () {
            Swal.fire('Gagal Memuat Data', 'Terjadi kesalahan saat mengambil data. Silakan coba lagi.', 'error');
        }
    });
}

$(document).ready(function () {
    initTable();
    reloadTableData();

    $(window).on('resize', function () {
        if (laporanTable) {
            laporanTable.columns.adjust().responsive.recalc();
        }
    });

    $('#table-laporan tbody').on('click', '.activity-detail', function () {
        const rencanaId = $(this).data('rencana');
        const targetHour = parseFloat($(this).data('target'));
        const month = $(this).data('month');
        const year = $(this).data('year');

        $('#activityList').html('<li class="list-group-item text-muted">Memuat data...</li>');
        $('#targetHours').text('-');
        $('#totalHours').text('-');
        $('#completionRate').text('-');

        $('#syncModal').modal('show');

        $.get(`/pegawai/aktivitas/rencana/fetch/${rencanaId}/${year}/${month}`, function (response) {
            const activities = response.events;
            const totalHours = parseFloat(response.total_hours);
            const percentage = targetHour > 0 ? ((totalHours / targetHour) * 100).toFixed(1) : 0;

            $('#targetHours').text(`${targetHour.toFixed(2)} jam`);
            $('#totalHours').text(`${totalHours.toFixed(2)} jam`);
            $('#completionRate')
                .text(`${percentage}%`)
                .removeClass('text-danger text-success')
                .addClass(percentage < 100 ? 'text-danger' : 'text-success');

            const $list = $('#activityList').empty();

            if (!activities.length) {
                $list.html('<li class="list-group-item text-muted">Tidak ada aktivitas untuk bulan ini. <a  href="/pegawai/aktivitas-harian"><span class="ml-2 badge"> Tambah Baru</span></a></li>');
                return;
            }

            activities.forEach(event => {
                const start = moment(event.start);
                const end = moment(event.end);
                const duration = end.diff(start, 'minutes') / 60;
                const aktivitas = event.aktivitas ?? '-';

                const item = `
                <li class="list-group-item">
                    <strong>${aktivitas}</strong><br>
                    <small>${start.format('D MMM YYYY, HH:mm')} - ${end.format('HH:mm')} (${duration.toFixed(2)} jam)</small>
                </li>`;
                $list.append(item);
            });
        });
    });

    $('#table-laporan tbody').on('click', '.btn-mapping-form', function () {
        // 1. Show SweetAlert2 loading
        Swal.fire({
            title: "Memuat Data",
            html: "Mohon tunggu sebentar",
            timerProgressBar: true,
            didOpen: () => {
                Swal.showLoading();
            },
            allowOutsideClick: false
        });

        const pelaksana = $(this).data('pelaksana');
        const rencana = $(this).data('rencana');
        const kegiatan = $(this).data('kegiatan');
        const capaian = $(this).data('capaian');
        const month = $('#filterBulan').val();
        const year = $('#filterTahun').val();

        const formatted = moment(`${year}-${month}`, 'YYYY-M').locale('id').format('MMMM YYYY');

        $('#mappingForm')[0].reset();
        $('#mappingForm input, #mappingForm textarea, #mappingForm select').prop('disabled', false);
        $('#submitSync').show();
        $('#syncWarning').addClass('d-none');
        $('#mappingFormModal').modal('show');

        // Run all AJAX in parallel
        const getRencana = $.get(`/pegawai/kipappsyncs/getRK/${year}/${month}`);
        const getEvents = $.get(`/pegawai/aktivitas/rencana/fetch/${rencana}/${year}/${month}`);
        const getSync = $.get(checkSyncUrl, { id_pelaksana: pelaksana, month, year });

        // When ALL done:
        $.when(getRencana, getEvents, getSync).done(function (rkres, eventsRes, syncRes) {
            // rkres, eventsRes, syncRes are arrays [data, status, xhr] due to $.when
            const rkData = rkres[0], events = eventsRes[0], syncResp = syncRes[0];

            // KipApp Rencana
            $('#kipappRencana').html(`<option value="" disabled selected>Pilih Rencana Kinerja KipApp Bulan ${formatted} </option>`);
            if (rkData.status) {
                const options = rkData.data.map(item =>
                    `<option value="${item.koderk}">${item.rencanakinerja}</option>`
                );
                $('#kipappRencana').append(options);
            }

            // Events
            const evtList = events.events || [];
            const $list = $('#eventList').empty();

            if (evtList.length === 0) {
                $list.append('<li class="list-group-item text-muted">Tidak ada aktivitas.</li>');
            } else {
                evtList.forEach(evt => {
                    const start = moment(evt.start);
                    const end = moment(evt.end);
                    const duration = end.diff(start, 'minutes') / 60;
                    $list.append(`
                    <li class="list-group-item">
                        <strong>${evt.aktivitas ?? '-'}</strong><br>
                        <small>${start.format('D MMM YYYY HH:mm')} - ${end.format('HH:mm')} (${duration.toFixed(2)} jam)</small>
                    </li>
                `);
                });

                const sorted = evtList.sort((a, b) => new Date(a.start) - new Date(b.start));
                $('#formStartDate').val(sorted[0]?.start.split('T')[0] || '');
                $('#formEndDate').val(sorted[evtList.length - 1]?.end.split('T')[0] || '');
            }

            $('#eventList').addClass('d-none');

            // Sync Check
            const sync = syncResp.data;
            $('#formIdPelaksana').val(pelaksana);
            $('#formMonth').val(month);
            $('#formYear').val(year);
            $('#formIdRencana').val(rencana);

            if (sync) {
                $('#kipappRencana').val(sync.koderk).trigger('change');
                $('#formKegiatan').val(sync.kegiatan);
                $('#formCapaian').val(sync.capaian);
                $('#formLink').val(sync.link);
                $('#formStartDate').val(sync.start_date);
                $('#formEndDate').val(sync.end_date);

                if (sync.synced) {
                    $('#mappingForm input, #mappingForm textarea, #mappingForm select').prop('disabled', true);
                    $('#submitSync').hide();
                    $('#syncWarning').removeClass('d-none');
                    $('#statusFlag')
                        .text('Sinkron')
                        .removeClass()
                        .addClass('ml-2 badge badge-success');
                } else {
                    $('#statusFlag')
                        .text('Belum Sinkron')
                        .removeClass()
                        .addClass('ml-2 badge badge-warning');
                }
            } else {
                $('#formKegiatan').val(kegiatan);
                $('#formCapaian').val(capaian);
                $('#formLink').val('');
                $('#submitSync').show();
                $('#syncWarning').addClass('d-none');
                $('#statusFlag')
                    .text('Belum Mapping')
                    .removeClass()
                    .addClass('ml-2 badge badge-danger');
            }

            // 2. Hide loading modal
            Swal.close();
        }).fail(function () {
            Swal.fire('Gagal Memuat Data', 'Terjadi kesalahan saat mengambil data. Silakan coba lagi.', 'error');
            $('#mappingFormModal').modal('hide');
        });
    });

    $('#toggleEventList').on('click', function () {
        $('#eventList').toggleClass('d-none');
        const isOpen = !$('#eventList').hasClass('d-none');
        $('#toggleEventListIcon').text(isOpen ? '▲' : '▼');
        $(this).contents().filter(function () {
            return this.nodeType === 3; // text node
        }).last().replaceWith(isOpen ? ' Sembunyikan Daftar Aktivitas' : ' Tampilkan Daftar Aktivitas');
    });

    $('#mappingForm').on('submit', function (e) {
        e.preventDefault();

        const formData = $(this).serialize();

        $.ajax({
            url: '/pegawai/kipappsyncs/store',
            method: 'POST',
            data: formData,
            success: function (res) {
                if (res.success) {
                    $('#mappingFormModal').modal('hide');

                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: res.message,
                        timer: 2000,
                        showConfirmButton: false,
                    });

                    reloadTableData();
                }
            },
            error: function (xhr) {
                let errorText = 'Gagal menyimpan data.';
                if (xhr.responseJSON?.message) {
                    errorText = xhr.responseJSON.message;
                }

                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: errorText,
                });

                console.error(xhr.responseText);
            }
        });
    });

    $('#syncAllBtn').on('click', function () {
        const month = $('#filterBulan').val();
        const year = $('#filterTahun').val();
        const token = $('meta[name="csrf-token"]').attr('content');

        Swal.fire({
            title: 'Sinkronkan Semua?',
            text: `Anda akan menyinkronkan semua aktivitas pada bulan ${month}/${year}. Sinkronisasi hanya dapat dilakukan sekali per bulan.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, sinkronkan!',
            cancelButtonText: 'Batal',
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/pegawai/kipappsyncs/sync',
                    type: 'POST',
                    headers: { 'X-CSRF-TOKEN': token },
                    data: { month: month, year: year },
                    success: function (response) {
                        if (response.count > 0) {
                            Swal.fire('Berhasil!', `${response.count} data berhasil disinkronkan.`, 'success')
                                .then(() => reloadTableData());
                        } else {
                            Swal.fire('Info', 'Tidak ada data yang perlu disinkronkan.', 'info');
                        }
                    },
                    error: function (xhr) {
                        Swal.fire('Gagal!', 'Terjadi kesalahan saat sinkronisasi.', 'error');
                    }
                });
            }
        });
    });

    // Reload table on filter change
    $('#filterBulan, #filterTahun').on('change', function () {
        reloadTableData();
    });

    $(window).on('resize', function () {
        if (laporanTable) {
            laporanTable.columns.adjust().responsive.recalc();
        }
    });
});