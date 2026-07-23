@extends('layouts.portal')

@section('title', 'Permintaan Lamaran')

@section('content')
    @php
        $statusTabs = [
            'semua' => 'Semua Lamaran',
            'menunggu' => 'Baru Masuk',
            'interview' => 'Interview',
            'disetujui' => 'Diterima',
        ];

        $statusBadgeClasses = [
            'menunggu' => 'bg-amber-100 text-amber-700 ring-amber-200',
            'interview' => 'bg-indigo-100 text-indigo-700 ring-indigo-200',
            'disetujui' => 'bg-emerald-100 text-emerald-700 ring-emerald-200',
            'ditolak' => 'bg-rose-100 text-rose-700 ring-rose-200',
        ];

        $activeStatus = request('status', 'semua');
        $keyword = request('search');
    @endphp

    <section class="mt-5 flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 class="text-2xl font-extrabold tracking-tight text-slate-950 sm:text-3xl">Permintaan Lamaran</h1>
            <p class="mt-1 max-w-2xl text-sm leading-6 text-slate-500">
                Kelola dan tinjau berkas lamaran calon karyawan, mulai dari verifikasi, interview, hingga keputusan akhir.
            </p>
        </div>
    </section>

    @if (session('success'))
        <div class="mt-4 rounded-2xl bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700 ring-1 ring-emerald-100">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mt-4 rounded-2xl bg-rose-50 px-4 py-3 text-sm font-semibold text-rose-700 ring-1 ring-rose-100">
            {{ session('error') }}
        </div>
    @endif

    {{-- ================= STAT CARDS ================= --}}
    <section class="mt-5 grid gap-4 sm:grid-cols-3">
        <div class="rounded-2xl border border-white/80 bg-white/90 p-5 shadow-[0_16px_36px_rgba(15,52,94,0.06)]">
            <p class="text-[11px] font-bold uppercase tracking-[0.15em] text-slate-400">Total Pendaftar</p>
            <p class="mt-2 text-3xl font-extrabold text-slate-950">{{ number_format($total_pendaftar) }}</p>
        </div>
        <div class="rounded-2xl border border-white/80 bg-white/90 p-5 shadow-[0_16px_36px_rgba(15,52,94,0.06)]">
            <p class="text-[11px] font-bold uppercase tracking-[0.15em] text-slate-400">Sedang Interview</p>
            <p class="mt-2 text-3xl font-extrabold text-indigo-600">{{ number_format($total_interview) }}</p>
        </div>
        <div class="rounded-2xl border border-white/80 bg-white/90 p-5 shadow-[0_16px_36px_rgba(15,52,94,0.06)]">
            <p class="text-[11px] font-bold uppercase tracking-[0.15em] text-slate-400">Sudah Disetujui</p>
            <p class="mt-2 text-3xl font-extrabold text-emerald-600">{{ number_format($total_disetujui) }}</p>
        </div>
    </section>

    {{-- ================= FILTER & SEARCH ================= --}}
    <section class="mt-5 rounded-2xl border border-white/80 bg-white/90 p-4 shadow-[0_16px_36px_rgba(15,52,94,0.06)]">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div class="flex flex-wrap gap-2">
                @foreach ($statusTabs as $value => $label)
                    <a
                        href="{{ request()->fullUrlWithQuery(['status' => $value, 'page' => null]) }}"
                        class="rounded-xl px-4 py-2 text-xs font-bold transition {{ $activeStatus === $value ? 'bg-sky-600 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}"
                    >
                        {{ $label }}
                    </a>
                @endforeach
            </div>

            <form method="GET" action="{{ route('admin.permintaan-lamaran.index') }}" class="flex items-center gap-2">
                <input type="hidden" name="status" value="{{ $activeStatus }}">
                <div class="relative">
                    <input
                        type="text"
                        name="search"
                        value="{{ $keyword }}"
                        placeholder="Cari nama, email, posisi..."
                        class="w-56 rounded-xl border border-slate-200 py-2 pl-9 pr-3 text-xs focus:border-sky-500 focus:ring-sky-500"
                    >
                    <svg class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" viewBox="0 0 24 24" fill="none"><circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="1.8"/><path d="m20 20-3-3" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                </div>
                <button type="submit" class="rounded-xl bg-slate-900 px-4 py-2 text-xs font-bold text-white transition hover:bg-slate-800">Cari</button>
            </form>
        </div>
    </section>

    {{-- ================= TABLE ================= --}}
    <section class="mt-5 overflow-hidden rounded-3xl border border-white/80 bg-white/90 shadow-[0_18px_45px_rgba(15,52,94,0.08)]">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b border-slate-200 bg-slate-50 text-[11px] font-bold uppercase tracking-wider text-slate-500">
                        <th class="px-6 py-4">Kandidat</th>
                        <th class="px-6 py-4">Posisi</th>
                        <th class="px-6 py-4">Tanggal Lamar</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($permintaan_lamaran as $lamaran)
                        @php
                            $initials = collect(preg_split('/\s+/', trim($lamaran->nama_pemohon)))
                                ->filter()
                                ->take(2)
                                ->map(fn ($word) => mb_strtoupper(mb_substr($word, 0, 1)))
                                ->implode('');
                        @endphp
                        <tr class="transition hover:bg-sky-50/40">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <span class="grid h-10 w-10 shrink-0 place-items-center rounded-full bg-sky-100 text-xs font-extrabold text-sky-700">
                                        {{ $initials ?: 'KY' }}
                                    </span>
                                    <div class="min-w-0">
                                        <p class="truncate text-sm font-bold text-slate-900">{{ $lamaran->nama_pemohon }}</p>
                                        <p class="truncate text-[11px] text-slate-500">{{ $lamaran->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-700">{{ $lamaran->posisi ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm text-slate-500">{{ \Illuminate\Support\Carbon::parse($lamaran->created_at)->format('d M Y') }}</td>
                            <td class="px-6 py-4">
                                <span class="rounded-full px-3 py-1 text-[10px] font-extrabold uppercase tracking-wide ring-1 {{ $statusBadgeClasses[$lamaran->status] ?? 'bg-slate-100 text-slate-600 ring-slate-200' }}">
                                    {{ $statusTabs[$lamaran->status] ?? ucfirst($lamaran->status) }}
                                </span>
                                @if ($lamaran->status === 'interview' && $lamaran->jadwal_interview)
                                    <p class="mt-1 text-[10px] text-slate-400">
                                        {{ \Illuminate\Support\Carbon::parse($lamaran->jadwal_interview)->translatedFormat('d M Y, H:i') }}
                                        @if ($lamaran->lokasi_interview)
                                            &bull; {{ $lamaran->lokasi_interview }}
                                        @endif
                                    </p>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap items-center justify-center gap-2">
                                    @if ($lamaran->status === 'menunggu')
                                        <button
                                            type="button"
                                            onclick="document.getElementById('interview-modal-{{ $lamaran->id_permintaan }}').classList.remove('hidden')"
                                            class="rounded-lg border border-indigo-200 px-3 py-1.5 text-[11px] font-bold text-indigo-700 transition hover:bg-indigo-600 hover:text-white"
                                        >
                                            Jadwalkan Interview
                                        </button>
                                    @endif

                                    @if (in_array($lamaran->status, ['menunggu', 'interview'], true))
                                        <form method="POST" action="{{ route('admin.permintaan-lamaran.action', $lamaran->id_permintaan) }}">
                                            @csrf
                                            <input type="hidden" name="action" value="approve">
                                            <button type="submit" class="rounded-lg border border-emerald-200 px-3 py-1.5 text-[11px] font-bold text-emerald-700 transition hover:bg-emerald-600 hover:text-white">
                                                Terima
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.permintaan-lamaran.action', $lamaran->id_permintaan) }}">
                                            @csrf
                                            <input type="hidden" name="action" value="reject">
                                            <button type="submit" class="rounded-lg border border-rose-200 px-3 py-1.5 text-[11px] font-bold text-rose-700 transition hover:bg-rose-600 hover:text-white">
                                                Tolak
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>

                        {{-- Modal jadwalkan interview --}}
                        <tr id="interview-modal-{{ $lamaran->id_permintaan }}" class="hidden">
                            <td colspan="5" class="bg-indigo-50/60 px-6 py-5">
                                <form method="POST" action="{{ route('admin.permintaan-lamaran.action', $lamaran->id_permintaan) }}" class="flex flex-wrap items-end gap-3">
                                    @csrf
                                    <input type="hidden" name="action" value="interview">

                                    <div>
                                        <label class="text-[10px] font-bold uppercase tracking-wide text-slate-500">Tanggal &amp; Jam Interview</label>
                                        <input type="datetime-local" name="jadwal_interview" required class="mt-1 rounded-xl border border-slate-200 px-3 py-2 text-xs focus:border-indigo-500 focus:ring-indigo-500">
                                    </div>

                                    <div class="flex-1">
                                        <label class="text-[10px] font-bold uppercase tracking-wide text-slate-500">Lokasi Interview</label>
                                        <input type="text" name="lokasi_interview" required placeholder="Kantor CV Natusi, Lt. 2" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-xs focus:border-indigo-500 focus:ring-indigo-500">
                                    </div>

                                    <button type="submit" class="rounded-xl bg-indigo-600 px-4 py-2 text-xs font-bold text-white transition hover:bg-indigo-700">
                                        Kirim Undangan
                                    </button>
                                    <button
                                        type="button"
                                        onclick="document.getElementById('interview-modal-{{ $lamaran->id_permintaan }}').classList.add('hidden')"
                                        class="rounded-xl border border-slate-300 px-4 py-2 text-xs font-bold text-slate-600 transition hover:bg-slate-100"
                                    >
                                        Batal
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-14 text-center">
                                <p class="font-bold text-slate-800">Belum ada lamaran yang cocok</p>
                                <p class="mt-1 text-sm text-slate-500">Coba ubah filter status atau kata kunci pencarian.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($permintaan_lamaran->hasPages())
            <div class="border-t border-slate-100 px-6 py-4">
                {{ $permintaan_lamaran->onEachSide(1)->links() }}
            </div>
        @endif
    </section>
@endsection