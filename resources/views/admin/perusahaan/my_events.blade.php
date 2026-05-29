@extends('layouts.admin')

@section('title', 'Event Diikuti')

@section('content')
<div class="row g-6 g-xl-9">
    @forelse($registrations as $reg)
        <div class="col-md-6 col-xl-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex flex-column p-0">
                    <!-- Event Header -->
                    <div class="position-relative p-6 pt-8 pb-4">
                        <div class="d-flex align-items-center mb-5">
                            <div class="symbol symbol-50px me-3">
                                <span class="symbol-label bg-light-primary text-primary fw-bold text-uppercase fs-4">{{ substr($reg->even->namaperiode, 0, 1) }}</span>
                            </div>
                            <div class="d-flex flex-column">
                                <a href="{{ route('admin.perusahaan.event.my-detail', encrypt($reg->id)) }}" class="text-gray-800 text-hover-primary fs-5 fw-bold">{{ $reg->even->namaperiode }}</a>
                                <span class="text-muted fw-semibold fs-7">{{ $reg->namapaket }}</span>
                            </div>
                        </div>

                        <!-- Status Badge -->
                        <div class="position-absolute top-0 end-0 mt-4 me-4">
                            @if($reg->aktivasi == 1)
                                <span class="badge badge-light-success fw-bold px-3 py-2"><i class="material-icons fs-9 text-success me-1">check_circle</i> Aktif</span>
                            @elseif($reg->payment)
                                <span class="badge badge-light-info fw-bold px-3 py-2"><i class="material-icons fs-9 text-info me-1">hourglass_empty</i> Verifikasi</span>
                            @else
                                <span class="badge badge-light-warning fw-bold px-3 py-2"><i class="material-icons fs-9 text-warning me-1">report_problem</i> Menunggu Bayar</span>
                            @endif
                        </div>

                        <!-- Info Grid -->
                        <div class="d-flex flex-wrap gap-3 mb-5">
                            <div class="border border-gray-300 border-dashed rounded min-w-100px py-3 px-4 mb-3">
                                <div class="fs-8 text-gray-400 fw-bold">TANGGAL</div>
                                <div class="fs-7 fw-bold text-gray-800">{{ \Carbon\Carbon::parse($reg->even->tanggalawal)->format('d M Y') }}</div>
                            </div>
                            <div class="border border-gray-300 border-dashed rounded min-w-100px py-3 px-4 mb-3">
                                <div class="fs-8 text-gray-400 fw-bold">BIAYA</div>
                                <div class="fs-7 fw-bold text-gray-800">
                                    @if($reg->biaya > 0)
                                        Rp {{ number_format($reg->biaya, 0, ',', '.') }}
                                    @else
                                        <span class="text-success">GRATIS</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="separator separator-dashed border-gray-300"></div>

                    <!-- Footer -->
                    <div class="p-6 d-flex align-items-center justify-content-between mt-auto">
                        <div class="symbol-group symbol-hover mb-0">
                            {{-- Lowongan counts could go here --}}
                            <span class="text-muted fs-8 fw-bold">{{ $reg->lowongans_count ?? 0 }} Lowongan Aktif</span>
                        </div>
                        <a href="{{ route('admin.perusahaan.event.my-detail', encrypt($reg->id)) }}" class="btn btn-sm btn-light-primary fw-bold px-4">Lihat Detail</a>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12 text-center py-20">
            <div class="card shadow-sm border-0 py-20">
                <i class="material-icons fs-3tx text-muted mb-4">event_busy</i>
                <h3 class="fw-bold text-gray-800">Belum Mengikuti Event</h3>
                <p class="text-muted mb-6">Anda belum mendaftar di event bursa kerja manapun.</p>
                <div>
                    <a href="{{ route('admin.perusahaan.dashboard') }}" class="btn btn-primary fw-bold">Cari Event Sekarang</a>
                </div>
            </div>
        </div>
    @endforelse
</div>
@endsection
