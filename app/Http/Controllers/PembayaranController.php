<?php

namespace App\Http\Controllers;

use App\Models\NominalPembayaran;
use App\Models\Pembayaran;
use App\Models\PesertaMagang;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PembayaranController extends ApiCrudController
{
    protected string $modelClass = Pembayaran::class;

    protected array $with = [
        'bank',
        'nominalPembayaran',
        'peserta.user',
    ];

    protected array $files = [
        'bukti_transfer' => 'bukti-transfer',
    ];

    protected function rules(?Model $model = null): array
    {
        return [
            'id_bank' => [
                'required',
                'exists:bank,id_bank',
            ],

            'nominal_id' => [
                'required',
                'exists:nominal_pembayaran,id_nominal',
            ],

            'bukti_transfer' => [
                $model ? 'sometimes' : 'required',
                'file',
                'mimes:pdf,jpg,jpeg,png',
                'max:5120',
            ],

            'keterangan' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ];
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate($this->rules());

        $nominalPembayaran = NominalPembayaran::findOrFail(
            $data['nominal_id']
        );

        $peserta = PesertaMagang::query()
            ->where('user_id', $request->user()->id_user)
            ->where('status', 'aktif')
            ->firstOrFail();

        $path = $request
            ->file('bukti_transfer')
            ->store('bukti-transfer', 'public');

        $pembayaran = Pembayaran::create([
            'id_bank' => $data['id_bank'],
            'nominal_id' => $data['nominal_id'],
            'peserta_id' => $peserta->id_peserta,

            // Diambil dari database, bukan input peserta
            'nominal' => $nominalPembayaran->jumlah_nominal,

            'bukti_transfer' => $path,
            'tgl_bayar' => now(),
            'status' => 'menunggu',
            'keterangan' => $data['keterangan'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Bukti pembayaran berhasil dikirim.',
            'data' => $this->formatPembayaran($pembayaran),
        ], 201);
    }

    public function lunas(Pembayaran $pembayaran): JsonResponse
    {
        $pembayaran->update([
            'status' => 'lunas',
            'keterangan' => 'Pembayaran telah diverifikasi admin.',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pembayaran telah dikonfirmasi lunas.',
            'data' => $this->formatPembayaran($pembayaran),
        ]);
    }

    public function tolak(
        Request $request,
        Pembayaran $pembayaran
    ): JsonResponse {
        $data = $request->validate([
            'keterangan' => [
                'required',
                'string',
                'max:1000',
            ],
        ]);

        $pembayaran->update([
            'status' => 'ditolak',
            'keterangan' => $data['keterangan'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pembayaran ditolak.',
            'data' => $this->formatPembayaran($pembayaran),
        ]);
    }

    private function formatPembayaran(
        Pembayaran $pembayaran
    ): array {
        $pembayaran->load([
            'bank',
            'nominalPembayaran',
            'peserta.user',
        ]);

        $data = $pembayaran->toArray();

        $data['bukti_transfer_url'] =
            $pembayaran->bukti_transfer
                ? Storage::url($pembayaran->bukti_transfer)
                : null;

        return $data;
    }
}