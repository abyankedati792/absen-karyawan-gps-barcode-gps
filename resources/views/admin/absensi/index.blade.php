@extends('layouts.app')

@section('content')
    {{-- ============ FILTER ============ --}}
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.absensi.index') }}" class="row g-2 align-items-end">
                <div class="col-6 col-md-2">
                    <label class="form-label small mb-1">Dari Tanggal</label>
                    <input type="date" name="mulai" value="{{ $filter['mulai'] }}" class="form-control form-control-sm">
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label small mb-1">Sampai</label>
                    <input type="date" name="sampai" value="{{ $filter['sampai'] }}" class="form-control form-control-sm">
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label small mb-1">Karyawan</label>
                    <select name="karyawan_id" class="form-select form-select-sm">
                        <option value="">Semua</option>
                        @foreach($karyawan as $k)
                            <option value="{{ $k->id }}" @selected((string) $filter['karyawan_id'] === (string) $k->id)>
                                {{ $k->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label small mb-1">Lokasi</label>
                    <select name="lokasi_id" class="form-select form-select-sm">
                        <option value="">Semua</option>
                        @foreach($lokasi as $l)
                            <option value="{{ $l->id }}" @selected((string) $filter['lokasi_id'] === (string) $l->id)>
                                {{ $l->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label small mb-1">Status</label>
                    <select name="status" class="form-select form-select-sm">
                        <option value="">Semua</option>
                        @foreach(['hadir', 'terlambat', 'izin', 'sakit', 'cuti', 'alpha'] as $s)
                            <option value="{{ $s }}" @selected($filter['status'] === $s)>{{ ucfirst($s) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label small mb-1">Cari Nama / NIP</label>
                    <input type="search" name="q" value="{{ $filter['q'] }}" class="form-control form-control-sm" placeholder="Nama…">
                </div>
                <div class="col-12 d-flex gap-2">
                    <button class="btn btn-primary btn-sm">🔍 Terapkan Filter</button>
                    <a href="{{ route('admin.absensi.index') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
                    <a href="{{ route('admin.absensi.export', request()->query()) }}" class="btn btn-success btn-sm ms-auto">
                        ⬇ Export CSV
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- ============ RINGKASAN ============ --}}
    <div class="row g-3 mb-3">
        <div class="col-6 col-lg-3">
            <div class="card stat-card border-0 shadow-sm">
                <div class="card-body py-3">
                    <div class="stat-label">Total Data</div>
                    <div class="stat-value">{{ number_format($jumlahTotal) }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card stat-card border-0 shadow-sm">
                <div class="card-body py-3">
                    <div class="stat-label">Hadir</div>
                    <div class="stat-value text-success">{{ number_format($jumlahHadir) }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card stat-card border-0 shadow-sm">
                <div class="card-body py-3">
                    <div class="stat-label">Terlambat</div>
                    <div class="stat-value text-warning">{{ number_format($jumlahTerlambat) }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card stat-card border-0 shadow-sm">
                <div class="card-body py-3">
                    <div class="stat-label">Izin / Sakit / Cuti</div>
                    <div class="stat-value text-info">{{ number_format($jumlahIzin) }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- ============ TABEL ============ --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            @if($data->count())
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Karyawan</th>
                                <th>Lokasi</th>
                                <th>Masuk</th>
                                <th>Pulang</th>
                                <th>Status</th>
                                <th class="text-end">Jarak Masuk</th>
                                <th>Koordinat Masuk</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data as $a)
                                <tr>
                                    <td class="text-nowrap">{{ $a->tanggal->format('d/m/Y') }}</td>
                                    <td>
                                        <strong>{{ $a->karyawan?->nama ?? '-' }}</strong>
                                        <div class="small text-muted">{{ $a->karyawan?->nip }}</div>
                                    </td>
                                    <td class="small">{{ $a->lokasiKantor?->nama ?? '-' }}</td>
                                    <td class="text-nowrap">
                                        {{ $a->jam_masuk?->format('H:i') ?? '-' }}
                                        @if($a->isTerlambat())
                                            <span class="badge bg-warning text-dark">telat</span>
                                        @endif
                                    </td>
                                    <td class="text-nowrap">{{ $a->jam_pulang?->format('H:i') ?? '-' }}</td>
                                    <td>
                                        @php($warna = ['hadir' => 'bg-success', 'terlambat' => 'bg-warning text-dark', 'izin' => 'bg-info', 'sakit' => 'bg-info', 'cuti' => 'bg-primary', 'alpha' => 'bg-danger'])
                                        <span class="badge {{ $warna[$a->status_masuk] ?? 'bg-secondary' }}">{{ ucfirst($a->status_masuk) }}</span>
                                    </td>
                                    <td class="text-end text-nowrap">
                                        {{ $a->jarak_masuk !== null ? number_format($a->jarak_masuk).' m' : '-' }}
                                    </td>
                                    <td class="small font-monospace text-nowrap">
                                        @if($a->latitude_masuk !== null)
                                            {{ $a->latitude_masuk }}, {{ $a->longitude_masuk }}
                                            <div class="text-muted" style="font-family:sans-serif">
                                                {{ \Illuminate\Support\Str::limit($a->alamat_masuk ?? '-', 40) }}
                                            </div>
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{ $data->links() }}
            @else
                <div class="text-center text-muted py-5">
                    <div class="fs-1">📋</div>
                    <p class="mb-0">Tidak ada data absensi pada rentang tanggal tersebut.</p>
                </div>
            @endif
        </div>
    </div>
@endsection
