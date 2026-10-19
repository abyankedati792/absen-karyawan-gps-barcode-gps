@extends('layouts.app')

@section('content')
    <div class="row g-3 justify-content-center">
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body p-4">
                    <img src="{{ asset($logoUrl ?? 'img/logo.svg') }}" alt="Logo" class="qr-logo d-block mb-2">
                    <h2 class="h5 mb-1">QR Code Absensi Hari Ini</h2>
                    <p class="text-muted small mb-3">
                        {{ $kantor->nama }} &middot; radius {{ number_format($kantor->radius) }} m
                        &middot; berlaku {{ $menit }} menit
                    </p>

                    <div class="qr-wrap mx-auto mb-3">
                        <div id="qrcode"></div>
                    </div>

                    <div class="mb-2">
                        <span class="badge bg-light text-dark border fs-6" id="sisaWaktu">--:--</span>
                        <span class="badge bg-secondary fs-6">otentikasi ulang otomatis</span>
                    </div>

                    <div class="input-group input-group-sm mb-2" style="max-width:520px; margin:0 auto;">
                        <input type="text" class="form-control font-monospace text-center" id="payloadText"
                               value="{{ $qr->payload() }}" readonly>
                        <button class="btn btn-outline-secondary" type="button" id="btnSalin">Salin</button>
                    </div>

                    <div class="small text-muted" id="infoQr">
                        Token: <code>{{ $qr->token }}</code><br>
                        Berlaku sampai {{ $qr->berlaku_sampai->format('H:i:s') }} WIB
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-body">
                    <h3 class="h6 mb-3">Petunjuk</h3>
                    <ol class="small ps-3 mb-0">
                        <li class="mb-1">Tampilkan QR ini di layar / cetak di pintu masuk.</li>
                        <li class="mb-1">Karyawan masuk ke menu <strong>Absensi QR + GPS</strong>.</li>
                        <li class="mb-1">Karyawan mengambil lokasi lalu memindai QR ini.</li>
                        <li class="mb-1">QR <strong>otomatis berganti</strong> setiap {{ $menit }} menit &mdash;
                            biarkan halaman ini terbuka.</li>
                    </ol>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h3 class="h6 mb-2">Data Token</h3>
                    <div class="small">
                        <div class="d-flex justify-content-between border-bottom py-1">
                            <span class="text-muted">Tanggal</span><strong>{{ $qr->tanggal->format('d/m/Y') }}</strong>
                        </div>
                        <div class="d-flex justify-content-between border-bottom py-1">
                            <span class="text-muted">Status</span><span class="badge bg-success">Aktif</span>
                        </div>
                        <div class="d-flex justify-content-between py-1">
                            <span class="text-muted">Server time</span><strong id="serverTime">{{ now()->format('H:i:s') }}</strong>
                        </div>
                    </div>
                    <a href="{{ route('absensi') }}" class="btn btn-outline-primary btn-sm w-100 mt-2">
                        Buka Halaman Absensi
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        window.QR_URL = @json(route('admin.qr.token'));
        window.QR_AWAL = @json($qr->payload());
        window.QR_EXPIRES = @json($qr->berlaku_sampai->toIso8601String());
        window.QR_MENIT = @json((int) $menit);
    </script>
    <script src="{{ asset('js/qrcode.min.js') }}"></script>
    <script src="{{ asset('js/qr-display.js') }}"></script>
@endpush
