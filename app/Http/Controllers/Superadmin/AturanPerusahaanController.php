<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\AturanPerusahaan;
use Illuminate\Http\Request;

class AturanPerusahaanController extends Controller
{
    public function index()
    {
        $aturan = AturanPerusahaan::orderBy('nama')->get();
        return view('superadmin.aturan.index', compact('aturan'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'status' => 'nullable|in:aktif,nonaktif',
        ]);

        AturanPerusahaan::create($data);

        return redirect()->route('superadmin.aturan.index')->with('success', 'Aturan berhasil ditambahkan.');
    }

    public function update(Request $request, AturanPerusahaan $aturan)
    {
        $data = $request->validate([
            'nama' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'status' => 'nullable|in:aktif,nonaktif',
        ]);

        $aturan->update($data);

        return redirect()->route('superadmin.aturan.index')->with('success', 'Aturan berhasil diperbarui.');
    }

    public function destroy(AturanPerusahaan $aturan)
    {
        $aturan->delete();
        return back()->with('success', 'Aturan berhasil dihapus.');
    }
}