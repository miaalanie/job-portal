@extends('layouts.admin')

@section('title', 'Edit Pengguna')
@section('page_title', 'Edit Data Pengguna')

@section('content')
<div class="card shadow-sm">
    <div class="card-header border-0 pt-6">
        <div class="card-title">
            <h3 class="fw-bold">Edit Profil: {{ $user->name }}</h3>
        </div>
        <div class="card-toolbar">
            <a href="{{ route('admin.users.index') }}" class="btn btn-light-primary btn-sm btn-flex btn-center">
                <i class="material-icons fs-5 me-1">arrow_back</i> Kembali
            </a>
        </div>
    </div>
    <div class="card-body">
        <form id="kt_user_edit_form" action="{{ route('admin.users.update', encrypt($user->id)) }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="row mb-6">
                <label class="col-lg-4 col-form-label required fw-bold fs-6">Nama Lengkap</label>
                <div class="col-lg-8 fv-row">
                    <input type="text" name="name" class="form-control form-control-lg form-control-solid" placeholder="Masukkan nama lengkap" value="{{ old('name', $user->name) }}" required />
                </div>
            </div>

            <div class="row mb-6">
                <label class="col-lg-4 col-form-label required fw-bold fs-6">Email</label>
                <div class="col-lg-8 fv-row">
                    <input type="email" name="email" class="form-control form-control-lg form-control-solid" placeholder="email@example.com" value="{{ old('email', $user->email) }}" required />
                </div>
            </div>

            <div class="row mb-6">
                <label class="col-lg-4 col-form-label required fw-bold fs-6">Pilih Role</label>
                <div class="col-lg-8 fv-row">
                    <select name="role" id="kt_role_select" class="form-select form-select-lg form-select-solid" data-control="select2" data-placeholder="Pilih Role..." required>
                        <option value="">Pilih Role...</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}" {{ old('role', $user->roles->first()?->name) == $role->name ? 'selected' : '' }}>{{ $role->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="row mb-6" id="even_selection_row" style="display: none;">
                <label class="col-lg-4 col-form-label required fw-bold fs-6">Pilih Event</label>
                <div class="col-lg-8 fv-row">
                    <select name="ideven" class="form-select form-select-lg form-select-solid" data-control="select2" data-placeholder="Pilih Event Bursa Kerja...">
                        <option value="">Pilih Event...</option>
                        @foreach($events as $even)
                            <option value="{{ $even->id }}" {{ old('ideven', $user->ideven) == $even->id ? 'selected' : '' }}>{{ $even->namaperiode }}</option>
                        @endforeach
                    </select>
                    <div class="form-text text-muted">Admin Event akan dibatasi aksesnya hanya pada event yang dipilih di atas.</div>
                </div>
            </div>

            <div class="row mb-6">
                <label class="col-lg-4 col-form-label fw-bold fs-6">Foto Profil</label>
                <div class="col-lg-8">
                    <div class="mb-3">
                        @if($user->gambar && $user->gambar !== 'no-image')
                            <img src="{{ asset('storage/' . $user->gambar) }}" class="rounded h-100px mb-3 d-block" alt="Current Profile">
                        @else
                            <div class="symbol symbol-100px mb-3 d-block">
                                <span class="symbol-label bg-light-primary text-primary fs-1 fw-bold">{{ substr($user->name, 0, 1) }}</span>
                            </div>
                        @endif
                    </div>
                    <div class="image-input image-input-outline" data-kt-image-input="true">
                        <input type="file" name="gambar" accept=".png, .jpg, .jpeg" class="form-control form-control-solid" />
                    </div>
                    <div class="form-text">Tipe file yang diizinkan: png, jpg, jpeg. Maksimum 2MB. Kosongkan jika tidak ingin mengubah foto.</div>
                </div>
            </div>

            <div class="d-flex justify-content-end">
                <button type="reset" class="btn btn-light me-3">Batal</button>
                <button type="submit" id="kt_user_edit_submit" class="btn btn-primary d-flex align-items-center">
                    <span class="indicator-label d-flex align-items-center">
                        <i class="material-icons me-2">save</i> Simpan Perubahan
                    </span>
                    <span class="indicator-progress">
                        Mohon tunggu... <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    const form = document.querySelector('#kt_user_edit_form');
    const submitButton = document.querySelector('#kt_user_edit_submit');

    // Toggle Even Selection based on Role
    $('#kt_role_select').on('change', function() {
        if ($(this).val() === 'Admin Event') {
            $('#even_selection_row').slideDown();
        } else {
            $('#even_selection_row').slideUp();
        }
    }).trigger('change');

    $(form).on('submit', function(e) {
        e.preventDefault();

        submitButton.setAttribute('data-kt-indicator', 'on');
        submitButton.disabled = true;
        NProgress.start();

        const formData = new FormData(this);

        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                NProgress.done();
                submitButton.removeAttribute('data-kt-indicator');
                
                if (response.status === 'success') {
                    new PNotify({
                        title: 'Berhasil!',
                        text: response.message,
                        type: 'success',
                        styling: 'brighttheme',
                        delay: 2000
                    });

                    setTimeout(function() {
                        window.location.href = response.redirect;
                    }, 1500);
                }
            },
            error: function(xhr) {
                NProgress.done();
                submitButton.removeAttribute('data-kt-indicator');
                submitButton.disabled = false;

                let message = 'Terjadi kesalahan sistem.';
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    message = Object.values(errors)[0][0]; 
                }

                new PNotify({
                    title: 'Gagal!',
                    text: message,
                    type: 'error',
                    styling: 'brighttheme',
                    delay: 3000
                });
            }
        });
    });
});
</script>
@endpush
