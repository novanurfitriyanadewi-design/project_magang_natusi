<?php

namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use App\Models\Cuti;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CutiController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $karyawan = $user->karyawan;

        $riwayat = collect();
        if ($karyawan) {
            $riwayat = Cuti::where('karyawan_id', $karyawan->id_karyawan)->latest()->get();
        }

        return view('karyawan.cuti.index', compact('karyawan', 'riwayat'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $karyawan = $user->karyawan;

        abort_unless($karyawan, 403, 'Data karyawan tidak ditemukan.');

        $validated = $request->validate([
            'jenis_cuti' => ['required', 'in:tahunan,sakit,melahirkan,penting,lainnya'],
            'tanggal_mulai' => ['required', 'date', 'after_or_equal:today'],
            'tanggal_selesai' => ['required', 'date', 'after_or_equal:tanggal_mulai'],
            'alasan' => ['required', 'string', 'max:1000'],
            'bukti_pendukung' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
        ], [
            'tanggal_mulai.after_or_equal' => 'Tanggal mulai tidak boleh sebelum hari ini.',
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai tidak boleh sebelum tanggal mulai.',
        ]);

        $path = null;
        if ($request->hasFile('bukti_pendukung')) {
            $path = $request->file('bukti_pendukung')->store('bukti-cuti', 'public');
        }

        Cuti::create([
            'karyawan_id' => $karyawan->id_karyawan, 
            'jenis_cuti' => $validated['jenis_cuti'],
            'tanggal_mulai' => $validated['tanggal_mulai'],
            'tanggal_selesai' => $validated['tanggal_selesai'],
            'alasan' => $validated['alasan'],
            'bukti_pendukung' => $path,
            'status' => 'pending',
        ]);

        return back()->with('success', 'Pengajuan cuti berhasil dikirim dan menunggu persetujuan HRD.');
    }
}