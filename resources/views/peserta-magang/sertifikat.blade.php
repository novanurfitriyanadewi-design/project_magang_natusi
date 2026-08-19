@extends('layouts.portal')

@section('title', 'Sertifikat Saya')

@section('content')
<div class="space-y-6">
    <header>
        <h1 class="mt-5 mb-1 text-2xl font-bold text-slate-900 md:text-3xl">Sertifikat Saya</h1>
        <p class="text-sm text-slate-500">Sertifikat magang yang diterbitkan admin untuk Anda bisa diunduh di sini.</p>
    </header>

    @if ($sertifikat->isEmpty())
        <div class="rounded-2xl border border-slate-200 bg-white p-12 text-center shadow-sm">
            <span class="material-symbols-outlined mb-3 text-[64px] text-slate-300">workspace_premium</span>
            <h3 class="text-lg font-semibold text-slate-900">Belum ada sertifikat</h3>
            <p class="mt-1 text-sm text-slate-500">Sertifikat akan muncul di sini setelah admin menerbitkannya, biasanya setelah masa magang Anda selesai.</p>
        </div>
    @else
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($sertifikat as $item)
                @php
                    $itemSmk = \Illuminate\Support\Str::contains(
                        \Illuminate\Support\Str::lower((string) ($item->peserta?->tingkat_pendidikan ?? '')),
                        'smk'
                    );
                    $previewPrimary = $itemSmk ? '#14532d' : '#00082e';
                    $previewGold = '#b48a2c';
                    $previewLine = $itemSmk ? '#d8f0df' : '#dce8fb';
                @endphp
                <article class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
                    <div class="relative aspect-[1.414] overflow-hidden bg-white">
                        <svg class="absolute inset-0 h-full w-full" viewBox="0 0 420 297" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M0 0H105Q52 52 0 105Z" fill="{{ $previewPrimary }}" opacity="0.95" />
                            <path d="M0 122C52 122 122 52 122 0" stroke="{{ $previewGold }}" stroke-width="1.7" />
                            <path d="M420 297H315Q368 245 420 192Z" fill="{{ $previewPrimary }}" opacity="0.95" />
                            <path d="M420 175C368 175 298 245 298 297" stroke="{{ $previewGold }}" stroke-width="1.7" />
                            <path d="M290 0C273 27 277 59 300 78C326 98 370 95 420 132" stroke="{{ $previewLine }}" stroke-width="1.5" />
                            <path d="M300 0C283 27 287 59 310 78C336 98 380 95 420 123" stroke="{{ $previewLine }}" stroke-width="1.5" />
                            <path d="M0 258C35 258 60 241 82 220C107 196 137 193 164 216" stroke="{{ $previewLine }}" stroke-width="1.5" />
                            <path d="M0 268C39 268 66 249 89 228C114 204 144 201 171 224" stroke="{{ $previewLine }}" stroke-width="1.5" />
                        </svg>

                        @if ($itemSmk)
                            <div class="absolute right-3 top-3 grid grid-cols-4 gap-1 opacity-65">
                                @for ($i = 0; $i < 8; $i++)
                                    <span class="h-1.5 w-1.5 rounded-full bg-green-200"></span>
                                @endfor
                            </div>
                        @endif

                        <div class="relative z-10 mx-auto flex h-full w-[72%] flex-col items-center justify-center text-center">
                            <img src="{{ asset('images/logo.jpeg') }}" alt="Logo" class="h-9 w-9 object-contain">
                            <p class="mt-1 text-[7px] font-medium" style="color: {{ $previewPrimary }};">Software House | Hardware Supplier | IT Consultant</p>
                            <div class="mt-1 h-px w-full bg-slate-300"></div>
                            <p class="mt-2 text-3xl font-black tracking-[0.18em]" style="font-family:'Bebas Neue',sans-serif;color:{{ $previewPrimary }};">SERTIFIKAT</p>
                            <p class="text-[9px] font-medium" style="color: {{ $previewPrimary }};">{{ $itemSmk ? 'SMK · Hijau Polkadot' : 'Universitas · Biru' }}</p>
                        </div>
                    </div>

                    <div class="p-4">
                        <div class="mb-2 flex items-center justify-between gap-2">
                            <p class="text-sm font-bold text-slate-900">{{ $item->judul }}</p>
                            <span class="rounded-full px-2.5 py-1 text-[10px] font-bold uppercase tracking-[0.12em] {{ $itemSmk ? 'bg-green-50 text-green-700 ring-1 ring-green-200' : 'bg-blue-50 text-blue-700 ring-1 ring-blue-200' }}">
                                {{ $itemSmk ? 'SMK' : 'Universitas' }}
                            </span>
                        </div>
                        <p class="text-xs text-slate-500">No. {{ $item->nomor_sertifikat }}</p>
                        <p class="mt-1 text-xs text-slate-500">Diterbitkan {{ $item->tanggal_terbit->translatedFormat('d F Y') }}</p>
                        <a href="{{ route('peserta-magang.sertifikat.unduh', $item) }}"
                           class="mt-4 flex items-center justify-center gap-2 rounded-xl py-2.5 text-sm font-bold text-white shadow-lg transition hover:-translate-y-0.5 {{ $itemSmk ? 'bg-gradient-to-r from-green-700 to-emerald-500 shadow-green-200/40' : 'bg-gradient-to-r from-blue-800 to-blue-600 shadow-blue-200/40' }}">
                            <span class="material-symbols-outlined text-[18px]">download</span>
                            Unduh PDF
                        </a>
                    </div>
                </article>
            @endforeach
        </div>
    @endif
</div>
@endsection
