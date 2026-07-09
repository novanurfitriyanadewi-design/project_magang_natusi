<?php

namespace App\Imports;

use App\Models\Tugas;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class TugasImport implements ToModel, WithHeadingRow, WithValidation
{
    public function __construct(
        private readonly int $userId
    ) {
    }

    public function model(array $row): Tugas
    {
        return new Tugas([
            'user_id' => $this->userId,
            'judul' => $row['judul'],
            'materi' => $row['materi'] ?? null,
            'jenis_tugas' => $row['jenis_tugas'],
            'minggu_ke' => $row['minggu_ke'] ?? null,
            'pengumpulan' => $row['tanggal_pengumpulan'] ?? null,
            'status' => $row['status'] ?? 'aktif',
        ]);
    }

    public function rules(): array
    {
        return [
            '*.judul' => [
                'required',
                'string',
                'max:255',
            ],

            '*.materi' => [
                'nullable',
                'string',
            ],

            '*.jenis_tugas' => [
                'required',
                Rule::in([
                    'harian',
                    'mingguan',
                    'akhir',
                ]),
            ],

            '*.minggu_ke' => [
                'nullable',
                'integer',
                'min:1',
            ],

            '*.tanggal_pengumpulan' => [
                'nullable',
                'date',
            ],

            '*.status' => [
                'nullable',
                Rule::in([
                    'aktif',
                    'nonaktif',
                    'selesai',
                ]),
            ],
        ];
    }
}