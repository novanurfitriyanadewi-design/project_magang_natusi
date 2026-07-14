@extends('layouts.portal')

@section('content')

{{-- Pastikan route ini jalan dan modal Alpine.js aktif --}}
<div class="max-w-7xl mx-auto p-6 space-y-6" x-data="{ showAddModal: false }" x-cloak>

    @if(session('success'))
        <div class="bg-green-100 border border-green-300 text-green-700 px-4 py-3 rounded">
            {{ session('success') }}
        </div>
    @endif

    {{-- Header --}}
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">
                Data Peserta Magang
            </h1>
            <p class="text-gray-500 mt-1">
                Kelola seluruh data peserta magang.
            </p>
        </div>

        {{-- Menggunakan Alpine.js untuk membuka modal --}}
        <button
            @click="showAddModal = true"
            class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg transition">
            Tambah Peserta
        </button>
    </div>

    {{-- Statistik --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-5">
        <div class="bg-white shadow rounded-xl p-5 border">
            <h5 class="text-gray-500 text-sm">Total Peserta</h5>
            <h2 class="text-3xl font-bold mt-2">{{ $stats['total'] }}</h2>
        </div>

        <div class="bg-green-50 shadow rounded-xl p-5 border border-green-200">
            <h5 class="text-green-700 text-sm">Peserta Aktif</h5>
            <h2 class="text-3xl font-bold text-green-700 mt-2">{{ $stats['aktif'] }}</h2>
        </div>

        <div class="bg-yellow-50 shadow rounded-xl p-5 border border-yellow-200">
            <h5 class="text-yellow-700 text-sm">Selesai</h5>
            <h2 class="text-3xl font-bold text-yellow-700 mt-2">{{ $stats['selesai'] }}</h2>
        </div>

        <div class="bg-red-50 shadow rounded-xl p-5 border border-red-200">
            <h5 class="text-red-700 text-sm">Dibatalkan</h5>
            <h2 class="text-3xl font-bold text-red-700 mt-2">{{ $stats['dibatalkan'] }}</h2>
        </div>
    </div>

    {{-- Search --}}
    <div class="bg-white shadow rounded-xl p-5">
        <form method="GET">
            <div class="flex gap-3">
                <input
                    type="text"
                    name="search"
                    value="{{ $search }}"
                    placeholder="Cari nama peserta / sekolah / jurusan..."
                    class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                <button class="bg-blue-600 hover:bg-blue-700 text-white px-5 rounded-lg transition">
                    Cari
                </button>
            </div>
        </form>
    </div>

    {{-- Tabel Peserta --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b">
                    <tr class="text-sm text-gray-600">
                        <th class="px-6 py-4 text-left">Nama Peserta</th>
                        <th class="px-6 py-4 text-left">Institusi</th>
                        <th class="px-6 py-4 text-left">Pendidikan</th>
                        <th class="px-6 py-4 text-left">Periode</th>
                        <th class="px-6 py-4 text-center">Status</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-200">
                @forelse($peserta as $item)
                    <tr class="hover:bg-gray-50">
                        {{-- Nama --}}
                        <td class="px-6 py-4">
                            <div class="font-semibold text-gray-800">
                                {{ $item->user->nama ?? '-' }}
                            </div>
                            <div class="text-sm text-gray-500">
                                {{ $item->user->email ?? '-' }}
                            </div>
                        </td>

                        {{-- Institusi --}}
                        <td class="px-6 py-4">
                            <div class="font-medium text-gray-800">
                                {{ $item->permintaan->nama_sekolah ?? '-' }}
                            </div>
                            <div class="text-sm text-gray-500">
                                {{ $item->permintaan->jurusan ?? '-' }}
                            </div>
                        </td>

                        {{-- Pendidikan --}}
                        <td class="px-6 py-4 text-gray-700">
                            <div>{{ $item->tingkat_pendidikan }}</div>
                            <small class="text-gray-500">
                                {{ $item->kelas ?? '-' }}
                            </small>
                        </td>

                        {{-- Periode --}}
                        <td class="px-6 py-4 text-gray-700">
                            <div>
                                {{ $item->tgl_mulai ? $item->tgl_mulai->format('d M Y') : '-' }}
                                <br>
                                <small class="text-gray-500">
                                    s/d {{ $item->tgl_selesai ? $item->tgl_selesai->format('d M Y') : '-' }}
                                </small>
                            </div>
                        </td>

                        {{-- Status --}}
                        <td class="px-6 py-4 text-center">
                            @if($item->status == 'aktif')
                                <span class="px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                                    Aktif
                                </span>
                            @elseif($item->status == 'selesai')
                                <span class="px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">
                                    Selesai
                                </span>
                            @else
                                <span class="px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">
                                    Dibatalkan
                                </span>
                            @endif
                        </td>

                        {{-- Tombol Aksi --}}
                        <td class="px-6 py-4">
                            <div class="flex justify-center gap-2">
                                <a href="{{ route('admin.peserta.show', $item->id_peserta) }}"
                                   class="px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600 text-sm transition">
                                    Detail
                                </a>

                                <a href="{{ route('admin.peserta.edit', $item->id_peserta) }}"
                                   class="px-3 py-1 bg-yellow-500 text-white rounded hover:bg-yellow-600 text-sm transition">
                                    Edit
                                </a>

                                <form action="{{ route('admin.peserta.destroy', $item->id_peserta) }}"
                                      method="POST"
                                      onsubmit="return confirm('Hapus peserta ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600 text-sm transition">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-8 text-center text-gray-500">
                            Belum ada data peserta magang.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="px-6 py-4 border-t bg-gray-50">
            {{ $peserta->links() }}
        </div>
    </div>

    {{-- Modal Tambah Peserta --}}
    <div
        x-show="showAddModal"
        x-cloak
        style="display:none"
        class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">

        <div
            @click.outside="showAddModal = false"
            class="bg-white rounded-xl shadow-xl w-full max-w-3xl p-6 overflow-y-auto max-h-[90vh]">

            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold text-gray-800">
                    Tambah Peserta Magang
                </h3>
                <button
                    @click="showAddModal = false"
                    class="text-gray-500 hover:text-red-500 text-2xl font-semibold">
                    &times;
                </button>
            </div>

            <form
                action="{{ route('admin.peserta.store') }}"
                method="POST"
                class="grid grid-cols-2 gap-5">

                @csrf

                {{-- User --}}
                <div>
                    <label class="block text-sm font-semibold mb-2">User</label>
                    <select
                        name="user_id"
                        class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required>
                        <option value="">-- Pilih User --</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id_user }}">
                                {{ $user->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Permintaan --}}
                <div>
                    <label class="block text-sm font-semibold mb-2">Permintaan Magang</label>
                    <select
                        name="permintaan_id"
                        class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Tidak Ada</option>
                        @foreach($permintaan as $p)
                            <option value="{{ $p->id_permintaan }}">
                                {{ $p->nama_pemohon }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Alamat --}}
                <div class="col-span-2">
                    <label class="block text-sm font-semibold mb-2">Alamat</label>
                    <textarea
                        name="alamat"
                        rows="3"
                        class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required></textarea>
                </div>

                {{-- Pendidikan --}}
                <div>
                    <label class="block text-sm font-semibold mb-2">Tingkat Pendidikan</label>
                    <input
                        type="text"
                        name="tingkat_pendidikan"
                        class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="SMK / D3 / S1"
                        required>
                </div>

                {{-- Kelas --}}
                <div>
                    <label class="block text-sm font-semibold mb-2">Kelas</label>
                    <input
                        type="text"
                        name="kelas"
                        class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                {{-- Mulai --}}
                <div>
                    <label class="block text-sm font-semibold mb-2">Tanggal Mulai</label>
                    <input
                        type="date"
                        name="tgl_mulai"
                        class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                {{-- Selesai --}}
                <div>
                    <label class="block text-sm font-semibold mb-2">Tanggal Selesai</label>
                    <input
                        type="date"
                        name="tgl_selesai"
                        class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                {{-- Durasi --}}
                <div>
                    <label class="block text-sm font-semibold mb-2">Durasi Magang</label>
                    <input
                        type="text"
                        name="durasi_magang"
                        placeholder="e.g. 3 Bulan"
                        class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                {{-- Guru --}}
                <div>
                    <label class="block text-sm font-semibold mb-2">Nama Guru/Dosen</label>
                    <input
                        type="text"
                        name="nama_guru"
                        class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                {{-- HP Guru --}}
                <div>
                    <label class="block text-sm font-semibold mb-2">No HP Guru</label>
                    <input
                        type="text"
                        name="no_hpguru"
                        class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                {{-- Status --}}
                <div>
                    <label class="block text-sm font-semibold mb-2">Status</label>
                    <select
                        name="status"
                        class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="aktif">Aktif</option>
                        <option value="selesai">Selesai</option>
                        <option value="dibatalkan">Dibatalkan</option>
                    </select>
                </div>

                {{-- Tombol Submit / Close --}}
                <div class="col-span-2 flex justify-end gap-3 mt-5">
                    <button
                        type="button"
                        @click="showAddModal = false"
                        class="px-5 py-2 rounded-lg border hover:bg-gray-100 transition">
                        Batal
                    </button>
                    <button
                        type="submit"
                        class="px-5 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white transition">
                        Simpan Peserta
                    </button>
                </div>

            </form>
        </div>
    </div>

</div>

@endsection