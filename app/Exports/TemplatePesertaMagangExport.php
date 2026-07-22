<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TemplatePesertaMagangExport implements
    FromArray,
    WithHeadings,
    ShouldAutoSize,
    WithStyles,
    WithColumnWidths,
    WithColumnFormatting,
    WithEvents
{
    public function array(): array
    {
        return [];
    }

    /**
     * Template ringkas untuk impor data lama peserta magang.
     * Timestamp, nomor induk, username, dan password tidak dibutuhkan.
     */
    public function headings(): array
    {
        return [
            'Nama',
            'Alamat',
            'No. WA',
            'Email',
            'Tingkat Pendidikan',
            'Nama Sekolah/Universitas',
            'Jurusan',
            'Kelas/Semester',
            'Periode Magang',
            'Tanggal Mulai',
            'Tanggal Selesai',
            'No. WA Guru/Dosen',
            'Status',
        ];
    }

    public function columnFormats(): array
    {
        return [
            'C' => NumberFormat::FORMAT_TEXT,
            'D' => NumberFormat::FORMAT_TEXT,
            'H' => NumberFormat::FORMAT_TEXT,
            'I' => NumberFormat::FORMAT_TEXT,
            'J' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'K' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'L' => NumberFormat::FORMAT_TEXT,
            'M' => NumberFormat::FORMAT_TEXT,
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 24,
            'B' => 40,
            'C' => 18,
            'D' => 30,
            'E' => 21,
            'F' => 34,
            'G' => 28,
            'H' => 19,
            'I' => 22,
            'J' => 18,
            'K' => 18,
            'L' => 24,
            'M' => 17,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        $sheet->freezePane('A2');
        $sheet->setAutoFilter('A1:M1');
        $sheet->getRowDimension(1)->setRowHeight(42);

        return [
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['argb' => 'FFFFFFFF'],
                    'size' => 11,
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF2563EB'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => true,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => 'FFBFDBFE'],
                    ],
                ],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event): void {
                $sheet = $event->sheet->getDelegate();

                $sheet->getStyle('A2:M1000')->getAlignment()->setVertical(Alignment::VERTICAL_TOP);
                $sheet->getStyle('A2:M1000')->getAlignment()->setWrapText(true);

                $educationValidation = new DataValidation();
                $educationValidation->setType(DataValidation::TYPE_LIST);
                $educationValidation->setErrorStyle(DataValidation::STYLE_STOP);
                $educationValidation->setAllowBlank(false);
                $educationValidation->setShowDropDown(true);
                $educationValidation->setShowErrorMessage(true);
                $educationValidation->setErrorTitle('Pilihan tidak valid');
                $educationValidation->setError('Pilih SMK atau Universitas.');
                $educationValidation->setPromptTitle('Tingkat pendidikan');
                $educationValidation->setPrompt('Pilih salah satu: SMK atau Universitas.');
                $educationValidation->setFormula1('"SMK,Universitas"');

                $statusValidation = new DataValidation();
                $statusValidation->setType(DataValidation::TYPE_LIST);
                $statusValidation->setErrorStyle(DataValidation::STYLE_STOP);
                $statusValidation->setAllowBlank(false);
                $statusValidation->setShowDropDown(true);
                $statusValidation->setShowErrorMessage(true);
                $statusValidation->setErrorTitle('Pilihan tidak valid');
                $statusValidation->setError('Pilih Aktif atau Nonaktif.');
                $statusValidation->setPromptTitle('Status peserta');
                $statusValidation->setPrompt('Pilih Aktif atau Nonaktif.');
                $statusValidation->setFormula1('"Aktif,Nonaktif"');

                for ($row = 2; $row <= 1000; $row++) {
                    $sheet->getCell("E{$row}")->setDataValidation(clone $educationValidation);
                    $sheet->getCell("M{$row}")->setDataValidation(clone $statusValidation);
                }
            },
        ];
    }
}
