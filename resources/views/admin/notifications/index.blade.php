@extends('layouts.admin')

@section('title', 'Notifikasi Sistem')
@section('page_title', 'Semua Notifikasi')

@section('content')
<div class="card shadow-sm border-0">
    <div class="card-header border-0 pt-7">
        <h3 class="card-title align-items-start flex-column">
            <span class="card-label fw-bold text-gray-800">Daftar Notifikasi</span>
            <span class="text-muted mt-1 fw-semibold fs-7">Kelola pemberitahuan aktivitas sistem</span>
        </h3>
        <div class="card-toolbar">
            <form action="{{ route('admin.notifications.mark-all-read') }}" method="POST" class="me-2">
                @csrf
                <button type="submit" class="btn btn-sm btn-light-primary fw-bold">
                    <i class="material-icons fs-6 me-1">done_all</i> Tandai Semua Dibaca
                </button>
            </form>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table align-middle table-row-dashed fs-6 gy-5">
                <thead>
                    <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                        <th class="min-w-50px">Status</th>
                        <th class="min-w-200px">Notifikasi</th>
                        <th class="min-w-100px">Waktu</th>
                        <th class="text-end min-w-100px">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 fw-bold">
                    @forelse($notifications as $notif)
                    <tr class="{{ $notif->is_read ? 'opacity-75' : 'bg-light-primary' }}">
                        <td>
                            @if($notif->is_read)
                                <span class="badge badge-light fw-bold">Dibaca</span>
                            @else
                                <span class="badge badge-primary fw-bold">Baru</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex flex-column">
                                <a href="{{ route('admin.notifications.read', $notif->id) }}" class="text-gray-800 text-hover-primary mb-1">{{ $notif->title }}</a>
                                <span class="fs-7 text-muted fw-normal">{{ $notif->message }}</span>
                            </div>
                        </td>
                        <td>
                            <span class="fs-7">{{ \Carbon\Carbon::parse($notif->created_at)->diffForHumans() }}</span>
                        </td>
                        <td class="text-end">
                            <form action="{{ route('admin.notifications.destroy', $notif->id) }}" method="POST" onsubmit="return confirm('Hapus notifikasi ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-icon btn-bg-light btn-active-color-danger btn-sm">
                                    <i class="material-icons fs-5">delete</i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-10">
                            <div class="text-muted">Tidak ada notifikasi ditemukan</div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-5">
            {{ $notifications->links() }}
        </div>
    </div>
</div>
@endsection
