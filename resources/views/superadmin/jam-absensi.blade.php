@extends('layouts.portal')

@section('title', 'Kelola Jam Absensi')

@section('content')
<style>
.natusi-time-input::-webkit-calendar-picker-indicator {
    cursor: pointer;
    opacity: 0.65;
    filter: invert(41%) sepia(86%) saturate(1354%) hue-rotate(170deg) brightness(91%) contrast(92%);
}
.natusi-time-input:disabled::-webkit-calendar-picker-indicator {
    cursor: not-allowed;
    opacity: 0.3;
}
</style>

<div x-data="{
    clock: '',
    dateLabel: '',
    timer: null,
    reloading: false,
    serverActive: @js($isActive),
    openMinutes: @js(((int) substr($openTime, 0, 2) * 60) + (int) substr($openTime, 3, 2)),
    closeMinutes: @js(((int) substr($closeTime, 0, 2) * 60) + (int) substr($closeTime, 3, 2)),
    init() {
        this.updateClock();
        this.timer = setInterval(() => { this.updateClock(); }, 1000);
    },
    jakartaTimeParts() {
        const formatter = new Intl.DateTimeFormat('en-GB', {
            timeZone: 'Asia/Jakarta',
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
            hour12: false,
        });
        const parts = Object.fromEntries(
            formatter.formatToParts(new Date())
                .filter((part) => part.type !== 'literal')
                .map((part) => [part.type, part.value])
        );
        return {
            hour: Number(parts.hour),
            minute: Number(parts.minute),
            second: Number(parts.second),
        };
    },
    isActiveNow(hour, minute) {
        const currentMinutes = (hour * 60) + minute;
        if (this.openMinutes === this.closeMinutes) return false;
        if (this.openMinutes < this.closeMinutes) {
            return currentMinutes >= this.openMinutes && currentMinutes < this.closeMinutes;
        }
        return currentMinutes >= this.openMinutes || currentMinutes < this.closeMinutes;
    },
    updateClock() {
        const now = new Date();
        const parts = this.jakartaTimeParts();
        this.clock = new Intl.DateTimeFormat('id-ID', {
            timeZone: 'Asia/Jakarta',
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
            hour12: false,
        }).format(now);
        this.dateLabel = new Intl.DateTimeFormat('id-ID', {
            timeZone: 'Asia/Jakarta',
            day: '2-digit',
            month: 'long',
            year: 'numeric',
        }).format(now);
        const liveActive = this.isActiveNow(parts.hour, parts.minute);
        if (liveActive !== this.serverActive && !this.reloading) {
            this.reloading = true;
            window.location.reload();
        }
    },
}">

    {{-- Judul --}}
    <section>
        <h1 class="mt-5 text-2xl font-extrabold tracking-tight text-slate-950 sm:text-3xl">Kelola Jam Absensi</h1>
        <p class="mt-1 text-sm text-slate-500">Atur rentang waktu pendaftaran kehadiran harian untuk peserta magang dan karyawan.</p>
    </section>

    {{-- Konten utama --}}
    <section class="mt-5 grid items-stretch gap-5 lg:grid-cols-[minmax(0,1.35fr)_minmax(320px,0.85fr)]">

        {{-- Form pengaturan --}}
        <article class="h-full overflow-hidden rounded-3xl border border-sky-100/90 bg-white/95 shadow-[0_20px_50px_rgba(15,52,94,0.09)]">

            {{-- Header form --}}
            <header class="flex items-start justify-between gap-4 border-b border-sky-100 bg-gradient-to-r from-sky-50 via-blue-50 to-cyan-50 px-6 py-5">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-[0.18em] text-sky-700">Pengaturan Waktu</p>
                    <h2 class="mt-1 text-xl font-extrabold text-slate-950">Rentang Jam Absensi</h2>
                    <p class="mt-1 text-sm text-slate-500">Perubahan berlaku pada pendaftaran kehadiran berikutnya.</p>
                </div>
                <span class="grid h-12 w-12 shrink-0 place-items-center rounded-2xl bg-white text-sky-700 shadow-sm ring-1 ring-sky-100">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="8"/><path d="M12 7v5l3 2" stroke-linecap="round"/></svg>
                </span>
            </header>

            <form method="POST" action="{{ route('superadmin.jam-absensi.update') }}">
                @csrf
                @method('PUT')

                <div class="space-y-5 p-6">

                    {{-- Error validation --}}
                    @if ($errors->any())
                    <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                        <p class="font-bold">Pengaturan belum dapat disimpan.</p>
                        <ul class="mt-1 list-inside list-disc">
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    {{-- Locked notice --}}
                    @if ($isActive)
                    <div class="flex items-start gap-3 rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3">
                        <span class="mt-0.5 grid h-9 w-9 shrink-0 place-items-center rounded-xl bg-white text-amber-600 ring-1 ring-amber-200">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="6" y="10" width="12" height="10" rx="2"/><path d="M9 10V7a3 3 0 0 1 6 0v3" stroke-linecap="round"/></svg>
                        </span>
                        <div>
                            <p class="text-sm font-bold text-amber-800">Pengaturan sedang dikunci</p>
                            <p class="mt-1 text-sm leading-6 text-amber-700">Jam absensi tidak dapat diubah selama periode {{ $openTime }}–{{ $closeTime }} sedang berlangsung.</p>
                        </div>
                    </div>
                    @endif

                    {{-- Input fields --}}
                    <div class="grid gap-5 md:grid-cols-2">
                        <div>
                            <label for="jam-buka" class="mb-2 block text-sm font-bold text-slate-700">Jam buka absensi</label>
                            <div class="relative">
                                <svg class="pointer-events-none absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-sky-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="8"/><path d="M12 7v5l3 2" stroke-linecap="round"/></svg>
                                <input id="jam-buka" name="jam_buka" type="time" value="{{ old('jam_buka', $openTime) }}" required @disabled($isActive) class="natusi-time-input h-12 w-full rounded-xl border-slate-300 bg-white pl-12 pr-4 text-slate-700 focus:border-sky-500 focus:ring-sky-500 disabled:cursor-not-allowed disabled:bg-slate-100 disabled:text-slate-400">
                            </div>
                            <p class="mt-2 text-xs text-slate-500">Default sistem: 07:30 WIB</p>
                        </div>
                        <div>
                            <label for="jam-tutup" class="mb-2 block text-sm font-bold text-slate-700">Jam tutup absensi</label>
                            <div class="relative">
                                <svg class="pointer-events-none absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-sky-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="8"/><path d="M12 7v5l3 2" stroke-linecap="round"/></svg>
                                <input id="jam-tutup" name="jam_tutup" type="time" value="{{ old('jam_tutup', $closeTime) }}" required @disabled($isActive) class="natusi-time-input h-12 w-full rounded-xl border-slate-300 bg-white pl-12 pr-4 text-slate-700 focus:border-sky-500 focus:ring-sky-500 disabled:cursor-not-allowed disabled:bg-slate-100 disabled:text-slate-400">
                            </div>
                            <p class="mt-2 text-xs text-slate-500">Default sistem: 09:00 WIB</p>
                        </div>
                    </div>
                </div>

                {{-- Footer form --}}
                <footer class="flex flex-col-reverse gap-3 border-t border-sky-100 bg-slate-50/60 px-6 py-4 sm:flex-row sm:justify-end">
                    <button type="submit" form="reset-attendance-form" @disabled($isActive) class="rounded-xl border border-sky-100 bg-white px-4 py-2.5 text-sm font-bold text-sky-700 shadow-sm transition hover:bg-sky-50 disabled:cursor-not-allowed disabled:opacity-45">Reset ke Default</button>
                    <button type="submit" @disabled($isActive) class="inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-sky-600 to-blue-600 px-5 py-2.5 text-sm font-bold text-white shadow-[0_10px_24px_rgba(2,132,199,0.24)] transition hover:-translate-y-0.5 disabled:cursor-not-allowed disabled:opacity-45 disabled:hover:translate-y-0">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M5 4h12l2 2v14H5V4Zm3 0v5h8V4M8 16h8"/></svg>
                        Simpan Perubahan
                    </button>
                </footer>
            </form>

            <form id="reset-attendance-form" method="POST" action="{{ route('superadmin.jam-absensi.reset') }}" class="hidden">
                @csrf
                @method('PATCH')
            </form>
        </article>

        {{-- Status card --}}
        <aside class="relative h-full overflow-hidden rounded-3xl bg-gradient-to-br from-blue-800 via-sky-700 to-cyan-600 p-6 text-white shadow-[0_20px_50px_rgba(30,64,175,0.22)]">
            <div class="pointer-events-none absolute -right-16 -top-20 h-52 w-52 rounded-full border-[30px] border-white/[0.08]"></div>
            <div class="relative">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-[0.18em] text-cyan-100">Status Sistem</p>
                        <h2 class="mt-1 text-xl font-extrabold">{{ $statusLabel }}</h2>
                    </div>
                    <span @class([
                        'inline-flex items-center gap-2 rounded-full px-3 py-1.5 text-[10px] font-bold uppercase tracking-[0.12em] ring-1',
                        'bg-emerald-400/15 text-emerald-100 ring-emerald-200/20' => $isActive,
                        'bg-white/10 text-sky-100 ring-white/15' => !$isActive,
                    ])>
                        <span @class([
                            'h-2 w-2 rounded-full',
                            'bg-emerald-400 shadow-[0_0_0_5px_rgba(52,211,153,0.12)]' => $isActive,
                            'bg-sky-200/70' => !$isActive,
                        ])></span>
                        {{ $isActive ? 'Online' : 'Standby' }}
                    </span>
                </div>

                <div class="mt-6 rounded-2xl bg-white/10 p-5 ring-1 ring-white/15 backdrop-blur-sm">
                    <p class="text-xs font-semibold text-sky-100">Waktu sekarang</p>
                    <p class="mt-2 font-mono text-4xl font-extrabold tracking-tight" x-text="clock">{{ $now->format('H:i:s') }}</p>
                    <p class="mt-1 text-sm text-sky-100/80" x-text="dateLabel">{{ $now->translatedFormat('d F Y') }}</p>
                </div>

                <dl class="mt-5 divide-y divide-white/10 rounded-2xl border border-white/10 bg-white/[0.06] px-4">
                    <div class="flex items-center justify-between gap-4 py-3 text-sm">
                        <dt class="text-sky-100/75">Rentang aktif</dt>
                        <dd class="font-bold">{{ $openTime }}–{{ $closeTime }}</dd>
                    </div>
                    <div class="flex items-center justify-between gap-4 py-3 text-sm">
                        <dt class="text-sky-100/75">Terakhir diperbarui</dt>
                        <dd class="text-right font-bold">{{ $setting->updated_at?->translatedFormat('d M Y, H:i') ?? '-' }}</dd>
                    </div>
                    <div class="flex items-center justify-between gap-4 py-3 text-sm">
                        <dt class="text-sky-100/75">Diperbarui oleh</dt>
                        <dd class="text-right font-bold">{{ $setting->diperbarui_oleh ?? 'Sistem' }}</dd>
                    </div>
                </dl>
            </div>
        </aside>
    </section>
</div>
@endsection