@extends('layouts.portal')

@section('title', 'Metode Pembayaran')

@section('content')
    @php
        $bankCodeByName = array_flip($bankOptions);
        $oldContext = old('form_context');
        $editBankId = old('bank_id');
    @endphp

    <div
        x-data="{
            historyOpen: false,
            editOpen: @js($errors->any() && $oldContext === 'edit'),
            editBank: {
                action: @js($editBankId ? route('admin.metode-pembayaran.bank.update', $editBankId) : ''),
                id: @js($editBankId ?? ''),
                nama_bank: @js($oldContext === 'edit' ? old('nama_bank', '') : ''),
                no_rekening: @js($oldContext === 'edit' ? old('no_rekening', '') : ''),
                nama_pemilik: @js($oldContext === 'edit' ? old('nama_pemilik', '') : ''),
            },
            openEdit(bank) {
                this.editBank = bank;
                this.editOpen = true;
            },
            closeModals() {
                this.historyOpen = false;
                this.editOpen = false;
            },
        }"
        @keydown.escape.window="closeModals()"
        x-effect="document.body.classList.toggle('overflow-hidden', historyOpen || editOpen)"
        class="p-6"
    >
        {{-- Page Header --}}
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
            <div>
                <nav class="flex items-center gap-2 text-[#3f4850] text-[11px] font-semibold uppercase tracking-[0.05em] mb-2">
                    <span>Admin Portal</span>
                    <svg class="h-3 w-3" viewBox="0 0 24 24" fill="none"><path d="m9 6 6 6-6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    <span class="text-[#006191]">Metode Pembayaran</span>
                </nav>
                <h2 class="text-[28px] leading-[36px] font-bold tracking-tight text-[#0b1c30]">Manajemen Rekening Bank</h2>
                <p class="text-sm text-[#3f4850] mt-1 max-w-2xl">Kelola daftar rekening bank resmi CV Natusi untuk transaksi pendaftaran dan administrasi magang.</p>
            </div>

            <button
                type="button"
                @click="historyOpen = true"
                class="flex items-center gap-2 px-4 py-2 border border-[#006191] text-[#006191] font-bold text-xs uppercase tracking-wide rounded-lg hover:bg-[#e5eeff] transition-all"
            >
                <svg class="h-[18px] w-[18px]" viewBox="0 0 24 24" fill="none">
                    <path d="M4 12a8 8 0 1 0 2.3-5.7L4 8.6M4 4v4.6h4.6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M12 7v5l3 2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                </svg>
                Riwayat Perubahan
            </button>
        </div>

        @if (session('success'))
            <div class="mt-5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mt-5 rounded-xl border border-[#ba1a1a]/30 bg-[#ffdad6] px-4 py-3 text-sm text-[#93000a]">
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mt-5 rounded-xl border border-[#ba1a1a]/30 bg-[#ffdad6] px-4 py-3 text-sm text-[#93000a]">
                <p class="font-bold">Data belum dapat disimpan.</p>
                <p class="mt-1">Periksa kembali kolom yang ditandai pada formulir.</p>
            </div>
        @endif

        {{-- Kelola Jumlah Pembayaran --}}
        <div class="mt-6 bg-white border border-[#bec7d2] rounded-xl p-6 border-l-4 border-l-[#006191] shadow-[0_4px_8px_-2px_rgba(0,97,145,0.04),0_2px_4px_-2px_rgba(0,97,145,0.02)]">
            <div class="flex items-center gap-3 mb-6">
                <svg class="h-5 w-5 text-[#006191]" viewBox="0 0 24 24" fill="none"><path d="M5 7h14v10H5V7Zm3 0V5h8v2M8 12h8" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                <h3 class="text-lg font-semibold text-[#0b1c30]">Kelola Jumlah Pembayaran</h3>
            </div>

            <form
                method="POST"
                action="{{ route('admin.metode-pembayaran.nominal.update') }}"
                class="grid grid-cols-1 md:grid-cols-3 gap-6 items-end"
            >
                @csrf
                @method('PUT')
                <input type="hidden" name="form_context" value="nominal">

                <div class="md:col-span-2 space-y-2">
                    <label for="jumlah_nominal" class="text-xs font-bold text-[#0b1c30]">Biaya Pendaftaran / Administrasi Magang</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 font-bold text-[#3f4850]">Rp</span>
                        <input
                            id="jumlah_nominal"
                            name="jumlah_nominal"
                            type="number"
                            inputmode="numeric"
                            min="1000"
                            step="1000"
                            value="{{ $oldContext === 'nominal' ? old('jumlah_nominal') : ($nominal?->jumlah_nominal ?? '') }}"
                            placeholder="Contoh: 150000"
                            required
                            class="w-full bg-[#f8f9ff] border border-[#bec7d2] rounded-lg pl-10 pr-3 py-2.5 text-sm focus:ring-2 focus:ring-[#006191]/20 focus:border-[#006191] outline-none transition-all"
                        >
                    </div>
                    @if ($oldContext === 'nominal')
                        @error('jumlah_nominal')
                            <p class="text-xs font-medium text-[#ba1a1a]">{{ $message }}</p>
                        @enderror
                    @endif
                </div>

                <div class="flex justify-end">
                    <button
                        type="submit"
                        class="bg-[#006191] text-white px-8 py-2.5 rounded-lg font-bold text-xs uppercase tracking-wide hover:brightness-110 active:scale-95 transition-all flex items-center gap-2 shadow-sm w-full md:w-auto justify-center"
                    >
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none"><path d="M5 4h12l2 2v14H5V4Zm3 0v6h8V4M8 16h8" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        Simpan
                    </button>
                </div>
            </form>
        </div>

        {{-- Tambah Metode Pembayaran --}}
        <div class="mt-6 bg-white border border-[#bec7d2] rounded-xl p-6 border-l-4 border-l-[#006191] shadow-[0_4px_8px_-2px_rgba(0,97,145,0.04),0_2px_4px_-2px_rgba(0,97,145,0.02)]">
            <div class="flex items-center gap-3 mb-6">
                <svg class="h-5 w-5 text-[#006191]" viewBox="0 0 24 24" fill="none"><path d="M4 9h16M6 9V6h12v3M5 9v10h14V9M8 13h3" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                <h3 class="text-lg font-semibold text-[#0b1c30]">Tambah Metode Pembayaran</h3>
            </div>

            <form
                method="POST"
                action="{{ route('admin.metode-pembayaran.bank.store') }}"
                class="grid grid-cols-1 md:grid-cols-3 gap-6"
            >
                @csrf
                <input type="hidden" name="form_context" value="create">

                <div class="space-y-2">
                    <label for="create_nama_bank" class="text-xs font-bold text-[#0b1c30]">Nama Bank</label>
                    <div class="relative">
                        <select
                            id="create_nama_bank"
                            name="nama_bank"
                            required
                            class="w-full bg-[#f8f9ff] border border-[#bec7d2] rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-[#006191]/20 focus:border-[#006191] outline-none transition-all appearance-none"
                        >
                            <option value="">Pilih Bank</option>
                            @foreach ($bankOptions as $code => $bankName)
                                <option value="{{ $bankName }}" @selected($oldContext === 'create' && old('nama_bank') === $bankName)>
                                    {{ $code }} — {{ $bankName }}
                                </option>
                            @endforeach
                        </select>
                        <svg class="h-4 w-4 absolute right-3 top-1/2 -translate-y-1/2 text-[#6f7881] pointer-events-none" viewBox="0 0 24 24" fill="none"><path d="m6 9 6 6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </div>
                    @if ($oldContext === 'create')
                        @error('nama_bank')
                            <p class="text-xs font-medium text-[#ba1a1a]">{{ $message }}</p>
                        @enderror
                    @endif
                </div>

                <div class="space-y-2">
                    <label for="create_no_rekening" class="text-xs font-bold text-[#0b1c30]">Nomor Rekening</label>
                    <input
                        id="create_no_rekening"
                        name="no_rekening"
                        type="text"
                        inputmode="numeric"
                        maxlength="30"
                        value="{{ $oldContext === 'create' ? old('no_rekening') : '' }}"
                        placeholder="Masukkan nomor rekening"
                        required
                        class="w-full bg-[#f8f9ff] border border-[#bec7d2] rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-[#006191]/20 focus:border-[#006191] outline-none transition-all"
                    >
                    @if ($oldContext === 'create')
                        @error('no_rekening')
                            <p class="text-xs font-medium text-[#ba1a1a]">{{ $message }}</p>
                        @enderror
                    @endif
                </div>

                <div class="space-y-2">
                    <label for="create_nama_pemilik" class="text-xs font-bold text-[#0b1c30]">Atas Nama</label>
                    <input
                        id="create_nama_pemilik"
                        name="nama_pemilik"
                        type="text"
                        maxlength="100"
                        value="{{ $oldContext === 'create' ? old('nama_pemilik') : '' }}"
                        placeholder="Masukkan nama pemilik rekening"
                        required
                        class="w-full bg-[#f8f9ff] border border-[#bec7d2] rounded-lg px-3 py-2.5 text-sm uppercase focus:ring-2 focus:ring-[#006191]/20 focus:border-[#006191] outline-none transition-all"
                    >
                    @if ($oldContext === 'create')
                        @error('nama_pemilik')
                            <p class="text-xs font-medium text-[#ba1a1a]">{{ $message }}</p>
                        @enderror
                    @endif
                </div>

                <div class="md:col-span-3 flex justify-end">
                    <button
                        type="submit"
                        class="bg-[#006191] text-white px-8 py-2.5 rounded-lg font-bold text-xs uppercase tracking-wide hover:brightness-110 active:scale-95 transition-all flex items-center gap-2 shadow-sm"
                    >
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none"><path d="M5 4h12l2 2v14H5V4Zm3 0v6h8V4M8 16h8" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        Simpan Metode Pembayaran
                    </button>
                </div>
            </form>
        </div>

        {{-- Daftar Rekening --}}
        <div class="mt-6 bg-white border border-[#bec7d2] rounded-xl overflow-hidden shadow-[0_4px_8px_-2px_rgba(0,97,145,0.04),0_2px_4px_-2px_rgba(0,97,145,0.02)]">
            <div class="p-6 border-b border-[#bec7d2] flex justify-between items-center bg-[#eff4ff] flex-wrap gap-3">
                <h3 class="text-xs font-bold text-[#0b1c30] uppercase tracking-widest flex items-center gap-2">
                    <svg class="h-5 w-5 text-[#006191]" viewBox="0 0 24 24" fill="none"><path d="M5 4h14v16H5V4Zm3 4h8M8 12h8M8 16h5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                    Daftar Rekening Pembayaran
                    @if ($search !== '')
                        <span class="normal-case font-medium text-[#3f4850]">— hasil pencarian "{{ $search }}"</span>
                    @endif
                </h3>
                <div class="flex items-center gap-2 px-3 py-1 bg-[#d3e4fe] rounded-full border border-[#bec7d2]">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span class="text-[11px] font-bold text-[#3f4850]">{{ $totalBanks }} Rekening Aktif</span>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-[#e5eeff] text-[#3f4850] text-[11px] font-bold uppercase tracking-widest">
                        <tr>
                            <th class="px-6 py-4 border-b border-[#bec7d2] w-16">No</th>
                            <th class="px-6 py-4 border-b border-[#bec7d2]">Nama Bank</th>
                            <th class="px-6 py-4 border-b border-[#bec7d2]">Nomor Rekening</th>
                            <th class="px-6 py-4 border-b border-[#bec7d2]">Atas Nama</th>
                            <th class="px-6 py-4 border-b border-[#bec7d2] text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm text-[#0b1c30]">
                        @forelse ($banks as $index => $bank)
                            @php
                                $bankCode = $bankCodeByName[$bank->nama_bank]
                                    ?? strtoupper(mb_substr(preg_replace('/[^A-Za-z]/', '', $bank->nama_bank), 0, 4));

                                $editPayload = [
                                    'action' => route('admin.metode-pembayaran.bank.update', $bank),
                                    'id' => $bank->id_bank,
                                    'nama_bank' => $bank->nama_bank,
                                    'no_rekening' => $bank->no_rekening,
                                    'nama_pemilik' => $bank->nama_pemilik,
                                ];
                            @endphp
                            <tr class="hover:bg-[#eff4ff] transition-colors group">
                                <td class="px-6 py-5 border-b border-[#bec7d2]">{{ $index + 1 }}</td>
                                <td class="px-6 py-5 border-b border-[#bec7d2]">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded bg-[#006191]/10 flex items-center justify-center text-[#006191] font-bold text-[12px]">
                                            {{ $bankCode }}
                                        </div>
                                        <span class="font-bold">{{ $bank->nama_bank }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-5 border-b border-[#bec7d2] font-mono tracking-wider">{{ $bank->no_rekening }}</td>
                                <td class="px-6 py-5 border-b border-[#bec7d2]">{{ $bank->nama_pemilik }}</td>
                                <td class="px-6 py-5 border-b border-[#bec7d2] text-right">
                                    <div class="flex justify-end gap-2">
                                        <button
                                            type="button"
                                            @click='openEdit(@js($editPayload))'
                                            class="p-2 text-[#006191] hover:bg-[#006191]/10 rounded-lg transition-colors"
                                            title="Edit"
                                            aria-label="Ubah rekening {{ $bank->nama_bank }}"
                                        >
                                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none"><path d="m5 16-.8 3.8L8 19l10-10-3-3L5 16ZM13.5 7.5l3 3" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                        </button>
                                        <button
                                            type="button"
                                            @click="$dispatch('open-delete-confirm', {
                                                action: @js(route('admin.metode-pembayaran.bank.destroy', $bank)),
                                                title: 'Hapus Rekening?',
                                                name: @js($bank->nama_bank . ' • ' . $bank->no_rekening),
                                                description: 'Rekening akan dihapus dari daftar metode pembayaran. Rekening yang sudah digunakan pada transaksi tidak dapat dihapus.',
                                                confirmText: 'Ya, Hapus Rekening'
                                            })"
                                            class="p-2 text-[#bb0014] hover:bg-[#bb0014]/10 rounded-lg transition-colors"
                                            title="Hapus"
                                            aria-label="Hapus rekening {{ $bank->nama_bank }}"
                                        >
                                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none"><path d="M5 7h14M9 7V4.5h6V7M8 10v7M12 10v7M16 10v7M6.5 7l.7 12h9.6l.7-12" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-14 text-center border-b border-[#bec7d2]">
                                    <p class="font-bold text-[#0b1c30]">
                                        {{ $search !== '' ? 'Rekening tidak ditemukan' : 'Belum ada rekening pembayaran' }}
                                    </p>
                                    <p class="mt-1 text-sm text-[#3f4850]">
                                        {{ $search !== '' ? 'Coba gunakan kata kunci lain pada kolom pencarian.' : 'Tambahkan rekening resmi melalui formulir di atas.' }}
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-4 bg-[#e5eeff] flex justify-center border-t border-[#bec7d2]">
                <p class="text-[11px] text-[#3f4850] font-medium">Hanya rekening yang terdaftar di sini yang akan tampil pada portal peserta magang.</p>
            </div>
        </div>

        {{-- Modal edit rekening --}}
        <template x-teleport="body">
            <div
                x-cloak
                x-show="editOpen"
                x-transition.opacity
                class="fixed inset-0 overflow-y-auto bg-slate-950/45"
                style="z-index: 2147483647; backdrop-filter: blur(3px);"
            >
                <div class="flex min-h-full items-center justify-center px-4 py-6" @click.self="editOpen = false">
                    <article
                        x-show="editOpen"
                        x-transition.scale.origin.center
                        role="dialog"
                        aria-modal="true"
                        aria-labelledby="edit-bank-title"
                        class="w-full max-w-lg overflow-hidden rounded-xl border border-[#bec7d2] bg-white shadow-2xl"
                    >
                        <header class="flex items-start justify-between gap-4 bg-[#006191] px-6 py-5 text-white">
                            <div>
                                <h2 id="edit-bank-title" class="text-xl font-bold">Ubah Rekening Bank</h2>
                                <p class="mt-1 text-sm text-white/80">Perbarui bank, nomor rekening, atau nama pemilik.</p>
                            </div>
                            <button type="button" @click="editOpen = false" class="rounded-lg p-2 text-white/80 transition hover:bg-white/10 hover:text-white" aria-label="Tutup modal">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none"><path d="m6 6 12 12M18 6 6 18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                            </button>
                        </header>

                        <form method="POST" :action="editBank.action" class="p-6">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="form_context" value="edit">
                            <input type="hidden" name="bank_id" :value="editBank.id">

                            <div class="space-y-4">
                                <div>
                                    <label for="edit_nama_bank" class="mb-1.5 block text-sm font-bold text-[#0b1c30]">Nama Bank</label>
                                    <select id="edit_nama_bank" name="nama_bank" x-model="editBank.nama_bank" required class="w-full rounded-lg border-[#bec7d2] focus:border-[#006191] focus:ring-[#006191]">
                                        <option value="">Pilih Bank</option>
                                        @foreach ($bankOptions as $code => $bankName)
                                            <option value="{{ $bankName }}">{{ $code }} — {{ $bankName }}</option>
                                        @endforeach
                                    </select>
                                    @if ($oldContext === 'edit')
                                        @error('nama_bank')<p class="mt-1.5 text-xs font-medium text-[#ba1a1a]">{{ $message }}</p>@enderror
                                    @endif
                                </div>

                                <div>
                                    <label for="edit_no_rekening" class="mb-1.5 block text-sm font-bold text-[#0b1c30]">Nomor Rekening</label>
                                    <input id="edit_no_rekening" name="no_rekening" type="text" inputmode="numeric" maxlength="30" x-model="editBank.no_rekening" required class="w-full rounded-lg border-[#bec7d2] focus:border-[#006191] focus:ring-[#006191]">
                                    @if ($oldContext === 'edit')
                                        @error('no_rekening')<p class="mt-1.5 text-xs font-medium text-[#ba1a1a]">{{ $message }}</p>@enderror
                                    @endif
                                </div>

                                <div>
                                    <label for="edit_nama_pemilik" class="mb-1.5 block text-sm font-bold text-[#0b1c30]">Atas Nama</label>
                                    <input id="edit_nama_pemilik" name="nama_pemilik" type="text" maxlength="100" x-model="editBank.nama_pemilik" required class="w-full rounded-lg border-[#bec7d2] uppercase focus:border-[#006191] focus:ring-[#006191]">
                                    @if ($oldContext === 'edit')
                                        @error('nama_pemilik')<p class="mt-1.5 text-xs font-medium text-[#ba1a1a]">{{ $message }}</p>@enderror
                                    @endif
                                </div>
                            </div>

                            <div class="mt-6 flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
                                <button type="button" @click="editOpen = false" class="rounded-lg border border-[#bec7d2] px-5 py-2.5 text-sm font-bold text-[#3f4850] transition hover:bg-[#eff4ff]">Batal</button>
                                <button type="submit" class="rounded-lg bg-[#006191] px-5 py-2.5 text-sm font-bold text-white shadow-lg transition hover:brightness-110">Simpan Perubahan</button>
                            </div>
                        </form>
                    </article>
                </div>
            </div>
        </template>

        {{-- Modal riwayat perubahan --}}
        <template x-teleport="body">
            <div
                x-cloak
                x-show="historyOpen"
                x-transition.opacity
                class="fixed inset-0 overflow-y-auto bg-slate-950/45"
                style="z-index: 2147483647; backdrop-filter: blur(3px);"
            >
                <div class="flex min-h-full items-center justify-center px-4 py-6" @click.self="historyOpen = false">
                    <article
                        x-show="historyOpen"
                        x-transition.scale.origin.center
                        role="dialog"
                        aria-modal="true"
                        aria-labelledby="history-title"
                        class="flex max-h-[85vh] w-full max-w-3xl flex-col overflow-hidden rounded-xl border border-[#bec7d2] bg-white shadow-2xl"
                    >
                        <header class="flex shrink-0 items-start justify-between gap-4 bg-[#006191] px-6 py-5 text-white">
                            <div>
                                <h2 id="history-title" class="text-xl font-bold">Riwayat Perubahan Pembayaran</h2>
                                <p class="mt-1 text-sm text-white/80">Menampilkan maksimal 30 aktivitas terbaru.</p>
                            </div>
                            <button type="button" @click="historyOpen = false" class="rounded-lg p-2 text-white/80 transition hover:bg-white/10 hover:text-white" aria-label="Tutup riwayat">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none"><path d="m6 6 12 12M18 6 6 18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                            </button>
                        </header>

                        <div class="min-h-0 flex-1 overflow-y-auto p-6">
                            <div class="space-y-3">
                                @forelse ($histories as $history)
                                    @php
                                        $historyBadge = match ($history->aksi) {
                                            'tambah' => 'bg-emerald-100 text-emerald-700',
                                            'hapus' => 'bg-[#ffdad6] text-[#93000a]',
                                            default => 'bg-amber-100 text-amber-700',
                                        };
                                    @endphp
                                    <article class="flex gap-4 rounded-xl border border-[#bec7d2] bg-[#f8f9ff] p-4">
                                        <span class="mt-0.5 grid h-10 w-10 shrink-0 place-items-center rounded-lg bg-white text-[#006191] shadow-sm ring-1 ring-[#bec7d2]">
                                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none">
                                                <path d="M4 12a8 8 0 1 0 2.3-5.7L4 8.6M4 4v4.6h4.6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                                <path d="M12 7v5l3 2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                            </svg>
                                        </span>
                                        <div class="min-w-0 flex-1">
                                            <div class="flex flex-wrap items-center gap-2">
                                                <span class="rounded-full px-2.5 py-1 text-[9px] font-bold uppercase tracking-[0.1em] {{ $historyBadge }}">{{ $history->aksi }}</span>
                                                <span class="text-[10px] font-bold uppercase tracking-[0.08em] text-[#6f7881]">{{ str_replace('_', ' ', $history->entitas) }}</span>
                                            </div>
                                            <p class="mt-2 text-sm font-semibold leading-6 text-[#0b1c30]">{{ $history->deskripsi }}</p>
                                            <p class="mt-1 text-xs text-[#3f4850]">
                                                {{ $history->user?->nama ?? 'Sistem' }} • {{ $history->created_at?->translatedFormat('d M Y, H:i') }}
                                            </p>
                                        </div>
                                    </article>
                                @empty
                                    <div class="py-12 text-center">
                                        <p class="mt-3 font-bold text-[#0b1c30]">Belum ada riwayat perubahan</p>
                                        <p class="mt-1 text-sm text-[#3f4850]">Aktivitas pengelolaan nominal dan rekening akan tercatat otomatis.</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </article>
                </div>
            </div>
        </template>
    </div>
@endsection
