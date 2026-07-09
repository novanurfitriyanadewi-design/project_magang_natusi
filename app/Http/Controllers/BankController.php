<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use Illuminate\Database\Eloquent\Model;

class BankController extends ApiCrudController
{
    protected string $modelClass = Bank::class;

    protected array $with = [];

    protected array $files = [];

    protected function rules(?Model $model = null): array
    {
        return [
            'nama_bank' => [
                'required',
                'string',
                'max:100',
            ],

            'nama_pemilik' => [
                'required',
                'string',
                'max:255',
            ],

            'no_rekening' => [
                'required',
                'string',
                'max:50',
            ],
        ];
    }
}