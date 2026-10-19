@extends('layouts.app')

@section('content')
    @php
        $sudahMasuk = $absensi?->jam_masuk !== null;
        $sudahPulang = $absensi?->jam_pulang !== null;
    @endphp

    <div class="row g-3">
        {{-- ================= KOLOM KIRI: FORM ABSENSI ================= --}}
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm mb-3"
                 id="absensiBox"
                 data-lat-kantor="{{ $kantor?->latitude }}"
                 data-lng-kantor="{{ $kantor?->longitude }}"
                 data-radius="{{ $kantor?->radius ?? 150 }}"
                 data-nama-kantor="{{ $kantor?->nama ?? '' }}">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h2 class="h5 mb-1">Absensi QR Code + GPS</h2>
                            <div class="text-muted small">
                                {{ $kantor?->nama ?? 'Lokasi kantor belum diatur' }}
                                &middot; radius {{ number_format($kantor?->radius ?? 0) }} m
                            </div>
                        </div>
                        <div class="text-end">
                            <div class="fs-4 fw-bold text-primary" id="jamSekarang">--:--:--</div>
                            <small class="text-muted">{{ now()->translatedFormat('d M Y') }}</small>
                        </div>
                    </div>

                    {{-- Pilihan jenis absensi --}}
                    <ul class="nav nav-pills mb-3" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="tab-masuk" data-bs-toggle="pill"
                                    data-bs-target="#pane-masuk" type="button"
                                    @disabled($sudahMasuk)>Absen Masuk</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="tab-pulang" data-bs-toggle="pill"
                                    data-bs-target="#pane-pulang" type="button"
                                    @disabled(!$sudahMasuk || $sudahPulang)>Absen Pulang</button>
                        </li>
                    </ul>

                    <form action="{{ route('absensi.store') }}" method="POST" enctype="multipart/form-data" id="formAbsensi">
                        @csrf
                        <input type="hidden" name="tipe" id="inputTipe" value="masuk">
                        <input type="hidden" name="latitude" id="inputLat">
                        <input type="hidden" name="longitude" id="inputLng">
                        <input type="hidden" name="alamat" id="inputAlamat" value="">

                        {{-- STATUS ABSENSI --}}
                        <div class="row text-center g-2 mb-3">
                            <div class="col-6">
                                <div class="border rounded p-2 h-100 {{ $sudahMasuk ? 'bg-success bg-opacity-10' : 'bg-light' }}">
                                    <div class="small text-muted">Absen Masuk</div>
                                    <div class="fw-bold {{ $sudahMasuk ? 'text-success' : 'text-muted' }}">
                                        {{ $absensi?->jam_masuk?->format('H:i') ?? 'Belum' }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="border rounded p-2 h-100 {{ $sudahPulang ? 'bg-success bg-opacity-10' : 'bg-light' }}">
                                    <div class="small text-muted">Absen Pulang</div>
                                    <div class="fw-bold {{ $sudahPulang ? 'text-success' : 'text-muted' }}">
                                        {{ $absensi?->jam_pulang?->format('H:i') ?? 'Belum' }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- 1. LOKASI GPS --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">1. Lokasi GPS Anda</label>
                            <div class="d-flex gap-2 flex-wrap mb-2">
                                <button type="button" class="btn btn-outline-primary btn-sm" id="btnLokasi">
                                    🎯 Ambil Lokasi Sekarang
                                </button>
                                <span class="badge align-self-center {{ $sudahMasuk ? 'bg-secondary' : 'bg-light text-dark border' }}" id="badgeLokasi">
                                    Belum ada lokasi
                                </span>
                            </div>
                            <div class="small text-muted" id="infoLokasi">
                                Lokasi dibutuhkan untuk memastikan Anda berada di area kantor.
                            </div>
                        </div>

                        <hr class="my-3">

                        {{-- 2. QR CODE --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">2. Pindai QR Code Absensi</label>
                            <div class="d-flex gap-2 flex-wrap mb-2">
                                <button type="button" class="btn btn-primary btn-sm" id="btnPindai">
                                    📷 Buka Kamera &amp; Pindai
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" id="btnManual">
                                    ✏️ Input Token Manual
                                </button>
                            </div>

                            <input type="text" name="qrcode" id="inputQr"
                                   class="form-control @error('qrcode') is-invalid @enderror"
                                   placeholder="Hasil pindaian QR / token absensi"
                                   value="{{ old('qrcode') }}" required>
                            @error('qrcode')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                QR Code ditampilkan admin di halaman <strong>QR Code Absensi</strong> dan berganti otomatis tiap {{ $pengaturan['menit_qr'] ?? 5 }} menit.
                            </div>
                        </div>

                        <hr class="my-3">

                        {{-- 3. FOTO --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">3. Foto ( opsional )</label>
                            <input type="file" name="foto" id="inputFoto" accept="image/*" capture="environment"
                                   class="form-control @error('foto') is-invalid @enderror">
                            @error('foto')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div id="previewFoto" class="mt-2 d-none">
                                <img src="" alt="Preview foto" class="img-thumbnail" style="max-height:160px">
                            </div>
                        </div>

                        @error('lokasi')
                            <div class="alert alert-danger py-2">{{ $message }}</div>
                        @enderror

                        <button type="submit" class="btn btn-success btn-lg w-100" id="btnKirim" disabled>
                            Simpan Absensi
                        </button>
                        <div class="form-text text-center">
                            Pastikan <strong>lokasi GPS</strong> dan <strong>QR Code</strong> sudah terisi.
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- ================= KOLOM KANAN: PANDUAN ================= --}}
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-body">
                    <h2 class="h6 mb-3">📋 Cara Absensi</h2>
                    <ol class="small ps-3 mb-3">
                        <li class="mb-1">Tekan <strong>Ambil Lokasi</strong>, izinkan akses GPS.</li>
                        <li class="mb-1">Tekan <strong>Buka Kamera</strong>, arahkan ke QR Code absensi ( ditampilkan admin ).</li>
                        <li class="mb-1">Opsional: unggah foto diri.</li>
                        <li class="mb-1">Tekan <strong>Simpan Absensi</strong>.</li>
                    </ol>

                    <div class="alert alert-info small mb-0">
                        <strong>Rentang absensi:</strong><br>
                        Lat {{ $kantor?->latitude ?? '-' }}, Lng {{ $kantor?->longitude ?? '-' }}<br>
                        Jika jarak Anda melebihi <strong>{{ number_format($kantor?->radius ?? 0) }} meter</strong> dari kantor,
                        absensi akan ditolak sistem.
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h2 class="h6 mb-3">🕘 Jam Kerja Hari Ini</h2>
                    @if($kantor)
                        <div class="d-flex justify-content-between small border-bottom py-2">
                            <span>Masuk</span>
                            <strong>{{ substr($kantor->jam_masuk, 0, 5) }} ( toleransi {{ $kantor->toleransi }} mnt )</strong>
                        </div>
                        <div class="d-flex justify-content-between small py-2">
                            <span>Pulang</span>
                            <strong>{{ substr($kantor->jam_pulang, 0, 5) }}</strong>
                        </div>
                    @else
                        <p class="small text-muted mb-0">Hubungi admin untuk mengatur lokasi kantor.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- ================= MODAL KAMERA QR ================= --}}
    <div class="modal fade" id="modalPindai" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title h6">Pindai QR Code Absensi</h3>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body text-center bg-dark">
                    <div class="position-relative d-inline-block w-100">
                        <video id="videoPindai" class="w-100 rounded" style="max-height:60vh; object-fit:cover;"></video>
                        <div id="garisPindai" class="scan-line d-none"></div>
                    </div>
                    <canvas id="canvasPindai" class="d-none"></canvas>
                    <p class="text-white small mt-2 mb-0" id="statusPindai">Arahkan kamera ke QR Code&hellip;</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal" id="btnTutupKamera">Tutup Kamera</button>
                </div>
            </div>
        </div>
    </div>

    <div class="visually-hidden" id="konfigurasi"
         data-masuk-terisi="{{ $sudahMasuk ? 1 : 0 }}"
         data-pulang-terisi="{{ $sudahPulang ? 1 : 0 }}"></div>
@endsection

@push('scripts')
    <script src="{{ asset('js/jsQR.js') }}"></script>
    <script src="{{ asset('js/scan.js') }}"></script>
@endpush
