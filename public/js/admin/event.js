"use strict";

$(document).ready(function() {
    // CSRF Token Setup
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // PNotify Default Styling
    if (typeof PNotify !== 'undefined') {
        PNotify.prototype.options.styling = "brighttheme";
    }

    if ($('#kt_table_events').length) {
        const table = $('#kt_table_events').DataTable({
            "pageLength": 10,
            "language": {
                "search": "Cari Event:",
                "lengthMenu": "Tampilkan _MENU_ data",
                "info": "Menampilkan _START_ - _END_ dari _TOTAL_ data"
            }
        });
    }

    // Handle Form Submission (Create/Edit)
    $('#event-form').on('submit', function(e) {
        e.preventDefault();
        
        const form = $(this);
        const url = form.attr('action');
        const formData = new FormData(this);
        const submitBtn = form.find('button[type="submit"]');

        // Clear existing validation
        form.find('.is-invalid').removeClass('is-invalid');
        form.find('.invalid-feedback').remove();

        submitBtn.attr('data-kt-indicator', 'on').prop('disabled', true);

        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                submitBtn.removeAttr('data-kt-indicator').prop('disabled', false);
                
                if (response.success) {
                    new PNotify({
                        title: 'Berhasil!',
                        text: response.message,
                        type: 'success',
                        delay: 3000
                    });

                    setTimeout(function() {
                        window.location.href = "/admin/event";
                    }, 1500);
                }
            },
            error: function(xhr) {
                submitBtn.removeAttr('data-kt-indicator').prop('disabled', false);
                
                if (xhr.status === 422) {
                    let errors = xhr.responseJSON.errors;
                    
                    $.each(errors, function(key, value) {
                        // Transform Laravel array key (sesi.0.nama_sesi) to HTML name (sesi[0][nama_sesi])
                        let inputName = key;
                        if (key.includes('.')) {
                            const parts = key.split('.');
                            inputName = parts[0] + parts.slice(1).map(p => `[${p}]`).join('');
                        }
                        
                        const input = form.find(`[name="${inputName}"]`);
                        input.addClass('is-invalid');
                        
                        if (input.closest('.position-relative').length) {
                             input.closest('.position-relative').after(`<div class="invalid-feedback d-block">${value[0]}</div>`);
                        } else {
                             input.after(`<div class="invalid-feedback">${value[0]}</div>`);
                        }
                    });

                    new PNotify({
                        title: 'Oops!',
                        text: 'Silakan periksa kembali form yang bertanda merah.',
                        type: 'error'
                    });
                } else {
                    new PNotify({
                        title: 'Error!',
                        text: 'Terjadi kesalahan sistem.',
                        type: 'error'
                    });
                }
            }
        });
    });

    // --- Package & Cost Management ---
    const statusPaket = $('#statuspaket');
    const biayaContainer = $('#biaya-container');
    const biayaInput = $('input[name="biaya"]');
    const packetWrapper = $('#packet-wrapper');
    const packetContainer = $('#packet-container');
    const addPacketBtn = $('#add-packet');
    let packetCount = packetContainer.find('.packet-row').length;

    function addPacketRow() {
        const row = `
            <div class="packet-row mb-5 p-5 border rounded bg-light position-relative animate__animated animate__fadeIn">
                <button type="button" class="btn btn-icon btn-sm btn-light-danger position-absolute top-0 end-0 m-2 remove-packet" title="Hapus Paket">
                    <i class="material-icons fs-5">close</i>
                </button>
                <div class="row g-5">
                    <div class="col-md-4">
                        <label class="required fs-7 fw-bold mb-2">Nama Paket</label>
                        <input type="text" name="pakets[${packetCount}][nama_paket]" class="form-control form-control-sm form-control-solid" placeholder="Contoh: Gold" required />
                    </div>
                    <div class="col-md-5">
                        <label class="fs-7 fw-bold mb-2">Fasilitas</label>
                        <input type="text" name="pakets[${packetCount}][fasilitas]" class="form-control form-control-sm form-control-solid" placeholder="Fasilitas (pisahkan dengan koma)" />
                    </div>
                    <div class="col-md-3">
                        <label class="required fs-7 fw-bold mb-2">Harga</label>
                        <div class="input-group input-group-sm input-group-solid">
                            <span class="input-group-text">Rp</span>
                            <input type="number" name="pakets[${packetCount}][harga]" class="form-control form-control-sm" placeholder="0" min="0" required />
                        </div>
                    </div>
                </div>
            </div>
        `;
        packetContainer.append(row);
        packetCount++;
    }

    function togglePackageUI() {
        if (statusPaket.is(':checked')) {
            biayaContainer.fadeOut();
            biayaInput.prop('required', false);
            packetWrapper.fadeIn();
            if (packetContainer.children().length === 0) {
                addPacketRow();
            }
        } else {
            biayaContainer.fadeIn();
            biayaInput.prop('required', true);
            packetWrapper.fadeOut();
        }
    }

    statusPaket.on('change', togglePackageUI);
    
    // Initial State Check for statusPaket (Handle Edit mode)
    // If it's create mode, we might want it unchecked by default as per request
    // But since the checkbox might be checked in HTML, we trigger the UI
    togglePackageUI();

    addPacketBtn.on('click', addPacketRow);

    $(document).on('click', '.remove-packet', function() {
        $(this).closest('.packet-row').remove();
    });

    // --- Sponsor Management ---
    const sponsorContainer = $('#sponsor-container');
    const addSponsorBtn = $('#add-sponsor');
    let sponsorCount = sponsorContainer.find('.sponsor-row').length;

    function addSponsorRow() {
        const row = `
            <div class="sponsor-row mb-5 p-5 border rounded bg-light position-relative animate__animated animate__fadeIn">
                <button type="button" class="btn btn-icon btn-sm btn-light-danger position-absolute top-0 end-0 m-2 remove-sponsor" title="Hapus Sponsor">
                    <i class="material-icons fs-5">close</i>
                </button>
                <div class="row g-5">
                    <div class="col-md-6">
                        <label class="required fs-7 fw-bold mb-2">Nama Sponsor</label>
                        <input type="text" name="sponsors[${sponsorCount}][nama]" class="form-control form-control-sm form-control-solid" placeholder="Contoh: PT. Bank Central" required />
                    </div>
                    <div class="col-md-6">
                        <label class="fs-7 fw-bold mb-2">Logo Sponsor</label>
                        <input type="file" name="sponsors[${sponsorCount}][logo]" class="form-control form-control-sm form-control-solid sponsor-logo-input" accept="image/*" />
                        <div class="mt-2 sponsor-logo-preview-container" style="display:none;">
                            <img src="#" class="sponsor-logo-preview img-thumbnail" style="max-height: 50px;">
                        </div>
                    </div>
                </div>
            </div>
        `;
        sponsorContainer.append(row);
        sponsorCount++;
    }

    addSponsorBtn.on('click', addSponsorRow);

    $(document).on('click', '.remove-sponsor', function() {
        $(this).closest('.sponsor-row').remove();
    });

    $(document).on('change', '.sponsor-logo-input', function() {
        const input = this;
        const container = $(this).siblings('.sponsor-logo-preview-container');
        const preview = container.find('.sponsor-logo-preview');

        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                preview.attr('src', e.target.result);
                container.fadeIn();
            }
            reader.readAsDataURL(input.files[0]);
        }
    });

    // --- Session Management ---
    const sessionWrapper = $('#session-wrapper');
    const statusSesi = $('#status_sesi');
    const sessionContainer = $('#session-container');
    const addSessionBtn = $('#add-session');
    let sessionCount = sessionContainer.find('.session-row').length;

    function addSessionRow() {
        const row = `
            <div class="session-row mb-5 p-5 border rounded bg-light position-relative animate__animated animate__fadeIn">
                <button type="button" class="btn btn-icon btn-sm btn-light-danger position-absolute top-0 end-0 m-2 remove-session" title="Hapus Sesi">
                    <i class="material-icons fs-5">close</i>
                </button>
                <div class="row g-5">
                    <div class="col-md-3">
                        <label class="required fs-7 fw-bold mb-2">Nama Sesi</label>
                        <input type="text" name="sesi[${sessionCount}][nama_sesi]" class="form-control form-control-sm form-control-solid" placeholder="Contoh: Sesi 1" required />
                    </div>
                    <div class="col-md-3">
                        <label class="required fs-7 fw-bold mb-2">Jam Mulai</label>
                        <input type="time" name="sesi[${sessionCount}][jam_mulai]" class="form-control form-control-sm form-control-solid" required />
                    </div>
                    <div class="col-md-3">
                        <label class="required fs-7 fw-bold mb-2">Jam Selesai</label>
                        <input type="time" name="sesi[${sessionCount}][jam_selesai]" class="form-control form-control-sm form-control-solid" required />
                    </div>
                    <div class="col-md-3">
                        <label class="required fs-7 fw-bold mb-2">Kuota</label>
                        <input type="number" name="sesi[${sessionCount}][kuota]" class="form-control form-control-sm form-control-solid" placeholder="0" min="1" required />
                    </div>
                </div>
            </div>
        `;
        sessionContainer.append(row);
        sessionCount++;
    }

    statusSesi.on('change', function() {
        if ($(this).is(':checked')) {
            sessionWrapper.fadeIn();
            if (sessionContainer.children().length === 0) {
                addSessionRow();
            }
        } else {
            sessionWrapper.fadeOut();
        }
    });

    addSessionBtn.on('click', function() {
        addSessionRow();
    });

    $(document).on('click', '.remove-session', function() {
        $(this).closest('.session-row').remove();
        if (sessionContainer.children().length === 0) {
            statusSesi.prop('checked', false).trigger('change');
        }
    });

    // Handle Delete via AJAX
    $(document).on('click', '.delete-event', function(e) {
        e.preventDefault();
        
        const id = $(this).data('id');
        const url = `/admin/event/${id}`;
        
        Swal.fire({
            text: "Apakah Anda yakin ingin menghapus event ini?",
            icon: "warning",
            showCancelButton: true,
            buttonsStyling: false,
            confirmButtonText: "Ya, Hapus!",
            cancelButtonText: "Batal",
            customClass: {
                confirmButton: "btn btn-danger",
                cancelButton: "btn btn-active-light"
            }
        }).then(function(result) {
            if (result.isConfirmed) {
                $.ajax({
                    url: url,
                    type: 'DELETE',
                    success: function(response) {
                        if (response.success) {
                            new PNotify({
                                title: 'Berhasil!',
                                text: response.message,
                                type: 'success'
                            });
                            location.reload();
                        }
                    },
                    error: function() {
                        new PNotify({
                            title: 'Gagal!',
                            text: 'Gagal menghapus data.',
                            type: 'error'
                        });
                    }
                });
            }
        });
    });

    // --- Image Preview Logic ---
    function previewImage(input, previewId, containerId, currentId = null) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $(`#${previewId}`).attr('src', e.target.result).show();
                $(`#${containerId}`).fadeIn();
                if (currentId) $(`#${currentId}`).hide();
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    $('#poster_input').on('change', function() {
        previewImage(this, 'poster_preview', 'poster_preview_container', 'current_poster');
    });

    $('#layout_input').on('change', function() {
        previewImage(this, 'layout_preview', 'layout_preview_container', 'current_layout');
    });

    // --- Leaflet Map Logic ---
    if ($('#map').length) {
        if (typeof L === 'undefined') {
            $.getScript('https://unpkg.com/leaflet@1.9.4/dist/leaflet.js', function() {
                $.getScript('https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js', function() {
                    initMap();
                });
            });
        } else {
            initMap();
        }
    }

    function initMap() {
        let latInput = $('#latitude');
        let lngInput = $('#longitude');
        let token = window.MAPBOX_TOKEN;
        
        // Default coord (Jakarta)
        let defaultLat = latInput.val() || -6.2088;
        let defaultLng = lngInput.val() || 106.8456;
        let zoom = latInput.val() ? 15 : 13;

        const map = L.map('map').setView([defaultLat, defaultLng], zoom);

        // Standard OpenStreetMap Tiles (Reliable)
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        let marker = L.marker([defaultLat, defaultLng], { draggable: true }).addTo(map);

        // --- Photon Geocoder (Smarter Search) ---
        const geocoder = L.Control.geocoder({
            geocoder: L.Control.Geocoder.photon(),
            defaultMarkGeocode: false,
            placeholder: "Cari lokasi (cth: Politeknik Sukabumi)...",
        })
        .on('markgeocode', function(e) {
            let latlng = e.geocode.center;
            marker.setLatLng(latlng);
            map.setView(latlng, 17); // Zoom deeper for schools/buildings
            latInput.val(latlng.lat.toFixed(6));
            lngInput.val(latlng.lng.toFixed(6));
        })
        .addTo(map);

        marker.on('dragend', function() {
            let pos = marker.getLatLng();
            latInput.val(pos.lat.toFixed(6));
            lngInput.val(pos.lng.toFixed(6));
        });

        map.on('click', function(e) {
            marker.setLatLng(e.latlng);
            latInput.val(e.latlng.lat.toFixed(6));
            lngInput.val(e.latlng.lng.toFixed(6));
        });

        function updateMarker() {
            let lat = parseFloat(latInput.val());
            let lng = parseFloat(lngInput.val());
            if (!isNaN(lat) && !isNaN(lng)) {
                marker.setLatLng([lat, lng]);
                map.panTo([lat, lng]);
            }
        }

        latInput.on('input', updateMarker);
        lngInput.on('input', updateMarker);
    }
    // --- Status & Headline Toggle Logic ---
    $(document).on('click', '.toggle-status, .toggle-headline', function(e) {
        e.preventDefault();
        
        const btn = $(this);
        const url = btn.data('url');
        const isStatusToggle = btn.hasClass('toggle-status');
        
        btn.prop('disabled', true).addClass('opacity-50');
        if (typeof NProgress !== 'undefined') NProgress.start();
        
        $.ajax({
            url: url,
            type: 'POST',
            success: function(response) {
                if (typeof NProgress !== 'undefined') NProgress.done();
                btn.prop('disabled', false).removeClass('opacity-50');
                
                if (response.success) {
                    new PNotify({
                        title: 'Berhasil!',
                        text: response.message,
                        type: 'success',
                        delay: 2000
                    });
                    
                    if (isStatusToggle) {
                        const isActive = response.statusaktif;
                        btn.removeClass('btn-light-success btn-light-danger')
                           .addClass(isActive ? 'btn-light-success' : 'btn-light-danger')
                           .text(isActive ? 'Aktif' : 'Tidak Aktif');
                    } else {
                        const isHeadline = response.statusheadline;
                        btn.removeClass('btn-light-warning btn-light-secondary')
                           .addClass(isHeadline ? 'btn-light-warning' : 'btn-light-secondary')
                           .html(`<i class="material-icons fs-9 me-1 align-middle text-${isHeadline ? 'warning' : 'muted'}">star</i> ${isHeadline ? 'Headline' : 'Reguler'}`);
                    }
                }
            },
            error: function() {
                if (typeof NProgress !== 'undefined') NProgress.done();
                btn.prop('disabled', false).removeClass('opacity-50');
                new PNotify({
                    title: 'Error!',
                    text: 'Terjadi kesalahan saat mengubah status.',
                    type: 'error'
                });
            }
        });
    });
});
