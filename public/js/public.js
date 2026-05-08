(function ($) {
    'use strict';

    const state = {
        serviceId:   null,
        serviceName: '',
        servicePrice: '',
        date:        '',
        time:        '',
        timeLabel:   '',
    };

    // ── Step navigation ────────────────────────────────────────────────────
    function goToStep(n) {
        $('.bm-pane').addClass('bm-pane--hidden');
        $('#bm-pane-' + n).removeClass('bm-pane--hidden');
        // Update step indicators
        $('.bm-step').each(function () {
            const s = parseInt($(this).data('step'));
            $(this).removeClass('bm-step--active bm-step--done');
            if (s === n) $(this).addClass('bm-step--active');
            else if (s < n) $(this).addClass('bm-step--done');
        });
    }

    $(document).on('click', '[data-go]', function () {
        const n = parseInt($(this).data('go'));
        // Validate before moving forward
        if (n === 3) {
            if (!state.serviceId)  { alert('Please choose a service.'); return; }
            if (!state.date)       { alert('Please select a date.'); return; }
            if (!state.time)       { alert('Please select a time slot.'); return; }
        }
        if (n === 4) {
            const name  = $('#bm-name').val().trim();
            const email = $('#bm-email').val().trim();
            if (!name)  { alert('Please enter your name.'); return; }
            if (!email) { alert('Please enter your email.'); return; }
            buildSummary(name, email);
        }
        goToStep(n);
    });

    // ── Service selection ─────────────────────────────────────────────────
    $(document).on('click', '.bm-service-card', function () {
        $('.bm-service-card').removeClass('bm-service-card--selected');
        $(this).addClass('bm-service-card--selected');
        state.serviceId    = $(this).data('id');
        state.serviceName  = $(this).data('name');
        state.servicePrice = $(this).data('price');
        // Move to step 2 automatically
        state.date = '';
        state.time = '';
        $('#bm-date').val('');
        $('#bm-slots-wrap').hide();
        $('#bm-slots').empty();
        goToStep(2);
    });

    // ── Date picker ────────────────────────────────────────────────────────
    $(document).on('change', '#bm-date', function () {
        state.date = $(this).val();
        state.time = '';
        if (!state.date || !state.serviceId) return;

        $('#bm-slots').html('<span style="color:#64748b;font-size:13px;">Loading times…</span>');
        $('#bm-slots-wrap').show();
        $('#bm-to-step3').prop('disabled', true).addClass('bm-btn--disabled');

        $.post(BM.ajax_url, {
            action:     'bm_get_slots',
            nonce:      BM.nonce,
            service_id: state.serviceId,
            date:       state.date,
        }).done(function (res) {
            if (!res.success || !res.data.length) {
                $('#bm-slots').html('<span style="color:#dc2626;font-size:13px;">No available slots for this date.</span>');
                return;
            }
            let html = '';
            res.data.forEach(function (slot) {
                const cls = slot.available ? '' : 'bm-slot--unavailable';
                html += `<div class="bm-slot ${cls}" data-value="${slot.value}" data-label="${slot.label}" ${!slot.available ? 'data-disabled="1"' : ''}>${slot.label}</div>`;
            });
            $('#bm-slots').html(html);
        });
    });

    // ── Slot selection ────────────────────────────────────────────────────
    $(document).on('click', '.bm-slot', function () {
        if ($(this).data('disabled')) return;
        $('.bm-slot').removeClass('bm-slot--selected');
        $(this).addClass('bm-slot--selected');
        state.time      = $(this).data('value') + ':00';
        state.timeLabel = $(this).data('label');
        $('#bm-to-step3').prop('disabled', false).removeClass('bm-btn--disabled');
    });

    // ── Summary builder ───────────────────────────────────────────────────
    function buildSummary(name, email) {
        const dateFormatted = new Date(state.date + 'T00:00:00').toLocaleDateString('en-ZA', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
        const priceFormatted = 'R ' + parseFloat(state.servicePrice).toFixed(2);
        const rows = [
            { label: 'Service',   value: state.serviceName },
            { label: 'Date',      value: dateFormatted },
            { label: 'Time',      value: state.timeLabel },
            { label: 'Name',      value: name },
            { label: 'Email',     value: email },
            { label: 'Price',     value: priceFormatted, cls: 'price' },
        ];
        let html = '';
        rows.forEach(r => {
            html += `<div class="bm-summary-row"><span class="label">${r.label}</span><span class="value ${r.cls || ''}">${r.value}</span></div>`;
        });
        $('#bm-summary').html(html);
    }

    // ── Submission ────────────────────────────────────────────────────────
    $(document).on('click', '#bm-submit', function () {
        const $btn = $(this).prop('disabled', true).text('Submitting…');
        $('#bm-error').addClass('bm-pane--hidden').text('');

        $.post(BM.ajax_url, {
            action:         'bm_submit_booking',
            nonce:          BM.nonce,
            service_id:     state.serviceId,
            customer_name:  $('#bm-name').val(),
            customer_email: $('#bm-email').val(),
            customer_phone: $('#bm-phone').val(),
            booking_date:   state.date,
            booking_time:   state.time,
            notes:          $('#bm-notes').val(),
        }).done(function (res) {
            $btn.prop('disabled', false).text('Confirm Booking');
            if (res.success) {
                $('.bm-pane').addClass('bm-pane--hidden');
                $('.bm-steps').hide();
                $('#bm-success-msg').text(res.data.message);
                $('#bm-success').removeClass('bm-pane--hidden');
            } else {
                $('#bm-error').removeClass('bm-pane--hidden').text(res.data);
            }
        }).fail(function () {
            $btn.prop('disabled', false).text('Confirm Booking');
            $('#bm-error').removeClass('bm-pane--hidden').text('Network error. Please try again.');
        });
    });

    // ── New booking reset ─────────────────────────────────────────────────
    $(document).on('click', '#bm-new-booking', function () {
        Object.assign(state, { serviceId: null, serviceName: '', servicePrice: '', date: '', time: '', timeLabel: '' });
        $('#bm-success').addClass('bm-pane--hidden');
        $('.bm-steps').show();
        $('#bm-name,#bm-email,#bm-phone,#bm-notes').val('');
        goToStep(1);
    });

})(jQuery);
