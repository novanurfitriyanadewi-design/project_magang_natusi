<?php

namespace App\Http\Controllers;

use App\Models\PesertaMagang;
use Illuminate\Database\Eloquent\Model;

class PesertaMagangController extends ApiCrudController
{
    protected string $modelClass = PesertaMagang::class;

    protected array $with = [
        'user',
        'permintaan',
    ];

    protected array $files = [];

    protected function rules(?Model $model = null): array
    {
        return [
            'user_id' => [
                'required',
                'exists:users,id_user',
            ],

            'permintaan_id' => [
                'nullable',
                'exists:permintaan_magang,id_permintaan',
            ],

            'alamat' => [
                'required',
                'string',
            ],

            'tingkat_pendidikan' => [
                'required',
                'string',
                'max:100',
            ],

            'kelas' => [
                'nullable',
                'string',
                'max:100',
            ],

            'tgl_mulai' => [
                'nullable',
                'date',
            ],

            'tgl_selesai' => [
                'nullable',
                'date',
                'after_or_equal:tgl_mulai',
            ],

            'durasi_magang' => [
                'nullable',
                'string',
                'max:100',
            ],

            'nama_guru' => [
                'nullable',
                'string',
                'max:255',
            ],

            'no_hpguru' => [
                'nullable',
                'string',
                'max:20',
            ],

            'status' => [
                'required',
                'in:aktif,selesai,dibatalkan',
            ],
        ];
    }
}