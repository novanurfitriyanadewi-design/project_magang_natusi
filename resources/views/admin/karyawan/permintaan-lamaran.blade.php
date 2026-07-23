@extends('layouts.portal')

@section('title', 'Permintaan Lamaran')

@section('content')
<div
    x-data="{
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
    }"
    @keydown.escape.window="closeDetail()"
    x-effect="document.body.classList.toggle('overflow-hidden', detailOpen)"
>
    <!-- Header Section -->
    <section class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 class="mt-5 text-2xl font-extrabold tracking-tight text-slate-950 sm:text-3xl">Permintaan Lamaran</h1>
            <p class="mt-1 text-sm text-slate-500">Tinjau data pelamar serta tentukan status pengajuan lamaran kerja di CV Natusi.</p>
        </div>
    </section>

    <!-- Stat Cards -->
    <section class="mt-5 grid gap-4 sm:grid-cols-2">
        <article class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-indigo-600 to-blue-500 p-5 text-white shadow-[0_16px_36px_rgba(79,70,229,0.18)]">
            <div class="absolute -bottom-12 -right-8 h-36 w-36 rounded-full border-[22px] border-white/10"></div>
            <div class="relative flex items-start justify-between gap-4">
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-[0.15em] text-indigo-100">Jumlah Pelamar</p>
                    <p class="mt-3 text-4xl font-extrabold">{{ number_format($total_pendaftar ?? 0) }}</p>
                    <p class="mt-1 text-sm text-indigo-100">Seluruh pengajuan lamaran yang tersimpan</p>
                </div>
                <span class="grid h-12 w-12 place-items-center rounded-2xl bg-white/15 ring-1 ring-white/20">
                    <span class="material-symbols-outlined text-[27px]">badge</span>
                </span>
            </div>
        </article>

        <article class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-teal-600 to-emerald-500 p-5 text-white shadow-[0_16px_36px_rgba(13,148,136,0.18)]">
            <div class="absolute -right-6 -top-10 h-32 w-32 rounded-[36px] border border-white/15"></div>
            <div class="relative flex items-start justify-between gap-4">
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-[0.15em] text-emerald-100">Jumlah Disetujui</p>
                    <p class="mt-3 text-4xl font-extrabold">{{ number_format($total_disetujui ?? 0) }}</p>
                    <p class="mt-1 text-sm text-emerald-100">Pelamar kerja yang telah diterima</p>
                </div>
                <span class="grid h-12 w-12 place-items-center rounded-2xl bg-white/15 ring-1 ring-white/20">
                    <span class="material-symbols-outlined text-[27px]">verified</span>
                </span>
            </div>
        </article>
    </section>

    @php
        $requestedStatus = request('status', 'all');
        $currentStatus = in_array($requestedStatus, ['menunggu', 'disetujui'], true)
            ? $requestedStatus
            : 'all';
        $searchQuery = request('search', '');
    @endphp

    <!-- Data Table & Filter Section -->
    <section class="mt-5 overflow-hidden rounded-3xl border border-sky-100/90 bg-white/95 shadow-[0_20px_50px_rgba(15,52,94,0.09)] backdrop-blur">
        <div class="flex flex-col gap-4 border-b border-sky-100 bg-gradient-to-r from-sky-50 via-blue-50 to-cyan-50 px-5 py-5 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h2 class="text-lg font-bold text-slate-950">Data Pengajuan Lamaran</h2>
                <p class="mt-1 text-sm text-slate-500">Gunakan tombol Show Detail untuk melihat data lengkap pelamar.</p>
            </div>

            <form method="GET" action="{{ url()->current() }}" class="w-full lg:w-80">
                <input type="hidden" name="status" value="{{ $currentStatus }}">
                <div class="relative">
                    <span class="material-symbols-outlined pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-[20px] text-slate-400">search</span>
                    <input
                        type="search"
                        name="search"
                        value="{{ $searchQuery }}"
                        placeholder="Cari nama, email, atau posisi..."
                        class="w-full rounded-xl border border-sky-200 bg-white py-3 pl-11 pr-4 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-sky-500 focus:ring-4 focus:ring-sky-100"
                    >
                </div>
            </form>
        </div>

        <div class="flex flex-col gap-3 border-b border-sky-100 bg-white px-5 py-4 md:flex-row md:items-center md:justify-between">
            <form id="filterForm" method="GET" action="{{ url()->current() }}" class="flex flex-wrap items-center gap-2">
                <input type="hidden" name="status" id="statusInput" value="{{ $currentStatus }}">
                @if($searchQuery !== '')
                    <input type="hidden" name="search" value="{{ $searchQuery }}">
                @endif

                @foreach([
                    'all' => 'Semua',
                    'menunggu' => 'Menunggu',
                    'disetujui' => 'Disetujui',
                ] as $filterValue => $filterLabel)
                    <button
                        type="button"
                        onclick="submitFilter('{{ $filterValue }}')"
                        class="rounded-xl px-4 py-2 text-xs font-bold transition duration-200 {{ $currentStatus === $filterValue ? 'bg-gradient-to-r from-sky-500 to-blue-600 text-white shadow-[0_8px_18px_rgba(2,132,199,0.22)]' : 'border border-slate-200 bg-white text-slate-600 hover:border-sky-200 hover:bg-sky-50 hover:text-sky-700' }}"
                    >
                        {{ $filterLabel }}
                    </button>
                @endforeach
            </form>

            <p class="text-xs text-slate-500">
                Menampilkan <strong class="text-slate-700">{{ $permintaan_lamaran->firstItem() ?? 0 }}–{{ $permintaan_lamaran->lastItem() ?? 0 }}</strong>
                dari <strong class="text-slate-700">{{ $permintaan_lamaran->total() ?? count($permintaan_lamaran) }}</strong> pengajuan
            </p>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-[1180px] w-full divide-y divide-slate-200">
                <thead class="bg-gradient-to-r from-sky-50 via-blue-50 to-cyan-50">
                    <tr>
                        <th class="px-5 py-3.5 text-left text-[11px] font-bold uppercase tracking-[0.09em] text-slate-500">Nama Pelamar</th>
                        <th class="px-5 py-3.5 text-left text-[11px] font-bold uppercase tracking-[0.09em] text-slate-500">Posisi dilamar</th>
                        <th class="px-5 py-3.5 text-left text-[11px] font-bold uppercase tracking-[0.09em] text-slate-500">Email</th>
                        <th class="px-5 py-3.5 text-left text-[11px] font-bold uppercase tracking-[0.09em] text-slate-500">No. HP</th>
                        <th class="px-5 py-3.5 text-left text-[11px] font-bold uppercase tracking-[0.09em] text-slate-500">Tanggal Pengajuan</th>
                        <th class="px-5 py-3.5 text-center text-[11px] font-bold uppercase tracking-[0.09em] text-slate-500">Status</th>
                        <th class="px-5 py-3.5 text-center text-[11px] font-bold uppercase tracking-[0.09em] text-slate-500">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100 bg-white/80">
                    @forelse($permintaan_lamaran as $item)
                        @php
                            $id = $item->id_permintaan ?? $item->id ?? null;
                            $nama = $item->nama_pemohon ?? $item->nama ?? '-';
                            $posisi = $item->posisi ?? '-';
                            $email = $item->email ?? '-';
                            $noHp = $item->no_hp ?? $item->kontak ?? '-';
                            $alamat = $item->alamat ?? '-';
                            $pesan = $item->pesan ?? $item->keterangan ?? '-';
                            $cvPath = $item->cv_path ?? null;
                            $status = strtolower($item->status ?? 'menunggu');
                            $status = $status === 'diterima' ? 'disetujui' : $status;
                            $tanggal = !empty($item->created_at)
                                ? \Carbon\Carbon::parse($item->created_at)->locale('id')->translatedFormat('d M Y, H:i')
                                : '-';
                            $initial = strtoupper(mb_substr(trim($nama), 0, 2));
                            $detailPayload = [
                                'id' => $id,
                                'nama' => $nama,
                                'posisi' => $posisi,
                                'email' => $email,
                                'no_hp' => $noHp,
                                'alamat' => $alamat,
                                'pesan' => $pesan,
                                'cv_url' => $cvPath ? asset('storage/' . $cvPath) : null,
                                'status' => $status,
                                'tanggal_pengajuan' => $tanggal,
                                'action_url' => $id ? route('admin.permintaan-lamaran.action', $id) : '#',
                            ];
                        @endphp

                        <tr class="transition duration-200 hover:bg-sky-50/80">
                            <td class="whitespace-nowrap px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <span class="grid h-10 w-10 shrink-0 place-items-center rounded-2xl bg-gradient-to-br from-sky-100 to-cyan-100 text-xs font-extrabold text-sky-700 ring-1 ring-sky-200/80">
                                        {{ $initial ?: 'PL' }}
                                    </span>
                                    <div class="min-w-0">
                                        <p class="max-w-52 truncate text-sm font-bold text-slate-900">{{ $nama }}</p>
                                        <p class="mt-0.5 text-xs text-slate-500">ID Lamaran #{{ $id ?? '-' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-4">
                                <p class="max-w-56 truncate text-sm font-semibold text-slate-700" title="{{ $posisi }}">{{ $posisi }}</p>
                            </td>
                            <td class="px-5 py-4">
                                <p class="max-w-56 truncate text-sm font-medium text-slate-700" title="{{ $email }}">{{ $email }}</p>
                            </td>
                            <td class="px-5 py-4 text-sm text-slate-600">{{ $noHp }}</td>
                            <td class="whitespace-nowrap px-5 py-4 text-sm text-slate-600">{{ $tanggal }}</td>
                            <td class="whitespace-nowrap px-5 py-4 text-center">
                                @if($status === 'disetujui')
                                    <span class="inline-flex items-center gap-1.5 rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1.5 text-xs font-bold text-emerald-700">
                                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                        Disetujui
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 rounded-full border border-amber-200 bg-amber-50 px-3 py-1.5 text-xs font-bold text-amber-700">
                                        <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span>
                                        Menunggu
                                    </span>
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-5 py-4 text-center">
                                <button
                                    type="button"
                                    @click='openDetail(@json($detailPayload, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT))'
                                    class="inline-flex items-center justify-center gap-2 rounded-xl border border-sky-200 bg-sky-50 px-4 py-2 text-xs font-bold text-sky-700 transition duration-200 hover:-translate-y-0.5 hover:border-sky-300 hover:bg-sky-100"
                                >
                                    <span class="material-symbols-outlined text-[18px]">visibility</span>
                                    Show Detail
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-16 text-center">
                                <span class="mx-auto grid h-14 w-14 place-items-center rounded-2xl bg-slate-100 text-slate-400">
                                    <span class="material-symbols-outlined text-[30px]">inbox</span>
                                </span>
                                <p class="mt-4 text-sm font-bold text-slate-700">Data pengajuan lamaran tidak ditemukan</p>
                                <p class="mt-1 text-xs text-slate-500">Coba ubah filter atau kata kunci pencarian.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(method_exists($permintaan_lamaran, 'links'))
            <div class="border-t border-sky-100 bg-slate-50/60 px-5 py-4">
                {{ $permintaan_lamaran->appends(request()->query())->links() }}
            </div>
        @endif
    </section>

    <!-- Modal Detail Pengajuan Lamaran -->
    <div
        x-show="detailOpen"
        x-cloak
        x-transition.opacity
        class="fixed inset-0 z-[70] flex items-center justify-center bg-slate-950/60 p-4 backdrop-blur-sm"
        @click.self="closeDetail()"
    >
        <section
            x-show="detailOpen"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 translate-y-4 scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-y-0 scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 scale-95"
            class="max-h-[92vh] w-full max-w-4xl overflow-hidden rounded-3xl bg-white shadow-[0_30px_80px_rgba(15,23,42,0.30)]"
        >
            <header class="flex items-start justify-between gap-4 border-b border-sky-100 bg-gradient-to-r from-sky-50 via-blue-50 to-cyan-50 px-6 py-5">
                <div>
                    <span class="inline-flex items-center gap-2 rounded-full bg-white px-3 py-1 text-[10px] font-bold uppercase tracking-[0.14em] text-sky-700 ring-1 ring-sky-200">
                        Detail Lamaran Kerja
                    </span>
                    <h3 class="mt-3 text-xl font-extrabold text-slate-950" x-text="detail.nama || '-'">-</h3>
                    <p class="mt-1 text-sm text-slate-500">Informasi lengkap calon pelamar kerja.</p>
                </div>
                <button
                    type="button"
                    @click="closeDetail()"
                    class="grid h-10 w-10 shrink-0 place-items-center rounded-xl border border-slate-200 bg-white text-slate-500 transition hover:border-rose-200 hover:bg-rose-50 hover:text-rose-600"
                    aria-label="Tutup detail"
                >
                    <span class="material-symbols-outlined">close</span>
                </button>
            </header>

            <div class="max-h-[calc(92vh-168px)] overflow-y-auto px-6 py-6">
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    <div class="rounded-2xl border border-slate-200 bg-slate-50/70 p-4">
                        <p class="text-[10px] font-bold uppercase tracking-[0.12em] text-slate-400">Nama Lengkap</p>
                        <p class="mt-2 break-words text-sm font-bold text-slate-900" x-text="detail.nama || '-'">-</p>
                    </div>
                    <div class="rounded-2xl border border-slate-200 bg-slate-50/70 p-4">
                        <p class="text-[10px] font-bold uppercase tracking-[0.12em] text-slate-400">Posisi Dilamar</p>
                        <p class="mt-2 break-words text-sm font-bold text-sky-700" x-text="detail.posisi || '-'">-</p>
                    </div>
                    <div class="rounded-2xl border border-slate-200 bg-slate-50/70 p-4">
                        <p class="text-[10px] font-bold uppercase tracking-[0.12em] text-slate-400">Email</p>
                        <p class="mt-2 break-all text-sm font-semibold text-slate-700" x-text="detail.email || '-'">-</p>
                    </div>
                    <div class="rounded-2xl border border-slate-200 bg-slate-50/70 p-4">
                        <p class="text-[10px] font-bold uppercase tracking-[0.12em] text-slate-400">Nomor HP</p>
                        <p class="mt-2 break-words text-sm font-semibold text-slate-700" x-text="detail.no_hp || '-'">-</p>
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
                        <p class="text-[10px] font-bold uppercase tracking-[0.12em] text-slate-400">Alamat</p>
                        <p class="mt-2 whitespace-pre-line break-words text-sm font-semibold leading-6 text-slate-700" x-text="detail.alamat || '-'">-</p>
                    </div>
                    <template x-if="detail.cv_url">
                        <div class="rounded-2xl border border-sky-200 bg-sky-50/60 p-4 sm:col-span-2 lg:col-span-3 flex items-center justify-between">
                            <div>
                                <p class="text-[10px] font-bold uppercase tracking-[0.12em] text-sky-600">Dokumen Berkas / CV</p>
                                <p class="mt-1 text-xs text-slate-600">Klik tombol di samping untuk mengunduh atau meninjau berkas CV pelamar.</p>
                            </div>
                            <a :href="detail.cv_url" target="_blank" class="inline-flex items-center gap-2 rounded-xl bg-sky-600 px-4 py-2 text-xs font-bold text-white transition hover:bg-sky-700">
                                <span class="material-symbols-outlined text-[18px]">download</span>
                                Buka Berkas CV
                            </a>
                        </div>
                    </template>
                </div>
            </div>

            <footer class="flex flex-col gap-3 border-t border-sky-100 bg-slate-50 px-6 py-4 sm:flex-row sm:items-center sm:justify-between">
                <button
                    type="button"
                    @click="closeDetail()"
                    class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-bold text-slate-600 transition hover:bg-slate-100"
                >
                    Tutup
                </button>

                <template x-if="detail.status === 'menunggu'">
                    <div class="flex flex-col gap-2 sm:flex-row">
                        <form :action="detail.action_url" method="POST" onsubmit="return confirm('Setujui pengajuan lamaran ini?')">
                            @csrf
                            <input type="hidden" name="action" value="approve">
                            <button
                                type="submit"
                                class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-600 px-5 py-2.5 text-sm font-bold text-white shadow-[0_10px_22px_rgba(5,150,105,0.24)] transition hover:-translate-y-0.5 hover:from-emerald-600 hover:to-teal-700"
                            >
                                <span class="material-symbols-outlined text-[19px]">check_circle</span>
                                Disetujui
                            </button>
                        </form>

                        <form :action="detail.action_url" method="POST" onsubmit="return confirm('Tolak pengajuan ini? Data akan dihapus permanen dan tidak dapat dikembalikan.')">
                            @csrf
                            <input type="hidden" name="action" value="reject">
                            <button
                                type="submit"
                                class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-rose-500 to-red-600 px-5 py-2.5 text-sm font-bold text-white shadow-[0_10px_22px_rgba(225,29,72,0.22)] transition hover:-translate-y-0.5 hover:from-rose-600 hover:to-red-700"
                            >
                                <span class="material-symbols-outlined text-[19px]">cancel</span>
                                Ditolak
                            </button>
                        </form>
                    </div>
                </template>

                <template x-if="detail.status !== 'menunggu'">
                    <span class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-500">
                        <span class="material-symbols-outlined text-[19px]">task_alt</span>
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