<div class="main-sidebar sidebar-style-2">
    <aside id="sidebar-wrapper">
        <div class="sidebar-brand">
            <a href="{{ route('arsiparis.dashboard') }}">
                <img src="{{ asset('img/simwas-text.png') }}" alt="brand" style="width: 120px">
            </a>
            <span class="badge badge-primary">Arsiparis</span>
        </div>
        <div class="sidebar-brand sidebar-brand-sm">
            <a href="{{ route('arsiparis.dashboard') }}">
                <img src="{{ asset('img/simwas.svg') }}" alt="brand" style="width: 42px">
            </a>
        </div>
        <ul class="sidebar-menu">
            <li class="{{ Request::is('arsiparis') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('arsiparis.dashboard') }}">
                    <i class="fab fa-solid fa-house"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            {{-- <li class="{{ Request::is('arsiparis/surat-tugas*') ? 'active' : '' }}">
                <a class="nav-link" href="/arsiparis/surat-tugas">
                    <i class="fas fa-solid fa-envelope"></i>
                    <span>Surat Tugas</span>
                </a>
            </li> --}}
            {{-- <li class="{{ Request::is('arsiparis/norma-hasil*') ? 'active' : '' }}">
                <a class="nav-link" href="/arsiparis/norma-hasil">
                    <i class="fas fa-check"></i>
                    <span>Norma Hasil</span>
                </a>
            </li> --}}
            <li class="nav-item dropdown {{ $type_menu === 'norma-hasil' ? 'active active-dropdown' : '' }}">
                <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                    <i class="fas fa-check"></i>
                    <span>Norma Hasil</span>
                </a>
                <ul class="dropdown-menu">
                    <li
                        class="{{ Request::is('arsiparis/nh/laporan') ? 'active' : '' }}">
                        <a class="nav-link" href="/arsiparis/nh/laporan">Laporan</a>
                    </li>
                    <li
                        class="{{ Request::is('arsiparis/nh/dokumen') ? 'active' : '' }}">
                        <a class="nav-link" href="/arsiparis/nh/dokumen">Dokumen</a>
                    </li>
                </ul>
            </li>
            <li class="{{ Request::is('arsiparis/kendali-mutu*') ? 'active' : '' }}">
                <a class="nav-link" href="/arsiparis/kendali-mutu">
                    <i class="fas fa-magnifying-glass"></i>
                    <span>Kendali Mutu</span>
                </a>
            </li>
        </ul>
        @include('components.footer')
    </aside>
</div>
