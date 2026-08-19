<?php

namespace App\Http\Controllers;

use Symfony\Component\HttpFoundation\BinaryFileResponse;

class SertifikatAssetController extends Controller
{
    public function ttdDirektur(): BinaryFileResponse
    {
        $path = public_path('images/TTD DIrektur.PNG');

        abort_unless(is_file($path), 404, 'Tanda tangan direktur tidak ditemukan.');

        return response()->file($path, [
            'Content-Type' => 'image/png',
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }
}
