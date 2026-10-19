<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\Karyawan;
use App\Models\LokasiKantor;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AbsensiController extends Controller
{
    /**
     * Rekap absensi semua karyawan + filter.
     */
    public function index(Request $request): View
    {
        [$query, $filter] = $this->queryRekap($request);

        $ringkas = (clone $query)
            ->selectRaw("COUNT(*) AS total")
            ->selectRaw("SUM(status_masuk IN ('hadir','terlambat')) AS hadir")
            ->selectRaw("SUM(status_masuk = 'terlambat') AS terlambat")
            ->selectRaw("SUM(status_masuk IN ('izin','sakit','cuti')) AS izin")
            ->toBase()
            ->first();

        $data = $query->paginate(25)->withQueryString();

        return view('admin.absensi.index', [
            'judul' => 'Rekap Absensi',
            'data' => $data,
            'filter' => $filter,
            'karyawan' => Karyawan::orderBy('nama')->get(['id', 'nama', 'nip']),
            'lokasi' => LokasiKantor::orderBy('nama')->get(['id', 'nama']),
            'jumlahTotal' => (int) ($ringkas->total ?? 0),
            'jumlahHadir' => (int) ($ringkas->hadir ?? 0),
            'jumlahTerlambat' => (int) ($ringkas->terlambat ?? 0),
            'jumlahIzin' => (int) ($ringkas->izin ?? 0),
        ]);
    }

    /**
     * Export CSV ( filter yang sama dengan rekap ).
     */
    public function export(Request $request): StreamedResponse
    {
        [$query] = $this->queryRekap($request);

        $namaFile = 'rekap-absensi-'.now()->format('Ymd-His').'.csv';

        return response()->streamDownload(function () use ($query) {
            $out = fopen('php://output', 'w');
            // BOM agar excel membaca UTF-8
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, [
                'Tanggal', 'NIP', 'Nama', 'Departemen', 'Lokasi Kantor',
                'Jam Masuk', 'Jam Pulang', 'Status Masuk', 'Jarak Masuk (m)',
                'Jarak Pulang (m)', 'Alamat', 'Catatan',
            ], ';');

            $query->orderBy('tanggal')->orderBy('karyawan_id')->chunk(500, function ($rows) use ($out) {
                foreach ($rows as $a) {
                    fputcsv($out, [
                        $a->tanggal->format('d/m/Y'),
                        $a->karyawan?->nip,
                        $a->karyawan?->nama,
                        $a->karyawan?->departemen,
                        $a->lokasiKantor?->nama,
                        $a->jam_masuk?->format('H:i'),
                        $a->jam_pulang?->format('H:i'),
                        ucfirst($a->status_masuk),
                        $a->jarak_masuk,
                        $a->jarak_pulang,
                        $a->alamat_masuk,
                        $a->catatan,
                    ], ';');
                }
            });

            fclose($out);
        }, $namaFile, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    /**
     * @return array{0: \Illuminate\Database\Eloquent\Builder, 1: array<string, string>}
     */
    private function queryRekap(Request $request): array
    {
        $filter = [
            'mulai' => $request->query('mulai', now()->startOfMonth()->format('Y-m-d')),
            'sampai' => $request->query('sampai', now()->format('Y-m-d')),
            'karyawan_id' => (string) $request->query('karyawan_id', ''),
            'lokasi_id' => (string) $request->query('lokasi_id', ''),
            'status' => (string) $request->query('status', ''),
            'q' => trim((string) $request->query('q', '')),
        ];

        if (! preg_match('/^\d{4}-\d{2}-\d{2}$/', $filter['mulai'])) {
            $filter['mulai'] = now()->startOfMonth()->format('Y-m-d');
        }
        if (! preg_match('/^\d{4}-\d{2}-\d{2}$/', $filter['sampai'])) {
            $filter['sampai'] = now()->format('Y-m-d');
        }

        $query = Absensi::query()
            ->with(['karyawan', 'lokasiKantor'])
            ->whereBetween('tanggal', [$filter['mulai'], $filter['sampai']])
            ->when($filter['karyawan_id'] !== '', fn ($q) => $q->where('karyawan_id', $filter['karyawan_id']))
            ->when($filter['lokasi_id'] !== '', fn ($q) => $q->where('lokasi_kantor_id', $filter['lokasi_id']))
            ->when($filter['status'] !== '', fn ($q) => $q->where('status_masuk', $filter['status']))
            ->when($filter['q'] !== '', fn ($q) => $q->whereHas('karyawan', fn ($k) => $k
                ->where(fn ($w) => $w->where('nama', 'like', "%{$filter['q']}%")
                    ->orWhere('nip', 'like', "%{$filter['q']}%"))));

        return [$query, $filter];
    }
}
