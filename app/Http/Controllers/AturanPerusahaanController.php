<?php

namespace App\Http\Controllers;

use App\Models\AturanPerusahaan;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AturanPerusahaanController extends ApiCrudController
{
    protected string $modelClass = AturanPerusahaan::class;

    protected array $with = [];

    protected array $files = [];

    protected function rules(?Model $model = null): array
    {
        return [
            'nama' => [
                'required',
                'string',
                'max:255',
            ],

            'kategori' => [
                'required',
                'string',
                'max:100',
            ],

            'deskripsi' => [
                'required',
                'string',
            ],

            'status' => [
                'sometimes',
                'in:aktif,nonaktif',
            ],
        ];
    }

    public function update(
        Request $request,
        int|string $id
    ): JsonResponse {
        $aturanPerusahaan = AturanPerusahaan::findOrFail($id);

        $data = $request->validate(
            $this->rules($aturanPerusahaan)
        );

        $aturanPerusahaan->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Aturan perusahaan berhasil diperbarui.',
            'data' => $aturanPerusahaan->fresh(),
        ]);
    }

    public function destroy(
        int|string $id
    ): JsonResponse {
        $aturanPerusahaan = AturanPerusahaan::findOrFail($id);

        $aturanPerusahaan->delete();

        return response()->json([
            'success' => true,
            'message' => 'Aturan perusahaan berhasil dihapus.',
        ]);
    }

    public function aktif(): JsonResponse
    {
        $aturan = AturanPerusahaan::query()
            ->where('status', 'aktif')
            ->orderBy('kategori')
            ->orderBy('nama')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $aturan,
        ]);
    }
}