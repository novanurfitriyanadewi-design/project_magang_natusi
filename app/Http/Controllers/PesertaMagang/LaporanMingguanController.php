<?php

namespace App\Http\Controllers\PesertaMagang;

use App\Http\Controllers\Controller;
use App\Models\LaporanMingguan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LaporanMingguanController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $peserta = $user->pesertaMagang;

        if (! $peserta) {
            return redirect()
                ->route('peserta-magang.dashboard')
                ->with('error', 'Data peserta magang Anda belum terdaftar di sistem. Hubungi admin.');
        }

        $mingguSaatIni = $this->hitungMingguSaatIni($peserta);

        $laporanMingguIni = LaporanMingguan::where('peserta_id', $peserta->id_peserta)
            ->where('minggu_ke', $mingguSaatIni)
            ->first();

        $riwayat = LaporanMingguan::where('peserta_id', $peserta->id_peserta)
            ->orderByDesc('minggu_ke')
            ->paginate(10);

        return view('peserta-magang.laporan-mingguan', compact(
            'mingguSaatIni',
            'laporanMingguIni',
            'riwayat'
        ));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $peserta = $user->pesertaMagang;

        if (! $peserta) {
            return redirect()
                ->route('peserta-magang.dashboard')
                ->with('error', 'Data peserta magang Anda belum terdaftar di sistem. Hubungi admin.');
        }

        $mingguSaatIni = $this->hitungMingguSaatIni($peserta);

        $validated = $request->validate([
            'laporan' => ['required', 'file', 'mimes:pdf,doc,docx', 'max:10240'],
        ]);

        $path = $request->file('laporan')->store('laporan-mingguan', 'public');

        LaporanMingguan::updateOrCreate(
            [
                'peserta_id' => $peserta->id_peserta,
                'minggu_ke'  => $mingguSaatIni,
            ],
            [
                'laporan'          => $path,
                'dikumpulkan_pada' => Carbon::now(),
            ]
        );

        return redirect()
            ->route('peserta-magang.laporan.index')
            ->with('success', "Laporan minggu ke-{$mingguSaatIni} berhasil dikirim.");
    }

    private function hitungMingguSaatIni($peserta): int
    {
        if (! $peserta->tgl_mulai) {
            return 1;
        }

        $selisihHari = Carbon::parse($peserta->tgl_mulai)->diffInDays(Carbon::now());

        return max(1, (int) floor($selisihHari / 7) + 1);
    }
}
