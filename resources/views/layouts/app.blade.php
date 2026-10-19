<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $judul ?? 'Dashboard' }} &middot; {{ $namaAplikasi ?? config('app.name') }}</title>
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
<div class="wrapper d-flex">

    {{-- ============ SIDEBAR ============ --}}
    <nav class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <span class="brand-icon">
                <img src="{{ asset($logoUrl ?? 'img/logo.svg') }}" alt="Logo" class="brand-logo">
            </span>
            <span class="brand-text">
                <strong>{{ $namaAplikasi ?? config('app.name') }}</strong>
                <small>{{ $pengaturan['nama_perusahaan'] ?? 'PT. Shelter Indonesia' }}</small>
            </span>
        </div>

        <ul class="sidebar-menu">
            <li class="menu-title">Menu Utama</li>
            <li>
                <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <span class="menu-icon">🏠</span> Dashboard
                </a>
            </li>
            <li>
                <a href="{{ route('absensi') }}" class="{{ request()->routeIs('absensi') ? 'active' : '' }}">
                    <span class="menu-icon">🧾</span> Absensi QR + GPS
                </a>
            </li>
            <li>
                <a href="{{ route('absensi.riwayat') }}" class="{{ request()->routeIs('absensi.riwayat') ? 'active' : '' }}">
                    <span class="menu-icon">📅</span> Riwayat Absensi
                </a>
            </li>
            <li>
                <a href="{{ route('izin.index') }}" class="{{ request()->routeIs('izin.index') ? 'active' : '' }}">
                    <span class="menu-icon">📝</span> Izin / Sakit / Cuti
                </a>
            </li>

            @if(auth()->user()?->isAdmin())
                <li class="menu-title">Administrasi</li>
                <li>
                    <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <span class="menu-icon">📊</span> Dashboard Admin
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.qr.index') }}" class="{{ request()->routeIs('admin.qr.*') ? 'active' : '' }}">
                        <span class="menu-icon">▣</span> QR Code Absensi
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.karyawan.index') }}" class="{{ request()->routeIs('admin.karyawan.*') ? 'active' : '' }}">
                        <span class="menu-icon">👥</span> Data Karyawan
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.lokasi.index') }}" class="{{ request()->routeIs('admin.lokasi.*') ? 'active' : '' }}">
                        <span class="menu-icon">🏢</span> Lokasi Kantor
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.absensi.index') }}" class="{{ request()->routeIs('admin.absensi.*') ? 'active' : '' }}">
                        <span class="menu-icon">📋</span> Rekap Absensi
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.izin.index') }}" class="{{ request()->routeIs('admin.izin.*') ? 'active' : '' }}">
                        <span class="menu-icon">✅</span> Persetujuan Izin
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.pengaturan.edit') }}" class="{{ request()->routeIs('admin.pengaturan.*') ? 'active' : '' }}">
                        <span class="menu-icon">⚙️</span> Pengaturan
                    </a>
                </li>
            @endif
        </ul>

        <div class="sidebar-footer">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-logout w-100">🚪 Keluar</button>
            </form>
        </div>
    </nav>

    {{-- ============ KONTEN ============ --}}
    <div class="content flex-grow-1">
        <header class="topbar d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center">
                <button class="btn btn-light btn-sm me-2 d-lg-none" id="btnSidebar" type="button">☰</button>
                <div>
                    <h1 class="topbar-title mb-0">{{ $judul ?? 'Dashboard' }}</h1>
                    <small class="text-muted">{{ now()->translatedFormat('l, d F Y') }} &middot; {{ now()->format('H:i') }} WIB</small>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2">
                @php($user = auth()->user())
                <a href="{{ route('profil') }}" class="d-flex align-items-center text-decoration-none text-dark">
                    <span class="avatar">{{ strtoupper(substr($user->nama ?? 'U', 0, 1)) }}</span>
                    <span class="ms-2 d-none d-md-inline">
                        <strong class="d-block lh-1">{{ $user->nama ?? '-' }}</strong>
                        <small class="text-muted">{{ ucfirst($user->role ?? '') }}</small>
                    </span>
                </a>
            </div>
        </header>

        <main class="page p-3 p-lg-4">
            @include('partials.alert')
            @yield('content')
        </main>

        <footer class="text-center text-muted small py-3">
            &copy; {{ date('Y') }} {{ $pengaturan['nama_perusahaan'] ?? 'PT. Shelter Indonesia' }} &middot; Absensi QR Code + GPS
        </footer>
    </div>
</div>

<script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('js/app.js') }}"></script>
@stack('scripts')
</body>
</html>
