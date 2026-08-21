@extends('layouts.portal')

@section('title', 'Aturan Perusahaan')

@section('content')
    <div class="mx-auto max-w-3xl">

        {{-- HEADER --}}
        <div class="mb-6">
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">
                Aturan Perusahaan
            </h1>

            <p class="mt-1 text-sm text-slate-500">
                Kebijakan serta pedoman CV Natusi untuk memastikan lingkungan kerja yang profesional, aman, dan produktif.
            </p>
        </div>

        {{-- SEARCH BAR --}}
        <div class="relative mb-6">
            <span
                class="material-symbols-outlined pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-xl text-slate-400">
                search
            </span>

            <input
                type="text"
                id="ruleSearch"
                placeholder="Cari aturan atau kebijakan..."
                autocomplete="off"
                class="w-full rounded-xl border border-slate-300 bg-white py-2.5 pl-10 pr-10 text-sm text-slate-800 placeholder-slate-400 shadow-sm transition focus:border-[#006689] focus:outline-none focus:ring-1 focus:ring-[#006689]"
            >

            <button
                type="button"
                id="ruleSearchClear"
                class="absolute right-3 top-1/2 hidden -translate-y-1/2 items-center justify-center text-slate-400 transition hover:text-slate-600"
                aria-label="Hapus pencarian"
            >
                <span class="material-symbols-outlined text-lg">
                    close
                </span>
            </button>
        </div>

        {{-- RULE LIST --}}
        <div
            id="rulesContainer"
            class="flex max-h-[560px] flex-col gap-4 overflow-y-auto pr-1"
        >
            @forelse($aturanList as $aturan)
                <div
                    class="rule-item overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition duration-200 hover:border-slate-300"
                >

                    {{-- ACCORDION HEADER --}}
                    <button
                        type="button"
                        class="accordion-trigger flex w-full items-center justify-between gap-4 p-5 text-left select-none"
                        aria-expanded="false"
                    >

                        {{-- LEFT --}}
                        <div class="flex min-w-0 flex-1 items-start gap-4">

                            {{-- ICON --}}
                            <div
                                class="mt-0.5 flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-slate-100 text-[#006689]"
                            >
                                <span class="material-symbols-outlined text-xl">
                                    gavel
                                </span>
                            </div>

                            {{-- TITLE --}}
                            <div class="min-w-0 flex-1">
                                <h2 class="text-base font-bold leading-snug text-slate-900">
                                    {{ $aturan->nama }}
                                </h2>

                                @if(!empty($aturan->deskripsi))
                                    <p class="accordion-preview mt-1 line-clamp-1 text-sm text-slate-500">
                                        {{ \Illuminate\Support\Str::limit(strip_tags($aturan->deskripsi), 110) }}
                                    </p>
                                @endif
                            </div>

                        </div>

                        {{-- RIGHT --}}
                        <div class="flex shrink-0 items-center gap-3">

                            @if($aturan->updated_at || $aturan->created_at)
                                <span class="hidden text-xs font-medium whitespace-nowrap text-slate-500 sm:block">
                                    {{ \Carbon\Carbon::parse($aturan->updated_at ?? $aturan->created_at)->translatedFormat('d M Y') }}
                                </span>
                            @endif

                            <span
                                class="material-symbols-outlined chevron text-xl text-slate-600 transition-transform duration-300"
                            >
                                expand_more
                            </span>

                        </div>
                    </button>

                    {{-- ACCORDION CONTENT --}}
                    <div class="accordion-content">

                        <div
                            class="border-t border-slate-100 bg-slate-50/60 px-6 py-5 text-sm leading-relaxed text-slate-600 whitespace-pre-line"
                        >
                            {{ $aturan->deskripsi }}
                        </div>

                        @if($aturan->updated_at || $aturan->created_at)
                            <div class="border-t border-slate-100 bg-white px-6 py-3 text-xs text-slate-400 sm:hidden">
                                Terakhir diperbarui:
                                {{ \Carbon\Carbon::parse($aturan->updated_at ?? $aturan->created_at)->translatedFormat('d M Y') }}
                            </div>
                        @endif

                    </div>

                </div>

            @empty

                {{-- EMPTY DATA --}}
                <div
                    class="flex flex-col items-center justify-center rounded-2xl border border-dashed border-slate-200 bg-white px-6 py-12 text-center"
                >
                    <span class="material-symbols-outlined mb-2 text-4xl text-slate-300">
                        rule_folder
                    </span>

                    <p class="text-sm font-semibold text-slate-800">
                        Belum ada aturan yang berlaku
                    </p>

                    <p class="mt-1 max-w-xs text-xs text-slate-500">
                        Aturan perusahaan akan muncul di sini setelah ditambahkan.
                    </p>
                </div>

            @endforelse
        </div>

        {{-- EMPTY SEARCH --}}
        <div
            id="ruleNoResult"
            class="hidden flex-col items-center justify-center rounded-2xl border border-dashed border-slate-200 bg-white px-6 py-12 text-center"
        >
            <span class="material-symbols-outlined mb-2 text-4xl text-slate-300">
                search_off
            </span>

            <p class="text-sm font-semibold text-slate-800">
                Tidak ada aturan yang cocok
            </p>

            <p class="mt-1 text-xs text-slate-500">
                Coba gunakan kata kunci pencarian lain.
            </p>
        </div>

    </div>

    <style>
        .accordion-content {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.35s ease;
        }

        .rule-item.is-open {
            border-color: rgba(0, 102, 137, 0.35);
        }

        .rule-item.is-hidden {
            display: none;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {

            const ruleItems = Array.from(
                document.querySelectorAll('.rule-item')
            );

            const searchInput = document.getElementById('ruleSearch');
            const clearButton = document.getElementById('ruleSearchClear');
            const noResult = document.getElementById('ruleNoResult');

            /*
            |--------------------------------------------------------------------------
            | ACCORDION
            |--------------------------------------------------------------------------
            */

            function closeAccordion(item) {
                const trigger = item.querySelector('.accordion-trigger');
                const content = item.querySelector('.accordion-content');
                const chevron = item.querySelector('.chevron');

                if (!trigger || !content || !chevron) {
                    return;
                }

                content.style.maxHeight = null;

                trigger.setAttribute('aria-expanded', 'false');

                chevron.style.transform = 'rotate(0deg)';

                item.classList.remove('is-open');
            }

            function openAccordion(item) {
                const trigger = item.querySelector('.accordion-trigger');
                const content = item.querySelector('.accordion-content');
                const chevron = item.querySelector('.chevron');

                if (!trigger || !content || !chevron) {
                    return;
                }

                content.style.maxHeight = content.scrollHeight + 'px';

                trigger.setAttribute('aria-expanded', 'true');

                chevron.style.transform = 'rotate(180deg)';

                item.classList.add('is-open');
            }

            ruleItems.forEach(function (item) {

                const trigger = item.querySelector('.accordion-trigger');

                if (!trigger) {
                    return;
                }

                trigger.addEventListener('click', function () {

                    const isOpen = item.classList.contains('is-open');

                    /*
                    | Tutup semua accordion lain
                    */
                    ruleItems.forEach(function (otherItem) {
                        if (otherItem !== item) {
                            closeAccordion(otherItem);
                        }
                    });

                    /*
                    | Toggle accordion yang diklik
                    */
                    if (isOpen) {
                        closeAccordion(item);
                    } else {
                        openAccordion(item);
                    }

                });

            });


            /*
            |--------------------------------------------------------------------------
            | SEARCH
            |--------------------------------------------------------------------------
            */

            function filterRules() {

                if (!searchInput) {
                    return;
                }

                const keyword = searchInput.value
                    .trim()
                    .toLowerCase();

                let visibleCount = 0;

                ruleItems.forEach(function (item) {

                    const title = item.querySelector('h2')
                        ? item.querySelector('h2').textContent.toLowerCase()
                        : '';

                    const description = item.querySelector('.accordion-content')
                        ? item.querySelector('.accordion-content').textContent.toLowerCase()
                        : '';

                    const matches =
                        title.includes(keyword) ||
                        description.includes(keyword);

                    if (matches) {

                        item.classList.remove('is-hidden');

                        visibleCount++;

                    } else {

                        item.classList.add('is-hidden');

                        /*
                        | Jika item disembunyikan,
                        | accordion juga ditutup
                        */
                        closeAccordion(item);

                    }

                });

                /*
                | Tampilkan tombol clear
                */
                if (clearButton) {

                    if (keyword.length > 0) {
                        clearButton.classList.remove('hidden');
                        clearButton.classList.add('flex');
                    } else {
                        clearButton.classList.add('hidden');
                        clearButton.classList.remove('flex');
                    }

                }

                /*
                | Tampilkan pesan jika tidak ada hasil
                */
                if (noResult) {

                    const showNoResult =
                        ruleItems.length > 0 &&
                        visibleCount === 0;

                    if (showNoResult) {
                        noResult.classList.remove('hidden');
                        noResult.classList.add('flex');
                    } else {
                        noResult.classList.add('hidden');
                        noResult.classList.remove('flex');
                    }

                }

            }


            /*
            |--------------------------------------------------------------------------
            | INPUT SEARCH
            |--------------------------------------------------------------------------
            */

            if (searchInput) {

                searchInput.addEventListener('input', filterRules);

            }


            /*
            |--------------------------------------------------------------------------
            | CLEAR SEARCH
            |--------------------------------------------------------------------------
            */

            if (clearButton) {

                clearButton.addEventListener('click', function () {

                    searchInput.value = '';

                    filterRules();

                    searchInput.focus();

                });

            }

        });
    </script>
@endsection