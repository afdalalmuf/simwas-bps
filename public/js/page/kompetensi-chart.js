var opt = {
    legend: {
        display: false
    },
    layout: {
        padding: {
            top: 20
        }
    },
    tooltips: {
        enabled: false
    },
    scales: {
        yAxes: [{
        gridLines: {
            display: false,
            drawBorder: false,
        },
        ticks: {
            stepSize: 150
        }
        }],
        xAxes: [{
        gridLines: {
            color: '#fbfbfb',
            lineWidth: 2
        }
        }]
    },
    animation: {
        duration: 500,
        onComplete: function () {
            //menampilkan nilai
            var ctx = this.chart.ctx;
            ctx.font = Chart.helpers.fontString(14, 'normal', Chart.defaults.global.defaultFontFamily);
            ctx.fillStyle = this.chart.config.options.defaultFontColor;
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';
            this.data.datasets.forEach(function (dataset) {
                for (var i = 0; i < dataset.data.length; i++) {
                    var model = dataset._meta[Object.keys(dataset._meta)[0]].data[i]._model;
                    ctx.fillText(dataset.data[i], model.x, model.y - 10);
                }
            });
        }
    },
    hover: {
        animationDuration: 0
    }
};

var diklat_chart = document.getElementById("diklatChart").getContext('2d');

var myChart = new Chart(diklat_chart, {
    type: 'bar',
    data: {
        labels: years,
        datasets: [
            {
                data: [],
                borderWidth: 3,
                borderColor: 'rgba(0, 105, 217, 1)',
                backgroundColor: 'rgba(0, 105, 217, 0.4)',
                // pointBorderColor: '#6777ef',
                // pointRadius: 4
            }
        ]
    },
    options: opt
});

var jp_chart = document.getElementById("JPChart").getContext('2d');

var myChart2 = new Chart(jp_chart, {
    type: 'bar',
    data: {
        labels: years,
        datasets: [
            {
                data: [],
                borderWidth: 3,
                borderColor: 'rgb(217, 83, 0)',
                backgroundColor: 'rgba(217, 83, 0, 0.4)',
                // pointBorderColor: '#6777ef',
                // pointRadius: 4
            }
        ]
    },
    options: opt
});

$('#filterPegawai').on("change", function () {
    //jumlah diklat
    diklat = diklat_count[$(this).val()];
    let max = 0;
    if (diklat) { //jika data pegawai ada
        myChart.data.labels = Object.keys(diklat);
        myChart.data.datasets[0].data = Object.values(diklat);
        max = Math.max(...Object.values(diklat)) + 1;
    } else {
        myChart.data.labels = years;
        myChart.data.datasets[0].data = [];
    }
    myChart.options.scales.yAxes[0].ticks.max = max;
    myChart.update();

    //total jp
    jp = jp_count[$(this).val()];
    max = 0;
    if (jp) { //jika data pegawai ada
        myChart2.data.labels = Object.keys(jp);
        myChart2.data.datasets[0].data = Object.values(jp);
        max = Math.max(...Object.values(jp)) + 30;
    } else {
        myChart2.data.labels = years;
        myChart2.data.datasets[0].data = [];
    }
    myChart2.options.scales.yAxes[0].ticks.max = max;
    myChart2.update();
});

function loadChart(tahun) {
    $.get('analis-sdm/skp/chart-data', { tahun }, function (res) {        
        const labels = res.map(r => r.wilayah);
        const belum = res.map(r => r.belum);
        const diperiksa = res.map(r => r.diperiksa);
        const sudah_kirim = res.map(r => r.sudah_kirim);
        const ditolak = res.map(r => r.ditolak);

        const ctx = document.getElementById('skpChart').getContext('2d');

        if (window.skpChart instanceof Chart) {
            window.skpChart.destroy();
        }
        
        window.skpChart = new Chart(ctx, {
            type: 'horizontalBar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Belum Kirim',
                        data: belum,
                        backgroundColor: '#ffc107'
                    },
                    {
                        label: 'Diperiksa',
                        data: diperiksa,
                        backgroundColor: '#007bff'
                    },
                    {
                        label: 'Sudah Kirim',
                        data: sudah_kirim,
                        backgroundColor: '#28a745'
                    },
                    {
                        label: 'Ditolak',
                        data: ditolak,
                        backgroundColor: '#dc3545'
                    },
                ]
            },
            options: {
                scales: {
                    xAxes: [{
                        stacked: true,
                        ticks: {
                            beginAtZero: true,
                            max: 100
                        }
                    }],
                    yAxes: [{
                        stacked: true
                    }]
                },
                tooltips: {
                    mode: 'index',
                    intersect: false,
                    callbacks: {
                        label: function (tooltipItem, data) {
                            const label = data.datasets[tooltipItem.datasetIndex].label;
                            const value = tooltipItem.xLabel.toFixed(2);
                            return `${label}: ${value}%`;
                        }
                    }
                },
                legend: {
                    position: 'bottom'
                }
            }
        });
    });
}

$('#tahunFilter').on('change', function () {
    loadChart(this.value);
});

// load chart on first load
loadChart($('#tahunFilter').val());

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
}).api();

//update ukuran tabel saat ukuran sidebar berubah
$('.nav-link').on("click", function() {
    setTimeout(function() {
        datatable.columns.adjust();
    }, 500);
});
