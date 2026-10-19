@extends('layouts.app')

@section('content')
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                <form method="GET" action="{{ route('admin.karyawan.index') }}" class="d-flex gap-2">
                    <input type="search" name="q" value="{{ $q }}" class="form-control form-control-sm"
                           placeholder="Cari nama / NIP / email / departemen…" style="min-width:260px;">
                    <button class="btn btn-outline-secondary btn-sm">Cari</button>
                    @if($q !== '')
                        <a href="{{ route('admin.karyawan.index') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
                    @endif
                </form>
                <a href="{{ route('admin.karyawan.create') }}" class="btn btn-primary btn-sm">＋ Tambah Karyawan</a>
            </div>

            @if($data->count())
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>NIP</th>
                                <th>Nama</th>
                                <th>Jabatan</th>
                                <th>Departemen</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data as $k)
                                <tr>
                                    <td class="font-monospace">{{ $k->nip }}</td>
                                    <td>
                                        <strong>{{ $k->nama }}</strong>
                                        @if($k->no_hp)
                                            <div class="small text-muted">{{ $k->no_hp }}</div>
                                        @endif
                                    </td>
                                    <td class="small">{{ $k->jabatan ?? '-' }}</td>
                                    <td class="small">{{ $k->departemen ?? '-' }}</td>
                                    <td class="small">{{ $k->email }}</td>
                                    <td><span class="badge {{ $k->role === 'admin' ? 'bg-primary' : 'bg-secondary' }}">{{ ucfirst($k->role) }}</span></td>
                                    <td><span class="badge {{ $k->status === 'aktif' ? 'bg-success' : 'bg-danger' }}">{{ ucfirst($k->status) }}</span></td>
                                    <td class="text-end text-nowrap">
                                        <a href="{{ route('admin.karyawan.edit', $k) }}" class="btn btn-outline-primary btn-sm">Edit</a>
                                        <form action="{{ route('admin.karyawan.destroy', $k) }}" method="POST" class="d-inline"
                                              data-confirm="Hapus karyawan {{ $k->nama }} beserta data absensinya?">
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

                {{ $data->links() }}
            @else
                <div class="text-center text-muted py-5">
                    <div class="fs-1">👥</div>
                    <p class="mb-0">Tidak ada karyawan yang cocok.</p>
                </div>
            @endif
        </div>
    </div>
@endsection
