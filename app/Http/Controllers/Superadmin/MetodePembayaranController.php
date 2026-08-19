<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Bank;
use App\Models\NominalPembayaran;
use App\Models\RiwayatMetodePembayaran;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class MetodePembayaranController extends Controller
{
    private const QRIS_BANK_NAME = 'QRIS';
    private const QRIS_ACCOUNT = 'QRIS-CV-NATUSI';

    public function index(Request $request): View
    {
        $nominal = NominalPembayaran::query()
            ->latest('id_nominal')
            ->first();

        $qris = Bank::query()
            ->where('nama_bank', self::QRIS_BANK_NAME)
            ->where('no_rekening', self::QRIS_ACCOUNT)
            ->first();

        $histories = RiwayatMetodePembayaran::query()
            ->with('user:id_user,nama')
            ->latest('id_riwayat')
            ->limit(30)
            ->get();

        return view('superadmin.metode-pembayaran', [
            'nominal' => $nominal,
            'qris' => $qris,
            'histories' => $histories,
        ]);
    }

    public function updateNominal(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'jumlah_nominal' => ['required', 'integer', 'min:1000', 'max:999999999'],
        ], [
            'jumlah_nominal.required' => 'Jumlah pembayaran wajib diisi.',
            'jumlah_nominal.integer' => 'Jumlah pembayaran harus berupa angka bulat.',
            'jumlah_nominal.min' => 'Jumlah pembayaran minimal Rp1.000.',
            'jumlah_nominal.max' => 'Jumlah pembayaran terlalu besar.',
        ]);

        DB::transaction(function () use ($validated): void {
            $nominal = NominalPembayaran::query()
                ->latest('id_nominal')
                ->lockForUpdate()
                ->first();

            $oldData = $nominal?->only(['id_nominal', 'jumlah_nominal']);

            if ($nominal) {
                $nominal->update(['jumlah_nominal' => $validated['jumlah_nominal']]);
            } else {
                $nominal = NominalPembayaran::query()->create([
                    'jumlah_nominal' => $validated['jumlah_nominal'],
                ]);
            }

            $this->recordHistory(
                action: $oldData ? 'ubah' : 'tambah',
                entity: 'nominal',
                description: 'Jumlah pembayaran pendaftaran/administrasi diperbarui.',
                oldData: $oldData,
                newData: $nominal->fresh()->only(['id_nominal', 'jumlah_nominal']),
            );
        });

        return redirect()
            ->route('superadmin.metode-pembayaran.index')
            ->with('success', 'Jumlah pembayaran berhasil disimpan.');
    }

    /**
     * QRIS dibuat sebagai satu metode pembayaran tunggal.
     * Record Bank di bawah hanya dipakai internal agar tetap kompatibel dengan
     * struktur tabel pembayaran lama yang mewajibkan id_bank.
     */
    public function storeQris(Request $request): RedirectResponse
    {
        $request->validate([
            'qris_image' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:4096'],
        ], [
            'qris_image.required' => 'Pilih gambar kode QR terlebih dahulu.',
            'qris_image.image' => 'File QR harus berupa gambar.',
            'qris_image.mimes' => 'Format QR harus JPG, JPEG, atau PNG.',
            'qris_image.max' => 'Ukuran gambar QR maksimal 4 MB.',
        ]);

        $qris = Bank::query()->firstOrCreate(
            [
                'nama_bank' => self::QRIS_BANK_NAME,
                'no_rekening' => self::QRIS_ACCOUNT,
            ],
            ['nama_pemilik' => 'CV NATUSI']
        );

        $oldImage = $qris->qris_image;

        if ($oldImage) {
            Storage::disk('public')->delete($oldImage);
        }

        $path = $request->file('qris_image')->store('qris', 'public');
        $qris->update(['qris_image' => $path]);

        $this->recordHistory(
            action: $oldImage ? 'ubah' : 'tambah',
            entity: 'qris',
            description: $oldImage ? 'Kode QR pembayaran diganti.' : 'Kode QR pembayaran diunggah.',
            oldData: $oldImage ? ['qris_image' => $oldImage] : null,
            newData: ['qris_image' => $path],
        );

        return redirect()
            ->route('superadmin.metode-pembayaran.index')
            ->with('success', 'Kode QR pembayaran berhasil disimpan.');
    }

    public function destroyQris(): RedirectResponse
    {
        $qris = Bank::query()
            ->where('nama_bank', self::QRIS_BANK_NAME)
            ->where('no_rekening', self::QRIS_ACCOUNT)
            ->first();

        if ($qris?->qris_image) {
            $oldImage = $qris->qris_image;
            Storage::disk('public')->delete($oldImage);
            $qris->update(['qris_image' => null]);

            $this->recordHistory(
                action: 'hapus',
                entity: 'qris',
                description: 'Kode QR pembayaran dihapus.',
                oldData: ['qris_image' => $oldImage],
                newData: null,
            );
        }

        return redirect()
            ->route('superadmin.metode-pembayaran.index')
            ->with('success', 'Kode QR pembayaran berhasil dihapus.');
    }

    private function recordHistory(
        string $action,
        string $entity,
        string $description,
        ?array $oldData,
        ?array $newData,
    ): void {
        RiwayatMetodePembayaran::query()->create([
            'user_id' => auth()->id(),
            'aksi' => $action,
            'entitas' => $entity,
            'deskripsi' => $description,
            'data_lama' => $oldData,
            'data_baru' => $newData,
        ]);
    }
}
