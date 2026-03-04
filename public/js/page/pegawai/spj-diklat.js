document.addEventListener("DOMContentLoaded", function () {
    $(document).ready(function () {
        $(".catatan-button").on("click", function () {
            const spjId = $(this).data("spj-id");

            $.ajax({
                url: `/pegawai/spj-diklat/verification-list/${spjId}`,
                method: "GET",
                success: function (verifications) {
                    // ❗️ Filter out entries with null status
                    const filtered = verifications.filter(item => item.status !== null);

                    if (!filtered.length) {
                        $("#documentVerifications").html("<p>Tidak ada catatan ditemukan.</p>");
                    } else {
                        let verifikasiHTML = '<div class="list-group">';

                        filtered.forEach((item) => {
                            const badge = item.status === 'valid' ? 'Sesuai' : 'Tidak Sesuai';
                            const badgeClass = item.status === 'valid' ? 'success' : 'danger';

                            verifikasiHTML += `
                            <div class="list-group-item d-flex justify-content-between align-items-start">
                                <div class="ms-2 me-auto">
                                    <div class="fw-bold">${formatLabel(item.document_type)}</div>
                                    <small>${item.comments || '-'}</small>
                                </div>
                                <span class="badge badge-${badgeClass}">${badge}</span>
                            </div>
                        `;
                        });

                        verifikasiHTML += "</div>";
                        $("#documentVerifications").html(verifikasiHTML);
                    }

                    $("#modalSummary").modal("show");
                },
                error: function (xhr) {
                    Swal.fire("Gagal", "Gagal mengambil catatan verifikasi", "error");
                }
            });
        });
    });


    // Helpers
    function formatLabel(text) {
        const forceUppercase = ['spd', 'kak'];

        if (forceUppercase.includes(text.toLowerCase())) {
            return text.toUpperCase();
        }

        return text
            .replace(/-/g, ' ')
            .replace(/\b\w/g, l => l.toUpperCase());
    }

    function updateNoSt() {
        const input1 = document.getElementById("input1").value.trim();
        const input2 = document.getElementById("input2").value.trim();
        const input3 = document.getElementById("input3").value.trim();
        const input4 = document.getElementById("input4").value.trim();

        const noSt = `B-${input1}/${input2}/${input3}/${input4}`;
        document.getElementById("no-st-create").value = noSt;
    }

    // Tambahkan event listener ke semua input
    ["input1", "input2", "input3", "input4"].forEach((id) => {
        const el = document.getElementById(id);
        if (el) {
            el.addEventListener("input", updateNoSt);
        }
    });

    // Inisialisasi Stepper
    const stepperEl = document.querySelector("#stepper1");
    window.stepper1 = new Stepper(stepperEl);

    // --- LOGIKA TOMBOL NEXT STEP 1 ---
    const formStep1 = document.querySelector("#test-l-1");
    const nextButton = document.querySelector("#next-form");
    const inputsStep1 = formStep1.querySelectorAll(
        "input[required], select[required], textarea[required]"
    );

    function checkInputsAndToggleNext() {
        const allFilled = Array.from(inputsStep1).every(
            (input) => input.value.trim() !== ""
        );
        if (allFilled) {
            nextButton.disabled = false;
            nextButton.onclick = () => stepper1.next();
        } else {
            nextButton.disabled = false;
            nextButton.onclick = () => stepper1.next();
        }
    }

    inputsStep1.forEach((input) => {
        input.addEventListener("input", checkInputsAndToggleNext);
        input.addEventListener("change", checkInputsAndToggleNext);
    });
    checkInputsAndToggleNext();

    function updateNoSpd() {
        const input_spd1 = document.getElementById("input_spd1").value.trim();
        const input_spd2 = document.getElementById("input_spd2").value.trim();
        const input_spd3 = document.getElementById("input_spd3").value.trim();
        const input_spd4 = document.getElementById("input_spd4").value.trim();
        const input_spd5 = document.getElementById("input_spd5").value.trim();

        const noSpd = `${input_spd1}/${input_spd2}/${input_spd3}/${input_spd4}/${input_spd5}`;
        document.getElementById("no-spd-create").value = noSpd;
    }

    // Tambahkan event listener ke semua input
    [
        "input_spd1",
        "input_spd2",
        "input_spd3",
        "input_spd4",
        "input_spd5",
    ].forEach((id) => {
        const el = document.getElementById(id);
        if (el) {
            el.addEventListener("input", updateNoSpd);
        }
    });

    // --- TOGGLE FORM INVOICE HOTEL ---
    const hotelYa = document.getElementById("hotel_ya");
    const hotelTidak = document.getElementById("hotel_tidak");
    const formInvoice = document.getElementById("form-invoice-hotel");
    const nominalHotel = document.getElementById("nominal_hotel_create");
    const fileHotel = document.getElementById("file_hotel");

    function toggleInvoiceForm() {
        if (hotelYa.checked) {
            formInvoice.style.display = "block";
            nominalHotel.required = true;
            fileHotel.required = true;
        } else {
            formInvoice.style.display = "none";
            nominalHotel.required = false;
            fileHotel.required = false;
        }
    }

    hotelYa.addEventListener("change", toggleInvoiceForm);
    hotelTidak.addEventListener("change", toggleInvoiceForm);
    toggleInvoiceForm();

    // --- HITUNG TOTAL UANG HARIAN DIKLAT ---
    const jumlahHari = document.getElementById("jumlah_hari");
    const uangHarian = document.getElementById("uh_diklat_create");
    const totalUH = document.getElementById("total_uh");

    function hitungTotalUH() {
        const hari = parseInt(jumlahHari?.value) || 0;
        const uang = parseFloat(uangHarian?.value) || 0;
        const total = hari * uang;
        if (totalUH) {
            totalUH.value = total.toLocaleString("id-ID", {
                style: "currency",
                currency: "IDR",
            });
        }
    }

    if (jumlahHari && uangHarian && totalUH) {
        jumlahHari.addEventListener("input", hitungTotalUH);
        uangHarian.addEventListener("input", hitungTotalUH);
        hitungTotalUH();
    }

    const berangkatYa = document.getElementById("berangkat_ya");
    const berangkatTidak = document.getElementById("berangkat_tidak");
    const formBerangkat = document.getElementById("form-harian-berangkat");
    const uhH_1 = document.getElementById("uh_h_1_create");

    function toggleBerangkatForm() {
        if (berangkatYa.checked) {
            formBerangkat.style.display = "block";
            uhH_1.required = true;
        } else {
            formBerangkat.style.display = "none";
            uhH_1.required = false;
        }
    }

    berangkatYa.addEventListener("change", toggleBerangkatForm);
    berangkatTidak.addEventListener("change", toggleBerangkatForm);
    toggleBerangkatForm();

    const pulangYa = document.getElementById("pulang_ya");
    const pulangTidak = document.getElementById("pulang_tidak");
    const formPulang = document.getElementById("form-harian-pulang");
    const uhH1 = document.getElementById("uh_h1_create");

    function togglPulangForm() {
        if (pulangYa.checked) {
            formPulang.style.display = "block";
            uhH1.required = true;
        } else {
            formPulang.style.display = "none";
            uhH1.required = false;
        }
    }

    pulangYa.addEventListener("change", togglPulangForm);
    pulangTidak.addEventListener("change", togglPulangForm);
    togglPulangForm();

    const dalkot = document.getElementById("dalkot");
    const lukot = document.getElementById("lukot");
    const pribadi = document.getElementById("pribadi");
    const umum = document.getElementById("umum");
    const transLuarKota = document.getElementById("trans-luar-kota");
    const transDalamKota = document.getElementById("trans-dalam-kota");
    const katLuarKota = document.getElementById("kat-luar-kota");
    //field Luar Kota
    const nomTransBerangkatLukot = document.getElementById(
        "nominal_trans_lukot_berangkat_create"
    );
    const tglTransBerangkatLukot = document.getElementById(
        "tgl_trans_lukot_berangkat_create"
    );
    const fileTransBerangkatLukot = document.getElementById(
        "file_trans_lukot_berangkat"
    );
    const nomTransPulangLukot = document.getElementById(
        "nominal_trans_lukot_pulang_create"
    );
    const tglTransPulangLukot = document.getElementById(
        "tgl_trans_lukot_pulang_create"
    );
    const fileTransPulangLukot = document.getElementById(
        "file_trans_lukot_pulang"
    );

    //Field Dalam Kota
    const jarakBerangkat = document.getElementById("jarak-berangkat");
    const catatanPribadi = document.getElementById("catatan-pribadi");
    const catatanPribadi2 = document.getElementById("catatan-pribadi2");

    function toogleTransportasi() {
        if (lukot.checked) {
            katLuarKota.style.display = "block";
            transDalamKota.style.display = "none";
            if (umum.checked) {
                transLuarKota.style.display = "block";
                jarakBerangkat.style.display = "none";
                catatanPribadi.style.display = "none";
                catatanPribadi2.style.display = "none";
                nomTransBerangkatLukot.required = true;
                tglTransBerangkatLukot.required = true;
                fileTransBerangkatLukot.required = true;
                nomTransPulangLukot.required = true;
                tglTransPulangLukot.required = true;
                fileTransPulangLukot.required = true;
            } else if (pribadi.checked) {
                transLuarKota.style.display = "block";
                jarakBerangkat.style.display = "block";
                catatanPribadi.style.display = "block";
                catatanPribadi2.style.display = "block";
            }
        } else if (dalkot.checked) {
            katLuarKota.style.display = "none";
            transDalamKota.style.display = "block";
            nomTransBerangkatLukot.required = false;
            tglTransBerangkatLukot.required = false;
            fileTransBerangkatLukot.required = false;
            nomTransPulangLukot.required = false;
            tglTransPulangLukot.required = false;
            fileTransPulangLukot.required = false;
            umum.checked = false;
            pribadi.checked = false;
            transLuarKota.style.display = "none";
        }
    }

    dalkot.addEventListener("change", toogleTransportasi);
    lukot.addEventListener("change", toogleTransportasi);
    pribadi.addEventListener("change", toogleTransportasi);
    umum.addEventListener("change", toogleTransportasi);
    toogleTransportasi();

    document.querySelectorAll(".preview-button").forEach((btn) => {
        btn.addEventListener("click", function () {
            // Ambil data input yang relevan
            function getValue(id) {
                const el = document.getElementById(id);
                if (!el) return "-";
                return el.type === "file"
                    ? el.files[0]?.name || "-"
                    : el.value || "-";
            }

            function isChecked(id) {
                const el = document.getElementById(id);
                return el?.checked ? "Ya" : "Tidak";
            }

            function formatTanggal(tanggalStr) {
                if (!tanggalStr) return "-";
                const [year, month, day] = tanggalStr.split("-");
                return `${day}-${month}-${year}`;
            }

            function formatRibuan(str) {
                return Number(str.replace(/\D/g, "") || 0).toLocaleString(
                    "id-ID"
                );
            }

            function getValue(id) {
                const el = document.getElementById(id);
                return el ? el.value : "0"; // fallback ke "0" jika elemen tidak ditemukan
            }

            function parseNominal(id) {
                let val = getValue(id); // Ambil nilai input
                if (!val) return 0;
                return Number(val.toString().replace(/\./g, "")); // Hilangkan titik, ubah ke number
            }

            const fileSTInput = document.getElementById("file_st");
            const fileST = fileSTInput?.files[0];
            let previewFile = "<p>Tidak ada file</p>";

            if (fileST) {
                const fileURL = URL.createObjectURL(fileST);
                const ext = fileST.name.split(".").pop().toLowerCase();

                if (ext === "pdf") {
                    previewFile = `<embed src="${fileURL}" type="application/pdf" width="100%" height="500px">`;
                } else {
                    previewFile = `<a href="${fileURL}" target="_blank">Lihat File</a>`;
                }
            } else {
                // Cek file dari database jika ada
                const dbPath = document.getElementById("st_path")?.value;
                if (dbPath) {
                    const fileURL = `${window.location.origin}/${dbPath}`;
                    const ext = dbPath.split(".").pop().toLowerCase();

                    if (ext === "pdf") {
                        previewFile = `<embed src="${fileURL}" type="application/pdf" width="100%" height="500px">`;
                    } else {
                        previewFile = `<a href="${fileURL}" target="_blank">Lihat File</a>`;
                    }
                }
            }

            const fileSPDInput = document.getElementById("file_spd");
            const fileSPD = fileSPDInput?.files[0];
            let previewFileSPD = "<p>Tidak ada file</p>";

            if (fileSPD) {
                const fileURL = URL.createObjectURL(fileSPD);
                const ext = fileSPD.name.split(".").pop().toLowerCase();

                if (ext === "pdf") {
                    previewFileSPD = `<embed src="${fileURL}" type="application/pdf" width="100%" height="500px">`;
                } else {
                    previewFileSPD = `<a href="${fileURL}" target="_blank">Lihat File</a>`;
                }
            } else {
                // Cek file dari database jika ada
                const dbPath = document.getElementById("spd_path")?.value;
                if (dbPath) {
                    const fileURL = `${window.location.origin}/${dbPath}`;
                    const ext = dbPath.split(".").pop().toLowerCase();

                    if (ext === "pdf") {
                        previewFileSPD = `<embed src="${fileURL}" type="application/pdf" width="100%" height="500px">`;
                    } else {
                        previewFileSPD = `<a href="${fileURL}" target="_blank">Lihat File</a>`;
                    }
                }
            }

            const filePenginapanInput = document.getElementById("file_hotel");
            const filePenginapan = filePenginapanInput?.files[0];
            let previewFilePenginapan = "<p>Tidak ada file</p>";

            if (filePenginapan) {
                const fileURL = URL.createObjectURL(filePenginapan);
                const ext = filePenginapan.name.split(".").pop().toLowerCase();

                if (ext === "pdf") {
                    previewFilePenginapan = `<embed src="${fileURL}" type="application/pdf" width="100%" height="500px">`;
                } else {
                    previewFilePenginapan = `<a href="${fileURL}" target="_blank">Lihat File</a>`;
                }
            } else {
                // Cek file dari database jika ada
                const dbPath = document.getElementById("hotel_path")?.value;
                if (dbPath) {
                    const fileURL = `${window.location.origin}/${dbPath}`;
                    const ext = dbPath.split(".").pop().toLowerCase();

                    if (ext === "pdf") {
                        previewFilePenginapan = `<embed src="${fileURL}" type="application/pdf" width="100%" height="500px">`;
                    } else {
                        previewFilePenginapan = `<a href="${fileURL}" target="_blank">Lihat File</a>`;
                    }
                }
            }

            const fileTransportLokalInput = document.getElementById(
                "file_trans_dalkot"
            );
            const fileTransportLokal =
                fileTransportLokalInput?.files[0];
            let previewFileTransportLokal = "<p>Tidak ada file</p>";

            if (fileTransportLokal) {
                const fileURL = URL.createObjectURL(
                    fileTransportLokal
                );
                const ext = fileTransportLokal.name
                    .split(".")
                    .pop()
                    .toLowerCase();

                if (ext === "pdf") {
                    previewFileTransportLokal = `<embed src="${fileURL}" type="application/pdf" width="100%" height="500px">`;
                } else {
                    previewFileTransportLokal = `<a href="${fileURL}" target="_blank">Lihat File</a>`;
                }
            } else {
                // Cek file dari database jika ada
                const dbPath = document.getElementById(
                    "file_trans_dalkot_path"
                )?.value;
                if (dbPath) {
                    const fileURL = `${window.location.origin}/${dbPath}`;
                    const ext = dbPath.split(".").pop().toLowerCase();

                    if (ext === "pdf") {
                        previewFileTransportLokal = `<embed src="${fileURL}" type="application/pdf" width="100%" height="500px">`;
                    } else {
                        previewFileTransportLokal = `<a href="${fileURL}" target="_blank">Lihat File</a>`;
                    }
                }
            }

            // const fileTransportDalkotPulangInput = document.getElementById(
            //     "file_trans_dalkot_pulang"
            // );
            // const fileTransportDalkotPulang =
            //     fileTransportDalkotPulangInput?.files[0];
            // let previewFileTransportDalkotPulang = "<p>Tidak ada file</p>";

            // if (fileTransportDalkotPulang) {
            //     const fileURL = URL.createObjectURL(fileTransportDalkotPulang);
            //     const ext = fileTransportDalkotPulang.name
            //         .split(".")
            //         .pop()
            //         .toLowerCase();

            //     if (ext === "pdf") {
            //         previewFileTransportDalkotPulang = `<embed src="${fileURL}" type="application/pdf" width="100%" height="500px">`;
            //     } else {
            //         previewFileTransportDalkotPulang = `<a href="${fileURL}" target="_blank">Lihat File</a>`;
            //     }
            // } else {
            //     // Cek file dari database jika ada
            //     const dbPath = document.getElementById(
            //         "file_trans_dalkot_pulang_path"
            //     )?.value;
            //     if (dbPath) {
            //         const fileURL = `${window.location.origin}/${dbPath}`;
            //         const ext = dbPath.split(".").pop().toLowerCase();

            //         if (ext === "pdf") {
            //             previewFileTransportDalkotPulang = `<embed src="${fileURL}" type="application/pdf" width="100%" height="500px">`;
            //         } else {
            //             previewFileTransportDalkotPulang = `<a href="${fileURL}" target="_blank">Lihat File</a>`;
            //         }
            //     }
            // }

            const fileTransportLukotBerangkatInput = document.getElementById(
                "file_trans_lukot_berangkat"
            );
            const fileTransportLukotBerangkat =
                fileTransportLukotBerangkatInput?.files[0];
            let previewFileTransportLukotBerangkat = "<p>Tidak ada file</p>";

            if (fileTransportLukotBerangkat) {
                const fileURL = URL.createObjectURL(
                    fileTransportLukotBerangkat
                );
                const ext = fileTransportLukotBerangkat.name
                    .split(".")
                    .pop()
                    .toLowerCase();

                if (ext === "pdf") {
                    previewFileTransportLukotBerangkat = `<embed src="${fileURL}" type="application/pdf" width="100%" height="500px">`;
                } else {
                    previewFileTransportLukotBerangkat = `<a href="${fileURL}" target="_blank">Lihat File</a>`;
                }
            } else {
                // Cek file dari database jika ada
                const dbPath = document.getElementById(
                    "file_trans_lukot_berangkat_path"
                )?.value;
                if (dbPath) {
                    const fileURL = `${window.location.origin}/${dbPath}`;
                    const ext = dbPath.split(".").pop().toLowerCase();

                    if (ext === "pdf") {
                        previewFileTransportLukotBerangkat = `<embed src="${fileURL}" type="application/pdf" width="100%" height="500px">`;
                    } else {
                        previewFileTransportLukotBerangkat = `<a href="${fileURL}" target="_blank">Lihat File</a>`;
                    }
                }
            }

            const fileTransportLukotPulangInput = document.getElementById(
                "file_trans_lukot_pulang"
            );
            const fileTransportLukotPulang =
                fileTransportLukotPulangInput?.files[0];
            let previewFileTransportLukotPulang = "<p>Tidak ada file</p>";

            if (fileTransportLukotPulang) {
                const fileURL = URL.createObjectURL(fileTransportLukotPulang);
                const ext = fileTransportLukotPulang.name
                    .split(".")
                    .pop()
                    .toLowerCase();

                if (ext === "pdf") {
                    previewFileTransportLukotPulang = `<embed src="${fileURL}" type="application/pdf" width="100%" height="500px">`;
                } else {
                    previewFileTransportLukotPulang = `<a href="${fileURL}" target="_blank">Lihat File</a>`;
                }
            } else {
                // Cek file dari database jika ada
                const dbPath = document.getElementById(
                    "file_trans_lukot_pulang_path"
                )?.value;
                if (dbPath) {
                    const fileURL = `${window.location.origin}/${dbPath}`;
                    const ext = dbPath.split(".").pop().toLowerCase();

                    if (ext === "pdf") {
                        previewFileTransportLukotPulang = `<embed src="${fileURL}" type="application/pdf" width="100%" height="500px">`;
                    } else {
                        previewFileTransportLukotPulang = `<a href="${fileURL}" target="_blank">Lihat File</a>`;
                    }
                }
            }

            const fileLaporanPerjadinInput = document.getElementById(
                "file_laporan_perjadin"
            );
            const fileLaporanPerjadin = fileLaporanPerjadinInput?.files[0];
            let previewFileLaporanPerjadin = "<p>Tidak ada file</p>";

            if (fileLaporanPerjadin) {
                const fileURL = URL.createObjectURL(fileLaporanPerjadin);
                const ext = fileLaporanPerjadin.name
                    .split(".")
                    .pop()
                    .toLowerCase();

                if (ext === "pdf") {
                    previewFileLaporanPerjadin = `<embed src="${fileURL}" type="application/pdf" width="100%" height="500px">`;
                } else {
                    previewFileLaporanPerjadin = `<a href="${fileURL}" target="_blank">Lihat File</a>`;
                }
            } else {
                // Cek file dari database jika ada
                const dbPath = document.getElementById(
                    "laporan_perjadin_path"
                )?.value;
                if (dbPath) {
                    const fileURL = `${window.location.origin}/${dbPath}`;
                    const ext = dbPath.split(".").pop().toLowerCase();

                    if (ext === "pdf") {
                        previewFileLaporanPerjadin = `<embed src="${fileURL}" type="application/pdf" width="100%" height="500px">`;
                    } else {
                        previewFileLaporanPerjadin = `<a href="${fileURL}" target="_blank">Lihat File</a>`;
                    }
                }
            }

            const fileFPPInput = document.getElementById("file_fpp");
            const fileFPP = fileFPPInput?.files[0];
            let previewFileFPP = "<p>Tidak ada file</p>";

            if (fileFPP) {
                const fileURL = URL.createObjectURL(fileFPP);
                const ext = fileFPP.name.split(".").pop().toLowerCase();

                if (ext === "pdf") {
                    previewFileFPP = `<embed src="${fileURL}" type="application/pdf" width="100%" height="500px">`;
                } else {
                    previewFileFPP = `<a href="${fileURL}" target="_blank">Lihat File</a>`;
                }
            } else {
                // Cek file dari database jika ada
                const dbPath = document.getElementById("fpp_path")?.value;
                if (dbPath) {
                    const fileURL = `${window.location.origin}/${dbPath}`;
                    const ext = dbPath.split(".").pop().toLowerCase();

                    if (ext === "pdf") {
                        previewFileFPP = `<embed src="${fileURL}" type="application/pdf" width="100%" height="500px">`;
                    } else {
                        previewFileFPP = `<a href="${fileURL}" target="_blank">Lihat File</a>`;
                    }
                }
            }

            const fileKAKInput = document.getElementById("file_kak");
            const fileKAK = fileKAKInput?.files[0];
            let previewFileKAK = "<p>Tidak ada file</p>";

            if (fileKAK) {
                const fileURL = URL.createObjectURL(fileKAK);
                const ext = fileKAK.name.split(".").pop().toLowerCase();

                if (ext === "pdf") {
                    previewFileKAK = `<embed src="${fileURL}" type="application/pdf" width="100%" height="500px">`;
                } else {
                    previewFileKAK = `<a href="${fileURL}" target="_blank">Lihat File</a>`;
                }
            } else {
                // Cek file dari database jika ada
                const dbPath = document.getElementById("kak_path")?.value;
                if (dbPath) {
                    const fileURL = `${window.location.origin}/${dbPath}`;
                    const ext = dbPath.split(".").pop().toLowerCase();

                    if (ext === "pdf") {
                        previewFileKAK = `<embed src="${fileURL}" type="application/pdf" width="100%" height="500px">`;
                    } else {
                        previewFileKAK = `<a href="${fileURL}" target="_blank">Lihat File</a>`;
                    }
                }
            }

            const fileSuratPemanggilanInput =
                document.getElementById("file_pemanggilan");
            const fileSuratPemanggilan = fileSuratPemanggilanInput?.files[0];
            let previewFileSuratPemanggilan = "<p>Tidak ada file</p>";

            if (fileSuratPemanggilan) {
                const fileURL = URL.createObjectURL(fileSuratPemanggilan);
                const ext = fileSuratPemanggilan.name
                    .split(".")
                    .pop()
                    .toLowerCase();

                if (ext === "pdf") {
                    previewFileSuratPemanggilan = `<embed src="${fileURL}" type="application/pdf" width="100%" height="500px">`;
                } else {
                    previewFileSuratPemanggilan = `<a href="${fileURL}" target="_blank">Lihat File</a>`;
                }
            } else {
                // Cek file dari database jika ada
                const dbPath = document.getElementById(
                    "surat_pemanggilan_path"
                )?.value;
                if (dbPath) {
                    const fileURL = `${window.location.origin}/${dbPath}`;
                    const ext = dbPath.split(".").pop().toLowerCase();

                    if (ext === "pdf") {
                        previewFileSuratPemanggilan = `<embed src="${fileURL}" type="application/pdf" width="100%" height="500px">`;
                    } else {
                        previewFileSuratPemanggilan = `<a href="${fileURL}" target="_blank">Lihat File</a>`;
                    }
                }
            }

            // Surat Tugas
            const surat = `
        <ul>
            <li><strong>Nomor Surat Tugas:</strong> B-${getValue(
                "input1"
            )}/${getValue("input2")}/${getValue("input3")}/${getValue(
                "input4"
            )}</li>
            <li><strong>Tanggal Penugasan:</strong> ${formatTanggal(
                getValue("tgl_mulai_st")
            )} s/d ${formatTanggal(getValue("tgl_selesai_st"))}</li>
            <li><strong>Dokumen Surat Tugas</strong></li>
            <li>${previewFile}</li>
        </ul>        
    `;

            // SPD
            const spd = `
        <ul>
            <li><strong>Nomor SPD:</strong> ${getValue(
                "input_spd1"
            )}/${getValue("input_spd2")}/${getValue("input_spd3")}/${getValue(
                "input_spd4"
            )}/${getValue("input_spd5")}</li>
            <li><strong>Tanggal SPD:</strong> ${formatTanggal(
                getValue("tgl_spd_create")
            )}</li>
            <li><strong>Dokumen SPD</strong></li>
            <li>${previewFileSPD}</li>
        </ul>
    `;

            const hotelChecked = document.getElementById("hotel_ya")?.checked;
            let penginapan = `
                <ul>
                    <li><strong>Apakah ada invoice penginapan? </strong> ${hotelChecked ? "Ya" : "Tidak"
                }</li>
            `;

            if (hotelChecked) {
                penginapan += `
                    <li><strong>Total Biaya Penginapan:</strong> ${formatRibuan(
                    getValue("nominal_hotel_create")
                )}</li>
                    <li><strong>Dokumen Invoice Penginapan:</strong></li>
                    <li>${previewFilePenginapan}</li>
                `;
            }

            penginapan += `</ul>`;

            // Transportasi

            const dalkotChecked = document.getElementById("dalkot")?.checked;
            const lukotChecked = document.getElementById("lukot")?.checked;
            const pribadiChecked = document.getElementById("pribadi")?.checked;
            let transport = `
                <h3 class="ml-4">Perjalanan Dinas ${dalkotChecked ? "Dalam Kota" : "Luar Kota"
                }</h3>
                <div class="row">                    
            `;

            if (dalkotChecked) {
                transport += `
                    <div class="col-6">
                        <ul>
                            <li><strong>Jumlah Hari Translok:</strong> ${getValue(
                    "jumlah_hari_transport"
                )}</li>
                            <li><strong>Dokumen bukti transportasi lokal:</strong></li>
                            <li>${previewFileTransportLokal}</li>
                        </ul>
                    </div>                                   
                `;
            } else if (lukotChecked) {
                transport += `
                        <h5 class="ml-4">${pribadiChecked
                        ? "Kendaraan Pribadi"
                        : "Kendaraan Umum"
                    }</h5>
                        <div class="col-12">
                    `;
                if (pribadiChecked) {
                    transport += `
                        <div class="row ml-3">
                            <strong class="ml-3">Jarak dari rumah ke tempat diklat: </strong> ${getValue(
                        "km_berangkat_create"
                    )} km
                        </div>
                    `;
                }
                transport += `
                    </div>
                        <div class="col-6">
                            <ul>
                                <li><strong>Tanggal Berangkat:</strong> ${formatTanggal(
                    getValue("tgl_trans_lukot_berangkat_create")
                )}</li>
                                <li><strong>Total Biaya Transportasi Berangkat:</strong> ${formatRibuan(
                    getValue(
                        "nominal_trans_lukot_berangkat_create"
                    )
                )}</li>
                                <li><strong>Dokumen bukti transportasi berangkat:</strong></li>
                                <li>${previewFileTransportLukotBerangkat}</li>
                            </ul>
                        </div>
                        <div class="col-6">
                            <ul>
                                <li><strong>Tanggal Pulang:</strong> ${formatTanggal(
                    getValue("tgl_trans_lukot_pulang_create")
                )}</li>
                                <li><strong>Total Biaya Transportasi Pulang:</strong> ${formatRibuan(
                    getValue(
                        "nominal_trans_lukot_pulang_create"
                    )
                )}</li>
                                <li><strong>Dokumen bukti transportasi pulang:</strong></li>
                                <li>${previewFileTransportLukotPulang}</li>
                            </ul>
                        </div>                                        
                `;
            }

            transport += `</div>`;

            // Rincian Uang Harian
            const uang = `
                <ul>
                    <li><strong>Jumlah Hari Diklat:</strong> ${getValue(
                "jumlah_hari"
            )}</li>
                    <li><strong>Uang Harian:</strong> ${formatRibuan(
                getValue("uh_diklat_create")
            )}</li>
                    <li><strong>Total Uang Harian:</strong> ${getValue(
                "total_uh"
            )}</li>
                    <li><strong>Uang Harian H-1:</strong> ${formatRibuan(
                getValue("uh_h_1_create")
            )}</li>
                    <li><strong>Uang Harian H+1:</strong> ${formatRibuan(
                getValue("uh_h1_create")
            )}</li>                    
                </ul>
            `;

            // Rekap
            const jumlahHari = Number(
                getValue("jumlah_hari").replace(/\D/g, "") || 0
            );
            const jumlahHariTransport = Number(
                getValue("jumlah_hari_transport").replace(/\D/g, "") || 0
            );
            const uangHarian = Number(
                getValue("uh_diklat_create").replace(/\D/g, "") || 0
            );
            const totalUang = jumlahHari * uangHarian;

            let nominalHotel = parseNominal("nominal_hotel_create");
            let uhH1 = parseNominal("uh_h_1_create");
            let uhHPlus1 = parseNominal("uh_h1_create");
            let total = 0;
            let translok = 0;
            if (dalkotChecked) {
                translok = 170000 * jumlahHariTransport
                total =
                    nominalHotel +
                    translok +
                    uhH1 +
                    uhHPlus1 +
                    totalUang;
            } else {
                let transportBerangkat = parseNominal("nominal_trans_lukot_berangkat_create");
                let transportPulang = parseNominal("nominal_trans_lukot_pulang_create");
                total =
                    nominalHotel +
                    transportBerangkat +
                    transportPulang +
                    uhH1 +
                    uhHPlus1 +
                    totalUang;
            }
            console.log("Total Biaya:", total);
            const selectRek = document.getElementById("no_rek");
            const selectedText =
                selectRek.options[selectRek.selectedIndex]?.text || "-";

            let rekap = `
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th class="text-center">Jenis</th>
                            <th class="text-center">Nominal</th>
                        </tr
                    </thead>
                    <tbody>
                        <tr>
                            <td>Hotel</td>
                            <td>${formatRibuan(
                getValue("nominal_hotel_create")
            )}</td>
                        </tr>
                        `;
            if (dalkotChecked) {
                rekap += `
                            <tr>
                                <td>Transportasi Lokal</td>
                                <td>${translok.toLocaleString("id-ID")}</td>
                            </tr>                            
                            `;
            } else if (lukotChecked) {
                rekap += `
                            <tr>
                                <td>Transportasi Berangkat</td>
                                <td>${formatRibuan(
                    getValue(
                        "nominal_trans_lukot_berangkat_create"
                    )
                )}</td>
                            </tr>
                            <tr>
                                <td>Transportasi Pulang</td>
                                <td>${formatRibuan(
                    getValue(
                        "nominal_trans_lukot_pulang_create"
                    )
                )}</td>
                            </tr>
                            `;
            }
            rekap += `                        
                        <tr>
                            <td>Uang Harian (H-1)</td>
                            <td>${formatRibuan(getValue("uh_h_1_create"))}</td>
                        </tr>
                        <tr>
                            <td>Uang Harian (H+1)</td>
                            <td>${formatRibuan(getValue("uh_h1_create"))}</td>
                        </tr>
                        <tr>
                            <td>Uang Diklat</td>
                            <td>${totalUang.toLocaleString("id-ID")}</td>
                        </tr>
                        <tr>
                            <td>Total</td>
                            <td>${total.toLocaleString("id-ID")}</td>
                        </tr>
                    </tbody>                    
                </table>
                <p><strong>Rekening:</strong> ${selectedText}</p>
            `;

            const dokumen = `
                <ul>                   
                    <li><strong>Dokumen Laporan Perjadin:</strong></li>
                    <li>${previewFileLaporanPerjadin}</li>
                    <li><strong>Dokumen FPP:</strong></li>
                    <li>${previewFileFPP}</li>
                    <li><strong>Dokumen KAK:</strong></li>
                    <li>${previewFileKAK}</li>
                    <li><strong>Dokumen Surat Pemanggilan:</strong></li>
                    <li>${previewFileSuratPemanggilan}</li>
                </ul>
            `;

            // Masukkan ke dalam tab masing-masing
            document.getElementById("preview-surat").innerHTML = surat;
            document.getElementById("preview-spd").innerHTML = spd;
            document.getElementById("preview-penginapan").innerHTML =
                penginapan;
            document.getElementById("preview-transport").innerHTML = transport;
            document.getElementById("preview-uang-harian").innerHTML = uang;
            document.getElementById("preview-rekap").innerHTML = rekap;
            document.getElementById("preview-dokumen").innerHTML = dokumen;

            $("#modalPreview").modal("show");
        });
    });

    const simpanBtn = document.getElementById("simpanFormBtn");
    const updateBtn = document.getElementById("updateFormBtn");
    const kirimBtn = document.getElementById("confirmSubmitBtn");

    if (simpanBtn) {
        document
            .getElementById("simpanFormBtn")
            .addEventListener("click", async function () {
                const form = document.getElementById("spj-diklat");
                const formData = new FormData(form);

                try {
                    const response = await fetch(form.action, {
                        method: "POST",
                        headers: {
                            "X-CSRF-TOKEN": document.querySelector(
                                'meta[name="csrf-token"]'
                            ).content,
                        },
                        body: formData,
                    });

                    if (response.status === 422) {
                        const data = await response.json();
                        let message = Object.values(data.errors)
                            .map((err) => `• ${err[0]}`)
                            .join("<br>");

                        Swal.fire({
                            icon: "warning",
                            title: "Form Belum Lengkap!",
                            html: message,
                        });
                    } else if (response.ok) {
                        const data = await response.json(); // Ambil ID dari response
                        const newId = data.id;

                        Swal.fire({
                            icon: "success",
                            title: "Berhasil!",
                            text: "Data berhasil disimpan.",
                            timer: 1000,
                            showConfirmButton: false,
                        }).then(() => {
                            window.location.href = `/pegawai/spj-diklat/edit/${newId}`;
                        });
                    } else {
                        Swal.fire({
                            icon: "error",
                            title: "Gagal!",
                            text: "Terjadi kesalahan server.",
                        });
                    }
                } catch (error) {
                    Swal.fire({
                        icon: "error",
                        title: "Gagal",
                        text: "Tidak dapat mengirim data. Coba lagi.",
                    });
                }
            });
    }

    if (updateBtn) {
        document
            .getElementById("updateFormBtn")
            .addEventListener("click", async function () {
                const form = document.getElementById("spj-diklat-update");
                const formData = new FormData(form);

                try {
                    const response = await fetch(form.action, {
                        method: "POST",
                        headers: {
                            "X-CSRF-TOKEN": document.querySelector(
                                'meta[name="csrf-token"]'
                            ).content,
                        },
                        body: formData,
                    });

                    if (response.status === 422) {
                        const data = await response.json();
                        let message = Object.values(data.errors)
                            .map((err) => `• ${err[0]}`)
                            .join("<br>");

                        Swal.fire({
                            icon: "warning",
                            title: "Form Belum Lengkap!",
                            html: message,
                        });
                    } else if (response.ok) {
                        Swal.fire({
                            icon: "success",
                            title: "Berhasil!",
                            text: "Data berhasil disimpan.",
                            timer: 1000,
                            showConfirmButton: false,
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: "error",
                            title: "Gagal!",
                            text: "Terjadi kesalahan server.",
                        });
                    }
                } catch (error) {
                    Swal.fire({
                        icon: "error",
                        title: "Gagal",
                        text: "Tidak dapat mengirim data. Coba lagi.",
                    });
                }
            });
    }

    const selectRek = document.getElementById("no_rek");
    const formTambah = document.getElementById("formTambahRekening");
    const errorBox = document.getElementById("errorTambahRekening");

    // 1. Tampilkan modal jika pilih "+ Tambah Rekening"
    if (selectRek) {
        selectRek.addEventListener("change", function () {
            if (this.value === "tambah") {
                $("#modalTambahRekening").modal("show");
                this.value = ""; // reset pilihan agar tidak terkunci
            }
        });
    }

    // 2. Submit form tambah rekening via AJAX
    if (formTambah) {
        formTambah.addEventListener("submit", async function (e) {
            e.preventDefault();
            errorBox.innerHTML = "";

            const formData = new FormData(formTambah);

            try {
                const response = await fetch("/pegawai/rekening/store", {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": document.querySelector(
                            'meta[name="csrf-token"]'
                        ).content,
                    },
                    body: formData,
                });

                if (response.status === 422) {
                    const data = await response.json();
                    let message = Object.values(data.errors)
                        .map((err) => err[0])
                        .join("<br>");
                    errorBox.innerHTML = message;
                } else if (response.ok) {
                    const data = await response.json();
                    $("#modalTambahRekening").modal("hide");
                    formTambah.reset();

                    // Refresh select dan pilih rekening baru
                    await refreshRekeningOptions(data.id_rekening);
                } else {
                    errorBox.innerHTML = "Terjadi kesalahan saat menyimpan.";
                }
            } catch (error) {
                errorBox.innerHTML = "Gagal terhubung ke server.";
            }
        });
    }

    // 3. Fungsi refresh daftar rekening
    async function refreshRekeningOptions(selectedId = null) {
        try {
            const response = await fetch("/pegawai/rekening/list");
            const data = await response.json();

            selectRek.innerHTML = ""; // kosongkan dulu

            // Tambahkan placeholder
            const placeholder = document.createElement("option");
            placeholder.value = "";
            placeholder.disabled = true;
            placeholder.textContent = "Pilih Rekening";
            selectRek.appendChild(placeholder);

            // Tambah semua rekening
            data.forEach((r) => {
                const option = document.createElement("option");
                option.value = r.id_rekening;
                option.textContent = `${r.nama_bank} - ${r.no_rekening}`;
                if (selectedId && selectedId == r.id_rekening)
                    option.selected = true;
                selectRek.appendChild(option);
            });

            // Tambah tombol "+ Tambah Rekening Baru"
            const optTambah = document.createElement("option");
            optTambah.value = "tambah";
            optTambah.textContent = "+ Tambah Rekening Baru";
            selectRek.appendChild(optTambah);
        } catch (err) {
            console.error("Gagal refresh rekening:", err);
        }
    }

    document
        .getElementById("kirimFormBtn")
        .addEventListener("click", function () {
            const modal = $("#modalKirimSpj");
            const modalInput = document.getElementById("konfirmasiSetujuInput");
            const btnKirim = document.getElementById("btnModalKirim");
            const formUpdate = document.getElementById("spj-diklat-update");
            const idSpj = document.getElementById("id_spj")?.value;

            // Reset field dan disable tombol saat modal dibuka
            modalInput.value = "";
            btnKirim.setAttribute("disabled", true);
            modal.modal("show");

            // Aktifkan tombol hanya jika input === 'setuju'
            modalInput.addEventListener("input", function () {
                if (this.value.trim().toLowerCase() === "setuju") {
                    btnKirim.removeAttribute("disabled");
                } else {
                    btnKirim.setAttribute("disabled", true);
                }
            });
        });

    // ✅ Pastikan hanya 1 kali binding click ke btnModalKirim
    let sudahTerpasang = false;

    // btnKirim.addEventListener("DOMContentLoaded", () => {
    const btnKirim = document.getElementById("btnModalKirim");
    const formUpdate = document.getElementById("spj-diklat-update");

    if (!btnKirim || !formUpdate) return;

    if (!sudahTerpasang) {
        sudahTerpasang = true;

        btnKirim.addEventListener("click", function () {
            const idSpj = document.getElementById("id_spj")?.value;
            if (!idSpj) return;

            // Hapus _method=PUT jika ada
            formUpdate.querySelector('input[name="_method"]')?.remove();

            const formDataKirim = new FormData(formUpdate);

            fetch(`/pegawai/spj-diklat/kirim/${idSpj}`, {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": document.querySelector(
                        'meta[name="csrf-token"]'
                    ).content,
                },
                body: formDataKirim,
            })
                .then(async (res) => {
                    if (res.status === 422) {
                        const data = await res.json();
                        tampilkanErrorValidasi(data.errors);

                        // Kembalikan method PUT
                        formUpdate.appendChild(
                            Object.assign(document.createElement("input"), {
                                type: "hidden",
                                name: "_method",
                                value: "PUT",
                            })
                        );
                    } else if (res.ok) {
                        Swal.fire({
                            icon: "success",
                            title: "Berhasil",
                            text: "SPJ berhasil dikirim!",
                            timer: 2000,
                            showConfirmButton: false,
                        }).then(() => {
                            window.location.href = "/pegawai/spj-diklat";
                        });
                    } else {
                        Swal.fire({
                            icon: "error",
                            title: "Gagal",
                            text: "Terjadi kesalahan saat mengirim data.",
                        });

                        // Kembalikan method PUT
                        formUpdate.appendChild(
                            Object.assign(document.createElement("input"), {
                                type: "hidden",
                                name: "_method",
                                value: "PUT",
                            })
                        );
                    }
                })
                .catch(() => {
                    Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: "Tidak dapat terhubung ke server.",
                    });

                    // Kembalikan method PUT
                    formUpdate.appendChild(
                        Object.assign(document.createElement("input"), {
                            type: "hidden",
                            name: "_method",
                            value: "PUT",
                        })
                    );
                });
        });
    }
    function tampilkanErrorValidasi(errors) {
        // Reset semua error dulu
        document
            .querySelectorAll('[id^="error-"]')
            .forEach((el) => (el.innerHTML = ""));

        let allMessages = [];

        for (let field in errors) {
            const pesan = errors[field][0];
            allMessages.push(`• ${pesan}`);

            const target = document.getElementById(`error-${field}`);
            if (target) {
                target.innerHTML = pesan;
            }
        }

        if (allMessages.length > 0) {
            Swal.fire({
                icon: "warning",
                title: "Isian Tidak Lengkap",
                html: allMessages.join("<br>"),
            });
        }
    }
});