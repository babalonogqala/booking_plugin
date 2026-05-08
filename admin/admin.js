(function ($) {
    'use strict';

    // ── Status change ──────────────────────────────────────────────────────
    $(document).on('change', '.bm-status-select', function () {
        const $el   = $(this);
        const id     = $el.data('id');
        const status = $el.val();
        if (!status) return;

        $.post(BM.ajax_url, {
            action: 'bm_update_status',
            nonce:  BM.nonce,
            id,
            status
        }).done(function (res) {
            if (res.success) {
                const $badge = $el.closest('tr').find('.bm-badge');
                $badge.attr('class', 'bm-badge bm-badge--' + status)
                      .text(status.charAt(0).toUpperCase() + status.slice(1));
                showNotice('Status updated to ' + status, 'success');
            } else {
                showNotice('Error: ' + res.data, 'error');
            }
        });
    });

    // ── Delete booking ─────────────────────────────────────────────────────
    $(document).on('click', '.bm-delete-booking', function () {
        if (!confirm('Delete this booking? This cannot be undone.')) return;
        const $btn = $(this);
        const id   = $btn.data('id');

        $.post(BM.ajax_url, {
            action: 'bm_delete_booking',
            nonce:  BM.nonce,
            id
        }).done(function (res) {
            if (res.success) {
                $btn.closest('tr').fadeOut(300, function () { $(this).remove(); });
            }
        });
    });

    // ── Services Modal ─────────────────────────────────────────────────────
    function openModal(title, data) {
        $('#bm-modal-title').text(title);
        $('#bm-service-id').val(data.id || '');
        $('#bm-svc-name').val(data.name || '');
        $('#bm-svc-desc').val(data.description || '');
        $('#bm-svc-duration').val(data.duration || 60);
        $('#bm-svc-price').val(data.price || '0.00');
        $('#bm-svc-capacity').val(data.capacity || 1);
        $('#bm-svc-status').val(data.status || 'active');
        $('#bm-service-modal').fadeIn(150);
    }

    function closeModal() {
        $('#bm-service-modal').fadeOut(150);
    }

    $(document).on('click', '#bm-add-service', function () {
        openModal('Add Service', {});
    });

    $(document).on('click', '.bm-edit-service', function () {
        const data = $(this).closest('tr').data('service');
        openModal('Edit Service', data);
    });

    $(document).on('click', '.bm-modal__close, .bm-modal__overlay', closeModal);

    $(document).on('click', '#bm-save-service', function () {
        const $btn = $(this).prop('disabled', true).text('Saving…');
        $.post(BM.ajax_url, {
            action:      'bm_save_service',
            nonce:       BM.nonce,
            service_id:  $('#bm-service-id').val(),
            name:        $('#bm-svc-name').val(),
            description: $('#bm-svc-desc').val(),
            duration:    $('#bm-svc-duration').val(),
            price:       $('#bm-svc-price').val(),
            capacity:    $('#bm-svc-capacity').val(),
            status:      $('#bm-svc-status').val(),
        }).done(function (res) {
            $btn.prop('disabled', false).text('Save Service');
            if (res.success) {
                closeModal();
                showNotice('Service saved. Refreshing…', 'success');
                setTimeout(() => location.reload(), 800);
            } else {
                showNotice('Error saving service.', 'error');
            }
        });
    });

    // ── Delete service ─────────────────────────────────────────────────────
    $(document).on('click', '.bm-delete-service', function () {
        if (!confirm('Delete this service?')) return;
        const $btn = $(this);
        const id   = $btn.data('id');

        $.post(BM.ajax_url, {
            action: 'bm_delete_service',
            nonce:  BM.nonce,
            id
        }).done(function (res) {
            if (res.success) {
                $btn.closest('tr').fadeOut(300, function () { $(this).remove(); });
            }
        });
    });

    // ── Toast helper ───────────────────────────────────────────────────────
    function showNotice(msg, type) {
        const $n = $('<div class="notice notice-' + type + ' is-dismissible bm-toast"><p>' + msg + '</p></div>');
        $('.bm-title').after($n);
        setTimeout(() => $n.fadeOut(400, function () { $(this).remove(); }), 3000);
    }

})(jQuery);
