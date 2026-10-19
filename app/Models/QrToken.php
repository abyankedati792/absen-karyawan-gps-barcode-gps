<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class QrToken extends Model
{
    protected $table = 'qr_token';

    protected $fillable = [
        'lokasi_kantor_id',
        'tanggal',
        'token',
        'berlaku_sampai',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date:Y-m-d',
            'berlaku_sampai' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function lokasiKantor(): BelongsTo
    {
        return $this->belongsTo(LokasiKantor::class, 'lokasi_kantor_id');
    }

    /**
     * Ambil / buat QR token aktif untuk hari ini ( per kantor ).
     */
    public static function aktif(int $kantorId, int $menitBerlaku): self
    {
        $hariIni = Carbon::today();

        $token = self::where('lokasi_kantor_id', $kantorId)
            ->where('tanggal', $hariIni)
            ->where('status', 'aktif')
            ->where('berlaku_sampai', '>=', now())
            ->orderByDesc('id')
            ->first();

        if ($token) {
            return $token;
        }

        return self::create([
            'lokasi_kantor_id' => $kantorId,
            'tanggal' => $hariIni,
            'token' => strtoupper(Str::random(32)),
            'berlaku_sampai' => now()->addMinutes(max(1, $menitBerlaku)),
            'status' => 'aktif',
        ]);
    }

    /**
     * Payload yang dicetak di QR Code:
     *   ABSENSI|{id_kantor}|{tanggal}|{token}
     */
    public function payload(): string
    {
        return implode('|', [
            'ABSENSI',
            $this->lokasi_kantor_id,
            $this->tanggal->format('Y-m-d'),
            $this->token,
        ]);
    }

    public function isKadaluarsa(): bool
    {
        return $this->berlaku_sampai->isPast();
    }

    /**
     * Validasi payload hasil pemindaian QR.
     * Mengembalikan [ ?QrToken, ?string pesan error ].
     */
    public static function validasi(string $payload): array
    {
        $parts = explode('|', trim($payload));

        if (count($parts) !== 4 || $parts[0] !== 'ABSENSI') {
            return [null, 'QR Code tidak valid. Pastikan Anda memindai QR absensi dari aplikasi ini.'];
        }

        [, $kantorId, $tanggal, $token] = $parts;

        $qr = self::where('token', $token)
            ->where('lokasi_kantor_id', $kantorId)
            ->where('tanggal', $tanggal)
            ->where('status', 'aktif')
            ->first();

        if (! $qr) {
            return [null, 'QR Code tidak ditemukan atau sudah tidak berlaku.'];
        }

        if ($qr->isKadaluarsa()) {
            return [null, 'QR Code sudah kedaluwarsa. Silakan pindai ulang QR terbaru.'];
        }

        return [$qr, null];
    }
}
