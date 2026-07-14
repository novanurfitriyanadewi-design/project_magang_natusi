<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\AturanPerusahaan;
use App\Models\JamOperasional;
use App\Models\User;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $totalAdmins = Schema::hasTable('users')
            ? User::query()->where('role', 'admin')->count()
            : 0;

        $totalUsers = Schema::hasTable('users')
            ? User::query()->count()
            : 0;

        $activeRules = Schema::hasTable('aturan_perusahaan')
            ? AturanPerusahaan::query()->where('status', 'aktif')->count()
            : 0;

        $activeSchedule = Schema::hasTable('jam_operasional')
            ? JamOperasional::query()->where('aktif', true)->first()
            : null;

        $latestAdmins = Schema::hasTable('users')
            ? User::query()
                ->where('role', 'admin')
                ->latest()
                ->limit(4)
                ->get(['id_user', 'nama', 'username', 'email', 'created_at'])
            : collect();

        return view('superadmin.dashboard', compact(
            'totalAdmins',
            'totalUsers',
            'activeRules',
            'activeSchedule',
            'latestAdmins',
        ));
    }
}
