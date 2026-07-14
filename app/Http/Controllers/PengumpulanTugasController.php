<?php

namespace App\Http\Controllers;

use App\Models\PengumpulanTugas;
use App\Models\PesertaMagang;
use App\Models\Tugas;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class PengumpulanTugasController extends ApiCrudController
{
    protected string $modelClass = PengumpulanTugas::class;
    protected array $with = [
        'tugas',
        'peserta.user',
    ];
    protected array $files = [
        'file_jawaban' => 'jawaban-tugas',
    ];

    protected function rules(?Model $model = null): array
    {
        return [
            'tugas_id' => [
                'required',
                'exists:tugas,id_tugas',
            ],
            'file_jawaban' => [
                $model ? 'sometimes' : 'required',
                'file',
                'mimes:pdf,doc,docx,xls,xlsx,zip',
                'max:10240',
            ],
        ];
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate($this->rules());
        $tugas = Tugas::findOrFail($data['tugas_id']);

        $peserta = PesertaMagang::query()
            ->where('user_id', $request->user()->id_user)
            ->where('status', 'aktif')
            ->firstOrFail();

        $sudahMengumpulkan = PengumpulanTugas::query()
            ->where('tugas_id', $tugas->id_tugas)
            ->where('peserta_id', $peserta->id_peserta)
            ->exists();
        if ($sudahMengumpulkan) {
            return response()->json([
                'success' => false,
                'message' => 'Tugas ini sudah pernah dikumpulkan.',
            ], 422);
        }

        $path = $request
            ->file('file_jawaban')
            ->store('jawaban-tugas', 'public');

        $status = 'terkumpul';
        if (
            $tugas->pengumpulan &&
            now()->greaterThan(Carbon::parse($tugas->pengumpulan))
        ) {
            $status = 'telat';
        }

        $pengumpulan = PengumpulanTugas::create([
            'tugas_id' => $tugas->id_tugas,
            'peserta_id' => $peserta->id_peserta,
            'file_jawaban' => $path,
            'dikumpulkan_pada' => now(),
            'status' => $status,
        ]);

        return response()->json([
            'success' => true,
            'message' => $status === 'telat'
                ? 'Tugas berhasil dikumpulkan, tetapi terlambat.'
                : 'Tugas berhasil dikumpulkan.',
            'data' => $pengumpulan->load([
                'tugas',
                'peserta.user',
            ]),
        ], 201);
    }

    public function tandaiDinilai(int|string $id): JsonResponse {
        $pengumpulanTugas = PengumpulanTugas::findOrFail($id);
        $pengumpulanTugas->update([
            'status' => 'dinilai',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Tugas telah ditandai sebagai dinilai.',
            'data' => $pengumpulanTugas,
        ]);
    }
}