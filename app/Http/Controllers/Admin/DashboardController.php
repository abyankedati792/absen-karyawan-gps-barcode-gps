<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\Karyawan;
use App\Models\LogScan;
use App\Models\PengajuanIzin;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $hariIni = now()->toDateString();
        $bulanIni = now()->format('Y-m');

        $absensiHariIni = Absensi::where('tanggal', $hariIni)->with('karyawan')->get();

        $totalKaryawan = Karyawan::where('status', 'aktif')->count();
        $hadirHariIni = $absensiHariIni->filter(fn ($a) => in_array($a->status_masuk, ['hadir', 'terlambat'], true))->count();
        $terlambatHariIni = $absensiHariIni->where('status_masuk', 'terlambat')->count();
        $izinHariIni = $absensiHariIni->whereIn('status_masuk', ['izin', 'sakit', 'cuti'])->count();
        $belumAbsen = max(0, $totalKaryawan - $hadirHariIni - $izinHariIni);

        // Grafik 7 hari terakhir
        $grafik = Absensi::whereRaw('tanggal >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)')
            ->get(['tanggal', 'status_masuk'])
            ->groupBy(fn ($a) => $a->tanggal->format('Y-m-d'));

        $labels = [];
        $seriHadir = [];
        $seriTerlambat = [];

        for ($i = 6; $i >= 0; $i--) {
            $tgl = now()->subDays($i)->format('Y-m-d');
            $rows = $grafik->get($tgl, collect());
            $labels[] = \Illuminate\Support\Carbon::parse($tgl)->format('d/m');
            $seriHadir[] = $rows->where('status_masuk', 'hadir')->count();
            $seriTerlambat[] = $rows->where('status_masuk', 'terlambat')->count();
        }

        $rekapBulan = Absensi::whereRaw('DATE_FORMAT(tanggal, "%Y-%m") = ?', [$bulanIni]);

        return view('admin.dashboard', [
            'judul' => 'Dashboard Admin',
            'totalKaryawan' => $totalKaryawan,
            'hadirHariIni' => $hadirHariIni,
            'terlambatHariIni' => $terlambatHariIni,
            'izinHariIni' => $izinHariIni,
            'belumAbsen' => $belumAbsen,
            'totalHadirBulan' => (clone $rekapBulan)->whereIn('status_masuk', ['hadir', 'terlambat'])->count(),
            'totalTerlambatBulan' => (clone $rekapBulan)->where('status_masuk', 'terlambat')->count(),
            'izinMenunggu' => PengajuanIzin::where('status', 'menunggu')->count(),
            'totalScanGagal' => LogScan::where('hasil', 'gagal')->where('tanggal', $hariIni)->count(),
            'absensiTerbaru' => Absensi::with('karyawan')
                ->where('tanggal', $hariIni)
                ->orderBy('updated_at', 'desc')
                ->limit(10)
                ->get(),
            'labels' => $labels,
            'seriHadir' => $seriHadir,
            'seriTerlambat' => $seriTerlambat,
        ]);
    }
}
