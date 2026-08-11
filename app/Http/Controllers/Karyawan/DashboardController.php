<?php

namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\Absensi;
use App\Models\Pengumuman;
use App\Models\Resign;
use App\Models\PembayaranKaryawan;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $karyawan = $user->karyawan; // Mengambil data relasi karyawan
        
        $now = Carbon::now();
        $currentMonth = $now->month;
        $currentYear = $now->year;
        $bulanLabel = $now->translatedFormat('F Y');

        $sudahAbsenHariIni = false;
        $absensiBulanIni = collect();

        if ($karyawan) {
            $sudahAbsenHariIni = Absensi::where('absentable_type', get_class($karyawan))
                ->where('absentable_id', $karyawan->id)
                ->whereDate('tanggal', $now->toDateString())
                ->exists();

            $absensiBulanIni = Absensi::where('absentable_type', get_class($karyawan))
                ->where('absentable_id', $karyawan->id)
                ->whereYear('tanggal', $currentYear)
                ->whereMonth('tanggal', $currentMonth)
                ->get();
        }

        $jumlahHadir = $absensiBulanIni->where('status', 'hadir')->count();
        
        $jumlahTelat = $absensiBulanIni->filter(function ($item) {
            if (!$item->jam_masuk) return false;
            return Carbon::parse($item->jam_masuk)->format('H:i:s') > '08:15:00';
        })->count();

        $jumlahIzin = $absensiBulanIni->whereIn('status', ['izin', 'cuti', 'sakit'])->count();

        $totalMenitKerja = $absensiBulanIni->sum(function ($item) {
            if ($item->jam_masuk && $item->jam_keluar) {
                return Carbon::parse($item->jam_masuk)->diffInMinutes(Carbon::parse($item->jam_keluar));
            }
            return 0;
        });

        $jumlahHariMasuk = $absensiBulanIni->whereNotNull('jam_keluar')->count();
        $rataRataMenit = $jumlahHariMasuk > 0 ? ($totalMenitKerja / $jumlahHariMasuk) : 0;
        
        $hours = floor($rataRataMenit / 60);
        $minutes = round($rataRataMenit % 60);
        $rataRataJam = $hours > 0 ? "{$hours}j {$minutes}m" : "0 Jam";
        if ($jumlahHariMasuk > 0 && $minutes == 0) {
            $rataRataJam = "{$hours} Jam";
        }

        $targetHariKerja = 20; 
        $progressPersen = min(round(($jumlahHadir / max($targetHariKerja, 1)) * 100), 100);

        // Status Resign Aktif berdasarkan karyawan_id
        $resignAktif = null;
        if ($karyawan) {
            $resignAktif = Resign::where('karyawan_id', $karyawan->id)
                ->whereIn('status', ['pending', 'diproses', 'menunggu_approval'])
                ->latest()
                ->first();
        }

        $pengumuman = Pengumuman::where('aktif', 1)
            ->latest()
            ->take(3)
            ->get();

             $slipGajiTerakhir = null;
        if ($karyawan) {
            $slipGajiTerakhir = PembayaranKaryawan::where('karyawan_id', $karyawan->id_karyawan)
                ->orderByDesc('periode')
                ->first();
        }


        return view('karyawan.dashboard', compact(
            'user',
            'bulanLabel',
            'sudahAbsenHariIni',
            'jumlahHadir',
            'jumlahTelat',
            'jumlahIzin',
            'rataRataJam',
            'progressPersen',
            'resignAktif',
            'pengumuman'
        ));
    }
}