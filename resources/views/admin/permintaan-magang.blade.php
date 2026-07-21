@extends('layouts.portal')

@section('content')
<div class="p-8 max-w-7xl mx-auto w-full">
    <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-3xl font-extrabold text-gray-900 tracking-tight">Permintaan Magang</h2>
            <p class="text-sm font-medium text-gray-500 mt-1">Kelola dan tinjau seluruh pengajuan pendaftaran magang di CV Natusi.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-800 rounded-xl flex items-center gap-2 text-sm font-bold shadow-sm">
            <span class="material-symbols-outlined text-green-600">check_circle</span>
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-800 rounded-xl flex items-center gap-2 text-sm font-bold shadow-sm">
            <span class="material-symbols-outlined text-red-600">error</span>
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white p-6 rounded-2xl border border-gray-200/80 shadow-sm flex justify-between items-center">
            <div>
                <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Total Pendaftar</p>
                <h3 class="text-3xl font-black text-gray-900">{{ number_format($total_pendaftar ?? 0) }}</h3>
            </div>
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                <span class="material-symbols-outlined text-[28px]">group</span>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl border border-gray-200/80 shadow-sm flex justify-between items-center">
            <div>
                <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Belum Di-acc</p>
                <h3 class="text-3xl font-black text-amber-600">{{ number_format($total_menunggu ?? 0) }}</h3>
            </div>
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                <span class="material-symbols-outlined text-[28px]">pending_actions</span>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl border border-gray-200/80 shadow-sm flex justify-between items-center">
            <div>
                <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Sudah Di-acc</p>
                <h3 class="text-3xl font-black text-green-600">{{ number_format($total_diterima ?? 0) }}</h3>
            </div>
            <div class="w-12 h-12 rounded-xl bg-green-50 text-green-600 flex items-center justify-center">
                <span class="material-symbols-outlined text-[28px]">verified</span>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-gray-200/80 shadow-sm overflow-hidden">
        
        <div class="p-4 bg-gray-50/50 border-b border-gray-200 flex flex-col md:flex-row items-center justify-between gap-4">
            <form id="filterForm" method="GET" action="{{ url()->current() }}" class="flex flex-wrap items-center gap-2 w-full md:w-auto">
                @php 
                    $currentStatus = request('status', 'all'); 
                    $searchQuery   = request('search', '');
                @endphp
                <input type="hidden" name="status" id="statusInput" value="{{ $currentStatus }}">

                <button type="button" onclick="submitFilter('all')" class="px-4 py-2 rounded-xl text-xs font-bold transition-all {{ $currentStatus == 'all' ? 'bg-blue-600 text-white shadow-sm' : 'bg-white text-gray-600 border border-gray-200 hover:bg-gray-100' }}">Semua</button>
                <button type="button" onclick="submitFilter('menunggu')" class="px-4 py-2 rounded-xl text-xs font-bold transition-all {{ $currentStatus == 'menunggu' ? 'bg-blue-600 text-white shadow-sm' : 'bg-white text-gray-600 border border-gray-200 hover:bg-gray-100' }}">Menunggu</button>
                <button type="button" onclick="submitFilter('diterima')" class="px-4 py-2 rounded-xl text-xs font-bold transition-all {{ $currentStatus == 'diterima' ? 'bg-blue-600 text-white shadow-sm' : 'bg-white text-gray-600 border border-gray-200 hover:bg-gray-100' }}">Diterima</button>
                <button type="button" onclick="submitFilter('ditolak')" class="px-4 py-2 rounded-xl text-xs font-bold transition-all {{ $currentStatus == 'ditolak' ? 'bg-blue-600 text-white shadow-sm' : 'bg-white text-gray-600 border border-gray-200 hover:bg-gray-100' }}">Ditolak</button>
            </form>

            <form method="GET" action="{{ url()->current() }}" class="w-full md:w-72 flex items-center">
                <input type="hidden" name="status" value="{{ $currentStatus }}">
                <div class="relative w-full">
                    <span class="material-symbols-outlined absolute left-3 top-2.5 text-gray-400 text-[20px]">search</span>
                    <input type="text" name="search" value="{{ $searchQuery }}" placeholder="Cari pendaftar/institusi..." class="w-full pl-10 pr-4 py-2 bg-white border border-gray-200 rounded-xl text-xs font-medium focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition-all">
                </div>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/80 text-gray-500 font-bold text-xs uppercase tracking-wider border-b border-gray-200">
                        <th class="px-6 py-4">Nama Pendaftar</th>
                        <th class="px-6 py-4">Email</th>
                        <th class="px-6 py-4">Institusi</th>
                        <th class="px-6 py-4">Tanggal Submit</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 font-medium text-sm text-gray-700">
                    @forelse($permintaan_magang as $item)
                        <tr class="hover:bg-gray-50/80 transition-colors">
                            <td class="px-6 py-4 font-bold text-gray-900">{{ $item->nama }}</td>
                            <td class="px-6 py-4 text-gray-600">{{ $item->email }}</td>
                            <td class="px-6 py-4 text-gray-600">{{ $item->institusi }}</td>
                            <td class="px-6 py-4 text-gray-500 text-xs">
                                {{ isset($item->created_at) ? date('d M Y', strtotime($item->created_at)) : '-' }}
                            </td>
                            <td class="px-6 py-4">
                                @if(($item->status ?? 'menunggu') === 'menunggu')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-50 border border-amber-200 text-amber-700 text-xs font-bold">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Menunggu
                                    </span>
                                @elseif(($item->status) === 'diterima')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-green-50 border border-green-200 text-green-700 text-xs font-bold">
                                        <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> Diterima
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-red-50 border border-red-200 text-red-700 text-xs font-bold">
                                        <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Ditolak
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <button type="button" 
                                        onclick="openDetailModal('{{ json_encode($item) }}')" 
                                        class="inline-flex items-center gap-1 text-blue-600 font-bold hover:underline">
                                    <span class="material-symbols-outlined text-[18px]">visibility</span> Detail
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-400 font-bold">
                                Tidak ada data permintaan magang yang ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-gray-200 bg-gray-50/30">
            @if(method_exists($permintaan_magang, 'links'))
                {{ $permintaan_magang->appends(request()->query())->links() }}
            @endif
        </div>
    </div>
</div>

<div id="detailModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-lg w-full shadow-2xl overflow-hidden transform transition-all">
        <div class="p-6 border-b border-gray-200 flex justify-between items-center bg-gray-50">
            <h3 class="text-lg font-black text-gray-900">Detail Permintaan Magang</h3>
            <button type="button" onclick="closeDetailModal()" class="text-gray-400 hover:text-gray-600">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        
        <div class="p-6 space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase">Nama Pendaftar</p>
                    <p id="modalNama" class="text-sm font-extrabold text-gray-900 mt-0.5">-</p>
                </div>
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase">Email</p>
                    <p id="modalEmail" class="text-sm font-bold text-gray-700 mt-0.5">-</p>
                </div>
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase">Institusi</p>
                    <p id="modalInstitusi" class="text-sm font-bold text-gray-700 mt-0.5">-</p>
                </div>
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase">Jurusan</p>
                    <p id="modalJurusan" class="text-sm font-bold text-gray-700 mt-0.5">-</p>
                </div>
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase">No. Whatsapp / Kontak</p>
                    <p id="modalKontak" class="text-sm font-bold text-gray-700 mt-0.5">-</p>
                </div>
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase">Durasi Magang</p>
                    <p id="modalDurasi" class="text-sm font-bold text-gray-700 mt-0.5">- Bulan</p>
                </div>
            </div>

            <div id="cvContainer" class="pt-2 border-t border-gray-100">
                <p class="text-xs font-bold text-gray-400 uppercase mb-2">Berkas CV</p>
                <a id="modalCvBtn" href="#" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-50 text-blue-600 hover:bg-blue-100 text-xs font-bold rounded-xl transition-all">
                    <span class="material-symbols-outlined text-[18px]">download</span> Unduh / Lihat CV Peserta
                </a>
                <span id="noCvText" class="text-xs text-gray-400 italic hidden">CV tidak diunggah</span>
            </div>
        </div>

        <div class="p-4 bg-gray-50 border-t border-gray-200 flex justify-between items-center">
            <button type="button" onclick="closeDetailModal()" class="px-4 py-2 bg-white border border-gray-200 text-gray-600 text-xs font-bold rounded-xl hover:bg-gray-100">Tutup</button>
            
            <form id="modalActionForm" method="POST" action="" class="flex items-center gap-2">
                @csrf
                <div id="actionButtons">
                    <button type="submit" name="action" value="accept" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-xs font-bold rounded-xl shadow-sm transition-all">Terima Akses</button>
                    <button type="submit" name="action" value="reject" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-xs font-bold rounded-xl shadow-sm transition-all">Tolak</button>
                </div>
                <span id="processedText" class="text-xs text-gray-400 italic font-bold hidden">Status Sudah Diproses</span>
            </form>
        </div>
    </div>
</div>

<script>
function submitFilter(status) {
    document.getElementById('statusInput').value = status;
    document.getElementById('filterForm').submit();
}

function openDetailModal(itemJson) {
    const item = JSON.parse(itemJson);

    document.getElementById('modalNama').innerText = item.nama || '-';
    document.getElementById('modalEmail').innerText = item.email || '-';
    document.getElementById('modalInstitusi').innerText = item.institusi || '-';
    document.getElementById('modalJurusan').innerText = item.jurusan || '-';
    document.getElementById('modalKontak').innerText = item.kontak || '-';
    document.getElementById('modalDurasi').innerText = (item.durasi || '-') + ' Bulan';

    // Set CV URL
    const cvBtn = document.getElementById('modalCvBtn');
    const noCvText = document.getElementById('noCvText');
    if (item.cv_path) {
        cvBtn.href = '/storage/' + item.cv_path;
        cvBtn.classList.remove('hidden');
        noCvText.classList.add('hidden');
    } else {
        cvBtn.classList.add('hidden');
        noCvText.classList.remove('hidden');
    }

    // Set Action Form Action URL
    const form = document.getElementById('modalActionForm');
    form.action = '/admin/permintaan/action/' + item.id;

    // Toggle Action Buttons based on status
    const actionBtns = document.getElementById('actionButtons');
    const processedText = document.getElementById('processedText');
    if (item.status === 'menunggu' || !item.status) {
        actionBtns.classList.remove('hidden');
        processedText.classList.add('hidden');
    } else {
        actionBtns.classList.add('hidden');
        processedText.classList.remove('hidden');
    }

    document.getElementById('detailModal').classList.remove('hidden');
}

function closeDetailModal() {
    document.getElementById('detailModal').classList.add('hidden');
}
</script>
@endsection