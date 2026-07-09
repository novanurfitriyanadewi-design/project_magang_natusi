<?php

namespace App\Http\Controllers;

use App\Models\NominalPembayaran;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;

class NominalPembayaranController extends ApiCrudController
{
    protected string $modelClass = NominalPembayaran::class;

    protected array $with = [];

    protected array $files = [];

    protected function rules(?Model $model = null): array
    {
        return [
            'jumlah_nominal' => [
                'required',
                'integer',
                'min:0',
            ],
        ];
    }

    public function destroy($id): JsonResponse
    {
        $nominalPembayaran = NominalPembayaran::findOrFail($id);

        if ($nominalPembayaran->pembayaran()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Nominal pembayaran tidak dapat dihapus karena sudah digunakan pada data pembayaran.',
            ], 422);
        }

        $nominalPembayaran->delete();

        return response()->json([
            'success' => true,
            'message' => 'Nominal pembayaran berhasil dihapus.',
        ]);
    }
}