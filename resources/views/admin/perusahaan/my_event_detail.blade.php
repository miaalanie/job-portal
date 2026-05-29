@extends('layouts.admin')

@section('title', 'Detail Event: ' . $registration->even->namaperiode)

@section('content')
<div class="row g-7">
    <div class="col-lg-12">
        <div class="card shadow-sm border-0 mb-8">
            <div class="card-header border-0 pt-6">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bold fs-3 mb-1">Status Keikutsertaan</span>
                    <span class="text-muted fw-semibold fs-7">Rincian pendaftaran Anda pada event {{ $registration->even->namaperiode }}</span>
                </h3>
                <div class="card-toolbar">
                    @if($registration->aktivasi == 1)
                        <span class="badge badge-light-success fs-7 fw-bold px-4 py-3"><i class="material-icons fs-6 text-success me-1">check_circle</i> STATUS: AKTIF / TERVERIFIKASI</span>
                    @elseif($registration->payment)
                        <span class="badge badge-light-info fs-7 fw-bold px-4 py-3"><i class="material-icons fs-6 text-info me-1">hourglass_empty</i> STATUS: MENUNGGU VERIFIKASI PEMBAYARAN</span>
                    @else
                        <span class="badge badge-light-warning fs-7 fw-bold px-4 py-3"><i class="material-icons fs-6 text-warning me-1">report_problem</i> STATUS: MENUNGGU PEMBAYARAN</span>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <div class="row g-5">
                    <div class="col-md-4">
                        <div class="fw-semibold text-gray-400 mb-1">Paket Pilihan:</div>
                        <div class="fw-bold fs-5 text-gray-800">{{ $registration->nama_paket_tampil }}</div>
                    </div>
                    <div class="col-md-4">
                        <div class="fw-semibold text-gray-400 mb-1">Biaya Registrasi:</div>
                        <div class="fw-bold fs-5 text-gray-800">Rp {{ number_format($registration->biaya, 0, ',', '.') }}</div>
                    </div>
                    <div class="col-md-4">
                        <div class="fw-semibold text-gray-400 mb-1">Tanggal Daftar:</div>
                        <div class="fw-bold fs-5 text-gray-800">{{ \Carbon\Carbon::parse($registration->tanggalregister)->format('d F Y') }}</div>
                    </div>
                </div>
            </div>
            @if($registration->aktivasi == 0)
                <div class="card-footer bg-light-warning border-0 p-6 d-flex flex-stack rounded-bottom">
                    <div class="d-flex align-items-center me-5">
                        <i class="material-icons fs-2tx text-warning me-4">info_outline</i>
                        <div class="text-gray-700">
                            <strong>Perhatian:</strong> Pendaftaran Anda belum aktif. Anda belum dapat mengaktifkan lowongan sebelum admin memverifikasi pembayaran Anda.
                        </div>
                    </div>
                    <a href="{{ route('admin.perusahaan.dashboard') }}" class="btn btn-warning fw-bold btn-sm">Upload Bukti Bayar</a>
                </div>
            @endif
        </div>

        <!-- Lowongan Section -->
        <div class="card shadow-sm border-0">
            <div class="card-header border-0 pt-7">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bold text-gray-800 fs-3">Lowongan Pekerjaan</span>
                    <span class="text-gray-400 mt-1 fw-semibold fs-6">Daftar lowongan yang Anda sertakan untuk event ini</span>
                </h3>
                <div class="card-toolbar">
                    <a href="{{ route('admin.perusahaan.loker.create', encrypt($registration->id)) }}" class="btn btn-sm btn-primary fw-bold" {{ $registration->aktivasi == 0 ? 'disabled' : '' }}>
                        <i class="material-icons fs-5">add</i> Tambah Lowongan
                    </a>
                </div>
            </div>
            <div class="card-body pt-6">
                <div class="table-responsive">
                    <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-5">
                        <thead>
                            <tr class="fw-bold text-muted bg-light">
                                <th class="ps-4 rounded-start">Posisi & Kategori</th>
                                <th>Persyaratan</th>
                                <th>Pelamar</th>
                                <th>Status</th>
                                <th class="pe-4 text-end rounded-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($lowongans as $loker)
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex flex-column">
                                            <span class="text-gray-800 fw-bold fs-6">{{ $loker->namalowongan }}</span>
                                            <span class="text-muted fw-semibold fs-8">{{ $loker->kategori->nama ?? 'Umum' }}</span>
                                            <span class="text-primary fs-9 italic">{{ $loker->kategorilokasi }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-gray-600 fs-8 job-requirements-preview" style="max-height: 80px; overflow-y: auto;">
                                            {!! $loker->deskripsi !!}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <span class="badge badge-light-primary fw-bold fs-7 me-2">{{ $loker->lamarans_count }}</span>
                                            <span class="text-muted fs-8">Kandidat</span>
                                        </div>
                                    </td>
                                    <td>
                                        @if($loker->status == 1)
                                            <span class="badge badge-light-success fs-8 fw-bold">Tayang</span>
                                        @else
                                            <span class="badge badge-light-danger fs-8 fw-bold">Draft / Tutup</span>
                                        @endif
                                    </td>
                                    <td class="text-end pe-4">
                                        <div class="d-flex justify-content-end gap-1">
                                            <a href="{{ route('vacancy.detail', encrypt($loker->id)) }}" target="_blank" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1" title="Lihat Tampilan Publik">
                                                <i class="material-icons fs-5 text-primary">open_in_new</i>
                                            </a>
                                            <form action="{{ route('admin.perusahaan.loker.toggle-status', encrypt($loker->id)) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-icon btn-sm {{ $loker->status == 1 ? 'btn-light-danger' : 'btn-light-success' }} me-1" title="{{ $loker->status == 1 ? 'Tutup Lowongan' : 'Tayangkan Lowongan' }}">
                                                    <i class="material-icons fs-5">{{ $loker->status == 1 ? 'visibility_off' : 'visibility' }}</i>
                                                </button>
                                            </form>
                                            <a href="{{ route('admin.perusahaan.loker.attendance', encrypt($loker->id)) }}" class="btn btn-icon btn-bg-light btn-active-color-info btn-sm me-1" title="Absensi Kehadiran Pelamar"><i class="material-icons fs-5">fingerprint</i></a>
                                            <a href="{{ route('admin.perusahaan.loker.applicants', encrypt($loker->id)) }}" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1" title="Lihat Pelamar"><i class="material-icons fs-5">group</i></a>
                                            <a href="{{ route('admin.perusahaan.loker.edit', encrypt($loker->id)) }}" class="btn btn-icon btn-bg-light btn-active-color-warning btn-sm" title="Edit Lowongan"><i class="material-icons fs-5">edit</i></a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-20 opacity-50">
                                        <i class="material-icons fs-2tx mb-3">work_outline</i>
                                        <div class="fw-bold">Belum Ada Lowongan</div>
                                        <div class="text-muted">Daftarkan lowongan Anda untuk mulai menerima pelamar di event ini.</div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
