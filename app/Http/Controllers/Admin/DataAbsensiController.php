<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\PesertaMagang;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DataAbsensiController extends Controller
{
    public function index(Request $request): View
    {
        $today = now()->toDateString();
        $todayLabel = now()->translatedFormat('l, d F Y');

        $todayTab = in_array($request->query('tab'), ['sudah_absen', 'belum_absen'], true)
            ? $request->query('tab')
            : 'sudah_absen';

        $todaySearch = trim((string) $request->query('today_search', ''));
        $historySearch = trim((string) $request->query('history_search', ''));
        $historyDate = $request->filled('history_date')
            ? (string) $request->query('history_date')
            : '';

        $allowedHistoryStatuses = ['hadir', 'terlambat', 'izin', 'sakit', 'alpa'];
        $historyStatus = in_array($request->query('history_status'), $allowedHistoryStatuses, true)
            ? (string) $request->query('history_status')
            : '';

        $activeParticipantsQuery = PesertaMagang::query()->where('status', 'aktif');
        $totalActiveParticipants = (clone $activeParticipantsQuery)->count();

        $todayAttendanceBase = Absensi::query()
            ->whereDate('tanggal', $today)
            ->whereHas('peserta', fn (Builder $query) => $query->where('status', 'aktif'));

        $totalSudahAbsen = (clone $todayAttendanceBase)->count();
        $totalHadir = (clone $todayAttendanceBase)->where('status', 'hadir')->count();
        $totalTerlambat = (clone $todayAttendanceBase)->where('status', 'terlambat')->count();
        $totalIzinSakit = (clone $todayAttendanceBase)->whereIn('status', ['izin', 'sakit'])->count();
        $totalBelumAbsen = max($totalActiveParticipants - $totalSudahAbsen, 0);

        $todayAttendances = null;
        $todayMissingParticipants = null;

        if ($todayTab === 'sudah_absen') {
            $todayAttendances = Absensi::query()
                ->with(['peserta.user', 'peserta.permintaan'])
                ->whereDate('tanggal', $today)
                ->whereHas('peserta', fn (Builder $query) => $query->where('status', 'aktif'))
                ->when($todaySearch !== '', function (Builder $query) use ($todaySearch) {
                    $query->whereHas('peserta', function (Builder $participantQuery) use ($todaySearch) {
                        $this->applyParticipantSearch($participantQuery, $todaySearch);
                    });
                })
                ->orderByRaw('CASE WHEN jam IS NULL THEN 1 ELSE 0 END')
                ->orderByDesc('jam')
                ->paginate(10, ['*'], 'today_page')
                ->withQueryString();
        } else {
            $todayMissingParticipants = PesertaMagang::query()
                ->with(['user', 'permintaan'])
                ->where('status', 'aktif')
                ->whereDoesntHave('absensi', fn (Builder $query) => $query->whereDate('tanggal', $today))
                ->when($todaySearch !== '', function (Builder $query) use ($todaySearch) {
                    $this->applyParticipantSearch($query, $todaySearch);
                })
                ->orderByDesc('id_peserta')
                ->paginate(10, ['*'], 'today_page')
                ->withQueryString();
        }

        $historyQuery = Absensi::query()
            ->with(['peserta.user', 'peserta.permintaan'])
            ->whereDate('tanggal', '<', $today)
            ->when($historyDate !== '', fn (Builder $query) => $query->whereDate('tanggal', $historyDate))
            ->when($historyStatus !== '', fn (Builder $query) => $query->where('status', $historyStatus))
            ->when($historySearch !== '', function (Builder $query) use ($historySearch) {
                $query->whereHas('peserta', function (Builder $participantQuery) use ($historySearch) {
                    $this->applyParticipantSearch($participantQuery, $historySearch);
                });
            })
            ->orderByDesc('tanggal')
            ->orderByDesc('jam');

        $historyAttendances = $historyQuery
            ->paginate(10, ['*'], 'history_page')
            ->withQueryString();

        return view('admin.absensi.index', compact(
            'today',
            'todayLabel',
            'todayTab',
            'todaySearch',
            'historySearch',
            'historyDate',
            'historyStatus',
            'todayAttendances',
            'todayMissingParticipants',
            'historyAttendances',
            'totalActiveParticipants',
            'totalSudahAbsen',
            'totalHadir',
            'totalTerlambat',
            'totalIzinSakit',
            'totalBelumAbsen'
        ));
    }

    private function applyParticipantSearch(Builder $query, string $search): void
    {
        $query->where(function (Builder $participantQuery) use ($search) {
            $participantQuery
                ->whereHas('user', function (Builder $userQuery) use ($search) {
                    $userQuery
                        ->where('nama', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('university', 'like', "%{$search}%");
                })
                ->orWhereHas('permintaan', function (Builder $applicationQuery) use ($search) {
                    $applicationQuery
                        ->where('nama_sekolah', 'like', "%{$search}%")
                        ->orWhere('no_hp', 'like', "%{$search}%")
                        ->orWhere('jurusan', 'like', "%{$search}%");
                });
        });
    }
}
