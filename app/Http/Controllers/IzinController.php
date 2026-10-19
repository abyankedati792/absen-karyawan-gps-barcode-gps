<?php

namespace App\Http\Controllers;

use App\Models\PengajuanIzin;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class IzinController extends Controller
{
    /**
     * Daftar pengajuan izin milik karyawan yang login + form pengajuan.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        return view('izin.index', [
            'judul' => 'Izin / Sakit / Cuti',
            'user' => $user,
            'data' => PengajuanIzin::where('karyawan_id', $user->id)
                ->orderByDesc('created_at')
                ->paginate(10),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'jenis' => ['required', 'in:izin,sakit,cuti'],
            'tanggal_mulai' => ['required', 'date', 'after_or_equal:today'],
            'tanggal_selesai' => ['required', 'date', 'after_or_equal:tanggal_mulai'],
            'alasan' => ['required', 'string', 'max:1000'],
            'surat' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
        ], [
            'tanggal_mulai.after_or_equal' => 'Tanggal mulai tidak boleh lewat dari hari ini.',
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai harus setelah atau sama dengan tanggal mulai.',
        ]);

        if ($request->hasFile('surat')) {
            $data['surat'] = $request->file('surat')->store('surat_izin', 'public');
        }

        $data['karyawan_id'] = $request->user()->id;

        PengajuanIzin::create($data);

        return redirect()->route('izin.index')->with('success', 'Pengajuan izin berhasil dikirim. Menunggu persetujuan admin.');
    }

    public function destroy(Request $request, PengajuanIzin $izin): RedirectResponse
    {
        if ($izin->karyawan_id !== $request->user()->id) {
            abort(403);
        }

        if ($izin->status !== 'menunggu') {
            return back()->with('error', 'Pengajuan yang sudah diproses tidak bisa dihapus.');
        }

        $izin->delete();

        return back()->with('success', 'Pengajuan dibatalkan.');
    }
}
