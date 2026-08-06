@extends('layouts.portal')

@section('title', 'Permintaan Magang')

@section('content')
<div x-data="{
    detailOpen: false,
    detail: {},
    openDetail(data) {
        data.status = data.status === 'diterima' ? 'disetujui' : (data.status || 'menunggu');
        this.detail = data;
        this.detailOpen = true;
    },
    closeDetail() {
        this.detailOpen = false;
        this.detail = {};
    },
    statusLabel(status) {
        if (status === 'disetujui' || status === 'diterima') return 'Disetujui';
        return 'Menunggu';
    }
}" @keydown.escape.window="closeDetail()" x-effect="document.body.classList.toggle('overflow-hidden', detailOpen)">

    {{-- Judul --}}
    <section class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 class="mt-5 text-2xl font-extrabold tracking-tight text-slate-950 sm:text-3xl">Permintaan Magang</h1>
            <p class="mt-1 text-sm text-slate-500">Tinjau data pendaftar serta tentukan status pengajuan magang di CV Natusi.</p>
        </div>
    </section>

    @php
        $requestedStatus = request('status', 'all');
        $currentStatus = in_array($requestedStatus, ['menunggu', 'disetujui'], true) ? $requestedStatus : 'all';
    @endphp

    {{-- Tabel --}}
    <section class="mt-5 overflow-hidden rounded-3xl border border-sky-100/90 bg-white/95 shadow-[0_20px_50px_rgba(15,52,94,0.09)] backdrop-blur">

        {{-- Header --}}
        <div class="flex flex-col gap-4 border-b border-sky-100 bg-gradient-to-r from-sky-50 via-blue-50 to-cyan-50 px-6 py-5 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h2 class="text-lg font-bold text-slate-950">Data Pengajuan Magang</h2>
                <p class="mt-0.5 text-sm text-slate-500">Gunakan tombol Show Detail untuk melihat data lengkap pendaftar.</p>
            </div>
        </div>

        {{-- Filter --}}
        <div class="flex flex-col gap-3 border-b border-sky-100 bg-white px-6 py-4 md:flex-row md:items-center md:justify-between">
            <form id="filterForm" method="GET" action="{{ url()->current() }}" class="flex flex-wrap items-center gap-2">
                <input type="hidden" name="status" id="statusInput" value="{{ $currentStatus }}">

                @foreach(['all' => 'Semua', 'menunggu' => 'Menunggu', 'disetujui' => 'Disetujui'] as $filterValue => $filterLabel)
                    <button type="button" onclick="submitFilter('{{ $filterValue }}')"
                        class="rounded-xl px-4 py-2 text-xs font-bold transition duration-200 {{ $currentStatus === $filterValue ? 'bg-gradient-to-r from-sky-500 to-blue-600 text-white shadow-[0_8px_18px_rgba(2,132,199,0.22)]' : 'border border-slate-200 bg-white text-slate-600 hover:border-sky-200 hover:bg-sky-50 hover:text-sky-700' }}">
                        {{ $filterLabel }}
                    </button>
                @endforeach
            </form>

            <p class="text-xs text-slate-500">
                Menampilkan <strong class="text-slate-700">{{ $permintaan_magang->firstItem() ?? 0 }}–{{ $permintaan_magang->lastItem() ?? 0 }}</strong>
                dari <strong class="text-slate-700">{{ $permintaan_magang->total() }}</strong> pengajuan
            </p>
        </div>

        {{-- Tabel --}}
        <div class="overflow-x-auto">
            <table class="min-w-[1180px] w-full divide-y divide-slate-200">
                <thead class="bg-gradient-to-r from-sky-50 via-blue-50 to-cyan-50">
                    <tr>
                        <th class="px-5 py-3.5 text-left text-[11px] font-bold uppercase tracking-[0.09em] text-slate-500">Nama</th>
                        <th class="px-5 py-3.5 text-left text-[11px] font-bold uppercase tracking-[0.09em] text-slate-500">Alamat</th>
                        <th class="px-5 py-3.5 text-left text-[11px] font-bold uppercase tracking-[0.09em] text-slate-500">Email</th>
                        <th class="px-5 py-3.5 text-left text-[11px] font-bold uppercase tracking-[0.09em] text-slate-500">Instansi</th>
                        <th class="px-5 py-3.5 text-left text-[11px] font-bold uppercase tracking-[0.09em] text-slate-500">Tanggal Pengajuan</th>
                        <th class="px-5 py-3.5 text-center text-[11px] font-bold uppercase tracking-[0.09em] text-slate-500">Status</th>
                        <th class="px-5 py-3.5 text-center text-[11px] font-bold uppercase tracking-[0.09em] text-slate-500">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white/80">
                    @forelse ($permintaan_magang as $item)
                        @php
                            $id = $item->id_permintaan ?? $item->id ?? null;
                            $nama = $item->nama_pemohon ?? $item->nama ?? '-';
                            $alamat = $item->alamat ?? '-';
                            $email = $item->email ?? '-';
                            $instansi = $item->nama_sekolah ?? $item->institusi ?? '-';
                            $noInduk = $item->no_induk ?? '-';
                            $jurusan = $item->jurusan ?? '-';
                            $noHp = $item->no_hp ?? $item->kontak ?? '-';
                            $pesan = $item->pesan ?? '-';
                            $status = strtolower($item->status ?? 'menunggu');
                            $status = $status === 'diterima' ? 'disetujui' : $status;
                            $tanggal = !empty($item->created_at) ? \Carbon\Carbon::parse($item->created_at)->locale('id')->translatedFormat('d M Y, H:i') : '-';
                            $initial = strtoupper(mb_substr(trim($nama), 0, 2));
                            $detailPayload = [
                                'id' => $id,
                                'nama' => $nama,
                                'alamat' => $alamat,
                                'email' => $email,
                                'instansi' => $instansi,
                                'no_induk' => $noInduk,
                                'jurusan' => $jurusan,
                                'no_hp' => $noHp,
                                'pesan' => $pesan,
                                'status' => $status,
                                'tanggal_pengajuan' => $tanggal,
                                'action_url' => $id ? route('admin-peserta.permintaan.action', $id) : '#',
                            ];
                        @endphp

                        <tr class="transition duration-200 hover:bg-sky-50/80">
                            <td class="whitespace-nowrap px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <span class="grid h-10 w-10 shrink-0 place-items-center rounded-2xl bg-gradient-to-br from-sky-100 to-cyan-100 text-xs font-extrabold text-sky-700 ring-1 ring-sky-200/80">
                                        {{ $initial ?: 'PM' }}
                                    </span>
                                    <div class="min-w-0">
                                        <p class="max-w-52 truncate text-sm font-bold text-slate-900">{{ $nama }}</p>
                                        <p class="mt-0.5 text-xs text-slate-500">ID Pengajuan #{{ $id ?? '-' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-4">
                                <p class="max-w-56 truncate text-sm text-slate-600" title="{{ $alamat }}">{{ \Illuminate\Support\Str::limit($alamat, 42) }}</p>
                            </td>
                            <td class="px-5 py-4">
                                <p class="max-w-56 truncate text-sm font-medium text-slate-700" title="{{ $email }}">{{ $email }}</p>
                            </td>
                            <td class="px-5 py-4">
                                <p class="max-w-56 truncate text-sm font-semibold text-slate-700" title="{{ $instansi }}">{{ $instansi }}</p>
                            </td>
                            <td class="whitespace-nowrap px-5 py-4 text-sm text-slate-600">{{ $tanggal }}</td>
                            <td class="whitespace-nowrap px-5 py-4 text-center">
                                @if($status === 'disetujui')
                                    <span class="inline-flex items-center gap-1.5 rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1.5 text-xs font-bold text-emerald-700">
                                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span> Disetujui
                                    </span>
                                @elseif($status === 'perlu_revisi')
                                    <span class="inline-flex items-center gap-1.5 rounded-full border border-amber-200 bg-amber-50 px-3 py-1.5 text-xs font-bold text-amber-700">Perlu Revisi</span>
                                @elseif($status === 'ditolak')
                                    <span class="inline-flex items-center gap-1.5 rounded-full border border-rose-200 bg-rose-50 px-3 py-1.5 text-xs font-bold text-rose-700">Ditolak</span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 rounded-full border border-amber-200 bg-amber-50 px-3 py-1.5 text-xs font-bold text-amber-700">
                                        <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span> Menunggu
                                    </span>
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-5 py-4 text-center">
                                <button type="button" @click='openDetail(@json($detailPayload, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT))'
                                    class="inline-flex items-center justify-center gap-2 rounded-xl border border-sky-200 bg-sky-50 px-4 py-2 text-xs font-bold text-sky-700 transition duration-200 hover:-translate-y-0.5 hover:border-sky-300 hover:bg-sky-100">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    Show Detail
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-16 text-center">
                                <span class="mx-auto grid h-14 w-14 place-items-center rounded-2xl bg-slate-100 text-slate-400">
                                    <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                                </span>
                                <p class="mt-4 text-sm font-bold text-slate-700">Data pengajuan tidak ditemukan</p>
                                <p class="mt-1 text-xs text-slate-500">Coba ubah filter status.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if ($permintaan_magang->hasPages())
            <div class="border-t border-sky-100 bg-slate-50/60 px-6 py-4">
                {{ $permintaan_magang->appends(request()->query())->links() }}
            </div>
        @endif
    </section>

    {{-- Modal Detail --}}
    <div x-show="detailOpen" x-cloak x-transition.opacity class="fixed inset-0 z-[70] flex items-center justify-center bg-slate-950/60 p-4 backdrop-blur-sm" @click.self="closeDetail()">
        <section x-show="detailOpen" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-4 scale-95" x-transition:enter-end="opacity-100 translate-y-0 scale-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0 scale-100" x-transition:leave-end="opacity-0 translate-y-4 scale-95" class="flex max-h-[95vh] w-full max-w-5xl flex-col overflow-hidden rounded-3xl bg-white shadow-[0_30px_80px_rgba(15,23,42,0.30)]">
            <header class="flex items-start justify-between gap-4 border-b border-sky-100 bg-gradient-to-r from-sky-50 via-blue-50 to-cyan-50 px-6 py-5">
                <div>
                    <span class="inline-flex items-center gap-2 rounded-full bg-white px-3 py-1 text-[10px] font-bold uppercase tracking-[0.14em] text-sky-700 ring-1 ring-sky-200">Detail Pengajuan</span>
                    <h3 class="mt-3 text-xl font-extrabold text-slate-950" x-text="detail.nama || '-'">-</h3>
                    <p class="mt-1 text-sm text-slate-500">Informasi lengkap calon peserta magang.</p>
                </div>
                <button type="button" @click="closeDetail()" class="grid h-10 w-10 shrink-0 place-items-center rounded-xl border border-slate-200 bg-white text-slate-500 transition hover:border-rose-200 hover:bg-rose-50 hover:text-rose-600" aria-label="Tutup detail">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </header>

            <div class="max-h-[calc(92vh-168px)] overflow-y-auto px-6 py-6">
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    <div class="rounded-2xl border border-slate-200 bg-slate-50/70 p-4">
                        <p class="text-[10px] font-bold uppercase tracking-[0.12em] text-slate-400">Nama Lengkap</p>
                        <p class="mt-2 break-words text-sm font-bold text-slate-900" x-text="detail.nama || '-'">-</p>
                    </div>
                    <div class="rounded-2xl border border-slate-200 bg-slate-50/70 p-4">
                        <p class="text-[10px] font-bold uppercase tracking-[0.12em] text-slate-400">Email</p>
                        <p class="mt-2 break-all text-sm font-semibold text-slate-700" x-text="detail.email || '-'">-</p>
                    </div>
                    <div class="rounded-2xl border border-slate-200 bg-slate-50/70 p-4">
                        <p class="text-[10px] font-bold uppercase tracking-[0.12em] text-slate-400">Nomor HP</p>
                        <p class="mt-2 break-words text-sm font-semibold text-slate-700" x-text="detail.no_hp || '-'">-</p>
                    </div>
                    <div class="rounded-2xl border border-slate-200 bg-slate-50/70 p-4 sm:col-span-2">
                        <p class="text-[10px] font-bold uppercase tracking-[0.12em] text-slate-400">Alamat</p>
                        <p class="mt-2 whitespace-pre-line break-words text-sm font-semibold leading-6 text-slate-700" x-text="detail.alamat || '-'">-</p>
                    </div>
                    <div class="rounded-2xl border border-slate-200 bg-slate-50/70 p-4">
                        <p class="text-[10px] font-bold uppercase tracking-[0.12em] text-slate-400">Instansi</p>
                        <p class="mt-2 break-words text-sm font-semibold text-slate-700" x-text="detail.instansi || '-'">-</p>
                    </div>
                    <div class="rounded-2xl border border-slate-200 bg-slate-50/70 p-4">
                        <p class="text-[10px] font-bold uppercase tracking-[0.12em] text-slate-400">Nomor Induk</p>
                        <p class="mt-2 break-words text-sm font-semibold text-slate-700" x-text="detail.no_induk || '-'">-</p>
                    </div>
                    <div class="rounded-2xl border border-slate-200 bg-slate-50/70 p-4">
                        <p class="text-[10px] font-bold uppercase tracking-[0.12em] text-slate-400">Jurusan</p>
                        <p class="mt-2 break-words text-sm font-semibold text-slate-700" x-text="detail.jurusan || '-'">-</p>
                    </div>
                    <div class="rounded-2xl border border-slate-200 bg-slate-50/70 p-4">
                        <p class="text-[10px] font-bold uppercase tracking-[0.12em] text-slate-400">Tanggal Pengajuan</p>
                        <p class="mt-2 text-sm font-semibold text-slate-700" x-text="detail.tanggal_pengajuan || '-'">-</p>
                    </div>
                    <div class="rounded-2xl border border-slate-200 bg-slate-50/70 p-4">
                        <p class="text-[10px] font-bold uppercase tracking-[0.12em] text-slate-400">Status Pengajuan</p>
                        <p class="mt-2 text-sm font-extrabold text-slate-800" x-text="statusLabel(detail.status)">-</p>
                    </div>
                    <div class="rounded-2xl border border-slate-200 bg-slate-50/70 p-4 sm:col-span-2 lg:col-span-3">
                        <p class="text-[10px] font-bold uppercase tracking-[0.12em] text-slate-400">Pesan atau Keterangan</p>
                        <p class="mt-2 whitespace-pre-line break-words text-sm font-medium leading-6 text-slate-700" x-text="detail.pesan || '-'">-</p>
                    </div>
                </div>
            </div>

            <footer class="flex flex-col gap-3 border-t border-sky-100 bg-slate-50 px-6 py-4 sm:flex-row sm:items-center sm:justify-between">
                <button type="button" @click="closeDetail()" class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-bold text-slate-600 transition hover:bg-slate-100">Tutup</button>

                <template x-if="detail.status === 'menunggu'">
                    <div class="flex flex-col gap-2 sm:flex-row">
                        <form :action="detail.action_url" method="POST" onsubmit="return confirm('Setujui pengajuan magang ini?')">
                            @csrf
                            <input type="hidden" name="action" value="approve">
                            <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-600 px-5 py-2.5 text-sm font-bold text-white shadow-[0_10px_22px_rgba(5,150,105,0.24)] transition hover:-translate-y-0.5 hover:from-emerald-600 hover:to-teal-700">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Disetujui
                            </button>
                        </form>
                        <form :action="detail.action_url" method="POST" onsubmit="const reason = prompt('Masukkan alasan penolakan (wajib):'); if (!reason || !reason.trim()) return false; this.querySelector('[name=alasan_penolakan]').value = reason.trim(); return true;">
                            @csrf
                            <input type="hidden" name="action" value="reject">
                            <input type="hidden" name="alasan_penolakan" value="">
                            <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-rose-500 to-red-600 px-5 py-2.5 text-sm font-bold text-white shadow-[0_10px_22px_rgba(225,29,72,0.22)] transition hover:-translate-y-0.5 hover:from-rose-600 hover:to-red-700">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                Ditolak
                            </button>
                        </form>
                        <form :action="detail.action_url" method="POST" onsubmit="const note = prompt('Masukkan catatan revisi (wajib):'); if (!note || !note.trim()) return false; this.querySelector('[name=catatan_revisi]').value = note.trim(); return true;">
                            @csrf
                            <input type="hidden" name="action" value="revision">
                            <input type="hidden" name="catatan_revisi" value="">
                            <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-amber-500 px-5 py-2.5 text-sm font-bold text-white shadow-[0_10px_22px_rgba(217,119,6,0.22)] transition hover:-translate-y-0.5 hover:bg-amber-600">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Perlu Revisi
                            </button>
                        </form>
                    </div>
                </template>

                <template x-if="detail.status !== 'menunggu'">
                    <span class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-500">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Pengajuan sudah diproses
                    </span>
                </template>
            </footer>
        </section>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function submitFilter(status) {
        const statusInput = document.getElementById('statusInput');
        const filterForm = document.getElementById('filterForm');
        if (!statusInput || !filterForm) return;
        statusInput.value = status;
        filterForm.submit();
    }
</script>
@endpush