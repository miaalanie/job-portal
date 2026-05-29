$(document).ready(function() {
    // CSRF Token for AJAX
    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });

    // Initialize Select2 with AJAX for Location Fields
    function initSelect2(selector, url, parentSelector = null, placeholder = 'Pilih...') {
        $(selector).select2({
            theme: 'bootstrap-5',
            placeholder: placeholder,
            allowClear: true,
            ajax: {
                url: function() {
                    if (parentSelector) {
                        const parentId = $(parentSelector).val();
                        return parentId ? `${url}/${parentId}` : url;
                    }
                    return url;
                },
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        q: params.term // Search term
                    };
                },
                processResults: function(data) {
                    return {
                        results: data.results
                    };
                },
                cache: true
            }
        });
    }

    // Custom Theme for Select2 (Bootstrap 5 compatibility)
    const select2Options = {
        width: '100%',
        dropdownParent: $('body')
    };

    // Initialize Industrial Category (generic searchable)
    $('#idkategori').select2({
        ...select2Options,
        placeholder: 'Cari Bidang Industri...',
        ajax: {
            url: RegistrationFormConfig.url_kategoris,
            dataType: 'json',
            delay: 250,
            data: params => ({ q: params.term }),
            processResults: data => ({ results: data.results }),
            cache: true
        }
    });

    // Initialize Provinsi
    $('#provinsi_id').select2({
        ...select2Options,
        placeholder: 'Cari Provinsi...',
        ajax: {
            url: RegistrationFormConfig.url_provinsis,
            dataType: 'json',
            delay: 250,
            data: params => ({ q: params.term }),
            processResults: data => ({ results: data.results }),
            cache: true
        }
    });

    // Initialize dependent fields
    const fields = [
        { id: '#kota_id', url: RegistrationFormConfig.url_kotas, parent: '#provinsi_id', placeholder: 'Pilih Kota...' },
        { id: '#kecamatan_id', url: RegistrationFormConfig.url_kecamatans, parent: '#kota_id', placeholder: 'Pilih Kecamatan...' },
        { id: '#kelurahan_id', url: RegistrationFormConfig.url_kelurahans, parent: '#kecamatan_id', placeholder: 'Pilih Kelurahan...' }
    ];

    fields.forEach(field => {
        $(field.id).select2({
            ...select2Options,
            placeholder: field.placeholder,
            ajax: {
                url: function() {
                    const parentId = $(field.parent).val();
                    return `${field.url}/${parentId}`;
                },
                dataType: 'json',
                delay: 250,
                data: params => ({ q: params.term }),
                processResults: data => ({ results: data.results }),
                cache: true
            }
        });
    });

    // Logic for Dependent Changes
    $('#provinsi_id').on('change', function() {
        const val = $(this).val();
        $('#kota_id').val(null).trigger('change').attr('disabled', !val);
        $('#kecamatan_id').val(null).trigger('change').attr('disabled', true);
        $('#kelurahan_id').val(null).trigger('change').attr('disabled', true);
    });

    $('#kota_id').on('change', function() {
        const val = $(this).val();
        $('#kecamatan_id').val(null).trigger('change').attr('disabled', !val);
        $('#kelurahan_id').val(null).trigger('change').attr('disabled', true);
    });

    $('#kecamatan_id').on('change', function() {
        const val = $(this).val();
        $('#kelurahan_id').val(null).trigger('change').attr('disabled', !val);
    });

    // Handle Registration
    $('#form_registration').submit(function(e) {
        e.preventDefault();
        const btn = $('#btn_submit');
        btn.attr('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span> Memproses...');

        $.ajax({
            url: RegistrationFormConfig.url_register_post,
            type: 'POST',
            data: $(this).serialize(),
            success: function(res) {
                Swal.fire({
                    icon: 'success',
                    title: 'Registrasi Berhasil',
                    text: res.message,
                    confirmButtonColor: '#e11d48'
                }).then(() => {
                    window.location.href = RegistrationFormConfig.url_register_success;
                });
            },
            error: function(err) {
                btn.attr('disabled', false).html('<i class="material-icons align-middle me-2">send</i> REGISTER PERUSAHAAN');
                let errors = err.responseJSON.errors;
                let msg = '';
                if (errors) {
                    for (let field in errors) {
                        msg += errors[field][0] + '<br>';
                    }
                } else {
                    msg = err.responseJSON.message || 'Terjadi kesalahan sistem.';
                }
                Swal.fire({
                    icon: 'error',
                    title: 'Oops!',
                    html: msg,
                    confirmButtonColor: '#e11d48'
                });
            }
        });
    });
});
