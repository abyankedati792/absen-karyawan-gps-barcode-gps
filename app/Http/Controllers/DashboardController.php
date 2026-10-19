<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Karyawan;
use App\Models\PengajuanIzin;
use App\Models\Pengaturan;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->isAdmin()) {
            return app(Admin\DashboardController::class)->index($request);
        }

        $hariIni = now()->toDateString();
        $bulanIni = now()->format('Y-m');

        $absensiHariIni = Absensi::where('karyawan_id', $user->id)
            ->where('tanggal', $hariIni)
            ->first();

        $rekapBulan = Absensi::where('karyawan_id', $user->id)
            ->whereRaw('DATE_FORMAT(tanggal, "%Y-%m") = ?', [$bulanIni])
            ->get();

        $kantor = \App\Models\LokasiKantor::default();

        return view('dashboard', [
            'user' => $user,
            'kantor' => $kantor,
            'absensiHariIni' => $absensiHariIni,
            'rekapBulan' => $rekapBulan,
            'totalHadir' => $rekapBulan->whereIn('status_masuk', ['hadir', 'terlambat'])->count(),
            'totalTerlambat' => $rekapBulan->where('status_masuk', 'terlambat')->count(),
            'totalIzin' => $rekapBulan->whereIn('status_masuk', ['izin', 'sakit', 'cuti'])->count(),
            'totalAlfa' => $rekapBulan->where('status_masuk', 'alpha')->count(),
            'pengajuanMenunggu' => PengajuanIzin::where('karyawan_id', $user->id)
                ->where('status', 'menunggu')->count(),
            'pengaturan' => Pengaturan::semua(),
            'judul' => 'Dashboard Karyawan',
        ]);
    }

    public function profil(Request $request)
    {
        return view('profil', [
            'user' => $request->user(),
            'judul' => 'Profil Saya',
        ]);
    }

    public function updateProfil(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'nama' => ['required', 'string', 'max:100'],
            'no_hp' => ['nullable', 'string', 'max:20'],
            'alamat' => ['nullable', 'string', 'max:255'],
            'password' => ['nullable', 'string', 'min:6', 'confirmed'],
        ], [
            'password.min' => 'Password minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak sama.',
        ]);

        if (blank($data['password'] ?? null)) {
            unset($data['password']);
        }

        $user->update($data);

        return redirect()->route('profil')->with('success', 'Profil berhasil diperbarui.');
    }
}
