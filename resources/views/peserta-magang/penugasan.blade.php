@extends('layouts.portal')

@section('title', 'Penugasan - InternHub CV Natusi')

@section('content')
<!-- Notifications / Alert Status -->
@if(session('success'))
    <div class="col-span-12 bg-emerald-100 border border-emerald-400 text-emerald-800 px-4 py-3 rounded-xl flex items-center justify-between mb-6">
        <div class="flex items-center gap-2">
            <span class="material-symbols-outlined">check_circle</span>
            <span>{{ session('success') }}</span>
        </div>
        <button onclick="this.parentElement.remove()" class="text-emerald-800 font-bold">&times;</button>
    </div>
@endif

<!-- Bento Grid Layout -->
<div class="bento-grid">
    <!-- Left Column: Assignment List -->
    <div class="col-span-12 lg:col-span-5 flex flex-col gap-stack-md">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-headline-md text-headline-md flex items-center gap-2">
                <span class="material-symbols-outlined text-primary">pending_actions</span>
                Tugas Aktif
            </h3>
            <form method="GET" action="{{ route('peserta-magang.penugasan.index') }}" id="filterForm">
                <div class="relative">
                    <select name="minggu" onchange="document.getElementById('filterForm').submit()" class="appearance-none bg-surface-container-low border border-outline-variant rounded-lg px-4 py-1.5 pr-10 font-label-bold text-label-bold text-on-surface-variant focus:ring-2 focus:ring-primary focus:border-primary outline-none cursor-pointer transition-all">
                        <option value="all" {{ $selectedMinggu == 'all' ? 'selected' : '' }}>Semua Minggu</option>
                        @for ($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}" {{ $selectedMinggu == $i ? 'selected' : '' }}>Minggu {{ $i }}</option>
                        @endfor
                    </select>
                    <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-on-surface-variant text-[18px]">expand_more</span>
                </div>
            </form>
        </div>

        @forelse ($tugasAktif as $tugas)
            @php
                $pengumpulan = $tugas->pengumpulanTugas->first();
                $isSelected = $detailTugas && $detailTugas->id_tugas === $tugas->id_tugas;
                
                // Logic badge status sesuai UI
                $badgeClass = 'bg-error-container text-on-error-container';
                $statusText = 'Belum Selesai';

                if ($pengumpulan) {
                    if ($pengumpulan->status_pengumpulan === 'dikumpul') {
                        $badgeClass = 'bg-surface-container-high text-on-surface-variant';
                        $statusText = 'Sedang Dinilai';
                    } elseif ($pengumpulan->status_pengumpulan === 'draft') {
                        $badgeClass = 'bg-tertiary-fixed text-on-tertiary-fixed-variant';
                        $statusText = 'Draft / Sudah Dikumpul';
                    }
                }
            @endphp

            <a href="{{ route('peserta-magang.penugasan.index', ['tugas_id' => $tugas->id_tugas, 'minggu' => $selectedMinggu]) }}" 
               class="bg-surface-container-lowest p-stack-md rounded-xl transition-all cursor-pointer block 
                      {{ $isSelected ? 'border-l-4 border-primary shadow-sm hover:shadow-md' : 'border border-outline-variant hover:border-primary opacity-80 hover:opacity-100' }}">
                <div class="flex justify-between items-start mb-2">
                    <span class="{{ $badgeClass }} text-[10px] px-2 py-1 rounded font-bold uppercase tracking-wider">
                        {{ $statusText }}
                    </span>
                    <span class="text-on-surface-variant text-label-sm font-label-sm flex items-center gap-1">
                        <span class="material-symbols-outlined text-[14px]">event</span>
                        {{ $tugas->pengumpulan ? \Carbon\Carbon::parse($tugas->pengumpulan)->format('d M, H:i') : '-' }}
                    </span>
                </div>
                <h4 class="font-headline-md text-headline-md text-on-surface mb-1">{{ $tugas->judul }}</h4>
                <p class="text-on-surface-variant text-body-md line-clamp-2">{{ $tugas->materi }}</p>
            </a>
        @empty
            <div class="bg-surface-container-lowest p-6 rounded-xl border border-outline-variant text-center text-on-surface-variant">
                <span class="material-symbols-outlined text-[48px] mb-2 text-outline">task_alt</span>
                <p>Tidak ada tugas aktif untuk periode ini.</p>
            </div>
        @endforelse
    </div>

    <!-- Right Column: Submission & Details -->
    <div class="col-span-12 lg:col-span-7 space-y-stack-lg">
        @if ($detailTugas)
            @php
                $userSubmission = $detailTugas->pengumpulanTugas->first();
                $isDeadlinePast = $detailTugas->pengumpulan && now()->gt($detailTugas->pengumpulan);
            @endphp
            <!-- Detail Task Card -->
            <div class="bg-surface-container-lowest rounded-xl border border-outline-variant overflow-hidden shadow-sm">
                <div class="p-6 border-b border-outline-variant bg-surface-container-low">
                    <div class="flex justify-between items-center">
                        <div>
                            <h3 class="font-headline-md text-headline-md text-primary">Detail Penugasan</h3>
                            <p class="text-on-surface-variant font-body-md">#TASK-NAT-{{ $detailTugas->id_tugas }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-label-bold font-label-bold text-on-surface-variant uppercase">Deadline</p>
                            @if ($detailTugas->pengumpulan)
                                <p class="{{ $isDeadlinePast ? 'text-error font-bold' : 'text-primary font-bold' }}">
                                    {{ $detailTugas->pengumpulan->diffForHumans() }}
                                </p>
                            @else
                                <p class="text-on-surface-variant font-bold">-</p>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="p-6">
                    <article class="prose prose-slate max-w-none">
                        <h4 class="font-headline-md text-headline-md mb-4">Instruksi Pengerjaan</h4>
                        <div class="text-on-surface-variant mb-6 whitespace-pre-line">
                            {{ $detailTugas->materi }}
                        </div>

                        @if ($detailTugas->file_tugas)
                            <div class="mb-6 p-4 bg-surface-container-low border border-outline-variant rounded-lg flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <span class="material-symbols-outlined text-primary">description</span>
                                    <div>
                                        <p class="font-bold text-sm">Lampiran Modul / Instansi</p>
                                        <p class="text-xs text-on-surface-variant">{{ $detailTugas->instansi }}</p>
                                    </div>
                                </div>
                                <a href="{{ asset('storage/' . $detailTugas->file_tugas) }}" target="_blank" class="px-3 py-1.5 bg-primary text-on-primary text-xs font-bold rounded-md flex items-center gap-1 hover:opacity-90">
                                    <span class="material-symbols-outlined text-[16px]">download</span> Unduh
                                </a>
                            </div>
                        @endif

                        <!-- Form Upload / Submission -->
                        <form action="{{ route('peserta-magang.penugasan.store', $detailTugas->id_tugas) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            
                            <!-- Upload Box -->
                            <div class="bg-surface-container-lowest border-2 border-dashed border-outline-variant rounded-xl p-8 flex flex-col items-center justify-center text-center group hover:border-primary transition-colors cursor-pointer mb-6 relative" onclick="document.getElementById('file_pengumpulan').click()">
                                <span class="material-symbols-outlined text-[48px] text-outline mb-3 group-hover:text-primary transition-colors">cloud_upload</span>
                                <h5 class="font-headline-md text-headline-md text-on-surface mb-1">Unggah Hasil Pekerjaan</h5>
                                <p class="text-on-surface-variant text-body-md mb-4">Format yang didukung: PDF, ZIP, RAR, atau DOCX (Maks. 25MB)</p>
                                
                                <input type="file" name="file_pengumpulan" id="file_pengumpulan" class="hidden" onchange="updateFileName(this)">
                                <button type="button" class="px-6 py-2 border border-primary text-primary font-label-bold text-label-bold rounded-lg hover:bg-primary hover:text-on-primary transition-all">
                                    Pilih File
                                </button>
                                <p id="file-name-display" class="mt-3 text-sm font-bold text-primary"></p>

                                @if ($userSubmission && $userSubmission->file_pengumpulan)
                                    <div class="mt-3 text-xs text-on-surface-variant">
                                        File terunggah: <a href="{{ asset('storage/' . $userSubmission->file_pengumpulan) }}" target="_blank" onclick="event.stopPropagation();" class="text-primary underline font-bold">Lihat Berkas</a>
                                    </div>
                                @endif
                            </div>

                            <!-- Form Inputs -->
                            <div class="space-y-4 mb-6">
                                <div>
                                    <label class="block font-label-bold text-label-bold text-on-surface mb-2">Catatan atau Link (Optional)</label>
                                    <textarea name="catatan" class="w-full bg-surface p-4 rounded-lg border border-outline-variant focus:ring-2 focus:ring-primary focus:border-primary outline-none text-body-md h-24 transition-all" placeholder="Masukkan link GitHub, Google Drive, atau catatan untuk pembimbing...">{{ old('catatan', $userSubmission?->catatan) }}</textarea>
                                </div>
                                <div>
                                    <label class="block font-label-bold text-label-bold text-on-surface mb-2">Link External / Repository (Optional)</label>
                                    <input type="url" name="link_external" value="{{ old('link_external', $userSubmission?->link_external) }}" class="w-full bg-surface px-4 py-2.5 rounded-lg border border-outline-variant focus:ring-2 focus:ring-primary focus:border-primary outline-none text-body-md transition-all" placeholder="https://github.com/... atau Drive">
                                </div>
                            </div>

                            <!-- Submit Buttons -->
                            <div class="flex justify-end gap-4">
                                <button type="submit" name="action" value="draft" class="px-6 py-3 text-on-surface-variant font-label-bold text-label-bold hover:bg-surface-container-high rounded-lg transition-all">
                                    Simpan Draft
                                </button>
                                <button type="submit" name="action" value="submit" class="px-10 py-3 bg-primary text-on-primary font-label-bold text-label-bold rounded-lg hover:opacity-90 shadow-lg shadow-primary/20 transition-all flex items-center gap-2">
                                    <span class="material-symbols-outlined text-[20px]">send</span>
                                    Kumpulkan Tugas
                                </button>
                            </div>
                        </form>
                    </article>
                </div>
            </div>
        @else
            <div class="bg-surface-container-lowest p-12 rounded-xl border border-outline-variant text-center">
                <span class="material-symbols-outlined text-[64px] text-outline mb-2">assignment_turned_in</span>
                <h3 class="font-headline-md text-headline-md text-on-surface">Pilih tugas untuk melihat detail</h3>
            </div>
        @endif

        <!-- History Section -->
        <div>
            <h3 class="font-headline-md text-headline-md mb-4 flex items-center gap-2">
                <span class="material-symbols-outlined text-tertiary">history</span>
                Riwayat Penugasan
            </h3>
            <div class="bg-surface-container-lowest border border-outline-variant rounded-xl overflow-hidden">
                <table class="w-full text-left">
                    <thead class="bg-surface-container-low border-b border-outline-variant">
                        <tr>
                            <th class="px-6 py-4 font-label-bold text-label-bold text-on-surface-variant">Tugas</th>
                            <th class="px-6 py-4 font-label-bold text-label-bold text-on-surface-variant">Selesai</th>
                            <th class="px-6 py-4 font-label-bold text-label-bold text-on-surface-variant">Nilai</th>
                            <th class="px-6 py-4 font-label-bold text-label-bold text-on-surface-variant">Feedback</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-outline-variant/30">
                        @forelse ($riwayatTugas as $tugas)
                            @php
                                $pengumpulan = $tugas->pengumpulanTugas->first();
                            @endphp
                            <tr class="hover:bg-surface-container-lowest transition-colors">
                                <td class="px-6 py-4">
                                    <div class="font-bold text-on-surface">{{ $tugas->judul }}</div>
                                    <div class="text-[12px] text-on-surface-variant">#TASK-NAT-{{ $tugas->id_tugas }}</div>
                                </td>
                                <td class="px-6 py-4 text-on-surface-variant text-body-md">
                                    {{ $pengumpulan?->tanggal_dikumpul ? \Carbon\Carbon::parse($pengumpulan->tanggal_dikumpul)->format('d M Y') : '-' }}
                                </td>
                                <td class="px-6 py-4">
                                    <span class="font-bold text-primary">
                                        {{ $pengumpulan?->nilai ?? 'Belum ada nilai' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <button type="button" onclick="alert('Feedback: {{ addslashes($pengumpulan?->catatan_pembimbing ?? 'Belum ada catatan dari pembimbing.') }}')" class="text-primary hover:underline font-label-bold text-label-bold flex items-center gap-1">
                                        Lihat Feedback
                                        <span class="material-symbols-outlined text-[16px]">chevron_right</span>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-on-surface-variant">
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