@extends('layouts.portal')

@section('title', 'Ajukan Resign')

@section('content')

<div class="mx-auto max-w-6xl">

    <div class="mb-7 flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
        <div>
            <div class="mb-2 flex items-center gap-2">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-50 text-[#05658f]">
                    <span class="material-symbols-outlined">person_remove</span>
                </span>
                <h1 class="headline text-2xl font-bold text-slate-900">Ajukan Resign</h1>
            </div>
            <p class="text-sm text-slate-500">Ajukan permohonan pengunduran diri dan pantau statusnya di sini.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-5 flex items-center gap-3 rounded-2xl border border-emerald-100 bg-emerald-50 px-5 py-4 text-sm font-semibold text-emerald-700">
            <span class="material-symbols-outlined text-[20px]">check_circle</span>
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-5">

        <div class="lg:col-span-2">
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="mb-4 text-base font-bold text-slate-800">Formulir Pengajuan</h2>
                <form method="POST" action="{{ route('karyawan.pengajuan.resign.store') }}" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <div>
                        <label class="mb-1.5 block text-xs font-semibold text-slate-600">Tanggal Efektif Resign</label>
                        <input type="date" name="tanggal_resign" value="{{ old('tanggal_resign') }}"
                            class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 shadow-sm outline-none transition focus:border-[#05658f] focus:ring-4 focus:ring-[#05658f]/10">
                        @error('tanggal_resign')<p class="mt-1 text-xs font-medium text-red-500">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="mb-1.5 block text-xs font-semibold text-slate-600">Alasan Pengunduran Diri</label>
                        <textarea name="alasan" rows="5" placeholder="Jelaskan alasan Anda mengajukan resign..."
                            class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 shadow-sm outline-none transition focus:border-[#05658f] focus:ring-4 focus:ring-[#05658f]/10">{{ old('alasan') }}</textarea>
                        @error('alasan')<p class="mt-1 text-xs font-medium text-red-500">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="mb-1.5 block text-xs font-semibold text-slate-600">Surat Pengunduran Diri (opsional)</label>
                        <input type="file" name="surat" accept=".pdf,.doc,.docx"
                            class="w-full rounded-xl border border-dashed border-slate-300 bg-slate-50 px-4 py-3 text-sm text-slate-500 outline-none file:mr-3 file:rounded-lg file:border-0 file:bg-[#05658f] file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-white">
                        @error('surat')<p class="mt-1 text-xs font-medium text-red-500">{{ $message }}</p>@enderror
                    </div>
                    <button type="submit" class="mt-2 flex w-full items-center justify-center gap-2 rounded-xl bg-[#05658f] px-5 py-3 text-sm font-semibold text-white transition hover:bg-[#045575]">
                        <span class="material-symbols-outlined text-[18px]">send</span>
                        Kirim Pengajuan
                    </button>
                </form>
            </div>
        </div>

        <div class="lg:col-span-3">
            @if($resign->count())
                <div class="mb-5">
                    <p class="text-sm font-semibold text-slate-700">Riwayat Pengajuan</p>
                    <p class="mt-1 text-xs text-slate-400">{{ $resign->count() }} pengajuan telah Anda kirim</p>
                </div>
                <div class="space-y-4">
                    @foreach($resign as $item)
                        @php
                            $statusClass = match($item->status) {
                                'disetujui' => 'bg-emerald-50 text-emerald-600 border-emerald-100',
                                'ditolak' => 'bg-red-50 text-red-600 border-red-100',
                                default => 'bg-amber-50 text-amber-700 border-amber-100',
                            };
                            $statusIcon = match($item->status) {
                                'disetujui' => 'check_circle',
                                'ditolak' => 'cancel',
                                default => 'hourglass_top',
                            };
                            $statusLabel = match($item->status) {
                                'disetujui' => 'Disetujui',
                                'ditolak' => 'Ditolak',
                                default => 'Menunggu',
                            };
                        @endphp
                        <article class="group rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition-all duration-200 hover:-translate-y-0.5 hover:border-slate-300 hover:shadow-md">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <h3 class="text-sm font-bold text-slate-800">
                                        Efektif {{ \Carbon\Carbon::parse($item->tanggal_resign)->translatedFormat('d F Y') }}
                                    </h3>
                                    <p class="mt-2 line-clamp-2 text-sm leading-6 text-slate-600">{{ $item->alasan }}</p>
                                </div>
                                <span class="inline-flex shrink-0 items-center gap-1 rounded-full border px-2.5 py-1 text-[11px] font-semibold {{ $statusClass }}">
                                    <span class="material-symbols-outlined text-[14px]">{{ $statusIcon }}</span>
                                    {{ $statusLabel }}
                                </span>
                            </div>
                            @if($item->catatan_admin)
                                <div class="mt-4 rounded-xl bg-slate-50 px-4 py-3 text-xs text-slate-500">
                                    <span class="font-semibold text-slate-600">Catatan Admin:</span> {{ $item->catatan_admin }}
                                </div>
                            @endif
                            <div class="mt-4 flex items-center gap-1.5 border-t border-slate-100 pt-3 text-xs text-slate-400">
                                <span class="material-symbols-outlined text-[16px]">schedule</span>
                                Diajukan {{ $item->created_at->translatedFormat('d F Y � H:i') }}
                            </div>
                        </article>
                    @endforeach
                </div>
            @else
                <div class="rounded-2xl border border-dashed border-slate-300 bg-white px-6 py-16 text-center shadow-sm">
                    <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-blue-50 text-[#05658f]">
                        <span class="material-symbols-outlined text-[32px]">person_remove</span>
                    </div>
                    <h3 class="mt-5 text-lg font-bold text-slate-700">Belum Ada Pengajuan</h3>
                    <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-slate-400">Anda belum pernah mengajukan permohonan resign.</p>
                </div>
            @endif
        </div>

    </div>
</div>

@endsection
