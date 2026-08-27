@extends('layouts.portal')

@section('title', 'Detail Pengajuan Cuti')

@section('content')
<div class="mx-auto max-w-3xl">
    <div class="mb-6 flex items-center justify-between gap-4">
        <div>
            <p class="text-xs font-bold uppercase tracking-wider text-sky-600">Pengajuan Cuti #{{ $cuti->id }}</p>
            <h1 class="mt-1 text-2xl font-extrabold text-slate-900">Detail Pengajuan Cuti</h1>
        </div>
        <a href="{{ route('admin-karyawan.cuti.index') }}" class="rounded-lg border border-slate-200 px-4 py-2 text-sm font-bold text-slate-600 hover:bg-slate-50">Kembali</a>
    </div>

    <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex flex-wrap items-start justify-between gap-3 border-b border-slate-100 pb-5">
            <div>
                <h2 class="text-lg font-bold text-slate-900">{{ $cuti->karyawan?->nama_karyawan ?? '-' }}</h2>
                <p class="mt-1 text-sm text-slate-500">NIP: {{ $cuti->karyawan?->nip ?? '-' }} | {{ $cuti->karyawan?->jabatan ?? '-' }}</p>
            </div>
            @php($meta = $cuti->statusMeta())
            <span class="rounded-full px-3 py-1 text-xs font-bold {{ $meta['class'] }}">{{ $meta['label'] }}</span>
        </div>

        <dl class="mt-5 grid gap-4 text-sm sm:grid-cols-2">
            <div><dt class="text-slate-400">Jenis Cuti</dt><dd class="mt-1 font-bold text-slate-800">{{ $cuti->jenis_label }}</dd></div>
            <div><dt class="text-slate-400">Durasi</dt><dd class="mt-1 font-bold text-slate-800">{{ $cuti->jumlah_hari }} hari</dd></div>
            <div><dt class="text-slate-400">Tanggal Mulai</dt><dd class="mt-1 font-bold text-slate-800">{{ $cuti->tanggal_mulai->translatedFormat('d F Y') }}</dd></div>
            <div><dt class="text-slate-400">Tanggal Selesai</dt><dd class="mt-1 font-bold text-slate-800">{{ $cuti->tanggal_selesai->translatedFormat('d F Y') }}</dd></div>
        </dl>

        <div class="mt-5 rounded-xl bg-slate-50 p-4">
            <p class="text-xs font-bold uppercase tracking-wide text-slate-400">Alasan Pengajuan</p>
            <p class="mt-2 text-sm leading-6 text-slate-700">{{ $cuti->alasan }}</p>
        </div>

        <div class="mt-5 flex flex-wrap items-center gap-3">
            @if ($cuti->bukti_pendukung)
                <a href="{{ asset('storage/' . $cuti->bukti_pendukung) }}" target="_blank" class="inline-flex items-center gap-2 rounded-lg bg-sky-50 px-4 py-2.5 text-sm font-bold text-sky-700 hover:bg-sky-100">
                    <span class="material-symbols-outlined text-[18px]">attach_file</span> Lihat Berkas Pendukung
                </a>
            @else
                <span class="text-sm text-slate-400">Tidak ada berkas pendukung.</span>
            @endif
            <a href="{{ route('admin-karyawan.cuti.letter', $cuti) }}" target="_blank" class="inline-flex items-center gap-2 rounded-lg bg-emerald-50 px-4 py-2.5 text-sm font-bold text-emerald-700 hover:bg-emerald-100">
                <span class="material-symbols-outlined text-[18px]">description</span> Lihat Surat Cuti
            </a>
        </div>

        @if ($cuti->status === 'pending')
            <div class="mt-6 flex flex-wrap gap-3 border-t border-slate-100 pt-5">
                <form method="POST" action="{{ route('admin-karyawan.cuti.approve', $cuti) }}">
                    @csrf @method('PATCH')
                    <button class="rounded-lg bg-emerald-600 px-5 py-2.5 text-sm font-bold text-white hover:bg-emerald-700">Terima Pengajuan</button>
                </form>
                <form method="POST" action="{{ route('admin-karyawan.cuti.reject', $cuti) }}" class="flex flex-1 gap-2 sm:max-w-md">
                    @csrf @method('PATCH')
                    <input name="catatan_hrd" required maxlength="500" placeholder="Alasan penolakan" class="min-w-0 flex-1 rounded-lg border-slate-300 text-sm focus:border-rose-500 focus:ring-rose-500">
                    <button class="rounded-lg bg-rose-600 px-5 py-2.5 text-sm font-bold text-white hover:bg-rose-700">Tolak</button>
                </form>
            </div>
        @elseif ($cuti->catatan_hrd)
            <div class="mt-5 rounded-xl bg-rose-50 p-4 text-sm text-rose-700"><strong>Catatan HRD:</strong> {{ $cuti->catatan_hrd }}</div>
        @endif
    </section>
</div>
@endsection
