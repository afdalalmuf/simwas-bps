@extends('layouts.app')

@section('title', 'Panduan')

@push('style')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- CSS Libraries -->
    <link
        href="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.13.4/af-2.5.3/b-2.3.6/b-colvis-2.3.6/b-html5-2.3.6/b-print-2.3.6/cr-1.6.2/date-1.4.1/fc-4.2.2/fh-3.3.2/kt-2.9.0/r-2.4.1/rg-1.3.1/rr-1.3.3/sc-2.1.1/sb-1.4.2/sp-2.1.2/sl-1.6.2/sr-1.2.2/datatables.min.css"
        rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('library') }}/sweetalert2/dist/sweetalert2.min.css">
@endpush

@section('main')
    @include('components.header')
    @include('components.pegawai-sidebar')
    <!-- Modal -->
    <div class="modal fade" id="uploadPanduanModal" tabindex="-1" role="dialog" aria-labelledby="uploadModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <form id="uploadPanduanForm" enctype="multipart/form-data">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Upload & Edit Panduan</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>

                    <div class="modal-body">
                        <!-- Drag & Drop Upload -->
                        <div class="form-group">
                            <label for="pdf">Upload PDF File</label>
                            <div id="drop-area" class="border rounded p-3 text-center" style="cursor: pointer;">
                                <input class="form-control" type="file" name="pdf" id="pdfInput"
                                    accept="application/pdf" hidden>
                                <p class="mb-0">Drag & drop PDF here or <strong>click to select</strong></p>
                                <small id="selected-file" class="mt-2 d-block"></small>
                            </div>
                        </div>

                        <!-- Index Editor (Loop from DB) -->
                        <div class="form-group">
                            <label>Edit Daftar Isi</label>
                            <div id="index-container">
                                @foreach ($index as $item)
                                    <div class="form-row mb-2">
                                        <div class="col">
                                            <input type="text" class="form-control"
                                                name="indexes[{{ $loop->index }}][label]" value="{{ $item->section }}">
                                        </div>
                                        <div class="col-3">
                                            <input type="number" class="form-control"
                                                name="indexes[{{ $loop->index }}][page]" value="{{ $item->page }}">
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-secondary mt-2" id="addIndex">+ Tambah
                                Index</button>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Panduan</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="/pegawai/dashboard">Dashboard</a></div>
                    <div class="breadcrumb-item">Panduan</div>
                </div>
            </div>

            <div class="section-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <!-- Sidebar -->
                                    <div class="col-md-3 border-right">
                                        <h5 class="mb-3">📑 Navigasi</h5>
                                        <ul class="list-group list-group-flush">
                                            @foreach ($index as $item)
                                                <button class="btn btn-link p-0 text-left"
                                                    onclick="goToPage({{ $item->page }})">
                                                    <li class="list-group-item">{{ $item->section }}</li>
                                                </button>
                                            @endforeach
                                        </ul>
                                    </div>

                                    <!-- PDF Viewer -->
                                    <div class="col-md-9">
                                        <!-- Upload & Download Controls -->
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h5 class="mb-0">📄 Pratinjau Panduan</h5>

                                            <div>
                                                @if (auth()->user()->is_admin)
                                                    <button class="btn btn-primary mr-2" data-toggle="modal"
                                                        data-target="#uploadPanduanModal">
                                                        <i class="fas fa-upload"></i> Upload Panduan
                                                    </button>
                                                @endif
                                                <a href="{{ asset('document/panduan/panduan.pdf') }}"
                                                    class="btn btn-success" download>
                                                    <i class="fas fa-download"></i> Download PDF
                                                </a>
                                            </div>
                                        </div>
                                        <iframe id="pdfViewer" src="{{ asset('document/panduan/panduan.pdf') }}#page=1"
                                            width="100%" height="700px" style="border: none;"></iframe>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('library') }}/sweetalert2/dist/sweetalert2.min.js"></script>
    <script>
        function goToPage(pageNumber) {
            const baseUrl = "{{ asset('document/panduan/panduan.pdf') }}";
            const viewer = document.getElementById('pdfViewer');

            // Add a timestamp to force iframe refresh
            const timestamp = new Date().getTime();
            viewer.src = `${baseUrl}?v=${timestamp}#page=${pageNumber}`;
        }

        // Drag & Drop Area
        const fileInput = document.getElementById('pdfInput');
        const dropArea = document.getElementById('drop-area');
        const fileDisplay = document.getElementById('selected-file');

        function updateFileNameDisplay(file) {
            const fileDisplay = document.getElementById('selected-file');

            if (!file) {
                fileDisplay.innerHTML = '';
                return;
            }

            const fileSize = (file.size / 1024 / 1024).toFixed(2); // MB
            const fileName = file.name;
            const fileType = file.type;

            if (fileType === 'application/pdf') {
                fileDisplay.innerHTML = `<span class="text-success">✅ ${fileName} (${fileSize} MB)</span>`;
            } else {
                fileDisplay.innerHTML = `<span class="text-danger">❌ ${fileName} is not a PDF file</span>`;
                fileInput.value = ''; // Reset input
            }
        }

        dropArea.addEventListener('click', () => fileInput.click());

        fileInput.addEventListener('change', function() {
            updateFileNameDisplay(this.files[0]); // ✅ update after click-select
        });

        dropArea.addEventListener('drop', e => {
            e.preventDefault();
            dropArea.classList.remove('bg-light');
            fileInput.files = e.dataTransfer.files;
            updateFileNameDisplay(fileInput.files[0]);
        });

        ['dragenter', 'dragover'].forEach(event => {
            dropArea.addEventListener(event, e => {
                e.preventDefault();
                dropArea.classList.add('bg-light');
            });
        });

        ['dragleave', 'drop'].forEach(event => {
            dropArea.addEventListener(event, e => {
                e.preventDefault();
                dropArea.classList.remove('bg-light');
            });
        });

        // Add new index input
        document.getElementById('addIndex').addEventListener('click', () => {
            const container = document.getElementById('index-container');
            const count = container.children.length;
            const div = document.createElement('div');
            div.className = "form-row mb-2";
            div.innerHTML = `
            <div class="col">
                <input type="text" name="indexes[${count}][label]" class="form-control" placeholder="Label">
            </div>
            <div class="col-3">
                <input type="number" name="indexes[${count}][page]" class="form-control" placeholder="Page">
            </div>
        `;
            container.appendChild(div);
        });

        // Submit form via AJAX
        document.getElementById('uploadPanduanForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const file = fileInput.files[0];
            const indexes = document.querySelectorAll('#index-container input[name^="indexes"]');

            if (!file) {
                Swal.fire({
                    icon: 'warning',
                    title: 'PDF belum dipilih',
                    text: 'Silakan unggah file PDF terlebih dahulu.',
                });
                return;
            }

            let hasValidIndex = false;
            for (let i = 0; i < indexes.length; i += 2) { // assuming label + page
                const label = indexes[i].value.trim();
                const page = indexes[i + 1].value.trim();
                if (label && page) {
                    hasValidIndex = true;
                    break;
                }
            }

            if (!hasValidIndex) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Daftar Isi kosong',
                    text: 'Harap isi minimal satu index dan halaman.',
                });
                return;
            }

            const formData = new FormData(this);

            fetch("{{ route('admin.panduan.upload') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json' // force JSON response
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        $('#uploadPanduanModal').modal('hide');
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: 'Panduan berhasil diperbarui!',
                            timer: 2000,
                            showConfirmButton: false
                        });
                        const viewer = document.getElementById('pdfViewer');
                        viewer.src = "{{ asset('document/panduan/panduan.pdf') }}?v=" + new Date().getTime() +
                            "#page=1";
                        setTimeout(() => location.reload(), 2000);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: 'Panduan gagal disimpan.',
                        });
                    }
                })
                .catch(error => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Terjadi Kesalahan',
                        text: 'Gagal mengunggah panduan.',
                    });
                    console.error('Upload error:', error);
                });
        });
    </script>
@endpush
