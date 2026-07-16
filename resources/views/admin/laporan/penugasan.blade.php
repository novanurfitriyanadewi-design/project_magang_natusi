@extends('layouts.portal')

@section('title', 'Laporan Penugasan - CV Natusi')

@section('content')
<div class="p-6 space-y-6">

    {{-- Header & Filter --}}
    <section class="flex flex-col md:flex-row justify-between items-start md:items-end gap-4">
        <div>
            <h2 class="text-2xl font-bold text-[#0b1c30]">Laporan Penugasan</h2>
            <p class="text-slate-500 text-sm">Evaluasi progres dan kualitas pengerjaan tugas peserta.</p>
        </div>

        <form method="GET" class="flex flex-wrap items-center gap-3">
            <select name="jenis_tugas" onchange="this.form.submit()" class="bg-white border border-slate-200 rounded-lg px-3 py-2 text-sm">
                <option value="">Semua Jenis Tugas</option>
                @foreach ($jenisTugasList as $j)
                    <option value="{{ $j }}" @selected($jenisTugas === $j)>{{ $j }}</option>
                @endforeach
            </select>

            <select name="status_filter" onchange="this.form.submit()" class="bg-white border border-slate-200 rounded-lg px-3 py-2 text-sm">
                <option value="">Semua Status</option>
                <option value="aktif" @selected($statusFilter === 'aktif')>Aktif</option>
                <option value="selesai" @selected($statusFilter === 'selesai')>Selesai</option>
                <option value="terlambat" @selected($statusFilter === 'terlambat')>Terlambat</option>
            </select>

            <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama tugas"
                   class="bg-white border border-slate-200 rounded-lg px-3 py-2 text-sm">

            <button type="submit" class="bg-[#006191] text-white px-4 py-2 rounded-lg text-sm font-semibold">Filter</button>
        </form>
    </section>

    {{-- Statistik --}}
    <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-4 rounded-xl border border-slate-200 border-l-4 border-l-[#006191]">
            <span class="text-xs font-bold text-slate-500 uppercase">Task Completion</span>
            <p class="text-2xl font-bold mt-1">{{ $stats['completion_rate'] }}%</p>
        </div>
        <div class="bg-white p-4 rounded-xl border border-slate-200 border-l-4 border-l-[#bb0014]">
            <span class="text-xs font-bold text-slate-500 uppercase">Average Grade</span>
            <p class="text-2xl font-bold mt-1">{{ $stats['avg_score'] }}</p>
        </div>
        <div class="bg-white p-4 rounded-xl border border-slate-200 border-l-4 border-l-[#4d5d70]">
            <span class="text-xs font-bold text-slate-500 uppercase">Pending Review</span>
            <p class="text-2xl font-bold mt-1">{{ $stats['pending_review'] }}</p>
        </div>
        <div class="bg-white p-4 rounded-xl border border-slate-200 border-l-4 border-l-[#0b1c30]">
            <span class="text-xs font-bold text-slate-500 uppercase">Total Tasks</span>
            <p class="text-2xl font-bold mt-1">{{ $stats['total_tugas'] }}</p>
        </div>
    </section>

    {{-- Grafik --}}
    <section class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <div class="lg:col-span-2 bg-white p-6 rounded-xl border border-slate-200">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-lg font-semibold">Submission vs Overdue</h3>
                <div class="flex gap-4 text-xs">
                    <span class="flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-[#006191] inline-block"></span> Submitted</span>
                    <span class="flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-[#bb0014] inline-block"></span> Overdue</span>
                </div>
            </div>

            <div class="h-56 flex items-end justify-between gap-2 px-2">
                @php $bulanLabel = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des']; @endphp
                @foreach ($monthlySubmitted as $bulan => $submitted)
                    @php
                        $overdue  = $monthlyOverdue[$bulan] ?? 0;
                        $hSub     = $submitted > 0 ? max(4, ($submitted / $chartMax) * 100) : 2;
                        $hOver    = $overdue > 0 ? max(4, ($overdue / $chartMax) * 100) : 2;
                    @endphp
                    <div class="flex-1 flex flex-col items-center gap-1">
                        <div class="flex items-end gap-1 h-full">
                            <div class="w-3 bg-[#006191] rounded-t" style="height: {{ $hSub }}%;" title="Submitted: {{ $submitted }}"></div>
                            <div class="w-3 bg-[#bb0014] rounded-t" style="height: {{ $hOver }}%;" title="Overdue: {{ $overdue }}"></div>
                        </div>
                        <span class="text-[10px] text-slate-500 uppercase">{{ $bulanLabel[$bulan - 1] }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="bg-[#006191] p-6 rounded-xl text-white flex flex-col justify-center">
            <h3 class="text-lg font-semibold mb-2">Performance Summary</h3>
            <p class="text-sm opacity-80 mb-4">Tingkat penyelesaian tugas peserta saat ini {{ $stats['completion_rate'] }}%.</p>
            <div class="space-y-3">
                <div class="bg-white/10 p-3 rounded-lg">
                    <p class="text-xs uppercase opacity-70">Rata-rata nilai</p>
                    <p class="text-lg font-bold">{{ $stats['avg_score'] }}</p>
                </div>
                <div class="bg-white/10 p-3 rounded-lg">
                    <p class="text-xs uppercase opacity-70">Menunggu review</p>
                    <p class="text-lg font-bold">{{ $stats['pending_review'] }} tugas</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Tabel --}}
    <section class="bg-white rounded-xl border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200">
            <h3 class="text-lg font-semibold">Data Penugasan Detail</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 text-xs uppercase text-slate-500">
                        <th class="px-6 py-3">Nama Tugas</th>
                        <th class="px-6 py-3">Total Submission</th>
                        <th class="px-6 py-3">Overdue</th>
                        <th class="px-6 py-3">Avg. Score</th>
                        <th class="px-6 py-3">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($tugasList as $t)
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-3">
                                <div class="font-semibold">{{ $t->judul }}</div>
                                <div class="text-xs text-slate-500">{{ $t->jenis_tugas }}</div>
                            </td>
                            <td class="px-6 py-3">{{ $t->total_submitted }}/{{ $t->total_peserta }}</td>
                            <td class="px-6 py-3">
                                <span class="{{ $t->overdue_count > 0 ? 'text-[#bb0014] font-semibold' : 'text-green-600' }}">{{ $t->overdue_count }}</span>
                            </td>
                            <td class="px-6 py-3">
                                <div class="flex items-center gap-2">
                                    <span class="font-semibold">{{ $t->avg_score }}</span>
                                    <div class="w-16 h-1.5 bg-slate-100 rounded-full overflow-hidden">
                                        <div class="h-full bg-[#006191]" style="width: {{ min(100, $t->avg_score) }}%"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-3">
                                @php
                                    $badge = match ($t->status) {
                                        'selesai' => 'bg-green-100 text-green-700',
                                        'terlambat' => 'bg-red-100 text-red-700',
                                        default => 'bg-blue-100 text-blue-700',
                                    };
                                @endphp
                                <span class="{{ $badge }} px-2 py-1 rounded text-[10px] font-bold uppercase">{{ ucfirst($t->status) }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-6 py-8 text-center text-slate-400">Belum ada data tugas.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t border-slate-200">
            {{ $tugasList->links() }}
        </div>
    </section>

</div>
@endsection