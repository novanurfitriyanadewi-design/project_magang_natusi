@extends('layouts.portal')

@section('title', 'Aturan Perusahaan')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-on-surface mb-1">Aturan Perusahaan</h1>
        <p class="text-on-surface-variant max-w-2xl">Harap tinjau dan patuhi kebijakan serta pedoman CV Natusi berikut.</p>
    </div>

    <div class="relative mb-6 max-w-2xl">
        <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-outline">search</span>
        <input type="text" id="ruleSearch" placeholder="Cari aturan..."
            class="w-full pl-12 pr-4 py-3 bg-white border border-outline-variant rounded-full focus:border-primary outline-none">
    </div>

    <div class="flex flex-col gap-4 max-w-2xl" id="rulesContainer">
        @forelse ($aturanList as $aturan)
            <div class="bg-white rounded-2xl border border-outline-variant overflow-hidden rule-item transition-shadow hover:shadow-sm">
                <button type="button" class="w-full flex items-center gap-4 p-5 text-left accordion-trigger">
                    <div class="min-w-0 flex-1">
                        <h2 class="font-semibold text-on-surface truncate">{{ $aturan->nama }}</h2>
                        <p class="text-on-surface-variant text-sm line-clamp-1 accordion-preview">
                            {{ \Illuminate\Support\Str::limit($aturan->deskripsi, 100) }}
                        </p>
                    </div>
                    <span class="material-symbols-outlined chevron shrink-0 text-outline transition-transform duration-300">expand_more</span>
                </button>
                <div class="accordion-content hidden px-5 pb-5 border-t border-outline-variant/50 pt-4 text-on-surface-variant leading-relaxed">
                    {!! nl2br(e($aturan->deskripsi)) !!}
                </div>
            </div>
        @empty
            <p class="text-on-surface-variant text-center py-8">Belum ada aturan yang berlaku untuk kamu saat ini.</p>
        @endforelse
    </div>

    <script>
        document.querySelectorAll('.accordion-trigger').forEach(trigger => {
            trigger.addEventListener('click', () => {
                const content = trigger.nextElementSibling;
                const chevron = trigger.querySelector('.chevron');
                const isExpanded = !content.classList.contains('hidden');
                content.classList.toggle('hidden');
                chevron.style.transform = isExpanded ? 'rotate(0deg)' : 'rotate(180deg)';
            });
        });

        document.getElementById('ruleSearch').addEventListener('input', (e) => {
            const term = e.target.value.toLowerCase();
            document.querySelectorAll('.rule-item').forEach(item => {
                item.style.display = item.textContent.toLowerCase().includes(term) ? 'block' : 'none';
            });
        });
    </script>
@endsection