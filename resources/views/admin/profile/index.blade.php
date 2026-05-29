@extends('layouts.admin')

@section('title', 'Manage Profile')
@section('page_title', 'Profil Saya')

@section('content')
<div class="row g-7">
    <!-- Left Column: User Summary -->
    <div class="col-lg-4">
        <div class="card shadow-sm border-0 mb-5">
            <div class="card-body pt-15 pb-10 text-center">
                @php
                    $profilePhoto = ($user->gambar && $user->gambar !== 'no-image') 
                        ? asset('storage/' . $user->gambar) 
                        : 'https://preview.keenthemes.com/metronic8/demo1/assets/media/avatars/300-1.jpg';
                @endphp
                <div class="symbol symbol-100px symbol-circle mb-7 shadow-sm border border-2 border-primary">
                    <img src="{{ $profilePhoto }}" alt="image">
                </div>
                <h3 class="text-gray-800 fw-bold fs-3 mb-1">{{ $user->name }}</h3>
                <div class="text-muted fw-semibold fs-7 mb-6">
                    <span class="badge badge-light-danger fw-bold px-3 py-2 rounded-pill">{{ $user->roles->first()->name ?? 'Administrator' }}</span>
                </div>
                
                <div class="d-flex flex-center flex-wrap mb-5">
                    <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 mx-3 mb-3">
                        <div class="fs-6 fw-bold text-gray-700">Terdaftar</div>
                        <div class="fw-semibold text-gray-400 fs-7">{{ $user->created_at->format('M Y') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column: Profile Form -->
    <div class="col-lg-8">
        <div class="card shadow-sm border-0">
            <div class="card-header border-0 pt-6">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bold text-gray-800 fs-3">Edit Detail Akun</span>
                    <span class="text-muted mt-1 fw-semibold fs-7">Kelola informasi dasar dan keamanan akses Anda</span>
                </h3>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <!-- Basic Info Section -->
                    <div class="row mb-10">
                        <label class="col-lg-4 col-form-label fw-bold fs-6">Foto Profil</label>
                        <div class="col-lg-8">
                            <div class="image-input image-input-outline" data-kt-image-input="true">
                                <input type="file" name="gambar" accept=".png, .jpg, .jpeg" class="form-control form-control-solid" />
                            </div>
                            <div class="form-text text-muted">Format: PNG, JPG, JPEG (Maks. 2MB). Kosongkan jika tidak ingin mengubah.</div>
                        </div>
                    </div>

                    <div class="row mb-10">
                        <label class="col-lg-4 col-form-label required fw-bold fs-6">Nama Lengkap</label>
                        <div class="col-lg-8">
                            <input type="text" name="name" class="form-control form-control-lg form-control-solid" placeholder="Nama Anda" value="{{ old('name', $user->name) }}">
                        </div>
                    </div>
                    
                    <div class="row mb-10">
                        <label class="col-lg-4 col-form-label required fw-bold fs-6">Email Address</label>
                        <div class="col-lg-8">
                            <input type="email" name="email" class="form-control form-control-lg form-control-solid" placeholder="email@domain.com" value="{{ old('email', $user->email) }}">
                            <div class="form-text text-muted">Email ini digunakan untuk login dan korespondensi resmi.</div>
                        </div>
                    </div>

                    <div class="separator separator-dashed border-gray-300 my-10"></div>

                    <!-- Password Section -->
                    <div class="row mb-10">
                        <label class="col-lg-4 col-form-label fw-bold fs-6 text-primary">
                            Ubah Password
                        </label>
                        <div class="col-lg-8">
                            <div class="alert alert-dismissible bg-light-primary d-flex flex-column flex-sm-row p-5 mb-5">
                                <i class="material-icons text-primary fs-2hx me-4 mb-5 mb-sm-0">info</i>
                                <div class="d-flex flex-column pe-0 pe-sm-10">
                                    <h4 class="fw-semibold text-primary fs-6">Hanya isi jika ingin merubah password</h4>
                                    <span class="fs-8 text-gray-600">Pastikan password yang Anda masukkan kuat dan mudah diingat.</span>
                                </div>
                            </div>
                            
                            <div class="row mb-5">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold fs-7">Password Baru</label>
                                    <input type="password" name="password" class="form-control form-control-solid" placeholder="Min. 8 Karakter">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold fs-7">Ulangi Password Baru</label>
                                    <input type="password" name="password_confirmation" class="form-control form-control-solid" placeholder="Konfirmasi">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end pt-5">
                        <button type="reset" class="btn btn-light me-3">Reset Perubahan</button>
                        <button type="submit" class="btn btn-primary fw-bold px-8">Update Profil</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
