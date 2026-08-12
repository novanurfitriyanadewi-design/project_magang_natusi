# ============================================================
# PERBAIKI SEL DOKUMEN: fix tag <a> yang hilang + tampilan ikon+nama file
# Jalankan: .\fix_dokumen_cell.ps1
# ============================================================

$path = "resources\views\admin-karyawan\resign\index.blade.php"
$content = Get-Content $path -Raw

$pattern = '(?s)@if\s*\(\$item->surat_resign_path\).*?@endif'

$replacement = @'
@if ($item->surat_resign_path)
                                    @php
                                        $ext = strtolower(pathinfo($item->surat_resign_original_name ?? $item->surat_resign_path, PATHINFO_EXTENSION));
                                    @endphp
                                    <a href="{{ route('admin-karyawan.resign.download', $item) }}"
                                        title="{{ $item->surat_resign_original_name }}"
                                        class="mx-auto inline-flex max-w-[180px] items-center gap-2 text-xs font-semibold text-slate-600 transition hover:text-sky-600">
                                        <span class="material-symbols-outlined shrink-0 text-[20px] {{ $ext === 'pdf' ? 'text-red-500' : 'text-blue-500' }}">
                                            {{ $ext === 'pdf' ? 'picture_as_pdf' : 'description' }}
                                        </span>
                                        <span class="truncate">{{ Str::limit($item->surat_resign_original_name, 22) }}</span>
                                    </a>
                                @endif
'@

if ($content -match $pattern) {
    $content = $content -replace $pattern, ($replacement -replace '\$', '$$')
    Set-Content -Path $path -Value $content -NoNewline
    Write-Host "Berhasil diperbaiki." -ForegroundColor Green
} else {
    Write-Host "Pola tidak ditemukan. Kirim isi file ini ke Claude untuk dicek manual:" -ForegroundColor Red
    Write-Host $path
}
