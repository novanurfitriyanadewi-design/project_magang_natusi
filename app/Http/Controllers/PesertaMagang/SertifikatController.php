<?php

namespace App\Http\Controllers\PesertaMagang;

use App\Http\Controllers\Controller;
use App\Models\Sertifikat;
use App\Services\CertificatePdfService;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class SertifikatController extends Controller
{
    public function index(): View
    {
        $pesertaId = Auth::user()->pesertaMagang?->id_peserta;

        $sertifikat = Sertifikat::query()
            ->with(['divisi', 'peserta'])
            ->where('peserta_id', $pesertaId)
            ->where('status', 'terbit')
            ->orderByDesc('tanggal_terbit')
            ->get();

        return view('peserta-magang.sertifikat', compact('sertifikat'));
    }

    public function unduh(Sertifikat $sertifikat, CertificatePdfService $pdfService): Response
    {
        abort_unless(
            $sertifikat->peserta_id === Auth::user()->pesertaMagang?->id_peserta,
            403,
            'Sertifikat ini bukan milik Anda.'
        );

        abort_if($sertifikat->status !== 'terbit', 404, 'Sertifikat tidak ditemukan atau sudah dicabut.');

        return $pdfService->download($sertifikat);
    }
}
