<?php

namespace App\Exports;

use App\Models\Jurusan;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TugasMingguanSheetExport implements
    FromArray,
    WithHeadings,
    WithTitle,
    ShouldAutoSize,
    WithStyles
{
    public function __construct(private readonly Jurusan $jurusan)
    {
    }

    public function array(): array
    {
        return [
            [
                1,
                'Materi 1',
                'Pengenalan Algoritma dan Pemrograman',
                'senin',
                'rabu',
                '23:59',
            ],
            [
                1,
                'Tugas Materi 1',
                'Jelaskan konsep dasar algoritma. Implementasikan algoritma pencarian biner dalam Python/Java/PHP. Bandingkan kinerjanya pada dataset 1 juta angka acak.',
                'senin',
                'rabu',
                '23:59',
            ],
            [
                1,
                'Materi 2',
                'Struktur Data',
                'rabu',
                'jumat',
                '23:59',
            ],
            [
                1,
                'Tugas Materi 2',
                'Desain dan implementasikan struktur data "Trie" untuk menyimpan dan mencari kata dalam kamus. Uji performa dengan dataset 100 ribu kata.',
                'rabu',
                'jumat',
                '23:59',
            ],
            [
                1,
                'Laporan',
                'Buat laporan minggu ini dengan mengikuti template yang telah disediakan, kemudian kirimkan melalui email. Setelah itu, ambil screenshot sebagai bukti pengiriman dan kirimkan ke grup KP.',
                'jumat',
                'jumat',
                '23:59',
            ],
        ];
    }

    public function headings(): array
    {
        return [
            'Minggu Ke',
            'Materi & Laporan',
            'Tugas',
            'Hari Tampil',
            'Hari Deadline',
            'Jam Deadline',
        ];
    }

    public function title(): string
    {
        // Nama sheet dibatasi Excel maksimal 31 karakter.
        return mb_substr($this->jurusan->nama_sheet, 0, 31);
    }

    public function styles(Worksheet $sheet): array
    {
        $sheet->freezePane('A2');
        $sheet->setAutoFilter('A1:F1');
        $sheet->getRowDimension(1)->setRowHeight(32);
        $sheet->getStyle('A2:F200')->getAlignment()->setVertical(Alignment::VERTICAL_TOP);
        $sheet->getStyle('A2:F200')->getAlignment()->setWrapText(true);
        $sheet->getStyle('A1:F200')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->getStyle('A1:A200')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Contoh baris (baris 2-6): gabung kolom Minggu Ke jadi satu blok,
        // dan tebalkan baris "Laporan" (baris 6), meniru format yang biasa
        // dipakai admin secara manual.
        $sheet->mergeCells('A2:A6');
        $sheet->getStyle('A2')->getFont()->setBold(true);
        $sheet->getStyle('B6:C6')->getFont()->setBold(true);
        $sheet->getStyle('B6:F6')->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFFEF3C7');

        return [
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['argb' => 'FFFFFFFF'],
                    'size' => 11,
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => $this->jurusan->tingkat === 'smk' ? 'FF0EA5E9' : 'FF7C3AED'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => true,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => 'FFE2E8F0'],
                    ],
                ],
            ],
        ];
    }
}
