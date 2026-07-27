<?php

namespace App\Http\Controllers;

use App\Models\PenugasanPeserta;
use App\Models\PesertaMagang;
use App\Models\Tugas;
use App\Services\PenugasanTemplateService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TugasController extends ApiCrudController
{
    protected string $modelClass = Tugas::class;
    protected array $with = ['user'];
    protected array $files = ['file_tugas' => 'tugas'];

    protected function rules(?Model $model = null): array
    {
        return [
            'kode_tugas' => ['nullable', 'string', 'max:80'],
            'judul' => ['required', 'string', 'max:255'],
            'materi' => ['nullable', 'string'],
            'kategori_tugas' => ['required', Rule::in(['materi', 'tugas', 'laporan'])],
            'jenis_tugas' => ['required', Rule::in(['harian', 'mingguan', 'akhir'])],
            'minggu_ke' => ['nullable', 'integer', 'min:1'],
            'file_tugas' => ['nullable', 'file', 'mimes:pdf,doc,docx,xls,xlsx', 'max:5120'],
            'instansi' => ['required', Rule::in(['universitas', 'sekolah', 'semua'])],
            'target_peserta' => ['nullable', Rule::in(['smk_rpl', 'smk_tkj', 'universitas', 'semua'])],
            'hari_tampil' => ['nullable', 'string', 'max:20'],
            'hari_deadline' => ['nullable', 'string', 'max:20'],
            'jam_deadline' => ['nullable', 'date_format:H:i'],
            'rilis_hari_ke' => ['nullable', 'integer', 'min:1'],
            'deadline_hari_ke' => ['nullable', 'integer', 'min:1', 'gte:rilis_hari_ke'],
            'hari_mulai' => ['nullable', 'string', 'max:255'],
            'keterangan' => ['nullable', 'string'],
            'status' => ['sometimes', Rule::in(['aktif', 'nonaktif', 'selesai'])],
        ];
    }

    public function index(Request $request): JsonResponse
    {
        if ($request->user()?->role !== 'peserta') {
            return parent::index($request);
        }

        $peserta = PesertaMagang::query()
            ->where('user_id', $request->user()->id_user)
            ->firstOrFail();

        app(PenugasanTemplateService::class)->refreshStatuses($peserta);

        $items = PenugasanPeserta::query()
            ->with(['tugas', 'templateLaporan'])
            ->where('peserta_id', $peserta->id_peserta)
            ->orderBy('deadline')
            ->paginate($this->resolvePerPage($request))
            ->withQueryString();

        return response()->json([
            'success' => true,
            'data' => $items,
        ]);
    }

    protected function prepareDataForStore(array $data, Request $request): array
    {
        $data['user_id'] = $request->user()->id_user;
        $data['status'] = $data['status'] ?? 'aktif';
        $data['instansi'] = strtolower($data['instansi']);

        return $data;
    }

    protected function prepareDataForUpdate(array $data, Request $request, Model $item): array
    {
        unset($data['user_id']);
        if (isset($data['instansi'])) {
            $data['instansi'] = strtolower($data['instansi']);
        }

        return $data;
    }

    public function importExcel(
        Request $request,
        PenugasanTemplateService $service
    ): JsonResponse {
        $request->validate([
            'file_excel' => ['required', 'file', 'mimes:xlsx', 'max:10240'],
        ]);

        $result = $service->import(
            $request->file('file_excel'),
            (int) $request->user()->id_user
        );

        return response()->json([
            'success' => true,
            'message' => 'Data tugas dan deadline per peserta berhasil diproses.',
            'data' => $result,
        ]);
    }
}
