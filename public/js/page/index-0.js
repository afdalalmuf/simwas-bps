"use strict";
function loadChart(tahun) {
  $.get('inspektur/skp/chart-data', { tahun }, function (res) {        
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

function loadChartWil1(tahun) {
  $.get('inspektur/skp/chart-irwil1', { tahun }, function (res) {        
      const labels = res.map(r => r.bulan);
      const belum_wil1 = res.map(r => r.belum);
      const diperiksa_wil1 = res.map(r => r.diperiksa);
      const sudah_kirim_wil1 = res.map(r => r.sudah_kirim);
      const ditolak_wil1 = res.map(r => r.ditolak);

      const ctx = document.getElementById('skpIrwil1Chart').getContext('2d');

      if (window.skpIrwil1Chart instanceof Chart) {
          window.skpIrwil1Chart.destroy();
      }
      
      window.skpIrwil1Chart = new Chart(ctx, {
          type: 'horizontalBar',
          data: {
              labels: labels,
              datasets: [
                  {
                      label: 'Belum Kirim',
                      data: belum_wil1,
                      backgroundColor: '#ffc107'
                  },
                  {
                      label: 'Diperiksa',
                      data: diperiksa_wil1,
                      backgroundColor: '#007bff'
                  },
                  {
                      label: 'Sudah Kirim',
                      data: sudah_kirim_wil1,
                      backgroundColor: '#28a745'
                  },
                  {
                      label: 'Ditolak',
                      data: ditolak_wil1,
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

function loadChartWil2(tahun) {
  $.get('inspektur/skp/chart-irwil2', { tahun }, function (res) {        
      const labels = res.map(r => r.bulan);
      const belum_wil2 = res.map(r => r.belum);
      const diperiksa_wil2 = res.map(r => r.diperiksa);
      const sudah_kirim_wil2 = res.map(r => r.sudah_kirim);
      const ditolak_wil2 = res.map(r => r.ditolak);

      const ctx = document.getElementById('skpIrwil2Chart').getContext('2d');

      if (window.skpIrwil2Chart instanceof Chart) {
          window.skpIrwil2Chart.destroy();
      }
      
      window.skpIrwil2Chart = new Chart(ctx, {
          type: 'horizontalBar',
          data: {
              labels: labels,
              datasets: [
                  {
                      label: 'Belum Kirim',
                      data: belum_wil2,
                      backgroundColor: '#ffc107'
                  },
                  {
                      label: 'Diperiksa',
                      data: diperiksa_wil2,
                      backgroundColor: '#007bff'
                  },
                  {
                      label: 'Sudah Kirim',
                      data: sudah_kirim_wil2,
                      backgroundColor: '#28a745'
                  },
                  {
                      label: 'Ditolak',
                      data: ditolak_wil2,
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

function loadChartWil3(tahun) {
  $.get('inspektur/skp/chart-irwil3', { tahun }, function (res) {        
      const labels = res.map(r => r.bulan);
      const belum_wil3 = res.map(r => r.belum);
      const diperiksa_wil3 = res.map(r => r.diperiksa);
      const sudah_kirim_wil3 = res.map(r => r.sudah_kirim);
      const ditolak_wil3 = res.map(r => r.ditolak);

      const ctx = document.getElementById('skpIrwil3Chart').getContext('2d');

      if (window.skpIrwil3Chart instanceof Chart) {
          window.skpIrwil3Chart.destroy();
      }
      
      window.skpIrwil3Chart = new Chart(ctx, {
          type: 'horizontalBar',
          data: {
              labels: labels,
              datasets: [
                  {
                      label: 'Belum Kirim',
                      data: belum_wil3,
                      backgroundColor: '#ffc107'
                  },
                  {
                      label: 'Diperiksa',
                      data: diperiksa_wil3,
                      backgroundColor: '#007bff'
                  },
                  {
                      label: 'Sudah Kirim',
                      data: sudah_kirim_wil3,
                      backgroundColor: '#28a745'
                  },
                  {
                      label: 'Ditolak',
                      data: ditolak_wil3,
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

function loadChartBuntama(tahun) {
  $.get('inspektur/skp/chart-buntama', { tahun }, function (res) {        
      const labels = res.map(r => r.bulan);
      const belum_buntama = res.map(r => r.belum);
      const diperiksa_buntama = res.map(r => r.diperiksa);
      const sudah_kirim_buntama = res.map(r => r.sudah_kirim);
      const ditolak_buntama = res.map(r => r.ditolak);

      const ctx = document.getElementById('skpBuntamaChart').getContext('2d');

      if (window.skpBuntamaChart instanceof Chart) {
          window.skpBuntamaChart.destroy();
      }
      
      window.skpBuntamaChart = new Chart(ctx, {
          type: 'horizontalBar',
          data: {
              labels: labels,
              datasets: [
                  {
                      label: 'Belum Kirim',
                      data: belum_buntama,
                      backgroundColor: '#ffc107'
                  },
                  {
                      label: 'Diperiksa',
                      data: diperiksa_buntama,
                      backgroundColor: '#007bff'
                  },
                  {
                      label: 'Sudah Kirim',
                      data: sudah_kirim_buntama,
                      backgroundColor: '#28a745'
                  },
                  {
                      label: 'Ditolak',
                      data: ditolak_buntama,
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
  loadChartWil1(this.value);
  loadChartWil2(this.value);
  loadChartWil3(this.value);
  loadChartBuntama(this.value);
});

// load chart on first load
loadChart($('#tahunFilter').val());
loadChartWil1($('#tahunFilter').val());
loadChartWil2($('#tahunFilter').val());
loadChartWil3($('#tahunFilter').val());
loadChartBuntama($('#tahunFilter').val());
