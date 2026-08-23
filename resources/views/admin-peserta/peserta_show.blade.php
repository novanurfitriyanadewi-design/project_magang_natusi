@extends('layouts.portal')
@section('title','Detail Peserta Magang')
@section('content')
<div class="mt-5 rounded-3xl border bg-white p-6 shadow">
<h1 class="text-2xl font-bold">Detail Peserta Magang</h1>
<div class="mt-5 space-y-2">
<p>Nama: {{ $pesertaMagang->user?->name ?? '-' }}</p>
<p>Email: {{ $pesertaMagang->user?->email ?? '-' }}</p>
<p>Jurusan: {{ $pesertaMagang->jurusan?->nama_jurusan ?? '-' }}</p>
<p>Status: {{ ucfirst($pesertaMagang->status) }}</p>
</div>
<div class="mt-5 flex gap-3">
<form method="POST" action="{{ route('admin-peserta.peserta.status',$pesertaMagang->id_peserta) }}">@csrf @method('PATCH')<input type="hidden" name="status" value="aktif"><button class="rounded-xl bg-emerald-600 px-4 py-2 text-white">Aktifkan</button></form>
<form method="POST" action="{{ route('admin-peserta.peserta.status',$pesertaMagang->id_peserta) }}">@csrf @method('PATCH')<input type="hidden" name="status" value="nonaktif"><button class="rounded-xl bg-slate-600 px-4 py-2 text-white">Nonaktifkan</button></form>
</div></div>
@endsection
