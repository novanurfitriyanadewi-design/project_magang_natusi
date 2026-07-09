<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(['success' => true, 'data' => User::query()->latest()->paginate(15)]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'nama' => ['required','string','max:255'],
            'email' => ['nullable','email','unique:users,email'],
            'username' => ['required','string','max:100','unique:users,username'],
            'password' => ['required','string','min:8'],
            'role' => ['required', Rule::in(['admin','peserta'])],
        ]);
        $data['password'] = Hash::make($data['password']);
        $user = User::create($data);
        return response()->json(['success'=>true,'message'=>'User berhasil ditambahkan.','data'=>$user],201);
    }

    public function show(int|string $id): JsonResponse
    {
        return response()->json(['success'=>true,'data'=>User::with(['permintaanMagang','pesertaMagang'])->findOrFail($id)]);
    }

    public function update(Request $request, int|string $id): JsonResponse
    {
        $user=User::findOrFail($id);
        $data=$request->validate([
            'nama'=>['sometimes','required','string','max:255'],
            'email'=>['sometimes','nullable','email',Rule::unique('users','email')->ignore($user->id_user,'id_user')],
            'username'=>['sometimes','required','string','max:100',Rule::unique('users','username')->ignore($user->id_user,'id_user')],
            'password'=>['nullable','string','min:8'],
            'role'=>['sometimes', Rule::in(['admin','peserta'])],
        ]);
        if (!empty($data['password'])) $data['password']=Hash::make($data['password']); else unset($data['password']);
        $user->update($data);
        return response()->json(['success'=>true,'message'=>'User berhasil diperbarui.','data'=>$user->fresh()]);
    }

    public function destroy(int|string $id): JsonResponse
    {
        $user=User::findOrFail($id);
        if ($user->role === 'superadmin') return response()->json(['success'=>false,'message'=>'Akun superadmin tidak dapat dihapus.'],422);
        $user->delete();
        return response()->json(['success'=>true,'message'=>'User berhasil dihapus.']);
    }
}
