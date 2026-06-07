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

        {{-- Result --}}
        <div id="applicants-result" class="d-none"></div>
    </div>
</div>

@push('styles')
<style>
@keyframes shimmer { 0% { background-position: 200% 0; } 100% { background-position: -200% 0; } }
.shimmer-box {
    background: linear-gradient(90deg, #e9ecef 25%, #f8f9fa 50%, #e9ecef 75%);
    background-size: 200% 100%;
    animation: shimmer 1.5s infinite;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => loadApplicantsTable());

async function loadApplicantsTable() {
    showAppEl('applicants-skeleton');
    hideAppEl('applicants-error');
    hideAppEl('applicants-result');
    setApplicantsBadge('loading');

    try {
        const res = await fetch('{{ route("admin.perusahaan.loker.applicants-ranking", encrypt($loker->id)) }}', {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? ''
            }
        });

        if (!res.ok) throw new Error('HTTP ' + res.status);

        const data = await res.json();
        hideAppEl('applicants-skeleton');

        if (!data.success) {
            showAppEl('applicants-error');
            setApplicantsBadge('error');
            return;
        }

        document.getElementById('applicants-result').innerHTML = buildTable(data);
        showAppEl('applicants-result');
        setApplicantsBadge('done', data.total, data.has_ranking);

    } catch (err) {
        console.error('Applicants ranking error:', err);
        hideAppEl('applicants-skeleton');
        showAppEl('applicants-error');
        setApplicantsBadge('error');
    }
}

// ── Build full table dari JSON (mirip buildCard di rekomendasi) ──────────

function buildTable(data) {
    let html = '';

    if (data.ml_error) {
        html += '<div class="alert alert-warning d-flex align-items-center mb-4">'
            + '<i class="material-icons me-2 fs-5">warning_amber</i>'
            + '<div>Ranking tidak tersedia: <strong>' + esc(data.ml_error) + '</strong>.'
            + ' Data pelamar tetap ditampilkan tanpa skor kecocokan.</div></div>';
    }

    html += '<div class="table-responsive">'
        + '<table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">'
        + '<thead><tr class="fw-bold text-muted bg-light">'
        + '<th class="ps-4 min-w-250px">Nama & Info Pelamar</th>'
        + '<th class="min-w-120px">Tanggal Melamar</th>'
        + '<th class="min-w-100px">Kategori Lokasi</th>'
        + '<th class="min-w-120px">Rencana Datang</th>'
        + '<th class="min-w-100px">Status</th>'
        + (data.has_ranking ? '<th class="min-w-150px">Kecocokan</th>' : '')
        + '<th class="text-end pe-4 min-w-100px">Aksi</th>'
        + '</tr></thead><tbody>';

    if (!data.lamarans?.length) {
        const colspan = data.has_ranking ? 7 : 6;
        html += '<tr><td colspan="' + colspan + '" class="text-center text-muted py-10">Belum ada pelamar untuk lowongan ini.</td></tr>';
    } else {
        data.lamarans.forEach(l => { html += buildRow(l, data.has_ranking, data.kategorilokasi); });
    }

    html += '</tbody></table></div>';
    return html;
}

function buildRow(l, hasRanking, kategorilokasi) {
    const avatar = l.foto_url
        ? '<img src="' + l.foto_url + '" alt="Foto">'
        : '<span class="symbol-label bg-light-danger text-danger fw-bold text-uppercase fs-5">' + esc((l.namalengkap ?? '?').charAt(0)) + '</span>';

    const statusMap = {
        1: '<span class="badge badge-light-success fw-bold">Diterima</span>',
        2: '<span class="badge badge-light-danger fw-bold">Ditolak</span>',
    };
    const status = statusMap[l.statusditerima] ?? '<span class="badge badge-light-warning fw-bold">Menunggu</span>';

    const tanggalDatang = l.tanggal_datang
        ? '<span class="badge badge-light-primary fw-bold fs-8">' + esc(l.tanggal_datang) + '</span>'
        : '<span class="text-muted fs-9">-</span>';

    let kecocokan = '';
    if (hasRanking) {
        if (l.rank) {
            const cls = { green: 'success', yellow: 'warning', red: 'danger' }[l.color] ?? 'secondary';
            const tags = (l.tags ?? []).slice(0, 2)
                .map(t => '<span class="badge badge-light-' + t.type + ' fs-9 me-1">' + esc(t.text) + '</span>')
                .join('');
            kecocokan = '<td><div class="d-flex flex-column gap-1">'
                + '<div class="d-flex align-items-center gap-2">'
                + '<span class="text-muted fw-semibold fs-9">#' + l.rank + '</span>'
                + '<span class="badge badge-light-' + cls + ' fw-bold fs-8">' + l.match_percentage + '% · ' + esc(l.label) + '</span>'
                + '</div>'
                + '<div class="text-muted fs-9">Sem: ' + Math.round(l.semantic_score * 100) + '%&nbsp;|&nbsp;'
                + 'Skill: ' + Math.round(l.skill_score * 100) + '%&nbsp;|&nbsp;'
                + 'Edu: ' + Math.round(l.education_score * 100) + '%&nbsp;|&nbsp;'
                + 'Exp: ' + Math.round(l.experience_score * 100) + '%</div>'
                + (tags ? '<div class="d-flex flex-wrap gap-1">' + tags + '</div>' : '')
                + '</div></td>';
        } else {
            kecocokan = '<td><span class="text-muted fs-9">—</span></td>';
        }
    }

    const cvBtn = l.cv_url
        ? '<a href="' + l.cv_url + '" class="btn btn-sm btn-light-primary fw-bold"><i class="material-icons fs-5">description</i> CV</a>'
        : '';

    return '<tr>'
        + '<td class="ps-4"><div class="d-flex align-items-center">'
        + '<div class="symbol symbol-45px me-5">' + avatar + '</div>'
        + '<div class="d-flex flex-column">'
        + '<span class="text-gray-800 fw-bold fs-6">' + esc(l.namalengkap) + '</span>'
        + '<span class="text-muted fw-semibold fs-8">' + esc(l.alamatlengkap) + '</span>'
        + '</div></div></td>'
        + '<td><div class="text-gray-600 fs-7 fw-bold">' + esc(l.tanggalmelamar) + '</div>'
        + '<div class="text-muted fs-8">' + esc(l.tanggalmelamar_diff) + '</div></td>'
        + '<td><span class="text-gray-600 fs-7">' + esc(kategorilokasi) + '</span></td>'
        + '<td>' + tanggalDatang + '</td>'
        + '<td>' + status + '</td>'
        + kecocokan
        + '<td class="text-end pe-4">' + cvBtn + '</td>'
        + '</tr>';
}

function showAppEl(id) { document.getElementById(id)?.classList.remove('d-none'); }
function hideAppEl(id) { document.getElementById(id)?.classList.add('d-none'); }
function esc(str) {
    return String(str ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}
function setApplicantsBadge(state, total, hasRanking) {
    const badge = document.getElementById('applicants-badge');
    if (!badge) return;
    const html = {
        loading: '<span class="spinner-border spinner-border-sm me-1" style="width:10px;height:10px;"></span> Memuat ranking...',
        done:    '<i class="material-icons fs-9 me-1">' + (hasRanking ? 'star' : 'people') + '</i> ' + total + ' pelamar' + (hasRanking ? ' · Terranking' : ''),
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