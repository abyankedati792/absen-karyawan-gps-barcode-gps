<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Karyawan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class KaryawanController extends Controller
{
    public function index(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));

        $data = Karyawan::query()
            ->when($q !== '', function ($query) use ($q) {
                $query->where(fn ($w) => $w
                    ->where('nama', 'like', "%{$q}%")
                    ->orWhere('nip', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%")
                    ->orWhere('departemen', 'like', "%{$q}%"));
            })
            ->orderBy('nama')
            ->paginate(12)->withQueryString();

        return view('admin.karyawan.index', [
            'judul' => 'Data Karyawan',
            'data' => $data,
            'q' => $q,
        ]);
    }

    public function create(): View
    {
        return view('admin.karyawan.create', [
            'judul' => 'Tambah Karyawan',
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validasi($request);

        $data['password'] = Hash::make($data['password']);
        $data['foto'] = $this->simpanFoto($request);

        Karyawan::create($data);

        return redirect()->route('admin.karyawan.index')->with('success', 'Karyawan berhasil ditambahkan.');
    }

    public function edit(Karyawan $karyawan): View
    {
        return view('admin.karyawan.edit', [
            'judul' => 'Edit Karyawan: '.$karyawan->nama,
            'karyawan' => $karyawan,
        ]);
    }

    public function update(Request $request, Karyawan $karyawan): RedirectResponse
    {
        $data = $this->validasi($request, $karyawan);

        if (filled($request->input('password'))) {
            $data['password'] = Hash::make($request->input('password'));
        } else {
            unset($data['password']);
        }

        $foto = $this->simpanFoto($request);
        if ($foto) {
            $data['foto'] = $foto;
        }

        $karyawan->update($data);

        return redirect()->route('admin.karyawan.index')->with('success', 'Data karyawan berhasil diperbarui.');
    }

    public function destroy(Request $request, Karyawan $karyawan): RedirectResponse
    {
        if ($karyawan->is($request->user())) {
            return back()->with('error', 'Tidak bisa menghapus akun yang sedang login.');
        }

        $karyawan->delete();

        return redirect()->route('admin.karyawan.index')->with('success', 'Karyawan berhasil dihapus.');
    }

    private function validasi(Request $request, ?Karyawan $karyawan = null): array
    {
        return $request->validate([
            'nip' => ['required', 'string', 'max:30', Rule::unique('karyawan', 'nip')->ignore($karyawan?->id)],
            'nama' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:100', Rule::unique('karyawan', 'email')->ignore($karyawan?->id)],
            'password' => [$karyawan ? 'nullable' : 'required', 'string', 'min:6', 'max:255'],
            'no_hp' => ['nullable', 'string', 'max:20'],
            'jabatan' => ['nullable', 'string', 'max:100'],
            'departemen' => ['nullable', 'string', 'max:100'],
            'alamat' => ['nullable', 'string', 'max:255'],
            'role' => ['required', Rule::in(['admin', 'karyawan'])],
            'status' => ['required', Rule::in(['aktif', 'nonaktif'])],
            'foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ], [
            'nip.unique' => 'NIP sudah terdaftar.',
            'email.unique' => 'Email sudah terdaftar.',
            'password.min' => 'Password minimal 6 karakter.',
        ]);
    }

    private function simpanFoto(Request $request): ?string
    {
        if (! $request->hasFile('foto')) {
            return null;
        }

        return $request->file('foto')->store('foto_karyawan', 'public');
    }
}
