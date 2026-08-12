<?php

namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use App\Models\Resign;
use Illuminate\Http\Request;

class ResignController extends Controller
{
    public function index(Request $request)
    {
        $resign = Resign::where('user_id', $request->user()->id)
            ->orderByDesc('created_at')
            ->get();

        return view('karyawan.pengajuan.resign', compact('resign'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tanggal_resign' => ['required', 'date', 'after:today'],
            'alasan' => ['required', 'string', 'min:10'],
            'surat' => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:2048'],
        ], [
            'tanggal_resign.after' => 'Tanggal efektif resign harus setelah hari ini.',
            'alasan.min' => 'Alasan minimal 10 karakter.',
        ]);

        $path = null;

        if ($request->hasFile('surat')) {
            $path = $request->file('surat')->store('resign', 'public');
        }

        Resign::create([
            'user_id' => $request->user()->id,
            'tanggal_resign' => $validated['tanggal_resign'],
            'alasan' => $validated['alasan'],
            'surat' => $path,
            'status' => 'pending',
        ]);

        return redirect()
            ->route('karyawan.pengajuan.resign.index')
            ->with('success', 'Pengajuan resign berhasil dikirim dan sedang menunggu persetujuan.');
    }
}
