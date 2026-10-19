<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('qr_token', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('lokasi_kantor_id');
            $table->date('tanggal');
            $table->string('token', 64)->unique();
            $table->dateTime('berlaku_sampai');
            $table->enum('status', ['aktif', 'arsip'])->default('aktif');
            $table->timestamps();

            $table->index(['lokasi_kantor_id', 'tanggal']);
            $table->foreign('lokasi_kantor_id')->references('id')->on('lokasi_kantor')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('qr_token');
    }
};
