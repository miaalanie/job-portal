{{-- resources/views/pelamar/rekomendasi-section.blade.php --}}

<div class="card border-0 shadow-sm rounded-4 mb-4" id="rekomendasi-section">
    <div class="card-header bg-white border-0 p-4 pb-0">
        <h5 class="fw-bold text-dark mb-3">Rekomendasi Lowongan</h5>

        {{-- Tab Navigation --}}
        <ul class="nav nav-tabs border-0 gap-2" id="rekomendasiTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active fw-semibold px-3 py-2 rounded-pill border-0 fs-8"
                    id="tab-sesuai" data-bs-toggle="tab" data-bs-target="#pane-sesuai"
                    type="button" role="tab" onclick="loadTabRekomendasi('sesuai')">
                    Sesuai Profilmu
                    <span id="badge-sesuai" class="badge ms-1 fs-10" style="background-color: #751e18;">
                        <span class="spinner-border spinner-border-sm" style="width:8px;height:8px;"></span>
                    </span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-semibold px-3 py-2 rounded-pill border-0 fs-8"
                    id="tab-serupa" data-bs-toggle="tab" data-bs-target="#pane-serupa"
                    type="button" role="tab" onclick="loadTabRekomendasi('serupa')">
                    Orang Serupa Melamar
                    <span id="badge-serupa" class="badge ms-1 fs-10" style="background-color: #751e18;">-</span>
                </button>
            </li>
        </ul>
    </div>

    <div class="card-body p-3 pt-3">
        <div class="tab-content">

            {{-- TAB: Sesuai Profilmu --}}
            <div class="tab-pane fade show active" id="pane-sesuai" role="tabpanel">
                <div id="skeleton-sesuai" class="row g-3 pt-2">
                    @for ($i = 0; $i < 3; $i++)
                        <div class="col-12 col-md-4">
                        <div class="p-3 rounded-4 d-flex align-items-center gap-3" style="background:#f8f9fa;">
                            <div class="rounded-4 shimmer-box" style="width:50px;height:50px;min-width:50px;"></div>
                            <div class="flex-grow-1">
                                <div class="rounded mb-2 shimmer-box" style="height:14px;width:65%;"></div>
                                <div class="rounded shimmer-box" style="height:11px;width:40%;"></div>
                            </div>
                        </div>
                </div>
                @endfor
            </div>
            <div id="error-sesuai" class="text-center py-4 d-none">
                <i class="material-icons text-muted mb-2" style="font-size:36px;">cloud_off</i>
                <p class="text-muted fs-8 mb-2">Rekomendasi tidak tersedia.</p>
                <button class="btn btn-sm btn-outline-secondary rounded-pill" onclick="loadTabRekomendasi('sesuai', true)">
                    <i class="material-icons fs-8 me-1">refresh</i> Coba Lagi
                </button>
            </div>
            <div id="empty-sesuai" class="text-center py-4 d-none">
                <i class="material-icons text-muted mb-2" style="font-size:36px;">work_off</i>
                <p class="text-muted fs-8 mb-0">Belum ada lowongan aktif.</p>
            </div>
            {{-- Hapus max-height & overflow dari sini --}}
            <div id="list-sesuai" class="pt-2 d-none"></div>
        </div>

        {{-- TAB: Orang Serupa Melamar --}}
        <div class="tab-pane fade" id="pane-serupa" role="tabpanel">
            <div id="skeleton-serupa" class="row g-3 pt-2 d-none">
                @for ($i = 0; $i < 3; $i++)
                    <div class="col-12 col-md-4">
                    <div class="p-3 rounded-4 d-flex align-items-center gap-3" style="background:#f8f9fa;">
                        <div class="rounded-4 shimmer-box" style="width:50px;height:50px;min-width:50px;"></div>
                        <div class="flex-grow-1">
                            <div class="rounded mb-2 shimmer-box" style="height:14px;width:65%;"></div>
                            <div class="rounded shimmer-box" style="height:11px;width:40%;"></div>
                        </div>
                    </div>
            </div>
            @endfor
        </div>
        <div id="error-serupa" class="text-center py-4 d-none">
            <i class="material-icons text-muted mb-2" style="font-size:36px;">cloud_off</i>
            <p class="text-muted fs-8 mb-2">Data tidak tersedia.</p>
            <button class="btn btn-sm btn-outline-secondary rounded-pill" onclick="loadTabRekomendasi('serupa', true)">
                <i class="material-icons fs-8 me-1">refresh</i> Coba Lagi
            </button>
        </div>
        <div id="empty-serupa" class="text-center py-4 d-none">
            <i class="material-icons text-muted mb-2" style="font-size:36px;">group_off</i>
            <p class="text-muted fs-8 mb-0">Belum ada data pelamar serupa.</p>
        </div>
        <div id="list-serupa" class="pt-2 d-none"></div>
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

    .job-recommend-card {
        background: #FFF;
        border: 1px solid #eee;
        cursor: pointer;
        transition: all 0.2s ease;
        text-decoration: none !important;
        color: inherit !important;
        display: block;
        height: 100%;
        /* biar tingginya rata per baris */
    }

    /* ✅ Hover: putih bersih, shadow lembut, border tipis biru */
    .job-recommend-card:hover {
        background: #ffffff;
        border-color: #751e18;
        box-shadow: 0 4px 18px rgba(0, 0, 0, 0.08);
        color: inherit !important;
        transform: translateY(-2px);
        /* naik dikit aja, bukan geser kanan */
    }

    .score-badge {
        font-size: 10px;
        font-weight: 700;
        padding: 2px 8px;
        border-radius: 20px;
        white-space: nowrap;
        flex-shrink: 0;
    }

    .score-green {
        background: #d1f5ea;
        color: #0a7a52;
    }

    .score-yellow {
        background: #fff3cd;
        color: #856404;
    }

    .score-red {
        background: #ffe0e0;
        color: #991b1b;
    }

    #rekomendasiTab .nav-link {
        color: #6c757d;
        background: #f8f9fa;
        font-size: 0.78rem;
    }

    #rekomendasiTab .nav-link.active {
        color: #8B1A1A;
        background: rgba(139, 26, 26, 0.08);
    }

    #rekomendasiTab .nav-link:hover:not(.active) {
        background: #f0f0f0;
        color: #444;
    }

    #list-sesuai,
    #list-serupa {
        max-height: 550px;
        /* sesuaikan tinggi */
        overflow-y: auto;
        /* scroll vertikal */
        overflow-x: hidden;
        /* hilangkan scroll horizontal */

        /* Firefox */
        scrollbar-width: thin;

        /* Supaya layout tidak bergeser saat scrollbar muncul */
        scrollbar-gutter: stable;
    }

    /* Chrome, Edge, Safari */
    #list-sesuai::-webkit-scrollbar,
    #list-serupa::-webkit-scrollbar {
        width: 2px;
        /* semakin kecil semakin tipis */
    }

    #list-sesuai::-webkit-scrollbar-track,
    #list-serupa::-webkit-scrollbar-track {
        background: transparent;
    }

    #list-sesuai::-webkit-scrollbar-thumb,
    #list-serupa::-webkit-scrollbar-thumb {
        background: #cfcfcf;
        border-radius: 10px;
    }

    #list-sesuai::-webkit-scrollbar-thumb:hover,
    #list-serupa::-webkit-scrollbar-thumb:hover {
        background: #a8a8a8;
    }

    .bg-maroon {
        background-color: #751e18 !important;
        color: #fff !important;
    }
</style>
@endpush

@push('scripts')
<script>
    const _rekLoaded = {
        sesuai: false,
        serupa: false
    };

    document.addEventListener('DOMContentLoaded', function() {
        loadTabRekomendasi('sesuai');
    });

    function setLoadingBadge(tab) {
        const el = document.getElementById('badge-' + tab);
        if (!el) return;

        el.className = 'badge bg-maroon ms-1 fs-10';
        el.innerHTML =
            '<span class="spinner-border spinner-border-sm" style="width:8px;height:8px;"></span>';
    }

    async function loadTabRekomendasi(tab, force = false) {
        if (_rekLoaded[tab] && !force) return;
        setLoadingBadge(tab);
        _rekLoaded[tab] = force ? false : _rekLoaded[tab];

        const routes = {
            sesuai: '{{ route("pelamar.rekomendasi") }}',
            serupa: '{{ route("pelamar.rekomendasi") }}'
        };

        showEl('skeleton-' + tab);
        hideEl('error-' + tab);
        hideEl('empty-' + tab);
        hideEl('list-' + tab);

        try {

            const res = await fetch(routes[tab], {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? ''
                }
            });

            if (!res.ok) throw new Error('HTTP ' + res.status);
            const data = await res.json();
            hideEl('skeleton-' + tab);

            if (!data.success) {
                showEl('error-' + tab);
                setTabBadge(tab, 'error');
                return;
            }

            const items = data.recommendations ?? [];
            if (items.length === 0) {
                showEl('empty-' + tab);
                setTabBadge(tab, 0);
                return;
            }

            renderTabCards(tab, items);
            setTabBadge(tab, items.length);
            showEl('list-' + tab);
            _rekLoaded[tab] = true;

        } catch (e) {
            console.error('Rekomendasi [' + tab + '] error:', e);
            hideEl('skeleton-' + tab);
            showEl('error-' + tab);
            setTabBadge(tab, 'error');
        }
    }

    function renderTabCards(tab, items) {
        const el = document.getElementById('list-' + tab);
        // ✅ row g-3, tiap card col-12 col-md-4 → 3 kolom, no scroll
        el.innerHTML = '<div class="row g-3">' +
            items.map(item => '<div class="col-12 col-md-4">' + buildCard(item) + '</div>').join('') +
            '</div>';
    }

    function buildCard(job) {
        const logoHtml = job.perusahaan_logo ?
            '<img src="/storage/' + job.perusahaan_logo + '" style="width:34px;height:34px;object-fit:contain;" loading="lazy">' :
            '<i class="material-icons text-muted" style="font-size:22px;">business</i>';

        const scoreClass = {
            green: 'score-green',
            yellow: 'score-yellow',
            red: 'score-red'
        } [job.color] ?? 'score-yellow';

        const gajiHtml = (job.gaji_awal || job.gaji_akhir) ?
            '<div class="d-flex align-items-center text-success fw-semibold mt-1" style="font-size:11px;"><i class="material-icons me-1" style="font-size:12px;">payments</i>' + formatGaji(job.gaji_awal, job.gaji_akhir) + '</div>' :
            '';

        const tagColorMap = {
            success: 'bg-success bg-opacity-10 text-success',
            warning: 'bg-warning bg-opacity-10 text-warning',
            danger: 'bg-danger  bg-opacity-10 text-danger',
            info: 'bg-info    bg-opacity-10 text-info',
        };
        const tagsHtml = (job.tags ?? []).map(tag => {
            const cls = tagColorMap[tag.type] ?? 'bg-secondary bg-opacity-10 text-secondary';
            return '<span class="badge ' + cls + ' fw-normal rounded-pill px-2 py-1" style="font-size:10px;">' + esc(tag.text) + '</span>';
        }).join('');

        const href = '/lowongan-detail/' + (job.encrypted_id ?? '');

        return '<a href="' + href + '" class="job-recommend-card p-3 rounded-4">' +
            '<div class="d-flex align-items-start gap-3">' +
            '<div class="bg-light rounded-3 d-flex align-items-center justify-content-center flex-shrink-0 shadow-sm" style="width:48px;height:48px;">' + logoHtml + '</div>' +
            '<div class="flex-grow-1 min-w-0">' +
            '<div class="d-flex align-items-start justify-content-between gap-2 mb-1">' +
            '<h6 class="fw-bold text-dark mb-0 lh-sm" style="font-size:13px;">' + esc(job.namalowongan) + '</h6>' +
            '<span class="score-badge ' + scoreClass + '">' + job.match_percentage + '% Sesuai</span>' +
            '</div>' +
            '<div class="text-muted lh-sm" style="font-size:12px;">' + esc(job.perusahaan_nama ?? '-') + '</div>' +
            '<div class="text-muted" style="font-size:11px;">' + esc(job.kategori) + '</div>' +
            gajiHtml +
            (tagsHtml ? '<div class="d-flex flex-wrap gap-1 mt-2">' + tagsHtml + '</div>' : '') +
            '</div>' +
            '</div>' +
            '</a>';
    }

    function setTabBadge(tab, val) {
        const el = document.getElementById('badge-' + tab);
        if (!el) return;
        if (val === 'error') {
            el.textContent = '!';
            el.className = 'badge bg-danger ms-1 fs-10';
        } else {
            el.textContent = val;
            el.className = 'badge ' + 'bg-maroon' + ' ms-1 fs-10';
        }
    }

    function showEl(id) {
        document.getElementById(id)?.classList.remove('d-none');
    }

    function hideEl(id) {
        document.getElementById(id)?.classList.add('d-none');
    }

    function esc(str) {
        return String(str ?? '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }

    function formatGaji(a, b) {
        const fmt = n => 'Rp ' + (n / 1000000).toFixed(0) + ' jt';
        if (a && b) return fmt(a) + ' – ' + fmt(b);
        if (a) return fmt(a) + '+';
        if (b) return 's/d ' + fmt(b);
        return 'Nego';
    }
</script>
@endpush