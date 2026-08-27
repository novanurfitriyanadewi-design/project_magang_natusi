@extends('layouts.portal')

@section('title', 'Ajukan Cuti')

@section('content')

<div class="mx-auto max-w-6xl">

    {{-- HEADER --}}
    <div class="mb-7 flex items-center gap-3">
        <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-100 text-blue-700">
            <span class="material-symbols-outlined">event_busy</span>
        </span>
        <div>
            <h1 class="headline text-2xl font-bold text-slate-900">Ajukan Cuti</h1>
            <p class="text-sm text-slate-500">Isi formulir di bawah untuk mengajukan cuti kepada HRD.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 flex items-center gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-semibold text-emerald-800 shadow-sm">
            <span class="material-symbols-outlined text-[20px] text-emerald-600">check_circle</span>
            {{ session('success') }}
        </div>
    @endif

    {{-- MAIN GRID --}}
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-12">

        {{-- LEFT COLUMN: INFO & ALUR --}}
        <div class="space-y-6 lg:col-span-4">
            {{-- ALUR PENGAJUAN --}}
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="mb-6 text-sm font-bold text-slate-800">Alur Pengajuan</h2>
                
                <div class="relative space-y-6 pl-2">
                    {{-- GARIS PENGHUBUNG --}}
                    <div class="absolute left-[19px] top-3 h-[calc(100%-24px)] w-0.5 bg-slate-200"></div>

                    {{-- STEP 1 --}}
                    <div class="relative flex items-start gap-4">
                        <span class="relative z-10 flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-blue-600 text-white shadow-sm ring-4 ring-blue-100">
                            <span class="material-symbols-outlined text-[14px]">edit_document</span>
                        </span>
                        <div class="pt-0.5">
                            <p class="text-sm font-bold text-slate-800">Diajukan</p>
                            <p class="mt-0.5 text-xs leading-relaxed text-slate-500">Anda mengisi formulir cuti beserta alasannya.</p>
                        </div>
                    </div>

                    {{-- STEP 2 --}}
                    <div class="relative flex items-start gap-4">
                        <span class="relative z-10 flex h-7 w-7 shrink-0 items-center justify-center rounded-full border border-slate-300 bg-white text-slate-500">
                            <span class="material-symbols-outlined text-[14px]">search</span>
                        </span>
                        <div class="pt-0.5">
                            <p class="text-sm font-bold text-slate-700">Ditinjau HRD</p>
                            <p class="mt-0.5 text-xs leading-relaxed text-slate-500">Pengajuan Anda diperiksa oleh tim HRD.</p>
                        </div>
                    </div>

                    {{-- STEP 3 --}}
                    <div class="relative flex items-start gap-4">
                        <span class="relative z-10 flex h-7 w-7 shrink-0 items-center justify-center rounded-full border border-slate-300 bg-white text-slate-500">
                            <span class="material-symbols-outlined text-[14px]">flag</span>
                        </span>
                        <div class="pt-0.5">
                            <p class="text-sm font-bold text-slate-700">Keputusan</p>
                            <p class="mt-0.5 text-xs leading-relaxed text-slate-500">Anda menerima status disetujui atau ditolak.</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- TIPS CARD (WARNA GELAP KONTRAS TINGGI) --}}
            <div class="rounded-2xl bg-slate-900 p-6 text-white shadow-md">
                <div class="flex items-center gap-2 text-amber-400">
                    <span class="material-symbols-outlined text-[20px]">lightbulb</span>
                    <span class="text-xs font-bold uppercase tracking-wider">Sebelum Mengajukan</span>
                </div>
                <p class="mt-3 text-xs leading-relaxed text-slate-300">
                    Ajukan cuti minimal beberapa hari sebelum tanggal mulai, agar HRD punya waktu meninjau dan mengatur pekerjaan pengganti.
                </p>
            </div>
        </div>

        {{-- RIGHT COLUMN: FORM --}}
        <div class="lg:col-span-8">
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm md:p-8">
                <form method="POST" action="{{ route('karyawan.cuti.store') }}" enctype="multipart/form-data" class="space-y-5">
                    @csrf

                    <div>
                        <label class="mb-1.5 block text-xs font-bold text-slate-700">Jenis Cuti</label>
                        <select name="jenis_cuti" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm font-medium text-slate-800 shadow-sm outline-none transition focus:border-blue-600 focus:ring-4 focus:ring-blue-100">
                            <option value="tahunan">Cuti Tahunan</option>
                            <option value="sakit">Cuti Sakit</option>
                            <option value="melahirkan">Cuti Melahirkan</option>
                            <option value="penting">Cuti Alasan Penting</option>
                            <option value="lainnya">Lainnya</option>
                        </select>
                        @error('jenis_cuti')
                            <p class="mt-1 text-xs font-medium text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label class="mb-1.5 block text-xs font-bold text-slate-700">Tanggal Mulai</label>
                            <input type="date" name="tanggal_mulai" value="{{ old('tanggal_mulai') }}"
                                class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm font-medium text-slate-800 shadow-sm outline-none transition focus:border-blue-600 focus:ring-4 focus:ring-blue-100">
                            @error('tanggal_mulai')
                                <p class="mt-1 text-xs font-medium text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="mb-1.5 block text-xs font-bold text-slate-700">Tanggal Selesai</label>
                            <input type="date" name="tanggal_selesai" value="{{ old('tanggal_selesai') }}"
                                class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm font-medium text-slate-800 shadow-sm outline-none transition focus:border-blue-600 focus:ring-4 focus:ring-blue-100">
                            @error('tanggal_selesai')
                                <p class="mt-1 text-xs font-medium text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label class="mb-1.5 block text-xs font-bold text-slate-700">Alasan</label>
                        <textarea name="alasan" rows="4" placeholder="Jelaskan alasan pengajuan cuti Anda..."
                            class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm font-medium text-slate-800 shadow-sm outline-none transition focus:border-blue-600 focus:ring-4 focus:ring-blue-100">{{ old('alasan') }}</textarea>
                        @error('alasan')
                            <p class="mt-1 text-xs font-medium text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                <div>
                    <label class="mb-1.5 block text-xs font-bold text-slate-700">Bukti Pendukung (opsional)</label>
                    
                    <div class="flex items-center gap-3">
                        <label for="bukti_pendukung" class="flex cursor-pointer items-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-xs font-bold text-white shadow-md transition hover:bg-blue-700 active:scale-95">
                            <span class="material-symbols-outlined text-[18px]">upload_file</span>
                            Pilih File
                        </label>

                        <span id="file-chosen" class="text-xs font-medium text-slate-500">
                            Belum ada file dipilih
                        </span>

                        <input 
                            type="file" 
                            id="bukti_pendukung" 
                            name="bukti_pendukung" 
                            accept=".pdf,.jpg,.jpeg,.png" 
                            class="hidden"
                            onchange="document.getElementById('file-chosen').textContent = this.files[0] ? this.files[0].name : 'Belum ada file dipilih'"
                        >
                    </div>

                    @error('bukti_pendukung')
                        <p class="mt-1 text-xs font-medium text-red-500">{{ $message }}</p>
                    @enderror

                    <p class="mt-2 flex items-center gap-1.5 text-[11px] text-slate-500">
                        <span class="material-symbols-outlined text-[14px]">info</span>
                        Contoh: surat dokter untuk cuti sakit. Format PDF/JPG/PNG, maksimal 2MB.
                    </p>
                </div>

                    {{-- TOMBOL UTAMA DENGAN WARNA BIRU TERANG & SHADOW --}}
                    <button type="submit" class="flex w-full items-center justify-center gap-2 rounded-xl bg-blue-600 px-5 py-3.5 text-sm font-bold text-white shadow-md transition-all duration-200 hover:bg-blue-700 hover:shadow-lg active:scale-[0.99]">
                        <span class="material-symbols-outlined text-[20px]">send</span>
                        Kirim Pengajuan
                    </button>
                </form>
            </div>
        </div>

    </div>

    {{-- RIWAYAT --}}
    <div class="mt-10">
        <h2 class="mb-4 text-base font-bold text-slate-800">Riwayat Pengajuan Cuti</h2>

        <div class="space-y-3">
            @forelse ($riwayat as $cuti)
                @php($meta = $cuti->statusMeta())
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:border-slate-300">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-sm font-bold text-slate-800">{{ $cuti->jenis_label }}</p>
                            <p class="mt-1 text-xs text-slate-500">
                                {{ $cuti->tanggal_mulai->translatedFormat('d M Y') }} — {{ $cuti->tanggal_selesai->translatedFormat('d M Y') }}
                                <span class="font-medium text-slate-400">({{ $cuti->jumlah_hari }} hari)</span>
                            </p>
                        </div>
                        <span class="inline-flex shrink-0 items-center gap-1 rounded-full px-3 py-1 text-[11px] font-semibold {{ $meta['class'] }}">
                            {{ $meta['label'] }}
                        </span>
                    </div>
                    
                    <p class="mt-3 text-sm leading-relaxed text-slate-600">{{ $cuti->alasan }}</p>

                    @if ($cuti->status === 'ditolak' && $cuti->catatan_hrd)
                        <div class="mt-3 rounded-xl border border-red-100 bg-red-50 px-4 py-3 text-xs text-red-700">
                            <span class="font-semibold">Alasan Penolakan:</span> {{ $cuti->catatan_hrd }}
                        </div>
                    @endif

                    @if ($cuti->bukti_pendukung)
                        <a href="{{ asset('storage/' . $cuti->bukti_pendukung) }}" target="_blank" class="mt-3 inline-flex items-center gap-1.5 text-xs font-semibold text-blue-600 hover:underline">
                            <span class="material-symbols-outlined text-[14px]">attach_file</span>
                            Lihat Bukti Pendukung
                        </a>
                    @endif

                    <div class="mt-4 flex items-center gap-1.5 border-t border-slate-100 pt-3 text-[11px] text-slate-400">
                        <span class="material-symbols-outlined text-[14px]">schedule</span>
                        Diajukan {{ $cuti->created_at->translatedFormat('d F Y, H:i') }}
                    </div>
                </div>
            @empty
                <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-12 text-center">
                    <span class="material-symbols-outlined mx-auto mb-2 text-[36px] text-slate-300">history</span>
                    <p class="text-sm font-bold text-slate-700">Belum Ada Riwayat Cuti</p>
                    <p class="mt-1 text-xs text-slate-400">Pengajuan cuti Anda akan muncul di sini.</p>
                </div>
            @endforelse
        </div>
    </div>

</div>

@endsection