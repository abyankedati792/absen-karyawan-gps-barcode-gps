@php($aksi = isset($karyawan) ? route('admin.karyawan.update', $karyawan) : route('admin.karyawan.store'))
@php($metode = isset($karyawan) ? 'PUT' : 'POST')
@php($k = $karyawan ?? null)

<form action="{{ $aksi }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method($metode)

    <div class="row g-3">
        <div class="col-sm-6">
            <label class="form-label" for="nip">NIP <span class="text-danger">*</span></label>
            <input type="text" name="nip" id="nip" value="{{ old('nip', $k?->nip) }}"
                   class="form-control @error('nip') is-invalid @enderror" required>
            @error('nip') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="col-sm-6">
            <label class="form-label" for="nama">Nama Lengkap <span class="text-danger">*</span></label>
            <input type="text" name="nama" id="nama" value="{{ old('nama', $k?->nama) }}"
                   class="form-control @error('nama') is-invalid @enderror" required>
            @error('nama') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="col-sm-6">
            <label class="form-label" for="email">Email <span class="text-danger">*</span></label>
            <input type="email" name="email" id="email" value="{{ old('email', $k?->email) }}"
                   class="form-control @error('email') is-invalid @enderror" required>
            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="col-sm-6">
            <label class="form-label" for="password">
                Password {{ $k ? '<span class="text-muted">( kosongkan bila tetap )</span>' : '<span class="text-danger">*</span>' }}
            </label>
            <input type="password" name="password" id="password"
                   class="form-control @error('password') is-invalid @enderror"
                   {{ $k ? '' : 'required' }} minlength="6">
            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="col-sm-6">
            <label class="form-label" for="no_hp">No. HP</label>
            <input type="text" name="no_hp" id="no_hp" value="{{ old('no_hp', $k?->no_hp) }}"
                   class="form-control @error('no_hp') is-invalid @enderror">
            @error('no_hp') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="col-sm-6">
            <label class="form-label" for="jabatan">Jabatan</label>
            <input type="text" name="jabatan" id="jabatan" value="{{ old('jabatan', $k?->jabatan) }}"
                   class="form-control @error('jabatan') is-invalid @enderror">
            @error('jabatan') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="col-sm-6">
            <label class="form-label" for="departemen">Departemen / Bagian</label>
            <input type="text" name="departemen" id="departemen" value="{{ old('departemen', $k?->departemen) }}"
                   class="form-control @error('departemen') is-invalid @enderror">
            @error('departemen') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="col-sm-6">
            <label class="form-label" for="role">Role <span class="text-danger">*</span></label>
            <select name="role" id="role" class="form-select @error('role') is-invalid @enderror">
                <option value="karyawan" @selected(old('role', $k?->role) === 'karyawan')>Karyawan</option>
                <option value="admin" @selected(old('role', $k?->role) === 'admin')>Admin</option>
            </select>
            @error('role') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="col-sm-6">
            <label class="form-label" for="status">Status <span class="text-danger">*</span></label>
            <select name="status" id="status" class="form-select @error('status') is-invalid @enderror">
                <option value="aktif" @selected(old('status', $k?->status) === 'aktif')>Aktif</option>
                <option value="nonaktif" @selected(old('status', $k?->status) === 'nonaktif')>Nonaktif</option>
            </select>
            @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="col-12">
            <label class="form-label" for="alamat">Alamat</label>
            <textarea name="alamat" id="alamat" rows="2"
                      class="form-control @error('alamat') is-invalid @enderror">{{ old('alamat', $k?->alamat) }}</textarea>
            @error('alamat') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="col-sm-6">
            <label class="form-label" for="foto">Foto ( jpg / png, maks 2 MB )</label>
            <input type="file" name="foto" id="foto" accept="image/*"
                   class="form-control @error('foto') is-invalid @enderror">
            @error('foto') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        @if($k?->foto_url)
            <div class="col-sm-6 text-center">
                <img src="{{ $k->foto_url }}" alt="{{ $k->nama }}" class="img-thumbnail" style="max-height:110px">
            </div>
        @endif
    </div>

    <hr class="my-4">

    <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary">💾 Simpan</button>
        <a href="{{ route('admin.karyawan.index') }}" class="btn btn-outline-secondary">Batal</a>
    </div>
</form>
