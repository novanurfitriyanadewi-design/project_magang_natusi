<?php

namespace App\Http\Controllers\AdminKaryawan;

use App\Http\Controllers\Controller;
use App\Models\AturanPerusahaan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class AturanController extends Controller
{
    public function index(Request $request)
    {
        $query = AturanPerusahaan::query();

        if (Schema::hasTable('aturan_perusahaan') && Schema::hasColumn('aturan_perusahaan', 'untuk_role')) {
            $query->whereIn('untuk_role', ['karyawan', 'semua']);
        }

        if ($request->filled('search')) {
            $query->where('nama', 'like', '%' . $request->search . '%');
        }

        $aturanList = $query->latest()->paginate(10)->withQueryString();

        return view('admin-karyawan.aturan.index', compact('aturanList'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama'      => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'status'    => 'required|in:aktif,nonaktif',
        ]);

        $validated['untuk_role'] = 'karyawan';

        AturanPerusahaan::create($validated);

        return redirect()->route('admin-karyawan.aturan.index')
            ->with('success', 'Aturan berhasil ditambahkan.');
    }

    public function update(Request $request, AturanPerusahaan $aturan)
    {
        $validated = $request->validate([
            'nama'      => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'status'    => 'required|in:aktif,nonaktif',
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