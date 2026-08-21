@extends('layouts.portal')

@section('title', 'Aturan Perusahaan')

@section('content')
    <div class="mx-auto max-w-3xl">

        {{-- HEADER SECTION --}}
        <div class="mb-6">
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Aturan Perusahaan</h1>
            <p class="mt-1 text-sm text-slate-500">
                Kebijakan serta pedoman CV Natusi untuk memastikan lingkungan kerja yang profesional, aman, dan produktif.
            </p>
        </div>

        {{-- SEARCH BAR --}}
        <div class="relative mb-6">
            <span class="material-symbols-outlined pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-xl text-slate-400">
                search
            </span>
            <input
                type="text"
                id="ruleSearch"
                placeholder="Cari aturan atau kebijakan..."
                class="w-full rounded-xl border border-slate-300 bg-white py-2.5 pl-10 pr-10 text-sm text-slate-800 placeholder-slate-400 shadow-2xs transition focus:border-[#006689] focus:outline-none focus:ring-1 focus:ring-[#006689]">
            <button
                type="button"
                id="ruleSearchClear"
                class="absolute right-3 top-1/2 hidden -translate-y-1/2 text-slate-400 transition hover:text-slate-600"
                aria-label="Hapus pencarian">
                <span class="material-symbols-outlined text-lg">close</span>
            </button>
        </div>

        {{-- RULES LIST CONTAINER (scrollable) --}}
        <div id="rulesContainer" class="flex max-h-[560px] flex-col gap-4 overflow-y-auto pr-1">
            @forelse($aturanList as $aturan)
                <div class="rule-item rounded-2xl border border-slate-200/80 bg-white shadow-2xs transition hover:border-slate-300">
                    {{-- CARD HEADER / ACCORDION TRIGGER --}}
                    <button
                        type="button"
                        class="accordion-trigger flex w-full items-center justify-between gap-4 p-5 text-left select-none"
                        aria-expanded="false">

                        {{-- LEFT SIDE: ICON, TITLE & PREVIEW --}}
                        <div class="flex min-w-0 flex-1 items-start gap-4">
                            <div class="mt-0.5 flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-slate-100/80 text-[#006689]">
                                <span class="material-symbols-outlined text-xl">gavel</span>
                            </div>

                            <div class="min-w-0 flex-1 pr-2">
                                <h2 class="text-base font-bold text-slate-900 leading-snug">
                                    {{ $aturan->nama }}
                                </h2>
                                <p class="accordion-preview line-clamp-1 mt-1 text-sm text-slate-500">
                                    {{ \Illuminate\Support\Str::limit($aturan->deskripsi, 110) }}
                                </p>
                            </div>
                        </div>

                        {{-- RIGHT SIDE: DATE & CHEVRON (role badge dihilangkan, halaman ini khusus karyawan) --}}
                        <div class="flex shrink-0 items-center gap-3.5">
                            <span class="text-xs text-slate-500 font-medium whitespace-nowrap">
                                {{ \Carbon\Carbon::parse($aturan->updated_at ?? $aturan->created_at)->translatedFormat('d M Y') }}
                            </span>

                            <span class="material-symbols-outlined chevron text-xl text-slate-700 transition-transform duration-300">
                                expand_more
                            </span>
                        </div>
                    </button>

                    {{-- DROPDOWN CONTENT (collapsed by default) --}}
                    <div class="accordion-content">
                        <div class="border-t border-slate-100 bg-slate-50/50 px-6 py-5 text-sm leading-relaxed text-slate-600 whitespace-pre-line">
                            {!! e($aturan->deskripsi) !!}
                        </div>
                    </div>
                </div>
            @empty
                <div class="flex flex-col items-center justify-center rounded-2xl border border-dashed border-slate-200 bg-white py-12 px-6 text-center">
                    <span class="material-symbols-outlined mb-2 text-4xl text-slate-300">rule_folder</span>
                    <p class="font-semibold text-slate-800 text-sm">Belum ada aturan yang berlaku</p>
                    <p class="text-xs text-slate-500 mt-1 max-w-xs">Aturan perusahaan akan muncul di sini setelah ditambahkan.</p>
                </div>
            @endforelse
        </div>

        {{-- EMPTY SEARCH RESULT --}}
        <div id="ruleNoResult" class="hidden flex-col items-center justify-center rounded-2xl border border-dashed border-slate-200 bg-white py-12 px-6 text-center">
            <span class="material-symbols-outlined mb-2 text-4xl text-slate-300">search_off</span>
            <p class="font-semibold text-slate-800 text-sm">Tidak ada aturan yang cocok</p>
            <p class="text-xs text-slate-500 mt-1">Coba gunakan kata kunci pencarian lain.</p>
        </div>

    </div>

    <style>
        .accordion-content {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
        }
        .accordion-content.is-open {
            max-height: 1000px; /* cukup besar untuk menampung teks aturan terpanjang */
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const ruleItems = Array.from(document.querySelectorAll('.rule-item'));
            const searchInput = document.getElementById('ruleSearch');
            const clearButton = document.getElementById('ruleSearchClear');
            const noResult = document.getElementById('ruleNoResult');

            /* ACCORDION TOGGLE */
            document.querySelectorAll('.accordion-trigger').forEach(trigger => {
                trigger.addEventListener('click', () => {
                    const content = trigger.nextElementSibling;
                    const chevron = trigger.querySelector('.chevron');
                    const isOpen = content.classList.contains('is-open');

                    content.classList.toggle('is-open', !isOpen);
                    trigger.setAttribute('aria-expanded', String(!isOpen));
                    chevron.style.transform = isOpen ? 'rotate(0deg)' : 'rotate(180deg)';
                });
            });

            /* SEARCH FILTER */
            if (searchInput) {
                searchInput.addEventListener('input', (e) => {
                    const term = e.target.value.trim().toLowerCase();
                    clearButton.classList.toggle('hidden', term.length === 0);
                    clearButton.classList.toggle('flex', term.length > 0);

                    let visibleCount = 0;
                    ruleItems.forEach(item => {
                        const matches = item.textContent.toLowerCase().includes(term);
                        item.style.display = matches ? '' : 'none';
                        if (matches) visibleCount++;
                    });

                    const shouldShowNoResult = ruleItems.length > 0 && visibleCount === 0;
                    noResult.classList.toggle('hidden', !shouldShowNoResult);
                    noResult.classList.toggle('flex', shouldShowNoResult);
                });

                clearButton.addEventListener('click', () => {
                    searchInput.value = '';
                    searchInput.dispatchEvent(new Event('input'));
                    searchInput.focus();
                });
            }
        });
    </script>
@endsection