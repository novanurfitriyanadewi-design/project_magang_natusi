<?php

namespace App\Http\Controllers\AdminKaryawan;

use App\Http\Controllers\Controller;
use App\Models\Notifikasi;
use App\Models\Resign;
use App\Models\User;
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

    public function adminShow(Resign $resign)
    {
        $resign->load('karyawan.divisi');

        return view('admin-karyawan.resign.show', compact('resign'));
    }

    public function approve(Resign $resign)
    {
        $resign->update([
            'status' => 'disetujui',
        ]);

        $this->kirimNotifikasiKeKaryawan(
            $resign,
            'Pengajuan Resign Diterima',
            'Pengajuan resign Anda telah disetujui. Terima kasih atas kontribusi Anda selama bekerja di perusahaan.'
        );

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

        $this->kirimNotifikasiKeKaryawan(
            $resign,
            'Pengajuan Resign Ditolak',
            'Pengajuan resign Anda ditolak. Catatan: ' . $validated['catatan_hrd']
        );

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

        $this->kirimNotifikasiKeAdminKaryawan(
            $resign,
            'Pengajuan Resign Baru',
            sprintf(
                '%s mengajukan resign dan menunggu persetujuan HRD.',
                $karyawan->nama_karyawan
            )
        );

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

    private function kirimNotifikasiKeAdminKaryawan(Resign $resign, string $judul, string $pesan): void
    {
        $adminIds = User::query()
            ->whereIn('role', ['admin', 'admin_karyawan'])
            ->pluck('id_user');

        foreach ($adminIds as $adminId) {
            Notifikasi::query()->create([
                'user_id' => $adminId,
                'judul' => $judul,
                'pesan' => $pesan,
                'kategori' => 'pengajuan',
                'tipe' => 'info',
                'referensi_id' => $resign->getKey(),
                'dibaca' => false,
            ]);
        }
    }

    private function kirimNotifikasiKeKaryawan(Resign $resign, string $judul, string $pesan): void
    {
        $userId = $resign->karyawan?->user_id ?? $resign->karyawan?->user?->id_user;

        if (! $userId) {
            return;
        }

        Notifikasi::query()->create([
            'user_id' => $userId,
            'judul' => $judul,
            'pesan' => $pesan,
            'kategori' => 'pengajuan',
            'tipe' => 'sukses',
            'referensi_id' => $resign->getKey(),
            'dibaca' => false,
        ]);
    }
}
