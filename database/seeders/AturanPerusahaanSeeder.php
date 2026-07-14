<?php

namespace Database\Seeders;

use App\Models\AturanPerusahaan;
use Illuminate\Database\Seeder;

class AturanPerusahaanSeeder extends Seeder
{
    public function run(): void
    {
        $rules = [
            [
                'nama' => 'Jam Kerja Operasional',
                'deskripsi' => 'Peserta magang wajib mengikuti jam operasional yang ditetapkan perusahaan. Kehadiran dicatat melalui sistem absensi portal. Keterlambatan, izin, atau ketidakhadiran wajib disampaikan kepada pembimbing atau admin sebelum jam kerja dimulai.',
                'status' => 'aktif',
            ],
            [
                'nama' => 'Standar Pakaian dan Atribut',
                'deskripsi' => 'Peserta magang wajib berpakaian rapi, sopan, bersih, dan sesuai lingkungan profesional. Atribut atau identitas perusahaan digunakan apabila diwajibkan. Pakaian yang memuat unsur diskriminatif, provokatif, atau tidak pantas tidak diperbolehkan.',
                'status' => 'aktif',
            ],
            [
                'nama' => 'Kode Etik Profesional',
                'deskripsi' => 'Seluruh pengguna portal wajib menjaga sopan santun, kerahasiaan data, integritas, dan hubungan kerja yang profesional. Informasi internal perusahaan tidak boleh dibagikan kepada pihak luar tanpa persetujuan yang berwenang.',
                'status' => 'aktif',
            ],
            [
                'nama' => 'Prosedur Lembur Darurat',
                'deskripsi' => 'Lembur hanya dapat dilakukan atas persetujuan pembimbing atau atasan yang berwenang. Waktu, alasan, dan hasil pekerjaan lembur wajib dicatat. Pelaksanaan lembur mengikuti ketentuan perusahaan yang berlaku.',
                'status' => 'aktif',
            ],
            [
                'nama' => 'Hak Cuti dan Izin Sakit',
                'deskripsi' => 'Permohonan izin atau cuti disampaikan melalui prosedur yang ditetapkan perusahaan. Izin sakit yang berlangsung lebih dari satu hari dapat memerlukan bukti pendukung. Peserta bertanggung jawab mengomunikasikan pekerjaan yang tertunda kepada pembimbing.',
                'status' => 'aktif',
            ],
        ];

        foreach ($rules as $rule) {
            AturanPerusahaan::query()->updateOrCreate(
                ['nama' => $rule['nama']],
                $rule,
            );
        }
    }
}
