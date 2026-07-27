<?php

namespace Database\Seeders;

use App\Models\PesertaMagang;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class PesertaMagangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Cari user yaumil berdasarkan id_user 4 (atau email)
        $user = User::where('id_user', 4)->first() ?? User::where('email', 'yaumil@gmail.com')->first();

        if ($user) {
            PesertaMagang::firstOrCreate(
                ['user_id' => $user->id_user],
                [
                    'permintaan_id'      => null,
                    'alamat'             => 'Surabaya',
                    'tingkat_pendidikan' => 'Perguruan Tinggi', // Menyesuaikan ITS
                    'kelas'              => 'SI',                // Dari data jurusan di users
                    'tgl_mulai'          => Carbon::now()->startOfMonth(),
                    'tgl_selesai'        => Carbon::now()->addMonths(3),
                    'durasi_magang'      => 3,
                    'nama_guru'          => 'Dosen Pembimbing',
                    'no_hpguru'          => '08123456789',
                    'status'             => 'aktif',
                ]
            );
        }
    }
}