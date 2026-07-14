<?php

namespace App\Http\Controllers;

use App\Models\JamOperasional;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class JamOperasionalController extends ApiCrudController
{
    protected string $modelClass = JamOperasional::class;
    protected array $with = [];
    protected array $files = [];
    protected function rules(?Model $model = null): array
    {
        return [
            'jam_mulai' => [
                'required',
                'date_format:H:i',
            ],

            'jam_selesai' => [
                'required',
                'date_format:H:i',
                'after:jam_mulai',
            ],
        ];
    }

    // Jam operasional hanya boleh diedit jika tidak aktif.
    public function update(
        Request $request,
        int|string $id
    ): JsonResponse {
        $jamOperasional = JamOperasional::findOrFail($id);

        if ($jamOperasional->aktif) {
            return $this->errorResponse(
                message: 'Jam operasional yang sedang aktif tidak dapat diedit.',
                status: 422
            );
        }

        $data = $request->validate(
            $this->rules($jamOperasional)
        );

        $jamOperasional->update($data);

        return $this->successResponse(
            message: 'Jam operasional berhasil diperbarui.',
            data: $jamOperasional->fresh()
        );
    }

    // Mengaktifkan salah satu jam operasional.
    public function aktifkan(
        int|string $id
    ): JsonResponse {
        $jamOperasional = JamOperasional::findOrFail($id);

        if ($jamOperasional->aktif) {
            return $this->errorResponse(
                message: 'Jam operasional tersebut sudah aktif.',
                status: 422
            );
        }

        DB::transaction(function () use ($jamOperasional) {
            JamOperasional::query()
                ->where('aktif', true)
                ->update([
                    'aktif' => false,
                ]);

            $jamOperasional->update([
                'aktif' => true,
            ]);
        });

        return $this->successResponse(
            message: 'Jam operasional berhasil diaktifkan.',
            data: $jamOperasional->fresh()
        );
    }

    // Menonaktifkan jam operasional.
    public function nonaktifkan(
        int|string $id
    ): JsonResponse {
        $jamOperasional = JamOperasional::findOrFail($id);

        if (!$jamOperasional->aktif) {
            return $this->errorResponse(
                message: 'Jam operasional tersebut sudah tidak aktif.',
                status: 422
            );
        }

        $jamOperasional->update([
            'aktif' => false,
        ]);

        return $this->successResponse(
            message: 'Jam operasional berhasil dinonaktifkan.',
            data: $jamOperasional->fresh()
        );
    }

    // Seluruh data jam operasional tidak boleh dihapus.
    protected function canDelete(Model $item): bool
    {
        return false;
    }

    protected function deleteDeniedMessage(Model $item): string
    {
        return 'Data jam operasional tidak dapat dihapus.';
    }
}