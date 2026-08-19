@extends('layouts.portal')

@section('title', 'Metode Pembayaran')

@section('content')
<div class="space-y-6" x-data="{ historyOpen: false }" @keydown.escape.window="historyOpen = false">
    <header class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 class="mt-5 text-2xl font-extrabold tracking-tight text-slate-950 sm:mt-0 sm:text-3xl">Metode Pembayaran QRIS</h1>
            <p class="mt-1 max-w-3xl text-sm leading-6 text-slate-500">
                Atur nominal pembayaran dan unggah satu kode QR yang akan digunakan seluruh peserta magang.
            </p>
        </div>
        <button type="button" @click="historyOpen = true"
            class="inline-flex shrink-0 items-center justify-center gap-2 rounded-xl border border-sky-200 bg-white px-4 py-2.5 text-sm font-bold text-sky-700 shadow-sm transition hover:bg-sky-50">
            <span class="material-symbols-outlined text-[18px]">history</span>
            Riwayat Perubahan
        </button>
    </header>

    @if (session('success'))
        <div class="flex items-start gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700">
            <span class="material-symbols-outlined text-[20px]">check_circle</span>
            <span>{{ session('success') }}</span>
        </div>
    @endif
    @if (session('error'))
        <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-semibold text-rose-700">{{ session('error') }}</div>
    @endif
    @if ($errors->any())
        <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
            <p class="font-bold">Data belum dapat disimpan.</p>
            <ul class="mt-1 list-inside list-disc">
                @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    {{-- Nominal pembayaran --}}
    <section class="overflow-hidden rounded-3xl border border-sky-100 bg-white shadow-[0_18px_45px_rgba(15,52,94,0.08)]">
        <div class="border-l-4 border-sky-600 p-6">
            <div class="flex items-center gap-3">
                <span class="grid h-11 w-11 place-items-center rounded-xl bg-sky-100 text-sky-700">
                    <span class="material-symbols-outlined">payments</span>
                </span>
                <div>
                    <h2 class="text-base font-extrabold text-slate-950">Jumlah Pembayaran</h2>
                    <p class="mt-0.5 text-xs text-slate-500">Nominal ini tampil kepada peserta di halaman pembayaran.</p>
                </div>
            </div>

            <form method="POST" action="{{ route('superadmin.metode-pembayaran.nominal.update') }}"
                class="mt-5 grid gap-4 lg:grid-cols-[minmax(0,1fr)_auto] lg:items-end">
                @csrf
                @method('PUT')
                <div>
                    <label for="jumlah_nominal" class="mb-1.5 block text-xs font-bold text-slate-700">Biaya Pendaftaran / Administrasi Magang</label>
                    <div class="relative max-w-2xl">
                        <span class="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-sm font-bold text-slate-500">Rp</span>
                        <input id="jumlah_nominal" name="jumlah_nominal" type="number" inputmode="numeric" min="1000" step="1000"
                            value="{{ old('jumlah_nominal', $nominal->jumlah_nominal ?? '') }}" placeholder="Contoh: 150000" required
                            class="w-full rounded-xl border-slate-300 py-3 pl-12 pr-4 text-sm focus:border-sky-500 focus:ring-sky-500">
                    </div>
                </div>
                <button type="submit" class="inline-flex h-11 items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-sky-600 to-blue-700 px-6 text-sm font-bold text-white shadow-lg">
                    <span class="material-symbols-outlined text-[18px]">save</span>
                    Simpan Nominal
                </button>
            </form>
        </div>
    </section>

    {{-- QR-only --}}
    <section class="overflow-hidden rounded-3xl border border-emerald-100 bg-white shadow-[0_18px_45px_rgba(15,52,94,0.08)]">
        <div class="border-b border-emerald-100 bg-gradient-to-r from-emerald-50 to-teal-50 px-6 py-5">
            <div class="flex items-center gap-3">
                <span class="grid h-11 w-11 place-items-center rounded-xl bg-white text-emerald-600 shadow-sm ring-1 ring-emerald-100">
                    <span class="material-symbols-outlined">qr_code_2</span>
                </span>
                <div>
                    <h2 class="text-lg font-extrabold text-slate-900">Kode QR Pembayaran</h2>
                    <p class="mt-0.5 text-sm text-slate-500">Tidak perlu menambahkan rekening bank. Cukup unggah gambar QR/QRIS resmi perusahaan.</p>
                </div>
            </div>
        </div>

        <div class="grid gap-6 p-6 lg:grid-cols-[minmax(0,1fr)_340px]">
            <form method="POST" action="{{ route('superadmin.metode-pembayaran.qris.store') }}" enctype="multipart/form-data" class="flex flex-col justify-center">
                @csrf
                <label for="qris_image" class="mb-2 block text-sm font-bold text-slate-700">Upload Kode QR</label>
                <div class="rounded-2xl border-2 border-dashed border-emerald-200 bg-emerald-50/40 p-6">
                    <input id="qris_image" name="qris_image" type="file" accept=".jpg,.jpeg,.png" required
                        class="block w-full text-sm file:mr-4 file:rounded-xl file:border-0 file:bg-emerald-600 file:px-4 file:py-2.5 file:text-sm file:font-bold file:text-white hover:file:bg-emerald-700">
                    <p class="mt-3 text-xs leading-5 text-slate-500">Format JPG, JPEG, atau PNG. Maksimal 4 MB. Upload baru otomatis mengganti QR lama.</p>
                </div>
                <button type="submit"
                    class="mt-4 inline-flex h-11 w-fit items-center justify-center gap-2 rounded-xl bg-emerald-600 px-5 text-sm font-bold text-white shadow-lg transition hover:bg-emerald-700">
                    <span class="material-symbols-outlined text-[18px]">upload</span>
                    {{ $qris?->qris_image ? 'Ganti QR Code' : 'Simpan QR Code' }}
                </button>
            </form>

            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5 text-center">
                <p class="mb-3 text-xs font-extrabold uppercase tracking-[0.12em] text-slate-500">QR Aktif</p>
                @if ($qris?->qris_image)
                    <div class="mx-auto w-fit overflow-hidden rounded-2xl border border-slate-200 bg-white p-3 shadow-sm">
                        <img src="{{ asset('storage/'.$qris->qris_image) }}" alt="QR Pembayaran" class="h-56 w-56 object-contain">
                    </div>
                    <form method="POST" action="{{ route('superadmin.metode-pembayaran.qris.destroy') }}" class="mt-4" onsubmit="return confirm('Hapus kode QR pembayaran?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-rose-50 px-4 py-2 text-xs font-bold text-rose-600 ring-1 ring-rose-100 hover:bg-rose-100">
                            <span class="material-symbols-outlined text-[17px]">delete</span>
                            Hapus QR
                        </button>
                    </form>
                @else
                    <div class="grid min-h-56 place-items-center rounded-2xl border-2 border-dashed border-slate-200 bg-white px-6">
                        <div>
                            <span class="material-symbols-outlined text-[64px] text-slate-300">qr_code_2</span>
                            <p class="mt-2 text-sm font-semibold text-slate-500">Belum ada QR yang diunggah</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </section>

    {{-- Modal riwayat --}}
    <div x-cloak x-show="historyOpen" x-transition.opacity class="fixed inset-0 z-[9999] overflow-y-auto bg-slate-950/60 p-4" @click.self="historyOpen = false">
        <div class="flex min-h-full items-center justify-center">
            <div x-show="historyOpen" x-transition class="w-full max-w-3xl overflow-hidden rounded-3xl bg-white shadow-2xl">
                <div class="flex items-center justify-between border-b border-slate-200 px-6 py-5">
                    <div>
                        <h3 class="text-lg font-extrabold text-slate-950">Riwayat Perubahan</h3>
                        <p class="text-sm text-slate-500">Perubahan terbaru pada nominal dan kode QR pembayaran.</p>
                    </div>
                    <button type="button" @click="historyOpen = false" class="grid h-9 w-9 place-items-center rounded-xl bg-slate-100 text-slate-500">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>
                <div class="max-h-[65vh] divide-y divide-slate-100 overflow-y-auto">
                    @forelse ($histories as $history)
                        <div class="px-6 py-4">
                            <div class="flex flex-wrap items-center justify-between gap-2">
                                <p class="text-sm font-bold text-slate-900">{{ $history->deskripsi }}</p>
                                <span class="rounded-full bg-slate-100 px-2.5 py-1 text-[10px] font-bold uppercase text-slate-600">{{ $history->aksi }}</span>
                            </div>
                            <p class="mt-1 text-xs text-slate-500">{{ $history->user?->nama ?? 'Sistem' }} · {{ $history->created_at?->translatedFormat('d M Y H:i') }}</p>
                        </div>
                    @empty
                        <div class="px-6 py-12 text-center text-sm text-slate-500">Belum ada riwayat perubahan.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
