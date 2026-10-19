<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('log_scan', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('karyawan_id')->nullable();
            $table->unsignedInteger('lokasi_kantor_id')->nullable();
            $table->date('tanggal');
            $table->dateTime('waktu');
            $table->string('payload')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->unsignedInteger('jarak')->nullable();
            $table->enum('hasil', ['sukses', 'gagal'])->default('sukses');
            $table->string('keterangan')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('device')->nullable();
            $table->timestamps();

            $table->index('karyawan_id');
            $table->index('tanggal');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('log_scan');
    }
};
