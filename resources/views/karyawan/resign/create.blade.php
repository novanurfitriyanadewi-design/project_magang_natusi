@extends('layouts.portal')

@section('title', 'Ajukan Resign')

@section('content')

<div class="mx-auto max-w-5xl">

    {{-- HEADER --}}
    <div class="mb-7 flex items-center gap-3 reveal" style="--d:0">
        <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-50 text-[#05658f]">
            <span class="material-symbols-outlined">person_remove</span>
        </span>
        <div>
            <h1 class="headline text-2xl font-bold text-slate-900">Ajukan Resign</h1>
            <p class="text-sm text-slate-500">Isi formulir di bawah untuk mengajukan pengunduran diri.</p>
        </div>
    </div>

    @if(session('info'))
        <div class="mb-5 flex items-center gap-3 rounded-2xl border border-amber-100 bg-amber-50 px-5 py-4 text-sm font-semibold text-amber-700 reveal" style="--d:1">
            <span class="material-symbols-outlined text-[20px]">info</span>
            {{ session('info') }}
        </div>
    @endif


    <div class="grid grid-cols-1 gap-6 lg:grid-cols-5">

        {{-- LEFT: PROSES + TIPS --}}
        <div class="lg:col-span-2">

            <div class="lg:sticky lg:top-6 space-y-4">

                {{-- TIMELINE --}}
                <div class="reveal rounded-2xl border border-slate-200 bg-white p-6 shadow-sm" style="--d:2">

                    <h2 class="mb-5 text-sm font-bold text-slate-700">Alur Pengajuan</h2>

                    <ol class="relative space-y-6 pl-1">

                        <li class="relative flex gap-3">
                            <span class="absolute left-[11px] top-6 h-[calc(100%+0.5rem)] w-px bg-slate-200"></span>
                            <span class="relative z-10 mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-[#05658f] text-white ring-4 ring-blue-50">
                                <span class="material-symbols-outlined text-[14px]">edit_document</span>
                            </span>
                            <div>
                                <p class="text-sm font-bold text-slate-800">Diajukan</p>
                                <p class="mt-0.5 text-xs leading-5 text-slate-500">Anda mengisi formulir dan (opsional) melampirkan surat resign resmi.</p>
                            </div>
                        </li>

                        <li class="relative flex gap-3">
                            <span class="absolute left-[11px] top-6 h-[calc(100%+0.5rem)] w-px bg-slate-200"></span>
                            <span class="relative z-10 mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full border-2 border-slate-300 bg-white text-slate-400">
                                <span class="material-symbols-outlined text-[14px]">fact_check</span>
                            </span>
                            <div>
                                <p class="text-sm font-bold text-slate-600">Verifikasi Dokumen</p>
                                <p class="mt-0.5 text-xs leading-5 text-slate-500">Jika ada lampiran, HRD memeriksa kelengkapan & keaslian surat.</p>
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

                {{-- TIP --}}
                <div class="reveal rounded-2xl border border-[#045575] bg-[#05658f] p-6 text-white shadow-sm" style="--d:3; background: linear-gradient(135deg, #05658f 0%, #0a7fb0 100%); color: #ffffff;">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-[18px]">lightbulb</span>
                        <p class="text-xs font-bold uppercase tracking-wide text-white">Sebelum mengajukan</p>
                    </div>
                    <p class="mt-2 text-sm leading-6 text-white" style="color: #ffffff;">
                        Disarankan mengajukan resign jauh-jauh hari, idealnya minimal 30 hari sebelum tanggal efektif, agar proses serah terima pekerjaan berjalan lancar.
                    </p>
                </div>

                {{-- TRANSPARANSI FILE --}}
                <div class="reveal rounded-2xl border border-slate-200 bg-white p-6 shadow-sm" style="--d:4">
                    <div class="flex items-center gap-2 text-slate-700">
                        <span class="material-symbols-outlined text-[18px]">privacy_tip</span>
                        <p class="text-xs font-bold uppercase tracking-wide">Tentang lampiran surat</p>
                    </div>
                    <ul class="mt-3 space-y-2 text-xs leading-5 text-slate-500">
                        <li class="flex gap-2">
                            <span class="material-symbols-outlined text-[14px] text-emerald-500">check_circle</span>
                            Lampiran bersifat opsional, formulir tetap bisa dikirim tanpa file.
                        </li>
                        <li class="flex gap-2">
                            <span class="material-symbols-outlined text-[14px] text-emerald-500">check_circle</span>
                            Format diterima: PDF, DOC, DOCX. Maksimal 5 MB.
                        </li>
                        <li class="flex gap-2">
                            <span class="material-symbols-outlined text-[14px] text-emerald-500">check_circle</span>
                            File hanya dapat diakses oleh Anda dan tim HRD untuk keperluan verifikasi.
                        </li>
                    </ul>
                </div>

            </div>

        </div>


        {{-- RIGHT: FORM --}}
        <div class="lg:col-span-3">

            <div class="reveal rounded-2xl border border-slate-200 bg-white p-6 shadow-sm md:p-7" style="--d:2">

                <form
                    method="POST"
                    action="{{ route('karyawan.resign.store') }}"
                    class="space-y-5"
                    enctype="multipart/form-data"
                    onsubmit="document.getElementById('resign-submit-btn').setAttribute('disabled', true); document.getElementById('resign-submit-btn').classList.add('opacity-70');"
                >
                    @csrf

                    {{-- TANGGAL --}}
                    <div>
                        <label class="mb-1.5 block text-xs font-semibold text-slate-600">
                            Tanggal Efektif Resign
                        </label>
                        <input
                            type="date"
                            name="tanggal_efektif"
                            id="tanggal_efektif"
                            value="{{ old('tanggal_efektif') }}"
                            oninput="updateCountdown(this.value)"
                            class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 shadow-sm outline-none transition focus:border-[#05658f] focus:ring-4 focus:ring-[#05658f]/10"
                        >
                        @error('tanggal_efektif')
                            <p class="mt-1 text-xs font-medium text-red-500">{{ $message }}</p>
                        @enderror

                        {{-- COUNTDOWN CHIP --}}
                        <div id="countdown-chip" class="mt-2 hidden items-center gap-1.5 rounded-full border px-3 py-1.5 text-xs font-semibold w-fit transition-colors">
                            <span id="countdown-icon" class="material-symbols-outlined text-[14px]"></span>
                            <span id="countdown-text"></span>
                        </div>
                    </div>

                    {{-- ALASAN --}}
                    <div>
                        <div class="mb-1.5 flex items-center justify-between">
                            <label class="block text-xs font-semibold text-slate-600">
                                Alasan Pengunduran Diri
                            </label>
                            <span id="char-counter" class="text-[11px] font-medium text-slate-400">0 / 10 karakter</span>
                        </div>
                        <textarea
                            name="alasan"
                            id="alasan"
                            rows="6"
                            oninput="updateCounter(this.value)"
                            placeholder="Jelaskan alasan Anda mengajukan resign..."
                            class="w-full resize-none rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 shadow-sm outline-none transition focus:border-[#05658f] focus:ring-4 focus:ring-[#05658f]/10"
                        >{{ old('alasan') }}</textarea>
                        @error('alasan')
                            <p class="mt-1 text-xs font-medium text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- UPLOAD SURAT RESIGN --}}
                    <div>
                        <div class="mb-1.5 flex items-center justify-between">
                            <label class="block text-xs font-semibold text-slate-600">
                                Surat Pengunduran Diri (PDF / Word)
                            </label>
                            <span class="rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide text-slate-500">Opsional</span>
                        </div>

                        <label
                            for="surat_resign"
                            id="dropzone"
                            class="flex cursor-pointer flex-col items-center justify-center gap-2 rounded-xl border-2 border-dashed border-slate-200 bg-slate-50/60 px-4 py-8 text-center transition hover:border-[#05658f] hover:bg-blue-50/40"
                        >
                            <span class="material-symbols-outlined text-[26px] text-slate-400">upload_file</span>
                            <p class="text-xs font-semibold text-slate-600">Klik untuk memilih file, atau seret file ke sini</p>
                            <p class="text-[11px] text-slate-400">PDF, DOC, DOCX &middot; maks. 5 MB</p>
                        </label>

                        <input
                            type="file"
                            name="surat_resign"
                            id="surat_resign"
                            accept=".pdf,.doc,.docx,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document"
                            onchange="handleFileSelect(this)"
                            class="hidden"
                        >

                        {{-- FILE PREVIEW --}}
                        <div id="file-preview" class="mt-2 hidden items-center justify-between gap-3 rounded-xl border border-slate-200 bg-white px-4 py-2.5">
                            <div class="flex min-w-0 items-center gap-2">
                                <span id="file-icon" class="material-symbols-outlined text-[18px] text-[#05658f]">description</span>
                                <div class="min-w-0">
                                    <p id="file-name" class="truncate text-xs font-semibold text-slate-700"></p>
                                    <p id="file-size" class="text-[11px] text-slate-400"></p>
                                </div>
                            </div>
                            <button type="button" onclick="clearFile()" class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full text-slate-400 hover:bg-slate-100 hover:text-red-500">
                                <span class="material-symbols-outlined text-[16px]">close</span>
                            </button>
                        </div>

                        <p id="file-error" class="mt-1.5 hidden text-xs font-medium text-red-500"></p>

                        @error('surat_resign')
                            <p class="mt-1 text-xs font-medium text-red-500">{{ $message }}</p>
                        @enderror

                        <p class="mt-2 flex items-start gap-1.5 text-[11px] leading-4 text-slate-400">
                            <span class="material-symbols-outlined text-[14px]">info</span>
                            Melampirkan surat resmi mempercepat proses verifikasi, tapi bukan syarat wajib untuk mengirim pengajuan ini.
                        </p>
                    </div>

                    <button
                        id="resign-submit-btn"
                        type="submit"
                        class="flex w-full items-center justify-center gap-2 rounded-xl bg-[#05658f] px-5 py-3 text-sm font-bold text-white shadow-md transition hover:bg-[#045575] disabled:cursor-not-allowed"
                        style="background-color: #05658f; color: #ffffff;"
                    >
                        <span class="material-symbols-outlined text-[18px]">send</span>
                        Simpan & Kirim Pengajuan
                    </button>

                </form>

            </div>

        </div>

    </div>

</div>


<style>
    @keyframes resignReveal {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .reveal {
        animation: resignReveal 0.5s ease-out both;
        animation-delay: calc(var(--d, 0) * 70ms);
    }
    @media (prefers-reduced-motion: reduce) {
        .reveal { animation: none; }
    }
</style>

<script>
    function updateCounter(value) {
        const counter = document.getElementById('char-counter');
        const len = value.trim().length;
        counter.textContent = len + ' / 10 karakter';
        counter.classList.toggle('text-emerald-600', len >= 10);
        counter.classList.toggle('text-slate-400', len < 10);
    }

    function updateCountdown(value) {
        const chip = document.getElementById('countdown-chip');
        const icon = document.getElementById('countdown-icon');
        const text = document.getElementById('countdown-text');

        if (!value) {
            chip.classList.add('hidden');
            return;
        }

        const today = new Date();
        today.setHours(0, 0, 0, 0);
        const target = new Date(value + 'T00:00:00');
        const diffDays = Math.round((target - today) / 86400000);

        chip.classList.remove('hidden');
        chip.classList.remove(
            'bg-red-50', 'text-red-600', 'border-red-100',
            'bg-amber-50', 'text-amber-700', 'border-amber-100',
            'bg-emerald-50', 'text-emerald-600', 'border-emerald-100',
            'bg-slate-50', 'text-slate-500', 'border-slate-200'
        );

        if (diffDays < 0) {
            chip.classList.add('bg-slate-50', 'text-slate-500', 'border-slate-200');
            icon.textContent = 'block';
            text.textContent = 'Tanggal sudah lewat';
        } else if (diffDays === 0) {
            chip.classList.add('bg-red-50', 'text-red-600', 'border-red-100');
            icon.textContent = 'priority_high';
            text.textContent = 'Hari ini';
        } else if (diffDays < 30) {
            chip.classList.add('bg-amber-50', 'text-amber-700', 'border-amber-100');
            icon.textContent = 'hourglass_top';
            text.textContent = diffDays + ' hari lagi';
        } else {
            chip.classList.add('bg-emerald-50', 'text-emerald-600', 'border-emerald-100');
            icon.textContent = 'check_circle';
            const weeks = Math.floor(diffDays / 7);
            text.textContent = diffDays + ' hari lagi (' + weeks + ' minggu)';
        }
    }

    const ALLOWED_EXT = ['pdf', 'doc', 'docx'];
    const MAX_SIZE_MB = 5;

    function formatBytes(bytes) {
        if (bytes < 1024) return bytes + ' B';
        if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
        return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
    }

    function handleFileSelect(input) {
        const errorEl = document.getElementById('file-error');
        const preview = document.getElementById('file-preview');
        errorEl.classList.add('hidden');

        const file = input.files[0];
        if (!file) return;

        const ext = file.name.split('.').pop().toLowerCase();

        if (!ALLOWED_EXT.includes(ext)) {
            errorEl.textContent = 'Format tidak didukung. Gunakan PDF, DOC, atau DOCX.';
            errorEl.classList.remove('hidden');
            input.value = '';
            preview.classList.add('hidden');
            preview.classList.remove('flex');
            return;
        }

        if (file.size > MAX_SIZE_MB * 1024 * 1024) {
            errorEl.textContent = 'Ukuran file melebihi ' + MAX_SIZE_MB + ' MB.';
            errorEl.classList.remove('hidden');
            input.value = '';
            preview.classList.add('hidden');
            preview.classList.remove('flex');
            return;
        }

        document.getElementById('file-name').textContent = file.name;
        document.getElementById('file-size').textContent = formatBytes(file.size);
        document.getElementById('file-icon').textContent = ext === 'pdf' ? 'picture_as_pdf' : 'description';

        preview.classList.remove('hidden');
        preview.classList.add('flex');
        document.getElementById('dropzone').classList.add('hidden');
    }

    function clearFile() {
        const input = document.getElementById('surat_resign');
        input.value = '';
        document.getElementById('file-preview').classList.add('hidden');
        document.getElementById('file-preview').classList.remove('flex');
        document.getElementById('dropzone').classList.remove('hidden');
        document.getElementById('file-error').classList.add('hidden');
    }
</script>

@endsection
