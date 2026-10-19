@extends('layouts.app')

@section('content')
    <div class="row g-3 mb-4">
        <div class="col-6 col-xl-2">
            <div class="card stat-card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="stat-label">Karyawan Aktif</div>
                    <div class="stat-value">{{ $totalKaryawan }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-2">
            <div class="card stat-card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="stat-label">Hadir Hari Ini</div>
                    <div class="stat-value text-success">{{ $hadirHariIni }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-2">
            <div class="card stat-card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="stat-label">Terlambat</div>
                    <div class="stat-value text-warning">{{ $terlambatHariIni }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-2">
            <div class="card stat-card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="stat-label">Izin / Sakit</div>
                    <div class="stat-value text-info">{{ $izinHariIni }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-2">
            <div class="card stat-card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="stat-label">Belum Absen</div>
                    <div class="stat-value text-danger">{{ $belumAbsen }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-2">
            <div class="card stat-card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="stat-label">Scan Gagal</div>
                    <div class="stat-value text-secondary">{{ $totalScanGagal }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h2 class="h6 mb-0">Kehadiran 7 Hari Terakhir</h2>
                        <div class="small">
                            <span class="badge bg-success">&nbsp;</span> Hadir
                            <span class="badge bg-warning text-dark ms-2">&nbsp;</span> Terlambat
                        </div>
                    </div>

                    <div class="chart-bars d-flex align-items-end gap-2" style="height:180px;">
                        @foreach($labels as $i => $label)
                            @php($totalBar = max(1, $seriHadir[$i] + $seriTerlambat[$i]))
                            <div class="flex-fill text-center h-100 d-flex flex-column justify-content-end">
                                <div class="d-flex flex-column justify-content-end h-100">
                                    <div class="bar bar-terlambat"
                                         style="height:{{ ($seriTerlambat[$i] / $totalBar) * 70 }}%"
                                         title="Terlambat: {{ $seriTerlambat[$i] }}"></div>
                                    <div class="bar bar-hadir"
                                         style="height:{{ ($seriHadir[$i] / $totalBar) * 95 }}%"
                                         title="Hadir: {{ $seriHadir[$i] }}"></div>
                                </div>
                                <small class="text-muted mt-1">{{ $label }}</small>
                            </div>
                        @endforeach
                    </div>
                    <div class="small text-muted mt-2 text-center">Angka di tooltip = jumlah karyawan</div>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h2 class="h6 mb-3">Rekap {{ now()->translatedFormat('F Y') }}</h2>
                    <div class="d-flex justify-content-between border-bottom py-2 small">
                        <span>Total hadir</span><strong>{{ $totalHadirBulan }}</strong>
                    </div>
                    <div class="d-flex justify-content-between border-bottom py-2 small">
                        <span>Total terlambat</span><strong class="text-warning">{{ $totalTerlambatBulan }}</strong>
                    </div>
                    <div class="d-flex justify-content-between border-bottom py-2 small">
                        <span>Izin menunggu persetujuan</span><strong class="text-info">{{ $izinMenunggu }}</strong>
                    </div>

                    <div class="d-grid gap-2 mt-3">
                        <a href="{{ route('admin.qr.index') }}" class="btn btn-primary btn-sm">▣ Tampilkan QR Absensi</a>
                        <a href="{{ route('admin.absensi.index') }}" class="btn btn-outline-secondary btn-sm">📋 Buka Rekap Absensi</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="h6 mb-0">Aktivitas Absensi Hari Ini</h2>
                <span class="text-muted small">{{ now()->format('d/m/Y') }}</span>
            </div>

            @if($absensiTerbaru->count())
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Karyawan</th>
                                <th>Masuk</th>
                                <th>Pulang</th>
                                <th>Status</th>
                                <th class="text-end">Jarak</th>
                                <th>Update</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($absensiTerbaru as $a)
                                <tr>
                                    <td>
                                        <strong>{{ $a->karyawan?->nama ?? '-' }}</strong>
                                        <div class="small text-muted">{{ $a->karyawan?->nip }} &middot; {{ $a->karyawan?->departemen }}</div>
                                    </td>
                                    <td>{{ $a->jam_masuk?->format('H:i') ?? '-' }}</td>
                                    <td>{{ $a->jam_pulang?->format('H:i') ?? '-' }}</td>
                                    <td>
                                        @php($warna = ['hadir' => 'bg-success', 'terlambat' => 'bg-warning text-dark', 'izin' => 'bg-info', 'sakit' => 'bg-info', 'cuti' => 'bg-primary', 'alpha' => 'bg-danger'])
                                        <span class="badge {{ $warna[$a->status_masuk] ?? 'bg-secondary' }}">{{ ucfirst($a->status_masuk) }}</span>
                                    </td>
                                    <td class="text-end">{{ $a->jarak_masuk !== null ? number_format($a->jarak_masuk).' m' : '-' }}</td>
                                    <td class="small text-muted">{{ $a->updated_at?->format('H:i:s') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-muted small mb-0">Belum ada aktivitas absensi hari ini.</p>
            @endif
        </div>
    </div>
@endsection
