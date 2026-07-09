<?php

namespace App\Http\Controllers;

use App\Imports\TugasImport;
use App\Models\Tugas;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class TugasController extends ApiCrudController
{
    protected string $modelClass = Tugas::class;

    protected array $with = [
        'user',
    ];

    protected array $files = [
        'file_tugas' => 'tugas',
    ];

    protected function rules(?Model $model = null): array
    {
        return [
                        'judul' => 'required|string|max:255',
            'materi' => 'nullable|string',
            'jenis_tugas' => 'required|in:harian,mingguan,akhir',
            'minggu_ke' => 'nullable|integer|min:1',
            'file_tugas' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx|max:5120',
            'pengumpulan' => 'nullable|date',
            'status' => 'sometimes|in:aktif,nonaktif,selesai',
        ];
    }

    protected function prepareDataForStore(array $data, Request $request): array
    {
        $data['user_id'] = $request->user()->id_user;
        $data['status'] = $data['status'] ?? 'aktif';
        return $data;
    }

    protected function prepareDataForUpdate(array $data, Request $request, Model $item): array
    {
        unset($data['user_id']);
        return $data;
    }

    public function importExcel(Request $request): JsonResponse
    {
        $request->validate([
            'file_excel' => [
                'required',
                'file',
                'mimes:xlsx,xls,csv',
                'max:5120',
            ],
        ]);

        Excel::import(
            new TugasImport($request->user()->id_user),
            $request->file('file_excel')
        );

        return response()->json([
            'success' => true,
            'message' => 'Data tugas berhasil diimpor dari Excel.',
        ]);
    }
}