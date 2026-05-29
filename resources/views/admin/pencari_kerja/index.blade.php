@extends('layouts.admin')

@section('title', 'Database Talent')
@section('page_title', 'Direktori Pencari Kerja')

@section('content')
<div class="row g-7 mb-7">
    <!-- Regional Filtering Card -->
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header border-0 pt-5">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bold text-gray-800">Filtrasi Wilayah & Export</span>
                    <span class="text-muted mt-1 fw-semibold fs-7">Saring data talent berdasarkan domisili geografis.</span>
                </h3>
                <div class="card-toolbar">
                    <form action="{{ route('admin.pencari-kerja.export') }}" method="GET" id="export-form" class="d-inline">
                        <input type="hidden" name="provinsi" id="exp_provinsi">
                        <input type="hidden" name="kota" id="exp_kota">
                        <input type="hidden" name="kecamatan" id="exp_kecamatan">
                        <input type="hidden" name="kelurahan" id="exp_kelurahan">
                        <button type="submit" class="btn btn-light-success fw-bold">
                            <i class="material-icons fs-5 me-2">download</i> Export ke Excel
                        </button>
                    </form>
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.pencari-kerja.index') }}" method="GET" id="filter-form">
                    <div class="row g-5">
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Provinsi</label>
                            <select name="provinsi" id="select_provinsi" class="form-select form-select-solid" data-control="select2" data-placeholder="Pilih Provinsi">
                                <option value=""></option>
                                @foreach($provinces as $p)
                                    <option value="{{ $p->id }}" {{ request('provinsi') == $p->id ? 'selected' : '' }}>{{ $p->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Kota/Kabupaten</label>
                            <select name="kota" id="select_kota" class="form-select form-select-solid" data-control="select2" data-placeholder="Pilih Kota">
                                <option value=""></option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Kecamatan</label>
                            <select name="kecamatan" id="select_kecamatan" class="form-select form-select-solid" data-control="select2" data-placeholder="Pilih Kecamatan">
                                <option value=""></option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Kelurahan</label>
                            <select name="kelurahan" id="select_kelurahan" class="form-select form-select-solid" data-control="select2" data-placeholder="Pilih Kelurahan">
                                <option value=""></option>
                            </select>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end mt-5">
                        <a href="{{ route('admin.pencari-kerja.index') }}" class="btn btn-light me-3">Reset</a>
                        <button type="submit" class="btn btn-primary">Terapkan Filter</button>
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
                <input type="text" data-kt-applicant-table-filter="search" class="form-control form-control-solid w-300px ps-15" placeholder="Cari Nama/NIK Talent..." />
            </div>
        </div>
    </div>
    <div class="card-body py-4">
        <div class="table-responsive">
            <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_applicants">
                <thead>
                    <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                        <th class="min-w-250px">Nama Lengkap / Identitas</th>
                        <th class="min-w-150px">Lokasi</th>
                        <th class="min-w-125px">Engagment</th>
                        <th class="min-w-125px">Bergabung Pada</th>
                        <th class="text-end min-w-100px pe-5">Aksi Audit</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 fw-semibold">
                    @foreach($applicants as $applicant)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="symbol symbol-50px me-3">
                                        @if($applicant->foto && $applicant->foto != 'no-image')
                                            <img src="{{ asset('storage/'.$applicant->foto) }}" alt="photo" />
                                        @else
                                            <span class="symbol-label bg-light-primary text-primary fw-bold text-uppercase fs-6">{{ substr($applicant->namalengkap ?? 'P', 0, 1) }}</span>
                                        @endif
                                    </div>
                                    <div class="d-flex flex-column">
                                        <span class="text-gray-800 fw-bold fs-6 mb-1">{{ $applicant->namalengkap ?? 'N/A' }}</span>
                                        <div class="d-flex align-items-center fs-8 text-muted">
                                            <i class="material-icons fs-9 me-1">id_card</i> NIK: {{ $applicant->noktp ?? '-' }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="fs-7">{{ Str::limit($applicant->alamatlengkap ?? 'Alamat Belum Diset', 35) }}</span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <span class="badge badge-light-success fw-bold me-2">{{ $applicant->lamarans_count }}</span>
                                    <span class="text-gray-600 fs-7">Lamaran Aktif</span>
                                </div>
                            </td>
                            <td>
                                <span class="fs-7">{{ \Carbon\Carbon::parse($applicant->created_at)->format('d M Y') }}</span>
                            </td>
                            <td class="text-end pe-5">
                                <a href="{{ route('admin.pencari-kerja.show', encrypt($applicant->id)) }}" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm" title="Audit Detail Talent">
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
        var table = $('#kt_table_applicants').DataTable({
            "pageLength": 10,
            "order": [],
            "language": {
                "search": "Cari Cepat:",
                "lengthMenu": "Tampilkan _MENU_ data",
                "zeroRecords": "Tidak ada pencari kerja yang ditemukan"
            }
        });

        // Regions AJAX Handling
        $('#select_provinsi').on('change', function() {
            let id = $(this).val();
            $('#select_kota, #select_kecamatan, #select_kelurahan').empty().append('<option value=""></option>');
            if(id) {
                $.get('/admin/pencari-kerja/get-cities/' + id, function(data) {
                    $.each(data, function(i, item) {
                        $('#select_kota').append('<option value="'+item.id+'">'+item.nama+'</option>');
                    });
                });
            }
        });

        $('#select_kota').on('change', function() {
            let id = $(this).val();
            $('#select_kecamatan, #select_kelurahan').empty().append('<option value=""></option>');
            if(id) {
                $.get('/admin/pencari-kerja/get-districts/' + id, function(data) {
                    $.each(data, function(i, item) {
                        $('#select_kecamatan').append('<option value="'+item.id+'">'+item.nama+'</option>');
                    });
                });
            }
        });

        $('#select_kecamatan').on('change', function() {
            let id = $(this).val();
            $('#select_kelurahan').empty().append('<option value=""></option>');
            if(id) {
                $.get('/admin/pencari-kerja/get-villages/' + id, function(data) {
                    $.each(data, function(i, item) {
                        $('#select_kelurahan').append('<option value="'+item.id+'">'+item.nama+'</option>');
                    });
                });
            }
        });

        // Sync Filters to Export Hidden Inputs
        $('#export-form').on('submit', function() {
            $('#exp_provinsi').val($('#select_provinsi').val());
            $('#exp_kota').val($('#select_kota').val());
            $('#exp_kecamatan').val($('#select_kecamatan').val());
            $('#exp_kelurahan').val($('#select_kelurahan').val());
        });

        // Search Filter
        $('[data-kt-applicant-table-filter="search"]').on('keyup', function() {
            table.search($(this).val()).draw();
        });
    });
</script>
@endpush
@endsection
