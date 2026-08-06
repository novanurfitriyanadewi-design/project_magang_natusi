<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Divisi;
use Illuminate\Http\Request;

class SuperadminDivisiController extends Controller
{
    public function index()
    {
        $divisiList = Divisi::withCount('karyawan')
            ->orderBy('nama_divisi')
            ->paginate(6); // <- ubah dari get() menjadi paginate(6)

        return view('superadmin.divisi.index', compact('divisiList'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_divisi' => ['required', 'string', 'max:255', 'unique:divisi,nama_divisi'],
            'keterangan'  => ['nullable', 'string'],
        ]);

        Divisi::create($validated);

        return back()->with('success', 'Divisi baru berhasil ditambahkan.');
    }

    public function update(Request $request, Divisi $divisi)
    {
        $validated = $request->validate([
            'nama_divisi' => ['required', 'string', 'max:255', 'unique:divisi,nama_divisi,' . $divisi->id_divisi . ',id_divisi'],
            'keterangan'  => ['nullable', 'string'],
        ]);

        $divisi->update($validated);

        return back()->with('success', 'Divisi berhasil diperbarui.');
    }

    public function destroy(Divisi $divisi)
    {
        if ($divisi->karyawan()->exists()) {
            return back()->with('error', 'Divisi tidak bisa dihapus karena masih ada karyawan yang tergabung di dalamnya.');
        }

        $divisi->delete();

        return back()->with('success', 'Divisi berhasil dihapus.');
    }
}