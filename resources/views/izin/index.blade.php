@extends('layouts.app')

@section('content')
    <div class="row g-3">
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h2 class="h6 mb-3">📝 Ajukan Izin / Sakit / Cuti</h2>

                    <form action="{{ route('izin.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label" for="jenis">Jenis</label>
                            <select name="jenis" id="jenis" class="form-select @error('jenis') is-invalid @enderror" required>
                                <option value="izin" @selected(old('jenis') === 'izin')>Izin</option>
                                <option value="sakit" @selected(old('jenis') === 'sakit')>Sakit</option>
                                <option value="cuti" @selected(old('jenis') === 'cuti')>Cuti</option>
                            </select>
                            @error('jenis') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-6">
                                <label class="form-label" for="tanggal_mulai">Mulai</label>
                                <input type="date" name="tanggal_mulai" id="tanggal_mulai"
                                       value="{{ old('tanggal_mulai') }}" min="{{ now()->toDateString() }}"
                                       class="form-control @error('tanggal_mulai') is-invalid @enderror" required>
                                @error('tanggal_mulai') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-6">
                                <label class="form-label" for="tanggal_selesai">Sampai</label>
                                <input type="date" name="tanggal_selesai" id="tanggal_selesai"
                                       value="{{ old('tanggal_selesai') }}" min="{{ now()->toDateString() }}"
                                       class="form-control @error('tanggal_selesai') is-invalid @enderror" required>
                                @error('tanggal_selesai') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="alasan">Alasan</label>
                            <textarea name="alasan" id="alasan" rows="3"
                                      class="form-control @error('alasan') is-invalid @enderror"
                                      placeholder="Tulis alasan…">{{ old('alasan') }}</textarea>
                            @error('alasan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="surat">Surat Keterangan ( jpg / png / pdf, maks 2 MB )</label>
                            <input type="file" name="surat" id="surat" accept=".jpg,.jpeg,.png,.pdf"
                                   class="form-control @error('surat') is-invalid @enderror">
                            @error('surat') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Kirim Pengajuan</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h2 class="h6 mb-3">Riwayat Pengajuan</h2>

                    @if($data->count())
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th>Jenis</th>
                                        <th>Periode</th>
                                        <th>Alasan</th>
                                        <th>Status</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($data as $izin)
                                        <tr>
                                            <td><span class="badge bg-secondary">{{ $izin->labelJenis() }}</span></td>
                                            <td class="small">
                                                {{ $izin->tanggal_mulai->format('d/m/Y') }} &ndash;
                                                {{ $izin->tanggal_selesai->format('d/m/Y') }}
                                            </td>
                                            <td class="small">
                                                {{ \Illuminate\Support\Str::limit($izin->alasan, 60) }}
                                                @if($izin->catatan_admin)
                                                    <div class="text-muted">Admin: {{ $izin->catatan_admin }}</div>
                                                @endif
                                            </td>
                                            <td>
                                                @php($badge = ['menunggu' => 'bg-warning text-dark', 'disetujui' => 'bg-success', 'ditolak' => 'bg-danger'])
                                                <span class="badge {{ $badge[$izin->status] ?? 'bg-secondary' }}">{{ ucfirst($izin->status) }}</span>
                                            </td>
                                            <td class="text-end">
                                                @if($izin->status === 'menunggu')
                                                    <form action="{{ route('izin.destroy', $izin) }}" method="POST"
                                                          data-confirm="Batalkan pengajuan ini?">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button class="btn btn-outline-danger btn-sm">Batal</button>
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
                        <p class="text-muted small mb-0">Belum ada pengajuan.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
