# Perbaikan Notifikasi Pengumuman Admin

## Masalah yang Ditemukan
Ketika admin membuat pengumuman, data tidak masuk di halaman karyawan karena:
1. **Tidak ada notifikasi otomatis** - Pengumuman dibuat tapi karyawan tidak mendapat notifikasi
2. **Status aktif tidak konsisten** - Nilai `aktif` bisa jadi `false` jika checkbox tidak dicentang
3. **Logika PengumumanPenerima inkonsisten** antara method `store()` dan `update()`

## Solusi yang Diimplementasikan

### 1. Tambahkan Import Notifikasi Model
File: `app/Http/Controllers/AdminKaryawan/PengumumanController.php`
- Tambahkan: `use App\Models\Notifikasi;`

### 2. Update Method `store()` - Buat Pengumuman
**Perubahan:**
- Pastikan `aktif` selalu `true` sebagai default: `$request->has('aktif') ? $request->boolean('aktif') : true`
- Buat notifikasi otomatis untuk setiap penerima pengumuman
- Untuk target `umum`: ambil semua karyawan aktif dan buat notifikasi untuk masing-masing
- Untuk target `individu`: ambil karyawan terpilih dan buat notifikasi untuk masing-masing

```php
// Buat notifikasi untuk setiap penerima
foreach ($penerimaBanyak as $karyawan) {
    if ($karyawan->user) {
        Notifikasi::create([
            'user_id' => $karyawan->user->id_user,
            'judul' => $pengumuman->judul,
            'pesan' => 'Pengumuman baru: ' . $pengumuman->judul,
            'kategori' => $pengumuman->kategori,
            'tipe' => 'pengumuman',
            'referensi_id' => $pengumuman->id_pengumuman,
            'dibaca' => false,
        ]);
    }
}
```

### 3. Update Method `update()` - Edit Pengumuman
**Perubahan:**
- Hapus notifikasi lama saat pengumuman diupdate
- Buat notifikasi baru untuk penerima yang baru/diubah
- Konsistenkan logika PengumumanPenerima untuk target `umum` dan `individu`

### 4. Perbaiki Form Blade
File: `resources/views/admin-karyawan/pengumuman/create.blade.php` dan `edit.blade.php`
- Tambahkan hidden input: `<input type="hidden" name="aktif" value="0">`
- Hal ini memastikan form selalu mengirim nilai `aktif` baik `0` atau `1`

```html
<label class="flex cursor-pointer items-center gap-3">
    <input type="hidden" name="aktif" value="0">
    <input
        type="checkbox"
        name="aktif"
        value="1"
        {{ old('aktif', true) ? 'checked' : '' }}
        class="h-4 w-4 rounded border-slate-300"
    >
    <span class="text-sm font-medium text-slate-700">
        Aktifkan pengumuman
    </span>
</label>
```

## Hasil Perbaikan
✅ Pengumuman admin sekarang akan:
- Selalu dibuat dengan status `aktif = true`
- Otomatis mengirim notifikasi ke semua penerima (umum atau individu)
- Notifikasi akan terupdate jika pengumuman diedit
- Karyawan akan melihat pengumuman di halaman mereka dengan notifikasi

## Testing
Untuk memverifikasi:
1. Login sebagai admin karyawan
2. Buat pengumuman baru dengan target "Semua Karyawan"
3. Cek tabel `notifikasi` - harus ada record baru untuk setiap karyawan
4. Login sebagai karyawan - pengumuman harus terlihat di halaman
5. Edit pengumuman - notifikasi lama dihapus dan notifikasi baru dibuat
