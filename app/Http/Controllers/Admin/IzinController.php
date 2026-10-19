<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\Karyawan;
use App\Models\PengajuanIzin;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class IzinController extends Controller
{
    public function index(Request $request): View
    {
        $status = (string) $request->query('status', 'menunggu');

        $data = PengajuanIzin::with('karyawan')
            ->when($status !== 'semua', fn ($q) => $q->where('status', $status))
            ->orderByDesc('created_at')
            ->paginate(15)->withQueryString();

        return view('admin.izin.index', [
            'judul' => 'Pengajuan Izin / Sakit / Cuti',
            'data' => $data,
            'status' => $status,
            'jumlahMenunggu' => PengajuanIzin::where('status', 'menunggu')->count(),
        ]);
    }

    public function update(Request $request, PengajuanIzin $izin): RedirectResponse
    {
        $request->validate([
            'aksi' => ['required', 'in:setujui,tolak'],
            'catatan_admin' => ['nullable', 'string', 'max:255'],
        ]);

        $aksi = $request->input('aksi');
        $status = $aksi === 'setujui' ? 'disetujui' : 'ditolak';

        $izin->update([
            'status' => $status,
            'catatan_admin' => $request->input('catatan_admin'),
            'disetujui_oleh' => $request->user()->id,
        ]);

        if ($aksi === 'setujui') {
            $this->terapkanKeAbsensi($izin);
            $pesan = 'Pengajuan disetujui dan sudah diterapkan ke rekap absensi.';
        } else {
            $pesan = 'Pengajuan ditolak.';
        }

        return back()->with('success', $pesan);
    }

    /**
     * Tandai baris absensi pada rentang tanggal yang diajukan ( bila belum absen masuk ).
     */
    private function terapkanKeAbsensi(PengajuanIzin $izin): void
    {
        $tanggal = Carbon::parse($izin->tanggal_mulai);
        $selesai = Carbon::parse($izin->tanggal_selesai);

        while ($tanggal->lte($selesai)) {
            Absensi::updateOrCreate(
                [
                    'karyawan_id' => $izin->karyawan_id,
                    'tanggal' => $tanggal->toDateString(),
                ],
                [
                    'lokasi_kantor_id' => optional(\App\Models\LokasiKantor::default())->id,
                    'status_masuk' => $izin->jenis,
                    'jam_masuk' => null,
                    'jam_pulang' => null,
                    'status_pulang' => null,
                    'catatan' => 'Pengajuan '.ucfirst($izin->jenis).' disetujui admin',
                ]
            );

            $tanggal->addDay();
        }
    }

    public function destroy(PengajuanIzin $izin): RedirectResponse
    {
        $izin->delete();

        return back()->with('success', 'Pengajuan berhasil dihapus.');
    }
}
