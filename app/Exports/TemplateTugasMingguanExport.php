<?php

namespace App\Exports;

use App\Support\JurusanKategori;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

/**
 * Template Excel untuk unggah tugas mingguan. Sheet-nya dibentuk dinamis
 * dari daftar jurusan aktif (Kelola Jurusan) — tambah jurusan baru otomatis
 * menambah sheet baru di template ini, tanpa perlu ubah kode.
 */
class TemplateTugasMingguanExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        $sheets = [];

        foreach (JurusanKategori::semua() as $jurusan) {
            $sheets[] = new TugasMingguanSheetExport($jurusan);
        }

        return $sheets;
    }
}
