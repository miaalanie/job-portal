{{-- resources/views/pelamar/rekomendasi-section.blade.php --}}

<div class="card border-0 shadow-sm rounded-4 mb-4" id="rekomendasi-section">
    <div class="card-header bg-white border-0 p-4 pb-1 d-flex align-items-center justify-content-between">
        <h5 class="fw-bold text-dark mb-0">Rekomendasi Lowongan</h5>
        <span id="rekomendasi-badge" class="badge bg-light text-muted fs-8 fw-normal">
            <span class="spinner-border spinner-border-sm me-1" style="width:10px;height:10px;"></span>
            Memuat...
        </span>
    </div>

    <div class="card-body p-4">

        {{-- Skeleton --}}
        <div id="rekomendasi-skeleton" class="vstack gap-3">
            @for ($i = 0; $i < 4; $i++)
                <div class="p-3 rounded-4 d-flex align-items-center gap-3" style="background:#f8f9fa;">
                    <div class="rounded-4 shimmer-box" style="width:50px;height:50px;min-width:50px;"></div>
                    <div class="flex-grow-1">
                        <div class="rounded mb-2 shimmer-box" style="height:14px;width:60%;"></div>
                        <div class="rounded shimmer-box" style="height:11px;width:40%;"></div>
                    </div>
                </div>
            @endfor
        </div>

        {{-- Error --}}
        <div id="rekomendasi-error" class="text-center py-4 d-none">
            <i class="material-icons text-muted mb-2" style="font-size:40px;">cloud_off</i>
            <p class="text-muted fs-8 mb-2">Rekomendasi tidak tersedia saat ini.</p>
            <button class="btn btn-sm btn-outline-secondary rounded-pill" onclick="loadRekomendasi()">
                <i class="material-icons fs-8 me-1">refresh</i> Coba Lagi
            </button>
        </div>

        {{-- Empty --}}
        <div id="rekomendasi-empty" class="text-center py-4 d-none">
            <i class="material-icons text-muted mb-2" style="font-size:40px;">work_off</i>
            <p class="text-muted fs-8 mb-0">Belum ada lowongan aktif saat ini.</p>
        </div>

        {{-- Hasil --}}
        <div id="rekomendasi-list" class="vstack gap-3 d-none" style="max-height: 420px; overflow-y: auto; padding-right: 4px;"></div>

    </div>
</div>

@push('styles')
<style>
@keyframes shimmer {
    0%   { background-position: 200% 0; }
    100% { background-position: -200% 0; }
}
.shimmer-box {
    background: linear-gradient(90deg, #e9ecef 25%, #f8f9fa 50%, #e9ecef 75%);
    background-size: 200% 100%;
    animation: shimmer 1.5s infinite;
}
.job-recommend-card {
    background: #f8f9fa;
    transition: background 0.2s ease, transform 0.2s ease;
}
.job-recommend-card:hover {
    background: #e8f4ff;
    transform: translateX(3px);
}
.score-badge {
    font-size: 10px;
    font-weight: 600;
    padding: 2px 8px;
    border-radius: 20px;
}
.score-green  { background: #d1f5ea; color: #0a7a52; }
.score-yellow { background: #fff3cd; color: #856404; }
.score-red    { background: #ffe0e0; color: #991b1b; }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    loadRekomendasi();
});

async function loadRekomendasi() {
    showEl('rekomendasi-skeleton');
    hideEl('rekomendasi-error');
    hideEl('rekomendasi-empty');
    hideEl('rekomendasi-list');
    setBadge('loading');

    try {
        const response = await fetch('{{ route("pelamar.rekomendasi") }}', {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? ''
            }
        });

        if (!response.ok) throw new Error('HTTP ' + response.status);

        const data = await response.json();
        hideEl('rekomendasi-skeleton');

        if (!data.success) {
            showEl('rekomendasi-error');
            setBadge('error');
            return;
        }

        if (!data.recommendations || data.recommendations.length === 0) {
            showEl('rekomendasi-empty');
            setBadge('empty');
            return;
        }

        renderCards(data.recommendations);
        setBadge('done', data.total);
        showEl('rekomendasi-list');

    } catch (err) {
        console.error('Rekomendasi error:', err);
        hideEl('rekomendasi-skeleton');
        showEl('rekomendasi-error');
        setBadge('error');
    }
}

function renderCards(recommendations) {
    const container = document.getElementById('rekomendasi-list');
    container.innerHTML = '';
    recommendations.forEach(job => {
        container.insertAdjacentHTML('beforeend', buildCard(job));
    });
}

function buildCard(job) {
    const logoHtml = job.perusahaan_logo
        ? '<img src="/storage/' + job.perusahaan_logo + '" style="width:32px;height:32px;object-fit:contain;">'
        : '<i class="material-icons text-muted fs-5">business</i>';

    const scoreClass = { green: 'score-green', yellow: 'score-yellow', red: 'score-red' }[job.color] ?? 'score-yellow';

    const gajiHtml = (job.gaji_awal || job.gaji_akhir)
        ? '<div class="d-flex align-items-center text-success fs-9 fw-medium mt-1"><i class="material-icons fs-10 me-1">payments</i><span>' + formatGaji(job.gaji_awal, job.gaji_akhir) + '</span></div>'
        : '';

    const tagsHtml = (job.tags ?? []).slice(0, 2).map(tag => {
        const cls = { success: 'bg-success bg-opacity-10 text-success', warning: 'bg-warning bg-opacity-10 text-warning', danger: 'bg-danger bg-opacity-10 text-danger' }[tag.type] ?? 'bg-secondary text-white';
        return '<span class="badge ' + cls + ' fs-9 fw-normal rounded-pill px-2">' + esc(tag.text) + '</span>';
    }).join('');

    return '<div class="job-recommend-card p-3 rounded-4 d-flex align-items-center gap-3">'
        + '<div class="bg-light rounded-4 p-2 d-flex align-items-center justify-content-center shadow-sm" style="width:50px;height:50px;min-width:50px;">' + logoHtml + '</div>'
        + '<div class="flex-grow-1 min-w-0">'
            + '<div class="d-flex align-items-start justify-content-between gap-2">'
                + '<h6 class="fw-bold text-dark fs-7 mb-1 text-truncate" style="max-width:180px;">' + esc(job.namalowongan) + '</h6>'
                + '<span class="score-badge ' + scoreClass + ' flex-shrink-0">' + job.match_percentage + '%</span>'
            + '</div>'
            + '<div class="text-muted fs-8 text-truncate">' + esc(job.perusahaan_nama ?? '-') + '</div>'
            + '<div class="text-muted fs-9">' + esc(job.kategori) + '</div>'
            + gajiHtml
            + (tagsHtml ? '<div class="d-flex flex-wrap gap-1 mt-2">' + tagsHtml + '</div>' : '')
        + '</div>'
        + '<a href="/lowongan-detail/' + (job.encrypted_id ?? '') + '" class="btn btn-sm btn-icon btn-light rounded-circle shadow-sm flex-shrink-0"><i class="material-icons fs-6">chevron_right</i></a>'
        + '</div>';
}

function showEl(id) { document.getElementById(id)?.classList.remove('d-none'); }
function hideEl(id) { document.getElementById(id)?.classList.add('d-none'); }
function esc(str) {
    return String(str ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}
function formatGaji(a, b) {
    const fmt = n => 'Rp ' + (n/1000000).toFixed(0) + ' jt';
    if (a && b) return fmt(a) + ' – ' + fmt(b);
    if (a) return fmt(a) + '+';
    if (b) return 's/d ' + fmt(b);
    return 'Nego';
}
function setBadge(state, total) {
    const badge = document.getElementById('rekomendasi-badge');
    if (!badge) return;
    const html = {
        loading: '<span class="spinner-border spinner-border-sm me-1" style="width:10px;height:10px;"></span> Memuat...',
        done:    '<i class="material-icons fs-9 me-1">check_circle</i> ' + total + ' lowongan',
        error:   '<i class="material-icons fs-9 me-1">error</i> Gagal',
        empty:   '<i class="material-icons fs-9 me-1">info</i> Tidak ada',
    };
    const cls = {
        loading: 'bg-light text-muted',
        done:    'bg-success bg-opacity-10 text-success',
        error:   'bg-danger bg-opacity-10 text-danger',
        empty:   'bg-light text-muted',
    };
    badge.innerHTML = html[state] ?? '';
    badge.className = 'badge fs-8 fw-normal ' + (cls[state] ?? 'bg-light text-muted');
}
</script>
@endpush