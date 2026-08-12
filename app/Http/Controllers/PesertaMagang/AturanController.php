<?php

namespace App\Http\Controllers\PesertaMagang;

use App\Http\Controllers\Controller;
use App\Models\AturanPerusahaan;

class AturanController extends Controller
{
    public function index()
    {
        $aturan = AturanPerusahaan::query()
            ->where('status', 'aktif')
            ->whereIn('untuk_role', ['magang', 'semua'])
            ->orderBy('nama')
            ->get();

        return view('peserta-magang.aturan.index', compact('aturan'));
    }
}