@extends('layouts.admin')

@section('title', 'Request Logs ML Service')
@section('page_title', 'Request Logs ML Service')

@section('content')

{{-- ===== SUMMARY CARDS ===== --}}
<div class="row g-5 mb-8">
    <div class="col-md-3">
        <div class="card shadow-sm h-100">
            <div class="card-body text-center">
                <div class="text-muted fs-8 mb-1">Total Request</div>
                <div class="fs-2x fw-bold text-gray-800">{{ number_format($stats['total']) }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow-sm h-100">
            <div class="card-body text-center">
                <div class="text-muted fs-8 mb-1">Success Rate</div>
                <div class="fs-2x fw-bold text-{{ $stats['success_rate'] >= 95 ? 'success' : ($stats['success_rate'] >= 80 ? 'warning' : 'danger') }}">
                    {{ $stats['success_rate'] }}%
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow-sm h-100">
            <div class="card-body text-center">
                <div class="text-muted fs-8 mb-1">Error</div>
                <div class="fs-2x fw-bold text-danger">{{ number_format($stats['errors']) }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow-sm h-100">
            <div class="card-body text-center">
                <div class="text-muted fs-8 mb-1">Avg Duration</div>
                <div class="fs-2x fw-bold text-gray-800">
                    {{ $stats['avg_duration_ms'] >= 1000 ? number_format($stats['avg_duration_ms'] / 1000, 2) . ' s' : number_format($stats['avg_duration_ms'], 0) . ' ms' }}
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ===== PER-TYPE BREAKDOWN ===== --}}
@if($stats['by_type']->isNotEmpty())
<div class="card shadow-sm mb-8">
    <div class="card-header">
        <h3 class="card-title">Breakdown per Tipe Request</h3>
    </div>
    <div class="card-body py-4">
        <div class="table-responsive">
            <table class="table table-row-dashed table-row-gray-300 gy-3">
                <thead>
                    <tr class="fw-bold text-muted fs-7">
                        <th>Tipe Request</th>
                        <th class="text-end">Total</th>
                        <th class="text-end">Success</th>
                        <th class="text-end">Error</th>
                        <th class="text-end">Avg Duration</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($stats['by_type'] as $type => $data)
                    <tr>
                        <td>
                            @php
                                $badge = match($type) {
                                    'embedding_pelamar' => 'primary',
                                    'embedding_lowongan' => 'info',
                                    'match' => 'success',
                                    'rank_applicants' => 'warning',
                                    'cf_recommendation' => 'dark',
                                    default => 'secondary',
                                };
                                $label = $requestTypes[$type] ?? $type;
                            @endphp
                            <span class="badge badge-light-{{ $badge }}">{{ $label }}</span>
                        </td>
                        <td class="text-end fw-bold">{{ number_format($data->total) }}</td>
                        <td class="text-end">
                            @php $successCount = $data->total - ($data->errors ?? 0); @endphp
                            <span class="text-success fw-bold">{{ number_format($successCount) }}</span>
                        </td>
                        <td class="text-end">
                            @if($data->errors > 0)
                                <span class="text-danger fw-bold">{{ number_format($data->errors) }}</span>
                            @else
                                <span class="text-muted">0</span>
                            @endif
                        </td>
                        <td class="text-end">
                            {{ $data->avg_ms >= 1000 ? number_format($data->avg_ms / 1000, 2) . ' s' : number_format($data->avg_ms, 0) . ' ms' }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

{{-- ===== FILTERS ===== --}}
<div class="card shadow-sm mb-6">
    <div class="card-body py-4">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-2">
                <label class="form-label fs-8 text-muted">Tipe Request</label>
                <select name="request_type" class="form-select form-select-sm">
                    <option value="">Semua</option>
                    @foreach($requestTypes as $value => $label)
                        <option value="{{ $value }}" {{ request('request_type') === $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label fs-8 text-muted">Status</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">Semua</option>
                    <option value="success" {{ request('status') === 'success' ? 'selected' : '' }}>Success</option>
                    <option value="error" {{ request('status') === 'error' ? 'selected' : '' }}>Error</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label fs-8 text-muted">Dari Tanggal</label>
                <input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label fs-8 text-muted">Sampai Tanggal</label>
                <input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label fs-8 text-muted">Cari</label>
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Request ID / endpoint..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-sm btn-primary flex-grow-1">
                    <i class="material-icons fs-5 me-1">filter_list</i> Filter
                </button>
                <a href="{{ route('admin.recommendation.request-logs') }}" class="btn btn-sm btn-light">
                    <i class="material-icons fs-5">refresh</i>
                </a>
            </div>
        </form>
    </div>
</div>

{{-- ===== LOG TABLE ===== --}}
<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title">Log Request</h3>
        <span class="text-muted fs-8">{{ $logs->total() }} data ditemukan</span>
    </div>
    <div class="card-body py-0">
        @if($logs->isEmpty())
            <div class="text-center py-10 text-muted">
                <i class="material-icons fs-2x mb-2">search_off</i>
                <div>Belum ada log request ditemukan.</div>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-row-dashed table-row-gray-300 align-middle gy-3">
                    <thead>
                        <tr class="fw-bold text-muted fs-7">
                            <th>Waktu</th>
                            <th>Tipe</th>
                            <th>Endpoint</th>
                            <th>Status</th>
                            <th class="text-end">Duration</th>
                            <th>Detail</th>
                            <th>Error</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($logs as $log)
                        <tr>
                            <td>
                                <div class="fs-8 text-gray-700">{{ $log->created_at->format('d M Y') }}</div>
                                <div class="fs-8 text-muted">{{ $log->created_at->format('H:i:s') }}</div>
                            </td>
                            <td>
                                @php
                                    $badge = $log->type_badge;
                                    $label = $log->type_label;
                                @endphp
                                <span class="badge badge-light-{{ $badge }}">{{ $label }}</span>
                            </td>
                            <td>
                                <span class="fs-8 text-gray-600" title="{{ $log->endpoint }}">{{ Str::limit($log->endpoint, 35) }}</span>
                            </td>
                            <td>
                                @if($log->status === 'success')
                                    <span class="badge badge-light-success">
                                        <i class="material-icons fs-8 me-1" style="vertical-align: text-bottom;">check_circle</i> Success
                                    </span>
                                @else
                                    <span class="badge badge-light-danger">
                                        <i class="material-icons fs-8 me-1" style="vertical-align: text-bottom;">error</i> Error
                                    </span>
                                @endif
                            </td>
                            <td class="text-end">
                                @php
                                    $durClass = 'text-gray-700';
                                    if ($log->duration_ms !== null) {
                                        if ($log->duration_ms > 5000) $durClass = 'text-danger fw-bold';
                                        elseif ($log->duration_ms > 2000) $durClass = 'text-warning fw-bold';
                                    }
                                @endphp
                                <span class="{{ $durClass }}">{{ $log->formatted_duration }}</span>
                            </td>
                            <td>
                                <div class="d-flex flex-column gap-1 fs-8">
                                    @if($log->pelamar_id)
                                        <span><i class="material-icons fs-9 text-muted me-1" style="vertical-align: text-bottom;">person</i> Pelamar #{{ $log->pelamar_id }}</span>
                                    @endif
                                    @if($log->lowongan_id)
                                        <span><i class="material-icons fs-9 text-muted me-1" style="vertical-align: text-bottom;">work</i> Loker #{{ $log->lowongan_id }}</span>
                                    @endif
                                    @if($log->total_records_embedded)
                                        <span class="text-muted">{{ $log->total_records_embedded }} records embedded</span>
                                    @endif
                                    @if($log->total_recommendations)
                                        <span class="text-muted">{{ $log->total_recommendations }} rekomendasi</span>
                                    @endif
                                    @if($log->total_ranked)
                                        <span class="text-muted">{{ $log->total_ranked }} ranked</span>
                                    @endif
                                    @if($log->result_count)
                                        <span class="text-muted">{{ $log->result_count }} hasil CF</span>
                                    @endif
                                    @if($log->model_version)
                                        <span class="text-muted"><i class="material-icons fs-9 me-1" style="vertical-align: text-bottom;">memory</i> {{ Str::limit($log->model_version, 30) }}</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                @if($log->error)
                                    <span class="text-danger fs-8" data-bs-toggle="tooltip" data-bs-placement="left" title="{{ $log->error }}">{{ Str::limit($log->error, 60) }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    @if($logs->hasPages())
    <div class="card-footer">
        {{ $logs->links() }}
    </div>
    @endif
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.forEach(function (el) {
        new bootstrap.Tooltip(el);
    });
});
</script>

@endsection