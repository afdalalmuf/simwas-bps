@extends('layouts.app')

@section('title', 'Kelola Arsip')

@section('main')
    @include('components.arsiparis-header')
    @include('components.arsiparis-sidebar')
    @include('components.arsiparis.modal-detail-arsip')
    @include('components.arsiparis.modal-preview-dokumen')

    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Kelola Arsip</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="/arsiparis">Dashboard</a></div>
                    <div class="breadcrumb-item">Kelola Arsip</div>
                </div>
            </div>

            <div class="section-body">

                {{-- Tabs --}}
                <ul class="nav nav-pills nav-justified mb-3" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link {{ ($activeTab ?? 'create') === 'create' || ($activeTab ?? '') === 'edit' ? 'active' : '' }}"
                            data-toggle="tab" href="#tab-create">
                            <i class="fas fa-plus-circle mr-1"></i>
                            {{ isset($editArsip) ? 'Lengkapi Arsip' : 'Tambah Arsip Baru' }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ ($activeTab ?? '') === 'show' ? 'active' : '' }}" data-toggle="tab"
                            href="#tab-show">
                            <i class="fas fa-list mr-1"></i> Lihat Daftar Arsip
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ ($activeTab ?? '') === 'jra' ? 'active' : '' }}" data-toggle="tab"
                            href="#tab-jra">
                            <i class="far fa-clock mr-1"></i> Cek JRA
                        </a>
                    </li>
                </ul>

                <div class="tab-content">
                    {{-- Tab: Tambah / Edit Arsip --}}
                    <div class="tab-pane fade {{ !isset($activeTab) || in_array($activeTab, ['create', 'edit']) ? 'show active' : '' }}"
                        id="tab-create" role="tabpanel">
                        @include('arsiparis.kelola-arsip.create')
                    </div>

                    {{-- Tab: Daftar Arsip --}}
                    <div class="tab-pane fade {{ isset($activeTab) && $activeTab === 'show' ? 'show active' : '' }}"
                        id="tab-show" role="tabpanel">
                        @include('arsiparis.kelola-arsip.show')
                    </div>

                    {{-- Tab: Cek JRA --}}
                    <div class="tab-pane fade {{ isset($activeTab) && $activeTab === 'jra' ? 'show active' : '' }}"
                        id="tab-jra" role="tabpanel">
                        @include('arsiparis.kelola-arsip.jra')
                    </div>
                </div>

            </div>
        </section>
    </div>
@endsection