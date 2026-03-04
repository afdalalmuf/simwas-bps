@if (!isset($type_menu))
<?php $type_menu = ''; ?>
@endif
<div class="main-sidebar sidebar-style-2">
    <aside id="sidebar-wrapper">
        <div class="sidebar-brand">
            <a href="{{ route('auditee.dashboard') }}">
                <img src="{{ asset('img/simwas-text.png') }}" alt="brand" style="width: 120px">
            </a>
            <span class="badge badge-primary">Auditi</span>
        </div>
        <div class="sidebar-brand sidebar-brand-sm">
            <a href="{{ route('analis-sdm.kategori') }}">
                <img src="{{ asset('img/simwas.svg') }}" alt="brand" style="width: 42px">
            </a>
        </div>
        <ul class="sidebar-menu">
            <li class="{{ Request::is('auditee/dashboard') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('auditee.dashboard') }}">
                    <i class="fab fa-solid fa-house"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="nav-item dropdown {{ $type_menu === 'auditee-schedule' ? 'active active-dropdown' : '' }}">
                <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                    <i class="fas fa-award"></i>
                    <span>Pengawasan</span>
                </a>
                <ul class="dropdown-menu">
                    <li class="{{ Request::is('auditee/schedule*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('auditee.schedule') }}">
                            <span>Jadwal</span>
                        </a>
                    </li>
                </ul>
            </li>
        </ul>
        @include('components.footer')
    </aside>
</div>
