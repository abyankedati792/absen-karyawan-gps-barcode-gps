<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Absensi extends Model
{
    protected $table = 'absensi';

    protected $fillable = [
        'karyawan_id',
        'lokasi_kantor_id',
        'tanggal',
        'jam_masuk',
        'jam_pulang',
        'status_masuk',
        'status_pulang',
        'latitude_masuk',
        'longitude_masuk',
        'jarak_masuk',
        'alamat_masuk',
        'foto_masuk',
        'latitude_pulang',
        'longitude_pulang',
        'jarak_pulang',
        'alamat_pulang',
        'catatan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date:Y-m-d',
            'jam_masuk' => 'datetime',
            'jam_pulang' => 'datetime',
            'latitude_masuk' => 'decimal:7',
            'longitude_masuk' => 'decimal:7',
            'latitude_pulang' => 'decimal:7',
            'longitude_pulang' => 'decimal:7',
            'jarak_masuk' => 'integer',
            'jarak_pulang' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function karyawan(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class);
    }

    public function lokasiKantor(): BelongsTo
    {
        return $this->belongsTo(LokasiKantor::class, 'lokasi_kantor_id');
    }

    public function isTerlambat(): bool
    {
        return $this->status_masuk === 'terlambat';
    }
}
