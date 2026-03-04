<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Language" content="id">
    <meta name="robots" content="noindex,nofollow">
    <meta name="googlebot" content="noindex,nofollow"> 
    <meta name="bingbot" content="noindex,nofollow">
    <link rel="prefetch" href="https://simwas.web.bps.go.id/auth-login">
    <link href="https://ettd.bps.go.id/additional/assets/images/logobps.png" rel="icon" sizes="32x32" type="image/png">
    <title>Monitoring Laporan Anda</title>
    <link rel="stylesheet" href="{{ asset('css/feedback.css') }}">
    <!-- Alpine CDN -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="app.js"></script>
</head>
<body class="md:px-16 py-10">
    
    <!-- link kembali ke halaman sebelumnya atau login ke SIMWAS  -->
    <div class="ml-5 flex gap-x-56 mb-10">
      <a onclick="history.back()" class="cursor-pointer inline-flex no-underline items-center text-gray-600 hover:text-gray-400"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24">
        <path fill="currentColor" d="m4.296 12l8.492-8.727a.75.75 0 1 0-1.075-1.046l-9 9.25a.75.75 0 0 0 0 1.046l9 9.25a.75.75 0 1 0 1.075-1.046z"/>
      </svg>
        <p class="text-sm">Kembali</p>
      </a>
      <a class="inline-flex no-underline text-center items-center text-gray-600 hover:text-gray-400" href="https://simwas.web.bps.go.id/auth-login">
        <p class="text-sm mr-0.5">Log In SIMWAS</p><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24">
          <path fill="currentColor" d="m19.704 12l-8.491-8.727a.75.75 0 1 1 1.075-1.046l9 9.25a.75.75 0 0 1 0 1.046l-9 9.25a.75.75 0 1 1-1.075-1.046z" />
        </svg>
        </svg>
      </a>
    </div>

    <div class="ml-5">
        <h1 class="text-gray-800 text-5xl mb-0">Laman Monitoring</h1>
        <!-- flex untuk statistics di sisi paling kanan -->
        <div class="flex items-end justify-start">
          <div>
            <p class="text-gray-600 text-[1.06em] py-5 w-1/2">Melalui laman ini, Anda dapat memperoleh informasi mengenai status serta tindak lanjut terhadap laporan dan saran yang telah disampaikan. <br> Laporan yang baru saja di-submit akan terlihat pada tabel di bawah ini. Anda dapat mengunjungi laman ini di lain waktu untuk mendapatkan update atas laporan atau saran Anda. Terima kasih telah berkontribusi.</p>
            <button onclick="window.location.reload()" id="tombolRefresh" class="bg-white border border-gray-300 rounded-md py-1.5 px-5 focus:ring-1 focus:ring-offset-4 ring-gray-400 focus:bg-gray-100 flex items-center justify-center gap-x-1 text-sm text-gray-700">Refresh  <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 512 512"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-miterlimit="10" stroke-width="32" d="M320 146s24.36-12-64-12a160 160 0 1 0 160 160" /><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="32" d="m256 58l80 80l-80 80" /></svg>
            </button>
          </div>          
          <!-- stats -->
          <!-- <div class="text-sm gap-x-2 flex mr-5 border border-gray-300 py-2 px-7 rounded-lg">
              <p>Masuk:<span>2</span></p>
              <p> Selesai:<span>2</span></p>
          </div> -->          
        </div>
    </div>
    
    <!--Monitoring error-->
    <div x-data="reportTable()" x-init="fetchData()" class="mx-4 my-12">
      <h1 class="text-3xl mb-4">Monitoring Error</h1>
  
      <!-- Table-->
      <div class="overflow-x-auto bg-white rounded-lg shadow-md">
        <table class="min-w-full text-sm text-left text-gray-700 border-collapse">
          <thead class="bg-red-50 text-gray-900 uppercase text-xs">
            <tr>
              <th class="px-4 py-4">Timestamp</th>
              <th class="px-4 py-4">ID</th>
              <th class="px-4 py-4">Tempat Error</th>
              <th class="px-4 py-4">Penjelasan</th>
              <th class="px-4 py-4">Respon Laporan</th>
            </tr>
          </thead>
          <tbody>
            <template x-for="(row, index) in data" :key="index">
              <tr class="border-b hover:bg-gray-50">
                <td class="px-4 py-2" x-text="row.timestamp"></td>
                <td class="px-4 py-2" x-text="row.id"></td>
                <td class="px-4 py-2" x-text="row['tempat-error']"></td>
                <td class="px-4 py-2" x-text="row.penjelasan"></td>
                <td class="px-4 py-2" x-text="row.respon_laporan"></td>
              </tr>
            </template>
          </tbody>
        </table>
      </div>
    </div>

    <!--Monitoring saran-->
    <div x-data="saranTable()" x-init="fetchData()" class="mx-4 my-16">
      <h1 class="text-3xl mb-4">Monitoring Saran</h1>
      <div class="overflow-x-auto bg-white rounded-2xl shadow-md">
        <table class="min-w-full text-sm text-left text-gray-700 border-collapse">
          <thead class="bg-green-100 text-gray-900 uppercase text-xs">
            <tr>
              <th class="px-4 py-2">Timestamp</th>
              <th class="px-4 py-2">ID</th>
              <th class="px-4 py-2">Kategori</th>
              <th class="px-4 py-2">Saran</th>
              <th class="px-4 py-2">Tanggapan</th>
              <th class="px-4 py-2">Tanggal Tanggapan</th>
            </tr>
          </thead>
          <tbody>
            <template x-for="(row, index) in data" :key="index">
              <tr class="border-b hover:bg-gray-50">
                <td class="px-4 py-2" x-text="row.timestamp"></td>
                <td class="px-4 py-2" x-text="row.id"></td>
                <td class="px-4 py-2" x-text="row.kategori"></td>
                <td class="px-4 py-2 whitespace-pre-wrap" x-text="row.saran"></td>
                <td class="px-4 py-2 whitespace-pre-wrap" x-text="row.tanggapan"></td>
                <td class="px-4 py-2" x-text="row.tanggal_tanggapan"></td>
              </tr>
            </template>
          </tbody>
        </table>
      </div>
    </div>

  <script>
      // JS laman monitoring

function reportTable() {
    return {
      data: [],
      async fetchData() {
        try {
          let res = await fetch("https://opensheet.elk.sh/1vTiP8zwytvp8C9T3At5U1qEjmabGjeIZ0LBrdmKW9_Y/Sheet1");
          this.data = await res.json();
        } catch (error) {
          console.error("Error fetching data:", error);
        }
      }
    }
}

function saranTable() {
    return {
      data: [],
      async fetchData() {
        try {
          let res = await fetch("https://opensheet.elk.sh/1vTiP8zwytvp8C9T3At5U1qEjmabGjeIZ0LBrdmKW9_Y/Sheet2");
          this.data = await res.json();
        } catch (error) {
          console.error("Error fetching Sheet2:", error);
        }
      }
    }
}

  </script>
</body>
</html>