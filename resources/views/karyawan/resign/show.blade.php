@extends('layouts.portal')

@section('title', 'Status Pengajuan Resign')

@section('content')

@php
    $statusClass = match($resign->status) {
        'disetujui' => 'bg-emerald-50 text-emerald-600 border-emerald-100',
        'ditolak' => 'bg-red-50 text-red-600 border-red-100',
        default => 'bg-amber-50 text-amber-700 border-amber-100',
    };
    $statusIcon = match($resign->status) {
        'disetujui' => 'check_circle',
        'ditolak' => 'cancel',
        default => 'hourglass_top',
    };
    $statusLabel = match($resign->status) {
        'disetujui' => 'Disetujui',
        'ditolak' => 'Ditolak',
        default => 'Menunggu',
    };
@endphp

<div class="mx-auto max-w-2xl">

    <div class="mb-7 flex items-center gap-2">
        <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-50 text-[#05658f]">
            <span class="material-symbols-outlined">person_remove</span>
        </span>
        <div>
            <h1 class="headline text-2xl font-bold text-slate-900">Status Pengajuan Resign</h1>
            <p class="text-sm text-slate-500">Detail pengajuan pengunduran diri Anda.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-5 flex items-center gap-3 rounded-2xl border border-emerald-100 bg-emerald-50 px-5 py-4 text-sm font-semibold text-emerald-700">
            <span class="material-symbols-outlined text-[20px]">check_circle</span>
            {{ session('success') }}
        </div>
    @endif

    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">

        <div class="flex items-start justify-between gap-3">
            <h2 class="text-base font-bold text-slate-800">
                Efektif {{ \Carbon\Carbon::parse($resign->tanggal_efektif)->translatedFormat('d F Y') }}
            </h2>
            <span class="inline-flex shrink-0 items-center gap-1 rounded-full border px-2.5 py-1 text-[11px] font-semibold {{ $statusClass }}">
                <span class="material-symbols-outlined text-[14px]">{{ $statusIcon }}</span>
                {{ $statusLabel }}
            </span>
        </div>

        <p class="mt-4 text-sm leading-7 text-slate-600">{{ $resign->alasan }}</p>

        @if($resign->status === 'ditolak' && $resign->catatan_hrd)
            <div class="mt-5 rounded-xl border border-red-100 bg-red-50 px-4 py-3 text-sm text-red-700">
                <span class="font-semibold">Alasan Penolakan:</span> {{ $resign->catatan_hrd }}
            </div>
        @endif

        <div class="mt-5 flex items-center gap-1.5 border-t border-slate-100 pt-4 text-xs text-slate-400">
            <span class="material-symbols-outlined text-[16px]">schedule</span>
            Diajukan {{ $resign->created_at->translatedFormat('d F Y • H:i') }}
        </div>

    </div>
</div>

@endsection
