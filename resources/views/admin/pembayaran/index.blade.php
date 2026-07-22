@extends('layouts.portal')

@section('title', 'Data Pembayaran')

@section('content')
    @php
        $formatRupiah = static fn ($value) => 'Rp ' . number_format((float) $value, 0, ',', '.');

        $search = $search ?? request('search', '');
        $status = $status ?? request('status', '');
        $dariTgl = $dariTgl ?? request('dari_tanggal');
        $sampaiTgl = $sampaiTgl ?? request('sampai_tanggal');
        $totalDiterima = $totalDiterima ?? 0;
        $totalBelumDiterima = $totalBelumDiterima ?? 0;
        $countBelumDiterima = $countBelumDiterima ?? 0;

        $tabStatus = [
            '' => 'Semua',
            'menunggu' => 'Menunggu',
            'lunas' => 'Diterima',
        ];
    @endphp

    <div
        x-data="{
            detailOpen: false,
            detail: {
                nama: '-',
                jenjang: '-',
                telepon: '-',
                tanggal: '-',
                nominal: '-',
                status: '-',
                buktiUrl: '',
                buktiNama: '',
                isPdf: false,
                acceptAction: '',
                canAccept: false,
            },
            openDetail(payload) {
                this.detail = payload;
                this.detailOpen = true;
            },
            closeDetail() {
                this.detailOpen = false;
            },
        }"
        @keydown.escape.window="closeDetail()"
        x-effect="document.body.classList.toggle('overflow-hidden', detailOpen)"
        class="space-y-5"
    >
        {{-- Header halaman --}}
        <section class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h1 class="mt-5 text-2xl font-extrabold tracking-tight text-slate-950 sm:text-3xl">
                    Data Pembayaran
                </h1>
                <p class="mt-1 max-w-3xl text-sm leading-6 text-slate-500">
                    Periksa bukti pembayaran peserta dan ubah status pembayaran yang valid menjadi lunas.
                </p>
            </div>
        </section>

        {{-- Notifikasi --}}
        @if (session('success'))
            <div class="flex items-start gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700 shadow-sm">
                <svg class="mt-0.5 h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="m5 12 4 4L19 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if (session('error') || $errors->any())
            <div class="flex items-start gap-3 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700 shadow-sm">
                <svg class="mt-0.5 h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M12 9v4m0 4h.01M10.3 3.9 2.7 17.1a1.6 1.6 0 0 0 1.4 2.4h15.8a1.6 1.6 0 0 0 1.4-2.4L13.7 3.9a1.6 1.6 0 0 0-2.8 0Z" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <span>{{ session('error') ?: 'Data belum dapat diproses. Periksa kembali data pembayaran.' }}</span>
            </div>
        @endif

        {{-- Ringkasan bergaya dashboard --}}
        <section class="grid gap-4 md:grid-cols-3">
            <article class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-sky-600 to-blue-600 p-5 text-white shadow-[0_16px_36px_rgba(2,132,199,0.20)] transition duration-200 hover:-translate-y-0.5">
                <div class="absolute -bottom-14 -right-8 h-36 w-36 rounded-full border-[22px] border-white/10"></div>
                <div class="relative flex items-start justify-between gap-4">
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-[0.15em] text-sky-100">Total Data</p>
                        <p class="mt-3 text-4xl font-extrabold">{{ number_format($pembayarans->total()) }}</p>
                        <p class="mt-1 text-sm text-sky-100">Transaksi sesuai filter aktif</p>
                    </div>
                    <span class="grid h-12 w-12 place-items-center rounded-2xl bg-white/15 ring-1 ring-white/20 transition group-hover:scale-105">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M4 5h16v14H4V5Zm4 4h8M8 13h5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>
                </div>
            </article>

            <article class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-amber-500 to-orange-500 p-5 text-white shadow-[0_16px_36px_rgba(245,158,11,0.20)] transition duration-200 hover:-translate-y-0.5">
                <div class="absolute -right-9 -top-9 h-32 w-32 rounded-full border-[20px] border-white/10"></div>
                <div class="relative flex items-start justify-between gap-4">
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-[0.15em] text-amber-50">Menunggu</p>
                        <p class="mt-3 text-3xl font-extrabold">{{ $formatRupiah($totalBelumDiterima) }}</p>
                        <p class="mt-1 text-sm text-amber-50">{{ number_format($countBelumDiterima) }} transaksi perlu diperiksa</p>
                    </div>
                    <span class="grid h-12 w-12 place-items-center rounded-2xl bg-white/15 ring-1 ring-white/20 transition group-hover:scale-105">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M12 7v5l3 2M21 12a9 9 0 1 1-9-9 9 9 0 0 1 9 9Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>
                </div>
            </article>

            <article class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-emerald-600 to-teal-500 p-5 text-white shadow-[0_16px_36px_rgba(5,150,105,0.18)] transition duration-200 hover:-translate-y-0.5">
                <div class="absolute -bottom-20 left-8 h-40 w-40 rounded-full bg-white/10 blur-2xl"></div>
                <div class="relative flex items-start justify-between gap-4">
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-[0.15em] text-emerald-100">Diterima</p>
                        <p class="mt-3 text-3xl font-extrabold">{{ $formatRupiah($totalDiterima) }}</p>
                        <p class="mt-1 text-sm text-emerald-100">Pembayaran berstatus lunas</p>
                    </div>
                    <span class="grid h-12 w-12 place-items-center rounded-2xl bg-white/15 ring-1 ring-white/20 transition group-hover:scale-105">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="m5 12 4 4L19 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>
                </div>
            </article>
        </section>

        {{-- Satu card data seperti dashboard --}}
        <section class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-[0_18px_45px_rgba(15,52,94,0.08)]">
            <header class="border-b border-slate-200 bg-gradient-to-r from-sky-50 to-blue-50 px-5 py-5 sm:px-6">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                    <div class="flex items-center gap-3">
                        <span class="grid h-11 w-11 shrink-0 place-items-center rounded-2xl bg-white text-sky-600 shadow-sm ring-1 ring-sky-100">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M4 7.5h16v10H4v-10Zm0 3h16M8 15h3" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </span>
                        <div>
                            <h2 class="text-lg font-extrabold text-slate-900">Data Pembayaran Peserta</h2>
                            <p class="mt-1 text-sm text-slate-500">Gunakan Show Detail untuk memeriksa data dan bukti pembayaran.</p>
                        </div>
                    </div>

                    <span class="w-fit rounded-xl bg-white px-4 py-2 text-xs font-bold text-sky-700 shadow-sm ring-1 ring-slate-200">
                        {{ number_format($pembayarans->total()) }} data
                    </span>
                </div>
            </header>

            {{-- Tab status --}}
            <div class="border-b border-slate-200 bg-white px-5 py-4 sm:px-6">
                <nav class="flex flex-wrap gap-2" aria-label="Filter status pembayaran">
                    @foreach ($tabStatus as $value => $label)
                        <a
                            href="{{ route('admin.pembayaran.index', array_filter([
                                'status' => $value,
                                'search' => $search,
                                'dari_tanggal' => $dariTgl,
                                'sampai_tanggal' => $sampaiTgl,
                            ], static fn ($item) => $item !== null && $item !== '')) }}"
                            @class([
                                'inline-flex min-w-24 items-center justify-center rounded-xl px-4 py-2.5 text-sm font-bold transition',
                                'bg-gradient-to-r from-sky-500 to-blue-600 text-white shadow-[0_8px_20px_rgba(14,165,233,0.24)]' => $status === $value,
                                'bg-white text-slate-600 ring-1 ring-slate-200 hover:-translate-y-0.5 hover:bg-sky-50 hover:text-sky-700' => $status !== $value,
                            ])
                        >
                            {{ $label }}
                        </a>
                    @endforeach
                </nav>
            </div>

            {{-- Filter pencarian dan tanggal --}}
            <form method="GET" action="{{ route('admin.pembayaran.index') }}" class="border-b border-slate-200 bg-slate-50/70 px-5 py-4 sm:px-6">
                <input type="hidden" name="status" value="{{ $status }}">

                <div class="flex flex-col gap-3 xl:flex-row xl:items-center">
                    <div class="relative min-w-0 flex-1">
                        <svg class="absolute left-3.5 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="1.8"/>
                            <path d="m20 20-3.5-3.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                        </svg>
                        <input
                            type="search"
                            name="search"
                            value="{{ $search }}"
                            placeholder="Cari nama peserta..."
                            class="w-full rounded-xl border border-slate-200 bg-white py-2.5 pl-11 pr-4 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-sky-400 focus:ring-4 focus:ring-sky-100"
                        >
                    </div>

                    <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                        <input
                            type="date"
                            name="dari_tanggal"
                            value="{{ $dariTgl }}"
                            aria-label="Tanggal awal"
                            class="rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none transition focus:border-sky-400 focus:ring-4 focus:ring-sky-100"
                        >
                        <span class="hidden text-xs font-semibold text-slate-400 sm:inline">s/d</span>
                        <input
                            type="date"
                            name="sampai_tanggal"
                            value="{{ $sampaiTgl }}"
                            aria-label="Tanggal akhir"
                            class="rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none transition focus:border-sky-400 focus:ring-4 focus:ring-sky-100"
                        >
                    </div>

                    <div class="flex items-center gap-2">
                        <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-sky-500 to-blue-600 px-5 py-2.5 text-sm font-extrabold text-white shadow-[0_8px_18px_rgba(14,165,233,0.22)] transition hover:-translate-y-0.5 focus:outline-none focus:ring-4 focus:ring-sky-200">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M4 5h16l-6 7v5l-4 2v-7L4 5Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            Terapkan
                        </button>

                        @if ($search !== '' || $status !== '' || $dariTgl || $sampaiTgl)
                            <a href="{{ route('admin.pembayaran.index') }}" class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-600 transition hover:bg-slate-100">
                                Reset
                            </a>
                        @endif
                    </div>
                </div>
            </form>

            {{-- Tabel --}}
            <div class="overflow-x-auto">
                <table class="min-w-[1280px] w-full border-collapse text-left">
                    <thead>
                        <tr class="border-b border-slate-200 bg-slate-50">
                            <th class="px-6 py-4 text-[11px] font-extrabold uppercase tracking-[0.12em] text-slate-500">Nama</th>
                            <th class="px-5 py-4 text-[11px] font-extrabold uppercase tracking-[0.12em] text-slate-500">Jenjang Pendidikan</th>
                            <th class="px-5 py-4 text-[11px] font-extrabold uppercase tracking-[0.12em] text-slate-500">No. Telepon</th>
                            <th class="px-5 py-4 text-[11px] font-extrabold uppercase tracking-[0.12em] text-slate-500">Tanggal Pengiriman</th>
                            <th class="px-5 py-4 text-[11px] font-extrabold uppercase tracking-[0.12em] text-slate-500">Bukti Pembayaran</th>
                            <th class="px-5 py-4 text-[11px] font-extrabold uppercase tracking-[0.12em] text-slate-500">Status</th>
                            <th class="px-6 py-4 text-right text-[11px] font-extrabold uppercase tracking-[0.12em] text-slate-500">Aksi</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100">
                        @forelse ($pembayarans as $pembayaran)
                            @php
                                $peserta = $pembayaran->peserta;
                                $nama = $peserta?->user?->nama
                                    ?? $peserta?->permintaan?->nama_pemohon
                                    ?? '-';

                                $telepon = $peserta?->user?->no_telp
                                    ?? $peserta?->user?->nomor_telepon
                                    ?? $peserta?->no_telp
                                    ?? $peserta?->nomor_telepon
                                    ?? $peserta?->permintaan?->no_telp
                                    ?? $peserta?->permintaan?->nomor_telepon
                                    ?? '-';

                                $rawJenjang = mb_strtolower((string) (
                                    $peserta?->tingkat_pendidikan
                                    ?? $peserta?->jenjang_pendidikan
                                    ?? $peserta?->permintaan?->jenjang_pendidikan
                                    ?? $peserta?->instansi
                                    ?? ''
                                ));

                                $jenjang = str_contains($rawJenjang, 'universitas')
                                    || str_contains($rawJenjang, 'mahasiswa')
                                    || str_contains($rawJenjang, 'kampus')
                                        ? 'Universitas'
                                        : 'SMK';

                                $tanggalKirim = $pembayaran->tgl_bayar ?? $pembayaran->created_at;
                                $buktiUrl = $pembayaran->bukti_transfer
                                    ? Storage::url($pembayaran->bukti_transfer)
                                    : null;
                                $buktiNama = $pembayaran->bukti_transfer
                                    ? basename($pembayaran->bukti_transfer)
                                    : 'Tidak ada bukti';
                                $isPdf = $pembayaran->bukti_transfer
                                    && mb_strtolower(pathinfo($pembayaran->bukti_transfer, PATHINFO_EXTENSION)) === 'pdf';
                                $isAccepted = $pembayaran->status === 'lunas';
                                $statusLabel = $isAccepted ? 'Diterima' : 'Menunggu';
                                $initials = collect(preg_split('/\s+/', trim($nama)) ?: [])
                                    ->filter()
                                    ->take(2)
                                    ->map(static fn ($word) => mb_strtoupper(mb_substr($word, 0, 1)))
                                    ->implode('');
                                $acceptAction = route('admin.pembayaran.terima', $pembayaran);
                                $tanggalText = $tanggalKirim?->translatedFormat('d M Y, H:i') ?? '-';
                            @endphp

                            <tr class="group transition hover:bg-sky-50/50">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="grid h-10 w-10 shrink-0 place-items-center rounded-full bg-gradient-to-br from-sky-100 to-blue-100 text-xs font-black text-sky-700 ring-1 ring-sky-200">
                                            {{ $initials ?: 'P' }}
                                        </div>
                                        <div class="min-w-0">
                                            <p class="max-w-52 truncate text-sm font-extrabold text-slate-900" title="{{ $nama }}">{{ $nama }}</p>
                                            <p class="mt-0.5 text-xs text-slate-400">#PAY-{{ str_pad((string) $pembayaran->id_pembayaran, 5, '0', STR_PAD_LEFT) }}</p>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-5 py-4">
                                    <span @class([
                                        'inline-flex rounded-full border px-3 py-1 text-xs font-bold',
                                        'border-violet-200 bg-violet-50 text-violet-700' => $jenjang === 'Universitas',
                                        'border-sky-200 bg-sky-50 text-sky-700' => $jenjang === 'SMK',
                                    ])>
                                        {{ $jenjang }}
                                    </span>
                                </td>

                                <td class="px-5 py-4">
                                    <span class="text-sm font-semibold text-slate-700">{{ $telepon }}</span>
                                </td>

                                <td class="px-5 py-4">
                                    @if ($tanggalKirim)
                                        <p class="text-sm font-semibold text-slate-700">{{ $tanggalKirim->translatedFormat('d M Y') }}</p>
                                        <p class="mt-1 text-xs text-slate-500">{{ $tanggalKirim->format('H:i') }} WIB</p>
                                    @else
                                        <span class="text-sm italic text-slate-400">Tanggal tidak tersedia</span>
                                    @endif
                                </td>

                                <td class="px-5 py-4">
                                    @if ($buktiUrl)
                                        <button
                                            type="button"
                                            @click="openDetail(@js([
                                                'nama' => $nama,
                                                'jenjang' => $jenjang,
                                                'telepon' => $telepon,
                                                'tanggal' => $tanggalText . ' WIB',
                                                'nominal' => $formatRupiah($pembayaran->nominal),
                                                'status' => $statusLabel,
                                                'buktiUrl' => $buktiUrl,
                                                'buktiNama' => $buktiNama,
                                                'isPdf' => $isPdf,
                                                'acceptAction' => $acceptAction,
                                                'canAccept' => !$isAccepted,
                                            ]))"
                                            class="inline-flex max-w-52 items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-bold text-sky-700 shadow-sm transition hover:border-sky-200 hover:bg-sky-50"
                                        >
                                            <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                <path d="M8 12.5 12.5 8a3 3 0 1 1 4.2 4.2l-6.1 6.1a4.5 4.5 0 1 1-6.4-6.4l6-6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                            <span class="truncate">{{ $buktiNama }}</span>
                                        </button>
                                    @else
                                        <span class="inline-flex rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-500">Tidak ada bukti</span>
                                    @endif
                                </td>

                                <td class="px-5 py-4">
                                    <span @class([
                                        'inline-flex items-center gap-1.5 rounded-full border px-3 py-1 text-xs font-extrabold',
                                        'border-emerald-200 bg-emerald-50 text-emerald-700' => $isAccepted,
                                        'border-amber-200 bg-amber-50 text-amber-700' => !$isAccepted,
                                    ])>
                                        <span @class([
                                            'h-1.5 w-1.5 rounded-full',
                                            'bg-emerald-500' => $isAccepted,
                                            'bg-amber-500' => !$isAccepted,
                                        ])></span>
                                        {{ $statusLabel }}
                                    </span>
                                </td>

                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <button
                                            type="button"
                                            @click="openDetail(@js([
                                                'nama' => $nama,
                                                'jenjang' => $jenjang,
                                                'telepon' => $telepon,
                                                'tanggal' => $tanggalText . ' WIB',
                                                'nominal' => $formatRupiah($pembayaran->nominal),
                                                'status' => $statusLabel,
                                                'buktiUrl' => $buktiUrl ?? '',
                                                'buktiNama' => $buktiNama,
                                                'isPdf' => $isPdf,
                                                'acceptAction' => $acceptAction,
                                                'canAccept' => !$isAccepted,
                                            ]))"
                                            class="inline-flex items-center gap-2 rounded-xl border border-sky-200 bg-sky-50 px-3.5 py-2 text-xs font-extrabold text-sky-700 transition hover:-translate-y-0.5 hover:bg-sky-100 focus:outline-none focus:ring-4 focus:ring-sky-100"
                                        >
                                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                                <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.8"/>
                                            </svg>
                                            Show Detail
                                        </button>

                                        @if (!$isAccepted)
                                            <form method="POST" action="{{ $acceptAction }}" onsubmit="return confirm('Terima pembayaran ini dan ubah status menjadi lunas?')">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-500 px-3.5 py-2 text-xs font-extrabold text-white shadow-[0_8px_18px_rgba(16,185,129,0.22)] transition hover:-translate-y-0.5 focus:outline-none focus:ring-4 focus:ring-emerald-100">
                                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                        <path d="m5 12 4 4L19 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                    </svg>
                                                    Terima
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-16 text-center">
                                    <div class="mx-auto grid h-14 w-14 place-items-center rounded-2xl bg-slate-100 text-slate-400">
                                        <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                            <path d="M4 7.5h16v10H4v-10Zm0 3h16M8 15h3" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </div>
                                    <p class="mt-4 font-extrabold text-slate-700">Data pembayaran tidak ditemukan.</p>
                                    <p class="mt-1 text-sm text-slate-500">Ubah filter atau tunggu peserta mengirim bukti pembayaran.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <footer class="flex flex-col gap-4 border-t border-slate-200 bg-white px-6 py-4 md:flex-row md:items-center md:justify-between">
                <p class="text-xs font-medium text-slate-500">
                    Menampilkan {{ $pembayarans->firstItem() ?? 0 }}–{{ $pembayarans->lastItem() ?? 0 }}
                    dari {{ $pembayarans->total() }} data
                </p>
                <div>{{ $pembayarans->onEachSide(1)->links() }}</div>
            </footer>
        </section>

        {{-- Modal detail pembayaran --}}
        <template x-teleport="body">
            <div
                x-cloak
                x-show="detailOpen"
                x-transition.opacity
                class="fixed inset-0 overflow-y-auto bg-slate-950/50 px-4 py-6"
                style="z-index: 2147483647; backdrop-filter: blur(4px);"
            >
                <div class="flex min-h-full items-center justify-center" @click.self="closeDetail()">
                    <article
                        x-show="detailOpen"
                        x-transition.scale.origin.center
                        role="dialog"
                        aria-modal="true"
                        aria-labelledby="detail-pembayaran-title"
                        class="w-full max-w-4xl overflow-hidden rounded-3xl border border-white/70 bg-white shadow-2xl"
                    >
                        <header class="flex items-start justify-between gap-4 bg-gradient-to-r from-sky-600 to-blue-600 px-6 py-5 text-white">
                            <div>
                                <p class="text-[10px] font-extrabold uppercase tracking-[0.16em] text-sky-100">Verifikasi pembayaran</p>
                                <h2 id="detail-pembayaran-title" class="mt-1 text-xl font-extrabold">Detail Pembayaran Peserta</h2>
                                <p class="mt-1 text-sm text-white/80">Periksa identitas dan bukti pembayaran sebelum menerima transaksi.</p>
                            </div>
                            <button type="button" @click="closeDetail()" class="rounded-xl p-2 text-white/80 transition hover:bg-white/10 hover:text-white" aria-label="Tutup detail">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="m6 6 12 12M18 6 6 18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                </svg>
                            </button>
                        </header>

                        <div class="grid gap-0 lg:grid-cols-[0.9fr_1.1fr]">
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
                                        <dd class="mt-2">
                                            <span
                                                class="inline-flex items-center gap-1.5 rounded-full border px-3 py-1 text-xs font-extrabold"
                                                :class="detail.status === 'Diterima'
                                                    ? 'border-emerald-200 bg-emerald-50 text-emerald-700'
                                                    : 'border-amber-200 bg-amber-50 text-amber-700'"
                                            >
                                                <span
                                                    class="h-1.5 w-1.5 rounded-full"
                                                    :class="detail.status === 'Diterima' ? 'bg-emerald-500' : 'bg-amber-500'"
                                                ></span>
                                                <span x-text="detail.status"></span>
                                            </span>
                                        </dd>
                                    </div>
                                </dl>
                            </section>

                            <section class="p-6">
                                <div class="flex items-center justify-between gap-3">
                                    <div>
                                        <h3 class="text-sm font-extrabold text-slate-900">Bukti Pembayaran</h3>
                                        <p class="mt-1 max-w-md truncate text-xs text-slate-500" x-text="detail.buktiNama"></p>
                                    </div>
                                    <a
                                        x-show="detail.buktiUrl"
                                        :href="detail.buktiUrl"
                                        target="_blank"
                                        rel="noopener"
                                        class="inline-flex shrink-0 items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-bold text-sky-700 transition hover:bg-sky-50"
                                    >
                                        Buka File
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                            <path d="M14 5h5v5M19 5l-8 8M19 14v5H5V5h5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </a>
                                </div>

                                <div class="mt-4 overflow-hidden rounded-2xl border border-slate-200 bg-slate-100">
                                    <template x-if="detail.buktiUrl && !detail.isPdf">
                                        <img :src="detail.buktiUrl" alt="Bukti pembayaran" class="max-h-[520px] w-full object-contain">
                                    </template>
                                    <template x-if="detail.buktiUrl && detail.isPdf">
                                        <iframe :src="detail.buktiUrl" title="Bukti pembayaran PDF" class="h-[520px] w-full bg-white"></iframe>
                                    </template>
                                    <template x-if="!detail.buktiUrl">
                                        <div class="grid min-h-72 place-items-center p-6 text-center">
                                            <div>
                                                <svg class="mx-auto h-12 w-12 text-slate-300" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                    <path d="M8 12.5 12.5 8a3 3 0 1 1 4.2 4.2l-6.1 6.1a4.5 4.5 0 1 1-6.4-6.4l6-6" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                                                </svg>
                                                <p class="mt-3 text-sm font-bold text-slate-600">Bukti pembayaran belum tersedia.</p>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </section>
                        </div>

                        <footer class="flex flex-col-reverse gap-2 border-t border-slate-200 bg-slate-50 px-6 py-4 sm:flex-row sm:items-center sm:justify-end">
                            <button type="button" @click="closeDetail()" class="rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-bold text-slate-600 transition hover:bg-slate-100">
                                Tutup
                            </button>

                            <form
                                x-show="detail.canAccept"
                                method="POST"
                                :action="detail.acceptAction"
                                onsubmit="return confirm('Terima pembayaran ini dan ubah status menjadi lunas?')"
                            >
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-500 px-5 py-2.5 text-sm font-extrabold text-white shadow-[0_8px_18px_rgba(16,185,129,0.22)] transition hover:-translate-y-0.5 focus:outline-none focus:ring-4 focus:ring-emerald-100 sm:w-auto">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <path d="m5 12 4 4L19 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                    Terima Pembayaran
                                </button>
                            </form>
                        </footer>
                    </article>
                </div>
            </div>
        </template>
    </div>
@endsection
