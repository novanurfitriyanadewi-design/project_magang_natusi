@extends('layouts.portal')

@section('title', 'Detail Pengajuan Resign')

@section('content')
<div class="mx-auto max-w-3xl">
    <div class="mb-6 flex items-center justify-between gap-4">
        <div>
            <p class="text-xs font-bold uppercase tracking-wider text-sky-600">Pengajuan Resign #{{ $resign->id }}</p>
            <h1 class="mt-1 text-2xl font-extrabold text-slate-900">Detail Pengajuan Resign</h1>
        </div>
        <a href="{{ route('admin-karyawan.resign.index') }}" class="rounded-lg border border-slate-200 px-4 py-2 text-sm font-bold text-slate-600 hover:bg-slate-50">Kembali</a>
    </div>

    @if (session('success'))
        <div class="mb-5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700">{{ session('success') }}</div>
    @endif

    <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex flex-wrap items-start justify-between gap-3 border-b border-slate-100 pb-5">
            <div>
                <h2 class="text-lg font-bold text-slate-900">{{ $resign->karyawan?->nama_karyawan ?? '-' }}</h2>
                <p class="mt-1 text-sm text-slate-500">NIP: {{ $resign->karyawan?->nip ?? '-' }} | {{ $resign->karyawan?->jabatan ?? '-' }}</p>
            </div>
            @php($status = match ($resign->status) {
                'disetujui' => ['label' => 'Disetujui', 'class' => 'bg-emerald-100 text-emerald-700'],
                'ditolak' => ['label' => 'Ditolak', 'class' => 'bg-rose-100 text-rose-700'],
                default => ['label' => 'Menunggu', 'class' => 'bg-amber-100 text-amber-700'],
            })
            <span class="rounded-full px-3 py-1 text-xs font-bold {{ $status['class'] }}">{{ $status['label'] }}</span>
        </div>

        <dl class="mt-5 grid gap-4 text-sm sm:grid-cols-2">
            <div><dt class="text-slate-400">Tanggal Efektif</dt><dd class="mt-1 font-bold text-slate-800">{{ $resign->tanggal_efektif?->translatedFormat('d F Y') ?? '-' }}</dd></div>
            <div><dt class="text-slate-400">Tanggal Pengajuan</dt><dd class="mt-1 font-bold text-slate-800">{{ $resign->created_at?->translatedFormat('d F Y, H:i') ?? '-' }}</dd></div>
            <div><dt class="text-slate-400">Divisi</dt><dd class="mt-1 font-bold text-slate-800">{{ $resign->karyawan?->divisi?->nama_divisi ?? '-' }}</dd></div>
        </dl>

        <div class="mt-5 rounded-xl bg-slate-50 p-4">
            <p class="text-xs font-bold uppercase tracking-wide text-slate-400">Alasan Pengunduran Diri</p>
            <p class="mt-2 text-sm leading-6 text-slate-700">{{ $resign->alasan }}</p>
        </div>

        <div class="mt-5">
            @if ($resign->surat_resign_path)
                <a href="{{ route('admin-karyawan.resign.download', $resign) }}" class="inline-flex items-center gap-2 rounded-lg bg-sky-50 px-4 py-2.5 text-sm font-bold text-sky-700 hover:bg-sky-100">
                    <span class="material-symbols-outlined text-[18px]">description</span>
                    Lihat / Unduh Surat Resign
                </a>
            @else
                <p class="text-sm text-slate-400">Karyawan tidak melampirkan surat resign.</p>
            @endif
        </div>

        @if ($resign->status === 'pending')
            <div class="mt-6 flex flex-wrap gap-3 border-t border-slate-100 pt-5">
                <form method="POST" action="{{ route('admin-karyawan.resign.approve', $resign) }}">
                    @csrf @method('PATCH')
                    <button type="submit" class="rounded-lg bg-emerald-600 px-5 py-2.5 text-sm font-bold text-white hover:bg-emerald-700">Terima Pengajuan</button>
                </form>
                <form method="POST" action="{{ route('admin-karyawan.resign.reject', $resign) }}" class="flex flex-1 gap-2 sm:max-w-md">
                    @csrf @method('PATCH')
                    <input name="catatan_hrd" required maxlength="500" placeholder="Catatan penolakan" class="min-w-0 flex-1 rounded-lg border-slate-300 text-sm focus:border-rose-500 focus:ring-rose-500">
                    <button type="submit" class="rounded-lg bg-rose-600 px-5 py-2.5 text-sm font-bold text-white hover:bg-rose-700">Tolak</button>
                </form>
            </div>
        @elseif ($resign->catatan_hrd)
            <div class="mt-5 rounded-xl bg-rose-50 p-4 text-sm text-rose-700"><strong>Catatan HRD:</strong> {{ $resign->catatan_hrd }}</div>
        @endif
    </section>
</div>
@endsection
