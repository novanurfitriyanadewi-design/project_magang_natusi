<?php

namespace App\Services;

use App\Models\Sertifikat;
use Dompdf\Dompdf;
use Dompdf\Options;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use RuntimeException;

class CertificatePdfService
{
    public function download(Sertifikat $sertifikat): Response
    {
        $sertifikat->loadMissing([
            'peserta.user',
            'peserta.permintaan',
            'peserta.jurusan',
            'divisi',
        ]);

        $signaturePath = public_path('images/TTD DIrektur.PNG');
        $logoPath = public_path('images/logo.jpeg');

        if (! is_file($signaturePath)) {
            throw new RuntimeException('File tanda tangan direktur tidak ditemukan: public/images/TTD DIrektur.PNG');
        }

        if (! is_file($logoPath)) {
            throw new RuntimeException('File logo tidak ditemukan: public/images/logo.jpeg');
        }

        $signatureUrl = $this->signaturePublicUrl();
        $qrDataUri = $this->buildSignatureQr($signatureUrl);
        $logoDataUri = $this->imageDataUri($logoPath);

        $html = view('sertifikat.pdf', [
            'sertifikat' => $sertifikat,
            'qrDataUri' => $qrDataUri,
            'logoDataUri' => $logoDataUri,
            'signatureUrl' => $signatureUrl,
        ])->render();

        $options = new Options();
        $options->set('defaultFont', 'DejaVu Sans');
        $options->set('isRemoteEnabled', false);
        $options->set('chroot', public_path());

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        $nama = Str::slug((string) ($sertifikat->peserta?->user?->nama ?? 'peserta'));
        $filename = 'sertifikat-magang-' . ($nama ?: 'peserta') . '.pdf';

        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'private, no-store, max-age=0',
        ]);
    }

    private function signaturePublicUrl(): string
    {
        $baseUrl = rtrim((string) config('certificate.qr_public_url', config('app.url')), '/');

        if ($baseUrl === '') {
            throw new RuntimeException('URL publik QR sertifikat belum diatur. Isi NATUSI_QR_PUBLIC_URL di file .env.');
        }

        return $baseUrl . route('sertifikat.ttd-direktur', [], false);
    }

    private function buildSignatureQr(string $signatureUrl): string
    {
        $builder = new Builder(
            writer: new PngWriter(),
            writerOptions: [],
            data: $signatureUrl,
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: ErrorCorrectionLevel::Medium,
            size: 360,
            margin: 24,
            roundBlockSizeMode: RoundBlockSizeMode::Margin,
            validateResult: false,
        );

        $result = $builder->build();

        return $result->getDataUri();
    }

    private function imageDataUri(string $path): string
    {
        $mime = mime_content_type($path) ?: 'image/png';
        $contents = file_get_contents($path);

        if ($contents === false) {
            throw new RuntimeException("Gagal membaca file gambar: {$path}");
        }

        return 'data:' . $mime . ';base64,' . base64_encode($contents);
    }
}