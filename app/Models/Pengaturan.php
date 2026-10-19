<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengaturan extends Model
{
    protected $table = 'pengaturan';

    protected $fillable = [
        'kunci',
        'nilai',
        'keterangan',
    ];

    public static function get(string $kunci, ?string $default = null): ?string
    {
        $row = static::where('kunci', $kunci)->first();

        return $row?->nilai ?? $default;
    }

    public static function getInt(string $kunci, int $default = 0): int
    {
        return (int) static::get($kunci, (string) $default);
    }

    public static function set(string $kunci, ?string $nilai, ?string $keterangan = null): void
    {
        static::updateOrCreate(
            ['kunci' => $kunci],
            ['nilai' => $nilai, 'keterangan' => $keterangan]
        );
    }

    /**
     * Pengaturan yang dipakai bersama oleh view ( ditambahkan ke shared data).
     *
     * @return array<string, string|null>
     */
    public static function semua(): array
    {
        return static::pluck('nilai', 'kunci')->all();
    }
}
