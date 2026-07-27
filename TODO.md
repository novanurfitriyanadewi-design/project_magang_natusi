# TODO: Fitur Absensi Karyawan & Notifikasi

## Absensi Karyawan - Progress
- [x] 0. Analisis kode existing (Model, Controller, View, Routes, Migration)
- [x] 1. Migration: Tambah 'terlambat' ke enum status absensi_karyawan
- [x] 2. Update Controller: Tambah show(), create(), edit(), update() + update statistik
- [x] 3. Update Routes: Tambah resource route lengkap (show, create, edit, update)
- [x] 4. Create View: admin/absensi-karyawan/create.blade.php
- [x] 5. Create View: admin/absensi-karyawan/edit.blade.php
- [x] 6. Create View: admin/absensi-karyawan/show.blade.php
- [x] 7. Update View: index.blade.php (tambah show, edit button, badge terlambat)

## Notifikasi - Progress
- [x] 8. Controller: Full CRUD + API methods (milikSaya, tandaiDibaca, tandaiSemuaDibaca, tandaiDibacaWeb, tandaiSemuaDibacaWeb)
- [x] 9. Routes web.php: Resource routes untuk admin/notifikasi (index, create, store, show, edit, update, destroy)
- [x] 10. Routes api.php: API routes untuk AdminNotifikasi (milik-saya, tandai-dibaca, tandai-semua-dibaca)
- [x] 11. composer dump-autoload

