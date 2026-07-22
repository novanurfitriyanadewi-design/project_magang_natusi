<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TemplateLaporan;
use App\Models\Tugas;
use App\Services\PenugasanTemplateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class TugasController extends Controller
{
    public function index(Request $request)
    {
        $targetPeserta = $request->string('target_peserta')->toString();
        $targetValid = in_array(
            $targetPeserta,
            ['smk_tkj', 'smk_rpl', 'universitas'],
            true
        );

        $tugasList = Tugas::query()
            ->where('jenis_tugas', 'mingguan')
            ->whereNotNull('template_batch')
            ->where('status', 'aktif')
            ->when(
                $targetValid,
                fn ($query) => $query->where('target_peserta', $targetPeserta)
            )
            ->withCount('penugasanPeserta')
            ->orderByRaw(
                "CASE target_peserta
                    WHEN 'smk_tkj' THEN 1
                    WHEN 'smk_rpl' THEN 2
                    WHEN 'universitas' THEN 3
                    ELSE 4
                END"
            )
            ->orderBy('minggu_ke')
            ->orderBy('rilis_hari_ke')
            ->orderBy('id_tugas')
            ->get();

        $templateLaporan = TemplateLaporan::query()
            ->latest('id_template_laporan')
            ->get();

        return view('admin-tugas', compact('tugasList', 'templateLaporan'));
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

        Tugas::create($validated);

        return redirect()
            ->route('admin.tugas.index')
            ->with('success', 'Tugas baru berhasil ditambahkan.');
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

        return redirect()
            ->route('admin.tugas.index')
            ->with('success', 'Tugas berhasil diperbarui.');
    }

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
            ->route('admin.tugas.index')
            ->with('success', $message);
    }

    public function storeTemplateLaporan(
        Request $request,
        PenugasanTemplateService $service
    ) {
        $validated = $request->validate([
            'judul_template' => ['required', 'string', 'max:255'],
            'instansi_laporan' => ['required', Rule::in(['universitas', 'sekolah', 'semua'])],
            'file_word' => ['required', 'file', 'mimes:doc,docx', 'max:10240'],
            'ketentuan_laporan' => ['required', 'string', 'max:20000'],
        ]);

        TemplateLaporan::query()
            ->where('instansi', $validated['instansi_laporan'])
            ->where('is_active', true)
            ->update(['is_active' => false]);

        $path = $request->file('file_word')
            ->store('template-laporan', 'public');

        $template = TemplateLaporan::create([
            'user_id' => auth()->id(),
            'instansi' => $validated['instansi_laporan'],
            'judul' => $validated['judul_template'],
            'file_word' => $path,
            'ketentuan' => $validated['ketentuan_laporan'],
            'is_active' => true,
        ]);

        $updatedAssignments = $service->refreshReportTemplate($template);

        return redirect()
            ->route('admin.tugas.index')
            ->with(
                'success',
                "Template laporan berhasil disimpan dan diterapkan ke {$updatedAssignments} penugasan laporan aktif."
            );
    }

    public function destroyTemplateLaporan(TemplateLaporan $templateLaporan)
    {
        Storage::disk('public')->delete($templateLaporan->file_word);
        $templateLaporan->delete();

        return redirect()
            ->route('admin.tugas.index')
            ->with('success', 'Template laporan berhasil dihapus.');
    }

    public function downloadPanduan()
    {
        $path = public_path('template/MATERI DAN TUGAS.xlsx');
        abort_unless(file_exists($path), 404, 'Panduan materi dan tugas tidak ditemukan.');

        return response()->download($path, 'MATERI DAN TUGAS.xlsx');
    }

    public function downloadTemplate()
    {
        $path = public_path('template/TEMPLATE PENUGASAN.xlsx');
        abort_unless(file_exists($path), 404, 'Template penugasan tidak ditemukan.');

        return response()->download($path, 'template_tugas_mingguan.xlsx');
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
            'target_peserta' => ['nullable', Rule::in(['smk_rpl', 'smk_tkj', 'universitas', 'semua'])],
            'hari_tampil' => ['nullable', 'string', 'max:20'],
            'hari_deadline' => ['nullable', 'string', 'max:20'],
            'jam_deadline' => ['nullable', 'date_format:H:i'],
            'status' => [$forUpdate ? 'required' : 'nullable', Rule::in(['aktif', 'nonaktif', 'selesai'])],
        ]);
    }
}
