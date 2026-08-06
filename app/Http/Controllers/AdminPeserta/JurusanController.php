<?php

namespace App\Http\Controllers\AdminPeserta;

use App\Http\Controllers\Controller;
use App\Models\Jurusan;
use App\Support\JurusanKategori;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class JurusanController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));

        $jurusanList = Jurusan::query()
            ->withCount('pesertaMagang')
            ->when($search !== '', function ($query) use ($search) {
                $query->where('nama_jurusan', 'like', "%{$search}%");
            })
            ->orderBy('tingkat')
            ->orderBy('nama_jurusan')
            ->get();

        return view('admin-peserta.jurusan', [
            'jurusanList' => $jurusanList,
            'search' => $search,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->rules());

        Jurusan::query()->create($this->normalize($validated));
        JurusanKategori::lupakanCache();

        return redirect()
            ->route('admin-peserta.jurusan.index')
            ->with('success', "Jurusan {$validated['nama_jurusan']} berhasil ditambahkan.");
    }

    public function update(Request $request, Jurusan $jurusan): RedirectResponse
    {
        $validated = $request->validate($this->rules($jurusan));

        $jurusan->update($this->normalize($validated));
        JurusanKategori::lupakanCache();

        return redirect()
            ->route('admin-peserta.jurusan.index')
            ->with('success', "Jurusan {$jurusan->nama_jurusan} berhasil diperbarui.");
    }

    public function destroy(Jurusan $jurusan): RedirectResponse
    {
        if ($jurusan->pesertaMagang()->exists()) {
            return redirect()
                ->route('admin-peserta.jurusan.index')
                ->with('error', "Jurusan {$jurusan->nama_jurusan} tidak dapat dihapus karena masih dipakai oleh data peserta magang. Nonaktifkan saja jurusan ini jika sudah tidak dipakai lagi.");
        }

        $nama = $jurusan->nama_jurusan;
        $jurusan->delete();
        JurusanKategori::lupakanCache();

        return redirect()
            ->route('admin-peserta.jurusan.index')
            ->with('success', "Jurusan {$nama} berhasil dihapus.");
    }

    private function rules(?Jurusan $jurusan = null): array
    {
        $uniqueNama = Rule::unique('jurusan', 'nama_jurusan')
            ->where(fn ($query) => $query->where('tingkat', request('tingkat')));
        $uniqueKode = Rule::unique('jurusan', 'kode');

        if ($jurusan) {
            $uniqueNama->ignore($jurusan->id_jurusan, 'id_jurusan');
            $uniqueKode->ignore($jurusan->id_jurusan, 'id_jurusan');
        }

        return [
            'nama_jurusan' => ['required', 'string', 'max:100', $uniqueNama],
            'tingkat' => ['required', Rule::in(['smk', 'kuliah'])],
            'kode' => ['required', 'string', 'max:20', 'regex:/^[A-Za-z0-9]+$/', $uniqueKode],
            'durasi_min_bulan' => ['required', 'integer', 'min:1', 'max:24'],
            'durasi_max_bulan' => ['nullable', 'integer', 'min:1', 'max:24', 'gte:durasi_min_bulan'],
            'keterangan' => ['nullable', 'string', 'max:500'],
            'status' => ['required', Rule::in(['aktif', 'nonaktif'])],
        ];
    }

    private function normalize(array $validated): array
    {
        $validated['kode'] = mb_strtoupper(trim($validated['kode']));
        $validated['nama_jurusan'] = trim($validated['nama_jurusan']);
        $validated['durasi_max_bulan'] = $validated['durasi_max_bulan'] ?? null;
        $validated['keterangan'] = $validated['keterangan'] ?? null;

        return $validated;
    }
}
