<?php

namespace App\Http\Controllers\AdminPeserta;

use App\Http\Controllers\Controller;
use App\Models\Divisi;
use App\Models\Notifikasi;
use App\Models\Sertifikat;
use App\Models\PesertaMagang;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SertifikatController extends Controller
{
    /**
     * Daftar predikat yang bisa dipilih admin saat menerbitkan sertifikat.
     */
    private const PREDIKAT_OPTIONS = [
        'Sangat Baik',
        'Baik',
        'Cukup',
    ];

    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));
        $status = $request->query('status', '');

        $riwayat = Sertifikat::query()
            ->with(['peserta.user', 'peserta.permintaan', 'divisi'])
            ->when($search !== '', function ($query) use ($search) {
                $query->whereHas('peserta.user', function ($q) use ($search) {
                    $q->where('nama', 'like', "%{$search}%");
                })->orWhere('nomor_sertifikat', 'like', "%{$search}%");
            })
            ->when($status !== '', fn ($query) => $query->where('status', $status))
            ->orderByDesc('tanggal_terbit')
            ->paginate(10)
            ->withQueryString();

        // Peserta yang belum dapat sertifikat, diutamakan yang sudah selesai/keluar.
        $pesertaBisaDisertifikasi = PesertaMagang::query()
            ->with(['user', 'permintaan'])
            ->whereIn('status', ['selesai', 'dibatalkan'])
            ->whereDoesntHave('sertifikat', fn ($q) => $q->where('status', 'terbit'))
            ->orderBy('status')
            ->get();

        $divisiList = Divisi::query()->orderBy('nama_divisi')->get();

        $stats = [
            'total_terbit' => Sertifikat::where('status', 'terbit')->count(),
            'total_dicabut' => Sertifikat::where('status', 'dicabut')->count(),
            'belum_disertifikasi' => $pesertaBisaDisertifikasi->count(),
        ];

        return view('admin-peserta.sertifikat', [
            'riwayat' => $riwayat,
            'pesertaBisaDisertifikasi' => $pesertaBisaDisertifikasi,
            'divisiList' => $divisiList,
            'predikatOptions' => self::PREDIKAT_OPTIONS,
            'stats' => $stats,
            'search' => $search,
            'status' => $status,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'peserta_id' => ['required', 'exists:peserta_magang,id_peserta'],
            'divisi_id' => ['required', 'exists:divisi,id_divisi'],
            'predikat' => ['required', Rule::in(self::PREDIKAT_OPTIONS)],
            'judul' => ['required', 'string', 'max:255'],
            'tanggal_terbit' => ['required', 'date'],
            'catatan' => ['nullable', 'string', 'max:1000'],
        ]);

        $sudahAda = Sertifikat::where('peserta_id', $validated['peserta_id'])
            ->where('status', 'terbit')
            ->exists();

        if ($sudahAda) {
            return redirect()
                ->route('admin-peserta.sertifikat.index')
                ->with('error', 'Peserta ini sudah memiliki sertifikat yang masih berlaku. Cabut sertifikat lama terlebih dahulu jika ingin menerbitkan yang baru.');
        }

        $sertifikat = Sertifikat::create([
            ...$validated,
            'nomor_sertifikat' => $this->buatNomorSertifikat(),
            'diterbitkan_oleh' => auth()->id(),
            'status' => 'terbit',
        ]);

        $peserta = $sertifikat->peserta()->with('user')->first();
        if ($peserta?->user) {
            Notifikasi::create([
                'user_id' => $peserta->user->id_user,
                'judul' => 'Sertifikat Magang Terbit',
                'pesan' => "Selamat! Sertifikat magang Anda (No. {$sertifikat->nomor_sertifikat}) sudah bisa dicetak di halaman Sertifikat Saya.",
                'kategori' => 'sertifikat',
                'tipe' => 'sukses',
                'referensi_id' => $sertifikat->id_sertifikat,
            ]);
        }

        return redirect()
            ->route('admin-peserta.sertifikat.index')
            ->with('success', "Sertifikat {$sertifikat->nomor_sertifikat} berhasil diterbitkan untuk {$peserta?->user?->nama}.");
    }

    public function cabut(Sertifikat $sertifikat): RedirectResponse
    {
        $sertifikat->update(['status' => 'dicabut']);

        return redirect()
            ->route('admin-peserta.sertifikat.index')
            ->with('success', "Sertifikat {$sertifikat->nomor_sertifikat} berhasil dicabut.");
    }

    public function cetak(Sertifikat $sertifikat): View
    {
        $sertifikat->load(['peserta.user', 'peserta.permintaan', 'peserta.jurusan', 'divisi']);

        return view('sertifikat.cetak', ['sertifikat' => $sertifikat]);
    }

    private function buatNomorSertifikat(): string
    {
        $tahun = now()->year;
        $urutan = Sertifikat::whereYear('created_at', $tahun)->count() + 1;

        return sprintf('%04d/SERT-MAGANG/CVN/%s/%d', $urutan, now()->translatedFormat('m'), $tahun);
    }
}
