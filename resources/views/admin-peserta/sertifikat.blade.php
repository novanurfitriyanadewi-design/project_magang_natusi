@extends('layouts.portal')

@section('title', 'Kelola Sertifikat')

@section('content')
<div
    class="space-y-6"
    x-data="{
        issueOpen: false,
    }"
>
    <header class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="mt-5 text-2xl font-bold tracking-tight text-slate-950 sm:mt-0">Kelola Sertifikat</h1>
            <p class="mt-1 text-sm text-slate-500">Terbitkan sertifikat magang untuk peserta yang selesai/keluar. Desain sertifikat mengikuti format resmi CV Natusi.</p>
        </div>

        <button type="button" @click="issueOpen = true" class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-sky-500 to-blue-600 px-4 py-2.5 text-sm font-bold text-white shadow-[0_8px_20px_rgba(14,165,233,0.24)] transition hover:-translate-y-0.5">
            <span class="material-symbols-outlined text-[18px]">workspace_premium</span>
            Terbitkan Sertifikat
        </button>
    </header>

    @if (session('success'))
        <div class="flex items-start gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700 shadow-sm">
            <span class="material-symbols-outlined text-[21px]">check_circle</span>
            <span>{{ session('success') }}</span>
        </div>
    @endif
    @if (session('error'))
        <div class="flex items-start gap-3 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-semibold text-rose-700 shadow-sm">
            <span class="material-symbols-outlined text-[21px]">error</span>
            <span>{{ session('error') }}</span>
        </div>
    @endif
    @if ($errors->any())
        <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-semibold text-rose-700 shadow-sm">
            <p class="mb-1">Periksa kembali data yang diisi:</p>
            <ul class="list-inside list-disc space-y-0.5 font-normal">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Riwayat --}}
    <section class="overflow-hidden rounded-3xl border border-white/80 bg-white/90 shadow-[0_18px_45px_rgba(15,52,94,0.08)] backdrop-blur">
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-sky-100 bg-gradient-to-r from-sky-50 to-blue-50 px-5 py-4 sm:px-6">
            <h2 class="text-base font-extrabold text-slate-950">Riwayat Sertifikat Diterbitkan</h2>
            <form method="GET" action="{{ route('admin-peserta.sertifikat.index') }}" class="flex flex-wrap items-center gap-2">
                <input type="search" name="search" value="{{ $search }}" placeholder="Cari nama / nomor sertifikat..." class="w-56 rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-100">
                <select name="status" onchange="this.form.submit()" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-100">
                    <option value="">Semua Status</option>
                    <option value="terbit" {{ $status === 'terbit' ? 'selected' : '' }}>Terbit</option>
                    <option value="dicabut" {{ $status === 'dicabut' ? 'selected' : '' }}>Dicabut</option>
                </select>
                <button type="submit" class="rounded-xl bg-sky-600 px-4 py-2 text-sm font-bold text-white hover:bg-sky-700">Cari</button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[960px] border-collapse text-left">
                <thead>
                    <tr class="border-b border-slate-200 bg-sky-50/70 text-[10px] font-bold uppercase tracking-[0.12em] text-slate-500">
                        <th class="px-6 py-4">Nomor</th>
                        <th class="px-5 py-4">Peserta</th>
                        <th class="px-5 py-4">Divisi</th>
                        <th class="px-5 py-4">Predikat</th>
                        <th class="px-5 py-4">Tanggal Terbit</th>
                        <th class="px-5 py-4 text-center">Status</th>
                        <th class="px-5 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($riwayat as $item)
                        <tr class="transition hover:bg-sky-50/40">
                            <td class="px-6 py-4 text-xs font-mono text-slate-500">{{ $item->nomor_sertifikat }}</td>
                            <td class="px-5 py-4">
                                <p class="text-sm font-bold text-slate-900">{{ $item->peserta?->user?->nama ?? '-' }}</p>
                                <p class="text-xs text-slate-500">{{ $item->peserta?->permintaan?->nama_sekolah ?? '-' }}</p>
                            </td>
                            <td class="px-5 py-4 text-sm text-slate-700">{{ $item->divisi?->nama_divisi ?? '-' }}</td>
                            <td class="px-5 py-4 text-sm text-slate-700">{{ $item->predikat }}</td>
                            <td class="px-5 py-4 text-sm text-slate-700">{{ $item->tanggal_terbit->translatedFormat('d M Y') }}</td>
                            <td class="px-5 py-4 text-center">
                                <span @class([
                                    'rounded-full px-3 py-1 text-xs font-bold',
                                    'bg-emerald-100 text-emerald-700' => $item->status === 'terbit',
                                    'bg-slate-100 text-slate-500' => $item->status !== 'terbit',
                                ])>{{ $item->status === 'terbit' ? 'Terbit' : 'Dicabut' }}</span>
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-center gap-1.5">
                                    <a href="{{ route('admin-peserta.sertifikat.cetak', $item) }}" target="_blank" class="grid h-9 w-9 place-items-center rounded-xl bg-sky-50 text-sky-700 ring-1 ring-sky-100 transition hover:bg-sky-100" title="Lihat/Cetak">
                                        <svg class="h-4.5 w-4.5" viewBox="0 0 24 24" fill="none"><path d="M3 12s3.5-6 9-6 9 6 9 6-3.5 6-9 6-9-6-9-6Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/><circle cx="12" cy="12" r="2.5" stroke="currentColor" stroke-width="1.7"/></svg>
                                    </a>
                                    @if ($item->status === 'terbit')
                                        <form action="{{ route('admin-peserta.sertifikat.cabut', $item) }}" method="POST" onsubmit="return confirm('Cabut sertifikat {{ $item->nomor_sertifikat }}?')">
                                            @csrf
                                            <button type="submit" class="grid h-9 w-9 place-items-center rounded-xl bg-rose-50 text-rose-600 ring-1 ring-rose-100 transition hover:bg-rose-100" title="Cabut sertifikat">
                                                <svg class="h-4.5 w-4.5" viewBox="0 0 24 24" fill="none"><path d="M6 6l12 12M18 6 6 18" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-16 text-center text-sm text-slate-500">Belum ada sertifikat yang diterbitkan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($riwayat->hasPages())
            <div class="border-t border-slate-100 px-6 py-4">
                {{ $riwayat->links() }}
            </div>
        @endif
    </section>

    {{-- Modal: Terbitkan Sertifikat --}}
    <div x-show="issueOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display:none;">
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="fixed inset-0 bg-slate-950/60" @click="issueOpen = false"></div>
            <div class="relative w-full max-w-lg rounded-2xl bg-white p-6 shadow-2xl" @click.stop>
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-extrabold text-slate-950">Terbitkan Sertifikat</h3>
                    <button type="button" @click="issueOpen = false" class="rounded-xl p-1.5 text-slate-400 hover:bg-slate-100 hover:text-slate-600">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none"><path d="m6 6 12 12M18 6 6 18" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                    </button>
                </div>

                <form method="POST" action="{{ route('admin-peserta.sertifikat.store') }}" class="mt-5 space-y-4">
                    @csrf
                    <div>
                        <label class="mb-1.5 block text-sm font-semibold text-slate-700">Peserta</label>
                        <select name="peserta_id" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-100">
                            <option value="">Pilih peserta yang selesai/keluar magang</option>
                            @foreach ($pesertaBisaDisertifikasi as $p)
                                <option value="{{ $p->id_peserta }}">
                                    {{ $p->user?->nama ?? 'Tanpa nama' }}
                                    ({{ $p->status === 'selesai' ? 'Selesai' : 'Keluar' }})
                                    — {{ $p->permintaan?->nama_sekolah ?? '-' }}
                                </option>
                            @endforeach
                        </select>
                        @if ($pesertaBisaDisertifikasi->isEmpty())
                            <p class="mt-1.5 text-xs text-amber-600">Tidak ada peserta berstatus "selesai"/"dibatalkan" yang belum punya sertifikat aktif saat ini.</p>
                        @endif
                    </div>

                    <div>
                        <label class="mb-1.5 block text-sm font-semibold text-slate-700">Divisi</label>
                        <select name="divisi_id" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-100">
                            <option value="">Pilih divisi tempat peserta ditempatkan</option>
                            @foreach ($divisiList as $d)
                                <option value="{{ $d->id_divisi }}">{{ $d->nama_divisi }}</option>
                            @endforeach
                        </select>
                        @if ($divisiList->isEmpty())
                            <p class="mt-1.5 text-xs text-amber-600">Belum ada divisi terdaftar. Tambahkan dulu lewat menu Kelola Divisi (superadmin).</p>
                        @endif
                    </div>

                    <div>
                        <label class="mb-1.5 block text-sm font-semibold text-slate-700">Predikat</label>
                        <select name="predikat" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-100">
                            @foreach ($predikatOptions as $p)
                                <option value="{{ $p }}" @selected($p === 'Sangat Baik')>{{ $p }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="mb-1.5 block text-sm font-semibold text-slate-700">Judul Sertifikat (untuk catatan internal)</label>
                        <input type="text" name="judul" value="Sertifikat Magang" required maxlength="255" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-100">
                    </div>

                    <div>
                        <label class="mb-1.5 block text-sm font-semibold text-slate-700">Tanggal Terbit</label>
                        <input type="date" name="tanggal_terbit" value="{{ now()->format('Y-m-d') }}" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-100">
                    </div>

                    <div>
                        <label class="mb-1.5 block text-sm font-semibold text-slate-700">Catatan (opsional, internal)</label>
                        <textarea name="catatan" rows="2" maxlength="1000" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-100"></textarea>
                    </div>

                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button" @click="issueOpen = false" class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50">Batal</button>
                        <button type="submit" class="rounded-xl bg-gradient-to-r from-sky-500 to-blue-600 px-5 py-2.5 text-sm font-bold text-white shadow-[0_8px_20px_rgba(14,165,233,0.24)] hover:-translate-y-0.5">Terbitkan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
