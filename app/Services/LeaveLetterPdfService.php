<?php

namespace App\Services;

use App\Models\Cuti;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class LeaveLetterPdfService
{
    public function download(Cuti $cuti): Response
    {
        $cuti->loadMissing('karyawan.divisi');

        $options = new Options();
        $options->set('defaultFont', 'DejaVu Sans');
        $options->set('isRemoteEnabled', false);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml(view('cuti.surat-pdf', compact('cuti'))->render(), 'UTF-8');
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $nama = Str::slug((string) ($cuti->karyawan?->nama_karyawan ?? 'karyawan')) ?: 'karyawan';

        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="surat-cuti-' . $nama . '.pdf"',
            'Cache-Control' => 'private, no-store, max-age=0',
        ]);
    }
}
