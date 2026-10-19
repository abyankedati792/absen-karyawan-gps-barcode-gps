<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Pesan validasi Bahasa Indonesia
    |--------------------------------------------------------------------------
    | Kunci yang tidak dicantumkan di sini otomatis memakai bahasa bawaan
    | Laravel ( fallback locale = en ).
    */

    'required' => ':attribute wajib diisi.',
    'required_if' => ':attribute wajib diisi.',
    'required_with' => ':attribute wajib diisi.',
    'nullable' => ':attribute boleh dikosongkan.',
    'accepted' => ':attribute harus diterima.',
    'active_url' => ':attribute bukan URL yang valid.',
    'email' => 'Format :attribute tidak valid.',
    'unique' => ':attribute sudah terpakai.',
    'exists' => ':attribute tidak ditemukan.',
    'in' => 'Pilihan :attribute tidak valid.',
    'integer' => ':attribute harus berupa bilangan bulat.',
    'numeric' => ':attribute harus berupa angka.',
    'boolean' => ':attribute harus bernilai ya/tidak.',
    'date' => ':attribute bukan tanggal yang valid.',
    'date_format' => 'Format :attribute tidak sesuai (:format).',
    'after_or_equal' => ':attribute harus tanggal setelah atau sama dengan :date.',
    'before_or_equal' => ':attribute harus tanggal sebelum atau sama dengan :date.',
    'image' => ':attribute harus berupa gambar.',
    'file' => ':attribute harus berupa berkas.',
    'confirmed' => 'Konfirmasi :attribute tidak sama.',
    'same' => ':attribute harus sama dengan :other.',
    'url' => 'Format :attribute tidak valid.',
    'string' => ':attribute harus berupa teks.',
    'array' => ':attribute harus berupa daftar.',
    'mimes' => ':attribute harus berupa berkas bertipe: :values.',
    'mimetypes' => ':attribute harus berupa berkas bertipe: :values.',
    'size' => [
        'numeric' => ':attribute harus :size.',
        'file' => ':attribute harus :size kilobyte.',
        'string' => ':attribute harus :size karakter.',
        'array' => ':attribute harus berisi :size item.',
    ],
    'min' => [
        'numeric' => ':attribute minimal :min.',
        'file' => ':attribute minimal :min kilobyte.',
        'string' => ':attribute minimal :min karakter.',
        'array' => ':attribute minimal :min item.',
    ],
    'max' => [
        'numeric' => ':attribute maksimal :max.',
        'file' => ':attribute maksimal :max kilobyte.',
        'string' => ':attribute maksimal :max karakter.',
        'array' => ':attribute maksimal :max item.',
    ],
    'between' => [
        'numeric' => ':attribute antara :min dan :max.',
        'string' => ':attribute antara :min dan :max karakter.',
        'file' => ':attribute antara :min dan :max kilobyte.',
        'array' => ':attribute antara :min dan :max item.',
    ],
    'custom' => [
        'attribute-name' => [
            'rule-name' => 'pesan-kustom',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Nama atribut ( field ) dalam Bahasa Indonesia
    |--------------------------------------------------------------------------
    */
    'attributes' => [
        'email' => 'email',
        'password' => 'password',
        'password_confirmation' => 'konfirmasi password',
        'remember' => 'ingat saya',
        'nip' => 'NIP',
        'nama' => 'nama',
        'nama_aplikasi' => 'nama aplikasi',
        'nama_perusahaan' => 'nama perusahaan',
        'no_hp' => 'nomor HP',
        'jabatan' => 'jabatan',
        'departemen' => 'departemen',
        'alamat' => 'alamat',
        'alamat_kantor' => 'alamat kantor',
        'role' => 'role',
        'status' => 'status',
        'foto' => 'foto',
        'latitude' => 'latitude',
        'longitude' => 'longitude',
        'radius' => 'radius',
        'radius_default' => 'radius default',
        'jam_masuk' => 'jam masuk',
        'jam_pulang' => 'jam pulang',
        'toleransi' => 'toleransi',
        'tipe' => 'jenis absensi',
        'qrcode' => 'QR Code',
        'jenis' => 'jenis',
        'tanggal_mulai' => 'tanggal mulai',
        'tanggal_selesai' => 'tanggal selesai',
        'alasan' => 'alasan',
        'surat' => 'surat keterangan',
        'aksi' => 'aksi',
        'catatan_admin' => 'catatan admin',
        'menit_qr' => 'masa berlaku QR',
        'aktifkan_foto' => 'wajib foto',
        'mulai' => 'tanggal mulai',
        'sampai' => 'tanggal sampai',
        'karyawan_id' => 'karyawan',
        'lokasi_id' => 'lokasi kantor',
        'q' => 'pencarian',
    ],

];
