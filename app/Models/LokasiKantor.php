<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LokasiKantor extends Model
{
    protected $table = 'lokasi_kantor';

    protected $fillable = [
        'nama',
        'alamat',
        'latitude',
        'longitude',
        'radius',
        'jam_masuk',
        'jam_pulang',
        'toleransi',
        'is_default',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'radius' => 'integer',
            'toleransi' => 'integer',
            'is_default' => 'boolean',
            'jam_masuk' => 'string',
            'jam_pulang' => 'string',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function absensi(): HasMany
    {
        return $this->hasMany(Absensi::class);
    }

    public function isAktif(): bool
    {
        return $this->status === 'aktif';
    }

    /**
     * Jarak (dalam meter) dari titik GPS tertentu ke kantor ini ( rumus Haversine ).
     */
    public function jarakDari(?float $lat, ?float $lng): ?float
    {
        if ($lat === null || $lng === null) {
            return null;
        }

        return self::haversine((float) $this->latitude, (float) $this->longitude, $lat, $lng);
    }

    /**
     * Hitung jarak antar 2 koordinat GPS dalam meter.
     */
    public static function haversine(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $radius = 6371000; // radius bumi (meter)

        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat / 2) ** 2
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) ** 2;

        return 2 * $radius * asin(min(1, sqrt($a)));
    }

    public static function default(): ?self
    {
        return self::where('status', 'aktif')
            ->orderByDesc('is_default')
            ->orderBy('id')
            ->first();
    }
}
