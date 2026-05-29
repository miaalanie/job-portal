@extends('layouts.admin')

@section('title', 'Database Lowongan Kerja')
@section('page_title', 'Master Lowongan Pekerjaan')

@section('content')
<div class="row g-7 mb-7">
    <!-- Filter Card -->
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header border-0 pt-5">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bold text-gray-800">Filtrasi Lowongan</span>
                    <span class="text-muted mt-1 fw-semibold fs-7">Saring data berdasarkan event atau mitra industri.</span>
                </h3>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.lowongan-kerja.index') }}" method="GET" id="filter-form">
                    <div class="row g-5">
                        <div class="col-md-5">
                            <label class="form-label fw-bold">Berdasarkan Event</label>
                            @if(isset($isAdminEvent) && $isAdminEvent)
                                <input type="hidden" name="even" value="{{ request('even') ?? Auth::user()->ideven }}">
                                <div class="d-flex align-items-center gap-2 mt-1">
                                    <span class="badge badge-light-primary fw-bold fs-7 py-2 px-4 border border-primary border-dashed">
                                        <i class="material-icons fs-6 me-1 align-middle">lock</i>
                                        {{ $events->first()?->namaperiode ?? 'Event Belum Ditentukan' }}
                                    </span>
                                </div>
                            @else
                                <select name="even" class="form-select form-select-solid" data-control="select2" data-placeholder="Pilih Event">
                                    <option value=""></option>
                                    @foreach($events as $e)
                                        <option value="{{ $e->id }}" {{ request('even') == $e->id ? 'selected' : '' }}>
                                            {{ $e->namaperiode }} {{ $e->statusaktif ? '(AKTIF)' : '' }}
                                        </option>
                                    @endforeach
                                </select>
                            @endif
                        </div>
                        <div class="col-md-5">
                            <label class="form-label fw-bold">Berdasarkan Perusahaan</label>
                            <select name="perusahaan" class="form-select form-select-solid" data-control="select2" data-placeholder="Pilih Perusahaan">
                                <option value=""></option>
                                @foreach($companies as $c)
                                    <option value="{{ $c->id }}" {{ request('perusahaan') == $c->id ? 'selected' : '' }}>{{ $c->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <div class="d-flex w-100 gap-2">
                                <button type="submit" class="btn btn-primary flex-grow-1">Filter</button>
                                <a href="{{ route('admin.lowongan-kerja.index') }}" class="btn btn-light">Reset</a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-header border-0 pt-6">
        <div class="card-title">
            <div class="d-flex align-items-center position-relative my-1">
                <i class="material-icons position-absolute ms-6">search</i>
                <input type="text" data-kt-vancacy-table-filter="search" class="form-control form-control-solid w-300px ps-15" placeholder="Cari Posisi Lowongan..." />
            </div>
        </div>
    </div>
    <div class="card-body py-4">
        <div class="table-responsive">
            <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_vacancies">
                <thead>
                    <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                        <th class="min-w-250px">Lowongan & Perusahaan</th>
                        <th class="min-w-150px">Placement Event</th>
                        <th class="min-w-125px">Pelamar</th>
                        <th class="min-w-100px text-center">Status</th>
                        <th class="text-end min-w-100px pe-5">Aksi Audit</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 fw-semibold">
                    @foreach($vacancies as $loker)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="symbol symbol-50px me-3">
                                        @php $logo = $loker->register->perusahaan->logo; @endphp
                                        @if($logo && $logo != 'no-image')
                                            <img src="{{ asset('storage/'.$logo) }}" alt="logo" />
                                        @else
                                            <span class="symbol-label bg-light-danger text-danger fw-bold text-uppercase fs-6">{{ substr($loker->namalowongan, 0, 1) }}</span>
                                        @endif
                                    </div>
                                    <div class="d-flex flex-column">
                                        <span class="text-gray-800 fw-bold fs-6 mb-1">{{ $loker->namalowongan }}</span>
                                        <span class="text-muted fs-8">{{ $loker->register->perusahaan->nama }}</span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex flex-column">
                                    <span class="text-gray-800 fw-bold fs-7">{{ $loker->register->even->namaperiode }}</span>
                                    @if($loker->register->even->statusaktif)
                                        <span class="badge badge-light-success fs-9 px-2 py-0 w-fit-content">ACTIVE EVENT</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <span class="badge badge-light-info fw-bold me-2">{{ $loker->lamarans_count }}</span>
                                    <span class="text-gray-600 fs-7">Kandidat</span>
                                </div>
                            </td>
                            <td class="text-center">
                                @if($loker->status == 1)
                                    <span class="badge badge-light-success fw-bold px-3 py-2">AKTIF</span>
                                @else
                                    <span class="badge badge-light-danger fw-bold px-3 py-2">CLOSED</span>
                                @endif
                            </td>
                            <td class="text-end pe-5">
                                <a href="{{ route('admin.lowongan-kerja.show', encrypt($loker->id)) }}" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm" title="Audit Detail & Pelamar">
                                    <i class="material-icons fs-5">visibility</i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        var table = $('#kt_table_vacancies').DataTable({
            "pageLength": 10,
            "order": [],
            "language": {
                "search": "Cari Cepat:",
                "lengthMenu": "Tampilkan _MENU_ data",
                "zeroRecords": "Tidak ada lowongan yang ditemukan"
            }
        });

        // Search Filter
        $('[data-kt-vancacy-table-filter="search"]').on('keyup', function() {
            table.search($(this).val()).draw();
        });
    });
</script>
@endpush
@endsection
