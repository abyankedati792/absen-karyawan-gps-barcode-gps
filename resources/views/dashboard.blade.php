@extends('layouts.app')

@section('content')
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="card stat-card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="stat-label">Status Hari Ini</div>
                    <div class="stat-value">
                        @if($absensiHariIni?->jam_masuk)
                            <span class="badge bg-success fs-6">Sudah Absen</span>
                        @else
                            <span class="badge bg-warning text-dark fs-6">Belum Absen</span>
                        @endif
                    </div>
                    <div class="small text-muted mt-1">
                        {{ $absensiHariIni?->jam_masuk ? 'Masuk '.$absensiHariIni->jam_masuk->format('H:i').' WIB' : 'Silakan absen masuk' }}
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="card stat-card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="stat-label">Hadir Bulan Ini</div>
                    <div class="stat-value">{{ $totalHadir }}</div>
                    <div class="small text-muted">hari</div>
                </div>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="card stat-card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="stat-label">Terlambat</div>
                    <div class="stat-value text-warning">{{ $totalTerlambat }}</div>
                    <div class="small text-muted">hari</div>
                </div>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="card stat-card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="stat-label">Izin / Sakit / Cuti</div>
                    <div class="stat-value text-info">{{ $totalIzin }}</div>
                    <div class="small text-muted">hari</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h2 class="h6 mb-0">Absensi Hari Ini</h2>
                        <a href="{{ route('absensi') }}" class="btn btn-primary btn-sm">🧾 Absen Sekarang</a>
                    </div>

                    @if($absensiHariIni)
                        <div class="table-responsive">
                            <table class="table table-sm align-middle mb-0">
                                <tbody>
                                    <tr>
                                        <th style="width:35%">Jam Masuk</th>
                                        <td>{{ $absensiHariIni->jam_masuk?->format('H:i') ?? '-' }} WIB</td>
                                    </tr>
                                    <tr>
                                        <th>Jam Pulang</th>
                                        <td>{{ $absensiHariIni->jam_pulang?->format('H:i') ?? 'Belum absen pulang' }} </td>
                                    </tr>
                                    <tr>
                                        <th>Status</th>
                                        <td>
                                            <span class="badge {{ $absensiHariIni->status_masuk === 'terlambat' ? 'bg-warning text-dark' : 'bg-success' }}">
                                                {{ ucfirst($absensiHariIni->status_masuk) }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Jarak Masuk</th>
                                        <td>{{ $absensiHariIni->jarak_masuk !== null ? number_format($absensiHariIni->jarak_masuk).' m dari kantor' : '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Lokasi</th>
                                        <td class="small">{{ $absensiHariIni->alamat_masuk ?: 'Lat '.$absensiHariIni->latitude_masuk.', Lng '.$absensiHariIni->longitude_masuk }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <div class="fs-1">📍</div>
                            <p class="text-muted mb-2">Anda belum melakukan absen hari ini.</p>
                            <a href="{{ route('absensi') }}" class="btn btn-primary">Buka Halaman Absensi</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-body">
                    <h2 class="h6 mb-3">Lokasi Kantor</h2>
                    @if($kantor)
                        <p class="mb-1"><strong>{{ $kantor->nama }}</strong></p>
                        <p class="small text-muted mb-2">{{ $kantor->alamat }}</p>
                        <ul class="list-unstyled small mb-0">
                            <li> Radius absensi: <strong>{{ number_format($kantor->radius) }} m</strong></li>
                            <li> Jam masuk: <strong>{{ substr($kantor->jam_masuk, 0, 5) }}</strong> ( toleransi {{ $kantor->toleransi }} menit )</li>
                            <li> Jam pulang: <strong>{{ substr($kantor->jam_pulang, 0, 5) }}</strong></li>
                        </ul>
                    @else
                        <p class="text-muted small mb-0">Belum ada lokasi kantor aktif.</p>
                    @endif
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h2 class="h6 mb-3">Rekap {{ now()->translatedFormat('F Y') }}</h2>
                    <div class="d-flex justify-content-between small border-bottom py-2">
                        <span>Hadir</span><strong>{{ $totalHadir }} hari</strong>
                    </div>
                    <div class="d-flex justify-content-between small border-bottom py-2">
                        <span>Terlambat</span><strong class="text-warning">{{ $totalTerlambat }} hari</strong>
                    </div>
                    <div class="d-flex justify-content-between small border-bottom py-2">
                        <span>Izin / Sakit / Cuti</span><strong class="text-info">{{ $totalIzin }} hari</strong>
                    </div>
                    <div class="d-flex justify-content-between small py-2">
                        <span>Alfa</span><strong class="text-danger">{{ $totalAlfa }} hari</strong>
                    </div>
                    <a href="{{ route('absensi.riwayat') }}" class="btn btn-outline-secondary btn-sm w-100 mt-2">Lihat Riwayat</a>
                </div>
            </div>
        </div>
    </div>
@endsection
