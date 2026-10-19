<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LogScan extends Model
{
    protected $table = 'log_scan';

    protected $fillable = [
        'karyawan_id',
        'lokasi_kantor_id',
        'tanggal',
        'waktu',
        'payload',
        'latitude',
        'longitude',
        'jarak',
        'hasil',
        'keterangan',
        'ip_address',
        'device',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date:Y-m-d',
            'waktu' => 'datetime',
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'jarak' => 'integer',
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
}
