@extends('layouts.portal')

@section('title', 'Detail Peserta Magang')

@section('content')
<div class="mt-5 rounded-3xl bg-white p-6 shadow">

    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold">Detail Peserta Magang</h1>

        <a href="{{ route('admin-peserta.peserta.index') }}"
           class="rounded-xl bg-gray-200 px-4 py-2 text-sm font-semibold hover:bg-gray-300">
            ← Kembali ke Data Peserta Magang
        </a>
    </div>

    <div class="mt-5 grid gap-3">
        <p><b>Nama:</b> {{ $peserta->user->nama ?? '-' }}</p>
        <p><b>Email:</b> {{ $peserta->user->email ?? '-' }}</p>
        <p><b>Status:</b> {{ ucfirst($peserta->status) }}</p>
    </div>

    <div class="mt-6">
        <form method="POST" action="{{ route('admin-peserta.peserta.status', $peserta->id_peserta) }}">
            @csrf
            @method('PATCH')

            @if($peserta->status === 'aktif')
                <input type="hidden" name="status" value="nonaktif">
                <button type="submit"
                    class="rounded-xl bg-red-600 px-4 py-2 font-semibold text-white hover:bg-red-700">
                    Nonaktifkan Peserta
                </button>
            @else
                <input type="hidden" name="status" value="aktif">
                <button type="submit"
                    class="rounded-xl bg-green-600 px-4 py-2 font-semibold text-white hover:bg-green-700">
                    Aktifkan Peserta
                </button>
            @endif
        </form>
    </div>

    @if($peserta->permintaan)
    <div class="mt-8">
        <h2 class="text-lg font-bold">Anggota Kelompok Magang</h2>
        <div class="mt-3 space-y-2">
            @foreach($peserta->permintaan->pesertas as $anggota)
            <div class="rounded-xl border p-3 flex justify-between">
                <span>{{ $anggota->user->nama ?? '-' }}</span>
                <span>{{ ucfirst($anggota->status) }}</span>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection
