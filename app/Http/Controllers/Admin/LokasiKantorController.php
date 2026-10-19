<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LokasiKantor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LokasiKantorController extends Controller
{
    public function index(): View
    {
        return view('admin.lokasi.index', [
            'judul' => 'Lokasi Kantor',
            'data' => LokasiKantor::orderBy('nama')->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.lokasi.create', [
            'judul' => 'Tambah Lokasi Kantor',
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validasi($request);

        if ($data['is_default']) {
            LokasiKantor::query()->update(['is_default' => 0]);
        }

        LokasiKantor::create($data);

        return redirect()->route('admin.lokasi.index')->with('success', 'Lokasi kantor berhasil ditambahkan.');
    }

    public function edit(LokasiKantor $lokasi): View
    {
        return view('admin.lokasi.edit', [
            'judul' => 'Edit Lokasi: '.$lokasi->nama,
            'lokasi' => $lokasi,
        ]);
    }

    public function update(Request $request, LokasiKantor $lokasi): RedirectResponse
    {
        $data = $this->validasi($request, $lokasi);

        if ($data['is_default']) {
            LokasiKantor::where('id', '!=', $lokasi->id)->update(['is_default' => 0]);
        }

        $lokasi->update($data);

        return redirect()->route('admin.lokasi.index')->with('success', 'Lokasi kantor berhasil diperbarui.');
    }

    public function destroy(LokasiKantor $lokasi): RedirectResponse
    {
        if ($lokasi->absensi()->exists()) {
            return back()->with('error', 'Lokasi tidak bisa dihapus karena sudah dipakai data absensi.');
        }

        $lokasi->delete();

        return redirect()->route('admin.lokasi.index')->with('success', 'Lokasi kantor berhasil dihapus.');
    }

    private function validasi(Request $request, ?LokasiKantor $lokasi = null): array
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:100'],
            'alamat' => ['nullable', 'string', 'max:255'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'radius' => ['required', 'integer', 'min:10', 'max:5000'],
            'jam_masuk' => ['required', 'date_format:H:i'],
            'jam_pulang' => ['required', 'date_format:H:i'],
            'toleransi' => ['required', 'integer', 'min:0', 'max:180'],
            'is_default' => ['nullable', 'in:1'],
            'status' => ['required', 'in:aktif,nonaktif'],
        ], [
            'jam_masuk.date_format' => 'Format jam masuk harus HH:MM (contoh 08:00).',
            'jam_pulang.date_format' => 'Format jam pulang harus HH:MM (contoh 17:00).',
            'latitude.between' => 'Latitude harus antara -90 sampai 90.',
            'longitude.between' => 'Longitude harus antara -180 sampai 180.',
            'radius.min' => 'Radius minimal 10 meter.',
        ]);

        $data['jam_masuk'] = $data['jam_masuk'].':00';
        $data['jam_pulang'] = $data['jam_pulang'].':00';
        $data['is_default'] = isset($data['is_default']) ? 1 : 0;

        return $data;
    }
}
