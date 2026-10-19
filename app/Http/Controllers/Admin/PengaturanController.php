<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pengaturan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PengaturanController extends Controller
{
    public function edit(): View
    {
        return view('admin.pengaturan.edit', [
            'judul' => 'Pengaturan Aplikasi',
            'pengaturan' => Pengaturan::semua(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nama_aplikasi' => ['required', 'string', 'max:100'],
            'nama_perusahaan' => ['required', 'string', 'max:100'],
            'alamat_kantor' => ['nullable', 'string', 'max:255'],
            'radius_default' => ['required', 'integer', 'min:10', 'max:5000'],
            'menit_qr' => ['required', 'integer', 'min:1', 'max:60'],
            'aktifkan_foto' => ['required', 'in:0,1'],
            'logo' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,svg', 'max:2048'],
            'hapus_logo' => ['nullable', 'in:1'],
        ], [
            'radius_default.min' => 'Radius minimal 10 meter.',
            'logo.mimes' => 'Logo harus berupa file gambar (jpg, png, webp, atau svg).',
            'logo.max' => 'Ukuran logo maksimal 2 MB.',
        ]);

        foreach ($data as $kunci => $nilai) {
            if (in_array($kunci, ['logo', 'hapus_logo'], true)) {
                continue;
            }
            Pengaturan::set($kunci, (string) $nilai);
        }

        // Ganti / hapus logo perusahaan.
        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('logo', 'public');
            Pengaturan::set('logo', 'storage/'.$path);
        } elseif ($request->input('hapus_logo')) {
            $logoLama = Pengaturan::get('logo');
            if ($logoLama && str_starts_with($logoLama, 'storage/') && is_file(public_path($logoLama))) {
                @unlink(public_path($logoLama));
            }
            Pengaturan::set('logo', 'img/logo.svg');
        }

        return redirect()->route('admin.pengaturan.edit')->with('success', 'Pengaturan berhasil disimpan.');
    }
}
