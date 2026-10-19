@php($aksi = isset($lokasi) ? route('admin.lokasi.update', $lokasi) : route('admin.lokasi.store'))
@php($metode = isset($lokasi) ? 'PUT' : 'POST')
@php($l = $lokasi ?? null)

<form action="{{ $aksi }}" method="POST">
    @csrf
    @method($metode)

    <div class="row g-3">
        <div class="col-sm-6">
            <label class="form-label" for="nama">Nama Lokasi / Kantor <span class="text-danger">*</span></label>
            <input type="text" name="nama" id="nama" value="{{ old('nama', $l?->nama) }}"
                   class="form-control @error('nama') is-invalid @enderror" required>
            @error('nama') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="col-sm-6">
            <label class="form-label" for="status">Status</label>
            <select name="status" id="status" class="form-select">
                <option value="aktif" @selected(old('status', $l?->status) === 'aktif')>Aktif</option>
                <option value="nonaktif" @selected(old('status', $l?->status) === 'nonaktif')>Nonaktif</option>
            </select>
        </div>

        <div class="col-12">
            <label class="form-label" for="alamat">Alamat</label>
            <textarea name="alamat" id="alamat" rows="2"
                      class="form-control @error('alamat') is-invalid @enderror">{{ old('alamat', $l?->alamat) }}</textarea>
            @error('alamat') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="col-sm-6">
            <label class="form-label" for="latitude">Latitude <span class="text-danger">*</span></label>
            <input type="text" name="latitude" id="latitude" value="{{ old('latitude', $l?->latitude) }}"
                   class="form-control font-monospace @error('latitude') is-invalid @enderror"
                   placeholder="-8.0644000" required>
            @error('latitude') <div class="invalid-feedback">{{ $message }}</div> @enderror
            <div class="form-text">Contoh kantor pusat: <code>-8.0644000</code></div>
        </div>

        <div class="col-sm-6">
            <label class="form-label" for="longitude">Longitude <span class="text-danger">*</span></label>
            <input type="text" name="longitude" id="longitude" value="{{ old('longitude', $l?->longitude) }}"
                   class="form-control font-monospace @error('longitude') is-invalid @enderror"
                   placeholder="112.1700000" required>
            @error('longitude') <div class="invalid-feedback">{{ $message }}</div> @enderror
            <div class="form-text">Contoh kantor pusat: <code>112.1700000</code></div>
        </div>

        <div class="col-sm-4">
            <label class="form-label" for="radius">Radius Absensi ( meter ) <span class="text-danger">*</span></label>
            <input type="number" name="radius" id="radius" min="10" max="5000"
                   value="{{ old('radius', $l?->radius ?? 150) }}"
                   class="form-control @error('radius') is-invalid @enderror" required>
            @error('radius') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="col-sm-4">
            <label class="form-label" for="jam_masuk">Jam Masuk <span class="text-danger">*</span></label>
            <input type="time" name="jam_masuk" id="jam_masuk"
                   value="{{ old('jam_masuk', $l ? substr($l->jam_masuk, 0, 5) : '08:00') }}"
                   class="form-control @error('jam_masuk') is-invalid @enderror" required>
            @error('jam_masuk') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="col-sm-4">
            <label class="form-label" for="jam_pulang">Jam Pulang <span class="text-danger">*</span></label>
            <input type="time" name="jam_pulang" id="jam_pulang"
                   value="{{ old('jam_pulang', $l ? substr($l->jam_pulang, 0, 5) : '17:00') }}"
                   class="form-control @error('jam_pulang') is-invalid @enderror" required>
            @error('jam_pulang') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="col-sm-4">
            <label class="form-label" for="toleransi">Toleransi Terlambat ( menit )</label>
            <input type="number" name="toleransi" id="toleransi" min="0" max="180"
                   value="{{ old('toleransi', $l?->toleransi ?? 15) }}"
                   class="form-control @error('toleransi') is-invalid @enderror">
            @error('toleransi') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="col-sm-8 d-flex align-items-center">
            <div class="form-check mt-4">
                <input type="checkbox" class="form-check-input" name="is_default" id="is_default" value="1"
                       @checked(old('is_default', $l?->is_default))>
                <label class="form-check-label" for="is_default">
                    Jadikan lokasi utama ( dipakai untuk QR absensi &amp; dashboard )
                </label>
            </div>
        </div>
    </div>

    <hr class="my-4">

    <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary">💾 Simpan</button>
        <a href="{{ route('admin.lokasi.index') }}" class="btn btn-outline-secondary">Batal</a>
    </div>
</form>

<div class="alert alert-info small mt-4 mb-0">
    💡 <strong>Cara mendapatkan koordinat:</strong> buka
    <a href="https://www.google.com/maps" target="_blank" rel="noopener">Google Maps</a>,
    klik titik lokasi kantor, lalu salin angka <em>lintang ( latitude )</em> dan
    <em>bujur ( longitude )</em> ke kolom di atas.
</div>
