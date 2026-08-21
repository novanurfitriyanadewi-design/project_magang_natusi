@extends('layouts.portal')

@section('title', 'Permintaan Lamaran')

@section('content')
    @php
        $statusTabs = [
            'semua' => 'Semua Lamaran',
            'menunggu' => 'Baru Masuk',
            'interview' => 'Interview',
            'disetujui' => 'Diterima',
            'ditolak' => 'Ditolak',
        ];

        $statusBadgeClasses = [
            'menunggu' => 'bg-amber-100 text-amber-700 ring-amber-200',
            'interview' => 'bg-indigo-100 text-indigo-700 ring-indigo-200',
            'disetujui' => 'bg-emerald-100 text-emerald-700 ring-emerald-200',
            'ditolak' => 'bg-rose-100 text-rose-700 ring-rose-200',
        ];

        $activeStatus = request('status', 'semua');
        $keyword = request('search');

        $daftarBerkas = [
    'surat_lamaran_path' => 'Surat Lamaran Kerja',
    'cv_path'            => 'CV (Curriculum Vitae)',
    'ijazah_path'        => 'Ijazah & Transkrip Nilai',
    'ktp_path'           => 'Fotokopi KTP',
];
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
<section class="mt-5 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-blue-600 to-blue-700 p-5 text-white shadow-lg">
        <div class="flex items-center justify-between">
            <p class="text-[11px] font-bold uppercase tracking-[0.15em] text-blue-100">Total Pendaftar</p>
            <span class="grid h-9 w-9 place-items-center rounded-xl bg-white/20">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none"><circle cx="9" cy="8" r="3" stroke="currentColor" stroke-width="1.8"/><path d="M3.5 19c.5-3.5 2.3-5.2 5.5-5.2s5 1.7 5.5 5.2M16 7.5h5M18.5 5v5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
            </span>
        </div>
        <p class="mt-2 text-3xl font-extrabold">{{ number_format($total_pendaftar) }}</p>
        <p class="mt-1 text-xs text-blue-100">Seluruh pelamar masuk</p>
        <span class="pointer-events-none absolute -bottom-4 -right-4 h-20 w-20 rounded-full bg-white/10"></span>
    </div>

    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-fuchsia-600 to-purple-700 p-5 text-white shadow-lg">
        <div class="flex items-center justify-between">
            <p class="text-[11px] font-bold uppercase tracking-[0.15em] text-fuchsia-100">Sedang Interview</p>
            <span class="grid h-9 w-9 place-items-center rounded-xl bg-white/20">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none"><path d="M12 6v6l4 2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.8"/></svg>
            </span>
        </div>
        <p class="mt-2 text-3xl font-extrabold">{{ number_format($total_interview) }}</p>
        <p class="mt-1 text-xs text-fuchsia-100">Menunggu jadwal/hasil</p>
        <span class="pointer-events-none absolute -bottom-4 -right-4 h-20 w-20 rounded-full bg-white/10"></span>
    </div>

    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-teal-500 to-teal-600 p-5 text-white shadow-lg">
        <div class="flex items-center justify-between">
            <p class="text-[11px] font-bold uppercase tracking-[0.15em] text-teal-100">Sudah Disetujui</p>
            <span class="grid h-9 w-9 place-items-center rounded-xl bg-white/20">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none"><path d="M5 13l4 4L19 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </span>
        </div>
        <p class="mt-2 text-3xl font-extrabold">{{ number_format($total_disetujui) }}</p>
        <p class="mt-1 text-xs text-teal-100">Resmi diterima</p>
        <span class="pointer-events-none absolute -bottom-4 -right-4 h-20 w-20 rounded-full bg-white/10"></span>
    </div>

    <a href="{{ route('admin-karyawan.permintaan-lamaran.index', ['status' => 'ditolak']) }}" class="relative block overflow-hidden rounded-2xl bg-gradient-to-br from-rose-500 to-rose-600 p-5 text-white shadow-lg transition hover:-translate-y-0.5 hover:shadow-xl">
        <div class="flex items-center justify-between">
            <p class="text-[11px] font-bold uppercase tracking-[0.15em] text-rose-100">Ditolak</p>
            <span class="grid h-9 w-9 place-items-center rounded-xl bg-white/20">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none"><path d="m6 6 12 12M18 6 6 18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
            </span>
        </div>
        <p class="mt-2 text-3xl font-extrabold">{{ number_format($total_ditolak) }}</p>
        <p class="mt-1 text-xs text-rose-100">Perlu ditinjau atau dihapus</p>
        <span class="pointer-events-none absolute -bottom-4 -right-4 h-20 w-20 rounded-full bg-white/10"></span>
    </a>
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
    <div class="flex flex-wrap items-center justify-center gap-1.5">
        <button
            type="button"
            title="Lihat Berkas"
            onclick="document.getElementById('berkas-modal-{{ $lamaran->id_permintaan }}').classList.toggle('hidden')"
            class="grid h-8 w-8 place-items-center rounded-lg border border-sky-200 text-sky-600 transition hover:bg-sky-600 hover:text-white"
        >
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7Z" stroke="currentColor" stroke-width="1.8"/><circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.8"/></svg>
        </button>

        @if ($lamaran->status === 'menunggu')
            <button
                type="button"
                title="Jadwalkan Interview"
                onclick="document.getElementById('interview-modal-{{ $lamaran->id_permintaan }}').classList.remove('hidden')"
                class="grid h-8 w-8 place-items-center rounded-lg border border-indigo-200 text-indigo-600 transition hover:bg-indigo-600 hover:text-white"
            >
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none"><rect x="3" y="5" width="18" height="16" rx="2" stroke="currentColor" stroke-width="1.8"/><path d="M3 10h18M8 3v4M16 3v4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
            </button>
        @endif

        @if (in_array($lamaran->status, ['menunggu', 'interview'], true))
            <form method="POST" action="{{ route('admin-karyawan.permintaan-lamaran.action', $lamaran->id_permintaan) }}">
                @csrf
                <input type="hidden" name="action" value="approve">
                <button type="submit" title="Terima" class="grid h-8 w-8 place-items-center rounded-lg border border-emerald-200 text-emerald-600 transition hover:bg-emerald-600 hover:text-white">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none"><path d="M5 13l4 4L19 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
            </form>
            <form method="POST" action="{{ route('admin-karyawan.permintaan-lamaran.action', $lamaran->id_permintaan) }}">
                @csrf
                <input type="hidden" name="action" value="reject">
                <button type="button" title="Tolak" onclick="document.getElementById('reject-modal-{{ $lamaran->id_permintaan }}').classList.remove('hidden')" class="grid h-8 w-8 place-items-center rounded-lg border border-rose-200 text-rose-600 transition hover:bg-rose-600 hover:text-white">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none"><path d="M6 6l12 12M18 6L6 18" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                </button>
            </form>
        @endif

        @if ($lamaran->status === 'ditolak')
            <form method="POST" action="{{ route('admin-karyawan.permintaan-lamaran.destroy', $lamaran->id_permintaan) }}" onsubmit="return confirm('Hapus data lamaran ini secara permanen agar pelamar dapat mendaftar kembali?')">
                @csrf
                @method('DELETE')
                <button type="submit" title="Hapus data" class="grid h-8 w-8 place-items-center rounded-lg border border-rose-200 text-rose-600 transition hover:bg-rose-600 hover:text-white">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none"><path d="M4 7h16M10 11v6M14 11v6M6 7l1 13h10l1-13M9 7l1-3h4l1 3" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
            </form>
        @endif
    </div>
</td>
                        </tr>

                        @if ($lamaran->status === 'ditolak')
                            <tr>
                                <td colspan="5" class="bg-rose-50/60 px-6 py-3 text-xs text-rose-800">
                                    <span class="font-bold">Alasan penolakan:</span> {{ $lamaran->alasan_penolakan ?: 'Belum ada catatan.' }}
                                </td>
                            </tr>
                        @endif

                        @if (in_array($lamaran->status, ['menunggu', 'interview'], true))
                            <tr id="reject-modal-{{ $lamaran->id_permintaan }}" class="hidden">
                                <td colspan="5" class="bg-rose-50/60 px-6 py-5">
                                    <form method="POST" action="{{ route('admin-karyawan.permintaan-lamaran.action', $lamaran->id_permintaan) }}" class="flex flex-col gap-3 sm:flex-row sm:items-end">
                                        @csrf
                                        <input type="hidden" name="action" value="reject">
                                        <div class="min-w-0 flex-1">
                                            <label for="alasan-{{ $lamaran->id_permintaan }}" class="text-[10px] font-bold uppercase tracking-wide text-rose-700">Catatan alasan penolakan</label>
                                            <textarea id="alasan-{{ $lamaran->id_permintaan }}" name="alasan_penolakan" required maxlength="2000" rows="2" class="mt-1 w-full rounded-xl border border-rose-200 px-3 py-2 text-xs focus:border-rose-500 focus:ring-rose-500" placeholder="Jelaskan alasan lamaran ditolak..."></textarea>
                                        </div>
                                        <div class="flex gap-2">
                                            <button type="submit" class="rounded-xl bg-rose-600 px-4 py-2 text-xs font-bold text-white transition hover:bg-rose-700">Simpan Penolakan</button>
                                            <button type="button" onclick="document.getElementById('reject-modal-{{ $lamaran->id_permintaan }}').classList.add('hidden')" class="rounded-xl border border-slate-300 px-4 py-2 text-xs font-bold text-slate-600 transition hover:bg-slate-100">Batal</button>
                                        </div>
                                    </form>
                                </td>
                            </tr>
                        @endif

                        {{-- Panel Lihat Berkas --}}
                        <tr id="berkas-modal-{{ $lamaran->id_permintaan }}" class="hidden">
                            <td colspan="5" class="bg-sky-50/60 px-6 py-5">
                                <p class="mb-3 text-xs font-bold uppercase tracking-wide text-slate-500">
                                    Berkas Lamaran — {{ $lamaran->nama_pemohon }}
                                </p>
                                <div class="grid gap-2 sm:grid-cols-2 lg:grid-cols-4">
                                    @foreach ($daftarBerkas as $kolom => $label)
                                        <div class="flex items-center justify-between gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2.5">
    <span class="truncate text-xs font-semibold text-slate-700">{{ $label }}</span>
    @if (!empty($lamaran->{$kolom}))
        <div class="flex shrink-0 gap-1">
            <a href="{{ asset('storage/' . $lamaran->{$kolom}) }}" target="_blank" rel="noopener" title="Lihat" class="grid h-7 w-7 place-items-center rounded-lg bg-sky-600 text-white hover:bg-sky-700">
                <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7Z" stroke="currentColor" stroke-width="2"/><circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="2"/></svg>
            </a>
            <a href="{{ asset('storage/' . $lamaran->{$kolom}) }}" download title="Unduh" class="grid h-7 w-7 place-items-center rounded-lg border border-sky-200 text-sky-700 hover:bg-sky-50">
                <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none"><path d="M12 3v12m0 0 4-4m-4 4-4-4M4 19h16" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </a>
        </div>
    @else
        <span class="shrink-0 text-[10px] font-semibold text-slate-400">Belum ada</span>
    @endif
</div>
                                    @endforeach
                                </div>
                                <button
                                    type="button"
                                    onclick="document.getElementById('berkas-modal-{{ $lamaran->id_permintaan }}').classList.add('hidden')"
                                    class="mt-3 rounded-xl border border-slate-300 px-4 py-2 text-xs font-bold text-slate-600 transition hover:bg-slate-100"
                                >
                                    Tutup
                                </button>
                            </td>
                        </tr>

                        {{-- Modal jadwalkan interview --}}
                        <tr id="interview-modal-{{ $lamaran->id_permintaan }}" class="hidden">
                            <td colspan="5" class="bg-indigo-50/60 px-6 py-5">
                                <form method="POST" action="{{ route('admin-karyawan.permintaan-lamaran.action', $lamaran->id_permintaan) }}" class="flex flex-wrap items-end gap-3">
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

    </section>
@endsection