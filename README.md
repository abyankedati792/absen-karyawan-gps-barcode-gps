# 📍 Aplikasi Absensi Karyawan — QR Code + GPS

> Perusahaan: **PT. Shelter Indonesia** (nama tampil otomatis di sidebar, footer, halaman login,
> dan bisa diubah kembali lewat menu **Pengaturan**).

Website absensi karyawan berbasis **Laravel 12** dengan verifikasi **QR Code dinamis** dan
**lokasi GPS** (radius kantor). Dibangun untuk XAMPP (Apache + MariaDB) maupun
`php artisan serve`.

---

## 🔑 Login Akun

| Role     | Email                | Password      |
|----------|----------------------|---------------|
| Admin    | `admin@absensi.test` | `admin123`    |
| Karyawan | `andi@absensi.test`  | `karyawan123` |
| Karyawan | `budi@absensi.test`  | `karyawan123` |
| Karyawan | `citra@absensi.test` | `karyawan123` |

---

## 🚀 Cara Menjalankan

### 1. Siapkan database
Buka **phpMyAdmin** → tab **Import** → pilih
`database/db_absensi_karyawan.sql` → Go.

Database `db_absensi_karyawan` (13 tabel + data awal) akan terbuat otomatis.

Alternatif via terminal:

```bat
E:\xampp\mysql\bin\mysql.exe -u root < database\db_absensi_karyawan.sql
```

### 2. Cek file `.env`

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=db_absensi_karyawan
DB_USERNAME=root
DB_PASSWORD=

APP_URL=http://localhost/Absen-karyawan-gps-barcode-gps/public
QR_MENIT_BERLAKU=5      # masa berlaku QR dinamis (menit)
RADIUS_DEFAULT=150      # radius absensi default (meter)
```

### 3. Logo perusahaan ( PT. Shelter Indonesia )

Logo tampil di **sidebar, halaman login, dan layar QR absensi**.

- **Ganti lewat aplikasi ( disarankan )** — menu **Admin → Pengaturan → Logo Perusahaan**
  ( format jpg / png / webp / svg, maks 2 MB ). File tersimpan otomatis di
  `storage/app/public/logo/` (terhubung ke `public/storage`).
- **Manual** — timpa file `public/img/logo.svg` dengan logo perusahaan Anda
  (nama file harus tetap `logo.svg`), atau ganti baris `logo` di tabel `pengaturan`
  menjadi `img/logo-anda.png`.

Saat ini dipakai logo bawaan `public/img/logo.svg` (lambang QR + pin GPS)
dan `public/img/logo-lengkap.svg` (lambang + wordmark, untuk keperluan cetak).

### 4. Jalankan aplikasi

**Via XAMPP (Apache)** — buka:
`http://localhost/Absen-karyawan-gps-barcode-gps/public`

**Via terminal:**

```bat
E:\xampp\php\php.exe artisan serve --port=8000
```

lalu buka `http://localhost:8000`.

> Saat butuh HTTPS untuk kamera/GPS di luar localhost, gunakan
> `http://localhost` / `http://127.0.0.1` (browser menganggapnya aman).

---

## 🧭 Cara Kerja Absensi

1. **Admin** membuka menu **QR Code Absensi** → QR tampil di layar dan
   **berganti otomatis** tiap 5 menit (dapat diubah di Pengaturan).
2. **Karyawan** membuka menu **Absensi QR + GPS**:
   - Tekan **Ambil Lokasi** → browser meminta izin GPS → jarak ke kantor dihitung
     (rumus Haversine).
   - Tekan **Buka Kamera** → QR dipindai (library `jsQR`) — dapat juga input token manual.
   - Opsional unggah foto, lalu **Simpan Absensi**.
3. Server memvalidasi:
   - token QR (ada, hari ini, belum kadaluarsa),
   - jarak GPS ≤ radius kantor,
   - absen masuk belum/ sudah, absen pulang butuh absen masuk,
   - keterlambatan dihitung dari jam masuk + toleransi.
4. Setiap pindai (sukses/gagal) dicatat di tabel `log_scan`.

---

## 📁 Struktur Penting

```
app/Http/Controllers/          Auth, Dashboard, Absensi, Izin
app/Http/Controllers/Admin/    Dashboard, Karyawan, LokasiKantor,
                               Absensi (rekap+export CSV), QR, Izin, Pengaturan
app/Http/Middleware/           RoleMiddleware (role:admin)
app/Models/                    Karyawan, Absensi, LokasiKantor, QrToken,
                               LogScan, PengajuanIzin, Pengaturan
database/db_absensi_karyawan.sql   Skema + data awal (import)
database/migrations/               Migrations (untuk database kosong)
resources/views/               Blade (layout, absensi, admin, dll)
public/css/app.css             Tampilan kustom
public/js/scan.js              Kamera QR + GPS
public/js/qr-display.js        QR admin otomatis berganti
routes/web.php                 Seluruh rute
```

### Tabel database

| Tabel            | Fungsi                                          |
|------------------|-------------------------------------------------|
| `karyawan`       | akun login (role admin/karyawan)                |
| `lokasi_kantor`  | titik GPS, radius, jam & toleransi kerja        |
| `absensi`        | 1 baris per karyawan per hari (masuk & pulang)  |
| `qr_token`       | token QR harian yang otomatis berganti          |
| `log_scan`       | audit setiap pemindaian QR (sukses/gagal)       |
| `pengajuan_izin` | izin / sakit / cuti + persetujuan admin         |
| `pengaturan`     | nama aplikasi, radius, masa berlaku QR, foto    |
| `sessions`, `cache`, `jobs` | bawaan Laravel (driver = database)    |

---

## ✅ Fitur

**Karyawan**
- Dashboard kehadiran hari ini & rekap bulanan
- Absen masuk/pulang dengan QR + GPS + foto
- Riwayat absensi (filter bulan)
- Ajukan izin / sakit / cuti + lampiran surat
- Ubah profil & password

**Admin**
- Dashboard statistik + grafik 7 hari
- Tampil QR dinamis (auto-refresh) untuk layar absensi
- CRUD karyawan, lokasi kantor (multi-cabang, radius per lokasi)
- Rekap absensi (filter tanggal/karyawan/status/lokasi) + **Export CSV**
- Persetujuan izin → otomatis menandai rekap absensi
- Pengaturan aplikasi (masa berlaku QR, wajib foto, radius default)

---

## 🧪 Verifikasi

Aplikasi diuji otomatis lewat HTTP (Apache) mencakup:
login/logout, seluruh halaman, proteksi role 403, alur absen masuk & pulang,
penolakan QR palsu & lokasi di luar radius, CRUD karyawan/lokasi, pengajuan +
persetujuan izin, pengaturan, profil, dan export CSV — **semua lulus**.
