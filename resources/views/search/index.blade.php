@extends('layouts.portal')

@section('title', 'Pencarian')

@section('content')
    <section class="relative overflow-hidden rounded-3xl border border-white/80 bg-gradient-to-br from-[#075985] via-[#0784b8] to-[#38bdf8] px-6 py-6 text-white shadow-[0_20px_50px_rgba(3,105,161,0.20)] sm:px-8">
        <div class="pointer-events-none absolute -right-16 -top-20 h-56 w-56 rounded-full border-[30px] border-white/10"></div>
        <div class="pointer-events-none absolute -bottom-20 right-[30%] h-48 w-48 rounded-full bg-white/10 blur-2xl"></div>

        <div class="relative z-10 max-w-3xl">
            <span class="inline-flex rounded-full border border-white/20 bg-white/10 px-3 py-1.5 text-[10px] font-bold uppercase tracking-[0.17em] text-sky-50">
                Pencarian Portal
            </span>

            <h1 class="mt-4 text-2xl font-extrabold sm:text-3xl">
                Hasil pencarian
            </h1>

            <p class="mt-2 text-sm leading-6 text-sky-50/90">
                {{ $searchDescription }}
            </p>

            <form method="GET" action="{{ route('search.index') }}" class="relative mt-5 max-w-2xl">
                <svg class="pointer-events-none absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <circle cx="11" cy="11" r="6" stroke="currentColor" stroke-width="1.8"/>
                    <path d="m16 16 4 4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                </svg>

                <input
                    name="q"
                    type="search"
                    value="{{ $query }}"
                    placeholder="Masukkan minimal 2 karakter..."
                    autofocus
                    class="h-12 w-full rounded-2xl border-0 bg-white py-3 pl-12 pr-32 text-sm text-slate-800 shadow-lg outline-none ring-1 ring-white/60 placeholder:text-slate-400 focus:ring-4 focus:ring-white/30"
                >

                <button type="submit" class="absolute right-1.5 top-1.5 h-9 rounded-xl bg-[#075985] px-5 text-xs font-bold text-white transition hover:bg-[#064b70]">
                    Cari Data
                </button>
            </form>
        </div>
    </section>

    <section class="mt-5">
        @if (mb_strlen($query) < 2)
            <div class="rounded-3xl border border-sky-100 bg-white/90 px-6 py-14 text-center shadow-[0_16px_40px_rgba(15,52,94,0.07)]">
                <span class="mx-auto grid h-14 w-14 place-items-center rounded-2xl bg-sky-100 text-sky-700">
                    <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <circle cx="11" cy="11" r="6" stroke="currentColor" stroke-width="1.8"/>
                        <path d="m16 16 4 4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                    </svg>
                </span>
                <h2 class="mt-4 text-lg font-bold text-slate-900">Mulai pencarian</h2>
                <p class="mt-2 text-sm text-slate-500">Masukkan sedikitnya 2 karakter untuk mencari data.</p>
            </div>
        @elseif ($results->isEmpty())
            <div class="rounded-3xl border border-slate-200 bg-white/90 px-6 py-14 text-center shadow-[0_16px_40px_rgba(15,52,94,0.07)]">
                <span class="mx-auto grid h-14 w-14 place-items-center rounded-2xl bg-slate-100 text-slate-500">
                    <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <circle cx="11" cy="11" r="6" stroke="currentColor" stroke-width="1.8"/>
                        <path d="m16 16 4 4M8.5 9.5l5 5M13.5 9.5l-5 5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                    </svg>
                </span>
                <h2 class="mt-4 text-lg font-bold text-slate-900">Data tidak ditemukan</h2>
                <p class="mt-2 text-sm text-slate-500">Tidak ada hasil untuk “{{ $query }}”. Coba gunakan nama atau kata kunci lain.</p>
            </div>
        @else
            <div class="mb-4 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-xl font-extrabold text-slate-900">Ditemukan {{ $results->count() }} data</h2>
                    <p class="mt-1 text-sm text-slate-500">Kata kunci: “{{ $query }}”</p>
                </div>
            </div>

            <div class="space-y-5">
                @foreach ($groupedResults as $category => $categoryResults)
                    <article class="overflow-hidden rounded-3xl border border-white/80 bg-white/90 shadow-[0_18px_45px_rgba(15,52,94,0.08)] backdrop-blur">
                        <div class="flex items-center justify-between border-b border-slate-200/80 bg-gradient-to-r from-sky-50 via-blue-50 to-indigo-50 px-5 py-4 sm:px-6">
                            <div>
                                <h3 class="text-base font-extrabold text-slate-900">{{ $category }}</h3>
                                <p class="mt-0.5 text-xs text-slate-500">{{ $categoryResults->count() }} hasil ditemukan</p>
                            </div>
                            <span class="rounded-full bg-white px-3 py-1 text-xs font-bold text-sky-700 shadow-sm ring-1 ring-sky-100">
                                {{ $categoryResults->count() }}
                            </span>
                        </div>

                        <div class="grid gap-3 p-4 sm:grid-cols-2 xl:grid-cols-3 sm:p-5">
                            @foreach ($categoryResults as $result)
                                <a
                                    href="{{ $result['url'] }}"
                                    class="group relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-4 transition duration-200 hover:-translate-y-1 hover:border-sky-200 hover:shadow-[0_14px_30px_rgba(2,132,199,0.12)]"
                                >
                                    <div class="absolute -right-7 -top-7 h-20 w-20 rounded-full bg-sky-50 transition group-hover:bg-sky-100"></div>

                                    <div class="relative flex items-start gap-3">
                                        <span class="grid h-11 w-11 shrink-0 place-items-center rounded-2xl bg-gradient-to-br from-sky-100 to-blue-100 text-sky-700 ring-1 ring-sky-200">
                                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                <circle cx="9" cy="8" r="3" stroke="currentColor" stroke-width="1.7"/>
                                                <path d="M3.5 19c.5-3.5 2.3-5.2 5.5-5.2s5 1.7 5.5 5.2M16 7.5h5M18.5 5v5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                                            </svg>
                                        </span>

                                        <span class="min-w-0 flex-1">
                                            <strong class="block truncate text-sm font-bold text-slate-900 group-hover:text-sky-700">{{ $result['title'] }}</strong>
                                            <span class="mt-1 block truncate text-xs text-slate-500">{{ $result['subtitle'] }}</span>
                                            <span class="mt-2 block text-[11px] leading-5 text-slate-400">{{ $result['meta'] }}</span>
                                        </span>
                                    </div>

                                    <div class="relative mt-4 flex items-center justify-between border-t border-slate-100 pt-3">
                                        <span class="rounded-full bg-slate-100 px-2.5 py-1 text-[9px] font-bold uppercase tracking-wide text-slate-500">
                                            {{ $result['category'] }}
                                        </span>
                                        <span class="inline-flex items-center gap-1 text-[11px] font-bold text-sky-700">
                                            {{ $result['can_open'] ? 'Buka data' : 'Lihat hasil' }}
                                            <svg class="h-3.5 w-3.5 transition group-hover:translate-x-0.5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                <path d="M5 12h14M14 7l5 5-5 5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                        </span>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
    </section>
@endsection
