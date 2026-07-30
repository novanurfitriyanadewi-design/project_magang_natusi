<?php

namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use App\Models\Resign;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ResignController extends Controller
{
    /**
     * Tampilkan daftar pengajuan resign milik karyawan yang login.
     */
    public function index()
    {
        $resigns = Resign::where('karyawan_id', Auth::id())
            ->latest()
            ->paginate(10);

        return view('karyawan.resign.index', compact('resigns'));
    }

    /**
     * Tampilkan form pengajuan resign baru.
     */
    public function create()
    {
        // Cegah pengajuan ganda jika masih ada yang berstatus pending
        $existingPending = Resign::where('karyawan_id', Auth::id())
            ->where('status', 'pending')
            ->exists();

        if ($existingPending) {
            return redirect()
                ->route('karyawan.resign.index')
                ->with('error', 'Anda masih memiliki pengajuan resign yang sedang diproses.');
        }

        return view('karyawan.resign.create');
    }

    /**
     * Simpan pengajuan resign baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'alasan' => 'required|string|max:1000',
            'tanggal_efektif' => 'required|date|after:today',
        ]);

        Resign::create([
            'karyawan_id' => Auth::id(),
            'alasan' => $validated['alasan'],
            'tanggal_efektif' => $validated['tanggal_efektif'],
            'status' => 'pending',
        ]);

        return redirect()
            ->route('karyawan.resign.index')
            ->with('success', 'Pengajuan resign berhasil dikirim.');
    }

    /**
     * Tampilkan detail satu pengajuan resign.
     */
    public function show(Resign $resign)
    {
        // Pastikan karyawan hanya bisa lihat pengajuan miliknya sendiri
        abort_if($resign->karyawan_id !== Auth::id(), 403);

        return view('karyawan.resign.show', compact('resign'));
    }

    /**
     * Tampilkan form edit pengajuan resign (hanya jika masih pending).
     */
    public function edit(Resign $resign)
    {
        abort_if($resign->karyawan_id !== Auth::id(), 403);
        abort_if($resign->status !== 'pending', 403, 'Pengajuan yang sudah diproses tidak bisa diubah.');

        return view('karyawan.resign.edit', compact('resign'));
    }

    /**
     * Perbarui pengajuan resign (hanya jika masih pending).
     */
    public function update(Request $request, Resign $resign)
    {
        abort_if($resign->karyawan_id !== Auth::id(), 403);
        abort_if($resign->status !== 'pending', 403, 'Pengajuan yang sudah diproses tidak bisa diubah.');

        $validated = $request->validate([
            'alasan' => 'required|string|max:1000',
            'tanggal_efektif' => 'required|date|after:today',
        ]);

        $resign->update($validated);

        return redirect()
            ->route('karyawan.resign.index')
            ->with('success', 'Pengajuan resign berhasil diperbarui.');
    }

    /**
     * Batalkan/hapus pengajuan resign (hanya jika masih pending).
     */
    public function destroy(Resign $resign)
    {
        abort_if($resign->karyawan_id !== Auth::id(), 403);
        abort_if($resign->status !== 'pending', 403, 'Pengajuan yang sudah diproses tidak bisa dibatalkan.');

        $resign->delete();

        return redirect()
            ->route('karyawan.resign.index')
            ->with('success', 'Pengajuan resign berhasil dibatalkan.');
    }
}