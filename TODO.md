# TODO: Ubah 4 Card Metrics Dashboard Admin Karyawan

## Step 1: Update Controller
- [x] Tambah variabel `$karyawanNonAktif`
- [x] Tambah variabel `$karyawanBaruBulanIni`
- [x] Kirim variabel baru ke view via compact()

## Step 2: Update Blade
- [x] Ganti 4 card kecil di grid "Ringkasan Operasional Karyawan" dengan card gradien baru:
  - Card 1: TOTAL KARYAWAN (violet-to-purple, icon `group`)
  - Card 2: KARYAWAN AKTIF (emerald-to-teal, icon `check_circle`)
  - Card 3: KARYAWAN NON-AKTIF (rose-to-red, icon `cancel`)
  - Card 4: BARU BULAN INI (sky-to-blue, icon `person_add`)

## Step 3: Testing
- [x] Jalankan `php artisan optimize:clear`
- [x] Verifikasi tampilan dashboard

