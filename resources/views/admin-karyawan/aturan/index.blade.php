@extends('layouts.portal')

@section('title', 'Kelola Aturan Perusahaan')

@section('content')

<div class="mx-auto w-full max-w-5xl text-on-surface">

    {{-- HEADER --}}
    <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight md:text-3xl">
                Aturan Perusahaan
            </h1>
            <p class="mt-1 max-w-2xl text-sm text-on-surface-variant">
                Kelola kebijakan serta pedoman CV Natusi untuk memastikan lingkungan kerja yang profesional, aman, dan produktif.
            </p>
        </div>

        {{-- TAMBAH BUTTON (Diperbaiki dengan Inline CSS !important agar pasti berwarna biru) --}}
        <button
            type="button"
            onclick="openCreateModal()"
            style="background-color: #05658f !important; color: #ffffff !important;"
            class="inline-flex shrink-0 items-center justify-center gap-2 rounded-xl px-5 py-2.5 text-sm font-semibold shadow-md transition hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-[#05658f]/40">
            <span class="material-symbols-outlined text-[20px]">add</span>
            Tambah Aturan
        </button>
    </div>

    {{-- SUCCESS ALERT --}}
    @if(session('success'))
        <div class="mb-6 flex items-center gap-3 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-green-800">
            <span class="material-symbols-outlined text-[20px] text-green-600">check_circle</span>
            <span class="text-sm font-medium">{{ session('success') }}</span>
        </div>
    @endif

    {{-- ERROR ALERT --}}
    @if($errors->any())
        <div class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4 text-red-800">
            <div class="flex gap-3">
                <span class="material-symbols-outlined text-[20px] text-red-600">error</span>
                <div>
                    <p class="text-sm font-semibold">Terdapat kesalahan:</p>
                    <ul class="mt-1 list-disc pl-5 text-sm space-y-0.5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    {{-- SEARCH BAR --}}
    <div class="relative mb-6 w-full max-w-xl">
        <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-[20px] text-on-surface-variant/60">
            search
        </span>
        <input
            type="text"
            id="ruleSearch"
            placeholder="Cari aturan atau kebijakan..."
            class="w-full rounded-xl border border-outline-variant/60 bg-white py-2.5 pl-11 pr-4 text-sm text-on-surface transition placeholder:text-on-surface-variant/50 focus:border-[#05658f] focus:outline-none focus:ring-2 focus:ring-[#05658f]/20">
    </div>

    {{-- RULES LIST --}}
    <div id="rulesContainer" class="flex flex-col gap-3">

        @forelse($aturanList as $aturan)

            @php
                $roleBadge = match($aturan->untuk_role) {
                    'magang' => 'bg-cyan-50 text-cyan-700 border-cyan-200',
                    'karyawan' => 'bg-purple-50 text-purple-700 border-purple-200',
                    default => 'bg-slate-100 text-slate-700 border-slate-200',
                };
            @endphp

            <div class="rule-item overflow-hidden rounded-2xl border border-outline-variant/50 bg-white shadow-xs transition-all hover:border-outline-variant hover:shadow-sm">

                {{-- HEADER CARD --}}
                <button
                    type="button"
                    class="accordion-trigger group flex w-full items-center justify-between gap-4 p-4 text-left sm:p-5 transition hover:bg-slate-50">

                    <div class="flex min-w-0 flex-1 items-center gap-4">
                        {{-- ICON --}}
                        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-[#05658f]/10 text-[#05658f] transition group-hover:bg-[#05658f] group-hover:text-white">
                            <span class="material-symbols-outlined text-[22px]">gavel</span>
                        </div>

                        {{-- INFO --}}
                        <div class="min-w-0 flex-1">
                            <h2 class="text-base font-semibold text-on-surface">
                                {{ $aturan->nama }}
                            </h2>
                            <p class="accordion-preview line-clamp-1 mt-0.5 text-xs sm:text-sm text-on-surface-variant">
                                {{ \Illuminate\Support\Str::limit($aturan->deskripsi, 110) }}
                            </p>
                        </div>
                    </div>

                    {{-- META & TRIGGER --}}
                    <div class="flex shrink-0 items-center gap-3">
                        <span class="rounded-lg border px-2.5 py-0.5 text-[11px] font-semibold tracking-wide uppercase {{ $roleBadge }}">
                            {{ $aturan->untuk_role === 'semua' ? 'Semua' : ucfirst($aturan->untuk_role) }}
                        </span>

                        <span class="hidden text-xs text-on-surface-variant/70 md:block">
                            {{ $aturan->updated_at?->format('d M Y') }}
                        </span>

                        <span class="material-symbols-outlined chevron text-[20px] text-on-surface-variant transition-transform duration-200">
                            expand_more
                        </span>
                    </div>

                </button>

                {{-- CONTENT EXPANDED --}}
                <div class="accordion-content hidden border-t border-outline-variant/30 bg-slate-50/50 px-5 pb-5 pt-4 sm:pl-[4.25rem]">

                    {{-- STATUS --}}
                    <div class="mb-3 flex items-center gap-2">
                        <span class="text-xs font-medium text-on-surface-variant">Status:</span>
                        @if($aturan->status === 'aktif')
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-semibold text-emerald-700 border border-emerald-200">
                                <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                Aktif
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-semibold text-gray-600 border border-gray-200">
                                <span class="h-1.5 w-1.5 rounded-full bg-gray-400"></span>
                                Nonaktif
                            </span>
                        @endif
                    </div>

                    {{-- DESKRIPSI --}}
                    <div class="text-sm leading-relaxed text-on-surface-variant">
                        {!! nl2br(e($aturan->deskripsi)) !!}
                    </div>

                    {{-- ACTIONS --}}
                    <div class="mt-4 flex items-center justify-end gap-2 border-t border-outline-variant/30 pt-3">
                        <button
                            type="button"
                            onclick='openEditModal(@json($aturan))'
                            class="inline-flex items-center gap-1.5 rounded-lg border border-outline-variant/70 bg-white px-3 py-1.5 text-xs font-medium text-on-surface transition hover:bg-slate-100">
                            <span class="material-symbols-outlined text-[16px]">edit</span>
                            Edit
                        </button>

                        <form
                            action="{{ route('admin-karyawan.aturan.destroy', $aturan->id_aturan) }}"
                            method="POST"
                            onsubmit="return confirm('Apakah Anda yakin ingin menghapus aturan ini?')">
                            @csrf
                            @method('DELETE')
                            <button
                                type="submit"
                                class="inline-flex items-center gap-1.5 rounded-lg border border-red-200 bg-white px-3 py-1.5 text-xs font-medium text-red-600 transition hover:bg-red-50">
                                <span class="material-symbols-outlined text-[16px]">delete</span>
                                Hapus
                            </button>
                        </form>
                    </div>

                </div>

            </div>

        @empty

            {{-- EMPTY STATE --}}
            <div class="rounded-2xl border border-dashed border-outline-variant/80 bg-white p-12 text-center">
                <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-xl bg-[#05658f]/10 text-[#05658f]">
                    <span class="material-symbols-outlined text-[24px]">gavel</span>
                </div>
                <h3 class="text-base font-semibold text-on-surface">Belum Ada Aturan</h3>
                <p class="mt-1 text-sm text-on-surface-variant">Belum ada aturan perusahaan yang ditambahkan.</p>
                
                <button
                    type="button"
                    onclick="openCreateModal()"
                    style="background-color: #05658f !important; color: #ffffff !important;"
                    class="mt-4 inline-flex items-center gap-2 rounded-xl px-4 py-2 text-sm font-semibold shadow-md transition hover:opacity-90">
                    <span class="material-symbols-outlined text-[18px]">add</span>
                    Tambah Aturan Baru
                </button>
            </div>

        @endforelse

    </div>

    {{-- PAGINATION --}}
    @if($aturanList->hasPages())
        <div class="mt-6">
            {{ $aturanList->links() }}
        </div>
    @endif

</div>

{{-- MODAL --}}
<div
    id="ruleModal"
    class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/40 p-4 backdrop-blur-xs">

    <div class="flex max-h-[90vh] w-full max-w-xl flex-col overflow-hidden rounded-2xl bg-white shadow-xl transition-all">

        {{-- MODAL HEADER --}}
        <div class="flex items-center justify-between border-b border-outline-variant/40 px-6 py-4">
            <div>
                <h3 id="modalTitle" class="text-base font-semibold text-on-surface">Tambah Aturan</h3>
                <p class="text-xs text-on-surface-variant">Kelola rincian aturan dan kelompok yang dituju.</p>
            </div>
            <button
                type="button"
                onclick="closeModal()"
                class="rounded-lg p-1 text-on-surface-variant hover:bg-slate-100">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
        </div>

        {{-- FORM --}}
        <form id="ruleForm" method="POST" class="flex-1 space-y-4 overflow-y-auto p-6">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">

            {{-- NAMA --}}
            <div>
                <label for="input_nama" class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-on-surface-variant">
                    Nama Aturan <span class="text-red-500">*</span>
                </label>
                <input
                    type="text"
                    name="nama"
                    id="input_nama"
                    required
                    placeholder="Contoh: Jam Kerja & Kehadiran"
                    class="w-full rounded-xl border border-outline-variant/60 bg-white px-3.5 py-2.5 text-sm outline-none focus:border-[#05658f] focus:ring-2 focus:ring-[#05658f]/20">
            </div>

            {{-- ROLE & STATUS --}}
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label for="input_untuk_role" class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-on-surface-variant">
                        Berlaku Untuk <span class="text-red-500">*</span>
                    </label>
                    <select
                        name="untuk_role"
                        id="input_untuk_role"
                        required
                        class="w-full rounded-xl border border-outline-variant/60 bg-white px-3.5 py-2.5 text-sm outline-none focus:border-[#05658f] focus:ring-2 focus:ring-[#05658f]/20">
                        <option value="semua">Semua (Magang & Karyawan)</option>
                        <option value="magang">Peserta Magang</option>
                        <option value="karyawan">Karyawan</option>
                    </select>
                </div>

                <div>
                    <label for="input_status" class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-on-surface-variant">
                        Status
                    </label>
                    <select
                        name="status"
                        id="input_status"
                        class="w-full rounded-xl border border-outline-variant/60 bg-white px-3.5 py-2.5 text-sm outline-none focus:border-[#05658f] focus:ring-2 focus:ring-[#05658f]/20">
                        <option value="aktif">Aktif</option>
                        <option value="nonaktif">Nonaktif</option>
                    </select>
                </div>
            </div>

            {{-- DESKRIPSI --}}
            <div>
                <label for="input_deskripsi" class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-on-surface-variant">
                    Deskripsi / Isi Aturan <span class="text-red-500">*</span>
                </label>
                <textarea
                    name="deskripsi"
                    id="input_deskripsi"
                    rows="6"
                    required
                    placeholder="Tuliskan isi detail aturan perusahaan..."
                    class="w-full resize-y rounded-xl border border-outline-variant/60 bg-white p-3.5 text-sm outline-none focus:border-[#05658f] focus:ring-2 focus:ring-[#05658f]/20"></textarea>
            </div>

            {{-- FOOTER MODAL --}}
            <div class="flex justify-end gap-2 border-t border-outline-variant/40 pt-4">
                <button
                    type="button"
                    onclick="closeModal()"
                    class="rounded-xl border border-outline-variant/60 px-4 py-2 text-sm font-semibold text-on-surface-variant hover:bg-slate-50">
                    Batal
                </button>
                <button
                    type="submit"
                    style="background-color: #05658f !important; color: #ffffff !important;"
                    class="inline-flex items-center gap-1.5 rounded-xl px-5 py-2 text-sm font-semibold shadow-xs transition hover:opacity-90">
                    <span class="material-symbols-outlined text-[18px]">save</span>
                    Simpan
                </button>
            </div>
        </form>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {

    /* ACCORDION HANDLER */
    document.querySelectorAll('.accordion-trigger').forEach(trigger => {
        trigger.addEventListener('click', () => {
            const content = trigger.nextElementSibling;
            const chevron = trigger.querySelector('.chevron');
            const preview = trigger.querySelector('.accordion-preview');
            const isOpened = !content.classList.contains('hidden');

            document.querySelectorAll('.accordion-content').forEach(el => el.classList.add('hidden'));
            document.querySelectorAll('.chevron').forEach(el => el.style.transform = 'rotate(0deg)');
            document.querySelectorAll('.accordion-preview').forEach(el => el.classList.remove('hidden'));

            if (!isOpened) {
                content.classList.remove('hidden');
                chevron.style.transform = 'rotate(180deg)';
                preview.classList.add('hidden');
            }
        });
    });

    /* SEARCH FILTER */
    const search = document.getElementById('ruleSearch');
    if (search) {
        search.addEventListener('input', () => {
            const keyword = search.value.toLowerCase().trim();
            document.querySelectorAll('.rule-item').forEach(item => {
                item.style.display = item.textContent.toLowerCase().includes(keyword) ? 'block' : 'none';
            });
        });
    }
});

/* MODAL CONTROL */
const modal = document.getElementById('ruleModal');
const form = document.getElementById('ruleForm');

function openCreateModal() {
    document.getElementById('modalTitle').textContent = 'Tambah Aturan';
    form.action = "{{ route('admin-karyawan.aturan.store') }}";
    document.getElementById('formMethod').value = 'POST';
    form.reset();
    document.getElementById('input_untuk_role').value = 'semua';
    document.getElementById('input_status').value = 'aktif';

    modal.classList.remove('hidden');
    modal.classList.add('flex');
    document.body.classList.add('overflow-hidden');
}

function openEditModal(aturan) {
    document.getElementById('modalTitle').textContent = 'Edit Aturan';
    form.action = `/admin-karyawan/aturan/${aturan.id_aturan}`;
    document.getElementById('formMethod').value = 'PUT';

    document.getElementById('input_nama').value = aturan.nama ?? '';
    document.getElementById('input_untuk_role').value = aturan.untuk_role ?? 'semua';
    document.getElementById('input_status').value = aturan.status ?? 'aktif';
    document.getElementById('input_deskripsi').value = aturan.deskripsi ?? '';

    modal.classList.remove('hidden');
    modal.classList.add('flex');
    document.body.classList.add('overflow-hidden');
}

function closeModal() {
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    document.body.classList.remove('overflow-hidden');
}

modal.addEventListener('click', event => {
    if (event.target === modal) closeModal();
});

document.addEventListener('keydown', event => {
    if (event.key === 'Escape' && !modal.classList.contains('hidden')) closeModal();
});
</script>

@endsection