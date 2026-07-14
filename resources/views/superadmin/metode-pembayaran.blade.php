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
                action: @js($editBankId ? route('superadmin.metode-pembayaran.bank.update', $editBankId) : ''),
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
    >
        <section class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <span class="inline-flex items-center gap-2 rounded-full bg-sky-100 px-3 py-1 text-[10px] font-bold uppercase tracking-[0.16em] text-sky-700">
                    <span class="h-1.5 w-1.5 rounded-full bg-sky-500"></span>
                    Metode Pembayaran
                </span>

                <h1 class="mt-3 text-2xl font-extrabold tracking-tight text-slate-950 sm:text-3xl">
                    Manajemen Rekening Bank
                </h1>

                <p class="mt-1 max-w-3xl text-sm leading-6 text-slate-500">
                    Kelola nominal administrasi dan rekening bank resmi CV Natusi untuk transaksi pendaftaran peserta magang.
                </p>
            </div>

            <button
                type="button"
                @click="historyOpen = true"
                class="inline-flex shrink-0 items-center justify-center gap-2 rounded-xl border border-sky-200 bg-white px-4 py-2.5 text-sm font-bold text-sky-700 shadow-sm transition hover:-translate-y-0.5 hover:border-sky-300 hover:bg-sky-50"
            >
                <svg class="h-[18px] w-[18px]" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M4 12a8 8 0 1 0 2.3-5.7L4 8.6M4 4v4.6h4.6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M12 7v5l3 2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                </svg>
                Riwayat Perubahan
            </button>
        </section>

        @if ($errors->any())
            <div class="mt-5 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                <p class="font-bold">Data belum dapat disimpan.</p>
                <p class="mt-1">Periksa kembali kolom yang ditandai pada formulir.</p>
            </div>
        @endif

        <section class="mt-5 overflow-hidden rounded-3xl border border-sky-100/90 bg-white/95 shadow-[0_20px_50px_rgba(15,52,94,0.08)] backdrop-blur">
            <div class="border-l-4 border-sky-600 px-5 py-5 sm:px-6">
                <div class="flex items-center gap-3">
                    <span class="grid h-10 w-10 place-items-center rounded-xl bg-sky-100 text-sky-700">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M5 7h14v10H5V7Zm3 0V5h8v2M8 12h8" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>
                    <div>
                        <h2 class="text-base font-extrabold text-slate-950">Kelola Jumlah Pembayaran</h2>
                        <p class="mt-0.5 text-xs text-slate-500">Nominal ini digunakan sebagai biaya pendaftaran atau administrasi magang.</p>
                    </div>
                </div>

                <form
                    method="POST"
                    action="{{ route('superadmin.metode-pembayaran.nominal.update') }}"
                    class="mt-5 grid gap-4 lg:grid-cols-[minmax(0,1fr)_auto] lg:items-end"
                >
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="form_context" value="nominal">

                    <div>
                        <label for="jumlah_nominal" class="mb-1.5 block text-xs font-bold text-slate-700">
                            Biaya Pendaftaran / Administrasi Magang
                        </label>
                        <div class="relative max-w-2xl">
                            <span class="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-sm font-bold text-slate-500">Rp</span>
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
                                class="w-full rounded-xl border-slate-300 py-3 pl-12 pr-4 text-sm focus:border-sky-500 focus:ring-sky-500"
                            >
                        </div>
                        @if ($oldContext === 'nominal')
                            @error('jumlah_nominal')
                                <p class="mt-1.5 text-xs font-medium text-rose-600">{{ $message }}</p>
                            @enderror
                        @endif
                    </div>

                    <button
                        type="submit"
                        class="inline-flex h-11 items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-sky-600 to-blue-700 px-6 text-sm font-bold text-white shadow-[0_10px_24px_rgba(2,132,199,0.24)] transition hover:-translate-y-0.5 hover:from-sky-700 hover:to-blue-800"
                    >
                        <svg class="h-[18px] w-[18px]" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M5 4h12l2 2v14H5V4Zm3 0v6h8V4M8 16h8" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        Simpan Nominal
                    </button>
                </form>
            </div>
        </section>

        <section class="mt-5 overflow-hidden rounded-3xl border border-sky-100/90 bg-white/95 shadow-[0_20px_50px_rgba(15,52,94,0.08)] backdrop-blur">
            <div class="border-l-4 border-sky-600 px-5 py-5 sm:px-6">
                <div class="flex items-center gap-3">
                    <span class="grid h-10 w-10 place-items-center rounded-xl bg-sky-100 text-sky-700">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M4 9h16M6 9V6h12v3M5 9v10h14V9M8 13h3" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>
                    <div>
                        <h2 class="text-base font-extrabold text-slate-950">Tambah Metode Pembayaran</h2>
                        <p class="mt-0.5 text-xs text-slate-500">Masukkan rekening resmi yang dapat dipilih peserta saat melakukan pembayaran.</p>
                    </div>
                </div>

                <form
                    method="POST"
                    action="{{ route('superadmin.metode-pembayaran.bank.store') }}"
                    class="mt-5 grid gap-4 xl:grid-cols-[1fr_1fr_1fr_auto] xl:items-end"
                >
                    @csrf
                    <input type="hidden" name="form_context" value="create">

                    <div>
                        <label for="create_nama_bank" class="mb-1.5 block text-xs font-bold text-slate-700">Nama Bank</label>
                        <select
                            id="create_nama_bank"
                            name="nama_bank"
                            required
                            class="w-full rounded-xl border-slate-300 py-3 text-sm focus:border-sky-500 focus:ring-sky-500"
                        >
                            <option value="">Pilih Bank</option>
                            @foreach ($bankOptions as $code => $bankName)
                                <option value="{{ $bankName }}" @selected($oldContext === 'create' && old('nama_bank') === $bankName)>
                                    {{ $code }} — {{ $bankName }}
                                </option>
                            @endforeach
                        </select>
                        @if ($oldContext === 'create')
                            @error('nama_bank')
                                <p class="mt-1.5 text-xs font-medium text-rose-600">{{ $message }}</p>
                            @enderror
                        @endif
                    </div>

                    <div>
                        <label for="create_no_rekening" class="mb-1.5 block text-xs font-bold text-slate-700">Nomor Rekening</label>
                        <input
                            id="create_no_rekening"
                            name="no_rekening"
                            type="text"
                            inputmode="numeric"
                            maxlength="30"
                            value="{{ $oldContext === 'create' ? old('no_rekening') : '' }}"
                            placeholder="Masukkan nomor rekening"
                            required
                            class="w-full rounded-xl border-slate-300 py-3 text-sm focus:border-sky-500 focus:ring-sky-500"
                        >
                        @if ($oldContext === 'create')
                            @error('no_rekening')
                                <p class="mt-1.5 text-xs font-medium text-rose-600">{{ $message }}</p>
                            @enderror
                        @endif
                    </div>

                    <div>
                        <label for="create_nama_pemilik" class="mb-1.5 block text-xs font-bold text-slate-700">Atas Nama</label>
                        <input
                            id="create_nama_pemilik"
                            name="nama_pemilik"
                            type="text"
                            maxlength="100"
                            value="{{ $oldContext === 'create' ? old('nama_pemilik') : '' }}"
                            placeholder="Masukkan nama pemilik rekening"
                            required
                            class="w-full rounded-xl border-slate-300 py-3 text-sm uppercase focus:border-sky-500 focus:ring-sky-500"
                        >
                        @if ($oldContext === 'create')
                            @error('nama_pemilik')
                                <p class="mt-1.5 text-xs font-medium text-rose-600">{{ $message }}</p>
                            @enderror
                        @endif
                    </div>

                    <button
                        type="submit"
                        class="inline-flex h-11 items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-sky-600 to-blue-700 px-5 text-sm font-bold text-white shadow-[0_10px_24px_rgba(2,132,199,0.24)] transition hover:-translate-y-0.5 hover:from-sky-700 hover:to-blue-800"
                    >
                        <svg class="h-[18px] w-[18px]" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M5 4h12l2 2v14H5V4Zm3 0v6h8V4M8 16h8" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        Simpan Metode Pembayaran
                    </button>
                </form>
            </div>
        </section>

        <section class="mt-5 overflow-hidden rounded-3xl border border-sky-100/90 bg-white/95 shadow-[0_20px_50px_rgba(15,52,94,0.09)] backdrop-blur">
            <div class="flex flex-col gap-3 border-b border-sky-100 bg-gradient-to-r from-sky-50 via-blue-50 to-cyan-50 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center gap-3">
                    <span class="grid h-9 w-9 place-items-center rounded-xl bg-white text-sky-700 shadow-sm ring-1 ring-sky-100">
                        <svg class="h-[18px] w-[18px]" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M5 4h14v16H5V4Zm3 4h8M8 12h8M8 16h5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                        </svg>
                    </span>
                    <div>
                        <h2 class="text-sm font-extrabold uppercase tracking-[0.08em] text-slate-800">Daftar Rekening Pembayaran</h2>
                        @if ($search !== '')
                            <p class="mt-0.5 text-xs text-slate-500">Hasil pencarian “{{ $search }}”.</p>
                        @endif
                    </div>
                </div>

                <span class="inline-flex w-fit items-center gap-2 rounded-full bg-emerald-100 px-3 py-1.5 text-[10px] font-extrabold text-emerald-700">
                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                    {{ $totalBanks }} Rekening Aktif
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-sky-100/60">
                        <tr>
                            <th class="w-16 px-5 py-3.5 text-left text-[10px] font-extrabold uppercase tracking-[0.1em] text-slate-500">No</th>
                            <th class="px-5 py-3.5 text-left text-[10px] font-extrabold uppercase tracking-[0.1em] text-slate-500">Nama Bank</th>
                            <th class="px-5 py-3.5 text-left text-[10px] font-extrabold uppercase tracking-[0.1em] text-slate-500">Nomor Rekening</th>
                            <th class="px-5 py-3.5 text-left text-[10px] font-extrabold uppercase tracking-[0.1em] text-slate-500">Atas Nama</th>
                            <th class="w-28 px-5 py-3.5 text-center text-[10px] font-extrabold uppercase tracking-[0.1em] text-slate-500">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @forelse ($banks as $index => $bank)
                            @php
                                $bankCode = $bankCodeByName[$bank->nama_bank]
                                    ?? strtoupper(mb_substr(preg_replace('/[^A-Za-z]/', '', $bank->nama_bank), 0, 4));

                                $editPayload = [
                                    'action' => route('superadmin.metode-pembayaran.bank.update', $bank),
                                    'id' => $bank->id_bank,
                                    'nama_bank' => $bank->nama_bank,
                                    'no_rekening' => $bank->no_rekening,
                                    'nama_pemilik' => $bank->nama_pemilik,
                                ];
                            @endphp
                            <tr class="transition hover:bg-sky-50/70">
                                <td class="px-5 py-4 text-sm font-semibold text-slate-600">{{ $index + 1 }}</td>
                                <td class="px-5 py-4">
                                    <div class="flex min-w-[230px] items-center gap-3">
                                        <span class="inline-flex min-w-12 items-center justify-center rounded-lg bg-sky-100 px-2 py-1.5 text-[10px] font-extrabold text-sky-700 ring-1 ring-sky-200">
                                            {{ $bankCode }}
                                        </span>
                                        <span class="text-sm font-bold text-slate-900">{{ $bank->nama_bank }}</span>
                                    </div>
                                </td>
                                <td class="px-5 py-4 font-mono text-sm font-semibold tracking-[0.08em] text-slate-700">{{ $bank->no_rekening }}</td>
                                <td class="px-5 py-4 text-sm font-semibold text-slate-700">{{ $bank->nama_pemilik }}</td>
                                <td class="px-5 py-4">
                                    <div class="flex items-center justify-center gap-1">
                                        <button
                                            type="button"
                                            @click='openEdit(@js($editPayload))'
                                            class="grid h-9 w-9 place-items-center rounded-xl text-sky-700 transition hover:bg-sky-100"
                                            aria-label="Ubah rekening {{ $bank->nama_bank }}"
                                        >
                                            <svg class="h-[18px] w-[18px]" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                <path d="m5 16-.8 3.8L8 19l10-10-3-3L5 16ZM13.5 7.5l3 3" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                        </button>

                                        <button
                                            type="button"
                                            @click="$dispatch('open-delete-confirm', {
                                                action: @js(route('superadmin.metode-pembayaran.bank.destroy', $bank)),
                                                title: 'Hapus Rekening?',
                                                name: @js($bank->nama_bank . ' • ' . $bank->no_rekening),
                                                description: 'Rekening akan dihapus dari daftar metode pembayaran. Rekening yang sudah digunakan pada transaksi tidak dapat dihapus.',
                                                confirmText: 'Ya, Hapus Rekening'
                                            })"
                                            class="grid h-9 w-9 place-items-center rounded-xl text-rose-600 transition hover:bg-rose-50"
                                            aria-label="Hapus rekening {{ $bank->nama_bank }}"
                                        >
                                            <svg class="h-[18px] w-[18px]" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                <path d="M5 7h14M9 7V4.5h6V7M8 10v7M12 10v7M16 10v7M6.5 7l.7 12h9.6l.7-12" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-14 text-center">
                                    <span class="mx-auto grid h-14 w-14 place-items-center rounded-2xl bg-sky-50 text-sky-500">
                                        <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                            <path d="M4 9h16M6 9V6h12v3M5 9v10h14V9M8 13h3" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </span>
                                    <p class="mt-3 font-bold text-slate-800">
                                        {{ $search !== '' ? 'Rekening tidak ditemukan' : 'Belum ada rekening pembayaran' }}
                                    </p>
                                    <p class="mt-1 text-sm text-slate-500">
                                        {{ $search !== '' ? 'Coba gunakan kata kunci lain pada kolom pencarian.' : 'Tambahkan rekening resmi melalui formulir di atas.' }}
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="border-t border-sky-100 bg-sky-50/70 px-5 py-3 text-center text-[10px] font-medium text-slate-500">
                Hanya rekening yang terdaftar di sini yang akan tampil pada portal peserta magang.
            </div>
        </section>

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
                        class="w-full max-w-lg overflow-hidden rounded-3xl border border-sky-100 bg-white shadow-2xl"
                    >
                        <header class="flex items-start justify-between gap-4 bg-gradient-to-r from-sky-600 to-blue-700 px-6 py-5 text-white">
                            <div>
                                <h2 id="edit-bank-title" class="text-xl font-extrabold">Ubah Rekening Bank</h2>
                                <p class="mt-1 text-sm text-sky-100">Perbarui bank, nomor rekening, atau nama pemilik.</p>
                            </div>
                            <button type="button" @click="editOpen = false" class="rounded-xl p-2 text-white/80 transition hover:bg-white/10 hover:text-white" aria-label="Tutup modal">
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
                                    <label for="edit_nama_bank" class="mb-1.5 block text-sm font-bold text-slate-700">Nama Bank</label>
                                    <select id="edit_nama_bank" name="nama_bank" x-model="editBank.nama_bank" required class="w-full rounded-xl border-slate-300 focus:border-sky-500 focus:ring-sky-500">
                                        <option value="">Pilih Bank</option>
                                        @foreach ($bankOptions as $code => $bankName)
                                            <option value="{{ $bankName }}">{{ $code }} — {{ $bankName }}</option>
                                        @endforeach
                                    </select>
                                    @if ($oldContext === 'edit')
                                        @error('nama_bank')<p class="mt-1.5 text-xs font-medium text-rose-600">{{ $message }}</p>@enderror
                                    @endif
                                </div>

                                <div>
                                    <label for="edit_no_rekening" class="mb-1.5 block text-sm font-bold text-slate-700">Nomor Rekening</label>
                                    <input id="edit_no_rekening" name="no_rekening" type="text" inputmode="numeric" maxlength="30" x-model="editBank.no_rekening" required class="w-full rounded-xl border-slate-300 focus:border-sky-500 focus:ring-sky-500">
                                    @if ($oldContext === 'edit')
                                        @error('no_rekening')<p class="mt-1.5 text-xs font-medium text-rose-600">{{ $message }}</p>@enderror
                                    @endif
                                </div>

                                <div>
                                    <label for="edit_nama_pemilik" class="mb-1.5 block text-sm font-bold text-slate-700">Atas Nama</label>
                                    <input id="edit_nama_pemilik" name="nama_pemilik" type="text" maxlength="100" x-model="editBank.nama_pemilik" required class="w-full rounded-xl border-slate-300 uppercase focus:border-sky-500 focus:ring-sky-500">
                                    @if ($oldContext === 'edit')
                                        @error('nama_pemilik')<p class="mt-1.5 text-xs font-medium text-rose-600">{{ $message }}</p>@enderror
                                    @endif
                                </div>
                            </div>

                            <div class="mt-6 flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
                                <button type="button" @click="editOpen = false" class="rounded-xl border border-slate-200 px-5 py-2.5 text-sm font-bold text-slate-600 transition hover:bg-slate-50">Batal</button>
                                <button type="submit" class="rounded-xl bg-gradient-to-r from-sky-600 to-blue-700 px-5 py-2.5 text-sm font-bold text-white shadow-lg transition hover:-translate-y-0.5">Simpan Perubahan</button>
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
                        class="flex max-h-[85vh] w-full max-w-3xl flex-col overflow-hidden rounded-3xl border border-sky-100 bg-white shadow-2xl"
                    >
                        <header class="flex shrink-0 items-start justify-between gap-4 bg-gradient-to-r from-sky-600 to-blue-700 px-6 py-5 text-white">
                            <div>
                                <h2 id="history-title" class="text-xl font-extrabold">Riwayat Perubahan Pembayaran</h2>
                                <p class="mt-1 text-sm text-sky-100">Menampilkan maksimal 30 aktivitas terbaru.</p>
                            </div>
                            <button type="button" @click="historyOpen = false" class="rounded-xl p-2 text-white/80 transition hover:bg-white/10 hover:text-white" aria-label="Tutup riwayat">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none"><path d="m6 6 12 12M18 6 6 18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                            </button>
                        </header>

                        <div class="min-h-0 flex-1 overflow-y-auto p-6">
                            <div class="space-y-3">
                                @forelse ($histories as $history)
                                    @php
                                        $historyBadge = match ($history->aksi) {
                                            'tambah' => 'bg-emerald-100 text-emerald-700',
                                            'hapus' => 'bg-rose-100 text-rose-700',
                                            default => 'bg-amber-100 text-amber-700',
                                        };
                                    @endphp
                                    <article class="flex gap-4 rounded-2xl border border-slate-100 bg-slate-50/70 p-4">
                                        <span class="mt-0.5 grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-white text-sky-700 shadow-sm ring-1 ring-slate-100">
                                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                <path d="M4 12a8 8 0 1 0 2.3-5.7L4 8.6M4 4v4.6h4.6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                                <path d="M12 7v5l3 2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                            </svg>
                                        </span>
                                        <div class="min-w-0 flex-1">
                                            <div class="flex flex-wrap items-center gap-2">
                                                <span class="rounded-full px-2.5 py-1 text-[9px] font-extrabold uppercase tracking-[0.1em] {{ $historyBadge }}">{{ $history->aksi }}</span>
                                                <span class="text-[10px] font-bold uppercase tracking-[0.08em] text-slate-400">{{ str_replace('_', ' ', $history->entitas) }}</span>
                                            </div>
                                            <p class="mt-2 text-sm font-semibold leading-6 text-slate-800">{{ $history->deskripsi }}</p>
                                            <p class="mt-1 text-xs text-slate-500">
                                                {{ $history->user?->nama ?? 'Sistem' }} • {{ $history->created_at?->translatedFormat('d M Y, H:i') }}
                                            </p>
                                        </div>
                                    </article>
                                @empty
                                    <div class="py-12 text-center">
                                        <span class="mx-auto grid h-14 w-14 place-items-center rounded-2xl bg-sky-50 text-sky-500">
                                            <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none"><path d="M4 12a8 8 0 1 0 2.3-5.7L4 8.6M4 4v4.6h4.6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                        </span>
                                        <p class="mt-3 font-bold text-slate-800">Belum ada riwayat perubahan</p>
                                        <p class="mt-1 text-sm text-slate-500">Aktivitas pengelolaan nominal dan rekening akan tercatat otomatis.</p>
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
