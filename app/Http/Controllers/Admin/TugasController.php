<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tugas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel; // composer require maatwebsite/excel (opsional, lihat catatan)

class TugasController extends Controller
{
    /**
     * Tampilkan halaman Kelola Tugas Magang beserta daftar tugas.
     */
    public function index(Request $request)
    {
        $tugasList = Tugas::query()
            ->when($request->filled('jenis_tugas'), fn ($q) => $q->where('jenis_tugas', $request->jenis_tugas))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin-tugas', compact('tugasList'));
    }

    /**
     * Simpan tugas baru (dari modal Create).
     */
    public function store(Request $request)
    {
        $validated = $this->validated($request);

        if ($request->hasFile('file_tugas')) {
            $validated['file_tugas'] = $request->file('file_tugas')->store('file-tugas', 'public');
        }

        $validated['user_id'] = auth()->id();
        $validated['status']  = $validated['status'] ?? 'aktif';

        Tugas::create($validated);

        return redirect()
            ->route('admin.tugas.index')
            ->with('success', 'Tugas baru berhasil ditambahkan.');
    }

    /**
     * Perbarui tugas (dari modal Edit).
     */
    public function update(Request $request, Tugas $tugas)
    {
        $validated = $this->validated($request, forUpdate: true);

        if ($request->hasFile('file_tugas')) {
            if ($tugas->file_tugas) {
                Storage::disk('public')->delete($tugas->file_tugas);
            }
            $validated['file_tugas'] = $request->file('file_tugas')->store('file-tugas', 'public');
        }

        $tugas->update($validated);

        return redirect()
            ->route('admin.tugas.index')
            ->with('success', 'Tugas berhasil diperbarui.');
    }

    /**
     * Hapus tugas.
     */
    public function destroy(Tugas $tugas)
    {
        if ($tugas->file_tugas) {
            Storage::disk('public')->delete($tugas->file_tugas);
        }

        $tugas->delete();

        return redirect()
            ->route('admin.tugas.index')
            ->with('success', 'Tugas berhasil dihapus.');
    }

    /**
     * Upload template Excel untuk pembaruan tugas secara massal
     * berdasarkan jenis_tugas (harian / mingguan / akhir).
     *
     * Catatan:
     * - Mengasumsikan package "maatwebsite/excel" terpasang
     *   (composer require maatwebsite/excel) dan class
     *   App\Imports\TugasImport yang memetakan baris excel ke kolom Tugas
     *   (judul, materi, minggu_ke, pengumpulan, dst).
     */
    public function upload(Request $request)
    {
        $request->validate([
            'jenis_tugas'   => ['required', Rule::in(['harian', 'mingguan', 'akhir'])],
            'file_template' => ['required', 'file', 'mimes:xlsx', 'max:10240'],
        ]);

        // Overwrite tugas lama untuk jenis_tugas yang dipilih.
        Tugas::where('jenis_tugas', $request->jenis_tugas)->delete();

        Excel::import(
            new \App\Imports\TugasImport($request->jenis_tugas, auth()->id()),
            $request->file('file_template')
        );

        return redirect()
            ->route('admin.tugas.index')
            ->with('success', 'Template tugas berhasil diunggah dan dipublikasikan.');
    }

    /**
     * Download template Excel kosong untuk diisi admin.
     */
public function downloadPanduan()
{
    return response()->download(
        public_path('template/MATERI DAN TUGAS.xlsx')
    );
}

public function downloadTemplate()
{
    return response()->download(
        public_path('template/TEMPLATE TUGAS.xlsx')
    );
}

    /**
     * Aturan validasi bersama untuk store & update.
     */
    private function validated(Request $request, bool $forUpdate = false): array
    {
        return $request->validate([
            'judul'       => ['required', 'string', 'max:255'],
            'materi'      => ['nullable', 'string'],
            'jenis_tugas' => ['required', Rule::in(['harian', 'mingguan', 'akhir'])],
            'file_tugas'  => ['nullable', 'file', 'mimes:pdf,doc,docx,xlxs', 'max:10240'],
            'instansi' => ['required', 'string', 'max:255'],
            'status'      => [$forUpdate ? 'required' : 'nullable', Rule::in(['aktif', 'nonaktif', 'selesai'])],
        ]);
    }
}