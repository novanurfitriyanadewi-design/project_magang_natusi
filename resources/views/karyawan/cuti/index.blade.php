@extends('layouts.portal')

@section('title', 'Ajukan Cuti')

@section('content')

<div class="mx-auto max-w-5xl" x-data="{ jenis: @js(old('jenis_cuti', 'tahunan')), mulai: @js(old('tanggal_mulai', '')), selesai: @js(old('tanggal_selesai', '')), alasan: @js(old('alasan', '')) }">

    <div class="mb-7 flex items-center gap-3">
        <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-50 text-[#05658f]">
            <span class="material-symbols-outlined">event_busy</span>
        </span>
        <div>
            <h1 class="headline text-2xl font-bold text-slate-900">Ajukan Cuti</h1>
            <p class="text-sm text-slate-500">Isi formulir di bawah untuk mengajukan cuti kepada HRD.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-5 flex items-center gap-3 rounded-2xl border border-emerald-100 bg-emerald-50 px-5 py-4 text-sm font-semibold text-emerald-700">
            <span class="material-symbols-outlined text-[20px]">check_circle</span>
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-5">

        {{-- LEFT: INFO --}}
        <div class="lg:col-span-2">
            <div class="lg:sticky lg:top-6 space-y-4">

                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h2 class="mb-5 text-sm font-bold text-slate-700">Alur Pengajuan</h2>
                    <ol class="relative space-y-6 pl-1">
                        <li class="relative flex gap-3">
                            <span class="absolute left-[11px] top-6 h-[calc(100%+0.5rem)] w-px bg-slate-200"></span>
                            <span class="relative z-10 mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-[#05658f] text-white ring-4 ring-blue-50">
                                <span class="material-symbols-outlined text-[14px]">edit_document</span>
                            </span>
                            <div>
                                <p class="text-sm font-bold text-slate-800">Diajukan</p>
                                <p class="mt-0.5 text-xs leading-5 text-slate-500">Anda mengisi formulir cuti beserta alasannya.</p>
                            </div>
                        </li>
                        <li class="relative flex gap-3">
                            <span class="absolute left-[11px] top-6 h-[calc(100%+0.5rem)] w-px bg-slate-200"></span>
                            <span class="relative z-10 mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full border-2 border-slate-300 bg-white text-slate-400">
                                <span class="material-symbols-outlined text-[14px]">search</span>
                            </span>
                            <div>
                                <p class="text-sm font-bold text-slate-600">Ditinjau HRD</p>
                                <p class="mt-0.5 text-xs leading-5 text-slate-500">Pengajuan Anda diperiksa oleh tim HRD.</p>
                            </div>
                        </li>
                        <li class="relative flex gap-3">
                            <span class="relative z-10 mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full border-2 border-slate-300 bg-white text-slate-400">
                                <span class="material-symbols-outlined text-[14px]">flag</span>
                            </span>
                            <div>
                                <p class="text-sm font-bold text-slate-600">Keputusan</p>
                                <p class="mt-0.5 text-xs leading-5 text-slate-500">Anda menerima status disetujui atau ditolak.</p>
                            </div>
                        </li>
                    </ol>
                </div>

                <div class="rounded-2xl border border-[#045575] bg-[#05658f] p-6 text-white shadow-sm" style="background: linear-gradient(135deg, #05658f 0%, #0a7fb0 100%); color: #ffffff;">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-[18px]">lightbulb</span>
                        <p class="text-xs font-bold uppercase tracking-wide text-white">Sebelum mengajukan</p>
                    </div>
                    <p class="mt-2 text-sm leading-6 text-white" style="color: #ffffff;">
                        Ajukan cuti minimal beberapa hari sebelum tanggal mulai, agar HRD punya waktu meninjau dan mengatur pekerjaan pengganti.
                    </p>
                </div>
            </div>
        </div>

        {{-- RIGHT: FORM --}}
        <div class="lg:col-span-3">
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm md:p-7">
                <form method="POST" action="{{ route('karyawan.cuti.store') }}" enctype="multipart/form-data" class="space-y-5">
                    @csrf

                    <div>
                        <label class="mb-1.5 block text-xs font-semibold text-slate-600">Jenis Cuti</label>
                        <select name="jenis_cuti" x-model="jenis" class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 shadow-sm outline-none transition focus:border-[#05658f] focus:ring-4 focus:ring-[#05658f]/10">
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
                            <label class="mb-1.5 block text-xs font-semibold text-slate-600">Tanggal Mulai</label>
                            <input type="date" name="tanggal_mulai" x-model="mulai"
                                class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 shadow-sm outline-none transition focus:border-[#05658f] focus:ring-4 focus:ring-[#05658f]/10">
                            @error('tanggal_mulai')
                                <p class="mt-1 text-xs font-medium text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="mb-1.5 block text-xs font-semibold text-slate-600">Tanggal Selesai</label>
                            <input type="date" name="tanggal_selesai" x-model="selesai"
                                class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 shadow-sm outline-none transition focus:border-[#05658f] focus:ring-4 focus:ring-[#05658f]/10">
                            @error('tanggal_selesai')
                                <p class="mt-1 text-xs font-medium text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label class="mb-1.5 block text-xs font-semibold text-slate-600">Alasan</label>
                        <textarea name="alasan" x-model="alasan" rows="4" placeholder="Jelaskan alasan pengajuan cuti Anda..."
                            class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 shadow-sm outline-none transition focus:border-[#05658f] focus:ring-4 focus:ring-[#05658f]/10"></textarea>
                        @error('alasan')
                            <p class="mt-1 text-xs font-medium text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-1.5 block text-xs font-semibold text-slate-600">Bukti Pendukung (opsional)</label>
                        <input type="file" name="bukti_pendukung" accept=".pdf,.jpg,.jpeg,.png"
                            class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-600 shadow-sm outline-none file:mr-3 file:rounded-lg file:border-0 file:bg-[#05658f] file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-white">
                        @error('bukti_pendukung')
                            <p class="mt-1 text-xs font-medium text-red-500">{{ $message }}</p>
                        @enderror
                        <p class="mt-2 flex items-start gap-1.5 text-[11px] leading-4 text-slate-400">
                            <span class="material-symbols-outlined text-[14px]">info</span>
                            Contoh: surat dokter untuk cuti sakit. Format PDF/JPG/PNG, maksimal 2MB.
                        </p>
                    </div>

                    <button type="submit" class="flex w-full items-center justify-center gap-2 rounded-xl bg-[#05658f] px-5 py-3 text-sm font-bold text-white shadow-md transition hover:bg-[#045575]" style="background-color: #05658f; color: #ffffff;">
                        <span class="material-symbols-outlined text-[18px]">send</span>
                        Simpan & Kirim Pengajuan
                    </button>
                </form>

                <div class="mt-6 rounded-xl border border-sky-100 bg-sky-50/70 p-5" x-show="jenis || mulai || selesai || alasan">
                    <div class="flex items-center justify-between gap-3">
                        <h2 class="text-sm font-bold text-slate-800">Template Surat Izin Cuti</h2>
                        <span class="text-[11px] font-semibold text-sky-700">Preview otomatis</span>
                    </div>
                    <div class="mt-4 border-t border-sky-100 pt-4 text-xs leading-6 text-slate-600">
                        <p class="text-center font-bold uppercase text-slate-800">Surat Izin Cuti</p>
                        <p class="mt-3">Yang bertanda tangan di bawah ini menerangkan bahwa karyawan <strong>{{ $karyawan?->nama_karyawan ?? '-' }}</strong> mengajukan <strong x-text="jenis === 'melahirkan' ? 'Cuti Melahirkan' : (jenis === 'sakit' ? 'Cuti Sakit' : (jenis === 'tahunan' ? 'Cuti Tahunan' : 'Cuti Alasan Penting'))"></strong>.</p>
                        <p>Periode: <strong x-text="mulai || '-'"></strong> sampai <strong x-text="selesai || '-'"></strong>.</p>
                        <p>Alasan: <span x-text="alasan || '-' "></span></p>
                    </div>
                    <p class="mt-3 text-[11px] text-slate-500">Surat PDF resmi akan tersedia setelah pengajuan tersimpan.</p>
                </div>
            </div>
        </div>
    </div>

    {{-- RIWAYAT --}}
    <div class="mt-8">
        <h2 class="mb-4 text-base font-bold text-slate-800">Riwayat Pengajuan Cuti</h2>

        <div class="space-y-3">
            @forelse ($riwayat as $cuti)
                @php($meta = $cuti->statusMeta())
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-sm font-bold text-slate-800">{{ $cuti->jenis_label }}</p>
                            <p class="mt-1 text-xs text-slate-500">
                                {{ $cuti->tanggal_mulai->translatedFormat('d M Y') }} — {{ $cuti->tanggal_selesai->translatedFormat('d M Y') }}
                                <span class="text-slate-400">({{ $cuti->jumlah_hari }} hari)</span>
                            </p>
                        </div>
                        <span class="inline-flex shrink-0 items-center gap-1 rounded-full px-2.5 py-1 text-[11px] font-semibold {{ $meta['class'] }}">
                            {{ $meta['label'] }}
                        </span>
                    </div>
                    <p class="mt-3 text-sm leading-6 text-slate-600">{{ $cuti->alasan }}</p>

                    @if ($cuti->status === 'ditolak' && $cuti->catatan_hrd)
                        <div class="mt-3 rounded-xl border border-red-100 bg-red-50 px-4 py-3 text-sm text-red-700">
                            <span class="font-semibold">Alasan Penolakan:</span> {{ $cuti->catatan_hrd }}
                        </div>
                    @endif

                    @if ($cuti->bukti_pendukung)
                        <a href="{{ asset('storage/' . $cuti->bukti_pendukung) }}" target="_blank" class="mt-3 inline-flex items-center gap-1.5 text-xs font-semibold text-[#05658f] hover:underline">
                            <span class="material-symbols-outlined text-[14px]">attach_file</span>
                            Lihat Bukti Pendukung
                        </a>
                    @endif

                    <a href="{{ route('karyawan.cuti.letter', $cuti) }}" target="_blank" class="mt-3 inline-flex items-center gap-1.5 text-xs font-semibold text-emerald-700 hover:underline">
                        <span class="material-symbols-outlined text-[14px]">description</span>
                        Lihat Template Surat
                    </a>

                    <div class="mt-3 flex items-center gap-1.5 border-t border-slate-100 pt-3 text-xs text-slate-400">
                        <span class="material-symbols-outlined text-[14px]">schedule</span>
                        Diajukan {{ $cuti->created_at->translatedFormat('d F Y, H:i') }}
                    </div>
                </div>
            @empty
                <div class="rounded-2xl border border-dashed border-slate-200 bg-white p-10 text-center">
                    <p class="text-sm font-bold text-slate-700">Belum Ada Riwayat Cuti</p>
                    <p class="mt-1 text-xs text-slate-500">Pengajuan cuti Anda akan muncul di sini.</p>
                </div>
            @endforelse
        </div>
    </div>

</div>

@endsection