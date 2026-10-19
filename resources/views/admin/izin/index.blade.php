@extends('layouts.app')

@section('content')
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                <div>
                    <h2 class="h6 mb-1">Daftar Pengajuan</h2>
                    <span class="badge bg-warning text-dark">{{ $jumlahMenunggu }} menunggu persetujuan</span>
                </div>

                <ul class="nav nav-pills nav-pills-sm">
                    @foreach(['menunggu' => 'Menunggu', 'disetujui' => 'Disetujui', 'ditolak' => 'Ditolak', 'semua' => 'Semua'] as $nilai => $label)
                        <li class="nav-item">
                            <a class="nav-link nav-link-sm {{ $status === $nilai ? 'active' : '' }}"
                               href="{{ route('admin.izin.index', ['status' => $nilai]) }}">{{ $label }}</a>
                        </li>
                    @endforeach
                </ul>
            </div>

            @if($data->count())
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Karyawan</th>
                                <th>Jenis</th>
                                <th>Periode</th>
                                <th>Alasan</th>
                                <th style="min-width:230px;">Keputusan / Catatan</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data as $izin)
                                <tr>
                                    <td>
                                        <strong>{{ $izin->karyawan?->nama ?? '-' }}</strong>
                                        <div class="small text-muted">{{ $izin->karyawan?->nip }}</div>
                                    </td>
                                    <td><span class="badge bg-secondary">{{ $izin->labelJenis() }}</span></td>
                                    <td class="small text-nowrap">
                                        {{ $izin->tanggal_mulai->format('d/m/Y') }} &ndash;
                                        {{ $izin->tanggal_selesai->format('d/m/Y') }}
                                        <div class="text-muted">
                                            {{ $izin->tanggal_mulai->diffInDays($izin->tanggal_selesai) + 1 }} hari
                                        </div>
                                    </td>
                                    <td class="small">
                                        {{ \Illuminate\Support\Str::limit($izin->alasan, 70) }}
                                        @if($izin->surat)
                                            <div>
                                                <a href="{{ asset('storage/'.$izin->surat) }}" target="_blank" rel="noopener">
                                                    📎 Lihat surat
                                                </a>
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        @php($badge = ['menunggu' => 'bg-warning text-dark', 'disetujui' => 'bg-success', 'ditolak' => 'bg-danger'])
                                        <span class="badge {{ $badge[$izin->status] ?? 'bg-secondary' }}">{{ ucfirst($izin->status) }}</span>
                                        @if($izin->catatan_admin)
                                            <div class="small text-muted mt-1">{{ $izin->catatan_admin }}</div>
                                        @endif
                                        @if($izin->pengesah)
                                            <div class="small text-muted">oleh {{ $izin->pengesah->nama }}</div>
                                        @endif
                                    </td>
                                    <td class="text-end text-nowrap">
                                        @if($izin->status === 'menunggu')
                                            <form action="{{ route('admin.izin.update', $izin) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="catatan_admin" value="Disetujui admin">
                                                <button type="submit" name="aksi" value="setujui" class="btn btn-success btn-sm">✓ Setujui</button>
                                            </form>
                                            <form action="{{ route('admin.izin.update', $izin) }}" method="POST" class="d-inline"
                                                  data-confirm="Tolak pengajuan izin dari {{ $izin->karyawan?->nama }}?">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="catatan_admin" value="Ditolak admin">
                                                <button type="submit" name="aksi" value="tolak" class="btn btn-outline-danger btn-sm">✕ Tolak</button>
                                            </form>
                                        @else
                                            <form action="{{ route('admin.izin.destroy', $izin) }}" method="POST" class="d-inline"
                                                  data-confirm="Hapus pengajuan ini?">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-outline-secondary btn-sm">Hapus</button>
                                            </form>
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
                    <div class="fs-1">✅</div>
                    <p class="mb-0">Tidak ada pengajuan dengan status <strong>{{ $status }}</strong>.</p>
                </div>
            @endif
        </div>
    </div>
@endsection
