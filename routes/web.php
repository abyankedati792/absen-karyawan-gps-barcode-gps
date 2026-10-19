<?php

use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\Admin;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\IzinController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route(Auth::check() ? 'dashboard' : 'login');
})->name('beranda');

/*
|--------------------------------------------------------------------------
| Halaman publik
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])
        ->middleware('throttle:5,1')
        ->name('login.attempt');
});

/*
|--------------------------------------------------------------------------
| Semua user login
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profil', [DashboardController::class, 'profil'])->name('profil');
    Route::put('/profil', [DashboardController::class, 'updateProfil'])->name('profil.update');

    Route::get('/absensi', [AbsensiController::class, 'index'])->name('absensi');
    Route::post('/absensi', [AbsensiController::class, 'store'])->name('absensi.store');
    Route::get('/absensi/riwayat', [AbsensiController::class, 'riwayat'])->name('absensi.riwayat');

    Route::get('/izin', [IzinController::class, 'index'])->name('izin.index');
    Route::post('/izin', [IzinController::class, 'store'])->name('izin.store');
    Route::delete('/izin/{izin}', [IzinController::class, 'destroy'])->name('izin.destroy');
});

/*
|--------------------------------------------------------------------------
| Khusus admin
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [Admin\DashboardController::class, 'index'])->name('dashboard');

    Route::get('/karyawan', [Admin\KaryawanController::class, 'index'])->name('karyawan.index');
    Route::get('/karyawan/create', [Admin\KaryawanController::class, 'create'])->name('karyawan.create');
    Route::post('/karyawan', [Admin\KaryawanController::class, 'store'])->name('karyawan.store');
    Route::get('/karyawan/{karyawan}/edit', [Admin\KaryawanController::class, 'edit'])->name('karyawan.edit');
    Route::put('/karyawan/{karyawan}', [Admin\KaryawanController::class, 'update'])->name('karyawan.update');
    Route::delete('/karyawan/{karyawan}', [Admin\KaryawanController::class, 'destroy'])->name('karyawan.destroy');

    Route::get('/lokasi', [Admin\LokasiKantorController::class, 'index'])->name('lokasi.index');
    Route::get('/lokasi/create', [Admin\LokasiKantorController::class, 'create'])->name('lokasi.create');
    Route::post('/lokasi', [Admin\LokasiKantorController::class, 'store'])->name('lokasi.store');
    Route::get('/lokasi/{lokasi}/edit', [Admin\LokasiKantorController::class, 'edit'])->name('lokasi.edit');
    Route::put('/lokasi/{lokasi}', [Admin\LokasiKantorController::class, 'update'])->name('lokasi.update');
    Route::delete('/lokasi/{lokasi}', [Admin\LokasiKantorController::class, 'destroy'])->name('lokasi.destroy');

    Route::get('/absensi', [Admin\AbsensiController::class, 'index'])->name('absensi.index');
    Route::get('/absensi/export', [Admin\AbsensiController::class, 'export'])->name('absensi.export');

    Route::get('/qr', [Admin\QrController::class, 'index'])->name('qr.index');
    Route::get('/qr/token', [Admin\QrController::class, 'token'])->name('qr.token');

    Route::get('/izin', [Admin\IzinController::class, 'index'])->name('izin.index');
    Route::patch('/izin/{izin}', [Admin\IzinController::class, 'update'])->name('izin.update');
    Route::delete('/izin/{izin}', [Admin\IzinController::class, 'destroy'])->name('izin.destroy');

    Route::get('/pengaturan', [Admin\PengaturanController::class, 'edit'])->name('pengaturan.edit');
    Route::put('/pengaturan', [Admin\PengaturanController::class, 'update'])->name('pengaturan.update');
});
