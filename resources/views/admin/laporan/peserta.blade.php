@extends('layouts.portal')

@section('content')

<div
    class="max-w-7xl mx-auto p-6 space-y-6"
    x-data="{
        showAddModal: false,
        showEditModal: false,
        editItem: {},
        baseUrl: '{{ url('admin/laporan-peserta') }}'
    }"
    x-cloak>

    @if(session('success'))
        <div class="bg-green-100 border border-green-300 text-green-700 px-4 py-3 rounded">
            {{ session('success') }}
        </div>
    @endif

    {{-- Header --}}
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

    {{-- Filter --}}
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
                @foreach($availableYears as $th)
                    <option value="{{ $th }}" @selected((int) $year === (int) $th)>{{ $th }}</option>
                @endforeach
            </select>

            <button class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg transition">
                Terapkan
            </button>
        </form>
    </div>

    {{-- Statistik --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        <div class="bg-white shadow rounded-xl p-5 border border-l-4 border-l-blue-600">
            <h5 class="text-gray-500 text-sm">Total Peserta</h5>
            <h2 class="text-3xl font-bold mt-2">{{ $stats['total'] }}</h2>
        </div>

        <div class="bg-white shadow rounded-xl p-5 border border-l-4 border-l-blue-400">
            <h5 class="text-gray-500 text-sm">Peserta Aktif</h5>
            <h2 class="text-3xl font-bold mt-2 text-blue-700">{{ $stats['aktif'] }}</h2>
        </div>

        <div class="bg-white shadow rounded-xl p-5 border border-l-4 border-l-gray-400">
            <h5 class="text-gray-500 text-sm">Peserta Non-Aktif</h5>
            <h2 class="text-3xl font-bold mt-2 text-gray-700">{{ $stats['nonaktif'] }}</h2>
        </div>
    </div>

    {{-- Grafik Tren --}}
    <div class="bg-white p-6 rounded-xl shadow border">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
            <div>
                <h4 class="text-lg font-semibold text-gray-800">Tren Peserta Magang per Bulan (Universitas vs SMK)</h4>
                <p class="text-gray-500 text-sm">Perbandingan jumlah peserta berdasarkan kategori institusi tahun {{ $year }}</p>
            </div>
            <div class="flex items-center gap-4">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-blue-600 inline-block"></span>
                    <span class="text-xs text-gray-500">Universitas</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-blue-200 inline-block"></span>
                    <span class="text-xs text-gray-500">SMK</span>
                </div>
            </div>
        </div>

        <div class="h-56 w-full relative mb-6">
            <svg class="w-full h-full" viewBox="0 0 1200 220" preserveAspectRatio="none">
                <line x1="0" y1="0" x2="1200" y2="0" stroke="#e5e7eb" stroke-width="1" stroke-dasharray="4"></line>
                <line x1="0" y1="55" x2="1200" y2="55" stroke="#e5e7eb" stroke-width="1" stroke-dasharray="4"></line>
                <line x1="0" y1="110" x2="1200" y2="110" stroke="#e5e7eb" stroke-width="1" stroke-dasharray="4"></line>
                <line x1="0" y1="165" x2="1200" y2="165" stroke="#e5e7eb" stroke-width="1" stroke-dasharray="4"></line>

                <polyline
                    fill="none"
                    stroke="#93c5fd"
                    stroke-width="3"
                    points="@foreach($pointsSmk as $p){{ $p['x'] }},{{ $p['y'] }} @endforeach">
                </polyline>

                <polyline
                    fill="none"
                    stroke="#2563eb"
                    stroke-width="3"
                    points="@foreach($pointsUniversitas as $p){{ $p['x'] }},{{ $p['y'] }} @endforeach">
                </polyline>

                @foreach($pointsUniversitas as $p)
                    <circle cx="{{ $p['x'] }}" cy="{{ $p['y'] }}" r="4" fill="#2563eb"></circle>
                @endforeach

                @foreach($pointsSmk as $p)
                    <circle cx="{{ $p['x'] }}" cy="{{ $p['y'] }}" r="4" fill="#93c5fd"></circle>
                @endforeach
            </svg>

            <div class="absolute bottom-0 left-0 w-full flex justify-between px-1 transform translate-y-6 text-[10px] text-gray-400">
                <span>Jan</span><span>Feb</span><span>Mar</span><span>Apr</span>
                <span>May</span><span>Jun</span><span>Jul</span><span>Aug</span>
                <span>Sep</span><span>Oct</span><span>Nov</span><span>Dec</span>
            </div>
        </div>
    </div>

    {{-- Tabel Rincian --}}
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
                                <button
                                    @click="showEditModal = true; editItem = {
                                        id: {{ $item->id_peserta }},
                                        user_id: {{ $item->user_id }},
                                        permintaan_id: {{ $item->permintaan_id ?? 'null' }},
                                        alamat: @js($item->alamat),
                                        tingkat_pendidikan: @js($item->tingkat_pendidikan),
                                        kelas: @js($item->kelas),
                                        tgl_mulai: @js(optional($item->tgl_mulai)->format('Y-m-d')),
                                        tgl_selesai: @js(optional($item->tgl_selesai)->format('Y-m-d')),
                                        durasi_magang: @js($item->durasi_magang),
                                        nama_guru: @js($item->nama_guru),
                                        no_hpguru: @js($item->no_hpguru),
                                        status: @js($item->status)
                                    }"
                                    class="px-3 py-1 bg-yellow-500 text-white rounded hover:bg-yellow-600 text-sm transition">
                                    Edit
                                </button>

                                <form action="{{ route('admin.laporan-peserta.destroy', $item->id_peserta) }}"
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
                <h3 class="text-xl font-bold text-gray-800">Tambah Peserta Magang</h3>
                <button @click="showAddModal = false" class="text-gray-500 hover:text-red-500 text-2xl font-semibold">&times;</button>
            </div>

            <form action="{{ route('admin.laporan-peserta.store') }}" method="POST" class="grid grid-cols-2 gap-5">
                @csrf

                <div>
                    <label class="block text-sm font-semibold mb-2">User</label>
                    <select name="user_id" class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        <option value="">-- Pilih User --</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id_user }}">{{ $user->nama }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold mb-2">Permintaan Magang</label>
                    <select name="permintaan_id" class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Tidak Ada</option>
                        @foreach($permintaan as $p)
                            <option value="{{ $p->id_permintaan }}">{{ $p->nama_pemohon }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-span-2">
                    <label class="block text-sm font-semibold mb-2">Alamat</label>
                    <textarea name="alamat" rows="3" class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required></textarea>
                </div>

                <div>
                    <label class="block text-sm font-semibold mb-2">Tingkat Pendidikan</label>
                    <input type="text" name="tingkat_pendidikan" class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="SMK / D3 / S1" required>
                </div>

                <div>
                    <label class="block text-sm font-semibold mb-2">Kelas</label>
                    <input type="text" name="kelas" class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-semibold mb-2">Tanggal Mulai</label>
                    <input type="date" name="tgl_mulai" class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-semibold mb-2">Tanggal Selesai</label>
                    <input type="date" name="tgl_selesai" class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-semibold mb-2">Durasi Magang</label>
                    <input type="text" name="durasi_magang" placeholder="e.g. 3 Bulan" class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-semibold mb-2">Nama Guru/Dosen</label>
                    <input type="text" name="nama_guru" class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-semibold mb-2">No HP Guru</label>
                    <input type="text" name="no_hpguru" class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-semibold mb-2">Status</label>
                    <select name="status" class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="aktif">Aktif</option>
                        <option value="selesai">Selesai</option>
                        <option value="dibatalkan">Dibatalkan</option>
                    </select>
                </div>

                <div class="col-span-2 flex justify-end gap-3 mt-5">
                    <button type="button" @click="showAddModal = false" class="px-5 py-2 rounded-lg border hover:bg-gray-100 transition">Batal</button>
                    <button type="submit" class="px-5 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white transition">Simpan Peserta</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Edit Peserta --}}
    <div
        x-show="showEditModal"
        x-cloak
        style="display:none"
        class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">

        <div
            @click.outside="showEditModal = false"
            class="bg-white rounded-xl shadow-xl w-full max-w-3xl p-6 overflow-y-auto max-h-[90vh]">

            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold text-gray-800">Edit Peserta Magang</h3>
                <button @click="showEditModal = false" class="text-gray-500 hover:text-red-500 text-2xl font-semibold">&times;</button>
            </div>

            <form :action="baseUrl + '/' + editItem.id" method="POST" class="grid grid-cols-2 gap-5">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-sm font-semibold mb-2">User</label>
                    <select name="user_id" x-model="editItem.user_id" class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        <option value="">-- Pilih User --</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id_user }}">{{ $user->nama }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold mb-2">Permintaan Magang</label>
                    <select name="permintaan_id" x-model="editItem.permintaan_id" class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Tidak Ada</option>
                        @foreach($permintaan as $p)
                            <option value="{{ $p->id_permintaan }}">{{ $p->nama_pemohon }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-span-2">
                    <label class="block text-sm font-semibold mb-2">Alamat</label>
                    <textarea name="alamat" x-model="editItem.alamat" rows="3" class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required></textarea>
                </div>

                <div>
                    <label class="block text-sm font-semibold mb-2">Tingkat Pendidikan</label>
                    <input type="text" name="tingkat_pendidikan" x-model="editItem.tingkat_pendidikan" class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>

                <div>
                    <label class="block text-sm font-semibold mb-2">Kelas</label>
                    <input type="text" name="kelas" x-model="editItem.kelas" class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-semibold mb-2">Tanggal Mulai</label>
                    <input type="date" name="tgl_mulai" x-model="editItem.tgl_mulai" class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-semibold mb-2">Tanggal Selesai</label>
                    <input type="date" name="tgl_selesai" x-model="editItem.tgl_selesai" class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-semibold mb-2">Durasi Magang</label>
                    <input type="text" name="durasi_magang" x-model="editItem.durasi_magang" class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-semibold mb-2">Nama Guru/Dosen</label>
                    <input type="text" name="nama_guru" x-model="editItem.nama_guru" class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-semibold mb-2">No HP Guru</label>
                    <input type="text" name="no_hpguru" x-model="editItem.no_hpguru" class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-semibold mb-2">Status</label>
                    <select name="status" x-model="editItem.status" class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="aktif">Aktif</option>
                        <option value="selesai">Selesai</option>
                        <option value="dibatalkan">Dibatalkan</option>
                    </select>
                </div>

                <div class="col-span-2 flex justify-end gap-3 mt-5">
                    <button type="button" @click="showEditModal = false" class="px-5 py-2 rounded-lg border hover:bg-gray-100 transition">Batal</button>
                    <button type="submit" class="px-5 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white transition">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

</div>

@endsection