<?php

namespace App\Http\Controllers\AdminPeserta;

use App\Http\Controllers\Controller;
use App\Models\Notifikasi;
use App\Models\PesertaMagang;
use App\Models\PenugasanPeserta;
use App\Models\Tugas;
use App\Services\PenugasanTemplateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class TugasController extends Controller
{
    public function index(Request $request)
    {
        return view('admin-peserta.admin-tugas');
    }

    public function store(Request $request)
    {
        $validated = $this->validated($request);

        if ($request->hasFile('file_tugas')) {
            $validated['file_tugas'] = $request->file('file_tugas')
                ->store('file-tugas', 'public');
        }

        $validated['user_id'] = auth()->id();
        $validated['status'] = $validated['status'] ?? 'aktif';
        $validated['instansi'] = strtolower($validated['instansi']);

        $tugas = Tugas::create($validated);

        // Auto-assign ke semua peserta magang yang aktif.
        $pesertaAktif = PesertaMagang::query()
            ->with('user')
            ->where('status', 'aktif')
            ->get();

        $rows = $pesertaAktif->map(fn ($peserta) => [
            'tugas_id'   => $tugas->id_tugas,
            'peserta_id' => $peserta->id_peserta,
            'created_at' => now(),
            'updated_at' => now(),
        ])->all();

        PenugasanPeserta::insert($rows);

        foreach ($pesertaAktif as $peserta) {
            if (! $peserta->user) {
                continue;
            }

            Notifikasi::query()->create([
                'user_id' => $peserta->user->id_user,
                'judul' => 'Penugasan Magang Baru',
                'pesan' => 'Tugas baru "'.$tugas->judul.'" telah diberikan kepada Anda. Silakan buka menu Penugasan untuk melihat detail dan deadline.',
                'kategori' => 'penugasan',
                'tipe' => 'info',
                'referensi_id' => $tugas->id_tugas,
                'dibaca' => false,
            ]);
        }

        return redirect()
            ->route('admin-peserta.tugas.index')
            ->with('success', 'Tugas baru berhasil ditambahkan, diberikan, dan dinotifikasikan melalui portal/email ke ' . count($rows) . ' peserta aktif.');
    }

    public function update(Request $request, Tugas $tugas)
    {
        $validated = $this->validated($request, forUpdate: true);

        if ($request->hasFile('file_tugas')) {
            if ($tugas->file_tugas) {
                Storage::disk('public')->delete($tugas->file_tugas);
            }

            $validated['file_tugas'] = $request->file('file_tugas')
                ->store('file-tugas', 'public');
        }

        $validated['instansi'] = strtolower($validated['instansi']);
        $tugas->update($validated);

        $pesertaDitugaskan = PenugasanPeserta::query()
            ->with('peserta.user')
            ->where('tugas_id', $tugas->id_tugas)
            ->get();

        foreach ($pesertaDitugaskan as $penugasan) {
            $user = $penugasan->peserta?->user;
            if (! $user) {
                continue;
            }

            Notifikasi::query()->create([
                'user_id' => $user->id_user,
                'judul' => 'Penugasan Magang Diperbarui',
                'pesan' => 'Detail tugas "'.$tugas->judul.'" telah diperbarui oleh Admin. Silakan cek kembali materi, instruksi, dan deadline di menu Penugasan.',
                'kategori' => 'penugasan',
                'tipe' => 'info',
                'referensi_id' => $tugas->id_tugas,
                'dibaca' => false,
            ]);
        }

        return redirect()
            ->route('admin-peserta.tugas.index')
            ->with('success', 'Tugas berhasil diperbarui dan perubahan telah dikirim melalui portal/email kepada peserta yang ditugaskan.');
    }

    public function destroy(Tugas $tugas)
    {
        if ($tugas->file_tugas) {
            Storage::disk('public')->delete($tugas->file_tugas);
        }

        $tugas->delete();

        return redirect()
            ->route('admin-peserta.tugas.index')
            ->with('success', 'Tugas berhasil dihapus.');
    }

    public function upload(
        Request $request,
        PenugasanTemplateService $service
    ) {
        $request->validate([
            'file_template' => ['required', 'file', 'mimes:xlsx', 'max:10240'],
        ]);

        $result = $service->import(
            $request->file('file_template'),
            (int) auth()->id()
        );

        $message = "Template benar berhasil dipublikasikan: {$result['tasks']} tugas dan {$result['assignments']} jadwal peserta diproses.";
        if ($result['unmatched_participants'] > 0) {
            $message .= " {$result['unmatched_participants']} peserta SMK belum dijadwalkan karena jurusannya belum terbaca sebagai RPL/PPLG atau TKJ/TJKT.";
        }

        return redirect()
            ->route('admin-peserta.tugas.index')
            ->with('success', $message);
    }

    public function downloadPanduan()
    {
        $path = public_path('template/MATERI DAN TUGAS.xlsx');
        abort_unless(file_exists($path), 404, 'Panduan materi dan tugas tidak ditemukan.');

        return response()->download($path, 'MATERI DAN TUGAS.xlsx');
    }

    public function downloadTemplate()
    {
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\TemplateTugasMingguanExport(),
            'template_tugas_mingguan.xlsx'
        );
    }



    private function validated(Request $request, bool $forUpdate = false): array
    {
        return $request->validate([
            'kode_tugas' => ['nullable', 'string', 'max:80'],
            'judul' => ['required', 'string', 'max:255'],
            'materi' => ['nullable', 'string'],
            'kategori_tugas' => ['required', Rule::in(['materi', 'tugas', 'laporan'])],
            'jenis_tugas' => ['required', Rule::in(['harian', 'mingguan', 'akhir'])],
            'minggu_ke' => ['nullable', 'integer', 'min:1'],
            'rilis_hari_ke' => ['nullable', 'integer', 'min:1'],
            'deadline_hari_ke' => ['nullable', 'integer', 'min:1', 'gte:rilis_hari_ke'],
            'hari_mulai' => ['nullable', 'string', 'max:255'],
            'keterangan' => ['nullable', 'string'],
            'file_tugas' => ['nullable', 'file', 'mimes:pdf,doc,docx,xls,xlsx', 'max:10240'],
            'instansi' => ['required', Rule::in(['universitas', 'sekolah', 'semua'])],
            'target_peserta' => ['nullable', Rule::in(['smk_rpl', 'smk_tkj', 'smk_sija', 'kuliah_ti', 'kuliah_si', 'kuliah_ptik', 'semua'])],
            'hari_tampil' => ['nullable', 'string', 'max:20'],
            'hari_deadline' => ['nullable', 'string', 'max:20'],
            'jam_deadline' => ['nullable', 'date_format:H:i'],
            'status' => [$forUpdate ? 'required' : 'nullable', Rule::in(['aktif', 'nonaktif', 'selesai'])],
        ]);
    }
}