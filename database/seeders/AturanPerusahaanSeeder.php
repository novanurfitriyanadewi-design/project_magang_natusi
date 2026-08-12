<?php

namespace Database\Seeders;

use App\Models\AturanPerusahaan;
use Illuminate\Database\Seeder;

class AturanPerusahaanSeeder extends Seeder
{
    public function run(): void
    {
        $aturan = [
            [
                'nama' => 'TATIB Peserta Magang',
                'untuk_role' => 'magang',
                'status' => 'aktif',
                'deskripsi' => "1. Masuk Magang\nPeserta magang wajib masuk pada pukul 08.00 dan mengikuti kegiatan sampai pukul 17.00.\n\n2. Istirahat Sholat Duhur\nWaktu istirahat untuk sholat duhur adalah pukul 12.00 sampai dengan 13.00.\n\n3. Sholat Ashar\nWaktu untuk melaksanakan sholat Ashar adalah pukul 15.00 sampai dengan 15.30.\n\n4. Kebersihan Ruangan\nSebelum masuk kantor dan setelah selesai kegiatan kantor, seluruh ruangan wajib dibersihkan dan dirapikan.\n\n5. Larangan Streaming dan Game Online\nPeserta magang dilarang melakukan streaming dan bermain game online selama jam kegiatan magang.\n\n6. Kepatuhan Terhadap Aturan\nPeserta magang wajib mematuhi seluruh aturan yang telah ditetapkan. Tidak setuju dengan aturan berarti memilih untuk tidak mengikuti kegiatan magang dan dapat dipersilakan untuk pulang.",
            ],
            [
                'nama' => 'Jam Kerja & Kehadiran',
                'untuk_role' => 'karyawan',
                'status' => 'aktif',
                'deskripsi' => "Karyawan wajib mengikuti jam kerja dan melakukan pencatatan kehadiran sesuai dengan ketentuan perusahaan.\n\nKaryawan wajib hadir tepat waktu, melakukan absensi masuk dan keluar, serta mengikuti ketentuan jam kerja yang telah ditetapkan oleh perusahaan.",
            ],
            [
                'nama' => 'Kode Etik Karyawan',
                'untuk_role' => 'karyawan',
                'status' => 'aktif',
                'deskripsi' => "Karyawan wajib menjaga sikap profesional dalam menjalankan pekerjaan.\n\nKaryawan wajib:\n- Menghormati rekan kerja.\n- Menjaga komunikasi dan perilaku yang baik.\n- Menjaga nama baik perusahaan.\n- Menjaga lingkungan kerja yang aman dan nyaman.\n- Melaksanakan pekerjaan dengan penuh tanggung jawab.",
            ],
            [
                'nama' => 'Cuti & Izin',
                'untuk_role' => 'karyawan',
                'status' => 'aktif',
                'deskripsi' => "Pengajuan cuti dan izin wajib dilakukan melalui prosedur yang telah ditetapkan perusahaan.\n\nSetiap karyawan wajib memberikan informasi dan mengajukan izin kepada pihak yang berwenang sebelum meninggalkan pekerjaan, kecuali dalam keadaan darurat.",
            ],
            [
                'nama' => 'Keamanan Data & TI',
                'untuk_role' => 'karyawan',
                'status' => 'aktif',
                'deskripsi' => "Karyawan wajib menjaga keamanan dan kerahasiaan seluruh data perusahaan.\n\nKaryawan dilarang:\n- Membagikan password akun perusahaan kepada pihak lain.\n- Membagikan dokumen internal perusahaan tanpa izin.\n- Mengakses data yang bukan menjadi kewenangannya.\n- Menggunakan perangkat atau sistem perusahaan untuk kegiatan yang tidak berkaitan dengan pekerjaan tanpa izin.",
            ],
            [
                'nama' => 'Kebersihan Lingkungan Kerja',
                'untuk_role' => 'semua',
                'status' => 'aktif',
                'deskripsi' => "Seluruh peserta magang dan karyawan wajib menjaga kebersihan, kerapian, dan kenyamanan seluruh area kerja.\n\nSebelum memulai kegiatan dan setelah selesai kegiatan kantor, setiap orang wajib membantu menjaga kebersihan dan kerapian ruangan.",
            ],
            [
                'nama' => 'Larangan Streaming & Game Online',
                'untuk_role' => 'semua',
                'status' => 'aktif',
                'deskripsi' => "Peserta magang dan karyawan dilarang melakukan streaming atau bermain game online selama jam kerja atau jam kegiatan kantor.\n\nPenggunaan perangkat kantor harus diutamakan untuk kepentingan pekerjaan dan kegiatan yang berkaitan dengan perusahaan.",
            ],
            [
                'nama' => 'Kepatuhan Terhadap Peraturan',
                'untuk_role' => 'semua',
                'status' => 'aktif',
                'deskripsi' => "Seluruh peserta magang dan karyawan wajib mematuhi aturan dan ketentuan yang telah ditetapkan oleh perusahaan.\n\nSetiap orang bertanggung jawab untuk menjaga ketertiban, kedisiplinan, kebersihan, keamanan, dan suasana kerja yang baik.",
            ],
        ];

        foreach ($aturan as $item) {
            AturanPerusahaan::query()->updateOrCreate(
                ['nama' => $item['nama']],
                $item
            );
        }
    }
}
