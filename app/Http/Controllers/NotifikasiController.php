<?php

namespace App\Http\Controllers;

use App\Models\Notifikasi;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotifikasiController extends ApiCrudController
{
    protected string $modelClass = Notifikasi::class;
    protected array $with = ['user'];
    protected array $files = [];

    protected function rules(?Model $model = null): array
    {
        return [
            'user_id'=>['required','exists:users,id_user'],
            'judul'=>['required','string','max:255'],
            'pesan'=>['required','string'],
            'kategori'=>['required','in:pengajuan,pembayaran,penugasan,absensi,akun'],
            'tipe'=>['required','in:info,peringatan,sukses'],
            'referensi_id'=>['nullable','integer'],
            'dibaca'=>['sometimes','boolean'],
        ];
    }

    public function milikSaya(Request $request): JsonResponse
    {
        return response()->json(['success'=>true,'data'=>Notifikasi::query()->where('user_id',$request->user()->id_user)->latest()->paginate(20)]);
    }

    public function tandaiDibaca(Request $request, int|string $id): JsonResponse
    {
        $notifikasi=Notifikasi::query()->where('user_id',$request->user()->id_user)->findOrFail($id);
        $notifikasi->update(['dibaca'=>true]);
        return response()->json(['success'=>true,'message'=>'Notifikasi telah dibaca.','data'=>$notifikasi]);
    }

    public function tandaiSemuaDibaca(Request $request): JsonResponse
    {
        Notifikasi::query()->where('user_id',$request->user()->id_user)->where('dibaca',false)->update(['dibaca'=>true]);
        return response()->json(['success'=>true,'message'=>'Semua notifikasi telah dibaca.']);
    }
}
