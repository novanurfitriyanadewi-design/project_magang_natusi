<?php

namespace App\Exports;

use App\Models\Absensi;
use App\Models\Karyawan;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AbsensiKaryawanExport implements FromCollection, WithHeadings, WithMapping
{
    protected string $tanggal;
    protected ?string $search;

    public function __construct(string $tanggal, ?string $search = null)
    {
        $this->tanggal = $tanggal;
        $this->search = $search;
    }

    public function collection()
    {
        $query = Absensi::with('absentable')
            ->where('absentable_type', Karyawan::class)
            ->whereDate('tanggal', $this->tanggal);

        if ($this->search) {
            $search = $this->search;

            $query->whereHas('absentable', function ($q) use ($search) {
                $q->where('nama_karyawan', 'like', "%{$search}%");
            });
        }

        return $query->latest('created_at')->get();
    }

    public function headings(): array
    {
        return [
            'Nama Karyawan',
            'Tanggal',
            'Jam Masuk',
            'Jam Pulang',
            'Status',
            'Keterangan'
        ];
    }

    public function map($item): array
    {
        return [
            $item->absentable->nama_karyawan ?? '-',
            Carbon::parse($item->tanggal)->format('d-m-Y'),
            $item->jam_masuk ?? '-',
            $item->jam_keluar ?? '-',
            ucfirst($item->status),
            $item->keterangan ?? '-',
        ];
    }
}