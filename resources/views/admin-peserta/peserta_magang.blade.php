@extends('layouts.portal')

@section('title', 'Peserta Magang')

@section('content')
<div class="mt-5">
    <div class="mb-5">
        <h1 class="text-2xl font-extrabold text-slate-950">Peserta Magang</h1>
        <p class="text-sm text-slate-500">Daftar peserta yang sudah diterima. Setiap anggota kelompok tampil sebagai peserta tersendiri.</p>
    </div>

    <div class="overflow-hidden rounded-3xl border bg-white shadow">
        <div class="border-b px-6 py-4">
            Menampilkan <strong>{{ $peserta->firstItem() ?? 0 }}-{{ $peserta->lastItem() ?? 0 }}</strong>
            dari <strong>{{ $peserta->total() }}</strong> peserta
    <section class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 class="mt-5 text-2xl font-extrabold tracking-tight text-slate-950 sm:text-3xl">Data Peserta Magang</h1>
            <p class="mt-1 text-sm text-slate-500">Kelola dan tinjau data peserta magang di CV Natusi.</p>
        </div>
    </section>

    @php
        $requestedStatus = request('status', 'all');
        $currentStatus = in_array($requestedStatus, ['menunggu', 'perlu_revisi', 'disetujui', 'ditolak'], true) ? $requestedStatus : 'all';
    @endphp

    <section class="mt-5 overflow-hidden rounded-3xl border border-sky-100/90 bg-white/95 shadow-[0_20px_50px_rgba(15,52,94,0.09)] backdrop-blur">
        <div class="flex flex-col gap-4 border-b border-sky-100 bg-gradient-to-r from-sky-50 via-blue-50 to-cyan-50 px-6 py-5 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h2 class="text-lg font-bold text-slate-950">Data Peserta Magang</h2>
                <p class="mt-0.5 text-sm text-slate-500">Gunakan tombol Show Detail untuk melihat data lengkap pendaftar.</p>
            </div>
        </div>

        <div class="flex flex-col gap-3 border-b border-sky-100 bg-white px-6 py-4 md:flex-row md:items-center md:justify-between">
            <form id="filterForm" method="GET" action="{{ url()->current() }}" class="flex flex-wrap items-center gap-2">
                <input type="hidden" name="status" id="statusInput" value="{{ $currentStatus }}">

                @foreach(['all' => 'Semua', 'menunggu' => 'Menunggu', 'perlu_revisi' => 'Perlu Revisi', 'disetujui' => 'Disetujui', 'ditolak' => 'Ditolak'] as $filterValue => $filterLabel)
                    <button type="button" onclick="submitFilter('{{ $filterValue }}')"
                        class="rounded-xl px-4 py-2 text-xs font-bold transition duration-200 {{ $currentStatus === $filterValue ? 'bg-gradient-to-r from-sky-500 to-blue-600 text-white shadow-[0_8px_18px_rgba(2,132,199,0.22)]' : 'border border-slate-200 bg-white text-slate-600 hover:border-sky-200 hover:bg-sky-50 hover:text-sky-700' }}">
                        {{ $filterLabel }}
                    </button>
                @endforeach
            </form>

            <p class="text-xs text-slate-500">
                Menampilkan <strong class="text-slate-700">{{ $peserta->firstItem() ?? 0 }}–{{ $peserta->lastItem() ?? 0 }}</strong>
                dari <strong class="text-slate-700">{{ $peserta->total() }}</strong> peserta
            </p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-5 py-3">Nama</th>
                        <th class="px-5 py-3">Email</th>
                        <th class="px-5 py-3">Kelompok</th>
                        <th class="px-5 py-3">Status</th>
                        <th class="px-5 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($peserta as $item)
                    <tr class="border-t">
                        <td class="px-5 py-3">{{ $item->user?->nama ?? '-' }}</td>
                        <td class="px-5 py-3">{{ $item->user?->email ?? '-' }}</td>
                        <td class="px-5 py-3">{{ $item->permintaan?->nama_sekolah ?? '-' }}</td>
                        <td class="px-5 py-3">
                            {{ ucfirst($item->status) }}
                        </td>
                        <td class="px-5 py-3">
                            <a href="{{ route('admin-peserta.peserta.show',$item->id_peserta) }}" class="text-blue-600 font-bold">Detail</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-5 py-5 text-center">Belum ada peserta diterima.</td></tr>
                @endforelse
                <tbody class="divide-y divide-slate-100 bg-white/80">
                    @forelse ($peserta as $item)
                        @php
                            $id = $item->id_peserta ?? $item->id_permintaan ?? $item->id ?? null;
                            $nama = $item->user?->nama ?? $item->nama_pemohon ?? $item->nama ?? '-';
                            $alamat = $item->alamat ?? '-';
                            $email = $item->user?->email ?? $item->email ?? '-';
                            $instansi = $item->permintaan?->nama_sekolah ?? $item->user?->university ?? $item->nama_sekolah ?? '-';
                            $noInduk = $item->permintaan?->no_induk ?? $item->user?->student_id ?? $item->no_induk ?? '-';
                            $jurusan = $item->jurusan?->nama_jurusan ?? $item->permintaan?->jurusan ?? $item->user?->major ?? $item->jurusan ?? '-';
                            $noHp = $item->user?->phone ?? $item->no_hp ?? '-';
                            $pesan = $item->permintaan?->pesan ?? $item->pesan ?? '-';
                            $status = strtolower($item->status ?? 'menunggu');
                            $status = $status === 'diterima' ? 'disetujui' : $status;
                            $tanggal = !empty($item->created_at) ? \Carbon\Carbon::parse($item->created_at)->locale('id')->translatedFormat('d M Y, H:i') : '-';
                            $initial = strtoupper(mb_substr(trim($nama), 0, 2));
                            $anggotaSource = $item->permintaan?->anggota ?? collect();
                            $anggotaPayload = $anggotaSource->map(fn ($anggota) => [
                                'nama' => $anggota->nama,
                                'email' => $anggota->email,
                                'no_induk' => $anggota->no_induk,
                                'jurusan' => $anggota->jurusan,
                                'no_hp' => $anggota->no_hp,
                                'is_ketua' => (bool) $anggota->is_ketua,
                                'cv_url' => ($anggota->cv_path ?: ($anggota->is_ketua ? ($item->cv_path ?? $item->permintaan?->cv_path) : null))
                                    ? route('pengajuan.berkas.lihat', ['permintaan' => $item->permintaan_id ?? $item->id_permintaan ?? $id, 'jenis' => 'cv', 'ref' => $anggota->id_anggota ?: 'ketua'])
                                    : null,
                                'surat_url' => ($anggota->surat_pengajuan_path ?: ($anggota->is_ketua ? ($item->surat_pengajuan_path ?? $item->permintaan?->surat_pengajuan_path) : null))
                                    ? route('pengajuan.berkas.lihat', ['permintaan' => $item->permintaan_id ?? $item->id_permintaan ?? $id, 'jenis' => 'surat', 'ref' => $anggota->id_anggota ?: 'ketua'])
                                    : null,
                                'username_peserta' => $anggota->username_peserta,
                            ])->values()->all();
                            if (empty($anggotaPayload)) {
                                $anggotaPayload[] = [
                                    'nama' => $nama,
                                    'email' => $email,
                                    'no_induk' => $noInduk,
                                    'jurusan' => $jurusan,
                                    'no_hp' => $noHp,
                                    'is_ketua' => true,
                                    'cv_url' => ($item->cv_path ?? $item->permintaan?->cv_path) ? route('pengajuan.berkas.lihat', ['permintaan' => $item->permintaan_id ?? $item->id_permintaan ?? $id, 'jenis' => 'cv', 'ref' => 'ketua']) : null,
                                    'surat_url' => ($item->surat_pengajuan_path ?? $item->permintaan?->surat_pengajuan_path) ? route('pengajuan.berkas.lihat', ['permintaan' => $item->permintaan_id ?? $item->id_permintaan ?? $id, 'jenis' => 'surat', 'ref' => 'ketua']) : null,
                                    'username_peserta' => $item->user?->username ?? $item->username_peserta ?? null,
                                ];
                            }
                            $revisiSource = $item->permintaan?->riwayatBerkas ?? collect();
                            $revisiPayload = $revisiSource->map(fn ($berkas) => [
                                'jenis' => $berkas->jenis_berkas,
                                'versi' => (int) $berkas->versi,
                                'url' => route('pengajuan.berkas.lihat', ['permintaan' => $item->permintaan_id ?? $item->id_permintaan ?? $id, 'jenis' => 'revisi', 'ref' => $berkas->getKey()]),
                                'tanggal' => $berkas->created_at?->locale('id')->translatedFormat('d M Y, H:i'),
                            ])->values()->all();
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
                                'jenjang' => $item->jenjang === 'smk' ? 'SMK' : ($item->jenjang === 'kuliah' ? 'Universitas' : '-'),
                                'tipe_pengajuan' => ucfirst($item->tipe_pengajuan ?? 'individu'),
                                'jumlah_anggota' => (int) ($item->jumlah_anggota ?: count($anggotaPayload)),
                                'anggota' => $anggotaPayload,
                                'cv_url' => $item->cv_path ? route('pengajuan.berkas.lihat', ['permintaan' => $item, 'jenis' => 'cv', 'ref' => 'ketua']) : null,
                                'surat_url' => $item->surat_pengajuan_path ? route('pengajuan.berkas.lihat', ['permintaan' => $item, 'jenis' => 'surat', 'ref' => 'ketua']) : null,
                                'revisi' => $revisiPayload,
                                'status' => $status,
                                'tanggal_pengajuan' => $tanggal,
                                'action_url' => $id ? route('admin-peserta.permintaan.action', $id) : '#',
                            ];
                        @endphp

                        <tr class="transition hover:bg-sky-50/40">
                            <td class="whitespace-nowrap px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <span class="grid h-10 w-10 shrink-0 place-items-center rounded-2xl bg-gradient-to-br from-sky-100 to-cyan-100 text-xs font-extrabold text-sky-700 ring-1 ring-sky-200/80">
                                        {{ $initial ?: 'PM' }}
                                    </span>
                                    <div class="min-w-0">
                                        <p class="max-w-52 truncate text-sm font-bold text-slate-900">{{ $nama }}</p>
                                        <p class="mt-0.5 text-xs text-slate-500">ID #{{ $id ?? '-' }} · {{ (int) ($item->jumlah_anggota ?: 1) }} peserta</p>
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
                                    <span class="inline-flex items-center gap-1.5 rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1.5 text-xs font-bold text-emerald-700 ring-1 ring-emerald-100/50">
                                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span> Disetujui
                                    </span>
                                @elseif($status === 'perlu_revisi')
                                    <span class="inline-flex items-center gap-1.5 rounded-full border border-amber-200 bg-amber-50 px-3 py-1.5 text-xs font-bold text-amber-700 ring-1 ring-amber-100/50">Perlu Revisi</span>
                                @elseif($status === 'ditolak')
                                    <span class="inline-flex items-center gap-1.5 rounded-full border border-rose-200 bg-rose-50 px-3 py-1.5 text-xs font-bold text-rose-700 ring-1 ring-rose-100/50">Ditolak</span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 rounded-full border border-amber-200 bg-amber-50 px-3 py-1.5 text-xs font-bold text-amber-700 ring-1 ring-amber-100/50">
                                        <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span> Menunggu
                                    </span>
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-5 py-4 text-center">
                                <button type="button" @click="openDetail(@js($detailPayload))"
                                    class="inline-flex items-center justify-center gap-2 rounded-xl border border-sky-200 bg-sky-50 px-4 py-2 text-xs font-bold text-sky-700 transition hover:-translate-y-0.5 hover:border-sky-300 hover:bg-sky-100">
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

        <div class="px-6 py-4">
            {{ $peserta->links() }}
        </div>
        @if ($peserta->hasPages())
            <div class="border-t border-sky-100 bg-sky-50/50 px-6 py-4">
                {{ $peserta->appends(request()->query())->links() }}
            </div>
        @endif
    </section>

    <template x-teleport="body">
    <div x-show="detailOpen" x-cloak x-transition.opacity class="fixed inset-0 z-[9999] flex items-center justify-center overflow-y-auto bg-slate-950/65 p-4" @click.self="closeDetail()">
        <section x-show="detailOpen" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-4 scale-95" x-transition:enter-end="opacity-100 translate-y-0 scale-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0 scale-100" x-transition:leave-end="opacity-0 translate-y-4 scale-95" class="flex w-full max-w-4xl flex-col overflow-hidden rounded-3xl bg-white shadow-[0_30px_80px_rgba(15,23,42,0.30)]" style="width:min(900px,calc(100vw - 32px));max-height:90vh">
            <header class="flex items-start justify-between gap-4 border-b border-sky-100 bg-gradient-to-r from-sky-50 via-blue-50 to-cyan-50 px-6 py-5">
                <div class="min-w-0 flex-1">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="inline-flex items-center gap-2 rounded-full bg-white px-3 py-1 text-[10px] font-bold uppercase tracking-[0.14em] text-sky-700 ring-1 ring-sky-200">
                            Detail Pengajuan
                        </span>
                        <span class="inline-flex items-center gap-1.5 rounded-full border px-3 py-1 text-[10px] font-bold uppercase tracking-[0.1em]"
                              :class="statusBadgeClass(detail.status)"
                              x-text="statusLabel(detail.status)">
                        </span>
                    </div>
                    <h3 class="mt-3 text-xl font-extrabold text-slate-950" x-text="detail.nama || '-'">-</h3>
                    <p class="mt-1 text-sm text-slate-500">Informasi lengkap calon peserta magang.</p>
                </div>
                <button type="button" @click="closeDetail()" class="grid h-10 w-10 shrink-0 place-items-center rounded-xl border border-slate-200 bg-white text-slate-500 transition hover:border-rose-200 hover:bg-rose-50 hover:text-rose-600" aria-label="Tutup detail">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </header>

            <div class="min-h-0 flex-1 overflow-y-auto overscroll-contain px-6 py-6">
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
                    <div class="rounded-2xl border border-slate-200 bg-slate-50/70 p-4 sm:col-span-2 lg:col-span-3">
                        <p class="text-[10px] font-bold uppercase tracking-[0.12em] text-slate-400">Pesan / Keterangan</p>
                        <p class="mt-2 whitespace-pre-line break-words text-sm font-medium leading-6 text-slate-700" x-text="detail.pesan || '-'">-</p>
                    </div>
                </div>

                <div class="mt-5 grid gap-4 sm:grid-cols-3">
                    <div class="rounded-2xl border border-sky-200 bg-sky-50 p-4">
                        <p class="text-[10px] font-bold uppercase tracking-[0.12em] text-sky-500">Jenjang</p>
                        <p class="mt-2 text-sm font-bold text-sky-900" x-text="detail.jenjang || '-'">-</p>
                    </div>
                    <div class="rounded-2xl border border-sky-200 bg-sky-50 p-4">
                        <p class="text-[10px] font-bold uppercase tracking-[0.12em] text-sky-500">Jenis Pengajuan</p>
                        <p class="mt-2 text-sm font-bold text-sky-900" x-text="detail.tipe_pengajuan || '-'">-</p>
                    </div>
                    <div class="rounded-2xl border border-sky-200 bg-sky-50 p-4">
                        <p class="text-[10px] font-bold uppercase tracking-[0.12em] text-sky-500">Jumlah Peserta</p>
                        <p class="mt-2 text-sm font-bold text-sky-900"><span x-text="detail.jumlah_anggota || 1"></span> orang</p>
                    </div>
                </div>

                <div class="mt-5 rounded-2xl border border-slate-200 bg-white p-4">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div>
                            <p class="text-[10px] font-bold uppercase tracking-[0.12em] text-slate-400">Berkas Pengajuan</p>
                            <p class="mt-1 text-sm font-semibold text-slate-700">CV dan surat resmi sekolah/kampus</p>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            <a x-show="detail.cv_url" :href="detail.cv_url" target="_blank" class="rounded-xl bg-sky-700 px-4 py-2 text-xs font-bold text-white hover:bg-sky-800">Lihat CV</a>
                            <a x-show="detail.surat_url" :href="detail.surat_url" target="_blank" class="rounded-xl bg-indigo-700 px-4 py-2 text-xs font-bold text-white hover:bg-indigo-800">Lihat Surat Pengajuan</a>
                            <span x-show="!detail.cv_url && !detail.surat_url" class="text-xs font-semibold text-slate-400">Berkas belum tersedia (data lama)</span>
                        </div>
                    </div>
                </div>

                <div x-show="(detail.revisi || []).length > 0" class="mt-5 rounded-2xl border border-amber-200 bg-amber-50/60 p-4">
                    <p class="text-[10px] font-bold uppercase tracking-[0.12em] text-amber-600">Berkas Revisi</p>
                    <p class="mt-1 text-sm font-semibold text-amber-900">Berkas tambahan yang dikirim pemohon setelah permintaan revisi.</p>
                    <div class="mt-3 flex flex-wrap gap-2">
                        <template x-for="(berkas, index) in (detail.revisi || [])" :key="index">
                            <a :href="berkas.url" target="_blank" class="rounded-xl border border-amber-200 bg-white px-3 py-2 text-xs font-bold text-amber-800 hover:bg-amber-100">
                                <span x-text="(berkas.jenis || 'Berkas revisi') + ' · v' + (berkas.versi || 1)"></span>
                                <span class="ml-1 font-medium text-amber-600" x-text="berkas.tanggal ? '— ' + berkas.tanggal : ''"></span>
                            </a>
                        </template>
                    </div>
                </div>

                <div class="mt-5 rounded-2xl border border-slate-200 bg-white p-4">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <p class="text-[10px] font-bold uppercase tracking-[0.12em] text-slate-400">Anggota Pengajuan</p>
                            <p class="mt-1 text-sm font-semibold text-slate-700">Setiap anggota memiliki data, CV, dan surat pengantar masing-masing. Akun peserta dibuat sebanyak anggota saat pengajuan disetujui.</p>
                        </div>
                    </div>
                    <div class="mt-3 grid gap-3 md:grid-cols-2">
                        <template x-for="(anggota, index) in (detail.anggota || [])" :key="index">
                            <div class="rounded-xl border border-slate-200 bg-slate-50 p-3">
                                <div class="flex items-center justify-between gap-2">
                                    <p class="text-sm font-bold text-slate-900" x-text="anggota.nama || '-'">-</p>
                                    <span x-show="anggota.is_ketua" class="rounded-full bg-sky-100 px-2 py-1 text-[10px] font-bold uppercase tracking-wide text-sky-700">Ketua</span>
                                </div>
                                <div class="mt-2 space-y-1 text-xs text-slate-600">
                                    <p><strong>Email:</strong> <span x-text="anggota.email || '-'">-</span></p>
                                    <p><strong>NIS/NIM:</strong> <span x-text="anggota.no_induk || '-'">-</span></p>
                                    <p><strong>Jurusan:</strong> <span x-text="anggota.jurusan || '-'">-</span></p>
                                    <p><strong>WhatsApp:</strong> <span x-text="anggota.no_hp || '-'">-</span></p>
                                    <p x-show="anggota.username_peserta"><strong>Username:</strong> <span class="font-mono" x-text="anggota.username_peserta"></span></p>
                                </div>
                                <div class="mt-3 flex flex-wrap gap-2 border-t border-slate-200 pt-3">
                                    <a x-show="anggota.cv_url" :href="anggota.cv_url" target="_blank" class="rounded-lg bg-sky-700 px-3 py-2 text-[11px] font-bold text-white hover:bg-sky-800">Lihat CV</a>
                                    <a x-show="anggota.surat_url" :href="anggota.surat_url" target="_blank" class="rounded-lg bg-indigo-700 px-3 py-2 text-[11px] font-bold text-white hover:bg-indigo-800">Lihat Surat Pengantar</a>
                                    <span x-show="!anggota.cv_url" class="rounded-lg bg-slate-200 px-3 py-2 text-[11px] font-semibold text-slate-500">CV belum tersedia</span>
                                    <span x-show="!anggota.surat_url" class="rounded-lg bg-slate-200 px-3 py-2 text-[11px] font-semibold text-slate-500">Surat belum tersedia</span>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <footer class="flex flex-col gap-3 border-t border-sky-100 bg-slate-50 px-6 py-4 sm:flex-row sm:items-center sm:justify-end">
                <template x-if="detail.status === 'menunggu'">
                    <div class="flex flex-col gap-2 sm:flex-row">
                        <form :action="detail.action_url" method="POST" onsubmit="return confirm('Setujui pengajuan magang ini?')">
                            @csrf
                            <input type="hidden" name="action" value="approve">
                            <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-emerald-600 px-5 py-2.5 text-sm font-bold text-white transition hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-200">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Setujui
                            </button>
                        </form>

                        <form :action="detail.action_url" method="POST" onsubmit="const note = prompt('Tuliskan berkas/data yang harus direvisi (wajib):'); if (!note || !note.trim()) return false; this.querySelector('[name=catatan_revisi]').value = note.trim(); return true;">
                            @csrf
                            <input type="hidden" name="action" value="revision">
                            <input type="hidden" name="catatan_revisi" value="">
                            <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-amber-500 px-5 py-2.5 text-sm font-bold text-white transition hover:bg-amber-600 focus:outline-none focus:ring-2 focus:ring-amber-200">
                                Minta Revisi
                            </button>
                        </form>

                        <form :action="detail.action_url" method="POST" onsubmit="const reason = prompt('Masukkan alasan penolakan (wajib):'); if (!reason || !reason.trim()) return false; this.querySelector('[name=alasan_penolakan]').value = reason.trim(); return true;">
                            @csrf
                            <input type="hidden" name="action" value="reject">
                            <input type="hidden" name="alasan_penolakan" value="">
                            <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-rose-600 px-5 py-2.5 text-sm font-bold text-white transition hover:bg-rose-700 focus:outline-none focus:ring-2 focus:ring-rose-200">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                Tolak
                            </button>
                        </form>

                    </div>
                </template>

                <template x-if="detail.status !== 'menunggu'">
                    <span class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-500">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Sudah diproses
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
