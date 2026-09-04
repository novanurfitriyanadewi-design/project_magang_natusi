@extends('layouts.portal')

@section('content')
<div class="p-6">

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-800">
            Tambah Pengumuman
        </h1>

        <p class="mt-1 text-sm text-slate-500">
            Buat pengumuman baru untuk karyawan.
        </p>
    </div>

    @if ($errors->any())
        <div class="mb-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-600">
            <ul class="list-inside list-disc">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form
        action="{{ route('admin-karyawan.pengumuman.store') }}"
        method="POST"
        class="max-w-4xl rounded-2xl border border-slate-200 bg-white p-6 shadow-sm"
    >
        @csrf

        <div class="space-y-5">

            {{-- JUDUL --}}
            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-700">
                    Judul Pengumuman
                </label>

                <input
                    type="text"
                    name="judul"
                    value="{{ old('judul') }}"
                    placeholder="Masukkan judul pengumuman"
                    class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-[#05658f] focus:ring-2 focus:ring-[#05658f]/10"
                    required
                >
            </div>

            {{-- KATEGORI --}}
            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-700">
                    Kategori
                </label>

                <select
                    name="kategori"
                    class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-[#05658f]"
                    required
                >
                    <option value="">Pilih Kategori</option>

                    <option value="umum" {{ old('kategori') === 'umum' ? 'selected' : '' }}>
                        Umum
                    </option>

                    <option value="penting" {{ old('kategori') === 'penting' ? 'selected' : '' }}>
                        Penting
                    </option>

                    <option value="acara" {{ old('kategori') === 'acara' ? 'selected' : '' }}>
                        Acara
                    </option>
                </select>
            </div>

            {{-- TARGET PENERIMA --}}
            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-700">
                    Penerima Pengumuman
                </label>

                <select
                    name="target"
                    id="target"
                    onchange="togglePenerima()"
                    class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-[#05658f]"
                    required
                >
                    <option value="umum" {{ old('target', 'umum') === 'umum' ? 'selected' : '' }}>
                        Semua Karyawan
                    </option>

                    <option value="individu" {{ old('target') === 'individu' ? 'selected' : '' }}>
                        Karyawan Tertentu
                    </option>
                </select>
            </div>

            {{-- PILIH KARYAWAN --}}
            <div
                id="pilihan-karyawan"
                class="{{ old('target') === 'individu' ? '' : 'hidden' }}"
            >
                <label class="mb-2 block text-sm font-semibold text-slate-700">
                    Pilih Karyawan
                </label>

                <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">

                    @if(isset($karyawan) && $karyawan->count())
                        <div class="max-h-64 space-y-2 overflow-y-auto">

                            @foreach ($karyawan as $item)
                                <label
                                    class="flex cursor-pointer items-center gap-3 rounded-lg p-3 hover:bg-white"
                                >
                                    <input
                                        type="checkbox"
                                        name="karyawan_id[]"
                                        value="{{ $item->id_karyawan }}"
                                        {{ in_array(
                                            $item->id_karyawan,
                                            old('karyawan_id', [])
                                        ) ? 'checked' : '' }}
                                        class="h-4 w-4 rounded border-slate-300 text-[#05658f] focus:ring-[#05658f]"
                                    >

                                    <div>
                                        <p class="text-sm font-semibold text-slate-700">
                                            {{ $item->nama_karyawan }}
                                        </p>

                                        @if($item->nip)
                                            <p class="text-xs text-slate-500">
                                                NIP: {{ $item->nip }}
                                            </p>
                                        @endif
                                    </div>
                                </label>
                            @endforeach

                        </div>
                    @else
                        <p class="text-sm text-slate-500">
                            Belum ada karyawan aktif.
                        </p>
                    @endif

                </div>

                <p class="mt-2 text-xs text-slate-500">
                    Pilih satu atau beberapa karyawan yang akan menerima pengumuman.
                </p>
            </div>

            {{-- ISI --}}
            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-700">
                    Isi Pengumuman
                </label>

                <textarea
                    name="isi"
                    rows="8"
                    placeholder="Tuliskan isi pengumuman..."
                    class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-[#05658f] focus:ring-2 focus:ring-[#05658f]/10"
                    required
                >{{ old('isi') }}</textarea>
            </div>

            {{-- AKTIF --}}
            <label class="flex cursor-pointer items-center gap-3">
                <input type="hidden" name="aktif" value="0">
                <input
                    type="checkbox"
                    name="aktif"
                    value="1"
                    {{ old('aktif', true) ? 'checked' : '' }}
                    class="h-4 w-4 rounded border-slate-300"
                >

                <span class="text-sm font-medium text-slate-700">
                    Aktifkan pengumuman
                </span>
            </label>

        </div>

        {{-- BUTTON --}}
        <div class="flex items-center justify-end gap-3 mt-6">
            <!-- Tombol Batal -->
            <a href="{{ route('admin-karyawan.pengumuman.index') }}" 
            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50">
                Batal
            </a>

            <!-- Tombol Simpan (Pastikan type="submit" dan diberi warna kontras) -->
            <button type="submit" 
                    class="px-5 py-2 text-sm font-medium text-white bg-sky-600 rounded-xl hover:bg-sky-700 focus:ring-4 focus:ring-sky-200">
                Simpan
            </button>
        </div>

    </form>

</div>

{{-- JAVASCRIPT --}}
<script>
    function togglePenerima() {
        const target = document.getElementById('target');
        const pilihanKaryawan = document.getElementById('pilihan-karyawan');

        if (target.value === 'individu') {
            pilihanKaryawan.classList.remove('hidden');
        } else {
            pilihanKaryawan.classList.add('hidden');

            // Hapus semua centang jika kembali ke semua karyawan
            document
                .querySelectorAll('input[name="karyawan_id[]"]')
                .forEach(function (checkbox) {
                    checkbox.checked = false;
                });
        }
    }

    // Jalankan saat halaman pertama kali dibuka
    document.addEventListener('DOMContentLoaded', function () {
        togglePenerima();
    });
</script>

@endsection