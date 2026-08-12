<?php

namespace App\Http\Controllers\AdminKaryawan;

use App\Http\Controllers\Controller;
use App\Models\AturanPerusahaan;
use Illuminate\Http\Request;

class AturanController extends Controller
{
    public function index(Request $request)
    {
        $query = AturanPerusahaan::query();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('nama', 'like', '%' . $request->search . '%')
                    ->orWhere('deskripsi', 'like', '%' . $request->search . '%');
            });
        }

        $aturanList = $query->latest()->paginate(10)->withQueryString();

        return view('admin-karyawan.aturan-perusahaan.index', compact('aturanList'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'untuk_role' => 'required|in:semua,magang,karyawan',
            'status' => 'required|in:aktif,nonaktif',
            'deskripsi' => 'required|string',
        ]);

        AturanPerusahaan::create($validated);

        return redirect()->route('admin-karyawan.aturan.index')
            ->with('success', 'Aturan berhasil ditambahkan.');
    }

    public function update(Request $request, AturanPerusahaan $aturan)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'untuk_role' => 'required|in:semua,magang,karyawan',
            'status' => 'required|in:aktif,nonaktif',
            'deskripsi' => 'required|string',
        ]);

        $aturan->update($validated);

        return redirect()->route('admin-karyawan.aturan.index')
            ->with('success', 'Aturan berhasil diperbarui.');
    }

    public function destroy(AturanPerusahaan $aturan)
    {
        $aturan->delete();

        return redirect()->route('admin-karyawan.aturan.index')
            ->with('success', 'Aturan berhasil dihapus.');
    }
}