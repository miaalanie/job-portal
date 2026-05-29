@extends('layouts.admin')

@section('title', 'Kelola Sponsor')
@section('page_title', 'Manajemen Sponsor Event')

@section('content')
<div class="card shadow-sm mb-5">
    <div class="card-header border-0 pt-6">
        <div class="card-title">
            <div class="d-flex align-items-center">
                <div class="symbol symbol-45px me-4">
                    @if($event->gambar)
                        <img src="{{ asset('storage/' . $event->gambar) }}" alt="Event Logo">
                    @else
                        <div class="symbol-label bg-light-primary text-primary fw-bold">
                            {{ substr($event->namaperiode, 0, 1) }}
                        </div>
                    @endif
                </div>
                <div class="d-flex flex-column">
                    <h3 class="fw-bold mb-0">{{ $event->namaperiode }}</h3>
                    <span class="text-muted fs-7">Kelola daftar sponsor untuk event ini</span>
                </div>
            </div>
        </div>
        <div class="card-toolbar">
            <a href="{{ route('admin.event') }}" class="btn btn-light-primary btn-sm">
                <i class="material-icons fs-5 me-1">arrow_back</i> Kembali
            </a>
        </div>
    </div>
</div>

<div class="row g-5">
    <div class="col-md-4">
        <div class="card shadow-sm border-0 sticky-top" style="top: 100px;">
            <div class="card-header border-0 pb-0">
                <h3 class="card-title fw-bold">Tambah Sponsor Baru</h3>
            </div>
            <div class="card-body">
                <form id="sponsor-form" action="{{ route('admin.event.store_sponsor', encrypt($event->id)) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-5">
                        <label class="required fs-6 fw-bold mb-2">Nama Sponsor</label>
                        <input type="text" name="nama" class="form-control form-control-solid" placeholder="Contoh: PT. Bank Mandiri" required />
                    </div>
                    <div class="mb-8">
                        <label class="fs-6 fw-bold mb-2">Logo Sponsor</label>
                        <input type="file" name="logo" id="logo_input" class="form-control form-control-solid" accept="image/*" />
                        <div class="form-text mt-2">Format: JPG, PNG. Maks: 1MB</div>
                        <div id="logo_preview_container" class="mt-4 text-center rounded bg-light p-5 border border-dashed" style="display: none;">
                            <img id="logo_preview" src="#" class="img-fluid rounded" style="max-height: 80px;">
                        </div>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary" id="submit-btn" disabled>
                            <span class="indicator-label">Tambahkan Sponsor</span>
                            <span class="indicator-progress">
                                Mohon tunggu... <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="row g-5" id="sponsor-list">
            @forelse($event->sponsors as $sponsor)
                <div class="col-md-6 mb-3 sponsor-card-item">
                    <div class="card h-100 shadow-sm hover-elevate-up">
                        <div class="card-body d-flex align-items-center p-6">
                            <div class="symbol symbol-60px me-5 bg-light p-2 rounded border">
                                @if($sponsor->logo)
                                    <img src="{{ asset('storage/' . $sponsor->logo) }}" class="object-fit-contain" alt="Logo">
                                @else
                                    <div class="symbol-label bg-light-warning text-warning">
                                        <i class="material-icons text-warning">loyalty</i>
                                    </div>
                                @endif
                            </div>
                            <div class="d-flex flex-column flex-grow-1">
                                <span class="text-gray-800 fw-bold fs-5 mb-1">{{ $sponsor->nama }}</span>
                                <span class="text-muted fs-7">Brand Partner</span>
                            </div>
                            <button type="button" class="btn btn-icon btn-sm btn-light-danger delete-sponsor" data-id="{{ encrypt($sponsor->id) }}" title="Hapus Sponsor">
                                <i class="material-icons fs-5">delete_outline</i>
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-20 bg-light rounded-4 border border-dashed border-gray-300">
                    <div class="mb-4">
                        <i class="material-icons fs-3x text-gray-400">loyalty</i>
                    </div>
                    <h4 class="fw-bold text-gray-600">Belum ada sponsor</h4>
                    <p class="text-muted">Siilakan gunakan form di samping untuk mulai menambahkan sponsor event.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // --- Enable submit on name input ---
    $('input[name="nama"]').on('input', function() {
        $('#submit-btn').prop('disabled', $(this).val().trim() === '');
    });

    // --- Image Preview ---
    $('#logo_input').on('change', function() {
        if (this.files && this.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#logo_preview').attr('src', e.target.result);
                $('#logo_preview_container').fadeIn();
            }
            reader.readAsDataURL(this.files[0]);
        }
    });

    // --- Add Sponsor ---
    $('#sponsor-form').on('submit', function(e) {
        e.preventDefault();
        const form = $(this);
        const formData = new FormData(this);
        const btn = $('#submit-btn');

        btn.attr('data-kt-indicator', 'on').prop('disabled', true);

        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(res) {
                if (res.success) {
                    new PNotify({
                        title: 'Berhasil!',
                        text: res.message,
                        type: 'success'
                    });
                    location.reload();
                }
            },
            error: function() {
                btn.removeAttr('data-kt-indicator').prop('disabled', false);
                new PNotify({
                    title: 'Gagal!',
                    text: 'Terjadi kesalahan sistem.',
                    type: 'error'
                });
            }
        });
    });

    // --- Delete Sponsor ---
    $(document).on('click', '.delete-sponsor', function() {
        const id = $(this).data('id');
        const card = $(this).closest('.sponsor-card-item');

        Swal.fire({
            text: "Hapus sponsor ini?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Ya, Hapus!",
            cancelButtonText: "Batal",
            customClass: {
                confirmButton: "btn btn-danger",
                cancelButton: "btn btn-active-light"
            }
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/admin/sponsor/${id}`,
                    type: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    success: function(res) {
                        if (res.success) {
                            card.fadeOut(function() { $(this).remove(); });
                            new PNotify({
                                title: 'Berhasil!',
                                text: res.message,
                                type: 'success'
                            });
                        }
                    }
                });
            }
        });
    });
});
</script>
@endpush
@endsection
