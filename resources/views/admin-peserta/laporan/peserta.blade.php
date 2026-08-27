@extends('layouts.portal')
@section('content')

<div
    class="max-w-7xl mx-auto p-6 space-y-6"
    x-data="{
        showAddModal: false,
        showEditModal: false,
        editItem: {},
        baseUrl: '{{ url('admin-peserta/laporan-peserta') }}'
    }">

    @if(session('success'))
        <div class="bg-green-100 border border-green-300 text-green-700 px-4 py-3 rounded">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-100 border border-red-300 text-red-700 px-4 py-3 rounded">
            <ul class="list-disc list-inside text-sm">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Laporan Peserta Magang</h1>
            <p class="text-gray-500 mt-1">Analisis data peserta magang berdasarkan instansi dan periode.</p>
        </div>
        <button
            @click="showAddModal = true"
            class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg transition">
            + Tambah Peserta
        </button>
    </div>

    <div class="bg-white shadow rounded-xl p-5">
        <form method="GET" class="flex flex-wrap gap-3 items-center">
            <input
                type="text"
                name="search"
                value="{{ $search }}"
                placeholder="Cari nama peserta / sekolah / jurusan..."
                class="flex-1 min-w-[220px] border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">

            <select name="status_filter" class="border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                <option value="overall" @selected($statusFilter === 'overall')>Overall</option>
                <option value="active" @selected($statusFilter === 'active')>Active</option>
                <option value="non-active" @selected($statusFilter === 'non-active')>Non-Active</option>
            </select>

            <select name="year" class="border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                <option value="0" @selected((int) $year === 0)>Semua Tahun</option>
                @foreach($availableYears as $th)
                    <option value="{{ $th }}" @selected((int) $year === (int) $th)>{{ $th }}</option>
                @endforeach
            </select>

            <button class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg transition">
                Terapkan
            </button>
        </form>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b bg-gray-50">
            <h4 class="text-lg font-semibold text-gray-800">Daftar Rincian Peserta</h4>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b">
                    <tr class="text-sm text-gray-600">
                        <th class="px-6 py-4 text-left">Nama</th>
                        <th class="px-6 py-4 text-left">Institusi</th>
                        <th class="px-6 py-4 text-left">Periode</th>
                        <th class="px-6 py-4 text-center">Status</th>
                        <th class="px-6 py-4 text-center">Durasi</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-200">
                @forelse($peserta as $item)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="font-semibold text-gray-800">{{ $item->user->nama ?? '-' }}</div>
                            <div class="text-sm text-gray-500">{{ $item->user->email ?? '-' }}</div>
                        </td>

                        <td class="px-6 py-4">
                            <div class="font-medium text-gray-800">{{ $item->permintaan->nama_sekolah ?? '-' }}</div>
                            <div class="text-sm text-gray-500">{{ $item->permintaan->jurusan ?? '-' }}</div>
                        </td>

                        <td class="px-6 py-4 text-gray-700">
                            {{ $item->tgl_mulai ? $item->tgl_mulai->format('d M Y') : '-' }}
                            <br>
                            <small class="text-gray-500">s/d {{ $item->tgl_selesai ? $item->tgl_selesai->format('d M Y') : '-' }}</small>
                        </td>

                        <td class="px-6 py-4 text-center">
                            @if($item->status == 'aktif')
                                <span class="px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">Aktif</span>
                            @elseif($item->status == 'selesai')
                                <span class="px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">Selesai</span>
                            @else
                                <span class="px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">Dibatalkan</span>
                            @endif
                        </td>

                        <td class="px-6 py-4 text-center text-gray-700">
                            {{ $item->durasi_magang ?? '-' }}
                        </td>

                        <td class="px-6 py-4">
                            <div class="flex justify-center gap-2">
                                @php
                                    $editPayload = [
                                        'id' => $item->id_peserta,
                                        'user_id' => $item->user_id,
                                        'permintaan_id' => $item->permintaan_id,
                                        'alamat' => $item->alamat,
                                        'tingkat_pendidikan' => $item->tingkat_pendidikan,
                                        'kelas' => $item->kelas,
                                        'tgl_mulai' => optional($item->tgl_mulai)->format('Y-m-d'),
                                        'tgl_selesai' => optional($item->tgl_selesai)->format('Y-m-d'),
                                        'durasi_magang' => $item->durasi_magang,
                                        'nama_guru' => $item->nama_guru,
                                        'no_hpguru' => $item->no_hpguru,
                                        'status' => $item->status,
                                    ];
                                @endphp
                                <button
                                    type="button"
                                    data-edit='{{ e(json_encode($editPayload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)) }}'
                                    @click="showEditModal = true; editItem = JSON.parse($event.currentTarget.dataset.edit)"
                                    class="px-3 py-1 bg-yellow-500 text-white rounded hover:bg-yellow-600 text-sm transition">
                                    Edit
                                </button>

                                <form action="{{ route('admin-peserta.laporan-peserta.destroy', $item->id_peserta) }}"
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

        <div class="px-6 py-4 border-t bg-gray-50">
            {{ $peserta->links() }}
        </div>
    </div>

    {{-- ================= MODAL TAMBAH ================= --}}
    <div
        x-show="showAddModal"
        x-cloak
        x-transition.opacity
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
        @keydown.escape.window="showAddModal = false"
    >
        <div
            @click.outside="showAddModal = false"
            x-show="showAddModal"
            x-transition
            class="w-full max-w-2xl max-h-[90vh] overflow-y-auto rounded-xl bg-white shadow-xl"
        >
            <form method="POST" :action="baseUrl" class="divide-y divide-gray-100">
                @csrf

                <div class="flex items-center justify-between px-6 py-4">
                    <h3 class="text-lg font-bold text-gray-800">Tambah Peserta Magang</h3>
                    <button type="button" @click="showAddModal = false" class="text-gray-400 hover:text-gray-600">&times;</button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 px-6 py-4">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
                        <textarea name="alamat" rows="2" class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tingkat Pendidikan</label>
                        <input type="text" name="tingkat_pendidikan" class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kelas</label>
                        <input type="text" name="kelas" class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai</label>
                        <input type="date" name="tgl_mulai" class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Selesai</label>
                        <input type="date" name="tgl_selesai" class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Durasi Magang (bulan)</label>
                        <input type="number" name="durasi_magang" class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Guru Pembimbing</label>
                        <input type="text" name="nama_guru" class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">No. HP Guru</label>
                        <input type="text" name="no_hpguru" class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select name="status" class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                            <option value="aktif">Aktif</option>
                            <option value="selesai">Selesai</option>
                            <option value="dibatalkan">Dibatalkan</option>
                        </select>
                    </div>
                </div>

                <div class="flex justify-end gap-2 px-6 py-4">
                    <button type="button" @click="showAddModal = false" class="px-4 py-2 rounded-lg border text-gray-600 hover:bg-gray-50">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ================= MODAL EDIT ================= --}}
    <div
        x-show="showEditModal"
        x-cloak
        x-transition.opacity
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
        @keydown.escape.window="showEditModal = false"
    >
        <div
            @click.outside="showEditModal = false"
            x-show="showEditModal"
            x-transition
            class="w-full max-w-2xl max-h-[90vh] overflow-y-auto rounded-xl bg-white shadow-xl"
        >
            <form method="POST" :action="baseUrl + '/' + editItem.id" class="divide-y divide-gray-100">
                @csrf
                @method('PUT')

                <div class="flex items-center justify-between px-6 py-4">
                    <h3 class="text-lg font-bold text-gray-800">Edit Peserta Magang</h3>
                    <button type="button" @click="showEditModal = false" class="text-gray-400 hover:text-gray-600">&times;</button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 px-6 py-4">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
                        <textarea name="alamat" rows="2" x-model="editItem.alamat" class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tingkat Pendidikan</label>
                        <input type="text" name="tingkat_pendidikan" x-model="editItem.tingkat_pendidikan" class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kelas</label>
                        <input type="text" name="kelas" x-model="editItem.kelas" class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai</label>
                        <input type="date" name="tgl_mulai" x-model="editItem.tgl_mulai" class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Selesai</label>
                        <input type="date" name="tgl_selesai" x-model="editItem.tgl_selesai" class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Durasi Magang (bulan)</label>
                        <input type="number" name="durasi_magang" x-model="editItem.durasi_magang" class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Guru Pembimbing</label>
                        <input type="text" name="nama_guru" x-model="editItem.nama_guru" class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">No. HP Guru</label>
                        <input type="text" name="no_hpguru" x-model="editItem.no_hpguru" class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select name="status" x-model="editItem.status" class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                            <option value="aktif">Aktif</option>
                            <option value="selesai">Selesai</option>
                            <option value="dibatalkan">Dibatalkan</option>
                        </select>
                    </div>
                </div>

                <div class="flex justify-end gap-2 px-6 py-4">
                    <button type="button" @click="showEditModal = false" class="px-4 py-2 rounded-lg border text-gray-600 hover:bg-gray-50">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700">
                        Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection