<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\JamOperasional;
use App\Models\PesertaMagang;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AbsensiController extends ApiCrudController
{
    protected string $modelClass = Absensi::class;

    protected array $with = [
        'peserta.user',
    ];

    protected array $files = [
        'surat_izin' => 'surat-izin',
        'surat_sakit' => 'surat-sakit',
    ];

    protected function rules(?Model $model = null): array
    {
        return [
            'latitude' => [
                'required',
                'numeric',
                'between:-90,90',
            ],

            'longitude' => [
                'required',
                'numeric',
                'between:-180,180',
            ],
        ];
    }

    public function absen(Request $request): JsonResponse
    {
        $data = $request->validate($this->rules());

        $peserta = PesertaMagang::query()
            ->where('user_id', $request->user()->id_user)
            ->where('status', 'aktif')
            ->first();

        if (!$peserta) {
            return response()->json([
                'success' => false,
                'message' => 'Data peserta magang aktif tidak ditemukan.',
            ], 404);
        }

        $sudahAbsen = Absensi::query()
            ->where('peserta_id', $peserta->id_peserta)
            ->whereDate('tanggal', today())
            ->exists();

        if ($sudahAbsen) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah melakukan absensi hari ini.',
            ], 422);
        }

        $jamOperasional = JamOperasional::query()
            ->where('aktif', true)
            ->first();

        if (!$jamOperasional) {
            return response()->json([
                'success' => false,
                'message' => 'Jam operasional belum diaktifkan.',
            ], 422);
        }

        /*
         * Koordinat lokasi kantor.
         * Lebih baik nantinya disimpan di tabel pengaturan perusahaan.
         */
        $latitudeKantor = -6.732000;
        $longitudeKantor = 108.552000;

        /*
         * Radius maksimal dalam meter.
         */
        $radiusMaksimal = 100;

        $jarak = $this->hitungJarak(
            (float) $data['latitude'],
            (float) $data['longitude'],
            $latitudeKantor,
            $longitudeKantor
        );

        if ($jarak > $radiusMaksimal) {
            return response()->json([
                'success' => false,
                'message' => 'Absensi ditolak karena Anda berada di luar lokasi perusahaan.',
                'jarak_meter' => round($jarak, 2),
                'radius_maksimal' => $radiusMaksimal,
            ], 422);
        }

        $jamSekarang = now()->format('H:i:s');

        $statusKehadiran =
            $jamSekarang > $jamOperasional->jam_mulai
                ? 'terlambat'
                : 'hadir';

        $absensi = Absensi::create([
            'peserta_id' => $peserta->id_peserta,
            'tanggal' => today(),
            'jam' => now(),
            'status' => $statusKehadiran,
            'latitude' => $data['latitude'],
            'longitude' => $data['longitude'],
            'jarak_meter' => round($jarak, 2),
        ]);

        return response()->json([
            'success' => true,
            'message' => $statusKehadiran === 'terlambat'
                ? 'Absensi berhasil, tetapi Anda terlambat.'
                : 'Absensi berhasil.',
            'data' => $absensi->load('peserta.user'),
        ], 201);
    }


    public function izin(Request $request): JsonResponse
    {
        return $this->simpanKetidakhadiran($request, 'izin', 'surat_izin', 'surat-izin');
    }

    public function sakit(Request $request): JsonResponse
    {
        return $this->simpanKetidakhadiran($request, 'sakit', 'surat_sakit', 'surat-sakit');
    }

    private function simpanKetidakhadiran(
        Request $request,
        string $status,
        string $field,
        string $directory
    ): JsonResponse {
        $data = $request->validate([
            $field => ['required','file','mimes:pdf,jpg,jpeg,png','max:5120'],
            'keterangan' => ['required','string','max:1000'],
        ]);

        $peserta = PesertaMagang::query()
            ->where('user_id', $request->user()->id_user)
            ->where('status', 'aktif')
            ->firstOrFail();

        if (Absensi::query()->where('peserta_id',$peserta->id_peserta)->whereDate('tanggal',today())->exists()) {
            return response()->json(['success'=>false,'message'=>'Data absensi hari ini sudah tersedia.'],422);
        }

        $path = $request->file($field)->store($directory, 'public');
        $absensi = Absensi::create([
            'peserta_id'=>$peserta->id_peserta,
            'tanggal'=>today(),
            'status'=>$status,
            $field=>$path,
            'keterangan'=>$data['keterangan'],
        ]);

        return response()->json(['success'=>true,'message'=>'Pengajuan '.$status.' berhasil dikirim.','data'=>$absensi],201);
    }

    private function hitungJarak(
        float $latitudePeserta,
        float $longitudePeserta,
        float $latitudeKantor,
        float $longitudeKantor
    ): float {
        $radiusBumi = 6371000;

        $lat1 = deg2rad($latitudePeserta);
        $lat2 = deg2rad($latitudeKantor);

        $selisihLat = deg2rad(
            $latitudeKantor - $latitudePeserta
        );

        $selisihLong = deg2rad(
            $longitudeKantor - $longitudePeserta
        );

        $a = sin($selisihLat / 2) ** 2
            + cos($lat1)
            * cos($lat2)
            * sin($selisihLong / 2) ** 2;

        $c = 2 * atan2(
            sqrt($a),
            sqrt(1 - $a)
        );

        return $radiusBumi * $c;
    }
}