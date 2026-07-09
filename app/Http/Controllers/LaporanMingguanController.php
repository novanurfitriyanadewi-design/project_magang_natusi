<?php

namespace App\Http\Controllers;

use App\Models\LaporanMingguan;
use App\Models\PesertaMagang;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class LaporanMingguanController extends ApiCrudController
{
    protected string $modelClass = LaporanMingguan::class;

    protected array $with = [
        'peserta.user',
    ];

    protected array $files = [
        'laporan' => 'laporan-mingguan',
    ];

    protected function rules(?Model $model = null): array
    {
        return [
            'laporan' => [
                $model ? 'sometimes' : 'required',
                'file',
                'mimes:pdf,doc,docx',
                'max:10240',
            ],
        ];
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate($this->rules());

        $peserta = PesertaMagang::query()
            ->where('user_id', $request->user()->id_user)
            ->where('status', 'aktif')
            ->firstOrFail();

        if (!$peserta->tgl_mulai) {
            return response()->json([
                'success' => false,
                'message' => 'Tanggal mulai magang peserta belum ditentukan.',
            ], 422);
        }

        $tanggalMulai = Carbon::parse($peserta->tgl_mulai)
            ->startOfDay();

        $hariIni = now()->startOfDay();

        if ($hariIni->lessThan($tanggalMulai)) {
            return response()->json([
                'success' => false,
                'message' => 'Masa magang peserta belum dimulai.',
            ], 422);
        }

        $mingguKe = intdiv(
            $tanggalMulai->diffInDays($hariIni),
            7
        ) + 1;

        $sudahAda = LaporanMingguan::query()
            ->where('peserta_id', $peserta->id_peserta)
            ->where('minggu_ke', $mingguKe)
            ->exists();

        if ($sudahAda) {
            return response()->json([
                'success' => false,
                'message' =>
                    "Laporan minggu ke-{$mingguKe} sudah pernah dikumpulkan.",
            ], 422);
        }

        $path = $request
            ->file('laporan')
            ->store('laporan-mingguan', 'public');

        $laporan = LaporanMingguan::create([
            'peserta_id' => $peserta->id_peserta,
            'minggu_ke' => $mingguKe,
            'laporan' => $path,
            'dikumpulkan_pada' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' =>
                "Laporan minggu ke-{$mingguKe} berhasil dikumpulkan.",
            'data' => $laporan->load('peserta.user'),
        ], 201);
    }
}