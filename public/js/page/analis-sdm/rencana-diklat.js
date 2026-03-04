
function showLoading() {
    Swal.fire({
        title: "Memuat Data",
        html: "Mohon tunggu sebentar",
        timerProgressBar: true,
        didOpen: () => {
            Swal.showLoading();
        },
        allowOutsideClick: false
    });
}

function formatRupiah(amount) {
    if (!amount || amount === 0) return '-';
    return 'Rp' + Number(amount).toLocaleString('id-ID');
}

function getStatusBadge(status) {
    let badgeClass = '';

    switch (status) {
        case 'Perencanaan':
            badgeClass = 'badge badge-secondary';
            break;
        case 'Diajukan':
            badgeClass = 'badge badge-info';
            break;
        case 'Disetujui':
            badgeClass = 'badge badge-primary';
            break;
        case 'Selesai':
            badgeClass = 'badge badge-success';
            break;
        case 'Dibatalkan':
            badgeClass = 'badge badge-danger';
            break;
        default:
            badgeClass = 'badge badge-light';
    }

    return `<span class="${badgeClass}">${status}</span>`;
}

function convertToDateInputFormat(dateStr) {
    const parts = dateStr.split('/');
    if (parts.length === 3) {
        return `${parts[2]}-${parts[1].padStart(2, '0')}-${parts[0].padStart(2, '0')}`;
    }
    return dateStr; // fallback if already correct
}

$(document).ready(function () {
    $(function () {
        let table;

        if ($("#table-rencana-diklat").length) {
            table = $("#table-rencana-diklat")
                .dataTable({
                    dom: "Bfrtip",
                    responsive: true, // <--- disable collapsing
                    scrollX: true,     // <--- enable horizontal scroll
                    lengthChange: false,
                    autoWidth: false,
                    buttons: [
                        {
                            extend: "excel",
                            className: "btn-success",
                            text: '<i class="fas fa-file-excel"></i> Excel',
                            exportOptions: {
                                columns: [0, 1, 2, 3],
                                orthogonal: "sort",
                            },
                            customizeData: function (data) {
                                for (var i = 0; i < data.body.length; i++) {
                                    for (var j = 0; j < data.body[i].length; j++) {
                                        data.body[i][j] =
                                            "\u200C" + data.body[i][j];
                                    }
                                }
                            },
                        },
                        {
                            extend: "pdf",
                            className: "btn-danger",
                            text: '<i class="fas fa-file-pdf"></i> PDF',
                            exportOptions: {
                                columns: [0, 1, 2, 3],
                            },
                        },
                    ],
                    oLanguage: {
                        sSearch: "Cari:",
                        sZeroRecords: "Data tidak ditemukan",
                        sEmptyTable: "Data tidak ditemukan",
                        sInfo: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
                        sInfoEmpty: "Menampilkan 0 - 0 dari 0 data",
                        sInfoFiltered: "(disaring dari _MAX_ data)",
                        sLengthMenu: "Tampilkan _MENU_ data",
                        oPaginate: {
                            sPrevious: "Sebelumnya",
                            sNext: "Selanjutnya",
                        },
                    },
                    pageLength: 25,
                })
                .api();

            // move datatable button to inside download button
            $(".dt-buttons").appendTo("#download-button");
            $(".dataTables_filter").appendTo("#filter-search-wrapper");
            $(".dataTables_filter").find("input").addClass("form-control");
            // .dataTables_filter width 100%
            $(".dataTables_filter").css("width", "100%");
            // .dataTables_filter label width 100%
            $(".dataTables_filter label").css("width", "100%");
            // input height 35px
            $(".dataTables_filter input").css("height", "35px");
            // make label text bold and black
            $(".dataTables_filter label").css("font-weight", "bold");
            // remove bottom margin from .dataTables_filter
            $(".dataTables_filter label").css("margin-bottom", "0");

            $(".dataTables_filter input").attr(
                "placeholder",
                "Cari berdasarkan Nama"
            );
            // add padding x 10px to .dataTables_filter input
            $(".dataTables_filter input").css("padding", "0 10px");

            function filterTable() {
                let filterUnitKerja = $("#filter-unit-kerja").val();

                if (filterUnitKerja == "Semua") {
                    filterUnitKerja = "";
                }

                if (filterUnitKerja == "") {
                    table.column(2).search(filterUnitKerja, true, false).draw();
                } else if (filterUnitKerja !== "") {
                    table
                        .column(2)
                        .search("^" + filterUnitKerja + "$", true, false)
                        .draw();
                } else if (filterUnitKerja == "") {
                    table.column(2).search(filterUnitKerja, true, false).draw();
                } else {
                    table
                        .column(2)
                        .search("^" + filterUnitKerja + "$", true, false)
                        .draw();
                }

                // reset numbering in table first column
                table
                    .column(0, { search: "applied", order: "applied" })
                    .nodes()
                    .each(function (cell, i) {
                        cell.innerHTML = i + 1;
                    });
            }
            $("#filter-unit-kerja").on("change", function () {
                filterTable();
            });
        }
        // restart numbering if data table is filter input is changed
        $("#table-rencana-diklat").on("search.dt", function () {
            table
                .column(0, { search: "applied", order: "applied" })
                .nodes()
                .each(function (cell, i) {
                    cell.innerHTML = i + 1;
                });
        });
    });

    $(document).on('click', '.btn-show', function () {
        const id = $(this).data('id');
        showLoading();

        $.ajax({
            url: `/analis-sdm/rencana-diklat/${id}`,
            method: 'GET',
            success: function (data) {
                $('#show-name').text(data.name);
                $('#show-pegawai').text(data.pegawai.name);
                $('#show-tanggal').text(`${data.start_date} s.d. ${data.end_date}`);
                $('#show-metode').text(data.metode);
                $('#show-penyelenggara').text(data.penyelenggara_diklat.penyelenggara);
                $('#show-biaya').text(formatRupiah(data.biaya));
                $('#show-transport').text(formatRupiah(data.transport));
                $('#show-akomodasi').text(formatRupiah(data.akomodasi));
                $('#show-uang-saku').text(formatRupiah(data.uang_saku));
                $('#show-status').html(getStatusBadge(data.status));
                $('#show-keterangan').text(data.keterangan ?? '-');

                swal.close();
                $('#showModal').modal('show');
            },
            error: function () {
                Swal.fire('Error', 'Gagal mengambil detail data.', 'error');
            }
        });
    });

    $(document).on('click', '.btn-edit', function () {
        const id = $(this).data('id');
        showLoading();
        // Show the modal
        $('#editModal').modal('show');

        $.ajax({
            url: `/analis-sdm/rencana-diklat/${id}`,
            method: 'GET',
            success: function (data) {

                $('#diklat-id-pegawai').empty();

                // Append the current pegawai as selected
                $('#diklat-id-pegawai').append(
                    $('<option>', {
                        value: data.id_pegawai,
                        text: data.pegawai.name,
                        selected: true
                    })
                );

                $('#diklat-penyelenggara').empty();
                $('#diklat-penyelenggara').append(
                    $('<option>', {
                        value: data.penyelenggara,
                        text: data.penyelenggara_diklat.penyelenggara,
                        selected: true
                    })
                );

                // Fill form inputs
                $('#form-edit-diklat').attr('action', `/analis-sdm/rencana-diklat/${id}`);
                $('#diklat-id').val(data.id);
                $('#diklat-name').val(data.name).prop('disabled', true);;

                $('#diklat-start-date').val(convertToDateInputFormat(data.start_date));
                $('#diklat-end-date').val(convertToDateInputFormat(data.end_date));
                $('#diklat-metode').val(data.metode).trigger('change').prop('disabled', true);
                $('#diklat-biaya').val(data.biaya).prop('disabled', true);
                $('#diklat-transport').val(data.transport).prop('disabled', true);
                $('#diklat-akomodasi').val(data.akomodasi).prop('disabled', true);
                $('#diklat-uang-saku').val(data.uang_saku).prop('disabled', true);
                $('#diklat-pembebanan').val(data.pembebanan_perjadin ? data.pembebanan_perjadin : '_NULL_').trigger('change').prop('disabled', true);
                $('#diklat-akun-anggaran').val(data.akun_anggaran ? data.akun_anggaran : '_NULL_').trigger('change').prop('disabled', true);
                const statusSelect = $('#diklat-status');
                statusSelect.empty();

                if (data.status === 'Selesai') {
                    statusSelect.append(new Option('Selesai', 'Selesai', true, true));
                } else {
                    const options = [
                        { text: 'Perencanaan', value: 'Perencanaan' },
                        { text: 'Diajukan', value: 'Diajukan' },
                        { text: 'Disetujui', value: 'Disetujui' },
                        { text: 'Dibatalkan', value: 'Dibatalkan' },
                        { text: 'Selesai', value: 'Selesai' },
                    ];
                    options.forEach(opt => {
                        statusSelect.append(new Option(opt.text, opt.value, opt.value === data.status, opt.value === data.status));
                    });
                }
                $('#diklat-keterangan').val(data.keterangan);

                swal.close();
            },
            error: function (xhr) {
                const errors = xhr.responseJSON.errors;
                let errorMsg = '';

                if (errors) {
                    Object.keys(errors).forEach(key => {
                        errorMsg += `${errors[key][0]}<br>`;
                    });
                } else {
                    errorMsg = 'Terjadi kesalahan. Silakan coba lagi.';
                }

                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    html: errorMsg
                });
            }
        });
    });

    $('#form-edit-diklat').on('submit', function (e) {
        e.preventDefault(); // prevent normal form submission

        const form = $(this);

        $.ajax({
            url: '/analis-sdm/rencana-diklat/' + $('#diklat-id').val() + '/ajax-update',
            method: 'POST',
            data: form.serialize(),
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: function (response) {
                $('#editModal').modal('hide');
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: response.message
                });
                location.reload();
            },
            error: function (xhr) {
                const errors = xhr.responseJSON.errors;
                let errorMsg = '';

                if (errors) {
                    Object.keys(errors).forEach(key => {
                        errorMsg += `${errors[key][0]}<br>`;
                    });
                } else {
                    errorMsg = 'Terjadi kesalahan. Silakan coba lagi.';
                }

                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    html: errorMsg
                });
            }
        });
    });

    $(document).on('click', '#btn-open-create', function () {
        showLoading();
        $('#createModal').modal('show');
        swal.close();
    });

    $('#form-create-diklat').on('submit', function (e) {
        e.preventDefault();

        const form = $(this);
        $.ajax({
            url: '/analis-sdm/rencana-diklat/ajax-store',
            method: 'POST',
            data: form.serialize(),
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response) {
                $('#createModal').modal('hide');
                Swal.fire('Sukses', response.message, 'success');
                location.reload();
            },
            error: function (xhr) {
                const errors = xhr.responseJSON.errors;
                let errorMsg = '';

                if (errors) {
                    Object.values(errors).forEach(errList => {
                        errorMsg += errList[0] + '<br>';
                    });
                } else {
                    errorMsg = 'Terjadi kesalahan saat menyimpan.';
                }

                Swal.fire('Gagal', errorMsg, 'error');
            }
        });
    });
});