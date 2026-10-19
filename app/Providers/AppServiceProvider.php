<?php

namespace App\Providers;

use App\Models\Pengaturan;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();

        // Pengaturan aplikasi ( tabel `pengaturan` ) dipakai oleh layout & view.
        try {
            $pengaturan = Pengaturan::semua();

            View::share('pengaturan', $pengaturan);
            View::share('namaAplikasi', $pengaturan['nama_aplikasi'] ?? config('app.name'));

            // Logo perusahaan: hasil unggahan ( storage ) / file di public / logo bawaan.
            $logo = $pengaturan['logo'] ?? null;
            $logoUrl = 'img/logo.svg';

            if ($logo && (str_starts_with($logo, 'http') || is_file(public_path($logo)))) {
                $logoUrl = $logo;
            }

            View::share('logoUrl', $logoUrl);
        } catch (\Throwable $e) {
            // Database belum siap ( misal saat import ) - abaikan.
        }
    }
}
