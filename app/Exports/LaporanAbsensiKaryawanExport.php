<?php

namespace App\Exports;

use App\Models\AbsensiKaryawan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class LaporanAbsensiKaryawanExport implements FromCollection, WithHeadings, WithMapping
{
    protected $dariTgl;
    protected $sampaiTgl;

    public function __construct($dariTgl = null, $sampaiTgl = null)
    {
        $this->dariTgl = $dariTgl;
        $this->sampaiTgl = $sampaiTgl;
    }

    public function collection()
    {
        $query = AbsensiKaryawan::with('karyawan')->orderBy('tanggal');

        if ($this->dariTgl) {
            $query->whereDate('tanggal', '>=', $this->dariTgl);
        }
        if ($this->sampaiTgl) {
            $query->whereDate('tanggal', '<=', $this->sampaiTgl);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return ['Tanggal', 'Nama Karyawan', 'NIP', 'Jabatan', 'Jam Masuk', 'Jam Pulang', 'Status', 'Keterangan'];
    }

    public function map($absen): array
    {
        return [
            $absen->tanggal->format('d-m-Y'),
            $absen->karyawan->nama_karyawan ?? '-',
            $absen->karyawan->nip ?? '-',
            $absen->karyawan->jabatan ?? '-',
            $absen->jam_masuk ?? '-',
            $absen->jam_pulang ?? '-',
            ucfirst($absen->status),
            $absen->keterangan ?? '-',
        ];
    }
}