@extends('layouts.admin')

@section('title', 'Detail Role: ' . $role->name)
@section('page_title', 'Daftar Pengguna: ' . $role->name)

@section('content')
<div class="d-flex flex-column flex-column-fluid">
    <div class="card mb-7 shadow-sm">
        <div class="card-header border-0 pt-6">
            <div class="card-title">
                <div class="d-flex align-items-center position-relative my-1">
                    <i class="material-icons position-absolute ms-5">groups</i>
                    <h3 class="fw-bold ms-12 mb-0">{{ $role->name }} <span class="badge badge-light-primary ms-2">{{ $users->total() }} Total</span></h3>
                </div>
            </div>
            <div class="card-toolbar">
                <a href="{{ route('admin.roles') }}" class="btn btn-light-primary btn-sm btn-flex btn-center">
                    <i class="material-icons fs-5 me-1">arrow_back</i> Kembali ke Role
                </a>
            </div>
        </div>
    </div>

    <div class="row g-6 g-xl-9">
        @forelse($users as $user)
            <div class="col-md-6 col-xl-4">
                <div class="card border-hover-primary shadow-sm h-100">
                    <div class="card-body p-9">
                        <div class="d-flex flex-center flex-column mb-5">
                            <div class="symbol symbol-70px symbol-circle mb-5 border border-3 border-white shadow-sm">
                                <img src="{{ $user->gambar == 'no-image' ? 'https://preview.keenthemes.com/metronic8/demo1/assets/media/avatars/300-1.jpg' : asset('storage/'.$user->gambar) }}" alt="image" />
                                @if($user->statusaktif == 1)
                                    <div class="bg-success position-absolute border border-2 border-white h-15px w-15px rounded-circle translate-middle start-100 top-100 ms-n3 mt-n3"></div>
                                @else
                                    <div class="bg-danger position-absolute border border-2 border-white h-15px w-15px rounded-circle translate-middle start-100 top-100 ms-n3 mt-n3"></div>
                                @endif
                            </div>
                            
                            <a href="#" class="fs-3 text-gray-800 text-hover-primary fw-bold mb-0">{{ $user->name }}</a>
                            <div class="fw-semibold text-gray-400 mb-6">{{ $user->email }}</div>
                        </div>

                        <div class="d-flex flex-stack flex-wrap">
                            <div class="border border-gray-300 border-dashed rounded min-w-100px py-3 px-4 me-3 mb-3">
                                <div class="fs-6 text-gray-800 fw-bold">{{ $user->created_at->format('d M Y') }}</div>
                                <div class="fw-semibold text-gray-400">Terdaftar</div>
                            </div>
                            <div class="border border-gray-300 border-dashed rounded min-w-80px py-3 px-4 mb-3">
                                <div class="fs-6 fw-bold text-{{ $user->statusaktif == 1 ? 'success' : 'danger' }}">
                                    {{ $user->statusaktif == 1 ? 'Aktif' : 'Non-Aktif' }}
                                </div>
                                <div class="fw-semibold text-gray-400">Status</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-20">
                <div class="card shadow-none bg-light-warning">
                    <div class="card-body">
                        <h3 class="text-warning">Belum ada pengguna dengan role ini.</h3>
                    </div>
                </div>
            </div>
        @endforelse
    </div>

    <div class="d-flex flex-stack flex-wrap pt-10">
        <div class="fs-6 fw-semibold text-gray-700">Menampilkan {{ $users->firstItem() }} sampai {{ $users->lastItem() }} dari {{ $users->total() }} pengguna</div>
        {{ $users->links() }}
    </div>
</div>
@endsection
