{{-- resources/views/admin/perusahaan/loker_applicants_section.blade.php --}}

<div class="card border-0 shadow-sm rounded-4 mb-4" id="applicants-section">
    <div class="card-header bg-white border-0 p-4 pb-1 d-flex align-items-center justify-content-between">
        <h5 class="fw-bold text-dark mb-0">Daftar Pelamar</h5>
        <span id="applicants-badge" class="badge bg-light text-muted fs-8 fw-normal">
            <span class="spinner-border spinner-border-sm me-1" style="width:10px;height:10px;"></span>
            Memuat ranking...
        </span>
    </div>

    <div class="card-body p-4">

        {{-- Skeleton --}}
        <div id="applicants-skeleton" class="vstack gap-2">
            @for ($i = 0; $i < 5; $i++)
                <div class="d-flex align-items-center gap-3 p-3 rounded-4" style="background:#f8f9fa;">
                    <div class="rounded-circle shimmer-box" style="width:45px;height:45px;min-width:45px;"></div>
                    <div class="flex-grow-1">
                        <div class="rounded mb-2 shimmer-box" style="height:14px;width:55%;"></div>
                        <div class="rounded shimmer-box" style="height:11px;width:35%;"></div>
                    </div>
                    <div class="rounded-pill shimmer-box" style="height:26px;width:100px;"></div>
                    <div class="rounded-circle shimmer-box" style="width:32px;height:32px;min-width:32px;"></div>
                </div>
            @endfor
        </div>

        {{-- Error --}}
        <div id="applicants-error" class="text-center py-5 d-none">
            <i class="material-icons text-muted mb-2" style="font-size:40px;">cloud_off</i>
            <p class="text-muted mb-2">Gagal memuat data pelamar.</p>
            <button class="btn btn-sm btn-outline-secondary rounded-pill" onclick="loadApplicantsTable()">
                <i class="material-icons fs-8 me-1">refresh</i> Coba Lagi
            </button>
        </div>

        {{-- Result — HTML dari server di-inject ke sini --}}
        <div id="applicants-result" class="d-none"></div>

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
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    loadApplicantsTable();
});

async function loadApplicantsTable() {
    showAppEl('applicants-skeleton');
    hideAppEl('applicants-error');
    hideAppEl('applicants-result');
    setApplicantsBadge('loading');

    try {
        const response = await fetch('{{ route("admin.perusahaan.loker.applicants-ranking", encrypt($loker->id)) }}', {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? ''
            }
        });

        if (!response.ok) throw new Error('HTTP ' + response.status);

        const data = await response.json();
        hideAppEl('applicants-skeleton');

        if (!data.success) {
            showAppEl('applicants-error');
            setApplicantsBadge('error');
            return;
        }

        document.getElementById('applicants-result').innerHTML = data.html;
        showAppEl('applicants-result');
        setApplicantsBadge('done', data.total, data.has_ranking);

    } catch (err) {
        console.error('Applicants ranking error:', err);
        hideAppEl('applicants-skeleton');
        showAppEl('applicants-error');
        setApplicantsBadge('error');
    }
}

function showAppEl(id) { document.getElementById(id)?.classList.remove('d-none'); }
function hideAppEl(id) { document.getElementById(id)?.classList.add('d-none'); }

function setApplicantsBadge(state, total, hasRanking) {
    const badge = document.getElementById('applicants-badge');
    if (!badge) return;
    const html = {
        loading: '<span class="spinner-border spinner-border-sm me-1" style="width:10px;height:10px;"></span> Memuat ranking...',
        done:    '<i class="material-icons fs-9 me-1">' + (hasRanking ? 'star' : 'people') + '</i> '
                 + total + ' pelamar' + (hasRanking ? ' · Terranking' : ''),
        error:   '<i class="material-icons fs-9 me-1">error</i> Gagal',
    };
    const cls = {
        loading: 'bg-light text-muted',
        done:    hasRanking ? 'bg-success bg-opacity-10 text-success' : 'bg-primary bg-opacity-10 text-primary',
        error:   'bg-danger bg-opacity-10 text-danger',
    };
    badge.innerHTML = html[state] ?? '';
    badge.className = 'badge fs-8 fw-normal ' + (cls[state] ?? 'bg-light text-muted');
}
</script>
@endpush