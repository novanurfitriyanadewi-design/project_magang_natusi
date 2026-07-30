# TODO: Perbaikan Laporan Absensi dan Karyawan

## 1. Perbaiki route di view `absensi-karyawan.blade.php`
- [x] Ubah form action dari `route('admin.laporan.absensi-karyawan')` → `route('admin-karyawan.laporan.absensi')`
- [x] Ubah export link dari `route('admin.laporan.absensi-karyawan.export', ...)` → `route('admin-karyawan.laporan.absensi.export', ...)`

## 2. Buat Export class untuk Laporan Karyawan
- [x] Buat file `app/Exports/LaporanKaryawanExport.php`

## 3. Tambahkan method `export()` di `LaporanKaryawanController`
- [x] Tambahkan method export dengan filter yang sama seperti method index

## 4. Testing
- [ ] Verifikasi halaman laporan karyawan bisa diakses (route: `admin-karyawan.laporan.karyawan`)
- [ ] Verifikasi halaman laporan absensi bisa diakses (route: `admin-karyawan.laporan.absensi`)

## Selesai ✅

