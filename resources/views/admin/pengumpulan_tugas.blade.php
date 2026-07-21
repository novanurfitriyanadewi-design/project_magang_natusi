@extends('layouts.portal')

@section('title', 'Data Pengumpulan Tugas - CV Natusi')

@section('content')
<div class="p-6">

    {{-- Page Header --}}
    <section class="mb-6">
        <span class="inline-flex items-center gap-2 rounded-full bg-sky-100 px-3 py-1 text-[10px] font-bold uppercase tracking-[0.16em] text-sky-700">
            <span class="h-1.5 w-1.5 rounded-full bg-sky-500"></span>
            Pengumpulan Tugas
        </span>
        <h2 class="mt-3 text-2xl font-extrabold tracking-tight text-slate-950 sm:text-3xl">Data Pengumpulan Tugas</h2>
        <p class="mt-1 text-sm leading-6 text-slate-500">Pantau progres pengerjaan tugas oleh peserta magang secara real-time.</p>
    </section>

    {{-- Stats Grid --}}
    <section class="grid grid-cols-1 gap-4 mb-6 md:grid-cols-4">
        {{-- Total Peserta --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-4 border-l-4 border-l-sky-600 shadow-sm transition-shadow hover:shadow-md">
            <div class="mb-2 flex items-start justify-between">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500">Total Peserta</span>
                <div class="rounded-lg bg-sky-50 p-2 text-sky-600">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none"><path d="M17 20v-1a4 4 0 0 0-4-4H7a4 4 0 0 0-4 4v1M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm10 9v-1a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </div>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-2xl font-extrabold text-slate-950">{{ $stats['total_peserta'] }}</span>
                <span class="flex items-center gap-0.5 text-xs font-bold text-emerald-600">
                    <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none"><path d="m3 17 6-6 4 4 8-8M15 7h6v6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    {{ $stats['peserta_trend'] }}%
                </span>
            </div>
            <p class="mt-2 text-xs text-slate-500">Aktif periode ini</p>
        </div>

        {{-- Tugas Terkumpul --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-4 border-l-4 border-l-sky-600 shadow-sm transition-shadow hover:shadow-md">
            <div class="mb-2 flex items-start justify-between">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500">Tugas Terkumpul</span>
                <div class="rounded-lg bg-emerald-50 p-2 text-emerald-600">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none"><path d="m9 12 2 2 4-4M5 5h14v14H5V5Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </div>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-2xl font-extrabold text-slate-950">{{ $stats['tugas_terkumpul'] }}</span>
                <div class="ml-2 h-1.5 w-24 overflow-hidden rounded-full bg-slate-100">
                    <div class="h-full bg-emerald-500" style="width: {{ min($stats['persentase_berhasil'], 100) }}%"></div>
                </div>
            </div>
            <p class="mt-2 text-xs text-slate-500">{{ $stats['persentase_berhasil'] }}% tingkat keberhasilan</p>
        </div>

        {{-- Tugas Terlambat --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-4 border-l-4 border-l-rose-500 shadow-sm transition-shadow hover:shadow-md">
            <div class="mb-2 flex items-start justify-between">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500">Tugas Terlambat</span>
                <div class="rounded-lg bg-rose-50 p-2 text-rose-600">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none"><path d="M12 9v4m0 4h.01M10.3 3.9 2.7 17.1a1.6 1.6 0 0 0 1.4 2.4h15.8a1.6 1.6 0 0 0 1.4-2.4L13.7 3.9a1.6 1.6 0 0 0-2.8 0Z" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </div>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-2xl font-extrabold text-slate-950">{{ $stats['tugas_terlambat'] }}</span>
                <span class="text-xs font-bold text-rose-600">Melewati batas waktu</span>
            </div>
            <p class="mt-2 text-xs text-slate-500">Perlu tindak lanjut segera</p>
        </div>

        {{-- Belum Mengumpulkan --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-4 border-l-4 border-l-amber-500 shadow-sm transition-shadow hover:shadow-md">
            <div class="mb-2 flex items-start justify-between">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500">Belum Mengumpulkan</span>
                <div class="rounded-lg bg-amber-50 p-2 text-amber-600">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none"><path d="M12 8v4l2.5 2.5M21 12a9 9 0 1 1-9-9 9 9 0 0 1 9 9Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </div>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-2xl font-extrabold text-slate-950">{{ $stats['belum_mengumpulkan'] }}</span>
                <span class="text-xs font-bold text-slate-500">Menunggu respon</span>
            </div>
            <p class="mt-2 text-xs text-slate-500">Dalam batas waktu normal</p>
        </div>
    </section>

    {{-- Filter Form --}}
    <section class="mb-6 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
        <form action="{{ route('pengumpulan-tugas.index') }}" method="GET" id="filterForm">
            @if(request('search'))
                <input type="hidden" name="search" value="{{ request('search') }}">
            @endif

            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <div class="flex flex-wrap items-center gap-3">
                    <div class="flex flex-col gap-1">
                        <label class="text-xs font-bold text-slate-600">Kategori</label>
                        <select name="kategori" onchange="this.form.submit()" class="rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-sm focus:border-sky-500 focus:ring-sky-500">
                            <option value="Semua Kategori" {{ request('kategori') == 'Semua Kategori' ? 'selected' : '' }}>Semua Kategori</option>
                            <option value="Universitas" {{ request('kategori') == 'Universitas' ? 'selected' : '' }}>Universitas</option>
                            <option value="SMK" {{ request('kategori') == 'SMK' ? 'selected' : '' }}>SMK</option>
                        </select>
                    </div>

                    <div class="flex flex-col gap-1">
                        <label class="text-xs font-bold text-slate-600">Status</label>
                        <select name="status" onchange="this.form.submit()" class="rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-sm focus:border-sky-500 focus:ring-sky-500">
                            <option value="Semua Status" {{ request('status') == 'Semua Status' ? 'selected' : '' }}>Semua Status</option>
                            <option value="Sudah Mengumpulkan" {{ request('status') == 'Sudah Mengumpulkan' ? 'selected' : '' }}>Sudah Mengumpulkan</option>
                            <option value="Belum Mengumpulkan" {{ request('status') == 'Belum Mengumpulkan' ? 'selected' : '' }}>Belum Mengumpulkan</option>
                            <option value="Terlambat" {{ request('status') == 'Terlambat' ? 'selected' : '' }}>Terlambat</option>
                        </select>
                    </div>

                    <div class="flex flex-col gap-1">
                        <label class="text-xs font-bold text-slate-600">Tugas</label>
                        <select name="tugas_id" onchange="this.form.submit()" class="rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-sm focus:border-sky-500 focus:ring-sky-500">
                            <option value="Semua Tugas">Semua Tugas</option>
                            @foreach($daftarTugas as $tugas)
                                <option value="{{ $tugas->id_tugas }}" {{ request('tugas_id') == $tugas->id_tugas ? 'selected' : '' }}>
                                    {{ $tugas->judul_tugas }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    @if(request()->hasAny(['kategori', 'status', 'tugas_id', 'search']))
                        <div class="flex flex-col gap-1 self-end">
                            <a href="{{ route('pengumpulan-tugas.index') }}" class="flex items-center gap-1 rounded-lg bg-slate-100 px-3 py-1.5 text-sm font-semibold text-slate-600 transition-colors hover:bg-slate-200">
                                <svg class="h-[18px] w-[18px]" viewBox="0 0 24 24" fill="none"><path d="M4 12a8 8 0 1 0 2.3-5.7L4 8.6M4 4v4.6h4.6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                Reset
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </form>
    </section>

    {{-- Main Data Table --}}
    <section class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full border-collapse text-left">
                <thead>
                    <tr class="border-b border-slate-200 bg-slate-50">
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-wider text-slate-500">Nama Peserta</th>
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-wider text-slate-500">Instansi</th>
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-wider text-slate-500">Judul Tugas</th>
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-wider text-slate-500">Waktu Submit</th>
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-wider text-slate-500">Status</th>
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-wider text-slate-500">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($submissions as $index => $item)
                        <tr class="transition-colors hover:bg-slate-50">
                            {{-- Nama Peserta --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    @if(!empty($item->peserta?->foto_profile))
                                        <img class="h-10 w-10 rounded-full border border-slate-200 object-cover" src="{{ asset('storage/'.$item->peserta->foto_profile) }}" alt="{{ $item->peserta->nama_peserta }}">
                                    @else
                                        @php
                                            $nama = $item->peserta->nama_peserta ?? 'NP';
                                            $initials = collect(explode(' ', $nama))->map(fn($s) => mb_substr($s, 0, 1))->take(2)->join('');
                                        @endphp
                                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-slate-100 font-bold text-slate-600">
                                            {{ strtoupper($initials) }}
                                        </div>
                                    @endif
                                    <div>
                                        <p class="font-bold text-slate-900">{{ $item->peserta->nama_peserta ?? 'Peserta Tidak Ditemukan' }}</p>
                                        <p class="text-xs text-slate-500">{{ $item->peserta->posisi ?? 'Internship' }}</p>
                                    </div>
                                </div>
                            </td>

                            {{-- Instansi --}}
                            <td class="px-6 py-4">
                                <span class="text-sm text-slate-700">{{ $item->peserta->instansi ?? '-' }}</span>
                            </td>

                            {{-- Judul Tugas --}}
                            <td class="px-6 py-4">
                                <span class="block max-w-[200px] truncate text-sm text-slate-700" title="{{ $item->tugas->judul_tugas ?? '' }}">
                                    {{ $item->tugas->judul_tugas ?? '-' }}
                                </span>
                            </td>

                            {{-- Waktu Submit --}}
                            <td class="px-6 py-4">
                                @if($item->dikumpulkan_pada)
                                    <div class="flex flex-col">
                                        <span class="text-sm text-slate-700">{{ $item->dikumpulkan_pada->translatedFormat('d M Y') }}</span>
                                        <span class="text-xs text-slate-500">{{ $item->dikumpulkan_pada->format('H:i') }} WIB</span>
                                    </div>
                                @else
                                    <span class="italic text-slate-400">Belum Mengumpulkan</span>
                                @endif
                            </td>

                            {{-- Status Badge --}}
                            <td class="px-6 py-4">
                                @if($item->status === 'Sudah Mengumpulkan')
                                    <span class="inline-flex items-center rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-bold text-emerald-700">
                                        Sudah Mengumpulkan
                                    </span>
                                @elseif($item->status === 'Terlambat')
                                    <span class="inline-flex items-center rounded-full border border-rose-300 bg-rose-50 px-2.5 py-0.5 text-xs font-bold text-rose-600">
                                        Terlambat
                                    </span>
                                @else
                                    <span class="inline-flex items-center rounded-full border border-slate-200 bg-slate-100 px-2.5 py-0.5 text-xs font-bold text-slate-600">
                                        Belum Mengumpulkan
                                    </span>
                                @endif
                            </td>

                            {{-- Aksi --}}
                            <td class="px-6 py-4">
                                @if($item->file_jawaban)
                                    <a href="{{ asset('storage/' . $item->file_jawaban) }}" target="_blank" class="flex items-center gap-1.5 text-sm font-bold text-sky-700 transition-all hover:text-sky-800">
                                        <svg class="h-[18px] w-[18px]" viewBox="0 0 24 24" fill="none"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.8"/></svg>
                                        Lihat File
                                    </a>
                                @elseif($item->status === 'Terlambat')
                                    <button type="button" class="flex items-center gap-1.5 text-sm font-bold text-rose-600 transition-all hover:text-rose-700">
                                        <svg class="h-[18px] w-[18px]" viewBox="0 0 24 24" fill="none"><path d="M12 9v4m0 4h.01M10.3 3.9 2.7 17.1a1.6 1.6 0 0 0 1.4 2.4h15.8a1.6 1.6 0 0 0 1.4-2.4L13.7 3.9a1.6 1.6 0 0 0-2.8 0Z" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                        Kirim Pengingat
                                    </button>
                                @else
                                    <button type="button" class="flex items-center gap-1.5 text-sm font-bold text-slate-500 transition-all hover:text-sky-700">
                                        <svg class="h-[18px] w-[18px]" viewBox="0 0 24 24" fill="none"><path d="M4 4h16v16H4V4Zm0 2 8 7 8-7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                        Hubungi
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-14 text-center">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <svg class="h-12 w-12 text-slate-300" viewBox="0 0 24 24" fill="none"><path d="M4 9h16M6 9V6h12v3M5 9v10h14V9M8 13h3" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    <p class="font-bold text-slate-700">Data pengumpulan tugas tidak ditemukan.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="flex flex-col gap-4 border-t border-slate-200 bg-white px-6 py-4 md:flex-row md:items-center md:justify-between">
            <p class="text-xs text-slate-500">
                Menampilkan {{ $submissions->firstItem() ?? 0 }} - {{ $submissions->lastItem() ?? 0 }} dari {{ $submissions->total() }} peserta
            </p>
            <div>
                {{ $submissions->links() }}
            </div>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const cards = document.querySelectorAll('.border-l-4');
        cards.forEach(card => {
            card.addEventListener('mouseenter', () => card.classList.add('-translate-y-0.5'));
            card.addEventListener('mouseleave', () => card.classList.remove('-translate-y-0.5'));
        });
    });
</script>
@endpush