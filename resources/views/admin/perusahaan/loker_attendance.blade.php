@extends('layouts.admin')

@section('title', 'Absensi: ' . $loker->namalowongan)

@section('content')
<div class="row g-7">
    <!-- Header Summary Card -->
    <div class="col-lg-12">
        <div class="card shadow-sm border-0 mb-8 overflow-hidden">
            <div class="card-body p-0">
                <div class="d-flex flex-stack bg-light-info p-9">
                    <div class="d-flex align-items-center">
                        <a href="{{ route('admin.perusahaan.event.my-detail', encrypt($loker->idregister)) }}" class="btn btn-icon btn-sm btn-light-info me-5" title="Kembali">
                            <i class="material-icons fs-5">arrow_back</i>
                        </a>
                        <div class="d-flex flex-column">
                            <div class="d-flex align-items-center mb-1">
                                <h3 class="fw-bold text-gray-800 me-3 mb-0">Daftar Hadir Pelamar</h3>
                                <span class="badge badge-info fw-bold">{{ $loker->namalowongan }}</span>
                            </div>
                            <div class="fw-semibold text-gray-600 fs-7">{{ $loker->register->even->namaperiode }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Attendance Form -->
    <div class="col-lg-12">
        <div class="card shadow-sm border-0">
            <form action="{{ route('admin.perusahaan.loker.attendance.update', encrypt($loker->id)) }}" method="POST">
                @csrf
                <div class="card-header border-0 pt-7">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bold text-gray-800 fs-3">Checklist Kehadiran</span>
                        <span class="text-gray-400 mt-1 fw-semibold fs-7 px-1">Tandai pelamar yang hadir untuk proses seleksi selanjutnya</span>
                    </h3>
                    <div class="card-toolbar">
                        <button type="submit" class="btn btn-primary fw-bold px-8">
                            <i class="material-icons fs-4 me-2">save</i> Simpan Kehadiran
                        </button>
                    </div>
                </div>
                <div class="card-body pt-6">
                    <div class="table-responsive">
                        <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-5">
                            <thead>
                                <tr class="fw-bold text-muted bg-light">
                                    <th class="ps-4 rounded-start w-50px">Hadir?</th>
                                    <th>Info Pelamar</th>
                                    <th>Status Lamaran</th>
                                    <th>Waktu Absen</th>
                                    <th class="pe-4 text-end rounded-end">Kelola</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($loker->lamarans as $lamaran)
                                    @php
                                        $isHadidByDate = $lamaran->kehadirans->isNotEmpty();
                                    @endphp
                                    <tr class="{{ $isHadidByDate ? 'bg-light-success bg-opacity-10' : '' }}">
                                        <td class="ps-4">
                                            <div class="form-check form-check-custom form-check-solid form-check-success">
                                                <input class="form-check-input h-25px w-25px" type="checkbox" name="presents[]" value="{{ $lamaran->id }}" {{ $isHadidByDate ? 'checked' : '' }}>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="symbol symbol-40px me-4">
                                                    @if($lamaran->pelamar && $lamaran->pelamar->foto)
                                                        <img src="{{ asset('storage/'.$lamaran->pelamar->foto) }}" alt="Foto">
                                                    @else
                                                        <span class="symbol-label bg-light-info text-info fw-bold text-uppercase fs-6">{{ substr($lamaran->pelamar->namalengkap ?? '?', 0, 1) }}</span>
                                                    @endif
                                                </div>
                                                <div class="d-flex flex-column">
                                                    <span class="text-gray-800 fw-bold fs-6">{{ $lamaran->pelamar->namalengkap ?? 'Tidak Ada Data' }}</span>
                                                    <span class="text-muted fs-8">{{ $lamaran->pelamar->noktp ?? '-' }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @if($lamaran->statusditerima == 1)
                                                <span class="badge badge-light-success fw-bold">Kandidat Lolos</span>
                                            @else
                                                <span class="badge badge-light-warning fw-bold">Proses Berjalan</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($isHadidByDate)
                                                <div class="d-flex flex-column">
                                                    <span class="text-success fw-bold fs-7">{{ \Carbon\Carbon::parse($lamaran->kehadirans->first()->tanggal)->format('d M Y') }}</span>
                                                    <span class="text-muted fs-8">{{ $lamaran->kehadirans->first()->jam }}</span>
                                                </div>
                                            @else
                                                <span class="text-muted fs-7 italic">Belum Absen</span>
                                            @endif
                                        </td>
                                        <td class="text-end pe-4">
                                            <a href="{{ route('admin.perusahaan.loker.applicants', encrypt($loker->id)) }}" class="btn btn-sm btn-icon btn-bg-light btn-active-color-primary" title="Detail Profile">
                                                <i class="material-icons fs-5">account_box</i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-20 opacity-50">
                                            <i class="material-icons fs-3tx mb-3">person_off</i>
                                            <div class="fw-bold fs-5">Belum Ada Pelamar</div>
                                            <div class="text-muted">Aksi absen akan tersedia setelah kandidat melamar posisi ini.</div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
