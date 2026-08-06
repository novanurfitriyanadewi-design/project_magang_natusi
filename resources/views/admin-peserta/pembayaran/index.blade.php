@extends('layouts.portal')

@section('title', 'Data Pembayaran')

@section('content')
<div class="space-y-6" x-data="{
    detailOpen: false,
    detail: {},
    tolakOpen: false,
    tolakAction: '',
    tolakId: null,
    openDetail(data) { this.detail = data; this.detailOpen = true; },
    closeDetail() { this.detailOpen = false; },
    openTolak(id, action) { this.tolakId = id; this.tolakAction = action; this.tolakOpen = true; },
    closeTolak() { this.tolakOpen = false; this.tolakId = null; this.tolakAction = ''; }
}">

    {{-- Judul --}}
    <header>
        <h1 class="mt-5 text-2xl font-extrabold tracking-tight text-slate-950 sm:text-3xl">Data Pembayaran</h1>
        <p class="mt-1 text-sm text-slate-500">Pantau dan kelola transaksi keuangan peserta magang.</p>
    </header>

    {{-- Alert --}}
    @if (session('success'))
        <div class="flex items-start gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-800">
            <svg class="mt-0.5 h-5 w-5 shrink-0 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    {{-- Card utama --}}
    <section class="overflow-hidden rounded-3xl border border-sky-100/90 bg-white/95 shadow-[0_20px_50px_rgba(15,52,94,0.09)] backdrop-blur">

        {{-- Header gradasi --}}
        <header class="border-b border-sky-100 bg-gradient-to-r from-sky-50 via-blue-50 to-cyan-50 px-6 py-5">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div class="flex items-center gap-3">
                    <span class="grid h-11 w-11 shrink-0 place-items-center rounded-2xl bg-white text-sky-600 shadow-sm ring-1 ring-sky-100">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 7.5h16v10H4v-10Zm0 3h16M8 15h3"/></svg>
                    </span>
                    <div>
                        <h2 class="text-lg font-extrabold text-slate-900">Data Pembayaran Peserta</h2>
                        <p class="mt-0.5 text-sm text-slate-500">Gunakan tombol Detail untuk memeriksa data dan bukti pembayaran.</p>
                    </div>
                </div>
                <span class="w-fit rounded-xl bg-white px-4 py-2 text-xs font-bold text-sky-700 shadow-sm ring-1 ring-slate-200">
                    {{ number_format($pembayarans->total()) }} data
                </span>
            </div>
        </header>

        {{-- Tab filter status --}}
        <div class="border-b border-sky-100 bg-white px-6 py-4">
            <nav class="flex flex-wrap gap-2" aria-label="Filter status pembayaran">
                @foreach ($tabStatus as $value => $label)
                    <a href="{{ route('admin-peserta.pembayaran.index', array_filter([
                        'status' => $value,
                        'search' => $search,
                        'dari_tanggal' => $dariTgl,
                        'sampai_tanggal' => $sampaiTgl,
                    ], fn($item) => $item !== null && $item !== '')) }}"
                       @class([
                           'inline-flex min-w-24 items-center justify-center rounded-xl px-4 py-2.5 text-sm font-bold transition',
                           'bg-gradient-to-r from-sky-500 to-blue-600 text-white shadow-[0_8px_20px_rgba(14,165,233,0.24)]' => $status === $value,
                           'border border-slate-200 bg-white text-slate-600 hover:-translate-y-0.5 hover:border-sky-200 hover:bg-sky-50 hover:text-sky-700' => $status !== $value,
                       ])
                    >
                        {{ $label }}
                    </a>
                @endforeach
            </nav>
        </div>

        {{-- Tabel --}}
        <div class="overflow-x-auto">
            <table class="w-full min-w-[1280px] border-collapse text-left">
                <thead>
                    <tr class="border-b border-slate-200 bg-slate-50/80 text-[10px] font-bold uppercase tracking-[0.12em] text-slate-500">
                        <th class="px-6 py-4">Nama</th>
                        <th class="px-5 py-4">Jenjang Pendidikan</th>
                        <th class="px-5 py-4">No. Telepon</th>
                        <th class="px-5 py-4">Tanggal Pengiriman</th>
                        <th class="px-5 py-4">Bukti Pembayaran</th>
                        <th class="px-5 py-4 text-center">Status</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($pembayarans as $pembayaran)
                        @php
                            $peserta = $pembayaran->peserta;
                            $nama = $peserta?->user?->nama ?? $peserta?->permintaan?->nama_pemohon ?? '-';
                            $telepon = $peserta?->user?->no_telp ?? $peserta?->user?->nomor_telepon ?? $peserta?->no_telp ?? $peserta?->nomor_telepon ?? $peserta?->permintaan?->no_telp ?? $peserta?->permintaan?->nomor_telepon ?? '-';
                            $rawJenjang = mb_strtolower((string)($peserta?->tingkat_pendidikan ?? $peserta?->jenjang_pendidikan ?? $peserta?->permintaan?->jenjang_pendidikan ?? $peserta?->instansi ?? ''));
                            $jenjang = str_contains($rawJenjang, 'universitas') || str_contains($rawJenjang, 'mahasiswa') || str_contains($rawJenjang, 'kampus') ? 'Universitas' : 'SMK';
                            $tanggalKirim = $pembayaran->tgl_bayar ?? $pembayaran->created_at;
                            $buktiUrl = $pembayaran->bukti_transfer ? Storage::url($pembayaran->bukti_transfer) : null;
                            $buktiNama = $pembayaran->bukti_transfer ? basename($pembayaran->bukti_transfer) : 'Tidak ada bukti';
                            $isPdf = $pembayaran->bukti_transfer && mb_strtolower(pathinfo($pembayaran->bukti_transfer, PATHINFO_EXTENSION)) === 'pdf';
                            $isAccepted = $pembayaran->status === 'lunas';
                            $statusLabel = $isAccepted ? 'Diterima' : 'Menunggu';
                            $initials = collect(preg_split('/\s+/', trim($nama)) ?: [])->filter()->take(2)->map(fn($w) => mb_strtoupper(mb_substr($w, 0, 1)))->implode('');
                            $acceptAction = route('admin-peserta.pembayaran.terima', $pembayaran);
                            $tolakAction = route('admin-peserta.pembayaran.tolak', $pembayaran);
                            $tanggalText = $tanggalKirim?->translatedFormat('d M Y, H:i') ?? '-';
                            $nominalFormatted = 'Rp ' . number_format($pembayaran->nominal ?? 0, 0, ',', '.');
                            $idPembayaran = $pembayaran->id_pembayaran;
                        @endphp

                        <tr class="group transition hover:bg-sky-50/40">
                            {{-- Nama --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <span class="grid h-10 w-10 shrink-0 place-items-center rounded-full bg-gradient-to-br from-sky-100 to-blue-100 text-xs font-extrabold text-sky-700 ring-1 ring-sky-200">{{ $initials ?: 'P' }}</span>
                                    <div class="min-w-0">
                                        <p class="max-w-52 truncate text-sm font-extrabold text-slate-900" title="{{ $nama }}">{{ $nama }}</p>
                                        <p class="mt-0.5 text-xs text-slate-400">#PAY-{{ str_pad((string) $idPembayaran, 5, '0', STR_PAD_LEFT) }}</p>
                                    </div>
                                </div>
                            </td>

                            {{-- Jenjang --}}
                            <td class="px-5 py-4">
                                <span @class([
                                    'inline-flex rounded-full border px-3 py-1 text-xs font-bold',
                                    'border-violet-200 bg-violet-50 text-violet-700' => $jenjang === 'Universitas',
                                    'border-sky-200 bg-sky-50 text-sky-700' => $jenjang === 'SMK',
                                ])>{{ $jenjang }}</span>
                            </td>

                            {{-- Telepon --}}
                            <td class="px-5 py-4 text-sm font-semibold text-slate-700">{{ $telepon }}</td>

                            {{-- Tanggal --}}
                            <td class="px-5 py-4">
                                @if ($tanggalKirim)
                                    <p class="text-sm font-semibold text-slate-700">{{ $tanggalKirim->translatedFormat('d M Y') }}</p>
                                    <p class="mt-0.5 text-xs text-slate-500">{{ $tanggalKirim->format('H:i') }} WIB</p>
                                @else
                                    <span class="text-sm italic text-slate-400">Tanggal tidak tersedia</span>
                                @endif
                            </td>

                            {{-- Bukti --}}
                            <td class="px-5 py-4">
                                @if ($buktiUrl)
                                    <button type="button" @click="openDetail(@js([
                                        'nama' => $nama,
                                        'jenjang' => $jenjang,
                                        'telepon' => $telepon,
                                        'tanggal' => $tanggalText . ' WIB',
                                        'nominal' => $nominalFormatted,
                                        'status' => $statusLabel,
                                        'buktiUrl' => $buktiUrl,
                                        'buktiNama' => $buktiNama,
                                        'isPdf' => $isPdf,
                                        'acceptAction' => $acceptAction,
                                        'canAccept' => !$isAccepted,
                                    ]))" class="inline-flex max-w-52 items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-bold text-sky-700 shadow-sm transition hover:border-sky-200 hover:bg-sky-50">
                                        <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M8 12.5 12.5 8a3 3 0 1 1 4.2 4.2l-6.1 6.1a4.5 4.5 0 1 1-6.4-6.4l6-6"/></svg>
                                        <span class="truncate">{{ $buktiNama }}</span>
                                    </button>
                                @else
                                    <span class="inline-flex rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-500">Tidak ada bukti</span>
                                @endif
                            </td>

                            {{-- Status --}}
                            <td class="px-5 py-4 text-center">
                                <span @class([
                                    'inline-flex items-center gap-1.5 rounded-full border px-3 py-1 text-xs font-extrabold',
                                    'border-emerald-200 bg-emerald-50 text-emerald-700' => $isAccepted,
                                    'border-amber-200 bg-amber-50 text-amber-700' => !$isAccepted,
                                ])>
                                    <span @class(['h-1.5 w-1.5 rounded-full', 'bg-emerald-500' => $isAccepted, 'bg-amber-500' => !$isAccepted])></span>
                                    {{ $statusLabel }}
                                </span>
                            </td>

                            {{-- Aksi --}}
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    {{-- Detail --}}
                                    <button type="button" @click="openDetail(@js([
                                        'nama' => $nama,
                                        'jenjang' => $jenjang,
                                        'telepon' => $telepon,
                                        'tanggal' => $tanggalText . ' WIB',
                                        'nominal' => $nominalFormatted,
                                        'status' => $statusLabel,
                                        'buktiUrl' => $buktiUrl ?? '',
                                        'buktiNama' => $buktiNama,
                                        'isPdf' => $isPdf,
                                        'acceptAction' => $acceptAction,
                                        'canAccept' => !$isAccepted,
                                    ]))" class="inline-flex items-center gap-2 rounded-xl border border-sky-200 bg-sky-50 px-3.5 py-2 text-xs font-extrabold text-sky-700 transition hover:-translate-y-0.5 hover:bg-sky-100 focus:outline-none focus:ring-4 focus:ring-sky-100">
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                                        Show Detail
                                    </button>

                                    @if (!$isAccepted)
                                        {{-- Terima --}}
                                        <form method="POST" action="{{ $acceptAction }}" onsubmit="return confirm('Terima pembayaran ini dan ubah status menjadi lunas?')">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-500 px-3.5 py-2 text-xs font-extrabold text-white shadow-[0_8px_18px_rgba(16,185,129,0.22)] transition hover:-translate-y-0.5 focus:outline-none focus:ring-4 focus:ring-emerald-100">
                                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m5 12 4 4L19 6"/></svg>
                                                Terima
                                            </button>
                                        </form>

                                        {{-- Tolak --}}
                                        <button type="button" @click="openTolak({{ $idPembayaran }}, '{{ $tolakAction }}')" class="inline-flex items-center gap-2 rounded-xl border border-red-200 bg-white px-3.5 py-2 text-xs font-extrabold text-red-600 transition hover:-translate-y-0.5 hover:bg-red-50">
                                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 18L18 6M6 6l12 12"/></svg>
                                            Tolak
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-16 text-center">
                                <span class="mx-auto grid h-14 w-14 place-items-center rounded-2xl bg-slate-100 text-slate-400">
                                    <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M4 7.5h16v10H4v-10Zm0 3h16M8 15h3"/></svg>
                                </span>
                                <p class="mt-4 font-extrabold text-slate-700">Data pembayaran tidak ditemukan.</p>
                                <p class="mt-1 text-sm text-slate-500">Ubah filter atau tunggu peserta mengirim bukti pembayaran.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Footer --}}
        @if ($pembayarans->hasPages())
            <footer class="flex flex-col gap-4 border-t border-sky-100 bg-sky-50/50 px-6 py-4 md:flex-row md:items-center md:justify-between">
                <p class="text-xs font-medium text-slate-500">
                    Menampilkan {{ $pembayarans->firstItem() ?? 0 }}–{{ $pembayarans->lastItem() ?? 0 }}
                    dari {{ $pembayarans->total() }} data
                </p>
                <div>{{ $pembayarans->onEachSide(1)->links() }}</div>
            </footer>
        @endif
    </section>

    {{-- Modal Detail Pembayaran --}}
    <template x-teleport="body">
        <div x-cloak x-show="detailOpen" x-transition.opacity class="fixed inset-0 z-[9999] overflow-y-auto bg-slate-950/60 px-4 py-6 backdrop-blur-sm" @click.self="closeDetail()">
            <div class="flex min-h-full items-center justify-center">
                <article x-show="detailOpen" x-transition.scale.origin.center role="dialog" aria-modal="true" aria-labelledby="detail-pembayaran-title" class="w-full max-w-4xl overflow-hidden rounded-3xl border border-white/70 bg-white shadow-2xl">
                    {{-- Header --}}
                    <header class="flex items-start justify-between gap-4 bg-gradient-to-r from-sky-600 to-blue-600 px-6 py-5 text-white">
                        <div>
                            <p class="text-[10px] font-extrabold uppercase tracking-[0.16em] text-sky-100">Verifikasi pembayaran</p>
                            <h2 id="detail-pembayaran-title" class="mt-1 text-xl font-extrabold">Detail Pembayaran Peserta</h2>
                            <p class="mt-0.5 text-sm text-white/80">Periksa identitas dan bukti pembayaran sebelum menerima transaksi.</p>
                        </div>
                        <button type="button" @click="closeDetail()" class="rounded-xl p-2 text-white/80 transition hover:bg-white/10 hover:text-white" aria-label="Tutup detail">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="m6 6 12 12M18 6 6 18"/></svg>
                        </button>
                    </header>

                    {{-- Body grid --}}
                    <div class="grid gap-0 lg:grid-cols-[0.9fr_1.1fr]">
                        {{-- Informasi --}}
                        <section class="border-b border-slate-200 p-6 lg:border-b-0 lg:border-r">
                            <h3 class="text-sm font-extrabold text-slate-900">Informasi Pembayaran</h3>
                            <dl class="mt-5 space-y-4">
                                <div class="rounded-2xl bg-slate-50 p-4">
                                    <dt class="text-[10px] font-extrabold uppercase tracking-[0.12em] text-slate-400">Nama Peserta</dt>
                                    <dd class="mt-1 text-sm font-bold text-slate-800" x-text="detail.nama"></dd>
                                </div>
                                <div class="grid gap-3 sm:grid-cols-2">
                                    <div class="rounded-2xl bg-slate-50 p-4">
                                        <dt class="text-[10px] font-extrabold uppercase tracking-[0.12em] text-slate-400">Jenjang</dt>
                                        <dd class="mt-1 text-sm font-bold text-slate-800" x-text="detail.jenjang"></dd>
                                    </div>
                                    <div class="rounded-2xl bg-slate-50 p-4">
                                        <dt class="text-[10px] font-extrabold uppercase tracking-[0.12em] text-slate-400">No. Telepon</dt>
                                        <dd class="mt-1 text-sm font-bold text-slate-800" x-text="detail.telepon"></dd>
                                    </div>
                                </div>
                                <div class="grid gap-3 sm:grid-cols-2">
                                    <div class="rounded-2xl bg-slate-50 p-4">
                                        <dt class="text-[10px] font-extrabold uppercase tracking-[0.12em] text-slate-400">Tanggal Pengiriman</dt>
                                        <dd class="mt-1 text-sm font-bold text-slate-800" x-text="detail.tanggal"></dd>
                                    </div>
                                    <div class="rounded-2xl bg-slate-50 p-4">
                                        <dt class="text-[10px] font-extrabold uppercase tracking-[0.12em] text-slate-400">Nominal</dt>
                                        <dd class="mt-1 text-sm font-bold text-slate-800" x-text="detail.nominal"></dd>
                                    </div>
                                </div>
                                <div class="rounded-2xl bg-slate-50 p-4">
                                    <dt class="text-[10px] font-extrabold uppercase tracking-[0.12em] text-slate-400">Status</dt>
                                    <dd class="mt-1">
                                        <span class="inline-flex rounded-full px-3 py-1 text-xs font-bold" :class="detail.status === 'Diterima' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800'" x-text="detail.status"></span>
                                    </dd>
                                </div>
                            </dl>
                            <template x-if="detail.canAccept">
                                <form :action="detail.acceptAction" method="POST" class="mt-6" onsubmit="return confirm('Terima pembayaran ini?')">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="w-full rounded-xl bg-emerald-600 py-3 text-sm font-extrabold text-white transition hover:bg-emerald-700">Terima Pembayaran</button>
                                </form>
                            </template>
                        </section>

                        {{-- Bukti --}}
                        <section class="p-6">
                            <h3 class="text-sm font-extrabold text-slate-900">Bukti Pembayaran</h3>
                            <div class="mt-4 flex min-h-[300px] flex-col items-center justify-center rounded-2xl border border-slate-200 bg-slate-50 p-4">
                                <template x-if="detail.buktiUrl">
                                    <div class="w-full text-center">
                                        <template x-if="!detail.isPdf">
                                            <img :src="detail.buktiUrl" :alt="detail.buktiNama" class="mx-auto max-h-[350px] rounded-lg object-contain shadow-sm">
                                        </template>
                                        <template x-if="detail.isPdf">
                                            <iframe :src="detail.buktiUrl" class="h-[350px] w-full rounded-lg border border-slate-200"></iframe>
                                        </template>
                                        <a :href="detail.buktiUrl" target="_blank" download class="mt-3 inline-flex items-center gap-1.5 text-xs font-bold text-sky-600 hover:underline">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                            Unduh Bukti Pembayaran
                                        </a>
                                    </div>
                                </template>
                                <template x-if="!detail.buktiUrl">
                                    <p class="text-xs text-slate-400">Tidak ada lampiran bukti pembayaran.</p>
                                </template>
                            </div>
                        </section>
                    </div>
                </article>
            </div>
        </div>
    </template>

    {{-- Modal Tolak Pembayaran --}}
    <template x-teleport="body">
        <div x-cloak x-show="tolakOpen" x-transition.opacity class="fixed inset-0 z-[9999] overflow-y-auto bg-slate-950/60 px-4 py-6 backdrop-blur-sm" @click.self="closeTolak()">
            <div class="flex min-h-full items-center justify-center">
                <article x-show="tolakOpen" x-transition.scale.origin.center role="dialog" aria-modal="true" aria-labelledby="tolak-pembayaran-title" class="w-full max-w-lg overflow-hidden rounded-3xl border border-white/70 bg-white shadow-2xl">
                    <header class="flex items-start justify-between gap-4 bg-gradient-to-r from-rose-600 to-red-600 px-6 py-5 text-white">
                        <div>
                            <p class="text-[10px] font-extrabold uppercase tracking-[0.16em] text-rose-100">Tolak pembayaran</p>
                            <h2 id="tolak-pembayaran-title" class="mt-1 text-xl font-extrabold">Konfirmasi Penolakan</h2>
                            <p class="mt-0.5 text-sm text-white/80">Berikan alasan penolakan agar peserta dapat memperbaiki.</p>
                        </div>
                        <button type="button" @click="closeTolak()" class="rounded-xl p-2 text-white/80 transition hover:bg-white/10 hover:text-white" aria-label="Tutup modal">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="m6 6 12 12M18 6 6 18"/></svg>
                        </button>
                    </header>

                    <form method="POST" :action="tolakAction" class="p-6">
                        @csrf @method('PATCH')
                        <div>
                            <label for="tolak-keterangan" class="mb-1.5 block text-sm font-bold text-slate-700">Alasan Penolakan</label>
                            <textarea id="tolak-keterangan" name="keterangan" rows="4" required placeholder="Tuliskan alasan penolakan dengan jelas..." class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm text-slate-700 placeholder:text-slate-400 focus:border-rose-500 focus:ring-4 focus:ring-rose-100"></textarea>
                            <p class="mt-1.5 text-xs text-slate-500">Alasan akan dikirimkan ke peserta sebagai catatan.</p>
                        </div>
                        <div class="mt-6 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                            <button type="button" @click="closeTolak()" class="rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-bold text-slate-600 transition hover:bg-slate-100">Batal</button>
                            <button type="submit" class="rounded-xl bg-gradient-to-r from-rose-600 to-red-600 px-5 py-2.5 text-sm font-bold text-white shadow-[0_10px_24px_rgba(225,29,72,0.22)] transition hover:-translate-y-0.5">Ya, Tolak Pembayaran</button>
                        </div>
                    </form>
                </article>
            </div>
        </div>
    </template>
</div>
@endsection