@extends('layouts.guest')

@section('content')
    <h2 class="h5 mb-3">Masuk ke Akun Anda</h2>

    <form action="{{ route('login.attempt') }}" method="POST" autocomplete="off">
        @csrf

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" name="email" id="email" value="{{ old('email') }}"
                   class="form-control @error('email') is-invalid @enderror"
                   placeholder="nama@absensi.test" required autofocus>
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" name="password" id="password"
                   class="form-control @error('password') is-invalid @enderror"
                   placeholder="••••••••" required>
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="form-check">
                <input type="checkbox" class="form-check-input" name="remember" id="remember" value="1">
                <label class="form-check-label small" for="remember">Ingat saya</label>
            </div>
        </div>

        <button type="submit" class="btn btn-primary w-100">🔓 Masuk</button>
    </form>

    <hr>

    <div class="small text-muted">
        <div class="fw-semibold mb-1">Akun demo:</div>
        <div>Admin &mdash; <code>admin@absensi.test</code> / <code>admin123</code></div>
        <div>Karyawan &mdash; <code>andi@absensi.test</code> / <code>karyawan123</code></div>
    </div>
@endsection
