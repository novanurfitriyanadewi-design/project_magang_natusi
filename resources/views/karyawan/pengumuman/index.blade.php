@extends('layouts.portal')

@section('title', 'Pengumuman')

@section('content')

<div class="mx-auto max-w-6xl">

    {{-- HEADER --}}
    <div class="mb-7 flex flex-col gap-4 md:flex-row md:items-end md:justify-between">

        <div>
            <div class="mb-2 flex items-center gap-2">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-50 text-[#05658f]">
                    <span class="material-symbols-outlined">
                        campaign
                    </span>
                </span>

                <h1 class="headline text-2xl font-bold text-slate-900">
                    Pengumuman
                </h1>
            </div>

            <p class="text-sm text-slate-500">
                Informasi dan pengumuman terbaru dari perusahaan.
            </p>
        </div>

        {{-- SEARCH --}}
        <form
            method="GET"
            action="{{ route('karyawan.pengumuman.index') }}"
            class="w-full md:w-80"
        >
            <div class="relative">

                <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-[20px] text-slate-400">
                    search
                </span>

                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Cari pengumuman..."
                    class="w-full rounded-xl border border-slate-200 bg-white py-3 pl-11 pr-4 text-sm text-slate-700 shadow-sm outline-none transition focus:border-[#05658f] focus:ring-4 focus:ring-[#05658f]/10"
                >

            </div>
        </form>

    </div>


    {{-- INFO --}}
    @if($pengumuman->count())

        <div class="mb-5 flex items-center justify-between">

            <div>
                <p class="text-sm font-semibold text-slate-700">
                    Pengumuman Terbaru
                </p>

                <p class="mt-1 text-xs text-slate-400">
                    {{ $pengumuman->count() }} pengumuman tersedia untuk Anda
                </p>
            </div>

        </div>


        {{-- LIST --}}
        <div class="space-y-4">

            @foreach($pengumuman as $item)

                @php

                    $baru = $item->created_at
                        ? $item->created_at->gt(now()->subDays(7))
                        : false;

                    $kategori = strtolower($item->kategori ?? 'umum');

                    $icon = match($kategori) {
                        'penting' => 'priority_high',
                        'acara' => 'event',
                        default => 'campaign',
                    };

                    $iconClass = match($kategori) {
                        'penting' => 'bg-red-50 text-red-600',
                        'acara' => 'bg-purple-50 text-purple-600',
                        default => 'bg-blue-50 text-[#05658f]',
                    };

                    $badgeClass = match($kategori) {
                        'penting' => 'bg-red-50 text-red-600 border-red-100',
                        'acara' => 'bg-purple-50 text-purple-600 border-purple-100',
                        default => 'bg-blue-50 text-blue-600 border-blue-100',
                    };

                    $isIndividu = $item->penerima
                        ->where('tipe_penerima', 'karyawan')
                        ->count() > 0;

                @endphp


                <article
                    class="group rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition-all duration-200 hover:-translate-y-0.5 hover:border-slate-300 hover:shadow-md md:p-6"
                >

                    <div class="flex gap-4">

                        {{-- ICON --}}
                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl {{ $iconClass }}">
                            <span class="material-symbols-outlined">
                                {{ $icon }}
                            </span>
                        </div>


                        {{-- CONTENT --}}
                        <div class="min-w-0 flex-1">

                            {{-- TOP --}}
                            <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">

                                <div class="min-w-0">

                                    <h2 class="text-base font-bold text-slate-800 md:text-lg">
                                        {{ $item->judul }}
                                    </h2>


                                    {{-- BADGES --}}
                                    <div class="mt-2 flex flex-wrap items-center gap-2">

                                        <span
                                            class="rounded-full border px-2.5 py-1 text-[11px] font-semibold {{ $badgeClass }}"
                                        >
                                            {{ ucfirst($kategori) }}
                                        </span>


                                        @if($baru)
                                            <span class="inline-flex items-center gap-1 rounded-full border border-emerald-100 bg-emerald-50 px-2.5 py-1 text-[11px] font-semibold text-emerald-600">

                                                <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>

                                                Baru
                                            </span>
                                        @endif


                                        {{-- TARGET --}}
                                        @if($isIndividu)

                                            <span class="inline-flex items-center gap-1 rounded-full border border-amber-100 bg-amber-50 px-2.5 py-1 text-[11px] font-semibold text-amber-700">

                                                <span class="material-symbols-outlined text-[14px]">
                                                    person
                                                </span>

                                                Untuk Anda
                                            </span>

                                        @else

                                            <span class="inline-flex items-center gap-1 rounded-full border border-slate-200 bg-slate-50 px-2.5 py-1 text-[11px] font-semibold text-slate-600">

                                                <span class="material-symbols-outlined text-[14px]">
                                                    groups
                                                </span>

                                                Semua Karyawan
                                            </span>

                                        @endif

                                    </div>

                                </div>

                            </div>


                            {{-- ISI --}}
                            <div class="mt-4">

                                <p class="whitespace-pre-line text-sm leading-7 text-slate-600">
                                    {{ Str::limit($item->isi, 300) }}
                                </p>

                            </div>


                            {{-- META --}}
                            <div class="mt-5 flex flex-wrap items-center gap-x-5 gap-y-2 border-t border-slate-100 pt-4 text-xs text-slate-400">

                                <span class="inline-flex items-center gap-1.5">

                                    <span class="material-symbols-outlined text-[16px]">
                                        person
                                    </span>

                                    {{ $item->pembuat?->name ?? $item->pembuat?->nama ?? 'Admin' }}

                                </span>


                                <span class="inline-flex items-center gap-1.5">

                                    <span class="material-symbols-outlined text-[16px]">
                                        schedule
                                    </span>

                                    {{ $item->created_at?->translatedFormat('d F Y • H:i') }}

                                </span>

                            </div>

                        </div>

                    </div>

                </article>

            @endforeach

        </div>


    @else

        {{-- EMPTY --}}
        <div class="rounded-2xl border border-dashed border-slate-300 bg-white px-6 py-16 text-center shadow-sm">

            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-blue-50 text-[#05658f]">

                <span class="material-symbols-outlined text-[32px]">
                    campaign
                </span>

            </div>


            @if(request('search'))

                <h3 class="mt-5 text-lg font-bold text-slate-700">
                    Pengumuman Tidak Ditemukan
                </h3>

                <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-slate-400">
                    Tidak ada pengumuman yang sesuai dengan pencarian
                    "{{ request('search') }}".
                </p>

                <a
                    href="{{ route('karyawan.pengumuman.index') }}"
                    class="mt-5 inline-flex items-center gap-2 rounded-xl bg-[#05658f] px-5 py-3 text-sm font-semibold text-white transition hover:bg-[#045575]"
                >
                    <span class="material-symbols-outlined text-[18px]">
                        refresh
                    </span>

                    Tampilkan Semua
                </a>

            @else

                <h3 class="mt-5 text-lg font-bold text-slate-700">
                    Belum Ada Pengumuman
                </h3>

                <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-slate-400">
                    Belum ada informasi atau pengumuman terbaru
                    dari perusahaan.
                </p>

            @endif

        </div>

    @endif

</div>

@endsection