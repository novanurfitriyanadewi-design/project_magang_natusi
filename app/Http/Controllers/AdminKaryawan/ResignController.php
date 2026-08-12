<?php

namespace App\Http\Controllers\AdminKaryawan;

use App\Http\Controllers\Controller;
use App\Models\Resign;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ResignController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search', '');
        $status = $request->query('status', '');

        $query = Resign::with('karyawan');

        if ($search != '') {
            $query->whereHas('karyawan', function ($q) use ($search) {
                $q->where('nama_karyawan', 'like', "%{$search}%")
                  ->orWhere('nip', 'like', "%{$search}%");
            });
        }

        if ($status != '') {
            $query->where('status', $status);
        }

        $resigns = $query->latest()->paginate(10)->withQueryString();

        $totalPengajuan = Resign::count();
        $menunggu = Resign::where('status', 'pending')->count();
        $disetujui = Resign::where('status', 'disetujui')->count();
        $ditolak = Resign::where('status', 'ditolak')->count();

        return view('admin-karyawan.resign.index', compact(
            'resigns',
            'search',
            'status',
            'totalPengajuan',
            'menunggu',
            'disetujui',
            'ditolak'
        ));
    }

    public function approve(Resign $resign)
    {
        $resign->update([
            'status' => 'disetujui',
        ]);

        return redirect()
            ->route('admin-karyawan.resign.index')
            ->with('success', 'Pengajuan resign berhasil disetujui.');
    }

    public function reject(Request $request, Resign $resign)
    {
        $validated = $request->validate([
            'catatan_hrd' => 'required|string|max:500',
        ]);

        $resign->update([
            'status' => 'ditolak',
            'catatan_hrd' => $validated['catatan_hrd'],
        ]);

        return redirect()
            ->route('admin-karyawan.resign.index')
            ->with('success', 'Pengajuan resign berhasil ditolak.');
    }

    public function download(Resign $resign)
    {
        abort_unless($resign->surat_resign_path, 404, 'Karyawan tidak melampirkan surat resign.');
        abort_unless(Storage::disk('local')->exists($resign->surat_resign_path), 404, 'File tidak ditemukan.');

        return Storage::disk('local')->download(
            $resign->surat_resign_path,
            $resign->surat_resign_original_name ?? basename($resign->surat_resign_path)
        );
    }

    public function create(Request $request)
    {
        $karyawan = $request->user()->karyawan;

        abort_unless($karyawan, 403, 'Akun Anda tidak terhubung ke data karyawan.');

        $pengajuanAktif = Resign::where('karyawan_id', $karyawan->id_karyawan)
            ->where('status', 'pending')
            ->latest()
            ->first();

        if ($pengajuanAktif) {
            return redirect()
                ->route('karyawan.resign.show', $pengajuanAktif)
                ->with('info', 'Anda masih memiliki pengajuan resign yang sedang diproses.');
        }

        return view('karyawan.resign.create');
    }

    public function store(Request $request)
    {
        $karyawan = $request->user()->karyawan;

        abort_unless($karyawan, 403, 'Akun Anda tidak terhubung ke data karyawan.');

        $validated = $request->validate([
            'tanggal_efektif' => ['required', 'date', 'after:today'],
            'alasan' => ['required', 'string', 'min:10'],
            'surat_resign' => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:5120'],
        ], [
            'tanggal_efektif.after' => 'Tanggal efektif resign harus setelah hari ini.',
            'alasan.min' => 'Alasan minimal 10 karakter.',
            'surat_resign.mimes' => 'Format surat resign harus PDF, DOC, atau DOCX.',
            'surat_resign.max' => 'Ukuran file surat resign maksimal 5 MB.',
        ]);

        $path = null;
        $originalName = null;

        if ($request->hasFile('surat_resign')) {
            $file = $request->file('surat_resign');
            $originalName = $file->getClientOriginalName();
            $path = $file->store('resign-letters/' . $karyawan->id_karyawan, 'local');
        }

        $resign = Resign::create([
            'karyawan_id' => $karyawan->id_karyawan,
            'alasan' => $validated['alasan'],
            'tanggal_efektif' => $validated['tanggal_efektif'],
            'status' => 'pending',
            'surat_resign_path' => $path,
            'surat_resign_original_name' => $originalName,
        ]);

        return redirect()
            ->route('karyawan.resign.show', $resign)
            ->with('success', 'Pengajuan resign berhasil dikirim dan sedang menunggu persetujuan.');
    }

    public function show(Request $request, Resign $resign)
    {
        $karyawan = $request->user()->karyawan;

        abort_unless($karyawan && $resign->karyawan_id === $karyawan->id_karyawan, 403);

        return view('karyawan.resign.show', compact('resign'));
    }
}
