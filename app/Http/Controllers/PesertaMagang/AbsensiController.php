<?php

namespace App\Http\Controllers\PesertaMagang;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class AbsensiController extends Controller
{
    /**
     * Tampilkan halaman presensi harian: form input + riwayat + statistik bulan berjalan.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $pesertaId = $user->pesertaMagang?->id_peserta;

        // 🟢 PERBAIKAN: Redirect ke dashboard, BUKAN back() agar tidak infinite loop
        if (! $pesertaId) {
            return redirect()
                ->route('peserta-magang.dashboard')
                ->with('error', 'Data peserta magang Anda belum terdaftar di sistem. Hubungi admin.');
        }

        $awalBulan = Carbon::now()->startOfMonth();
        $akhirBulan = Carbon::now()->endOfMonth();

        $sudahAbsenHariIni = Absensi::where('peserta_id', $pesertaId)
            ->whereDate('tanggal', Carbon::today())
            ->exists();

        $totalHadir = Absensi::where('peserta_id', $pesertaId)
            ->where('status', 'hadir')
            ->whereBetween('tanggal', [$awalBulan, $akhirBulan])
            ->count();

        $totalSakit = Absensi::where('peserta_id', $pesertaId)
            ->where('status', 'sakit')
            ->whereBetween('tanggal', [$awalBulan, $akhirBulan])
            ->count();

        $totalIzin = Absensi::where('peserta_id', $pesertaId)
            ->where('status', 'izin')
            ->whereBetween('tanggal', [$awalBulan, $akhirBulan])
            ->count();

        $totalHariKerja = Carbon::now()->diffInWeekdays($awalBulan) + 1;

        $riwayat = Absensi::where('peserta_id', $pesertaId)
            ->orderByDesc('tanggal')
            ->paginate(10);

        // Data untuk kalender mini bulan berjalan
        $absensiBulanIni = Absensi::where('peserta_id', $pesertaId)
            ->whereBetween('tanggal', [$awalBulan, $akhirBulan])
            ->get()
            ->keyBy(fn ($item) => Carbon::parse($item->tanggal)->format('Y-m-d'));

        $stats = [
            'total_hadir'      => $totalHadir,
            'total_sakit'      => $totalSakit,
            'total_izin'       => $totalIzin,
            'total_hari_kerja' => $totalHariKerja,
        ];

        return view('peserta-magang.absensi', compact(
            'sudahAbsenHariIni',
            'stats',
            'riwayat',
            'absensiBulanIni'
        ));
    }

    /**
     * Simpan presensi harian (hadir/sakit/izin).
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $pesertaId = $user->pesertaMagang?->id_peserta;

        // 🟢 PERBAIKAN: Redirect ke dashboard jika belum ada relasi peserta
        if (! $pesertaId) {
            return redirect()
                ->route('peserta-magang.dashboard')
                ->with('error', 'Data peserta magang Anda belum terdaftar di sistem. Hubungi admin.');
        }

        $sudahAbsenHariIni = Absensi::where('peserta_id', $pesertaId)
            ->whereDate('tanggal', Carbon::today())
            ->exists();

        if ($sudahAbsenHariIni) {
            return redirect()
                ->route('peserta-magang.absensi.index')
                ->with('error', 'Anda sudah melakukan presensi untuk hari ini.');
        }

        $validated = $request->validate([
            'status'      => ['required', Rule::in(['hadir', 'sakit', 'izin'])],
            'latitude'    => ['required_if:status,hadir', 'nullable', 'numeric'],
            'longitude'   => ['required_if:status,hadir', 'nullable', 'numeric'],
            'jarak_meter' => ['nullable', 'numeric'],
            'keterangan'  => ['nullable', 'string', 'max:500'],
            'bukti'       => [
                'required_if:status,sakit',
                'required_if:status,izin',
                'nullable',
                'file',
                'mimes:pdf,jpg,jpeg,png',
                'max:5120', // 5MB
            ],
        ]);

        $suratSakit = null;
        $suratIzin = null;

        if ($request->hasFile('bukti')) {
            $path = $request->file('bukti')->store('absensi/bukti', 'public');

            if ($validated['status'] === 'sakit') {
                $suratSakit = $path;
            } elseif ($validated['status'] === 'izin') {
                $suratIzin = $path;
            }
        }

        Absensi::create([
            'peserta_id'  => $pesertaId,
            'tanggal'     => Carbon::today(),
            'jam'         => Carbon::now()->format('H:i:s'),
            'status'      => $validated['status'],
            'latitude'    => $validated['latitude'] ?? null,
            'longitude'   => $validated['longitude'] ?? null,
            'jarak_meter' => $validated['jarak_meter'] ?? null,
            'surat_sakit' => $suratSakit,
            'surat_izin'  => $suratIzin,
            'keterangan'  => $validated['keterangan'] ?? null,
        ]);

        return redirect()
            ->route('peserta-magang.absensi.index')
            ->with('success', 'Presensi berhasil dicatat.');
    }
}