@extends('layouts.portal')

@section('title', 'Laporan Mingguan')

@section('content')

<section class="mb-6">
    <h1 class="mt-5 text-2xl font-bold text-slate-900 md:text-3xl mb-1">Laporan Mingguan</h1>
    <p class="text-sm text-slate-500">Unggah rangkuman kegiatan Anda setiap minggu untuk ditinjau mentor.</p>
</section>

@if (session('success'))
    <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-700 text-sm px-4 py-3">
        {{ session('success') }}
    </div>
@endif
@if (session('error'))
    <div class="mb-4 rounded-xl border border-rose-200 bg-rose-50 text-rose-700 text-sm px-4 py-3">
        {{ session('error') }}
    </div>
@endif

<div class="bg-white rounded-2xl border border-slate-200 shadow-sm mb-6">
    <div class="p-5 border-b border-slate-100 flex items-center justify-between">
        <div>
            <h3 class="text-lg font-semibold text-slate-900">Upload Laporan: Minggu {{ $mingguSaatIni }}</h3>
            <p class="text-sm text-slate-500">Format PDF/DOC/DOCX, maksimal 10MB.</p>
        </div>
        @if ($laporanMingguIni)
            <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-[11px] font-bold uppercase">Sudah Dikirim</span>
        @else
            <span class="px-3 py-1 bg-amber-100 text-amber-700 rounded-full text-[11px] font-bold uppercase">Belum Dikirim</span>
        @endif
    </div>
    <form method="POST" action="{{ route('peserta-magang.laporan-mingguan.store') }}" enctype="multipart/form-data" class="p-6 grid grid-cols-1 lg:grid-cols-2 gap-6">
        @csrf

        <div class="space-y-2">
            <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">File Laporan (PDF/DOC/DOCX)</label>
            <div class="border-2 border-dashed border-slate-300 rounded-2xl flex flex-col items-center justify-center p-8 bg-slate-50 hover:border-blue-400 hover:bg-slate-100 transition-colors">
                <span class="material-symbols-outlined text-blue-600 text-4xl mb-2">cloud_upload</span>
                <input type="file" name="laporan" accept=".pdf,.doc,.docx" class="text-sm" required>
            </div>
            @error('laporan')
                <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
            @enderror

            @if ($laporanMingguIni)
                <p class="text-xs text-slate-500 mt-2">
                    Mengunggah ulang akan menggantikan laporan minggu ke-{{ $mingguSaatIni }} yang sebelumnya sudah dikirim
                    ({{ $laporanMingguIni->dikumpulkan_pada?->format('d M Y, H:i') }}).
                </p>
            @endif
        </div>

        <div class="flex flex-col justify-end">
            <button type="submit" class="w-full bg-blue-600 text-white font-semibold py-3 rounded-xl shadow-lg shadow-blue-200/40 hover:bg-blue-700 transition-colors flex items-center justify-center gap-2">
                {{ $laporanMingguIni ? 'Kirim Ulang Laporan' : 'Kirim Laporan' }}
                <span class="material-symbols-outlined text-[20px]">send</span>
            </button>
        </div>
    </form>
</div>

<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="p-5 border-b border-slate-100">
        <h3 class="text-lg font-semibold text-slate-900">Riwayat Laporan</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-200">
                    <th class="px-6 py-3 text-xs font-bold text-slate-500 uppercase tracking-wide">Minggu</th>
                    <th class="px-6 py-3 text-xs font-bold text-slate-500 uppercase tracking-wide">Tgl Pengiriman</th>
                    <th class="px-6 py-3 text-xs font-bold text-slate-500 uppercase tracking-wide">File</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($riwayat as $item)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-4 text-sm font-semibold text-slate-900">Minggu {{ $item->minggu_ke }}</td>
                        <td class="px-6 py-4 text-sm text-slate-700">{{ $item->dikumpulkan_pada?->format('d M Y, H:i') ?? '-' }}</td>
                        <td class="px-6 py-4">
                            <a href="{{ Storage::url($item->laporan) }}" target="_blank" class="text-blue-600 text-sm font-semibold hover:underline">Lihat File</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-6 py-8 text-center text-sm text-slate-500">Belum ada laporan yang dikirim.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($riwayat->hasPages())
        <div class="p-4 border-t border-slate-100">
            {{ $riwayat->links() }}
        </div>
    @endif
</div>

@endsection
