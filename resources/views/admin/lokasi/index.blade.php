@extends('layouts.app')

@section('content')
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="h6 mb-0">Daftar Lokasi Kantor</h2>
                <a href="{{ route('admin.lokasi.create') }}" class="btn btn-primary btn-sm">＋ Tambah Lokasi</a>
            </div>

            @if($data->count())
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Koordinat</th>
                                <th class="text-end">Radius</th>
                                <th>Jam Masuk</th>
                                <th>Jam Pulang</th>
                                <th>Toleransi</th>
                                <th>Utama</th>
                                <th>Status</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data as $l)
                                <tr>
                                    <td>
                                        <strong>{{ $l->nama }}</strong>
                                        <div class="small text-muted">{{ \Illuminate\Support\Str::limit($l->alamat ?? '-', 50) }}</div>
                                    </td>
                                    <td class="small font-monospace">
                                        {{ $l->latitude }}<br>{{ $l->longitude }}
                                    </td>
                                    <td class="text-end">{{ number_format($l->radius) }} m</td>
                                    <td>{{ substr($l->jam_masuk, 0, 5) }}</td>
                                    <td>{{ substr($l->jam_pulang, 0, 5) }}</td>
                                    <td>{{ $l->toleransi }} mnt</td>
                                    <td>
                                        @if($l->is_default)
                                            <span class="badge bg-primary">Utama</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td><span class="badge {{ $l->status === 'aktif' ? 'bg-success' : 'bg-danger' }}">{{ ucfirst($l->status) }}</span></td>
                                    <td class="text-end text-nowrap">
                                        <a href="{{ route('admin.lokasi.edit', $l) }}" class="btn btn-outline-primary btn-sm">Edit</a>
                                        <form action="{{ route('admin.lokasi.destroy', $l) }}" method="POST" class="d-inline"
                                              data-confirm="Hapus lokasi {{ $l->nama }}?">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-outline-danger btn-sm">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center text-muted py-5">
                    <div class="fs-1">🏢</div>
                    <p>Belum ada lokasi kantor. Tambahkan dulu agar absensi GPS bisa dipakai.</p>
                    <a href="{{ route('admin.lokasi.create') }}" class="btn btn-primary btn-sm">Tambah Lokasi</a>
                </div>
            @endif
        </div>
    </div>
@endsection
