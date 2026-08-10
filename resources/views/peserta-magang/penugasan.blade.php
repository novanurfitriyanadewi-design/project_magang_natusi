@extends('layouts.portal')

@section('title', 'Penugasan - InternHub CV Natusi')

@section('content')
@if(session('success'))
    <div class="mb-6 flex items-center justify-between rounded-xl border border-emerald-300 bg-emerald-50 px-4 py-3 text-emerald-800">
        <div class="flex items-center gap-2">
            <span class="material-symbols-outlined">check_circle</span>
            <span>{{ session('success') }}</span>
        </div>
        <button onclick="this.parentElement.remove()" class="font-bold text-emerald-800">&times;</button>
    </div>
@endif

<div class="grid grid-cols-1 gap-6 lg:grid-cols-12">
    <div class="flex flex-col gap-3 lg:col-span-5">
        <div class="mb-1 flex items-center justify-between">
            <h3 class="flex items-center gap-2 text-lg font-semibold text-slate-900">
                <span class="material-symbols-outlined text-blue-600">pending_actions</span>
                Tugas Aktif
            </h3>
            <form method="GET" action="{{ route('peserta-magang.penugasan.index') }}" id="filterForm">
                <div class="relative">
                    <select name="minggu" onchange="document.getElementById('filterForm').submit()" class="cursor-pointer appearance-none rounded-lg border border-slate-200 bg-slate-50 px-4 py-1.5 pr-10 text-sm font-semibold text-slate-600 outline-none transition-all focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                        <option value="all" {{ $selectedMinggu == 'all' ? 'selected' : '' }}>Semua Minggu</option>
                        @for ($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}" {{ $selectedMinggu == $i ? 'selected' : '' }}>Minggu {{ $i }}</option>
                        @endfor
                    </select>
                    <span class="material-symbols-outlined pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-[18px] text-slate-400">expand_more</span>
                </div>
            </form>
        </div>

        @forelse ($tugasAktif as $tugas)
            @php
                $pengumpulan = $tugas->pengumpulanTugas->first();
                $isSelected = $detailTugas && $detailTugas->id_tugas === $tugas->id_tugas;

                $badgeClass = 'bg-rose-100 text-rose-700';
                $statusText = 'Belum Selesai';

                if ($pengumpulan) {
                    if ($pengumpulan->status_pengumpulan === 'dikumpul') {
                        $badgeClass = 'bg-slate-200 text-slate-600';
                        $statusText = 'Sedang Dinilai';
                    } elseif ($pengumpulan->status_pengumpulan === 'draft') {
                        $badgeClass = 'bg-indigo-100 text-indigo-700';
                        $statusText = 'Draft / Sudah Dikumpul';
                    }
                }
            @endphp

            <a href="{{ route('peserta-magang.penugasan.index', ['tugas_id' => $tugas->id_tugas, 'minggu' => $selectedMinggu]) }}"
               class="block cursor-pointer rounded-2xl bg-white p-4 shadow-sm transition-all
                      {{ $isSelected ? 'border-l-4 border-blue-600 shadow-md' : 'border border-slate-200 opacity-80 hover:border-blue-300 hover:opacity-100' }}">
                <div class="mb-2 flex items-start justify-between">
                    <span class="{{ $badgeClass }} rounded px-2 py-1 text-[10px] font-bold uppercase tracking-wider">
                        {{ $statusText }}
                    </span>
                    <span class="flex items-center gap-1 text-xs text-slate-500">
                        <span class="material-symbols-outlined text-[14px]">event</span>
                        {{ $tugas->pengumpulan ? \Carbon\Carbon::parse($tugas->pengumpulan)->format('d M, H:i') : '-' }}
                    </span>
                </div>
                <h4 class="mb-1 font-semibold text-slate-900">{{ $tugas->judul }}</h4>
                <p class="line-clamp-2 text-sm text-slate-500">{{ $tugas->materi }}</p>
            </a>
        @empty
            <div class="rounded-2xl border border-slate-200 bg-white p-6 text-center text-slate-500">
                <span class="material-symbols-outlined mb-2 text-[48px] text-slate-300">task_alt</span>
                <p>Tidak ada tugas aktif untuk periode ini.</p>
            </div>
        @endforelse
    </div>

    <div class="space-y-6 lg:col-span-7">
        @if ($detailTugas)
            @php
                $userSubmission = $detailTugas->pengumpulanTugas->first();
                $isDeadlinePast = $detailTugas->pengumpulan && now()->gt($detailTugas->pengumpulan);
            @endphp
            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 bg-slate-50 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-blue-600">Detail Penugasan</h3>
                            <p class="text-sm text-slate-500">#TASK-NAT-{{ $detailTugas->id_tugas }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Deadline</p>
                            @if ($detailTugas->pengumpulan)
                                <p class="font-bold {{ $isDeadlinePast ? 'text-rose-600' : 'text-blue-600' }}">
                                    {{ $detailTugas->pengumpulan->diffForHumans() }}
                                </p>
                            @else
                                <p class="font-bold text-slate-500">-</p>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="p-6">
                    <article class="prose prose-slate max-w-none">
                        <h4 class="mb-4 text-lg font-semibold text-slate-900">Instruksi Pengerjaan</h4>
                        <div class="mb-6 whitespace-pre-line text-slate-600">
                            {{ $detailTugas->materi }}
                        </div>

                        @if ($detailTugas->file_tugas)
                            <div class="mb-6 flex items-center justify-between rounded-lg border border-slate-200 bg-slate-50 p-4">
                                <div class="flex items-center gap-3">
                                    <span class="material-symbols-outlined text-blue-600">description</span>
                                    <div>
                                        <p class="text-sm font-bold">Lampiran Modul / Instansi</p>
                                        <p class="text-xs text-slate-500">Materi pendukung dari admin</p>
                                    </div>
                                </div>
                                <a href="{{ asset('storage/' . $detailTugas->file_tugas) }}" target="_blank" class="rounded-lg border border-blue-600 px-4 py-1.5 text-sm font-semibold text-blue-600 transition hover:bg-blue-600 hover:text-white">
                                    Unduh
                                </a>
                            </div>
                        @endif

                        <form action="{{ route('peserta-magang.penugasan.store', $detailTugas->id_tugas) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="group relative mb-6 flex cursor-pointer flex-col items-center justify-center rounded-2xl border-2 border-dashed border-slate-300 p-8 text-center transition-colors hover:border-blue-500" onclick="document.getElementById('file_pengumpulan').click()">
                                <span class="material-symbols-outlined mb-3 text-[48px] text-slate-300 transition-colors group-hover:text-blue-500">cloud_upload</span>
                                <h5 class="mb-1 font-semibold text-slate-900">Unggah Hasil Pekerjaan</h5>
                                <p class="mb-4 text-sm text-slate-500">Format yang didukung: PDF, ZIP, RAR, atau DOCX (Maks. 25MB)</p>

                                <input type="file" name="file_pengumpulan" id="file_pengumpulan" class="hidden" onchange="updateFileName(this)">
                                <button type="button" class="rounded-lg border border-blue-600 px-6 py-2 text-sm font-semibold text-blue-600 transition-all hover:bg-blue-600 hover:text-white">
                                    Pilih File
                                </button>
                                <p id="file-name-display" class="mt-3 text-sm font-bold text-blue-600"></p>

                                @if ($userSubmission && $userSubmission->file_pengumpulan)
                                    <div class="mt-3 text-xs text-slate-500">
                                        File terunggah: <a href="{{ asset('storage/' . $userSubmission->file_pengumpulan) }}" target="_blank" onclick="event.stopPropagation();" class="font-bold text-blue-600 underline">Lihat Berkas</a>
                                    </div>
                                @endif
                            </div>

                            <div class="mb-6 space-y-4">
                                <div>
                                    <label class="mb-2 block text-sm font-semibold text-slate-700">Catatan atau Link (Optional)</label>
                                    <textarea name="catatan" class="h-24 w-full rounded-lg border border-slate-200 bg-white p-4 text-sm outline-none transition-all focus:border-blue-500 focus:ring-2 focus:ring-blue-100" placeholder="Masukkan link GitHub, Google Drive, atau catatan untuk pembimbing...">{{ old('catatan', $userSubmission?->catatan) }}</textarea>
                                </div>
                                <div>
                                    <label class="mb-2 block text-sm font-semibold text-slate-700">Link External / Repository (Optional)</label>
                                    <input type="url" name="link_external" value="{{ old('link_external', $userSubmission?->link_external) }}" class="w-full rounded-lg border border-slate-200 bg-white px-4 py-2.5 text-sm outline-none transition-all focus:border-blue-500 focus:ring-2 focus:ring-blue-100" placeholder="https://github.com/... atau Drive">
                                </div>
                            </div>

                            <div class="flex justify-end gap-4">
                                <button type="submit" name="action" value="draft" class="rounded-lg px-6 py-3 text-sm font-semibold text-slate-600 transition-all hover:bg-slate-100">
                                    Simpan Draft
                                </button>
                                <button type="submit" name="action" value="submit" class="flex items-center gap-2 rounded-lg bg-blue-600 px-10 py-3 text-sm font-semibold text-white shadow-lg shadow-blue-200/40 transition-all hover:bg-blue-700">
                                    <span class="material-symbols-outlined text-[20px]">send</span>
                                    Kumpulkan Tugas
                                </button>
                            </div>
                        </form>
                    </article>
                </div>
            </div>
        @else
            <div class="rounded-2xl border border-slate-200 bg-white p-12 text-center shadow-sm">
                <span class="material-symbols-outlined mb-2 text-[64px] text-slate-300">assignment_turned_in</span>
                <h3 class="text-lg font-semibold text-slate-900">Pilih tugas untuk melihat detail</h3>
            </div>
        @endif

        <div>
            <h3 class="mb-4 flex items-center gap-2 text-lg font-semibold text-slate-900">
                <span class="material-symbols-outlined text-indigo-600">history</span>
                Riwayat Penugasan
            </h3>
            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="border-b border-slate-200 bg-slate-50">
                            <tr>
                                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wide text-slate-500">Tugas</th>
                                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wide text-slate-500">Selesai</th>
                                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wide text-slate-500">Nilai</th>
                                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wide text-slate-500">Feedback</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($riwayatTugas as $tugas)
                                @php
                                    $pengumpulan = $tugas->pengumpulanTugas->first();
                                @endphp
                                <tr class="transition-colors hover:bg-slate-50">
                                    <td class="px-6 py-4">
                                        <div class="font-semibold text-slate-900">{{ $tugas->judul }}</div>
                                        <div class="text-xs text-slate-400">#TASK-NAT-{{ $tugas->id_tugas }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-slate-500">
                                        {{ $pengumpulan?->tanggal_dikumpul ? \Carbon\Carbon::parse($pengumpulan->tanggal_dikumpul)->format('d M Y') : '-' }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="font-bold text-blue-600">
                                            {{ $pengumpulan?->nilai ?? 'Belum ada nilai' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <button type="button" onclick="alert('Feedback: {{ addslashes($pengumpulan?->catatan_pembimbing ?? 'Belum ada catatan dari pembimbing.') }}')" class="flex items-center gap-1 text-sm font-semibold text-blue-600 hover:underline">
                                            Lihat Feedback
                                            <span class="material-symbols-outlined text-[16px]">chevron_right</span>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-8 text-center text-slate-500">
                                        Belum ada riwayat tugas yang telah dinilai.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function updateFileName(input) {
        const display = document.getElementById('file-name-display');
        if (input.files && input.files[0]) {
            display.textContent = 'File terpilih: ' + input.files[0].name;
        } else {
            display.textContent = '';
        }
    }
</script>
@endsection
