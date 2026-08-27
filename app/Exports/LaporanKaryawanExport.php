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
    protected $divisiId;
    protected $search;

    public function __construct($dariTgl = null, $sampaiTgl = null, $divisiId = null, $search = null)
    {
        $this->dariTgl = $dariTgl;
        $this->sampaiTgl = $sampaiTgl;
        $this->divisiId = $divisiId;
        $this->search = $search;
    }

    public function collection()
    {
        $query = Karyawan::query()->with('divisi');

        if ($this->dariTgl) {
            $query->whereDate('tanggal_bergabung', '>=', $this->dariTgl);
        }
        if ($this->sampaiTgl) {
            $query->whereDate('tanggal_bergabung', '<=', $this->sampaiTgl);
        }
        if ($this->divisiId) {
            $query->where('divisi_id', $this->divisiId);
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
        return ['Nama Karyawan', 'NIP', 'Email', 'No. HP', 'Divisi', 'Status', 'Tanggal Bergabung'];
    }

    public function map($karyawan): array
    {
        return [
            $karyawan->nama_karyawan,
            $karyawan->nip ?? '-',
            $karyawan->email ?? '-',
            $karyawan->no_hp ?? '-',
            $karyawan->divisi?->nama_divisi ?? '-',
            ucfirst($karyawan->status),
            $karyawan->tanggal_bergabung ? $karyawan->tanggal_bergabung->format('d-m-Y') : '-',
        ];
    }
}

