<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('absensi', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('karyawan_id');
            $table->unsignedInteger('lokasi_kantor_id');
            $table->date('tanggal');
            $table->dateTime('jam_masuk')->nullable();
            $table->dateTime('jam_pulang')->nullable();
            $table->enum('status_masuk', ['hadir', 'terlambat', 'izin', 'sakit', 'cuti', 'alpha'])->default('hadir');
            $table->enum('status_pulang', ['selesai', 'lembur', 'belum_pulang'])->nullable();
            $table->decimal('latitude_masuk', 10, 7)->nullable();
            $table->decimal('longitude_masuk', 10, 7)->nullable();
            $table->unsignedInteger('jarak_masuk')->nullable()->comment('jarak ke kantor dalam meter');
            $table->string('alamat_masuk')->nullable();
            $table->string('foto_masuk')->nullable();
            $table->decimal('latitude_pulang', 10, 7)->nullable();
            $table->decimal('longitude_pulang', 10, 7)->nullable();
            $table->unsignedInteger('jarak_pulang')->nullable();
            $table->string('alamat_pulang')->nullable();
            $table->string('catatan')->nullable();
            $table->timestamps();

            $table->unique(['karyawan_id', 'tanggal']);
            $table->index('tanggal');
            $table->index('lokasi_kantor_id');

            $table->foreign('karyawan_id')->references('id')->on('karyawan')->cascadeOnDelete();
            $table->foreign('lokasi_kantor_id')->references('id')->on('lokasi_kantor')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('absensi');
    }
};
