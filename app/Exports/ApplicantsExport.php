<?php

namespace App\Exports;

use App\Models\Pelamar;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ApplicantsExport implements FromQuery, WithHeadings, WithMapping
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $query = Pelamar::query()->with(['kelurahan.kecamatan.kota.provinsi'])->withCount('lamarans');

        if (!empty($this->filters['provinsi'])) {
            $query->whereHas('kelurahan.kecamatan.kota', function ($q) {
                $q->where('idprovinsi', $this->filters['provinsi']);
            });
        }
        if (!empty($this->filters['kota'])) {
            $query->whereHas('kelurahan.kecamatan', function ($q) {
                $q->where('idkota', $this->filters['kota']);
            });
        }
        if (!empty($this->filters['kecamatan'])) {
            $query->whereHas('kelurahan', function ($q) {
                $q->where('idkecamatan', $this->filters['kecamatan']);
            });
        }
        if (!empty($this->filters['kelurahan'])) {
            $query->where('idkelurahan', $this->filters['kelurahan']);
        }

        return $query->latest();
    }

    public function headings(): array
    {
        return [
            'Nama Lengkap',
            'NIK',
            'Jenis Kelamin',
            'Alamat Lengkap',
            'Kelurahan',
            'Kecamatan',
            'Kota/Kabupaten',
            'Provinsi',
            'Tinggi/Berat',
            'Jumlah Lamaran',
            'Tgl Bergabung',
        ];
    }

    public function map($applicant): array
    {
        return [
            $applicant->namalengkap,
            "'" . $applicant->noktp, // Force string in Excel
            $applicant->jeniskelamin == 'L' ? 'Laki-laki' : 'Perempuan',
            $applicant->alamatlengkap,
            $applicant->kelurahan->nama ?? '-',
            $applicant->kelurahan->kecamatan->nama ?? '-',
            $applicant->kelurahan->kecamatan->kota->nama ?? '-',
            $applicant->kelurahan->kecamatan->kota->provinsi->nama ?? '-',
            ($applicant->tinggibadan ?? '0') . '/' . ($applicant->beratbadan ?? '0'),
            $applicant->lamarans_count,
            $applicant->created_at->format('d/m/Y'),
        ];
    }
}
