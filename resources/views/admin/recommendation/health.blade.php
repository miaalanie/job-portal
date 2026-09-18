@extends('layouts.admin')

@section('title', 'Health ML Service')
@section('page_title', 'Health ML Service')

@section('content')
@php
$reachable = $health['reachable'] ?? false;
$serviceStatus = $health['data']['status'] ?? null;
$modelLoaded = $health['data']['model_loaded'] ?? null;
$meta = $health['meta'] ?? [];
$uvicornCommand = $meta['uvicorn_command'] ?? 'uvicorn app.main:app --reload';
$host = $meta['host'] ?? parse_url($meta['ml_service_url'] ?? '', PHP_URL_HOST) ?? 'localhost';
$scheme = parse_url($meta['ml_service_url'] ?? '', PHP_URL_SCHEME) ?? 'http';

if (!$reachable) {
$state = 'danger';
$badgeLabel = 'Tidak Terhubung';
$icon = 'error';
$message = 'Gagal terhubung ke mesin rekomendasi. Kemungkinan service belum dijalankan atau alamat ML_SERVICE_URL salah.';
} elseif ($serviceStatus === 'degraded') {
$state = 'warning';
$badgeLabel = 'Sebagian Bermasalah';
$icon = 'warning';
$message = 'Server aktif, tetapi model AI belum berhasil dimuat. Rekomendasi bisa gagal sampai ini diperbaiki.';
} else {
$state = 'success';
$badgeLabel = 'Terhubung';
$icon = 'check_circle';
$message = 'Mesin rekomendasi merespons normal dan siap digunakan.';
}
@endphp

@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif
@if($errors->any())
<div class="alert alert-danger">{{ $errors->first() }}</div>
@endif

{{-- ===== STATUS ===== --}}
<div class="card shadow-sm mb-6">
    <div class="card-header">
        <h3 class="card-title">Status Koneksi Mesin Rekomendasi</h3>
        <div class="card-toolbar d-flex align-items-center gap-3">
            <span class="text-muted fs-8" id="health-last-checked" data-checked-at="{{ $health['checked_at'] ?? '' }}"></span>
            <button type="button" id="health-refresh-button" class="btn btn-sm btn-light-primary" data-url="{{ route('admin.recommendation.health.check') }}">
                <span class="spinner-border spinner-border-sm me-1 d-none"></span>
                <span class="btn-label">Cek Ulang</span>
            </button>
            <span id="health-badge" class="badge badge-light-{{ $state }} fs-6">{{ $badgeLabel }}</span>
        </div>
    </div>

    <div class="card-body">
        <div class="d-flex align-items-center gap-3 mb-6">
            <i id="health-icon" class="material-icons fs-2x text-{{ $state }}">{{ $icon }}</i>
            <div id="health-message" class="text-gray-700">{{ $message }}</div>
        </div>

        @if($reachable)
        <div class="d-flex flex-wrap gap-6 mb-2 pb-6 border-bottom" id="health-metrics">
            <div>
                <div class="text-muted fs-8">Status HTTP</div>
                <div id="health-http-status" class="fw-bold">{{ $health['status'] ?? '-' }}</div>
            </div>
            <div>
                <div class="text-muted fs-8">Model</div>
                <div id="health-model-name" class="fw-bold">{{ $health['data']['model'] ?? '-' }}</div>
            </div>
            <div>
                <div class="text-muted fs-8">Dimensi embedding</div>
                <div id="health-embedding-dim" class="fw-bold">{{ $health['data']['embedding_dimension'] ?? '-' }}</div>
            </div>
            <div>
                <div class="text-muted fs-8">Model dimuat</div>
                <div id="health-model-loaded" class="fw-bold">
                    @if($modelLoaded === true) <span class="text-success">Ya</span>
                    @elseif($modelLoaded === false) <span class="text-danger">Tidak</span>
                    @else - @endif
                </div>
            </div>
        </div>
        @endif

        <div id="health-error-wrapper" class="{{ $reachable ? 'd-none' : '' }}">
            <details>
                <summary class="text-muted fs-7" style="cursor: pointer;">Detail teknis</summary>
                <div id="health-error-detail" class="bg-light rounded p-3 mt-2 fs-8 text-muted" style="font-family: monospace; word-break: break-word;">{{ $health['error'] ?: 'Tidak ada respons dari service.' }}</div>
            </details>
        </div>
    </div>
</div>

{{-- ===== KONFIGURASI ===== --}}
<div class="card shadow-sm">
    <div class="card-header">
        <h3 class="card-title">Konfigurasi Service</h3>
    </div>
    <div class="card-body">
        <div class="row g-6 mb-6 align-items-center">
            <div class="col-md-6">
                <form method="POST" action="{{ route('admin.recommendation.health.update') }}">
                    @csrf
                    <label for="ml_port" class="form-label fw-bold">Ubah Port</label>
                    <div class="d-flex gap-2">
                        <input id="ml_port" type="number" name="ml_port" class="form-control" min="1" max="65535" value="{{ $meta['port'] ?? '' }}" placeholder="misal 8001" required>
                        <button type="submit" class="btn btn-primary text-nowrap">Simpan</button>
                    </div>
                    <div class="form-text">Host tetap {{ $scheme }}://{{ $host }}, hanya port yang bisa diubah dari sini.</div>
                </form>
            </div>

            <div class="col-md-6 ps-md-10">
                <label class="form-label fw-bold">Alamat service saat ini</label>
                <div class="fs-5">
                    <span class="text-muted">{{ $scheme }}://{{ $host }}:</span><span class="fw-bold">{{ $meta['port'] ?? '-' }}</span>
                </div>
            </div>
        </div>

        <div>
            <label class="form-label fw-bold">Jalankan service dengan command berikut</label>
            <div class="d-flex align-items-center gap-2 bg-light rounded p-3">
                <code id="uvicorn-command" class="flex-grow-1 text-dark">{{ $uvicornCommand }}</code>
                <button type="button" class="btn btn-sm btn-icon btn-light copy-uvicorn-command" data-copy="{{ $uvicornCommand }}" title="Salin">
                    <i class="material-icons fs-5">content_copy</i>
                </button>
            </div>
        </div>

    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const el = (id) => document.getElementById(id);
        const refreshButton = el('health-refresh-button');
        const lastChecked = el('health-last-checked');

        const STATES = {
            down: {
                state: 'danger',
                label: 'Tidak Terhubung',
                icon: 'error',
                message: 'Gagal terhubung ke mesin rekomendasi. Kemungkinan service belum dijalankan atau alamat ML_SERVICE_URL salah.'
            },
            degraded: {
                state: 'warning',
                label: 'Sebagian Bermasalah',
                icon: 'warning',
                message: 'Server aktif, tetapi model AI belum berhasil dimuat. Rekomendasi bisa gagal sampai ini diperbaiki.'
            },
            ok: {
                state: 'success',
                label: 'Terhubung',
                icon: 'check_circle',
                message: 'Mesin rekomendasi merespons normal dan siap digunakan.'
            },
        };

        function relativeTime(iso) {
            if (!iso) return 'belum pernah dicek';
            const diffMin = Math.max(0, Math.round((Date.now() - new Date(iso)) / 60000));
            if (diffMin < 1) return 'dicek baru saja';
            if (diffMin < 60) return 'dicek ' + diffMin + ' menit lalu';
            const diffHour = Math.round(diffMin / 60);
            if (diffHour < 24) return 'dicek ' + diffHour + ' jam lalu';
            return 'dicek ' + Math.round(diffHour / 24) + ' hari lalu';
        }

        function exactTime(iso) {
            if (!iso) return '';
            return new Intl.DateTimeFormat('id-ID', {
                day: '2-digit',
                month: 'short',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                timeZone: 'Asia/Jakarta'
            }).format(new Date(iso)) + ' WIB';
        }

        function updateLastChecked(iso) {
            lastChecked.dataset.checkedAt = iso || '';
            lastChecked.title = exactTime(iso);
            lastChecked.textContent = relativeTime(iso);
        }

        function render(payload) {
            const data = payload.data ?? {};
            const meta = payload.meta ?? {};
            const key = !payload.reachable ? 'down' : (data.status === 'degraded' ? 'degraded' : 'ok');
            const preset = STATES[key];

            el('health-badge').className = 'badge badge-light-' + preset.state + ' fs-6';
            el('health-badge').textContent = preset.label;
            el('health-icon').className = 'material-icons fs-2x text-' + preset.state;
            el('health-icon').textContent = preset.icon;
            el('health-message').textContent = preset.message;

            const metrics = el('health-metrics');
            const errorWrapper = el('health-error-wrapper');
            if (payload.reachable) {
                if (metrics) metrics.classList.remove('d-none');
                errorWrapper.classList.add('d-none');
                if (el('health-http-status')) el('health-http-status').textContent = payload.status ?? '-';
                if (el('health-model-name')) el('health-model-name').textContent = data.model ?? '-';
                if (el('health-embedding-dim')) el('health-embedding-dim').textContent = data.embedding_dimension ?? '-';
                if (el('health-model-loaded')) {
                    el('health-model-loaded').innerHTML = data.model_loaded === true ?
                        '<span class="text-success">Ya</span>' :
                        data.model_loaded === false ?
                        '<span class="text-danger">Tidak</span>' :
                        '-';
                }
            } else {
                if (metrics) metrics.classList.add('d-none');
                errorWrapper.classList.remove('d-none');
                el('health-error-detail').textContent = payload.error || 'Tidak ada respons dari service.';
            }

            const command = meta.uvicorn_command || el('uvicorn-command').textContent;
            el('uvicorn-command').textContent = command;
            document.querySelector('.copy-uvicorn-command').dataset.copy = command;

            updateLastChecked(payload.checked_at);
        }

        refreshButton.addEventListener('click', async function() {
            const spinner = refreshButton.querySelector('.spinner-border');
            const label = refreshButton.querySelector('.btn-label');
            refreshButton.disabled = true;
            spinner.classList.remove('d-none');
            label.textContent = 'Mengecek...';

            try {
                const res = await fetch(refreshButton.dataset.url, {
                    headers: {
                        'Accept': 'application/json'
                    }
                });
                if (!res.ok) throw new Error();
                render(await res.json());
            } catch {
                alert('Gagal mengecek status, coba lagi.');
            } finally {
                refreshButton.disabled = false;
                spinner.classList.add('d-none');
                label.textContent = 'Cek Ulang';
            }
        });

        document.querySelector('.copy-uvicorn-command').addEventListener('click', async function() {
            const btn = this;
            const icon = btn.querySelector('i');
            try {
                await navigator.clipboard.writeText(btn.dataset.copy);
                icon.textContent = 'check';
                setTimeout(() => icon.textContent = 'content_copy', 1500);
            } catch {
                icon.textContent = 'close';
                setTimeout(() => icon.textContent = 'content_copy', 1500);
            }
        });

        updateLastChecked(lastChecked.dataset.checkedAt);
        setInterval(() => updateLastChecked(lastChecked.dataset.checkedAt), 30000);
    });
</script>
@endsection