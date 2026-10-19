@extends('layouts.app')

@section('content')
    <div class="row g-3">
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <span class="avatar mx-auto d-inline-flex" style="width:84px;height:84px;font-size:2rem;">
                        {{ strtoupper(substr($user->nama, 0, 1)) }}
                    </span>
                    <h2 class="h5 mt-3 mb-1">{{ $user->nama }}</h2>
                    <p class="text-muted small mb-2">{{ $user->jabatan ?? 'Karyawan' }} &middot; {{ $user->departemen ?? '-' }}</p>
                    <span class="badge {{ $user->isAdmin() ? 'bg-primary' : 'bg-secondary' }}">{{ ucfirst($user->role) }}</span>

                    <hr>

                    <div class="text-start small">
                        <div class="d-flex justify-content-between border-bottom py-2">
                            <span class="text-muted">NIP</span><strong>{{ $user->nip }}</strong>
                        </div>
                        <div class="d-flex justify-content-between border-bottom py-2">
                            <span class="text-muted">Email</span><strong>{{ $user->email }}</strong>
                        </div>
                        <div class="d-flex justify-content-between border-bottom py-2">
                            <span class="text-muted">No. HP</span><strong>{{ $user->no_hp ?? '-' }}</strong>
                        </div>
                        <div class="d-flex justify-content-between py-2">
                            <span class="text-muted">Status</span>
                            <span class="badge {{ $user->isAktif() ? 'bg-success' : 'bg-danger' }}">{{ ucfirst($user->status) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h2 class="h6 mb-3">Ubah Profil &amp; Password</h2>

                    <form action="{{ route('profil.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label" for="nama">Nama Lengkap</label>
                            <input type="text" name="nama" id="nama" value="{{ old('nama', $user->nama) }}"
                                   class="form-control @error('nama') is-invalid @enderror" required>
                            @error('nama') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-sm-6">
                                <label class="form-label" for="no_hp">No. HP</label>
                                <input type="text" name="no_hp" id="no_hp" value="{{ old('no_hp', $user->no_hp) }}"
                                       class="form-control @error('no_hp') is-invalid @enderror">
                                @error('no_hp') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label" for="email">Email ( login )</label>
                                <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}"
                                       class="form-control @error('email') is-invalid @enderror" readonly>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="alamat">Alamat</label>
                            <textarea name="alamat" id="alamat" rows="2"
                                      class="form-control @error('alamat') is-invalid @enderror">{{ old('alamat', $user->alamat) }}</textarea>
                            @error('alamat') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-sm-6">
                                <label class="form-label" for="password">Password Baru</label>
                                <input type="password" name="password" id="password"
                                       class="form-control @error('password') is-invalid @enderror"
                                       placeholder="Kosongkan bila tidak diganti">
                                @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label" for="password_confirmation">Ulangi Password</label>
                                <input type="password" name="password_confirmation" id="password_confirmation"
                                       class="form-control" placeholder="Kosongkan bila tidak diganti">
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">💾 Simpan Perubahan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
