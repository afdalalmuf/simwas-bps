<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Language" content="id">
    <meta name="robots" content="noindex,nofollow">
    <meta name="googlebot" content="noindex,nofollow"> 
    <meta name="bingbot" content="noindex,nofollow">
    <link href="https://ettd.bps.go.id/additional/assets/images/logobps.png" rel="icon" sizes="32x32" type="image/png">
    <title>Form Feedback Aplikasi SIMWAS</title>
    <link rel="stylesheet" href="{{ asset('css/feedback.css') }}">
    <!-- Alpine CDN -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="md:pl-8">
    <!-- AlpineJS root -->
<section class="grid grid-cols-2 gap-x-24" x-data="{ activeForm: 'none' }">

    <!-- Buttons & Judul (grid kiri) -->
    <aside class="text-gray-700 flex flex-col gap-y-5">
      <!-- Announcement Sticker -->
      <!-- <div class="fixed mt-2 w-fit py-1 px-3 rounded-md text-red-600 border-2 border-red-600">
        <p>Ini environment Dev, jangan lupa remove banner ini sebelum push ke main repo</p>
      </div> -->
  
      <!-- Buttons & Info cards -->
      <div class="pt-20 pl-5">
        <img src="https://s6.imgcdn.dev/YNjSet.png" class="mb-5 w-32 h-auto">
        <h1 class="text-5xl">Berikan Saran & Laporkan Error</h1>
        <p class="text-gray-600 py-4">Kami mengharapkan partisipasi anda dalam memberi masukan, saran, serta kritik yang membangun untuk SIMWAS yang lebih andal dan bermanfaat.</p>
  
        <div class="mt-10 flex gap-x-3">
          <button @click="activeForm = 'A'" class="text-white py-2 px-5 rounded-lg bg-red-700 focus:ring-2 focus:ring-offset-2 ring-red-300">Laporkan Error & Bug</button>
          <button @click="activeForm = 'B'" class="text-white py-2 px-5 rounded-lg bg-green-800 focus:ring-2 focus:ring-offset-2 ring-emerald-200">Berikan Saran</button>
          <button @click="activeForm = 'C'" class="text-white py-2 px-5 rounded-lg bg-blue-800 focus:ring-2 focus:ring-offset-2">Tanya SIMWAS Bot <svg class="inline" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 512 512">
            <path fill="#fff" d="m320 192l-85.333-32L320 127.968l32-85.301l32.03 85.301L469.333 160l-85.303 32L352 277.333zM149.333 362.667L42.667 320l106.666-42.667L192 170.667l42.667 106.666L341.333 320l-106.666 42.667L192 469.333z" />
          </svg></button>          
        </div>

        <!-- Info Cards -->
        <div x-show="activeForm === 'A'" class="border border-red-700 mt-10 rounded-md p-4">
          <svg xmlns="http://www.w3.org/2000/svg" class="mb-1" width="28" height="28" viewBox="0 0 24 24"><path fill="#4d4d4d" d="M12 20a8 8 0 1 0 0-16a8 8 0 0 0 0 16m0 2C6.477 22 2 17.523 2 12S6.477 2 12 2s10 4.477 10 10s-4.477 10-10 10m-1-11v6h2v-6zm0-4h2v2h-2z"/></svg>
          <p>Kirimkan laporan Anda terkait bug atau masalah teknis yang ditemukan dalam aplikasi SIMWAS. Pastikan laporan tersebut mencakup deskripsi masalah secara detail.</p>
        </div>
        <div x-show="activeForm === 'B'" class="border border-green-700 mt-10 rounded-md p-4">
          <svg xmlns="http://www.w3.org/2000/svg" class="mb-1" width="28" height="28" viewBox="0 0 24 24"><path fill="#4d4d4d" d="M12 20a8 8 0 1 0 0-16a8 8 0 0 0 0 16m0 2C6.477 22 2 17.523 2 12S6.477 2 12 2s10 4.477 10 10s-4.477 10-10 10m-1-11v6h2v-6zm0-4h2v2h-2z"/></svg>
          <p>Anda dapat berbagi ide atau saran terkait penambahan fitur baru yang dapat meningkatkan fungsionalitas SIMWAS. <br> Selain itu, jika Anda memiliki ide untuk memperbaiki atau mengoptimalkan fitur yang sudah ada, jangan ragu untuk menyampaikannya. Masukan Anda akan menjadi bahan pertimbangan penting bagi kami dalam meningkatkan kualitas aplikasi ini.</p>
        </div>
        <div x-show="activeForm === 'C'" class="border border-blue-700 mt-10 rounded-md p-4">
          <span class="flex items-center gap-x-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="mb-1" width="28" height="28" viewBox="0 0 24 24"><path fill="#4d4d4d" d="M12 20a8 8 0 1 0 0-16a8 8 0 0 0 0 16m0 2C6.477 22 2 17.523 2 12S6.477 2 12 2s10 4.477 10 10s-4.477 10-10 10m-1-11v6h2v-6zm0-4h2v2h-2z"/></svg>
            <p class="font-semibold">Fitur Eksperimen</p>
          </span>
          <p>SIMWAS Bot merupakan chatbot Custom GPT yang dirancang untuk memberikan jawaban serta panduan terkait penggunaan SIMWAS. Saat ini, SIMWAS Bot masih dalam tahap pengembangan.</p>
        </div>
      </div>

<!-- Modal utk submit form -->
<dialog id="myDialog" class="p-0 w-[92vw] max-w-md rounded-2xl shadow-2xl open:animate-in open:fade-in [&::backdrop]:bg-black/50 [&::backdrop]:backdrop-blur-sm">
  <div class="p-6">
    <div class="flex items-start justify-between gap-6">
      <h2 class="text-xl text-gray-700 font-bold">Terima Kasih!</h2>
      <!-- Close (X) button -->
      <button id="closeBtn" aria-label="Close" class="shrink-0 rounded-full w-9 h-9 grid place-content-center text-gray-500 text-2xl hover:text-gray-700 hover:bg-gray-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500">×</button>
    </div>

    <p class="mt-3 text-gray-700">Berikut adalah ID Anda: <br> <span id="IdFeedback" class="my-2 grid place-items-center text-3xl font-bold">ABCD</span>ID di atas dapat dilihat perkembangannya di laman Monitoring laporan dan saran yang dapat diakses dengan tautan di bawah ini:</p>

    <div class="mt-6 flex justify-end gap-2">
      <button id="cancelBtn" class="px-4 py-2 rounded-xl bg-white border border-gray-200 text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500">Tutup</button>
      <a href="/feedback/monitoring" id="confirmBtn" class="no-underline px-4 py-2 rounded-xl bg-blue-600 text-white font-medium shadow hover:bg-blue-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500">Monitoring →</a>
    </div>
  </div>
</dialog>
      
      <!-- Footer -->
      <div class="fixed bottom-5">
        <a class="no-underline hover:underline text-gray-800" href="/feedback/monitoring">Monitor Saran & Laporan →</a>
        <p class="pt-3 text-gray-400 text-xs"><a target="_blank" href="https://git.bps.go.id/zhafran.ligardi/feedbacksimwas"> SIMWAS Feedback v1.2</a> | 01-09-2025 | Inspektorat Utama BPS </p>
      </div>
    </aside>
  
    <!-- Grid kanan -->
    <!-- Form A (Error) -->
    <form id="errorForm" x-show="activeForm === 'A'" @submit.prevent class="bg-red-50 h-screen px-28 pt-28">
      <h1 class="text-red-700 text-4xl mb-8">Laporkan Error & Bug</h1>
      <div class="flex flex-col gap-y-3">
        <input type="hidden" name="formType" value="errorReport">
        <label>
          <span class="text-sm font-medium">Nama Anda</span>
          <input required id="name" name="name" type="text" class="mt-0.5 p-3 w-full rounded border shadow-sm sm:text-sm"/>
        </label>
  
        <label>
          <span class="text-sm font-medium">E-mail BPS</span>
          <input placeholder="...@bps.go.id" required id="e-mail" name="e-mail" type="email" class="p-3 w-full rounded border shadow-sm sm:text-sm" />
        </label>

        <label>
          <fieldset class="border p-2.5 border-gray-400 rounded-lg text-sm flex gap-10">
            <legend class="px-1" id="urgensi">Urgensi</legend>
        
            <label>
              <input type="radio" name="priority" value="HIGH"/>
              High
            </label>
        
            <label>
              <input type="radio" name="priority" value="NORMAL"/>
              Normal
            </label>
        
            <label>
              <input type="radio" name="priority" value="LOW"/>
              Low
            </label>
          </fieldset>
        </label>
  
        <label>
          <span class="text-sm font-medium">Fungsi yang Error</span>
          <input required id="tempat-error" name="tempat-error" type="text" class="p-3 w-full rounded border shadow-sm sm:text-sm" />
        </label>

        <label>
          <span class="text-sm font-medium">Deskripsi</span>
          <textarea required id="penjelasan" name="penjelasan" class="w-full h-28 p-4 text-base border rounded resize-none" placeholder="Mohon untuk dijelaskan dengan lengkap"></textarea>
        </label>

        <input class="hidden" value="" type="text" readonly id="idLaporan" name="idLaporan">

        <input class="hidden" value="Menunggu respon" type="text" readonly id="respon_laporan" name="respon_laporan">
        
        <button type="submit" class="bg-gray-900 text-white w-28 rounded-md py-2 focus:ring-2 ring-gray-500 focus:ring-offset-2  focus:bg-gray-500">
        Submit</button>

      </div>
    </form>
  
    <!-- Form B (Saran) -->
    <form id="saranForm" x-show="activeForm === 'B'" @submit.prevent class="bg-green-50 h-screen px-28 pt-28">
      <h1 class="text-green-700 text-4xl mb-8">Berikan Saran</h1>
      <div class="flex flex-col gap-y-3">
        <input type="hidden" name="formType" value="saranReport">
        <label>
          <span class="text-sm font-medium">Nama Anda</span>
          <input required id="nameB" name="nameB" type="text" class="p-3 w-full rounded border shadow-sm sm:text-sm"/>
        </label>
  
        <label>
          <span class="text-sm font-medium">E-mail BPS <p class="text-xs italic inline">(Untuk keperluan komunikasi)</p></span>
          <input placeholder="Gunakan e-mail BPS anda" required id="emailB" name="emailB" type="email" class="p-3 w-full rounded border shadow-sm sm:text-sm" />
        </label>
  
        <label class="text-sm font-medium">Pilih Kategori Saran:</label>
        <select id="kategori" name="kategori" class="py-3 px-2 border rounded" required>
          <option value="" disabled selected>Pilih</option>
          <option value="tampilan-dan-fitur-simwas">Tampilan dan Fitur SIMWAS</option>
          <option value="proses-bisnis-SIMWAS">Proses Bisnis SIMWAS</option>
          <option value="memberi-ide">Ingin Memberi Ide</option>
          <option value="lainnya">Lainnya</option>
        </select>
  
        <label>
          <span class="text-sm font-medium">Ide Baru atau Saran</span>
          <textarea id="saran" name="saran" required class="w-full h-32 p-4 text-base border rounded resize-none" placeholder="Deskripsikan ide atau saran yang Bapak/Ibu miliki"></textarea>
        </label>

        <input hidden value="Menunggu respon" type="text" readonly id="respon_saran" name="respon_saran">
        <input hidden value="" type="text" readonly id="idSaran" name="idSaran">
          
        <button type="submit" class="bg-gray-900 text-white w-28 rounded-md py-2 focus:ring-2 ring-gray-500 focus:ring-offset-2  focus:bg-gray-500">
        Submit
        </button>
      </div>
    </form>
  
    <!-- SIMWAS Bot -->
    <form id="SaranBot" @submit.prevent class="flex flex-col mt-5">
    <div x-show="activeForm === 'C'" class="bg-blue-50 h-screen px-20 pt-32">
      <img class="rounded-lg mb-5 w-16 h-auto" src="https://s6.imgcdn.dev/YIY0ll.png">
      <h1 class="text-blue-700 text-4xl mb-4">Memperkenalkan SIMWAS Bot</h1>
      <p class="mb-5">Silakan tekan link di bawah untuk mengunjungi SIMWAS Bot. <br> Perlu diketahui, Pengguna perlu untuk memiliki akun ChatGPT dan log in sebelum menggunakan SIMWAS Bot. </p>
      <a href="https://chatgpt.com/g/g-683f9bf2fe688191ba30788525983f30-simwas-bot" target="_blank" class="no-underline text-white py-2 text-center rounded-lg bg-blue-800 block w-fit px-4 mb-3">
        Kunjungi Bot SIMWAS di ChatGPT <svg class="inline" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"><path fill="#fff" d="M8.46 6.3a.75.75 0 1 0 0 1.5h6.68l-8.62 8.62a.75.75 0 1 0 1.06 1.06l8.62-8.62v6.68a.75.75 0 0 0 1.5 0V7.05a.8.8 0 0 0-.06-.29a.76.76 0 0 0-.64-.46Z" /></svg>
      </a>
      <!-- form SimwasBOT -->
        <div class="p-1 rounded-lg w-80">
        <!-- textarea saran -->
        <div class="p-1 rounded-lg w-80">
          <input type="hidden" name="formType" value="saranBot">
          <label>
            <h2 class="text-sm font-semibold mb-2">Saran Untuk SIMWAS Bot</h2>
            <textarea id="saran_bot" name="saran_bot" required class="w-full h-24 p-4 text-sm border rounded resize-none" placeholder="Berikan masukan untuk pengembangan SIMWAS Bot"></textarea>
          </label>
          <button type="submit" class="bg-gray-900 text-white w-28 rounded-md py-2 focus:ring-2 ring-gray-500 focus:ring-offset-2  focus:bg-gray-500">Submit</button>
        </div>
        </div>
      </form>
</section>

<script>
  // inisialisasi variabel
const forms = document.querySelectorAll('form');
const idLaporan = document.getElementById('idLaporan');
const idSaran = document.getElementById('idSaran');

// func utk generate Identifier Token
function RandomString() {
const characters = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789"
let result = ""
for (let i = 0; i < 5; i++) {
  const randomIndex = Math.floor(Math.random() * characters.length)
  result += characters[randomIndex]
}
return result
}

// modal utk form submission
const dialog = document.getElementById('myDialog');
// const openBtn = document.getElementById('openDialog');
const closeBtn = document.getElementById('closeBtn');
const cancelBtn = document.getElementById('cancelBtn');
const confirmBtn = document.getElementById('confirmBtn');
// Event listener utk show/close modal pop-up
function showMessage() {
  setTimeout(() => {
    if (typeof dialog.showModal === 'function') {
      dialog.showModal();
    } else {
      console.log('ada error');
    }
  }, 1500);
  closeBtn.addEventListener('click', () => dialog.close());
  cancelBtn.addEventListener('click', () => dialog.close());
  confirmBtn.addEventListener('click', () => dialog.close());
}


// function RandomString untuk ID
window.addEventListener('load', function() {
  const randomString = RandomString();
  idLaporan.value = randomString;
  idSaran.value = randomString;
  document.getElementById('IdFeedback').textContent = randomString;
  console.log(randomString);
});
// event listener untuk form submission
forms.forEach(form => 
{
form.addEventListener('submit', async (e) => {
    e.preventDefault(); // Prevent default form submission, biar engga reload

    // Google apps script Production
    const webAppUrlProd = 'https://script.google.com/macros/s/AKfycbw3u9vuvuvkG5hscnep2ISmgFoixQkf789PgB0i9HQGDKf0n2fyqONdqe8WwVQe1Eu5Rg/exec';
    
    // // Google apps script utk Development
    // const webAppUrlDev = 'https://script.google.com/macros/s/AKfycbxBaHAqbM0mRY-ywTvYvnb4hcTFd28NwQvjbuzqRYK15hodxzhM-eVaUYUjiT4f4se5/exec';
    showMessage();
    const formData = new FormData(form);

    try {
        // post ke google sheet
        const response = await fetch(webAppUrlProd, {
            method: 'POST',
            body: formData
        });

        // cek apakah sukses
        if (response.ok) {
          const resultText = await response.text();
          console.log('Server response:', resultText);               
          form.reset();
        } else {
            const errorText = await response.text();
            console.error('Submission failed:', response.status, errorText);
        }
    } catch (error) {
        console.error('Network error or script error:', error);
    }
});
});
</script>
</body>
</html>