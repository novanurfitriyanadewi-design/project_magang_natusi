<?php

namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use App\Models\Absensi;
use App\Models\Pengumuman;
use App\Models\Resign;

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

    public function absensiIndex()
    {
        $user = Auth::user();
        $karyawan = $user->karyawan;

        $now = Carbon::now();
        $sudahAbsenHariIni = false;
        $riwayat = Absensi::where('id', 0)->paginate(15); // hasil kosong default

        if ($karyawan) {
            $absentableType = get_class($karyawan);

            $sudahAbsenHariIni = Absensi::where('absentable_type', $absentableType)
                ->where('absentable_id', $karyawan->id)
                ->whereDate('tanggal', $now->toDateString())
                ->exists();

            $riwayat = Absensi::where('absentable_type', $absentableType)
                ->where('absentable_id', $karyawan->id)
                ->orderByDesc('tanggal')
                ->paginate(15);

            $absensiBulanIni = Absensi::where('absentable_type', $absentableType)
                ->where('absentable_id', $karyawan->id)
                ->whereYear('tanggal', $now->year)
                ->whereMonth('tanggal', $now->month)
                ->get();
        } else {
            $absensiBulanIni = collect();
        }

        $stats = [
            'total_hadir'      => $absensiBulanIni->where('status', 'hadir')->count(),
            'total_sakit'      => $absensiBulanIni->where('status', 'sakit')->count(),
            'total_izin'       => $absensiBulanIni->where('status', 'izin')->count(),
            'total_hari_kerja' => 20, // sesuaikan dengan target/hari kerja efektif bulan berjalan
        ];

        // View diambil dari resources/views/karyawan/absensi.blade.php
        return view('karyawan.absensi', [
            'sudahAbsenHariIni' => $sudahAbsenHariIni,
            'riwayat'           => $riwayat,
            'stats'             => $stats,
            'officeLat'         => (float) config('office.latitude'),
            'officeLng'         => (float) config('office.longitude'),
            'radiusMeters'      => (int) config('office.radius_meters'),
        ]);
    }

    public function absensiStore(Request $request)
    {
        $user = Auth::user();
        $karyawan = $user->karyawan;

        if (!$karyawan) {
            abort(403, 'Data karyawan tidak ditemukan.');
        }

        $now = Carbon::now();
        $absentableType = get_class($karyawan);

        $sudahAbsenHariIni = Absensi::where('absentable_type', $absentableType)
            ->where('absentable_id', $karyawan->id)
            ->whereDate('tanggal', $now->toDateString())
            ->exists();

        if ($sudahAbsenHariIni) {
            return back()->withErrors(['status' => 'Anda sudah melakukan presensi hari ini.']);
        }

        $validated = $request->validate([
            'status'    => 'required|in:hadir,sakit,izin',
            'latitude'  => 'required_if:status,hadir|nullable|numeric',
            'longitude' => 'required_if:status,hadir|nullable|numeric',
            'foto'      => 'required_if:status,hadir|nullable|image|max:5120',
            'bukti'     => 'required_if:status,sakit,izin|nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'keterangan'=> 'nullable|string|max:500',
        ], [
            'latitude.required_if'  => 'Lokasi wajib dideteksi sebelum submit.',
            'longitude.required_if' => 'Lokasi wajib dideteksi sebelum submit.',
            'foto.required_if'      => 'Foto kehadiran wajib diunggah.',
            'bukti.required_if'     => 'Lampiran bukti wajib diunggah.',
        ]);

        $data = [
            'absentable_type' => $absentableType,
            'absentable_id'   => $karyawan->id,
            'tanggal'         => $now->toDateString(),
            'status'          => $validated['status'],
            'keterangan'      => $validated['keterangan'] ?? null,
        ];

        if ($validated['status'] === 'hadir') {
            // Validasi jarak dilakukan di SERVER, jangan percaya klien sepenuhnya
            $officeLat = (float) config('office.latitude');
            $officeLng = (float) config('office.longitude');
            $radius = (int) config('office.radius_meters');

            $distance = $this->haversineDistance(
                (float) $validated['latitude'],
                (float) $validated['longitude'],
                $officeLat,
                $officeLng
            );

            if ($distance > $radius) {
                throw ValidationException::withMessages([
                    'latitude' => 'Anda berada di luar area kantor (' . round($distance) . ' meter dari kantor). Presensi tidak dapat dilakukan.',
                ]);
            }

            $data['latitude'] = $validated['latitude'];
            $data['longitude'] = $validated['longitude'];
            $data['jam_masuk'] = $now->format('H:i:s');

            if ($request->hasFile('foto')) {
                $data['foto'] = $request->file('foto')->store('absensi/foto', 'public');
            }
        } else {
            if ($request->hasFile('bukti')) {
                $data['bukti'] = $request->file('bukti')->store('absensi/bukti', 'public');
            }
        }

        Absensi::create($data);

        return redirect()
            ->route('karyawan.absensi.index')
            ->with('success', 'Presensi berhasil dicatat.');
    }

    /**
     * Menghitung jarak antara dua koordinat (meter) menggunakan rumus Haversine.
     */
    private function haversineDistance(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadius = 6371000; // meter

        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat / 2) ** 2 +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLng / 2) ** 2;

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}