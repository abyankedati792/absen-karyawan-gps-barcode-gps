<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LokasiKantor;
use App\Models\Pengaturan;
use App\Models\QrToken;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class QrController extends Controller
{
    /**
     * Halaman tampilan QR Code absensi ( untuk di-scan karyawan ).
     */
    public function index(Request $request): View
    {
        $kantor = LokasiKantor::default();

        abort_if(! $kantor, 500, 'Belum ada lokasi kantor aktif. Tambahkan dulu di menu Lokasi Kantor.');

        $qr = QrToken::aktif($kantor->id, $this->menitBerlaku());

        return view('admin.qr', [
            'judul' => 'QR Code Absensi',
            'qr' => $qr,
            'kantor' => $kantor,
            'menit' => $this->menitBerlaku(),
        ]);
    }

    /**
     * JSON: token QR terbaru ( di-poll oleh halaman tiap beberapa detik ).
     */
    public function token(): JsonResponse
    {
        $kantor = LokasiKantor::default();

        if (! $kantor) {
            return response()->json(['ok' => false, 'message' => 'Belum ada lokasi kantor aktif.'], 422);
        }

        $menit = $this->menitBerlaku();
        $qr = QrToken::aktif($kantor->id, $menit);

        // Putar balik ( rotate ) token menjelang masa berlaku habis.
        if ($qr->berlaku_sampai->getTimestamp() - now()->getTimestamp() < 45) {
            $qr->update(['status' => 'arsip']);
            $qr = QrToken::aktif($kantor->id, $menit);
        }

        return response()->json([
            'ok' => true,
            'payload' => $qr->payload(),
            'kantor' => $kantor->nama,
            'radius' => (int) $kantor->radius,
            'menit' => $menit,
            'expires_at' => $qr->berlaku_sampai->toIso8601String(),
            'sisa' => max(0, $qr->berlaku_sampai->getTimestamp() - now()->getTimestamp()),
            'server_time' => now()->toIso8601String(),
        ]);
    }

    private function menitBerlaku(): int
    {
        $dariEnv = (int) env('QR_MENIT_BERLAKU', 5);
        $dariDb = Pengaturan::getInt('menit_qr', $dariEnv > 0 ? $dariEnv : 5);

        return max(1, min(60, $dariDb ?: 5));
    }
}
