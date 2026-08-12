<?php

namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;

class AturanController extends Controller
{
    public function index()
    {
        return view('karyawan.aturan.index');
    }
}