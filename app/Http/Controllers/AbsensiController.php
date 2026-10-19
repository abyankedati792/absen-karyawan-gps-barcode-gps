<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\LogScan;
use App\Models\LokasiKantor;
use App\Models\Pengaturan;
use App\Models\QrToken;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AbsensiController extends Controller
{
    /**
     * Halaman absensi: kamera QR + GPS.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        return view('absensi.index', [
            'user' => $user,
            'kantor' => LokasiKantor::default(),
            'absensi' => Absensi::where('karyawan_id', $user->id)
                ->where('tanggal', now()->toDateString())
                ->first(),
            'pengaturan' => Pengaturan::semua(),
            'judul' => 'Absensi QR + GPS',
        ]);
    }

    /**
     * Proses absen masuk / pulang.
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'tipe' => ['required', 'in:masuk,pulang'],
            'qrcode' => ['required', 'string', 'max:255'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'alamat' => ['nullable', 'string', 'max:255'],
            'foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ], [
            'qrcode.required' => 'QR Code belum dipindai.',
            'latitude.required' => 'Lokasi GPS belum didapatkan. Aktifkan GPS lalu coba lagi.',
            'longitude.required' => 'Lokasi GPS belum didapatkan. Aktifkan GPS lalu coba lagi.',
            'foto.image' => 'Foto harus berupa gambar.',
            'foto.max' => 'Ukuran foto maksimal 2 MB.',
        ]);

        $user = $request->user();
        $lat = (float) $data['latitude'];
        $lng = (float) $data['longitude'];
        $alamat = $data['alamat'] ?? null;

        // ---------------------------------------------------------------
        // 1. Validasi QR Code
        // ---------------------------------------------------------------
        [$qr, $pesanError] = QrToken::validasi($data['qrcode']);

        if ($pesanError !== null) {
            $this->catatLog($user, null, $data, $lat, $lng, 'gagal', $pesanError, $request);

            throw ValidationException::withMessages(['qrcode' => $pesanError]);
        }

        $kantor = $qr->lokasiKantor;

        if (! $kantor || ! $kantor->isAktif()) {
            $pesan = 'Lokasi kantor pada QR tidak aktif. Hubungi administrator.';
            $this->catatLog($user, $qr, $data, $lat, $lng, 'gagal', $pesan, $request);

            throw ValidationException::withMessages(['qrcode' => $pesan]);
        }

        // ---------------------------------------------------------------
        // 2. Validasi jarak GPS terhadap radius kantor
        // ---------------------------------------------------------------
        $jarak = (int) round($kantor->jarakDari($lat, $lng));

        if ($jarak > (int) $kantor->radius) {
            $pesan = sprintf(
                'Anda berada di luar area absensi: jarak %s m dari %s (radius absensi %s m).',
                number_format($jarak),
                $kantor->nama,
                number_format((int) $kantor->radius)
            );
            $this->catatLog($user, $qr, $data, $lat, $lng, 'gagal', $pesan, $request);

            throw ValidationException::withMessages(['lokasi' => $pesan]);
        }

        // ---------------------------------------------------------------
        // 3. Foto (opsional sesuai pengaturan)
        // ---------------------------------------------------------------
        $wajibFoto = Pengaturan::get('aktifkan_foto', '0') === '1';
        $fotoPath = null;

        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('foto_absensi', 'public');
        } elseif ($wajibFoto) {
            $pesan = 'Foto wajib diunggah saat absen ( diatur admin di menu Pengaturan ).';
            $this->catatLog($user, $qr, $data, $lat, $lng, 'gagal', $pesan, $request);

            throw ValidationException::withMessages(['foto' => $pesan]);
        }

        $hariIni = now()->toDateString();
        $absensi = Absensi::firstOrNew([
            'karyawan_id' => $user->id,
            'tanggal' => $hariIni,
        ]);

        // ---------------------------------------------------------------
        // 4. Absen MASUK
        // ---------------------------------------------------------------
        if ($data['tipe'] === 'masuk') {
            if ($absensi->exists && $absensi->jam_masuk) {
                $pesan = 'Anda sudah absen masuk hari ini pukul '.$absensi->jam_masuk->format('H:i').' WIB.';
                $this->catatLog($user, $qr, $data, $lat, $lng, 'gagal', $pesan, $request);

                throw ValidationException::withMessages(['qrcode' => $pesan]);
            }

            $batasMasuk = Carbon::parse($kantor->jam_masuk)->addMinutes((int) $kantor->toleransi);

            $absensi->fill([
                'lokasi_kantor_id' => $kantor->id,
                'jam_masuk' => now(),
                'status_masuk' => now()->greaterThan($batasMasuk) ? 'terlambat' : 'hadir',
                'status_pulang' => null,
                'latitude_masuk' => $lat,
                'longitude_masuk' => $lng,
                'jarak_masuk' => $jarak,
                'alamat_masuk' => $alamat,
                'foto_masuk' => $fotoPath ?? $absensi->foto_masuk,
            ]);
        } else {
            // -------------------------------------------------------------
            // 4b. Absen PULANG
            // -------------------------------------------------------------
            if (! $absensi->exists || ! $absensi->jam_masuk) {
                $pesan = 'Anda belum melakukan absen masuk hari ini.';
                $this->catatLog($user, $qr, $data, $lat, $lng, 'gagal', $pesan, $request);

                throw ValidationException::withMessages(['qrcode' => $pesan]);
            }

            if ($absensi->jam_pulang) {
                $pesan = 'Anda sudah absen pulang hari ini pukul '.$absensi->jam_pulang->format('H:i').' WIB.';
                $this->catatLog($user, $qr, $data, $lat, $lng, 'gagal', $pesan, $request);

                throw ValidationException::withMessages(['qrcode' => $pesan]);
            }

            $absensi->fill([
                'jam_pulang' => now(),
                'status_pulang' => 'selesai',
                'latitude_pulang' => $lat,
                'longitude_pulang' => $lng,
                'jarak_pulang' => $jarak,
                'alamat_pulang' => $alamat,
            ]);
        }

        $absensi->save();

        $this->catatLog($user, $qr, $data, $lat, $lng, 'sukses', null, $request, $jarak);

        $pesanSukses = $data['tipe'] === 'masuk'
            ? sprintf('Absen masuk berhasil pukul %s WIB. Jarak dari kantor: %s m.', now()->format('H:i'), number_format($jarak))
            : sprintf('Absen pulang berhasil pukul %s WIB. Jarak dari kantor: %s m.', now()->format('H:i'), number_format($jarak));

        return redirect()->route('absensi')->with('success', $pesanSukses);
    }

    /**
     * Riwayat absensi milik karyawan yang login.
     */
    public function riwayat(Request $request): View
    {
        $user = $request->user();
        $bulan = $request->query('bulan', now()->format('Y-m'));

        if (! preg_match('/^\d{4}-\d{2}$/', $bulan)) {
            $bulan = now()->format('Y-m');
        }

        $data = Absensi::with('lokasiKantor')
            ->where('karyawan_id', $user->id)
            ->whereRaw('DATE_FORMAT(tanggal, "%Y-%m") = ?', [$bulan])
            ->orderByDesc('tanggal')
            ->paginate(15)->withQueryString();

        return view('absensi.riwayat', [
            'user' => $user,
            'data' => $data,
            'bulan' => $bulan,
            'judul' => 'Riwayat Absensi',
        ]);
    }

    private function catatLog(
        $user,
        ?QrToken $qr,
        array $data,
        float $lat,
        float $lng,
        string $hasil,
        ?string $keterangan,
        Request $request,
        ?int $jarak = null
    ): void {
        LogScan::create([
            'karyawan_id' => $user->id,
            'lokasi_kantor_id' => $qr?->lokasi_kantor_id,
            'tanggal' => now()->toDateString(),
            'waktu' => now(),
            'payload' => substr((string) ($data['qrcode'] ?? ''), 0, 255),
            'latitude' => $lat,
            'longitude' => $lng,
            'jarak' => $jarak,
            'hasil' => $hasil,
            'keterangan' => $keterangan,
            'ip_address' => $request->ip(),
            'device' => substr((string) $request->userAgent(), 0, 255),
        ]);
    }
}
