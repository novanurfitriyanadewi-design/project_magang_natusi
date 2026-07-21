
@extends('layouts.portal')

@section('title', 'Kelola Tugas Magang')

@section('content')
<div
    x-data="kelolaTugas()"
    x-init="init()"
>

    {{-- Breadcrumb --}}
    <nav class="mb-3 flex items-center gap-2 text-sm text-slate-500">
        <span>Manajemen</span>
        <span class="material-symbols-outlined text-[16px]">chevron_right</span>
        <span class="font-medium text-slate-700">Kelola Tugas Magang</span>
    </nav>

    {{-- Heading --}}
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="headline text-2xl font-bold text-slate-900">Kelola Tugas Magang</h1>
            <p class="mt-1 text-sm text-slate-500">
                Unggah dan perbarui daftar tugas berkala untuk peserta magang berdasarkan jenis tugas.
            </p>
        </div>

        <button
            type="button"
            @click="openCreate()"
            class="inline-flex items-center gap-2 rounded-xl bg-blue-900 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-800"
        >
            <span class="material-symbols-outlined text-[18px]">add</span>
            Tambah Tugas
        </button>
    </div>

    {{-- ================= UPLOAD TEMPLATE (MASSAL) ================= --}}
    <div class="mb-6 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">

        <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
            <h2 class="text-base font-semibold text-slate-800">Unggah Template Tugas</h2>
            <p class="text-xs text-slate-500">Gunakan file Excel (.xlsx) untuk pembaruan massal.</p>

            <a
                href="{{ route('admin.tugas.template.download') }}"
                class="ml-auto inline-flex items-center gap-1 text-sm font-medium text-blue-700 hover:underline"
            >
                <span class="material-symbols-outlined text-[18px]">download</span>
                Download Template
            </a>
        </div>

        <form
            action="{{ route('admin.tugas.upload') }}"
            method="POST"
            enctype="multipart/form-data"
            class="space-y-5"
        >
            @csrf

            {{-- Jenis Tugas --}}
            <div>
                <p class="mb-2 text-sm font-medium text-slate-700">Jenis Tugas</p>

                <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">

                    <label
                        class="flex cursor-pointer items-center gap-3 rounded-xl border-2 p-4 transition"
                        :class="uploadJenis === 'harian'
                            ? 'border-blue-600 bg-blue-50'
                            : 'border-slate-200 hover:border-slate-300'"
                    >
                        <input type="radio" name="jenis_tugas" value="harian" class="hidden" x-model="uploadJenis">
                        <span class="flex h-10 w-10 items-center justify-center rounded-full bg-blue-100 text-blue-700">
                            <span class="material-symbols-outlined text-[20px]">today</span>
                        </span>
                        <span>
                            <span class="block text-sm font-semibold text-slate-800">Harian</span>
                            <span class="block text-xs text-slate-500">Tugas rutin setiap hari</span>
                        </span>
                    </label>

                    <label
                        class="flex cursor-pointer items-center gap-3 rounded-xl border-2 p-4 transition"
                        :class="uploadJenis === 'mingguan'
                            ? 'border-blue-600 bg-blue-50'
                            : 'border-slate-200 hover:border-slate-300'"
                    >
                        <input type="radio" name="jenis_tugas" value="mingguan" class="hidden" x-model="uploadJenis">
                        <span class="flex h-10 w-10 items-center justify-center rounded-full bg-amber-100 text-amber-700">
                            <span class="material-symbols-outlined text-[20px]">calendar_view_week</span>
                        </span>
                        <span>
                            <span class="block text-sm font-semibold text-slate-800">Mingguan</span>
                            <span class="block text-xs text-slate-500">Tugas per minggu ke-</span>
                        </span>
                    </label>

                    <label
                        class="flex cursor-pointer items-center gap-3 rounded-xl border-2 p-4 transition"
                        :class="uploadJenis === 'akhir'
                            ? 'border-blue-600 bg-blue-50'
                            : 'border-slate-200 hover:border-slate-300'"
                    >
                        <input type="radio" name="jenis_tugas" value="akhir" class="hidden" x-model="uploadJenis">
                        <span class="flex h-10 w-10 items-center justify-center rounded-full bg-purple-100 text-purple-700">
                            <span class="material-symbols-outlined text-[20px]">flag</span>
                        </span>
                        <span>
                            <span class="block text-sm font-semibold text-slate-800">Akhir</span>
                            <span class="block text-xs text-slate-500">Tugas akhir magang</span>
                        </span>
                    </label>

                </div>
            </div>

            {{-- File Excel Dropzone --}}
            <div>
                <p class="mb-2 text-sm font-medium text-slate-700">Pilih File Excel</p>

                <label
                    class="flex min-h-[160px] cursor-pointer flex-col items-center justify-center gap-2 rounded-xl border-2 border-dashed p-6 text-center transition"
                    :class="dragOver ? 'border-blue-500 bg-blue-50' : 'border-slate-300 hover:border-slate-400'"
                    @dragover.prevent="dragOver = true"
                    @dragleave.prevent="dragOver = false"
                    @drop.prevent="dragOver = false; handleFile($event.dataTransfer.files[0])"
                >
                    <span class="flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-slate-500">
                        <span class="material-symbols-outlined text-[24px]">upload</span>
                    </span>

                    <template x-if="!fileName">
                        <div>
                            <p class="text-sm font-medium text-blue-700">Klik untuk mengunggah atau drag &amp; drop</p>
                            <p class="mt-1 text-xs text-slate-400">Pastikan file dalam format .xlsx (Max. 10MB)</p>
                        </div>
                    </template>

                    <template x-if="fileName">
                        <p class="text-sm font-medium text-slate-700" x-text="fileName"></p>
                    </template>

                    <span class="mt-1 rounded-full bg-slate-100 px-3 py-1 text-[11px] text-slate-500">
                        Hanya file .xlsx yang didukung
                    </span>

                    <input
                        type="file"
                        name="file_template"
                        accept=".xlsx"
                        class="hidden"
                        @change="handleFile($event.target.files[0])"
                    >
                </label>
            </div>

            <div class="flex flex-col gap-3 sm:flex-row">
                <button
                    type="submit"
                    class="inline-flex flex-1 items-center justify-center gap-2 rounded-xl bg-blue-900 px-4 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-800"
                >
                    <span class="material-symbols-outlined text-[18px]">cloud_upload</span>
                    Simpan &amp; Publikasikan Tugas
                </button>

                <a
                    href="{{ route('admin.tugas.index') }}"
                    class="inline-flex items-center justify-center rounded-xl border border-slate-300 px-4 py-3 text-sm font-semibold text-slate-600 transition hover:bg-slate-50"
                >
                    Batal
                </a>
            </div>
        </form>

        <div class="mt-5 flex items-start gap-2 rounded-xl border border-blue-100 bg-blue-50 p-4">
            <span class="material-symbols-outlined mt-0.5 text-[18px] text-blue-600">info</span>
            <p class="text-xs text-blue-700">
                <span class="font-semibold">Informasi Penting:</span>
                Mengunggah file baru akan menimpa (overwrite) daftar tugas yang ada untuk jenis tugas yang dipilih.
                Pastikan Anda telah mengunduh data terakhir sebelum melakukan pembaruan massal.
            </p>
        </div>
    </div>

    {{-- ================= DAFTAR TUGAS ================= --}}
    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">

        <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
            <h2 class="text-base font-semibold text-slate-800">Daftar Tugas</h2>

            <div class="relative">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[18px] text-slate-400">search</span>
                <input
                    type="text"
                    x-model="query"
                    placeholder="Cari judul tugas..."
                    class="w-64 rounded-lg border border-slate-200 py-2 pl-9 pr-3 text-sm focus:border-blue-500 focus:outline-none"
                >
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="border-b border-slate-200 text-xs uppercase text-slate-500">
                        <th class="py-3 pr-4">Judul Tugas</th>
                        <th class="py-3 pr-4">Jenis Tugas</th>
                        <th class="py-3 pr-4">Minggu Ke</th>
                        <th class="py-3 pr-4">Batas Pengumpulan</th>
                        <th class="py-3 pr-4">Status</th>
                        <th class="py-3 pr-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tugasList as $tugas)
                        <tr
                            x-show="matches(@js($tugas->judul))"
                            class="border-b border-slate-100 last:border-0"
                        >
                            <td class="py-3 pr-4 font-medium text-slate-800">{{ $tugas->judul }}</td>
                            <td class="py-3 pr-4">
                                @php
                                    $jenisBadge = [
                                        'harian'   => 'bg-blue-100 text-blue-700',
                                        'mingguan' => 'bg-amber-100 text-amber-700',
                                        'akhir'    => 'bg-purple-100 text-purple-700',
                                    ][$tugas->jenis_tugas] ?? 'bg-slate-100 text-slate-600';
                                @endphp
                                <span class="rounded-full px-2.5 py-1 text-xs font-medium {{ $jenisBadge }}">
                                    {{ ucfirst($tugas->jenis_tugas) }}
                                </span>
                            </td>
                            <td class="py-3 pr-4 text-slate-600">
                                {{ $tugas->minggu_ke ?? '-' }}
                            </td>
                            <td class="py-3 pr-4 text-slate-600">
                                {{ optional($tugas->pengumpulan)->format('d M Y, H:i') ?? '-' }}
                            </td>
                            <td class="py-3 pr-4">
                                @php
                                    $statusBadge = [
                                        'aktif'    => 'bg-green-100 text-green-700',
                                        'nonaktif' => 'bg-slate-100 text-slate-600',
                                        'selesai'  => 'bg-blue-100 text-blue-700',
                                    ][$tugas->status] ?? 'bg-slate-100 text-slate-600';
                                @endphp
                                <span class="rounded-full px-2.5 py-1 text-xs font-medium {{ $statusBadge }}">
                                    {{ ucfirst($tugas->status) }}
                                </span>
                            </td>
                            <td class="py-3 pr-4">
                                <div class="flex items-center justify-end gap-1">
                                    <button
                                        type="button"
                                        title="Lihat Detail"
                                        @click="openShow(@js($tugas))"
                                        class="rounded-lg p-2 text-slate-500 hover:bg-slate-100"
                                    >
                                        <span class="material-symbols-outlined text-[18px]">visibility</span>
                                    </button>

                                    <button
                                        type="button"
                                        title="Edit Tugas"
                                        @click="openEdit(@js($tugas))"
                                        class="rounded-lg p-2 text-blue-600 hover:bg-blue-50"
                                    >
                                        <span class="material-symbols-outlined text-[18px]">edit</span>
                                    </button>

                                    <form
                                        action="{{ route('admin.tugas.destroy', $tugas->id_tugas) }}"
                                        method="POST"
                                        onsubmit="return confirm('Yakin ingin menghapus tugas ini?');"
                                    >
                                        @csrf
                                        @method('DELETE')
                                        <button
                                            type="submit"
                                            title="Hapus Tugas"
                                            class="rounded-lg p-2 text-red-500 hover:bg-red-50"
                                        >
                                            <span class="material-symbols-outlined text-[18px]">delete</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-sm italic text-slate-400">
                                Belum ada tugas yang ditambahkan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(method_exists($tugasList, 'links'))
            <div class="mt-4">
                {{ $tugasList->links() }}
            </div>
        @endif
    </div>

    {{-- ================= MODAL: CREATE ================= --}}
    <div
        x-show="createOpen"
        x-cloak
        x-transition.opacity
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
    >
        <div
            @click.outside="createOpen = false"
            class="w-full max-w-lg rounded-2xl bg-white p-6 shadow-xl"
        >
            <div class="mb-4 flex items-center justify-between">
                <h3 class="text-base font-semibold text-slate-800">Tambah Tugas Baru</h3>
                <button type="button" @click="createOpen = false" class="text-slate-400 hover:text-slate-600">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <form action="{{ route('admin.tugas.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf

                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Judul Tugas</label>
                    <input type="text" name="judul" required
                        class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none">
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Materi</label>
                    <textarea name="materi" rows="3"
                        class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none"></textarea>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">Jenis Tugas</label>
                        <select name="jenis_tugas" x-model="createJenis" required
                            class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none">
                            <option value="harian">Harian</option>
                            <option value="mingguan">Mingguan</option>
                            <option value="akhir">Akhir</option>
                        </select>
                    </div>
                    <div x-show="createJenis === 'mingguan'">
                        <label class="mb-1 block text-sm font-medium text-slate-700">Minggu Ke</label>
                        <input type="number" name="minggu_ke" min="1"
                            class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none">
                    </div>
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Batas Pengumpulan</label>
                    <input type="datetime-local" name="pengumpulan"
                        class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none">
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">File Tugas (opsional)</label>
                    <input type="file" name="file_tugas"
                        class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none">
                </div>

                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" @click="createOpen = false"
                        class="rounded-xl border border-slate-300 px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50">
                        Batal
                    </button>
                    <button type="submit"
                        class="rounded-xl bg-blue-900 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-800">
                        Simpan Tugas
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ================= MODAL: EDIT ================= --}}
    <div
        x-show="editOpen"
        x-cloak
        x-transition.opacity
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
    >
        <div
            @click.outside="editOpen = false"
            class="w-full max-w-lg rounded-2xl bg-white p-6 shadow-xl"
        >
            <div class="mb-4 flex items-center justify-between">
                <h3 class="text-base font-semibold text-slate-800">Edit Tugas</h3>
                <button type="button" @click="editOpen = false" class="text-slate-400 hover:text-slate-600">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <form
                :action="'{{ url('admin/tugas') }}/' + (selected?.id_tugas ?? '')"
                method="POST"
                enctype="multipart/form-data"
                class="space-y-4"
            >
                @csrf
                @method('PUT')

                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Judul Tugas</label>
                    <input type="text" name="judul" x-model="selected.judul" required
                        class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none">
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Materi</label>
                    <textarea name="materi" rows="3" x-model="selected.materi"
                        class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none"></textarea>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">Jenis Tugas</label>
                        <select name="jenis_tugas" x-model="selected.jenis_tugas" required
                            class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none">
                            <option value="harian">Harian</option>
                            <option value="mingguan">Mingguan</option>
                            <option value="akhir">Akhir</option>
                        </select>
                    </div>
                    <div x-show="selected.jenis_tugas === 'mingguan'">
                        <label class="mb-1 block text-sm font-medium text-slate-700">Minggu Ke</label>
                        <input type="number" name="minggu_ke" min="1" x-model="selected.minggu_ke"
                            class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none">
                    </div>
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Batas Pengumpulan</label>
                    <input type="datetime-local" name="pengumpulan" x-model="selected.pengumpulan"
                        class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none">
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Ganti File Tugas (opsional)</label>
                    <input type="file" name="file_tugas"
                        class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none">
                    <template x-if="selected.file_tugas">
                        <p class="mt-1 text-xs text-slate-400">File saat ini: <span x-text="selected.file_tugas"></span></p>
                    </template>
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Status</label>
                    <select name="status" x-model="selected.status"
                        class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none">
                        <option value="aktif">Aktif</option>
                        <option value="nonaktif">Nonaktif</option>
                        <option value="selesai">Selesai</option>
                    </select>
                </div>

                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" @click="editOpen = false"
                        class="rounded-xl border border-slate-300 px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50">
                        Batal
                    </button>
                    <button type="submit"
                        class="rounded-xl bg-blue-900 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-800">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ================= MODAL: SHOW (DETAIL) ================= --}}
    <div
        x-show="showOpen"
        x-cloak
        x-transition.opacity
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
    >
        <div
            @click.outside="showOpen = false"
            class="w-full max-w-lg rounded-2xl bg-white p-6 shadow-xl"
        >
            <div class="mb-4 flex items-center justify-between">
                <h3 class="text-base font-semibold text-slate-800">Detail Tugas</h3>
                <button type="button" @click="showOpen = false" class="text-slate-400 hover:text-slate-600">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <template x-if="selected">
                <div class="space-y-3 text-sm">
                    <div>
                        <p class="text-xs uppercase text-slate-400">Judul Tugas</p>
                        <p class="font-medium text-slate-800" x-text="selected.judul"></p>
                    </div>
                    <div>
                        <p class="text-xs uppercase text-slate-400">Materi</p>
                        <p class="text-slate-600" x-text="selected.materi || '-'"></p>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <p class="text-xs uppercase text-slate-400">Jenis Tugas</p>
                            <p class="text-slate-700" x-text="selected.jenis_tugas"></p>
                        </div>
                        <div x-show="selected.jenis_tugas === 'mingguan'">
                            <p class="text-xs uppercase text-slate-400">Minggu Ke</p>
                            <p class="text-slate-700" x-text="selected.minggu_ke || '-'"></p>
                        </div>
                    </div>
                    <div>
                        <p class="text-xs uppercase text-slate-400">Batas Pengumpulan</p>
                        <p class="text-slate-700" x-text="selected.pengumpulan || '-'"></p>
                    </div>
                    <div>
                        <p class="text-xs uppercase text-slate-400">File Tugas</p>
                        <p class="text-slate-700" x-text="selected.file_tugas || '-'"></p>
                    </div>
                    <div>
                        <p class="text-xs uppercase text-slate-400">Status</p>
                        <p class="text-slate-700" x-text="selected.status"></p>
                    </div>
                </div>
            </template>

            <div class="mt-5 flex justify-end">
                <button type="button" @click="showOpen = false"
                    class="rounded-xl border border-slate-300 px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50">
                    Tutup
                </button>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    function kelolaTugas() {
        return {
            // upload state
            uploadJenis: 'harian',
            dragOver: false,
            fileName: null,

            // create modal state
            createJenis: 'harian',

            // list & search state
            query: '',

            // modal state
            createOpen: false,
            editOpen: false,
            showOpen: false,
            selected: {},

            init() {},

            matches(text) {
                return String(text ?? '')
                    .toLowerCase()
                    .includes(this.query.toLowerCase());
            },

            handleFile(file) {
                this.fileName = file ? file.name : null;
            },

            openCreate() {
                this.createJenis = 'harian';
                this.createOpen = true;
            },

            openEdit(tugas) {
                this.selected = { ...tugas };
                this.editOpen = true;
            },

            openShow(tugas) {
                this.selected = { ...tugas };
                this.showOpen = true;
            },
        }
    }
</script>
@endpush