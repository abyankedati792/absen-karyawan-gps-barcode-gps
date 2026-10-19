<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $judul ?? 'Masuk' }} &middot; {{ $namaAplikasi ?? config('app.name') }}</title>
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body class="login-page">
<div class="container">
    <div class="row justify-content-center align-items-center min-vh-100 py-4">
        <div class="col-sm-10 col-md-8 col-lg-5 col-xl-4">
            <div class="text-center mb-4">
                <div class="login-logo">
                    <img src="{{ asset($logoUrl ?? 'img/logo.svg') }}" alt="Logo {{ $pengaturan['nama_perusahaan'] ?? 'Perusahaan' }}">
                </div>
                <h1 class="h4 mt-3 mb-1">{{ $namaAplikasi ?? config('app.name') }}</h1>
                <p class="text-muted mb-0">{{ $pengaturan['nama_perusahaan'] ?? 'PT. Shelter Indonesia' }}</p>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    @include('partials.alert')
                    @yield('content')
                </div>
            </div>

            <p class="text-center text-muted small mt-3 mb-0">
                Verifikasi lokasi GPS &amp; QR Code &middot; {{ date('Y') }}
            </p>
        </div>
    </div>
</div>
<script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('js/app.js') }}"></script>
@stack('scripts')
</body>
</html>
