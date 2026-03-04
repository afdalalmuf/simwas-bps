

function checkInputsAndToggleNext() {
    const allFilled = Array.from(inputsStep1).every(
        (input) => input.value.trim() !== ""
    );
    nextButton.disabled = !allFilled;
    nextButton.onclick = allFilled ? () => stepper1.next() : null;
}

function toggleComment(documentType) {
    const statusSelect = document.getElementById(`verifikasi_${documentType}`);
    const commentSection = document.getElementById(`wrapper_comments_${documentType}`);
    const commentTextarea = document.getElementById(`comments_${documentType}`);
    if (statusSelect && commentSection) {
        if (statusSelect.value === "invalid") {
            commentSection.style.display = "block";
        } else {
            commentSection.style.display = "none";
            commentTextarea.value = "";
        }
    }
}

function countTotalUH() {
    const hari = parseInt($("#jumlah_hari").val()) || 0;
    const uangHarian = parseInt($("#uh_diklat").val()) || 0;
    const total = hari * uangHarian;
    $("#total_uh").val(total);
}

function countTranslok() {
    const hari = parseInt($("#hari_translok").val()) || 0;
    const total = hari * 170000; // Asumsi 1 hari Translok = Rp 170.000
    $("#nominal_translok").val(total);
}

function updateSummaryModal() {
    const biaya = {
        hotel: parseInt($("#nominal_hotel_create").val()) || 0,
        transportBerangkat: parseInt($("#nominal_transport_berangkat").val()) || 0,
        transportPulang: parseInt($("#nominal_transport_pulang").val()) || 0,
        transportLokal: parseInt($("#nominal_translok").val()) || 0,
        totalUH: parseInt($("#total_uh").val()) || 0,
        uhBerangkat: parseInt($("#uh_berangkat").val()) || 0,
        uhPulang: parseInt($("#uh_pulang").val()) || 0,
    };

    const total = Object.values(biaya).reduce((sum, val) => sum + val, 0);

    $("#summaryHotel").text(formatRupiah(biaya.hotel));
    $("#summaryTransportBerangkat").text(formatRupiah(biaya.transportBerangkat));
    $("#summaryTransportPulang").text(formatRupiah(biaya.transportPulang));
    $("#summaryTranslok").text(formatRupiah(biaya.transportLokal));
    $("#summaryTotalUH").text(formatRupiah(biaya.totalUH));
    $("#summaryUHB").text(formatRupiah(biaya.uhBerangkat));
    $("#summaryUHP").text(formatRupiah(biaya.uhPulang));
    $("#summaryGrandTotal").text(formatRupiah(total));

    let verifikasiHTML = '<div class="list-group">';
    documentTypes.forEach(doc => {
        const status = $(`#verifikasi_${doc}`).val();
        const comment = $(`#comments_${doc}`).val();
        const badge = status === 'valid' ? 'Sesuai' : 'Tidak Sesuai';
        const badgeClass = status === 'valid' ? 'success' : 'danger';

        verifikasiHTML += `
            <div class="list-group-item d-flex justify-content-between align-items-start">
                <div class="ms-2 me-auto">
                    <div class="fw-bold">${formatLabel(doc)}</div>
                    <small>${comment || '-'}</small>
                </div>
                <span class="badge badge-${badgeClass}">${badge}</span>
            </div>
        `;
    });
    verifikasiHTML += '</div>';

    $("#documentVerifications").html(verifikasiHTML);
    $("#modalSummary").modal("show");
}

function formatRupiah(number) {
    return 'Rp ' + number.toLocaleString('id-ID');
}

function formatLabel(text) {
    return text.replace(/-/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
}

function validateAllVerificationStatuses() {
    let missingStatus = [];
    let missingComments = [];

    documentTypes.forEach((doc) => {
        const status = $(`#verifikasi_${doc}`).val();
        const comment = $(`#comments_${doc}`).val();

        if (!status) {
            missingStatus.push(formatLabel(doc));
        } else if (status === "invalid" && (!comment || comment.trim() === "")) {
            missingComments.push(formatLabel(doc));
        }
    });

    if (missingStatus.length > 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Verifikasi Belum Lengkap',
            html: `Status verifikasi berikut belum dipilih:<br><strong>${missingStatus.join(', ')}</strong>`,
            confirmButtonText: 'Lengkapi Dulu'
        });
        return false;
    }

    if (missingComments.length > 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Catatan Wajib Diisi',
            html: `Dokumen berikut diberi status <strong>Tidak Sesuai</strong>, maka catatan wajib diisi:<br><strong>${missingComments.join(', ')}</strong>`,
            confirmButtonText: 'Lengkapi Dulu'
        });
        return false;
    }

    return true;
}

$(document).ready(function () {
    const stepperEl = document.querySelector("#stepper1");
    window.stepper1 = new Stepper(stepperEl);
    $('[data-toggle="tooltip"]').tooltip();
    const formStep1 = document.querySelector("#test-l-1");
    const nextButton = document.querySelector("#next-form");
    const inputsStep1 = formStep1.querySelectorAll("input[required], select[required], textarea[required]");
    countTranslok();
    countTotalUH();
    $("#jumlah_hari").keyup(countTotalUH);
    $("#uh_diklat").keyup(countTotalUH);
    $("#hari_translok").keyup(countTranslok);

    inputsStep1.forEach((input) => {
        input.addEventListener("input", checkInputsAndToggleNext);
        input.addEventListener("change", checkInputsAndToggleNext);
    });

    documentTypes.forEach((doc) => {
        toggleComment(doc);
        $(`#verifikasi_${doc}`).change(() => toggleComment(doc));
    });

    $("#verification-button").on("click", function () {
        if (validateAllVerificationStatuses()) {
            updateSummaryModal();
        }
    });

    $("#save-verification-button").on("click", function () {
        const button = $(this);
        button.prop("disabled", true);

        const idSpj = $("#id_spj").val();

        const verifications = documentTypes.map(type => ({
            document_type: type,
            status: $(`#verifikasi_${type}`).val(),
            comments: $(`#comments_${type}`).val()
        }));

        const spjData = {
            nominal_hotel: $("#nominal_hotel_create").val(),
            nominal_translok: $("#nominal_translok").val(),
            nominal_transport_berangkat: $("#nominal_transport_berangkat").val(),
            nominal_transport_pulang: $("#nominal_transport_pulang").val(),
            uang_diklat: $("#uh_diklat").val(),
            uang_harian_berangkat: $("#uh_berangkat").val(),
            uang_harian_pulang: $("#uh_pulang").val(),
            hari_diklat: $("#jumlah_hari").val(),
            tgl_mulai_st: $("#tgl_mulai_st").val(),
            tgl_selesai_st: $("#tgl_selesai_st").val(),
            no_st: $("#no-st-create").val(),
            tgl_spd: $("#tgl_spd_create").val(),
            no_spd: $("#no-spd-create").val(),
            km_berangkat: $("#km_berangkat_create").val()
        };

        $.ajax({
            url: `/verifikator-spj/spj-diklat/save/${idSpj}`,
            method: "POST",
            data: {
                _token: $('meta[name="csrf-token"]').attr("content"),
                verifications: verifications,
                spj: spjData
            },
            success: function (response) {
                $("#modalSummary").modal("hide");
                Swal.fire({
                    icon: "success",
                    title: "Tersimpan!",
                    text: response.message || "Data berhasil disimpan",
                }).then(() => {
                    window.location.href = "/verifikator-spj/spj-diklat";
                });
            },
            error: function (xhr) {
                button.prop("disabled", false);
                Swal.fire("Gagal", xhr.responseJSON?.message || "Terjadi kesalahan saat menyimpan", "error");
            }
        });
    });
});
