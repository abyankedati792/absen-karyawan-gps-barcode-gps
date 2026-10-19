@extends('layouts.app')

@section('content')
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                <h2 class="h6 mb-0">Riwayat Absensi Bulan {{ \Illuminate\Support\Carbon::parse($bulan.'-01')->translatedFormat('F Y') }}</h2>

                <form method="GET" action="{{ route('absensi.riwayat') }}" class="d-flex gap-2">
                    <input type="month" name="bulan" value="{{ $bulan }}" class="form-control form-control-sm"
                           style="max-width:180px;" onchange="this.form.submit()">
                </form>
            </div>

            @if($data->count())
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Masuk</th>
                                <th>Pulang</th>
                                <th>Status</th>
                                <th class="text-end">Jarak</th>
                                <th>Lokasi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data as $row)
                                <tr>
                                    <td>{{ $row->tanggal->format('d/m/Y') }}
                                        <div class="small text-muted">{{ $row->tanggal->translatedFormat('l') }}</div>
                                    </td>
                                    <td>
                                        {{ $row->jam_masuk?->format('H:i') ?? '-' }}
                                        @if($row->isTerlambat())
                                            <span class="badge bg-warning text-dark">telat</span>
                                        @endif
                                    </td>
                                    <td>{{ $row->jam_pulang?->format('H:i') ?? '-' }}</td>
                                    <td>
                                        @php($warna = ['hadir' => 'bg-success', 'terlambat' => 'bg-warning text-dark', 'izin' => 'bg-info', 'sakit' => 'bg-info', 'cuti' => 'bg-primary', 'alpha' => 'bg-danger'])
                                        <span class="badge {{ $warna[$row->status_masuk] ?? 'bg-secondary' }}">{{ ucfirst($row->status_masuk) }}</span>
                                    </td>
                                    <td class="text-end">{{ $row->jarak_masuk !== null ? number_format($row->jarak_masuk).' m' : '-' }}</td>
                                    <td class="small text-muted">{{ $row->lokasiKantor?->nama ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{ $data->links() }}
            @else
                <div class="text-center text-muted py-5">
                    <div class="fs-1">📭</div>
                    <p class="mb-0">Belum ada data absensi pada bulan ini.</p>
                </div>
            @endif
        </div>
    </div>
@endsection
