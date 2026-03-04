$(".submit-btn").on("click", function (e) {
    e.preventDefault();

    var form = document.getElementById('formPenetapanAnalisSDM');
    var formData = new FormData(form);
    let token = $("meta[name='csrf-token']").attr("content");
    formData.append('_token', token);

    $.ajax({
        url: `/analis-sdm/upload-skp/store`,
        type: "POST",
        cache: false,
        data: formData,
        contentType: false,
        processData: false, 
        success: function (response) {
            location.reload();
        },
        error: function (error) {           
            let errorResponses = error.responseJSON;
            let errors = Object.entries(errorResponses.errors);

            errors.forEach(([key, value]) => {
                let errorMessage = document.getElementById(`error-${key}`);
                errorMessage.innerText = `${value}`;
            });
            // console.log(errors);
        },
    });
});

$(".submit-btn-penilaian").on("click", function (e) {
    e.preventDefault();

    const isActive = $('#statusToggle').is(':checked'); // toggle "Aktif" = true

    if (!isActive) {
        Swal.fire({
            title: 'Apakah yakin?',
            text: 'SKP dengan status TIDAK AKTIF, TIDAK BISA DIKEMBALIKAN!!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                submitSKPPenilaian(); // lanjut submit
            }
        });
    } else {
        submitSKPPenilaian(); // langsung submit
    }
});

$(".submit-btn-penilaian-wilayah").on("click", function (e) {
    e.preventDefault();

    var form = document.getElementById('formPenilaianAnalisSDMWilayah');
    var formData = new FormData(form);
    let token = $("meta[name='csrf-token']").attr("content");
    formData.append('_token', token);

    $.ajax({
        url: `/analis-sdm/upload-skp/store`,
        type: "POST",
        cache: false,
        data: formData,
        contentType: false,
        processData: false, 
        success: function (response) {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: 'SKP berhasil disimpan.',
                timer: 2000,
                showConfirmButton: false
            }).then(() => location.reload());
        },
        error: function (error) {           
            let errorResponses = error.responseJSON;
            let errors = Object.entries(errorResponses.errors);

            errors.forEach(([key, value]) => {              
                let errorMessage = document.getElementById(`error-${key}`);
                errorMessage.innerText = `${value}`;
            });
            // console.log(errors);
        },
    });
});

function submitSKPPenilaian(){
    var form = document.getElementById('formPenilaianAnalisSDM');
    var formData = new FormData(form);
    let token = $("meta[name='csrf-token']").attr("content");
    formData.append('_token', token);

    $.ajax({
        url: `/analis-sdm/upload-skp/store`,
        type: "POST",
        cache: false,
        data: formData,
        contentType: false,
        processData: false, 
        success: function (response) {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: 'SKP berhasil disimpan.',
                timer: 2000,
                showConfirmButton: false
            }).then(() => location.reload());
        },
        error: function (error) {           
            let errorResponses = error.responseJSON;
            let errors = Object.entries(errorResponses.errors);

            errors.forEach(([key, value]) => {
                let errorMessage = document.getElementById(`error-${key}`);
                errorMessage.innerText = `${value}`;
            });
            // console.log(errors);
        },
    });
}

$('#table-cek-skp').find("td.convert").each(function() {
    $(this).attr('value', $(this).text());
});

var datatable = $('#table-cek-skp').dataTable({
    dom: "Bfrtip",
    responsive: false,
    lengthChange: false,
    autoWidth: false,
    scrollX: true,
    pageLength: 25,
    buttons: [
        {
            extend: "excel",
            className: "btn-success",
            text: '<i class="fas fa-file-excel"></i> Excel',
            exportOptions: {
                columns: [0, 1, 2, 3, 4, 5, 6, 7,8,9,10,11,12,13,14,15,16],
            },
        },
    ],
    columnDefs: [{
        "targets": 0,
        "createdCell": function(td, cellData, rowData, row, col) {
            $(td).text(row + 1);
        }
    }]
}).api();

//update ukuran tabel saat ukuran sidebar berubah
$('.nav-link').on("click", function() {
    setTimeout(function() {
        datatable.columns.adjust();
    }, 500);
});

$('#yearSelect').on('change', function() {
    let year = $(this).val();
    $('#yearForm').attr('action', `?year=${year}`);
    $('#yearForm').find('[name="_token"]').remove();
    $('#yearForm').submit();
});

$('#unitSelect').on('change', function() {
    let unit = $(this).val();
    $('#yearForm').attr('action', `?unitKerja=${unit}`);
    $('#yearForm').find('[name="_token"]').remove();
    $('#yearForm').submit();
});

$(".detail").attr('href', function(_, el) {
    return el.replace(/\/[^\/]*$/, '/' + $('#yearSelect').val());
});

$('#table-cek-skp').on('draw.dt', function() {
    $(".detail").attr('href', function(_, el) {
        return el.replace(/\/[^\/]*$/, '/' + $('#yearSelect').val());
    });
});

$(document).on('click', '.create-skp-penetapan', function() {
    $('#create-tahun-penetapan').val($(this).data('tahun-penetapan'));
    $('#create-jenis-penetapan').val($(this).data('jenis-penetapan'));
    $('#create-user-id-penetapan').val($(this).data('user-id-penetapan'));

});

$('#modal-create-skp-penilaian').on('shown.bs.modal', function () {
    toggleFormFields(); // set kondisi awal saat modal dibuka
});

$(document).on('click', '.create-skp-penilaian', function() {
    const isChecked = $('#toggleStatusSKP').is(':checked');
    toggleFormFields(!isChecked);            
    $('#create-tahun-penilaian').val($(this).data('tahun-penilaian'));
    $('#create-bulan-penilaian').val($(this).data('bulan-penilaian'));
    $('#create-jenis-penilaian').val($(this).data('jenis-penilaian'));
    $('#create-user-id-penilaian').val($(this).data('user-id-penilaian'));
    $('#create-bulan').val($(this).data('bulan'));
    $('#create-tahun-penilaian-wilayah').val($(this).data('tahun-penilaian'));
    $('#create-bulan-penilaian-wilayah').val($(this).data('bulan-penilaian'));
    $('#create-jenis-penilaian-wilayah').val($(this).data('jenis-penilaian'));
    $('#create-user-id-penilaian-wilayah').val($(this).data('user-id-penilaian'));    
});

function toggleFormFields() {
    const isActive = $('#statusToggle').is(':checked');
    $('#formContainer').toggle(isActive);
    $('#statusToggle').val(isActive ? 'aktif' : 'tidak aktif');
    $('.custom-switch-description').text(isActive ? 'Aktif' : 'Tidak Aktif');

    // hapus "required" jika tidak aktif
    $('#formContainer')
        .find('input, select, textarea')
        .prop('required', isActive);
}

$('#statusToggle').on('change', toggleFormFields);