<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notifikasi;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotifikasiController extends Controller
{
    /**
     * Display a listing of the resource (Web).
     */
    public function index(Request $request): View
    {
        $query = Notifikasi::with('user');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                  ->orWhere('pesan', 'like', "%{$search}%");
            });
        }

        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        if ($request->filled('status')) {
            $dibaca = $request->status === 'dibaca' ? true : false;
            $query->where('dibaca', $dibaca);
        }

        $notifikasis = $query->latest('id_notifikasi')->paginate(15)->withQueryString();

        return view('admin.notifikasi.index', compact('notifikasis'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('admin.notifikasi.create');
    }

    /**
     * Store a newly created resource in storage (Web).
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id_user'],
            'judul' => ['required', 'string', 'max:255'],
            'pesan' => ['required', 'string'],
            'kategori' => ['required', 'in:pengajuan,pembayaran,penugasan,absensi,akun'],
            'tipe' => ['required', 'in:info,peringatan,sukses'],
            'referensi_id' => ['nullable', 'integer'],
        ]);

        $validated['dibaca'] = false;

        Notifikasi::create($validated);

        return redirect()->route('admin.notifikasi.index')
            ->with('success', 'Notifikasi berhasil dikirim.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Notifikasi $notifikasi): View
    {
        $notifikasi->load('user');
        return view('admin.notifikasi.show', compact('notifikasi'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Notifikasi $notifikasi): View
    {
        $notifikasi->load('user');
        return view('admin.notifikasi.edit', compact('notifikasi'));
    }

    /**
     * Update the specified resource in storage (Web).
     */
    public function update(Request $request, Notifikasi $notifikasi): RedirectResponse
    {
        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id_user'],
            'judul' => ['required', 'string', 'max:255'],
            'pesan' => ['required', 'string'],
            'kategori' => ['required', 'in:pengajuan,pembayaran,penugasan,absensi,akun'],
            'tipe' => ['required', 'in:info,peringatan,sukses'],
            'referensi_id' => ['nullable', 'integer'],
            'dibaca' => ['sometimes', 'boolean'],
        ]);

        $notifikasi->update($validated);

        return redirect()->route('admin.notifikasi.index')
            ->with('success', 'Notifikasi berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Notifikasi $notifikasi): RedirectResponse
    {
        $notifikasi->delete();

        return redirect()->route('admin.notifikasi.index')
            ->with('success', 'Notifikasi berhasil dihapus.');
    }

    // ========================================================================
    // API Methods
    // ========================================================================

    /**
     * Get notifications for the authenticated user (API).
     */
    public function milikSaya(Request $request): JsonResponse
    {
        $notifikasis = Notifikasi::query()
            ->where('user_id', $request->user()->id_user)
            ->latest('id_notifikasi')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $notifikasis,
        ]);
    }

    /**
     * Mark a single notification as read (API).
     */
    public function tandaiDibaca(Request $request, int $id): JsonResponse
    {
        $notifikasi = Notifikasi::query()
            ->where('user_id', $request->user()->id_user)
            ->findOrFail($id);

        $notifikasi->update(['dibaca' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Notifikasi telah dibaca.',
            'data' => $notifikasi,
        ]);
    }

    /**
     * Mark all notifications as read for the authenticated user (API).
     */
    public function tandaiSemuaDibaca(Request $request): JsonResponse
    {
        $updated = Notifikasi::query()
            ->where('user_id', $request->user()->id_user)
            ->where('dibaca', false)
            ->update(['dibaca' => true]);

        return response()->json([
            'success' => true,
            'message' => "{$updated} notifikasi telah ditandai dibaca.",
        ]);
    }

    /**
     * Mark a notification as read via Web (POST from Blade).
     */
    public function tandaiDibacaWeb(Notifikasi $notifikasi): RedirectResponse
    {
        if ($notifikasi->user_id !== auth()->id()) {
            abort(403, 'Akses ditolak.');
        }

        $notifikasi->update(['dibaca' => true]);

        return back()->with('success', 'Notifikasi telah dibaca.');
    }

    /**
     * Mark all notifications as read for the authenticated user via Web.
     */
    public function tandaiSemuaDibacaWeb(): RedirectResponse
    {
        Notifikasi::query()
            ->where('user_id', auth()->id())
            ->where('dibaca', false)
            ->update(['dibaca' => true]);

        return back()->with('success', 'Semua notifikasi telah dibaca.');
    }
}

