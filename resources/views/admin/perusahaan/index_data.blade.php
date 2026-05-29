@extends('layouts.admin')

@section('title', 'Database Perusahaan')
@section('page_title', 'Direktori Mitra Perusahaan')

@section('content')
<div class="card shadow-sm border-0">
    <div class="card-header border-0 pt-6">
        <div class="card-title">
            <div class="d-flex align-items-center position-relative my-1">
                <i class="material-icons position-absolute ms-6">search</i>
                <input type="text" data-kt-perusahaan-table-filter="search" class="form-control form-control-solid w-300px ps-15" placeholder="Cari Nama/Alamat Mitra..." />
            </div>
        </div>
        <div class="card-toolbar">
            <div class="d-flex justify-content-end" data-kt-perusahaan-table-toolbar="base">
                <button type="button" class="btn btn-primary d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#kt_modal_add_perusahaan">
                    <i class="material-icons me-2">add</i> Tambah Perusahaan
                </button>
            </div>
        </div>
    </div>
    <div class="card-body py-4">
        <div class="table-responsive">
            <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_perusahaans">
                <thead>
                    <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                        <th class="min-w-250px">Nama Perusahaan</th>
                        <th class="min-w-150px">Kategori Industri</th>
                        <th class="min-w-125px">Partisipasi Event</th>
                        <th class="min-w-100px text-center">Status Validasi</th>
                        <th class="text-end min-w-100px pe-5">Aksi Audit</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 fw-semibold">
                    @foreach($perusahaans as $perusahaan)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="symbol symbol-50px me-3">
                                        @if($perusahaan->logo && $perusahaan->logo != 'no-image')
                                            <img src="{{ asset('storage/'.$perusahaan->logo) }}" alt="logo" />
                                        @else
                                            <span class="symbol-label bg-light-info text-info fw-bold text-uppercase fs-6">{{ substr($perusahaan->nama ?? 'P', 0, 1) }}</span>
                                        @endif
                                    </div>
                                    <div class="d-flex flex-column">
                                        <span class="text-gray-800 fw-bold fs-6 mb-1">{{ $perusahaan->nama ?? 'N/A' }}</span>
                                        <span class="text-muted fs-8">{{ Str::limit($perusahaan->alamatlengkap ?? '-', 40) }}</span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge badge-light-primary fw-bold">{{ $perusahaan->kategori->nama ?? 'Umum' }}</span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <span class="badge badge-light-info fw-bold me-2">{{ $perusahaan->registers->count() }}</span>
                                    <span class="text-gray-600 fs-7">Event Diikuti</span>
                                </div>
                            </td>
                            <td class="text-center">
                                @if($perusahaan->user && $perusahaan->user->statusvalidasi == 1)
                                    <span class="badge badge-light-success fw-bold px-3 py-2">
                                        <i class="material-icons fs-7 text-success me-1">verified</i> VERIFIED
                                    </span>
                                @else
                                    <span class="badge badge-light-warning fw-bold px-3 py-2">
                                        <i class="material-icons fs-7 text-warning me-1">schedule</i> PENDING
                                    </span>
                                @endif
                            </td>
                            <td class="text-end pe-5">
                                <div class="d-flex justify-content-end gap-1">
                                    <button type="button" class="btn btn-icon btn-bg-light btn-active-color-success btn-sm register-event-btn" 
                                            data-id="{{ $perusahaan->id }}" 
                                            data-company-name="{{ $perusahaan->nama }}" 
                                            onclick="openRegisterEventModal('{{ $perusahaan->id }}', '{{ addslashes($perusahaan->nama) }}')"
                                            data-bs-toggle="modal" 
                                            data-bs-target="#kt_modal_register_event" 
                                            title="Daftarkan ke Event">
                                        <i class="material-icons fs-5">event_available</i>
                                    </button>
                                    <a href="{{ route('admin.perusahaan-data.edit', encrypt($perusahaan->id)) }}" class="btn btn-icon btn-bg-light btn-active-color-warning btn-sm" title="Edit Profil & Dokumen">
                                        <i class="material-icons fs-5">edit</i>
                                    </a>
                                    <a href="{{ route('admin.perusahaan-data.show', encrypt($perusahaan->id)) }}" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm" title="Audit Detail Perusahaan">
                                        <i class="material-icons fs-5">visibility</i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah Perusahaan -->
<div class="modal fade" id="kt_modal_add_perusahaan" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-900px">
        <div class="modal-content shadow-lg border-0">
            <div class="modal-header bg-light-primary py-5">
                <h2 class="fw-bold text-primary mb-0">Registrasi Perusahaan Baru</h2>
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                    <i class="material-icons">close</i>
                </div>
            </div>
            
            <div class="modal-body py-10 px-lg-17">
                <!-- Progress Bar -->
                <div class="progress mb-8 d-none" style="height: 12px; border-radius: 10px;" id="add_perusahaan_progress_container">
                    <div class="progress-bar progress-bar-striped progress-bar-animated bg-success" role="progressbar" id="add_perusahaan_progress_bar" style="width: 0%"></div>
                </div>

                <form id="kt_modal_add_perusahaan_form" class="form" action="{{ route('admin.perusahaan-data.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row g-9 mb-8">
                        <div class="col-md-6 fv-row">
                            <label class="required fs-6 fw-semibold mb-2">Nama Perusahaan</label>
                            <input type="text" class="form-control form-control-solid" placeholder="Contoh: PT Maju Bersama" name="nama" required />
                        </div>
                        <div class="col-md-6 fv-row">
                            <label class="required fs-6 fw-semibold mb-2">Email Bisnis (Login)</label>
                            <input type="email" class="form-control form-control-solid" placeholder="email@perusahaan.com" name="email" required />
                        </div>
                    </div>

                    <div class="row g-9 mb-8">
                        <div class="col-md-6 fv-row">
                            <label class="required fs-6 fw-semibold mb-2">Kategori Industri</label>
                            <select class="form-select form-select-solid" name="idkategori" required>
                                <option value="">Pilih Kategori...</option>
                                @foreach($kategoris as $kat)
                                    <option value="{{ $kat->id }}">{{ $kat->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 fv-row">
                            <label class="required fs-6 fw-semibold mb-2">Telepon / HP</label>
                            <input type="text" class="form-control form-control-solid" placeholder="021-xxxxxx" name="telp" required />
                        </div>
                    </div>

                    <div class="row g-9 mb-8">
                        <div class="col-md-6 fv-row">
                            <label class="fs-6 fw-semibold mb-2">NPWP</label>
                            <input type="text" class="form-control form-control-solid" placeholder="Masukkan nomor NPWP" name="npwp" />
                        </div>
                        <div class="col-md-6 fv-row">
                            <label class="fs-6 fw-semibold mb-2">NIB</label>
                            <input type="text" class="form-control form-control-solid" placeholder="Nomor Induk Berusaha" name="nib" />
                        </div>
                    </div>

                    <div class="fv-row mb-8">
                        <label class="required fs-6 fw-semibold mb-2">Alamat Lengkap Kantor</label>
                        <textarea class="form-control form-control-solid" rows="2" name="alamatlengkap" placeholder="Jl. Raya No. 123..." required></textarea>
                    </div>

                    <div class="row g-9 mb-8">
                        <div class="col-md-6 fv-row">
                            <label class="required fs-6 fw-semibold mb-2">Nama Pimpinan / Direktur</label>
                            <input type="text" class="form-control form-control-solid" name="namapimpinan" required />
                        </div>
                        <div class="col-md-6 fv-row">
                            <label class="required fs-6 fw-semibold mb-2">Nama PIC (Contact Person)</label>
                            <input type="text" class="form-control form-control-solid" name="pic" required />
                        </div>
                    </div>

                    <div class="row g-9 mb-8">
                        <div class="col-md-4 fv-row">
                            <label class="fs-6 fw-semibold mb-2">Tahun Berdiri</label>
                            <input type="number" class="form-control form-control-solid" name="tahunberdiri" />
                        </div>
                        <div class="col-md-4 fv-row">
                            <label class="fs-6 fw-semibold mb-2">Jml Karyawan</label>
                            <input type="text" class="form-control form-control-solid" name="jumlah_karyawan" placeholder="Contoh: 50-100" />
                        </div>
                        <div class="col-md-4 fv-row">
                            <label class="fs-6 fw-semibold mb-2">Website</label>
                            <input type="url" class="form-control form-control-solid" name="website" placeholder="https://..." />
                        </div>
                    </div>

                    <div class="row g-9 mb-8">
                        <div class="col-12 fv-row">
                            <label class="fs-6 fw-semibold mb-2">Upload Logo Utama</label>
                            <input type="file" class="form-control form-control-solid" name="logo" accept="image/*" />
                        </div>
                    </div>

                    <div class="notice d-flex bg-light-warning rounded border-warning border border-dashed p-6">
                        <i class="material-icons fs-2tx text-warning me-4">lock</i>
                        <div class="d-flex flex-stack flex-grow-1">
                            <div class="fw-semibold">
                                <div class="fs-6 text-gray-700">Akun pengguna otomatis aktif dengan password: <strong class="text-primary">password</strong></div>
                            </div>
                        </div>
                    </div>

                    <div class="text-center pt-15">
                        <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" id="add_perusahaan_submit_btn" class="btn btn-primary px-10">
                            <span class="indicator-label">Simpan & Daftarkan</span>
                            <span class="indicator-progress d-none">Menyimpan... 
                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Daftar Event -->
<div class="modal fade" id="kt_modal_register_event" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-500px">
        <div class="modal-content shadow-lg border-0">
            <div class="modal-header">
                <h2 class="fw-bold">Daftarkan Perusahaan ke Event</h2>
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                    <i class="material-icons">close</i>
                </div>
            </div>
            <div class="modal-body py-10 px-lg-15">
                <form action="{{ route('admin.perusahaan-data.register-event') }}" method="POST">
                    @csrf
                    <input type="hidden" name="idperusahaan" id="modal_perusahaan_id">
                    
                    <div class="mb-10 text-center">
                        <div class="fs-6 fw-semibold text-muted mb-2">Perusahaan Terpilih:</div>
                        <div class="fs-4 fw-bold text-dark" id="display_company_name_on_modal">Memilih Perusahaan...</div>
                    </div>

                    <div class="d-flex flex-column mb-10 fv-row">
                        <label class="required fs-6 fw-semibold mb-2">Pilih Event Job Fair</label>
                        <select class="form-select form-select-solid" name="ideven" id="modal_event_select" onchange="checkEventPaket(this.value)" required>
                            <option value="">Pilih Event Aktif...</option>
                        </select>
                        <div id="modal_event_no_available" class="text-danger fs-8 mt-2 d-none">Tidak ada event baru yang tersedia untuk perusahaan ini.</div>
                    </div>

                    <div class="d-flex flex-column mb-10 fv-row d-none" id="div_select_paket">
                        <label class="required fs-6 fw-semibold mb-2">Pilih Paket Event</label>
                        <select class="form-select form-select-solid" name="idpaket" id="modal_paket_select">
                            <option value="">Pilih Paket...</option>
                        </select>
                    </div>

                    <div class="notice d-flex bg-light-warning rounded border-warning border border-dashed p-6 mb-8">
                        <i class="material-icons fs-2tx text-warning me-4">info</i>
                        <div class="d-flex flex-stack flex-grow-1">
                            <div class="fw-semibold">
                                <div class="fs-7 text-gray-700">Pendaftaran melalui Admin akan otomatis **Aktif** tanpa melalui proses pembayaran invoice.</div>
                            </div>
                        </div>
                    </div>

                    <div class="text-center">
                        <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" id="register_event_submit_btn" class="btn btn-success px-10">
                            <span class="indicator-label">Konfirmasi Daftar</span>
                            <span class="indicator-progress d-none">Mendaftarkan... 
                                <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        var table = $('#kt_table_perusahaans').DataTable({
            "pageLength": 10,
            "order": [],
            "language": {
                "search": "Cari Cepat:",
                "lengthMenu": "Tampilkan _MENU_ data",
                "zeroRecords": "Tidak ada mitra yang ditemukan"
            }
        });

        // Search Filter
        $('[data-kt-perusahaan-table-filter="search"]').on('keyup', function() {
            table.search($(this).val()).draw();
        });

        // AJAX Submission for Add Perusahaan
        $('#kt_modal_add_perusahaan_form').on('submit', function(e) {
            e.preventDefault();
            const form = $(this);
            const btn = $('#add_perusahaan_submit_btn');
            const btnLabel = btn.find('.indicator-label');
            const btnProgress = btn.find('.indicator-progress');
            const progressContainer = $('#add_perusahaan_progress_container');
            const progressBar = $('#add_perusahaan_progress_bar');

            const formData = new FormData(this);

            btn.prop('disabled', true);
            btnLabel.addClass('d-none');
            btnProgress.removeClass('d-none');
            progressContainer.removeClass('d-none');
            progressBar.css('width', '0%').text('0%');
            NProgress.start();

            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                xhr: function() {
                    const xhr = new window.XMLHttpRequest();
                    xhr.upload.addEventListener("progress", function(evt) {
                        if (evt.lengthComputable) {
                            const percentComplete = Math.round((evt.loaded / evt.total) * 100);
                            progressBar.css('width', percentComplete + '%').text(percentComplete + '%');
                        }
                    }, false);
                    return xhr;
                },
                success: function(res) {
                    NProgress.done();
                    if (res.status === 'success') {
                        new PNotify({
                            title: 'Berhasil',
                            text: res.message,
                            type: 'success',
                            styling: 'brighttheme'
                        });
                        setTimeout(() => { location.reload(); }, 1500);
                    }
                },
                error: function(xhr) {
                    NProgress.done();
                    btn.prop('disabled', false);
                    btnLabel.removeClass('d-none');
                    btnProgress.addClass('d-none');
                    progressContainer.addClass('d-none');

                    let msg = 'Gagal menyimpan data.';
                    if (xhr.status === 422) msg = Object.values(xhr.responseJSON.errors)[0][0];
                    else if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;

                    new PNotify({
                        title: 'Kesalahan',
                        text: msg,
                        type: 'error',
                        styling: 'brighttheme'
                    });
                }
            });
        });

        // AJAX Submission for Register Event
        $('#kt_modal_register_event form').on('submit', function(e) {
            e.preventDefault();
            const form = $(this);
            const btn = $('#register_event_submit_btn');
            const btnLabel = btn.find('.indicator-label');
            const btnProgress = btn.find('.indicator-progress');

            btn.prop('disabled', true);
            btnLabel.addClass('d-none');
            btnProgress.removeClass('d-none');
            NProgress.start();

            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: form.serialize(),
                success: function(res) {
                    NProgress.done();
                    new PNotify({
                        title: 'Berhasil',
                        text: 'Perusahaan berhasil didaftarkan ke event.',
                        type: 'success',
                        styling: 'brighttheme'
                    });
                    setTimeout(() => { location.reload(); }, 1200);
                },
                error: function(xhr) {
                    NProgress.done();
                    btn.prop('disabled', false);
                    btnLabel.removeClass('d-none');
                    btnProgress.addClass('d-none');
                    
                    let msg = 'Gagal mendaftar ke event.';
                    if (xhr.status === 422 && xhr.responseJSON.message) msg = xhr.responseJSON.message;
                    else if (xhr.responseJSON && xhr.responseJSON.error) msg = xhr.responseJSON.error;

                    new PNotify({
                        title: 'Gagal',
                        text: msg,
                        type: 'error',
                        styling: 'brighttheme'
                    });
                }
            });
        });
    });

    // Global function outside $(document).ready for maximum reliability
    var current_available_events = [];

    function openRegisterEventModal(id, nama) {
        // Update Modal UI
        $('#modal_perusahaan_id').val(id);
        $('#display_company_name_on_modal').html('<strong>' + nama + '</strong>');
        
        // Hide paket select initially
        $('#div_select_paket').addClass('d-none');
        $('#modal_paket_select').prop('required', false).html('<option value="">Pilih Paket...</option>');

        // Fetch Available Events via AJAX
        var select = $('#modal_event_select');
        var noAvailable = $('#modal_event_no_available');
        
        select.html('<option value="">Memuat Event...</option>').prop('disabled', true);
        noAvailable.addClass('d-none');

        $.get('{{ url("/admin/perusahaan-data/available-events") }}/' + id, function(events) {
            current_available_events = events;
            select.html('<option value="">Pilih Event Aktif...</option>').prop('disabled', false);
            if (events && events.length > 0) {
                events.forEach(function(event) {
                    select.append('<option value="' + event.id + '">' + event.namaperiode + ' (' + event.tanggalawal + ')</option>');
                });
            } else {
                noAvailable.removeClass('d-none');
            }
        }).fail(function() {
            select.html('<option value="">Gagal memuat event</option>');
        });
    }

    function checkEventPaket(eventId) {
        const event = current_available_events.find(e => e.id == eventId);
        const divPaket = $('#div_select_paket');
        const selectPaket = $('#modal_paket_select');

        if (event && event.statuspaket == 1) {
            divPaket.removeClass('d-none');
            selectPaket.prop('required', true).html('<option value="">Pilih Paket...</option>');
            
            if (event.pakets && event.pakets.length > 0) {
                event.pakets.forEach(function(paket) {
                    selectPaket.append('<option value="' + paket.id + '">' + paket.nama_paket + ' (Rp. ' + parseFloat(paket.harga).toLocaleString('id-ID') + ')</option>');
                });
            } else {
                selectPaket.append('<option value="0">Tidak Ada Paket Tersedia</option>');
            }
        } else {
            divPaket.addClass('d-none');
            selectPaket.prop('required', false).val('');
        }
    }
</script>
@endpush
@endsection
