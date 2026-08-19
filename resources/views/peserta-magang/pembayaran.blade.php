@extends('layouts.portal')

@section('title', 'Pembayaran')

@section('content')
    <section class="mb-6">
        <h1 class="mt-5 mb-1 text-2xl font-bold text-slate-900 md:text-3xl">Pembayaran</h1>
        <p class="text-sm text-slate-500">Kelola pembayaran bulanan magang dan pantau periode yang sudah lunas.</p>
    </section>

    @if (session('success'))
        <div class="mb-5 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="mb-5 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">
            {{ session('error') }}
        </div>
    @endif

    {{-- Status pembayaran bulan ini --}}
    <div class="mb-6 grid gap-4 md:grid-cols-2">
        <div class="rounded-2xl border {{ $lunasBulanIni ? 'border-emerald-200 bg-emerald-50' : 'border-amber-200 bg-amber-50' }} p-5">
            <div class="flex items-start gap-3">
                <span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl {{ $lunasBulanIni ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                    <span class="material-symbols-outlined text-[21px]">{{ $lunasBulanIni ? 'check_circle' : 'schedule' }}</span>
                </span>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.12em] {{ $lunasBulanIni ? 'text-emerald-700' : 'text-amber-700' }}">Status bulan ini</p>
                    <h3 class="mt-1 text-lg font-semibold text-slate-900">{{ $lunasBulanIni ? 'Lunas untuk bulan ini' : 'Belum lunas bulan ini' }}</h3>
                    @if ($lunasSampai)
                        <p class="mt-1 text-sm text-slate-600">Pembayaran tercatat lunas sampai <span class="font-medium">{{ $lunasSampai }}</span>.</p>
                    @else
                        <p class="mt-1 text-sm text-slate-600">Belum ada periode pembayaran yang terverifikasi lunas.</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-sky-200 bg-sky-50 p-5">
            <p class="text-xs font-semibold uppercase tracking-[0.12em] text-sky-700">Periode pembayaran berikutnya</p>
            <h3 class="mt-1 text-lg font-semibold text-slate-900">{{ $periodeMulaiBerikutnya->translatedFormat('F Y') }}</h3>
            @if ($periodeBerikutnyaMenunggu)
                <p class="mt-1 text-sm text-slate-600">Bukti untuk periode ini sedang menunggu verifikasi admin.</p>
            @elseif ($maxBulanPilihan > 0)
                <p class="mt-1 text-sm text-slate-600">Anda dapat membayar sampai {{ $maxBulanPilihan }} bulan berturut-turut sekaligus.</p>
            @else
                <p class="mt-1 text-sm text-slate-600">Seluruh periode yang tersedia sudah terbayar atau sedang diproses.</p>
            @endif
        </div>
    </div>

    {{-- QRIS --}}
    <div class="mb-6 rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-100 p-5">
            <h3 class="text-lg font-semibold text-slate-900">Cara Pembayaran</h3>
            <p class="text-sm text-slate-500">Scan QRIS, bayar sesuai total periode yang dipilih, lalu unggah bukti pembayaran.</p>
        </div>

        <div class="flex flex-col items-center p-8">
            @if ($qris?->qris_image)
                <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white p-3 shadow-sm">
                    <img src="{{ asset('storage/'.$qris->qris_image) }}" alt="QR Pembayaran" class="h-[240px] w-[240px] object-contain">
                </div>
                <span class="mt-3 inline-flex items-center gap-1 rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">
                    <span class="material-symbols-outlined text-[16px]">qr_code_scanner</span>
                    Scan QR & Bayar
                </span>
                @if ($nominalAktif)
                    <p class="mt-3 text-sm text-slate-500">Iuran per bulan: <span class="font-semibold text-slate-900">Rp {{ number_format($nominalAktif->jumlah_nominal, 0, ',', '.') }}</span></p>
                @endif
            @else
                <span class="material-symbols-outlined mb-2 text-[56px] text-slate-300">qr_code_2</span>
                <p class="text-sm text-slate-500">QR pembayaran belum diunggah admin.</p>
            @endif
        </div>
    </div>

    {{-- Form pembayaran --}}
    <div class="mb-6 rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-100 p-5">
            <h3 class="text-lg font-semibold text-slate-900">Unggah Bukti Pembayaran</h3>
            <p class="text-sm text-slate-500">Pilih berapa bulan yang ingin dibayar. Periode selalu dimulai dari bulan pertama yang belum lunas.</p>
        </div>

        @if ($periodeBerikutnyaMenunggu)
            <div class="p-6">
                <div class="rounded-2xl border border-amber-200 bg-amber-50 p-5 text-sm text-amber-800">
                    Bukti pembayaran untuk <strong>{{ $periodeMulaiBerikutnya->translatedFormat('F Y') }}</strong> sedang menunggu verifikasi. Anda dapat melakukan pembayaran berikutnya setelah admin memproses transaksi ini.
                </div>
            </div>
        @elseif ($maxBulanPilihan < 1)
            <div class="p-6">
                <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-5 text-sm text-emerald-800">
                    Tidak ada periode pembayaran yang perlu dibayar saat ini.
                </div>
            </div>
        @else
            <form method="POST" action="{{ route('peserta-magang.pembayaran.store') }}" enctype="multipart/form-data" class="grid grid-cols-1 gap-6 p-6 lg:grid-cols-2" id="payment-form">
                @csrf

                <div class="space-y-4">
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase text-slate-600">Periode mulai</label>
                        <input type="text" value="{{ $periodeMulaiBerikutnya->translatedFormat('F Y') }}" readonly class="w-full rounded-xl border-slate-200 bg-slate-50 text-sm text-slate-700">
                        <p class="mt-1 text-xs text-slate-400">Periode dimulai otomatis dari bulan pertama yang belum lunas.</p>
                    </div>

                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase text-slate-600">Bayar untuk berapa bulan?</label>
                        <select name="jumlah_bulan" id="jumlah-bulan" class="w-full rounded-xl border-slate-200 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100" required>
                            @for ($i = 1; $i <= $maxBulanPilihan; $i++)
                                @php
                                    $akhir = $periodeMulaiBerikutnya->copy()->addMonthsNoOverflow($i - 1);
                                    $labelPeriode = $i === 1
                                        ? $periodeMulaiBerikutnya->translatedFormat('F Y')
                                        : $periodeMulaiBerikutnya->translatedFormat('F Y') . ' – ' . $akhir->translatedFormat('F Y');
                                    $totalOption = ($nominalAktif?->jumlah_nominal ?? 0) * $i;
                                @endphp
                                <option value="{{ $i }}" @selected((int) old('jumlah_bulan', 1) === $i)>
                                    {{ $i }} bulan · {{ $labelPeriode }} · Rp {{ number_format($totalOption, 0, ',', '.') }}
                                </option>
                            @endfor
                        </select>
                        @error('jumlah_bulan')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                    </div>

                    <div class="rounded-2xl bg-sky-50 p-4 ring-1 ring-sky-100">
                        <p class="text-xs font-semibold uppercase tracking-[0.1em] text-sky-700">Total yang harus dibayar</p>
                        <p class="mt-1 text-2xl font-semibold text-slate-900" id="total-pembayaran">Rp {{ number_format($nominalAktif?->jumlah_nominal ?? 0, 0, ',', '.') }}</p>
                        <p class="mt-1 text-xs text-slate-500" id="periode-ringkas">Untuk {{ $periodeMulaiBerikutnya->translatedFormat('F Y') }}</p>
                    </div>

                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase text-slate-600">Tanggal Pembayaran</label>
                        <input type="date" name="tgl_bayar" value="{{ old('tgl_bayar', now()->toDateString()) }}" class="w-full rounded-xl border-slate-200 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100" required>
                        @error('tgl_bayar')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase text-slate-600">Catatan (opsional)</label>
                        <textarea name="keterangan" rows="3" class="w-full rounded-xl border-slate-200 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100" placeholder="Catatan pembayaran jika diperlukan...">{{ old('keterangan') }}</textarea>
                    </div>
                </div>

                <div class="flex flex-col">
                    <label class="mb-1 block text-xs font-semibold uppercase text-slate-600">Bukti Pembayaran</label>
                    <div class="flex flex-1 flex-col items-center justify-center rounded-2xl border-2 border-dashed border-slate-300 bg-slate-50 p-8 transition hover:border-blue-400 hover:bg-slate-100">
                        <span class="material-symbols-outlined mb-2 text-[38px] text-blue-600">upload_file</span>
                        <input type="file" name="bukti_transfer" accept=".jpg,.jpeg,.png,.pdf" class="text-sm" required>
                        <p class="mt-2 text-xs text-slate-400">JPG, PNG, atau PDF · Maksimal 5 MB</p>
                    </div>
                    @error('bukti_transfer')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror

                    <button type="submit" class="mt-4 flex w-full items-center justify-center gap-2 rounded-xl bg-blue-600 py-3 font-semibold text-white shadow-lg shadow-blue-200/40 transition hover:bg-blue-700">
                        <span class="material-symbols-outlined text-[18px]">send</span>
                        Kirim Bukti Pembayaran
                    </button>
                </div>
            </form>
        @endif
    </div>

    {{-- Riwayat --}}
    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-100 p-5">
            <h3 class="text-lg font-semibold text-slate-900">Riwayat Pembayaran</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full min-w-[900px] border-collapse text-left">
                <thead>
                    <tr class="border-b border-slate-200 bg-slate-50">
                        <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wide text-slate-500">Tanggal Bayar</th>
                        <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wide text-slate-500">Periode</th>
                        <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wide text-slate-500">Metode</th>
                        <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wide text-slate-500">Jumlah</th>
                        <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wide text-slate-500">Status</th>
                        <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wide text-slate-500">Bukti</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($riwayat as $item)
                        @php
                            $statusLabel = match ($item->status) {
                                'lunas' => 'Lunas',
                                'ditolak' => 'Ditolak',
                                default => 'Menunggu',
                            };
                            $statusClass = match ($item->status) {
                                'lunas' => 'bg-green-100 text-green-700',
                                'ditolak' => 'bg-rose-100 text-rose-700',
                                default => 'bg-amber-100 text-amber-700',
                            };
                            $sudahDiselesaikan = in_array($item->id_pembayaran, $resolvedRejectedIds, true);
                        @endphp
                        <tr class="transition hover:bg-slate-50">
                            <td class="px-6 py-4 text-sm text-slate-700">{{ $item->tgl_bayar?->format('d M Y') ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm text-slate-700">
                                {{ $periodeLabels[$item->id_pembayaran] ?? '-' }}
                                @if (($item->jumlah_bulan ?? 1) > 1)
                                    <span class="ml-1 text-xs text-slate-400">({{ $item->jumlah_bulan }} bulan)</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-700">QRIS</td>
                            <td class="px-6 py-4 text-sm text-slate-900">Rp {{ number_format($item->nominal, 0, ',', '.') }}</td>
                            <td class="px-6 py-4">
                                <span class="rounded px-2 py-1 text-[10px] font-semibold uppercase {{ $statusClass }}">{{ $statusLabel }}</span>
                                @if ($sudahDiselesaikan)
                                    <span class="ml-1 text-[10px] text-emerald-600">sudah diselesaikan</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if ($item->bukti_transfer)
                                    <a href="{{ Storage::url($item->bukti_transfer) }}" target="_blank" class="text-sm font-medium text-blue-600 hover:underline">Lihat</a>
                                @else
                                    <span class="text-xs text-slate-400">-</span>
                                @endif
                            </td>
                        </tr>

                        {{-- Catatan penolakan hanya ditampilkan selama periode tersebut belum dilunasi --}}
                        @if ($item->status === 'ditolak' && $item->keterangan && ! $sudahDiselesaikan)
                            <tr class="bg-rose-50/60">
                                <td colspan="6" class="px-6 py-3">
                                    <div class="flex items-start gap-2">
                                        <span class="material-symbols-outlined mt-0.5 text-[16px] text-rose-500">info</span>
                                        <p class="text-xs text-rose-700"><span class="font-semibold">Catatan Admin:</span> {{ $item->keterangan }}</p>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-sm text-slate-500">Belum ada riwayat pembayaran.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($riwayat->hasPages())
            <div class="border-t border-slate-100 p-4">{{ $riwayat->links() }}</div>
        @endif
    </div>

    @if ($nominalAktif && $maxBulanPilihan > 0 && ! $periodeBerikutnyaMenunggu)
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const select = document.getElementById('jumlah-bulan');
                const total = document.getElementById('total-pembayaran');
                const period = document.getElementById('periode-ringkas');
                if (!select || !total || !period) return;

                const monthly = {{ (int) $nominalAktif->jumlah_nominal }};
                const startYear = {{ $periodeMulaiBerikutnya->year }};
                const startMonth = {{ $periodeMulaiBerikutnya->month - 1 }};
                const formatter = new Intl.NumberFormat('id-ID');
                const monthFormatter = new Intl.DateTimeFormat('id-ID', { month: 'long', year: 'numeric' });

                function updateSummary() {
                    const count = Number(select.value || 1);
                    const start = new Date(startYear, startMonth, 1);
                    const end = new Date(startYear, startMonth + count - 1, 1);
                    total.textContent = 'Rp ' + formatter.format(monthly * count);
                    period.textContent = count === 1
                        ? 'Untuk ' + monthFormatter.format(start)
                        : 'Untuk ' + monthFormatter.format(start) + ' – ' + monthFormatter.format(end);
                }

                select.addEventListener('change', updateSummary);
                updateSummary();
            });
        </script>
    @endif
@endsection
