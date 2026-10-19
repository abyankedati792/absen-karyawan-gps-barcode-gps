@extends('layouts.app')

@section('content')
    <div class="row g-3">
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h2 class="h6 mb-3">Pengaturan Umum</h2>

                    <form action="{{ route('admin.pengaturan.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            <div class="col-sm-6">
                                <label class="form-label" for="nama_aplikasi">Nama Aplikasi</label>
                                <input type="text" name="nama_aplikasi" id="nama_aplikasi"
                                       value="{{ old('nama_aplikasi', $pengaturan['nama_aplikasi'] ?? 'Absensi Karyawan') }}"
                                       class="form-control @error('nama_aplikasi') is-invalid @enderror" required>
                                @error('nama_aplikasi') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-sm-6">
                                <label class="form-label" for="nama_perusahaan">Nama Perusahaan</label>
                                <input type="text" name="nama_perusahaan" id="nama_perusahaan"
                                       value="{{ old('nama_perusahaan', $pengaturan['nama_perusahaan'] ?? '') }}"
                                       class="form-control @error('nama_perusahaan') is-invalid @enderror" required>
                                @error('nama_perusahaan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label" for="alamat_kantor">Alamat Kantor</label>
                                <input type="text" name="alamat_kantor" id="alamat_kantor"
                                       value="{{ old('alamat_kantor', $pengaturan['alamat_kantor'] ?? '') }}"
                                       class="form-control @error('alamat_kantor') is-invalid @enderror">
                                @error('alamat_kantor') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-sm-4">
                                <label class="form-label" for="radius_default">Radius Default ( meter )</label>
                                <input type="number" name="radius_default" id="radius_default" min="10" max="5000"
                                       value="{{ old('radius_default', $pengaturan['radius_default'] ?? 150) }}"
                                       class="form-control @error('radius_default') is-invalid @enderror" required>
                                @error('radius_default') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-sm-4">
                                <label class="form-label" for="menit_qr">Masa Berlaku QR ( menit )</label>
                                <input type="number" name="menit_qr" id="menit_qr" min="1" max="60"
                                       value="{{ old('menit_qr', $pengaturan['menit_qr'] ?? 5) }}"
                                       class="form-control @error('menit_qr') is-invalid @enderror" required>
                                @error('menit_qr') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                <div class="form-text">QR otomatis berganti tiap rentang ini.</div>
                            </div>

                            <div class="col-sm-4">
                                <label class="form-label" for="aktifkan_foto">Wajib Foto Saat Absen</label>
                                <select name="aktifkan_foto" id="aktifkan_foto" class="form-select">
                                    <option value="0" @selected(($pengaturan['aktifkan_foto'] ?? '0') === '0')>Tidak</option>
                                    <option value="1" @selected(($pengaturan['aktifkan_foto'] ?? '0') === '1')>Ya, wajib foto</option>
                                </select>
                            </div>

                            <div class="col-12">
                                <hr>
                                <label class="form-label">Logo Perusahaan</label>
                                <div class="d-flex align-items-center gap-3 flex-wrap">
                                    <div class="logo-preview">
                                        <img src="{{ asset($logoUrl ?? 'img/logo.svg') }}" alt="Logo" id="previewLogo">
                                    </div>
                                    <div class="flex-grow-1" style="min-width:250px;">
                                        <input type="file" name="logo" id="logo" accept=".jpg,.jpeg,.png,.webp,.svg"
                                               class="form-control form-control-sm @error('logo') is-invalid @enderror">
                                        @error('logo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        <div class="form-text">
                                            Format jpg / png / webp / svg, maksimal 2 MB &mdash; misal
                                            <code>logo-shelter-indonesia.png</code>. Kosongkan bila tidak diganti.
                                        </div>
                                        <div class="form-check mt-1">
                                            <input type="checkbox" name="hapus_logo" id="hapus_logo" value="1" class="form-check-input">
                                            <label class="form-check-label small" for="hapus_logo">
                                                Kembalikan ke logo bawaan aplikasi
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <button type="submit" class="btn btn-primary">💾 Simpan Pengaturan</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-body">
                    <h2 class="h6 mb-3">Akun Login</h2>
                    <p class="small text-muted mb-2">
                        Password login tidak bisa diubah dari menu ini. Ubah password admin melalui
                        <a href="{{ route('profil') }}">Profil</a>, dan password karyawan melalui menu
                        <a href="{{ route('admin.karyawan.index') }}">Data Karyawan</a>.
                    </p>
                    <div class="small">
                        <div class="d-flex justify-content-between border-bottom py-1">
                            <span class="text-muted">Admin demo</span><code>admin@absensi.test</code>
                        </div>
                        <div class="d-flex justify-content-between py-1">
                            <span class="text-muted">Karyawan demo</span><code>andi@absensi.test</code>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h2 class="h6 mb-3">Tabel Database</h2>
                    <p class="small text-muted mb-2">
                        Struktur &amp; data awal tersimpan di
                        <code>database/db_absensi_karyawan.sql</code>.
                    </p>
                    <ul class="small mb-0 ps-3">
                        <li>karyawan</li>
                        <li>lokasi_kantor</li>
                        <li>absensi</li>
                        <li>qr_token</li>
                        <li>log_scan</li>
                        <li>pengajuan_izin</li>
                        <li>pengaturan</li>
                        <li>sessions / cache / jobs</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Preview logo sebelum disimpan
        (function () {
            const input = document.getElementById('logo');
            const preview = document.getElementById('previewLogo');
            if (!input || !preview) return;
            input.addEventListener('change', function () {
                const file = this.files && this.files[0];
                if (!file) return;
                preview.src = URL.createObjectURL(file);
            });
        })();
    </script>
@endpush
