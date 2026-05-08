<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<div class="bm-form-wrap" id="bm-booking-wrap">

    <!-- Step indicator -->
    <div class="bm-steps">
        <div class="bm-step bm-step--active" data-step="1">
            <span class="bm-step__num">1</span>
            <span class="bm-step__label">Service</span>
        </div>
        <div class="bm-step-line"></div>
        <div class="bm-step" data-step="2">
            <span class="bm-step__num">2</span>
            <span class="bm-step__label">Date &amp; Time</span>
        </div>
        <div class="bm-step-line"></div>
        <div class="bm-step" data-step="3">
            <span class="bm-step__num">3</span>
            <span class="bm-step__label">Details</span>
        </div>
        <div class="bm-step-line"></div>
        <div class="bm-step" data-step="4">
            <span class="bm-step__num">4</span>
            <span class="bm-step__label">Confirm</span>
        </div>
    </div>

    <!-- Step 1 – Service Selection -->
    <div class="bm-pane" id="bm-pane-1">
        <h3 class="bm-pane__title">Choose a Service</h3>
        <div class="bm-service-grid">
            <?php foreach ( $services as $service ) : ?>
            <div class="bm-service-card" data-id="<?php echo (int) $service->id; ?>"
                 data-name="<?php echo esc_attr( $service->name ); ?>"
                 data-price="<?php echo esc_attr( $service->price ); ?>"
                 data-duration="<?php echo (int) $service->duration; ?>">
                <div class="bm-service-card__icon">📋</div>
                <div class="bm-service-card__info">
                    <strong><?php echo esc_html( $service->name ); ?></strong>
                    <?php if ( $service->description ) : ?>
                        <p><?php echo esc_html( $service->description ); ?></p>
                    <?php endif; ?>
                </div>
                <div class="bm-service-card__meta">
                    <span class="bm-price">R <?php echo number_format( $service->price, 2 ); ?></span>
                    <span class="bm-duration"><?php echo (int) $service->duration; ?> min</span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Step 2 – Date & Time -->
    <div class="bm-pane bm-pane--hidden" id="bm-pane-2">
        <h3 class="bm-pane__title">Pick a Date &amp; Time</h3>
        <div class="bm-date-time">
            <div class="bm-field">
                <label for="bm-date">Date</label>
                <input type="date" id="bm-date" min="<?php echo date('Y-m-d'); ?>">
            </div>
            <div class="bm-field" id="bm-slots-wrap" style="display:none;">
                <label>Available Times</label>
                <div class="bm-slots" id="bm-slots"></div>
            </div>
        </div>
        <div class="bm-nav">
            <button class="bm-btn bm-btn--back" data-go="1">← Back</button>
            <button class="bm-btn bm-btn--next bm-btn--disabled" id="bm-to-step3" data-go="3" disabled>Next →</button>
        </div>
    </div>

    <!-- Step 3 – Customer Details -->
    <div class="bm-pane bm-pane--hidden" id="bm-pane-3">
        <h3 class="bm-pane__title">Your Details</h3>
        <div class="bm-field">
            <label for="bm-name">Full Name *</label>
            <input type="text" id="bm-name" placeholder="Jane Smith">
        </div>
        <div class="bm-field">
            <label for="bm-email">Email Address *</label>
            <input type="email" id="bm-email" placeholder="jane@example.com">
        </div>
        <div class="bm-field">
            <label for="bm-phone">Phone Number</label>
            <input type="tel" id="bm-phone" placeholder="+27 82 000 0000">
        </div>
        <div class="bm-field">
            <label for="bm-notes">Additional Notes</label>
            <textarea id="bm-notes" rows="3" placeholder="Anything we should know…"></textarea>
        </div>
        <div class="bm-nav">
            <button class="bm-btn bm-btn--back" data-go="2">← Back</button>
            <button class="bm-btn bm-btn--next" id="bm-to-step4" data-go="4">Review →</button>
        </div>
    </div>

    <!-- Step 4 – Review & Confirm -->
    <div class="bm-pane bm-pane--hidden" id="bm-pane-4">
        <h3 class="bm-pane__title">Review Your Booking</h3>
        <div class="bm-summary" id="bm-summary"></div>
        <div class="bm-nav">
            <button class="bm-btn bm-btn--back" data-go="3">← Back</button>
            <button class="bm-btn bm-btn--submit" id="bm-submit">Confirm Booking</button>
        </div>
    </div>

    <!-- Success State -->
    <div class="bm-success bm-pane--hidden" id="bm-success">
        <div class="bm-success__icon">✅</div>
        <h3>Booking Confirmed!</h3>
        <p id="bm-success-msg"></p>
        <button class="bm-btn bm-btn--back" id="bm-new-booking">Make Another Booking</button>
    </div>

    <div id="bm-error" class="bm-error bm-pane--hidden"></div>
</div>
