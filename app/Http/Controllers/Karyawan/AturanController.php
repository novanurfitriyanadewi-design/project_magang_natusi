<?php

namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use App\Models\AturanPerusahaan;

class AturanController extends Controller
{
    public function index()
    {
        $aturanList = AturanPerusahaan::query()
            ->where('status', 'aktif')
            ->whereIn('untuk_role', ['karyawan', 'semua'])
            ->latest()
            ->get();

        return view('karyawan.aturan-perusahaan.index', compact('aturanList'));
    }
}