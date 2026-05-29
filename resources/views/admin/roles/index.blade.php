@extends('layouts.admin')

@section('title', 'Kelola Role')
@section('page_title', 'Kelola Role')

@section('content')
<div class="d-flex flex-column flex-column-fluid">
    <div class="row g-6 g-xl-9">
        @foreach($roles as $role)
            @php
                $colors = ['primary', 'success', 'info', 'warning', 'danger'];
                $color = $colors[$loop->index % count($colors)];
            @endphp
            <div class="col-md-6 col-xl-4">
                <div class="card border-hover-{{ $color }} shadow-sm h-100 position-relative overflow-hidden">
                    <div class="position-absolute top-0 end-0 p-3" style="opacity: 0.1;">
                        <i class="material-icons" style="font-size: 100px;">security</i>
                    </div>
                    
                    <div class="card-body p-9">
                        <div class="d-flex align-items-center mb-5">
                            <div class="symbol symbol-50px me-5">
                                <span class="symbol-label bg-light-{{ $color }}">
                                    <i class="material-icons text-{{ $color }} fs-2x">groups</i>
                                </span>
                            </div>
                            <div class="d-flex flex-column">
                                <a href="{{ route('admin.roles.show', $role->id) }}" class="fs-4 text-gray-800 text-hover-{{ $color }} fw-bold">{{ $role->name }}</a>
                                <span class="text-gray-400 fw-semibold fs-7">{{ $role->users_count }} Pengguna Terdaftar</span>
                            </div>
                        </div>

                        <div class="d-flex flex-stack flex-wrap">
                            <div class="d-flex align-items-center">
                                <div class="symbol-group symbol-hover">
                                    @php
                                        // Get 5 random user avatars for this role
                                        $sampleUsers = \App\Models\User::role($role->name)->limit(5)->get();
                                    @endphp
                                    @foreach($sampleUsers as $user)
                                        <div class="symbol symbol-35px symbol-circle" data-bs-toggle="tooltip" title="{{ $user->name }}">
                                            <img src="{{ $user->gambar == 'no-image' ? 'https://preview.keenthemes.com/metronic8/demo1/assets/media/avatars/300-1.jpg' : asset('storage/'.$user->gambar) }}" alt="{{ $user->name }}" />
                                        </div>
                                    @endforeach
                                    @if($role->users_count > 5)
                                        <div class="symbol symbol-35px symbol-circle">
                                            <span class="symbol-label bg-{{ $color }} text-inverse-{{ $color }} fw-bold">+{{ $role->users_count - 5 }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="separator separator-dashed my-6"></div>

                        <div class="d-flex justify-content-between align-items-center">
                            <a href="{{ route('admin.roles.show', $role->id) }}" class="btn btn-sm btn-light-{{ $color }} fw-bold">
                                <i class="material-icons fs-5 me-1">visibility</i> Detail & User
                            </a>
                            
                            @if($role->name !== 'Superadmin' && $role->users_count == 0)
                                <form action="{{ route('admin.roles.destroy', $role->id) }}" method="POST" onsubmit="return confirm('Hapus role ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-icon btn-light-danger">
                                        <i class="material-icons fs-5">delete</i>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
