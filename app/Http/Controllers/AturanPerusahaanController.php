<?php

namespace App\Http\Controllers;

use App\Models\AturanPerusahaan;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AturanPerusahaanController extends ApiCrudController
{
    protected string $modelClass = AturanPerusahaan::class;
    protected array $with = [];
    protected array $files = [];
    protected array $searchable = [
        'nama',
        'deskripsi',
    ];

    protected function rules(?Model $model = null): array
    {
        return [
            'nama' => [
                'required',
                'string',
                'max:255',
                Rule::unique(
                    'aturan_perusahaan',
                    'nama',
                )->ignore(
                    $model?->getKey(),
                    $model?->getKeyName() ?? 'id_aturan',
                ),
            ],
            'deskripsi' => [
                'required',
                'string',
                'min:20',
                'max:10000',
            ],
        ];
    }

    protected function prepareDataForStore(
        array $data,
        Request $request,
    ): array {
        $data['status'] = 'aktif';

        return $data;
    }

    protected function prepareDataForUpdate(
        array $data,
        Request $request,
        Model $item,
    ): array {
        $data['status'] = 'aktif';

        return $data;
    }

    public function aktif(): JsonResponse
    {
        $aturan = AturanPerusahaan::query()
            ->where('status', 'aktif')
            ->orderBy('nama')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $aturan,
        ]);
    }
}
