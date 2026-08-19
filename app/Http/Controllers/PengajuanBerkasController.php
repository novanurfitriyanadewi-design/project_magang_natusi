<?php

namespace App\Http\Controllers;

use App\Models\PermintaanMagang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class PengajuanBerkasController extends Controller
{
    public function show(
        Request $request,
        PermintaanMagang $permintaan,
        string $jenis,
        ?string $ref = null
    ): BinaryFileResponse {
        $user = $request->user();

        $isAdmin = $user && in_array($user->role, ['superadmin', 'admin', 'admin_peserta'], true);
        $isOwner = $user && (int) $permintaan->user_id === (int) $user->id_user;

        abort_unless($isAdmin || $isOwner, 403, 'Anda tidak memiliki akses ke berkas pengajuan ini.');

        $path = null;

        if (in_array($jenis, ['cv', 'surat'], true)) {
            if ($ref && $ref !== 'ketua') {
                $anggota = $permintaan->anggota()
                    ->where('id_anggota', $ref)
                    ->firstOrFail();

                $path = $jenis === 'cv'
                    ? ($anggota->cv_path ?: ($anggota->is_ketua ? $permintaan->cv_path : null))
                    : ($anggota->surat_pengajuan_path ?: ($anggota->is_ketua ? $permintaan->surat_pengajuan_path : null));
            } else {
                $path = $jenis === 'cv'
                    ? $permintaan->cv_path
                    : $permintaan->surat_pengajuan_path;
            }
        } elseif ($jenis === 'revisi') {
            abort_unless($ref && ctype_digit((string) $ref), 404);

            $berkas = $permintaan->riwayatBerkas()
                ->whereKey((int) $ref)
                ->firstOrFail();

            $path = $berkas->path;
        } else {
            abort(404);
        }

        abort_unless($path, 404, 'Berkas belum tersedia.');

        $disk = Storage::disk('public');
        abort_unless($disk->exists($path), 404, 'File tidak ditemukan di penyimpanan server.');

        $mime = $disk->mimeType($path) ?: 'application/octet-stream';
        $filename = basename($path);

        return response()->file($disk->path($path), [
            'Content-Type' => $mime,
            'Content-Disposition' => 'inline; filename="'.str_replace('"', '', $filename).'"',
            'Cache-Control' => 'private, max-age=300',
        ]);
    }
}
