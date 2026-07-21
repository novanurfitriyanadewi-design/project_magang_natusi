@extends('layouts.portal')

@section('title', 'Data Pembayaran')

@section('content')
    @php
        $formatRupiah = fn ($n) => 'Rp ' . number_format((float) $n, 0, ',', '.');
    @endphp

    <div
        x-data="{
            tolakOpen: false,
            tolakAction: '',
            buktiOpen: false,
            buktiUrl: '',
            openTolak(action) {
                this.tolakAction = action;
                this.tolakOpen = true;
            },
            openBukti(url) {
                this.buktiUrl = url;
                this.buktiOpen = true;
            },
            closeModals() {
                this.tolakOpen = false;
                this.buktiOpen = false;
            },
        }"
        @keydown.escape.window="closeModals()"
        x-effect="document.body.classList.toggle('overflow-hidden', tolakOpen || buktiOpen)"
        class="p-6"
    >
        {{-- Page Header --}}
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
            <div>
                <nav class="flex items-center gap-2 text-[#3f4850] text-[11px] font-semibold uppercase tracking-[0.05em] mb-2">
                    <span>Admin Portal</span>
                    <svg class="h-3 w-3" viewBox="0 0 24 24" fill="none"><path d="m9 6 6 6-6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    <span class="text-[#006191]">Data Pembayaran</span>
                </nav>
                <h2 class="text-[28px] leading-[36px] font-bold tracking-tight text-[#0b1c30]">Data Pembayaran</h2>
                <p class="text-sm text-[#3f4850] mt-1 max-w-2xl">Monitor dan kelola transaksi pembayaran magang.</p>
            </div>
        </div>

        @if (session('success'))
            <div class="mt-5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mt-5 rounded-xl border border-[#ba1a1a]/30 bg-[#ffdad6] px-4 py-3 text-sm text-[#93000a]">
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mt-5 rounded-xl border border-[#ba1a1a]/30 bg-[#ffdad6] px-4 py-3 text-sm text-[#93000a]">
                <p class="font-bold">Data belum dapat disimpan.</p>
                <p class="mt-1">Periksa kembali kolom yang ditandai pada formulir.</p>
            </div>
        @endif

        {{-- Ringkasan --}}
        <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white border border-[#bec7d2] rounded-xl p-5 border-l-4 border-l-[#006191] shadow-[0_4px_8px_-2px_rgba(0,97,145,0.04),0_2px_4px_-2px_rgba(0,97,145,0.02)] flex flex-col gap-1">
                <span class="text-[11px] font-bold uppercase tracking-widest text-[#3f4850]">Total Pembayaran Diterima</span>
                <div class="flex items-baseline gap-2">
                    <span class="text-2xl font-bold text-[#006191]">{{ $formatRupiah($totalDiterima) }}</span>
                </div>
                <p class="text-[11px] text-[#3f4850]">Performa 30 hari terakhir</p>
            </div>

            <div class="bg-white border border-[#bec7d2] rounded-xl p-5 border-l-4 border-l-[#bb0014] shadow-[0_4px_8px_-2px_rgba(0,97,145,0.04),0_2px_4px_-2px_rgba(0,97,145,0.02)] flex flex-col gap-1">
                <span class="text-[11px] font-bold uppercase tracking-widest text-[#3f4850]">Pembayaran Belum Diterima</span>
                <div class="flex items-baseline gap-2">
                    <span class="text-2xl font-bold text-[#bb0014]">{{ $formatRupiah($totalBelumDiterima) }}</span>
                    <span class="text-xs text-[#3f4850]">{{ $countBelumDiterima }} transaksi</span>
                </div>
                <p class="text-[11px] text-[#3f4850]">Perlu segera ditindaklanjuti</p>
            </div>
        </div>

        {{-- Filter --}}
        <form method="GET" action="{{ route('admin.pembayaran.index') }}"
              class="mt-6 bg-white border border-[#bec7d2] rounded-xl p-4 flex flex-wrap gap-4 items-center shadow-sm">
            <div class="relative flex-grow min-w-[200px]">
                <svg class="h-[18px] w-[18px] absolute left-3 top-1/2 -translate-y-1/2 text-[#6f7881]" viewBox="0 0 24 24" fill="none"><circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="1.8"/><path d="m20 20-3.5-3.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                <input type="text" name="search" value="{{ $search }}"
                       placeholder="Cari nama peserta..."
                       class="w-full bg-[#f8f9ff] border border-[#bec7d2] rounded-lg pl-10 pr-3 py-2.5 text-sm focus:ring-2 focus:ring-[#006191]/20 focus:border-[#006191] outline-none transition-all">
            </div>

            <select name="status" onchange="this.form.submit()"
                    class="bg-[#f8f9ff] border border-[#bec7d2] rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-[#006191]/20 focus:border-[#006191] outline-none transition-all">
                <option value="">Semua Status</option>
                <option value="lunas" @selected($status === 'lunas')>Lunas</option>
                <option value="menunggu" @selected($status === 'menunggu')>Menunggu</option>
                <option value="ditolak" @selected($status === 'ditolak')>Ditolak</option>
            </select>

            <div class="flex items-center gap-2">
                <input type="date" name="dari_tanggal" value="{{ $dariTgl }}"
                       class="bg-[#f8f9ff] border border-[#bec7d2] rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-[#006191]/20 focus:border-[#006191] outline-none transition-all">
                <span class="text-xs text-[#6f7881]">s/d</span>
                <input type="date" name="sampai_tanggal" value="{{ $sampaiTgl }}"
                       class="bg-[#f8f9ff] border border-[#bec7d2] rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-[#006191]/20 focus:border-[#006191] outline-none transition-all">
            </div>

            <button type="submit"
                    class="bg-[#006191] text-white px-6 py-2.5 rounded-lg font-bold text-xs uppercase tracking-wide hover:brightness-110 transition-all">
                Terapkan
            </button>

            @if ($search !== '' || $status !== '' || $dariTgl || $sampaiTgl)
                <a href="{{ route('admin.pembayaran.index') }}" class="ml-auto text-[#006191] font-bold text-xs uppercase tracking-wide hover:underline">
                    Hapus Semua Filter
                </a>
            @endif
        </form>

        {{-- Tabel --}}
        <div class="mt-6 bg-white border border-[#bec7d2] rounded-xl overflow-hidden shadow-[0_4px_8px_-2px_rgba(0,97,145,0.04),0_2px_4px_-2px_rgba(0,97,145,0.02)]">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-[#e5eeff] text-[#3f4850] text-[11px] font-bold uppercase tracking-widest">
                        <tr>
                            <th class="px-6 py-4 border-b border-[#bec7d2]">ID Transaksi</th>
                            <th class="px-6 py-4 border-b border-[#bec7d2]">Nama Peserta</th>
                            <th class="px-6 py-4 border-b border-[#bec7d2]">Tanggal</th>
                            <th class="px-6 py-4 border-b border-[#bec7d2]">Jumlah</th>
                            <th class="px-6 py-4 border-b border-[#bec7d2]">Status</th>
                            <th class="px-6 py-4 border-b border-[#bec7d2] text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm text-[#0b1c30]">
                        @forelse ($pembayarans as $pembayaran)
                            @php
                                $nama = $pembayaran->peserta->user->nama ?? '-';
                                $initial = $nama !== '-' ? strtoupper(substr($nama, 0, 1)) : '?';
                                $buktiUrl = $pembayaran->bukti_transfer ? Storage::url($pembayaran->bukti_transfer) : null;

                                $statusLabel = match ($pembayaran->status) {
                                    'lunas' => 'Lunas',
                                    'ditolak' => 'Ditolak',
                                    default => 'Menunggu',
                                };

                                $statusClass = match ($pembayaran->status) {
                                    'lunas' => 'bg-green-100 text-green-800',
                                    'ditolak' => 'bg-red-100 text-red-800',
                                    default => 'bg-amber-100 text-amber-800',
                                };

                                $statusDotClass = match ($pembayaran->status) {
                                    'lunas' => 'bg-green-600',
                                    'ditolak' => 'bg-red-600',
                                    default => 'bg-amber-600',
                                };
                            @endphp
                            <tr class="hover:bg-[#eff4ff] transition-colors group">
                                <td class="px-6 py-4 border-b border-[#bec7d2] font-medium text-[#006191]">
                                    #TXN-{{ str_pad($pembayaran->id_pembayaran, 5, '0', STR_PAD_LEFT) }}
                                </td>
                                <td class="px-6 py-4 border-b border-[#bec7d2]">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-[#006191]/10 flex items-center justify-center font-bold text-[#006191] text-xs">
                                            {{ $initial }}
                                        </div>
                                        <span class="font-medium">{{ $nama }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 border-b border-[#bec7d2] text-[#3f4850]">
                                    {{ $pembayaran->tgl_bayar?->translatedFormat('d M Y') ?? '-' }}
                                </td>
                                <td class="px-6 py-4 border-b border-[#bec7d2] font-bold">
                                    {{ $formatRupiah($pembayaran->nominal) }}
                                </td>
                                <td class="px-6 py-4 border-b border-[#bec7d2]">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full {{ $statusClass }} text-[11px] font-bold">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $statusDotClass }}"></span>
                                        {{ strtoupper($statusLabel) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 border-b border-[#bec7d2] text-right">
                                    <div class="flex justify-end gap-2">
                                        @if ($buktiUrl)
                                            <button type="button" @click="openBukti(@js($buktiUrl))"
                                                    class="px-3 py-1 border border-[#6f7881] text-[#3f4850] rounded-lg text-[11px] font-bold hover:bg-[#e5eeff] transition-colors flex items-center gap-1">
                                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7Z" stroke="currentColor" stroke-width="1.7"/><circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.7"/></svg>
                                                Lihat Bukti
                                            </button>
                                        @endif

                                        @if ($pembayaran->status === 'menunggu')
                                            <form method="POST" action="{{ route('admin.pembayaran.terima', $pembayaran) }}">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit"
                                                        class="px-3 py-1 border border-green-600 text-green-600 rounded-lg text-[11px] font-bold hover:bg-green-600 hover:text-white transition-colors">
                                                    Terima
                                                </button>
                                            </form>

                                            <button type="button"
                                                    @click="openTolak(@js(route('admin.pembayaran.tolak', $pembayaran)))"
                                                    class="px-3 py-1 border border-red-600 text-red-600 rounded-lg text-[11px] font-bold hover:bg-red-600 hover:text-white transition-colors">
                                                Tolak
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-14 text-center border-b border-[#bec7d2]">
                                    <p class="font-bold text-[#0b1c30]">Tidak ada transaksi pembayaran untuk filter ini.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 bg-[#e5eeff] flex flex-col md:flex-row justify-between items-center gap-3 border-t border-[#bec7d2]">
                <span class="text-xs text-[#3f4850] font-medium">
                    Menampilkan {{ $pembayarans->firstItem() ?? 0 }}-{{ $pembayarans->lastItem() ?? 0 }}
                    dari {{ $pembayarans->total() }} transaksi
                </span>
                {{ $pembayarans->onEachSide(1)->links() }}
            </div>
        </div>

        {{-- Modal Tolak Pembayaran --}}
        <template x-teleport="body">
            <div
                x-cloak
                x-show="tolakOpen"
                x-transition.opacity
                class="fixed inset-0 overflow-y-auto bg-slate-950/45"
                style="z-index: 2147483647; backdrop-filter: blur(3px);"
            >
                <div class="flex min-h-full items-center justify-center px-4 py-6" @click.self="tolakOpen = false">
                    <article
                        x-show="tolakOpen"
                        x-transition.scale.origin.center
                        role="dialog"
                        aria-modal="true"
                        class="w-full max-w-md overflow-hidden rounded-xl border border-[#bec7d2] bg-white shadow-2xl"
                    >
                        <header class="flex items-start justify-between gap-4 bg-[#bb0014] px-6 py-5 text-white">
                            <div>
                                <h2 class="text-xl font-bold">Tolak Pembayaran</h2>
                                <p class="mt-1 text-sm text-white/80">Berikan alasan penolakan untuk peserta.</p>
                            </div>
                            <button type="button" @click="tolakOpen = false" class="rounded-lg p-2 text-white/80 transition hover:bg-white/10 hover:text-white">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none"><path d="m6 6 12 12M18 6 6 18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                            </button>
                        </header>

                        <form method="POST" :action="tolakAction" class="p-6">
                            @csrf
                            @method('PATCH')

                            <label for="keterangan_tolak" class="mb-1.5 block text-sm font-bold text-[#0b1c30]">Alasan Penolakan</label>
                            <textarea id="keterangan_tolak" name="keterangan" rows="4" required maxlength="1000"
                                      placeholder="Contoh: Bukti transfer tidak jelas / nominal tidak sesuai"
                                      class="w-full rounded-lg border-[#bec7d2] focus:border-[#bb0014] focus:ring-[#bb0014] text-sm"></textarea>

                            <div class="mt-6 flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
                                <button type="button" @click="tolakOpen = false" class="rounded-lg border border-[#bec7d2] px-5 py-2.5 text-sm font-bold text-[#3f4850] transition hover:bg-[#eff4ff]">Batal</button>
                                <button type="submit" class="rounded-lg bg-[#bb0014] px-5 py-2.5 text-sm font-bold text-white shadow-lg transition hover:brightness-110">Tolak Pembayaran</button>
                            </div>
                        </form>
                    </article>
                </div>
            </div>
        </template>

        {{-- Modal Lihat Bukti --}}
        <template x-teleport="body">
            <div
                x-cloak
                x-show="buktiOpen"
                x-transition.opacity
                class="fixed inset-0 overflow-y-auto bg-slate-950/45"
                style="z-index: 2147483647; backdrop-filter: blur(3px);"
            >
                <div class="flex min-h-full items-center justify-center px-4 py-6" @click.self="buktiOpen = false">
                    <article
                        x-show="buktiOpen"
                        x-transition.scale.origin.center
                        role="dialog"
                        aria-modal="true"
                        class="w-full max-w-lg overflow-hidden rounded-xl border border-[#bec7d2] bg-white shadow-2xl"
                    >
                        <header class="flex items-start justify-between gap-4 bg-[#006191] px-6 py-5 text-white">
                            <h2 class="text-xl font-bold">Bukti Transfer</h2>
                            <button type="button" @click="buktiOpen = false" class="rounded-lg p-2 text-white/80 transition hover:bg-white/10 hover:text-white">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none"><path d="m6 6 12 12M18 6 6 18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                            </button>
                        </header>
                        <div class="p-6">
                            <img :src="buktiUrl" alt="Bukti transfer" class="w-full rounded-lg border border-[#bec7d2]">
                            <a :href="buktiUrl" target="_blank" class="mt-4 inline-block text-[#006191] font-bold text-sm hover:underline">Buka di tab baru →</a>
                        </div>
                    </article>
                </div>
            </div>
        </template>
    </div>
@endsection