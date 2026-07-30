<?php

namespace App\Exports;

use App\Models\Karyawan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class LaporanKaryawanExport implements FromCollection, WithHeadings, WithMapping
{
    protected $dariTgl;
    protected $sampaiTgl;
    protected $jabatan;
    protected $search;

    public function __construct($dariTgl = null, $sampaiTgl = null, $jabatan = null, $search = null)
    {
        $this->dariTgl = $dariTgl;
        $this->sampaiTgl = $sampaiTgl;
        $this->jabatan = $jabatan;
        $this->search = $search;
    }

    public function collection()
    {
        $query = Karyawan::query();

        if ($this->dariTgl) {
            $query->whereDate('created_at', '>=', $this->dariTgl);
        }
        if ($this->sampaiTgl) {
            $query->whereDate('created_at', '<=', $this->sampaiTgl);
        }
        if ($this->jabatan) {
            $query->where('jabatan', $this->jabatan);
        }
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('nama_karyawan', 'like', "%{$this->search}%")
                    ->orWhere('nip', 'like', "%{$this->search}%");
            });
        }

        return $query->latest()->get();
    }

    public function headings(): array
    {
        return ['Nama Karyawan', 'NIP', 'Email', 'No. HP', 'Jabatan', 'Status', 'Tanggal Bergabung'];
    }

    public function map($karyawan): array
    {
        return [
            $karyawan->nama_karyawan,
            $karyawan->nip ?? '-',
            $karyawan->email ?? '-',
            $karyawan->no_hp ?? '-',
            $karyawan->jabatan ?? '-',
            ucfirst($karyawan->status),
            $karyawan->created_at ? $karyawan->created_at->format('d-m-Y') : '-',
        ];
    }
}

