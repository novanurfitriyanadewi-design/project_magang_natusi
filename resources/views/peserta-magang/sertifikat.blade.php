@extends('layouts.portal')

@section('title', 'Sertifikat Saya')

@section('content')
<div class="space-y-6">
    <header>
        <h1 class="mt-5 text-2xl font-bold text-slate-900 md:text-3xl mb-1">Sertifikat Saya</h1>
        <p class="text-sm text-slate-500">Sertifikat magang yang diterbitkan admin untuk Anda bisa diunduh di sini.</p>
    </header>

    @if ($sertifikat->isEmpty())
        <div class="rounded-2xl border border-slate-200 bg-white p-12 text-center shadow-sm">
            <span class="material-symbols-outlined mb-3 text-[64px] text-slate-300">workspace_premium</span>
            <h3 class="text-lg font-semibold text-slate-900">Belum ada sertifikat</h3>
            <p class="mt-1 text-sm text-slate-500">
                Sertifikat akan muncul di sini setelah admin menerbitkannya, biasanya setelah masa magang Anda selesai.
            </p>
        </div>
    @else
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($sertifikat as $item)
                <article class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
                    <div class="relative flex aspect-[4/3] flex-col items-center justify-center overflow-hidden bg-gradient-to-br from-[#12235c] via-[#1c3a8f] to-[#2f5bd6] p-4 text-center">
                        <div class="pointer-events-none absolute -left-10 -top-10 h-28 w-28 rotate-12 rounded-[45%] border-2 border-amber-400/60"></div>
                        <div class="pointer-events-none absolute -bottom-10 -right-10 h-28 w-28 rotate-12 rounded-[45%] border-2 border-amber-400/60"></div>
                        <span class="material-symbols-outlined relative z-10 text-4xl text-amber-300">workspace_premium</span>
                        <p class="relative z-10 mt-2 text-sm font-extrabold uppercase tracking-[0.15em] text-white">Sertifikat</p>
                        <p class="relative z-10 mt-1 max-w-[85%] truncate text-xs font-semibold text-blue-100">{{ $item->divisi?->nama_divisi ?? '-' }}</p>
                    </div>
                    <div class="p-4">
                        <p class="text-sm font-bold text-slate-900">{{ $item->judul }}</p>
                        <p class="mt-0.5 text-xs text-slate-500">No. {{ $item->nomor_sertifikat }}</p>
                        <p class="mt-1 text-xs text-slate-500">Diterbitkan {{ $item->tanggal_terbit->translatedFormat('d F Y') }}</p>
                        <a href="{{ route('peserta-magang.sertifikat.cetak', $item) }}" target="_blank"
                           class="mt-4 flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-blue-600 to-blue-700 py-2.5 text-sm font-bold text-white shadow-lg shadow-blue-200/40 transition hover:-translate-y-0.5">
                            <span class="material-symbols-outlined text-[18px]">download</span>
                            Lihat / Unduh
                        </a>
                    </div>
                </article>
            @endforeach
        </div>
    @endif
</div>
@endsection
