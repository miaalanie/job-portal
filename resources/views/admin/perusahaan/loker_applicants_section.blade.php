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

{{-- Modal: Ranking Detail --}}
<div class="modal fade" id="rankingDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-lg modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg">

            {{-- Header --}}
            <div class="modal-header border-0 px-5 pt-5 pb-2">
                <div>
                    <h5 class="modal-title fw-bold text-dark mb-1" id="rankModalName"></h5>
                    <div id="rankModalBadge"></div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body px-5 pb-4">

                {{-- Score Cards --}}
                <div class="row row-cols-5 g-2 mb-4" id="rankModalScores"></div>

                {{-- Tags --}}
                <div class="mb-4">
                    <p class="fw-bold text-dark fs-7 mb-2">
                        <i class="material-icons fs-6 me-1 align-middle">label</i> Tags
                    </p>
                    <div id="rankModalTags"></div>
                </div>

                {{-- Reasons --}}
                <div>
                    <p class="fw-bold text-dark fs-7 mb-2">
                        <i class="material-icons fs-6 me-1 align-middle">analytics</i> Analisis Detail
                    </p>
                    <div id="rankModalReasons"></div>
                </div>

            </div>

            <div class="modal-footer border-0 px-5 pb-4 pt-0">
                <button class="btn btn-sm btn-light rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
            </div>

        </div>
    </div>
</div>

@push('styles')
<style>
    @keyframes shimmer {
        0% {
            background-position: 200% 0;
        }

        100% {
            background-position: -200% 0;
        }
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
    document.addEventListener('DOMContentLoaded', () => loadApplicantsTable());

    // ─── State pagination ─────────────────────────────────────────────────────────
    let _allLamarans = [];
    let _hasRanking = false;
    let _kategorilokasi = '';
    let _currentPage = 1;
    const PER_PAGE = 10;

    // ─── Load (sama seperti sebelumnya, tapi simpan ke state) ────────────────────
    async function loadApplicantsTable() {
        showAppEl('applicants-skeleton');
        hideAppEl('applicants-error');
        hideAppEl('applicants-result');
        setApplicantsBadge('loading');
        _currentPage = 1;

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

            // Simpan ke state
            _allLamarans = data.lamarans ?? [];
            _hasRanking = data.has_ranking;
            _kategorilokasi = data.kategorilokasi;

            renderPage(data); // ml_error & has_ranking dari data asli
            showAppEl('applicants-result');
            setApplicantsBadge('done', data.total, data.has_ranking);

        } catch (err) {
            console.error('Applicants ranking error:', err);
            hideAppEl('applicants-skeleton');
            showAppEl('applicants-error');
            setApplicantsBadge('error');
        }
    }

    // ─── Render halaman tertentu ──────────────────────────────────────────────────
    function renderPage(data = null) {
        const totalPages = Math.ceil(_allLamarans.length / PER_PAGE);
        _currentPage = Math.max(1, Math.min(_currentPage, totalPages));

        const start = (_currentPage - 1) * PER_PAGE;
        const sliced = _allLamarans.slice(start, start + PER_PAGE);

        let html = '';

        // ml_error banner — hanya muncul di halaman pertama
        if (data?.ml_error && _currentPage === 1) {
            html += '<div class="alert alert-warning d-flex align-items-center justify-content-between mb-4">' +
                '<div class="d-flex align-items-center">' +
                '<i class="material-icons me-2 fs-5">warning_amber</i>' +
                '<div>Ranking tidak tersedia: <strong>' + esc(data.ml_error) + '</strong>. Data pelamar tetap ditampilkan tanpa skor kecocokan.</div>' +
                '</div>' +
                '<button class="btn btn-sm btn-outline-warning rounded-pill ms-3" onclick="loadApplicantsTable()">' +
                '<i class="material-icons fs-6 me-1">refresh</i> Coba Lagi</button>' +
                '</div>';
        }

        // Table
        html += '<div class="table-responsive">' +
            '<table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">' +
            '<thead><tr class="fw-bold text-muted bg-light">' +
            '<th class="ps-4 min-w-250px">Nama & Info Pelamar</th>' +
            '<th class="min-w-120px">Tanggal Melamar</th>' +
            '<th class="min-w-100px">Kategori Lokasi</th>' +
            '<th class="min-w-120px">Rencana Datang</th>' +
            '<th class="min-w-100px">Status</th>' +
            (_hasRanking ? '<th class="min-w-150px">Kecocokan</th>' : '') +
            '<th class="text-end pe-4 min-w-100px">Aksi</th>' +
            '</tr></thead><tbody>';

        if (!sliced.length) {
            const colspan = _hasRanking ? 7 : 6;
            html += '<tr><td colspan="' + colspan + '" class="text-center text-muted py-10">Belum ada pelamar untuk lowongan ini.</td></tr>';
        } else {
            sliced.forEach(l => {
                html += buildRow(l, _hasRanking, _kategorilokasi);
            });
        }

        html += '</tbody></table></div>';

        // Pagination controls
        html += buildPagination(totalPages);

        document.getElementById('applicants-result').innerHTML = html;
    }

    // ─── Pagination HTML ──────────────────────────────────────────────────────────
    function buildPagination(totalPages) {
        if (totalPages <= 1) return '';

        const info = 'Halaman ' + _currentPage + ' dari ' + totalPages +
            ' &nbsp;·&nbsp; ' + _allLamarans.length + ' pelamar';

        let pages = '';

        // Window: selalu tampil max 5 nomor di sekitar currentPage
        const delta = 2;
        const left = Math.max(2, _currentPage - delta);
        const right = Math.min(totalPages - 1, _currentPage + delta);

        // Tombol halaman 1
        pages += pageBtn(1);

        // Ellipsis kiri
        if (left > 2) pages += '<li class="page-item disabled"><span class="page-link">…</span></li>';

        for (let i = left; i <= right; i++) pages += pageBtn(i);

        // Ellipsis kanan
        if (right < totalPages - 1) pages += '<li class="page-item disabled"><span class="page-link">…</span></li>';

        // Tombol halaman terakhir
        if (totalPages > 1) pages += pageBtn(totalPages);

        return '<div class="d-flex align-items-center justify-content-between mt-4 flex-wrap gap-2">' +
            '<span class="text-muted fs-8">' + info + '</span>' +
            '<ul class="pagination pagination-sm mb-0">' +

            // Prev
            '<li class="page-item ' + (_currentPage === 1 ? 'disabled' : '') + '">' +
            '<button class="page-link rounded-start-pill" onclick="goPage(' + (_currentPage - 1) + ')" ' + (_currentPage === 1 ? 'disabled' : '') + '>' +
            '<i class="material-icons fs-7">chevron_left</i></button></li>' +

            pages +

            // Next
            '<li class="page-item ' + (_currentPage === totalPages ? 'disabled' : '') + '">' +
            '<button class="page-link rounded-end-pill" onclick="goPage(' + (_currentPage + 1) + ')" ' + (_currentPage === totalPages ? 'disabled' : '') + '>' +
            '<i class="material-icons fs-7">chevron_right</i></button></li>' +

            '</ul></div>';
    }

    function pageBtn(n) {
        const active = n === _currentPage ? ' active' : '';
        return '<li class="page-item' + active + '">' +
            '<button class="page-link" onclick="goPage(' + n + ')">' + n + '</button></li>';
    }

    function goPage(n) {
        _currentPage = n;
        renderPage(); // tanpa argumen, ml_error banner tidak muncul lagi — itu memang benar

        // Scroll ke atas section
        document.getElementById('applicants-section')?.scrollIntoView({
            behavior: 'smooth',
            block: 'start'
        });
    }

    // ─────────────────────────────────────────────────────────────────────────────

    function buildTable(data) {
        let html = '';

        if (data.ml_error) {
            html += '<div class="alert alert-warning d-flex align-items-center justify-content-between mb-4">' +
                '<div class="d-flex align-items-center">' +
                '<i class="material-icons me-2 fs-5">warning_amber</i>' +
                '<div>Ranking tidak tersedia: <strong>' + esc(data.ml_error) + '</strong>. Data pelamar tetap ditampilkan tanpa skor kecocokan.</div>' +
                '</div>' +
                '<button class="btn btn-sm btn-outline-warning rounded-pill ms-3" onclick="loadApplicantsTable()">' +
                '<i class="material-icons fs-6 me-1">refresh</i> Coba Lagi</button>' +
                '</div>';
        }

        html += '<div class="table-responsive">' +
            '<table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">' +
            '<thead><tr class="fw-bold text-muted bg-light">' +
            '<th class="ps-4 min-w-250px">Nama & Info Pelamar</th>' +
            '<th class="min-w-120px">Tanggal Melamar</th>' +
            '<th class="min-w-100px">Kategori Lokasi</th>' +
            '<th class="min-w-120px">Rencana Datang</th>' +
            '<th class="min-w-100px">Status</th>' +
            (data.has_ranking ? '<th class="min-w-150px">Kecocokan</th>' : '') +
            '<th class="text-end pe-4 min-w-100px">Aksi</th>' +
            '</tr></thead><tbody>';

        if (!data.lamarans?.length) {
            const colspan = data.has_ranking ? 7 : 6;
            html += '<tr><td colspan="' + colspan + '" class="text-center text-muted py-10">Belum ada pelamar untuk lowongan ini.</td></tr>';
        } else {
            data.lamarans.forEach(l => {
                html += buildRow(l, data.has_ranking, data.kategorilokasi);
            });
        }

        html += '</tbody></table></div>';
        return html;
    }

    function buildRow(l, hasRanking, kategorilokasi) {
        const avatar = l.foto_url ?
            '<img src="' + l.foto_url + '" alt="Foto">' :
            '<span class="symbol-label bg-light-danger text-danger fw-bold text-uppercase fs-5">' + esc((l.namalengkap ?? '?').charAt(0)) + '</span>';

        const statusMap = {
            1: '<span class="badge badge-light-success fw-bold">Diterima</span>',
            2: '<span class="badge badge-light-danger fw-bold">Ditolak</span>',
        };
        const status = statusMap[l.statusditerima] ?? '<span class="badge badge-light-warning fw-bold">Menunggu</span>';

        const tanggalDatang = l.tanggal_datang ?
            '<span class="badge badge-light-primary fw-bold fs-8">' + esc(l.tanggal_datang) + '</span>' :
            '<span class="text-muted fs-9">-</span>';

        // ── Kolom kecocokan ───────────────────────────────────────────────────────
        let kecocokan = '';
        if (hasRanking) {
            if (l.rank) {
                const cls = {
                    green: 'success',
                    yellow: 'warning',
                    red: 'danger'
                } [l.color] ?? 'secondary';
                const tags = (l.tags ?? []).slice(0, 3)
                    .map(t => '<span class="badge badge-light-' + t.type + ' fs-9 me-1">' + esc(t.text) + '</span>')
                    .join('');

                // Simpan data lengkap ke attribute supaya tidak XSS via onclick string
                const dataAttr = 'data-pelamar=\'' + esc(JSON.stringify(l)) + '\'';

                kecocokan = '<td><div class="d-flex flex-column gap-1">' +
                    '<div class="d-flex align-items-center gap-2">' +
                    '<span class="text-muted fw-semibold fs-9">#' + l.rank + '</span>' +
                    '<span class="badge badge-light-' + cls + ' fw-bold fs-8">' + l.match_percentage + '% · ' + esc(l.label) + '</span>' +
                    '<button class="btn btn-icon btn-sm btn-light-info ranking-detail-btn" ' + dataAttr + ' title="Lihat Analisis Lengkap" style="width:22px;height:22px;">' +
                    '<i class="material-icons" style="font-size:13px;">info</i>' +
                    '</button>' +
                    '</div>' +
                    '<div class="text-muted fs-9">' +
                    'Profil: ' + Math.round(l.semantic_score * 100) + '%&nbsp;|&nbsp;' +
                    'Skill: ' + Math.round(l.skill_score * 100) + '%&nbsp;|&nbsp;' +
                    'Edu: ' + Math.round(l.education_score * 100) + '%&nbsp;|&nbsp;' +
                    'Exp: ' + Math.round(l.experience_score * 100) + '%' +
                    '</div>' +
                    (tags ? '<div class="d-flex flex-wrap gap-1">' + tags + '</div>' : '') +
                    '</div></td>';
            } else {
                kecocokan = '<td><span class="text-muted fs-9">—</span></td>';
            }
        }

        // ── Kolom aksi ────────────────────────────────────────────────────────────
        const cvBtn = l.cv_url ?
            '<a href="' + l.cv_url + '" class="btn btn-icon btn-light-primary btn-sm" title="Lihat Profil">' +
            '<i class="material-icons fs-5">visibility</i></a>' :
            '';

        return '<tr>' +
            '<td class="ps-4"><div class="d-flex align-items-center">' +
            '<div class="symbol symbol-45px me-5">' + avatar + '</div>' +
            '<div class="d-flex flex-column">' +
            '<span class="text-gray-800 fw-bold fs-6">' + esc(l.namalengkap) + '</span>' +
            '<span class="text-muted fw-semibold fs-8">' + esc(l.alamatlengkap) + '</span>' +
            '</div></div></td>' +
            '<td><div class="text-gray-600 fs-7 fw-bold">' + esc(l.tanggalmelamar) + '</div>' +
            '<div class="text-muted fs-8">' + esc(l.tanggalmelamar_diff) + '</div></td>' +
            '<td><span class="text-gray-600 fs-7">' + esc(kategorilokasi) + '</span></td>' +
            '<td>' + tanggalDatang + '</td>' +
            '<td>' + status + '</td>' +
            kecocokan +
            '<td class="text-end pe-4">' + cvBtn + '</td>' +
            '</tr>';
    }

    // ─── Modal ranking detail ─────────────────────────────────────────────────────

    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.ranking-detail-btn');
        if (!btn) return;
        const l = JSON.parse(btn.dataset.pelamar);
        openRankingModal(l);
    });

    function openRankingModal(l) {
        const cls = {
            green: 'success',
            yellow: 'warning',
            red: 'danger'
        } [l.color] ?? 'secondary';

        // Header
        document.getElementById('rankModalName').textContent = l.namalengkap;
        document.getElementById('rankModalBadge').innerHTML =
            '<span class="badge badge-light-' + cls + ' fw-bold fs-7">' +
            '#' + l.rank + ' &nbsp;·&nbsp; ' + l.match_percentage + '% &nbsp;·&nbsp; ' + esc(l.label) +
            '</span>';

        // Score cards
        const scores = [{
                label: 'Final Score',
                val: l.final_score,
                icon: 'leaderboard'
            },
            {
                label: 'Kesesuaian Profil',
                val: l.semantic_score,
                icon: 'person_search'
            },
            {
                label: 'Keseuaian Skill',
                val: l.skill_score,
                icon: 'build'
            },
            {
                label: 'Kesesuaian Pendidikan',
                val: l.education_score,
                icon: 'school'
            },
            {
                label: 'Kesesuaian Pengalaman',
                val: l.experience_score,
                icon: 'work_history'
            },
        ];

        document.getElementById('rankModalScores').innerHTML = scores.map(s => {
            const pct = Math.round((s.val ?? 0) * 100);
            const c = pct >= 60 ? 'success' : pct >= 40 ? 'warning' : 'danger';
            return '<div class="col">' +
                '<div class="bg-light rounded-3 p-3 text-center h-100">' +
                '<i class="material-icons text-' + c + ' mb-1" style="font-size:20px;">' + s.icon + '</i>' +
                '<div class="fw-bold text-' + c + ' fs-4">' + pct + '%</div>' +
                '<div class="text-muted fs-9 mt-1">' + s.label + '</div>' +
                '</div></div>';
        }).join('');

        // Tags
        const tagsHtml = (l.tags ?? []).map(t =>
            '<span class="badge badge-light-' + t.type + ' fs-8 me-1 mb-1">' + esc(t.text) + '</span>'
        ).join('');
        document.getElementById('rankModalTags').innerHTML = tagsHtml || '<span class="text-muted fs-8">—</span>';

        // Reasons
        document.getElementById('rankModalReasons').innerHTML = (l.reasons ?? []).map((r, i) => {
            // Deteksi icon per konteks
            const icon = r.toLowerCase().includes('skill') ? 'build' :
                r.toLowerCase().includes('pendidikan') ? 'school' :
                r.toLowerCase().includes('pengalaman') ? 'work_history' :
                r.toLowerCase().includes('catatan') ? 'info' :
                r.toLowerCase().includes('loker mensya') ? 'warning_amber' :
                'chevron_right';
            const isWarning = r.toLowerCase().includes('catatan') || r.toLowerCase().includes('verifikasi');
            return '<div class="d-flex gap-3 p-3 rounded-3 mb-2 ' + (isWarning ? 'bg-warning bg-opacity-10' : 'bg-light') + '">' +
                '<i class="material-icons text-' + (isWarning ? 'warning' : 'muted') + ' flex-shrink-0" style="font-size:18px;margin-top:1px;">' + icon + '</i>' +
                '<span class="text-gray-700 fs-7">' + esc(r) + '</span>' +
                '</div>';
        }).join('') || '<span class="text-muted fs-8">Tidak ada detail analisis.</span>';

        new bootstrap.Modal(document.getElementById('rankingDetailModal')).show();
    }

    // ─── Utils ────────────────────────────────────────────────────────────────────

    function showAppEl(id) {
        document.getElementById(id)?.classList.remove('d-none');
    }

    function hideAppEl(id) {
        document.getElementById(id)?.classList.add('d-none');
    }

    function esc(str) {
        return String(str ?? '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }

    function setApplicantsBadge(state, total, hasRanking) {
        const badge = document.getElementById('applicants-badge');
        if (!badge) return;
        const html = {
            loading: '<span class="spinner-border spinner-border-sm me-1" style="width:10px;height:10px;"></span> Memuat ranking...',
            done: '<i class="material-icons fs-9 me-1">' + (hasRanking ? 'star' : 'people') + '</i> ' + total + ' pelamar' + (hasRanking ? ' · Terranking' : ''),
            error: '<i class="material-icons fs-9 me-1">error</i> Gagal',
        };
        const cls = {
            loading: 'bg-light text-muted',
            done: hasRanking ? 'bg-success bg-opacity-10 text-success' : 'bg-primary bg-opacity-10 text-primary',
            error: 'bg-danger bg-opacity-10 text-danger',
        };
        badge.innerHTML = html[state] ?? '';
        badge.className = 'badge fs-8 fw-normal ' + (cls[state] ?? 'bg-light text-muted');
    }
</script>
@endpush