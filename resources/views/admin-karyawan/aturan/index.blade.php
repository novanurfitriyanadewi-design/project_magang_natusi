@extends('layouts.portal')

@section('title', 'Kelola Aturan Perusahaan')

@section('content')

<div class="min-h-full bg-background text-on-surface">

    {{-- PAGE HEADER --}}
    {{-- Tambahan pt-6 md:pt-8 agar tidak mepet dengan header --}}
    <div class="pt-6 md:pt-8 mb-8">

        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-5">

            <div class="min-w-0">

                <h1 class="text-2xl md:text-[30px] leading-tight font-bold text-on-surface">
                    Aturan Perusahaan
                </h1>

                <p class="mt-2 text-sm md:text-base text-on-surface-variant max-w-2xl leading-relaxed">
                    Kelola kebijakan serta pedoman CV Natusi untuk memastikan lingkungan kerja
                    yang profesional, aman, dan produktif bagi seluruh karyawan dan peserta magang.
                </p>

            </div>


            {{-- TAMBAH ATURAN --}}
            <button
                type="button"
                onclick="openCreateModal()"
                class="inline-flex
                       items-center
                       justify-center
                       gap-2
                       bg-primary
                       text-white
                       px-4
                       py-2.5
                       rounded-lg
                       font-semibold
                       text-sm
                       hover:bg-primary-container
                       transition-all
                       shadow-sm
                       active:scale-95
                       whitespace-nowrap
                       sm:mt-1">

                <span class="material-symbols-outlined text-[20px]">
                    add
                </span>

                Tambah Aturan

            </button>

        </div>

    </div>


    {{-- SUCCESS MESSAGE --}}
    @if (session('success'))

        <div
            class="mb-6
                   flex
                   items-center
                   gap-3
                   bg-green-50
                   border
                   border-green-200
                   text-green-700
                   rounded-xl
                   px-4
                   py-3">

            <span class="material-symbols-outlined text-[20px]">
                check_circle
            </span>

            <span class="text-sm font-medium">
                {{ session('success') }}
            </span>

        </div>

    @endif


    {{-- VALIDATION ERROR --}}
    @if ($errors->any())

        <div
            class="mb-6
                   bg-red-50
                   border
                   border-red-200
                   text-red-700
                   rounded-xl
                   px-4
                   py-3">

            <div class="flex items-start gap-3">

                <span class="material-symbols-outlined text-[20px]">
                    error
                </span>

                <div>

                    <p class="font-semibold text-sm mb-1">
                        Terdapat kesalahan:
                    </p>

                    <ul class="text-sm list-disc pl-5 space-y-1">

                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach

                    </ul>

                </div>

            </div>

        </div>

    @endif


    {{-- SEARCH --}}
    <div class="relative mb-7 max-w-2xl">

        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">

            <span class="material-symbols-outlined text-outline">
                search
            </span>

        </div>

        <input
            type="text"
            id="ruleSearch"
            placeholder="Cari aturan atau kebijakan..."
            class="w-full
                   pl-12
                   pr-4
                   py-3.5
                   bg-white
                   border
                   border-outline-variant
                   rounded-full
                   focus:border-primary
                   focus:ring-1
                   focus:ring-primary
                   focus:outline-none
                   text-sm
                   text-on-surface
                   transition-all
                   shadow-sm
                   hover:shadow-md">

    </div>


    {{-- RULES CONTAINER --}}
    <div
        id="rulesContainer"
        class="flex flex-col gap-5">

        @forelse ($aturanList as $aturan)

            {{-- RULE ITEM --}}
            <div
                class="rule-item
                       bg-white
                       rounded-2xl
                       border
                       border-surface-container-highest
                       shadow-sm
                       overflow-hidden
                       relative
                       transition-all
                       hover:shadow-md">

                {{-- BLUE LEFT LINE --}}
                <div
                    class="absolute
                           left-0
                           top-0
                           bottom-0
                           w-1.5
                           bg-corporate-blue
                           rounded-l-2xl">
                </div>


                {{-- ACCORDION HEADER --}}
                <button
                    type="button"
                    class="accordion-trigger
                           w-full
                           flex
                           items-center
                           justify-between
                           p-5
                           md:p-6
                           pl-7
                           md:pl-8
                           text-left
                           hover:bg-surface-container-low
                           transition-colors
                           focus:outline-none
                           group">

                    <div
                        class="flex
                               flex-col
                               md:flex-row
                               md:items-center
                               gap-4
                               w-full">

                        {{-- ICON --}}
                        <div
                            class="p-3
                                   bg-surface-container
                                   text-corporate-blue
                                   rounded-xl
                                   flex-shrink-0
                                   group-hover:bg-primary-fixed
                                   transition-colors
                                   w-fit">

                            <span class="material-symbols-outlined icon-filled text-[22px]">
                                gavel
                            </span>

                        </div>


                        {{-- TITLE + DESCRIPTION --}}
                        <div class="flex-1 min-w-0">

                            <h2
                                class="text-lg
                                       md:text-xl
                                       font-semibold
                                       text-on-surface
                                       mb-1">

                                {{ $aturan->nama }}

                            </h2>

                            <p
                                class="accordion-preview
                                       text-sm
                                       text-on-surface-variant
                                       line-clamp-1">

                                {{ \Illuminate\Support\Str::limit($aturan->deskripsi, 100) }}

                            </p>

                        </div>


                        {{-- META --}}
                        <div
                            class="flex
                                   items-center
                                   gap-3
                                   w-full
                                   md:w-auto
                                   justify-between
                                   md:justify-end
                                   md:flex-shrink-0">

                            @php
                                $roleClass = match ($aturan->untuk_role) {
                                    'magang' => 'bg-[#e6f4fb] text-corporate-blue',
                                    'karyawan' => 'bg-purple-50 text-purple-700',
                                    default => 'bg-gray-100 text-gray-700',
                                };
                            @endphp


                            {{-- ROLE BADGE --}}
                            <span
                                class="inline-flex
                                       items-center
                                       px-3
                                       py-1
                                       {{ $roleClass }}
                                       font-semibold
                                       text-[11px]
                                       rounded-full
                                       uppercase
                                       tracking-wider
                                       whitespace-nowrap">

                                {{ $aturan->untuk_role === 'semua'
                                    ? 'Semua'
                                    : ucfirst($aturan->untuk_role) }}

                            </span>


                            {{-- DATE + CHEVRON --}}
                            <div
                                class="flex
                                       items-center
                                       gap-2
                                       text-on-surface-variant">

                                <span class="text-[11px] whitespace-nowrap hidden sm:block">

                                    {{ $aturan->updated_at?->format('d M Y') }}

                                </span>

                                <span
                                    class="material-symbols-outlined
                                           chevron
                                           transition-transform
                                           duration-300
                                           text-on-surface-variant">

                                    expand_more

                                </span>

                            </div>

                        </div>

                    </div>

                </button>


                {{-- ACCORDION CONTENT --}}
                <div
                    class="accordion-content
                           hidden
                           px-5
                           md:px-6
                           pb-6
                           pt-0
                           ml-2
                           md:ml-20">

                    <div
                        class="border-t
                               border-outline-variant/40
                               pt-4
                               text-sm
                               text-on-surface-variant
                               leading-relaxed">


                        {{-- STATUS --}}
                        <div class="flex flex-wrap items-center gap-2 mb-4">

                            <span class="text-xs font-semibold text-on-surface">
                                Status:
                            </span>


                            @if ($aturan->status === 'aktif')

                                <span
                                    class="inline-flex
                                           items-center
                                           gap-1
                                           px-2.5
                                           py-1
                                           bg-green-50
                                           text-green-700
                                           rounded-full
                                           text-xs
                                           font-semibold">

                                    <span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span>

                                    Aktif

                                </span>

                            @else

                                <span
                                    class="inline-flex
                                           items-center
                                           gap-1
                                           px-2.5
                                           py-1
                                           bg-gray-100
                                           text-gray-500
                                           rounded-full
                                           text-xs
                                           font-semibold">

                                    <span class="w-1.5 h-1.5 bg-gray-400 rounded-full"></span>

                                    Nonaktif

                                </span>

                            @endif

                        </div>


                        {{-- DESCRIPTION --}}
                        <div
                            class="prose
                                   prose-sm
                                   max-w-none
                                   text-on-surface-variant">

                            {!! nl2br(e($aturan->deskripsi)) !!}

                        </div>


                        {{-- ACTIONS --}}
                        <div
                            class="mt-6
                                   pt-4
                                   border-t
                                   border-outline-variant/30
                                   flex
                                   flex-wrap
                                   justify-end
                                   gap-2">


                            {{-- EDIT --}}
                            <button
                                type="button"
                                onclick='openEditModal(@json($aturan))'
                                class="inline-flex
                                       items-center
                                       gap-2
                                       px-3
                                       py-2
                                       rounded-lg
                                       border
                                       border-outline-variant
                                       text-on-surface-variant
                                       text-sm
                                       font-semibold
                                       hover:bg-surface-container
                                       hover:text-primary
                                       transition-colors">

                                <span class="material-symbols-outlined text-[18px]">
                                    edit
                                </span>

                                Edit

                            </button>


                            {{-- DELETE --}}
                            <form
                                action="{{ route('admin-karyawan.aturan.destroy', $aturan->id_aturan) }}"
                                method="POST"
                                class="inline"
                                onsubmit="return confirm('Apakah Anda yakin ingin menghapus aturan ini?')">

                                @csrf
                                @method('DELETE')

                                <button
                                    type="submit"
                                    class="inline-flex
                                           items-center
                                           gap-2
                                           px-3
                                           py-2
                                           rounded-lg
                                           border
                                           border-red-200
                                           text-red-600
                                           text-sm
                                           font-semibold
                                           hover:bg-red-50
                                           transition-colors">

                                    <span class="material-symbols-outlined text-[18px]">
                                        delete
                                    </span>

                                    Hapus

                                </button>

                            </form>

                        </div>

                    </div>

                </div>

            </div>

        @empty

            {{-- EMPTY STATE --}}
            <div
                class="bg-white
                       rounded-2xl
                       border
                       border-outline-variant
                       px-6
                       py-14
                       text-center">

                <div
                    class="mx-auto
                           w-14
                           h-14
                           rounded-2xl
                           bg-surface-container
                           text-corporate-blue
                           flex
                           items-center
                           justify-center
                           mb-4">

                    <span class="material-symbols-outlined text-[28px]">
                        gavel
                    </span>

                </div>


                <h3 class="text-lg font-semibold text-on-surface">
                    Belum Ada Aturan
                </h3>


                <p class="mt-1 text-sm text-on-surface-variant">
                    Belum ada aturan perusahaan yang ditambahkan.
                </p>


                <button
                    type="button"
                    onclick="openCreateModal()"
                    class="mt-5
                           inline-flex
                           items-center
                           gap-2
                           px-4
                           py-2.5
                           rounded-lg
                           bg-primary
                           text-white
                           font-semibold
                           text-sm
                           hover:bg-primary-container
                           transition-colors">

                    <span class="material-symbols-outlined text-[18px]">
                        add
                    </span>

                    Tambah Aturan

                </button>

            </div>

        @endforelse

    </div>


    {{-- PAGINATION --}}
    @if ($aturanList->hasPages())

        <div class="mt-6">
            {{ $aturanList->links() }}
        </div>

    @endif

</div>



{{-- ========================================================= --}}
{{-- MODAL TAMBAH / EDIT --}}
{{-- ========================================================= --}}

<div
    id="ruleModal"
    class="hidden
           fixed
           inset-0
           z-50
           flex
           items-center
           justify-center
           p-4
           bg-black/40
           backdrop-blur-sm">

    <div
        class="bg-white
               rounded-2xl
               w-full
               max-w-2xl
               max-h-[90vh]
               flex
               flex-col
               overflow-hidden
               shadow-2xl">


        {{-- MODAL HEADER --}}
        <div
            class="flex
                   justify-between
                   items-center
                   p-5
                   border-b
                   border-outline-variant">

            <div>

                <h3
                    id="modalTitle"
                    class="text-lg
                           font-semibold
                           text-on-surface">

                    Tambah Aturan

                </h3>

                <p class="text-xs text-on-surface-variant mt-1">
                    Tambahkan kebijakan atau aturan perusahaan.
                </p>

            </div>


            <button
                type="button"
                onclick="closeModal()"
                class="p-2
                       rounded-full
                       text-on-surface-variant
                       hover:bg-surface-container
                       transition-colors">

                <span class="material-symbols-outlined">
                    close
                </span>

            </button>

        </div>


        {{-- FORM --}}
        <form
            id="ruleForm"
            method="POST"
            class="flex-1
                   overflow-y-auto
                   flex
                   flex-col
                   gap-5
                   p-6">

            @csrf

            <input
                type="hidden"
                name="_method"
                id="formMethod"
                value="POST">


            {{-- NAMA --}}
            <div>

                <label
                    for="input_nama"
                    class="block
                           text-sm
                           font-semibold
                           text-on-surface
                           mb-2">

                    Nama Aturan
                    <span class="text-red-500">*</span>

                </label>


                <input
                    type="text"
                    name="nama"
                    id="input_nama"
                    required
                    placeholder="Contoh: Jam Kerja & Kehadiran"
                    class="w-full
                           px-4
                           py-3
                           bg-white
                           border
                           border-outline-variant
                           rounded-lg
                           text-sm
                           focus:border-primary
                           focus:ring-1
                           focus:ring-primary
                           outline-none
                           transition-all">

            </div>


            {{-- ROLE + STATUS --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">


                {{-- ROLE --}}
                <div>

                    <label
                        for="input_untuk_role"
                        class="block
                               text-sm
                               font-semibold
                               text-on-surface
                               mb-2">

                        Berlaku Untuk
                        <span class="text-red-500">*</span>

                    </label>


                    <select
                        name="untuk_role"
                        id="input_untuk_role"
                        required
                        class="w-full
                               px-4
                               py-3
                               bg-white
                               border
                               border-outline-variant
                               rounded-lg
                               text-sm
                               focus:border-primary
                               focus:ring-1
                               focus:ring-primary
                               outline-none">

                        <option value="semua">
                            Semua (Magang & Karyawan)
                        </option>

                        <option value="magang">
                            Peserta Magang
                        </option>

                        <option value="karyawan">
                            Karyawan
                        </option>

                    </select>

                </div>


                {{-- STATUS --}}
                <div>

                    <label
                        for="input_status"
                        class="block
                               text-sm
                               font-semibold
                               text-on-surface
                               mb-2">

                        Status

                    </label>


                    <select
                        name="status"
                        id="input_status"
                        class="w-full
                               px-4
                               py-3
                               bg-white
                               border
                               border-outline-variant
                               rounded-lg
                               text-sm
                               focus:border-primary
                               focus:ring-1
                               focus:ring-primary
                               outline-none">

                        <option value="aktif">
                            Aktif
                        </option>

                        <option value="nonaktif">
                            Nonaktif
                        </option>

                    </select>

                </div>

            </div>


            {{-- DESKRIPSI --}}
            <div>

                <label
                    for="input_deskripsi"
                    class="block
                           text-sm
                           font-semibold
                           text-on-surface
                           mb-2">

                    Deskripsi / Isi Aturan
                    <span class="text-red-500">*</span>

                </label>


                <textarea
                    name="deskripsi"
                    id="input_deskripsi"
                    rows="7"
                    required
                    placeholder="Tuliskan isi aturan perusahaan..."
                    class="w-full
                           px-4
                           py-3
                           bg-white
                           border
                           border-outline-variant
                           rounded-lg
                           text-sm
                           focus:border-primary
                           focus:ring-1
                           focus:ring-primary
                           outline-none
                           resize-y"></textarea>

            </div>


            {{-- MODAL FOOTER --}}
            <div
                class="flex
                       justify-end
                       gap-3
                       pt-4
                       border-t
                       border-outline-variant">

                <button
                    type="button"
                    onclick="closeModal()"
                    class="px-4
                           py-2.5
                           rounded-lg
                           border
                           border-outline-variant
                           text-on-surface-variant
                           font-semibold
                           text-sm
                           hover:bg-surface-container
                           transition-colors">

                    Batal

                </button>


                <button
                    type="submit"
                    class="inline-flex
                           items-center
                           gap-2
                           px-5
                           py-2.5
                           rounded-lg
                           bg-primary
                           text-white
                           font-semibold
                           text-sm
                           hover:bg-primary-container
                           transition-colors">

                    <span class="material-symbols-outlined text-[18px]">
                        save
                    </span>

                    Simpan

                </button>

            </div>

        </form>

    </div>

</div>



{{-- ========================================================= --}}
{{-- JAVASCRIPT --}}
{{-- ========================================================= --}}

<script>

document.addEventListener('DOMContentLoaded', function () {


    /*
    |--------------------------------------------------------------------------
    | ACCORDION
    |--------------------------------------------------------------------------
    */

    document.querySelectorAll('.accordion-trigger').forEach(trigger => {

        trigger.addEventListener('click', function () {

            const content = this.nextElementSibling;
            const chevron = this.querySelector('.chevron');
            const preview = this.querySelector('.accordion-preview');

            const isExpanded =
                !content.classList.contains('hidden');


            /*
            | Tutup semua accordion
            */

            document.querySelectorAll('.accordion-content').forEach(item => {

                item.classList.add('hidden');

            });


            document.querySelectorAll('.chevron').forEach(icon => {

                icon.style.transform = 'rotate(0deg)';

            });


            document.querySelectorAll('.accordion-preview').forEach(item => {

                item.classList.remove('hidden');

            });


            /*
            | Buka accordion yang dipilih
            */

            if (!isExpanded) {

                content.classList.remove('hidden');


                if (chevron) {

                    chevron.style.transform =
                        'rotate(180deg)';

                }


                if (preview) {

                    preview.classList.add('hidden');

                }

            }

        });

    });



    /*
    |--------------------------------------------------------------------------
    | SEARCH
    |--------------------------------------------------------------------------
    */

    const searchInput =
        document.getElementById('ruleSearch');

    const ruleItems =
        document.querySelectorAll('.rule-item');


    if (searchInput) {

        searchInput.addEventListener('input', function (event) {

            const term =
                event.target.value
                    .toLowerCase()
                    .trim();


            ruleItems.forEach(item => {

                const title =
                    item.querySelector('h2')
                        ?.textContent
                        .toLowerCase() ?? '';


                const content =
                    item.querySelector('.accordion-content')
                        ?.textContent
                        .toLowerCase() ?? '';


                const category =
                    item.querySelector('.rounded-full')
                        ?.textContent
                        .toLowerCase() ?? '';


                const matched =
                    title.includes(term) ||
                    content.includes(term) ||
                    category.includes(term);


                item.style.display =
                    matched ? 'block' : 'none';

            });

        });

    }

});



/*
|--------------------------------------------------------------------------
| MODAL
|--------------------------------------------------------------------------
*/

const modal =
    document.getElementById('ruleModal');

const form =
    document.getElementById('ruleForm');



/*
|--------------------------------------------------------------------------
| CREATE MODAL
|--------------------------------------------------------------------------
*/

function openCreateModal() {

    document.getElementById('modalTitle').textContent =
        'Tambah Aturan';


    form.action =
        "{{ route('admin-karyawan.aturan.store') }}";


    document.getElementById('formMethod').value =
        'POST';


    form.reset();


    document.getElementById('input_untuk_role').value =
        'semua';


    document.getElementById('input_status').value =
        'aktif';


    modal.classList.remove('hidden');


    document.body.classList.add('overflow-hidden');

}



/*
|--------------------------------------------------------------------------
| EDIT MODAL
|--------------------------------------------------------------------------
*/

function openEditModal(aturan) {

    document.getElementById('modalTitle').textContent =
        'Edit Aturan';


    form.action =
        `/admin-karyawan/aturan/${aturan.id_aturan}`;


    document.getElementById('formMethod').value =
        'PUT';


    document.getElementById('input_nama').value =
        aturan.nama ?? '';


    document.getElementById('input_untuk_role').value =
        aturan.untuk_role ?? 'semua';


    document.getElementById('input_status').value =
        aturan.status ?? 'aktif';


    document.getElementById('input_deskripsi').value =
        aturan.deskripsi ?? '';


    modal.classList.remove('hidden');


    document.body.classList.add('overflow-hidden');

}



/*
|--------------------------------------------------------------------------
| CLOSE MODAL
|--------------------------------------------------------------------------
*/

function closeModal() {

    modal.classList.add('hidden');

    document.body.classList.remove('overflow-hidden');

}



/*
|--------------------------------------------------------------------------
| CLICK OUTSIDE MODAL
|--------------------------------------------------------------------------
*/

if (modal) {

    modal.addEventListener('click', function (event) {

        if (event.target === modal) {

            closeModal();

        }

    });

}



/*
|--------------------------------------------------------------------------
| ESCAPE KEY
|--------------------------------------------------------------------------
*/

document.addEventListener('keydown', function (event) {

    if (event.key === 'Escape') {

        if (!modal.classList.contains('hidden')) {

            closeModal();

        }

    }

});

</script>

@endsection
