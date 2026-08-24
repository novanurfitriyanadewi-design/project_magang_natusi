<?php

namespace App\Http\Controllers\AdminPeserta;

use App\Exports\TemplatePesertaMagangExport;
use App\Http\Controllers\Controller;
use App\Imports\PesertaMagangImport;
use App\Models\PesertaMagang;
use App\Support\JurusanKategori;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class PesertaMagangController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim($request->string('search')->toString());
        $status = $request->string('status', 'semua')->toString();

        $query = PesertaMagang::query()
            ->with(['user', 'permintaan', 'jurusan', 'permintaan.pesertas.user']);

        if ($search !== '') {
            $query->where(function ($query) use ($search) {
                $query
                    ->where('alamat', 'like', "%{$search}%")
                    ->orWhere('tingkat_pendidikan', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery
                            ->where('nama', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('username', 'like', "%{$search}%");
                    })
                    ->orWhereHas('permintaan', function ($applicationQuery) use ($search) {
                        $applicationQuery
                            ->where('nama_sekolah', 'like', "%{$search}%")
                            ->orWhere('jurusan', 'like', "%{$search}%")
                            ->orWhere('no_induk', 'like', "%{$search}%");
                    });
            });
        }

        if ($status === 'aktif') {
            $query->where('status', 'aktif');
        } elseif ($status === 'nonaktif') {
            $query->where('status', '!=', 'aktif');
        }

        $peserta = $query
            ->latest('id_peserta')
            ->paginate(10)
            ->withQueryString();

        $stats = [
            'total' => PesertaMagang::query()->count(),
            'aktif' => PesertaMagang::query()->where('status', 'aktif')->count(),
            'nonaktif' => PesertaMagang::query()->where('status', '!=', 'aktif')->count(),
        ];

        $jurusanOptions = \App\Models\Jurusan::query()
            ->aktif()
            ->orderBy('tingkat')
            ->orderBy('nama_jurusan')
            ->get();

        return view('admin-peserta.peserta_magang', compact(
            'peserta',
            'stats',
            'search',
            'status',
            'jurusanOptions',
        ));
    }

    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'file_excel' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:10240'],
        ], [
            'file_excel.required' => 'Pilih file Excel terlebih dahulu.',
            'file_excel.mimes' => 'File harus berformat XLSX, XLS, atau CSV.',
            'file_excel.max' => 'Ukuran file Excel maksimal 10 MB.',
        ]);

        $import = new PesertaMagangImport();

        try {
            DB::transaction(function () use ($import, $request) {
                Excel::import($import, $request->file('file_excel'));
            });
        } catch (\Illuminate\Validation\ValidationException $exception) {
            throw $exception;
        } catch (\Throwable $exception) {
            report($exception);

            return back()
                ->withInput()
                ->with('error', 'Import gagal. Pastikan struktur kolom sesuai template dan tidak ada data yang bertabrakan.');
        }

        return redirect()
            ->route('admin-peserta.peserta.index')
            ->with(
                'success',
                "Import selesai: {$import->importedCount()} peserta baru ditambahkan, {$import->updatedCount()} data diperbarui, dan {$import->skippedCount()} baris kosong/rusak dilewati."
            );
    }

    public function downloadTemplatePeserta()
    {
        $file = public_path('template/peserta magang.xlsx');

        if (!file_exists($file)) {
            abort(404, 'File template tidak ditemukan.');
        }

        return response()->download($file, 'peserta magang.xlsx');
    }

    public function show(PesertaMagang $pesertaMagang): View
    {
        $pesertaMagang->load([
            'user',
            'jurusan',
            'permintaan',
            'permintaan.pesertas.user',
        ]);

        return view('admin-peserta.peserta_show', [
            'peserta' => $pesertaMagang,
        ]);
    }

    public function updateStatus(Request $request, PesertaMagang $pesertaMagang): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['aktif', 'nonaktif'])],
        ]);

        $pesertaMagang->update([
            'status' => $validated['status'] === 'aktif' ? 'aktif' : 'selesai',
        ]);

        return back()->with(
            'success',
            $validated['status'] === 'aktif'
                ? 'Peserta berhasil diaktifkan.'
                : 'Peserta berhasil dinonaktifkan.'
        );
    }

    public function update(Request $request, PesertaMagang $pesertaMagang): RedirectResponse
    {
        $validated = $request->validate([
            'alamat' => ['required', 'string', 'max:1000'],
            'tingkat_pendidikan' => ['required', Rule::in(['SMK', 'Universitas'])],
            'kelas' => ['nullable', 'string', 'max:100'],
            'jurusan_id' => ['nullable', 'exists:jurusan,id_jurusan'],
            'tgl_mulai' => ['nullable', 'date'],
            'tgl_selesai' => ['nullable', 'date', 'after_or_equal:tgl_mulai'],
            'durasi_magang' => ['nullable', 'string', 'max:100'],
            'nama_guru' => ['nullable', 'string', 'max:255'],
            'no_hpguru' => ['nullable', 'string', 'max:20'],
            'status' => ['required', Rule::in(['aktif', 'selesai', 'dibatalkan'])],
        ]);

        if (!empty($validated['tgl_mulai']) && !empty($validated['tgl_selesai'])) {
            $bulanMagang = Carbon::parse($validated['tgl_mulai'])
                ->diffInMonths(Carbon::parse($validated['tgl_selesai']));

            $jurusan = !empty($validated['jurusan_id'])
                ? JurusanKategori::cariById((int) $validated['jurusan_id'])
                : null;

            $pesanDurasi = JurusanKategori::validasiDurasi($jurusan, $bulanMagang);
            if ($pesanDurasi !== null) {
                throw ValidationException::withMessages([
                    'tgl_selesai' => $pesanDurasi,
                ]);
            }
        }

        $pesertaMagang->update($validated);

        return redirect()
            ->route('admin-peserta.peserta.index')
            ->with('success', 'Data peserta berhasil diperbarui.');
    }

    public function destroy(PesertaMagang $pesertaMagang): RedirectResponse
    {
        $pesertaMagang->delete();

        return redirect()
            ->route('admin-peserta.peserta.index')
            ->with('success', 'Peserta berhasil dihapus.');
    }
}
