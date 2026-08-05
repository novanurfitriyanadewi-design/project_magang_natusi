<?php

namespace App\Http\Controllers;

use App\Models\Notifikasi;
use Illuminate\Support\Facades\Auth;

class NotifikasiController extends Controller
{
    public function markAllRead()
    {
        Notifikasi::where('user_id', Auth::id())
            ->where('dibaca', 0)
            ->update(['dibaca' => 1]);

        return back();
    }
}

