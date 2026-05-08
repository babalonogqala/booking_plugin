<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<div class="bm-wrap wrap">
    <h1 class="bm-title">
        <span class="dashicons dashicons-calendar-alt"></span>
        Booking Manager
    </h1>

    <!-- Stats Cards -->
    <div class="bm-stats">
        <div class="bm-stat bm-stat--total">
            <span class="bm-stat__num"><?php echo $counts['total']; ?></span>
            <span class="bm-stat__label">Total</span>
        </div>
        <div class="bm-stat bm-stat--pending">
            <span class="bm-stat__num"><?php echo $counts['pending']; ?></span>
            <span class="bm-stat__label">Pending</span>
        </div>
        <div class="bm-stat bm-stat--confirmed">
            <span class="bm-stat__num"><?php echo $counts['confirmed']; ?></span>
            <span class="bm-stat__label">Confirmed</span>
        </div>
        <div class="bm-stat bm-stat--completed">
            <span class="bm-stat__num"><?php echo $counts['completed']; ?></span>
            <span class="bm-stat__label">Completed</span>
        </div>
        <div class="bm-stat bm-stat--cancelled">
            <span class="bm-stat__num"><?php echo $counts['cancelled']; ?></span>
            <span class="bm-stat__label">Cancelled</span>
        </div>
    </div>

    <!-- Filter Tabs -->
    <div class="bm-tabs">
        <?php
        $tabs = [ '' => 'All', 'pending' => 'Pending', 'confirmed' => 'Confirmed', 'completed' => 'Completed', 'cancelled' => 'Cancelled' ];
        foreach ( $tabs as $key => $label ) :
            $active = ( $filter === $key ) ? 'bm-tab--active' : '';
            $url = admin_url( 'admin.php?page=booking-manager' . ( $key ? '&status=' . $key : '' ) );
        ?>
            <a href="<?php echo esc_url( $url ); ?>" class="bm-tab <?php echo $active; ?>"><?php echo $label; ?></a>
        <?php endforeach; ?>
    </div>

    <!-- Bookings Table -->
    <div class="bm-card">
        <?php if ( empty( $bookings ) ) : ?>
            <div class="bm-empty">
                <span class="dashicons dashicons-calendar"></span>
                <p>No bookings found.</p>
            </div>
        <?php else : ?>
        <table class="bm-table">
            <thead>
                <tr>
                    <th>#ID</th>
                    <th>Customer</th>
                    <th>Service</th>
                    <th>Date &amp; Time</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ( $bookings as $b ) : ?>
                <tr data-id="<?php echo (int) $b->id; ?>">
                    <td><strong>#<?php echo (int) $b->id; ?></strong></td>
                    <td>
                        <div class="bm-customer">
                            <strong><?php echo esc_html( $b->customer_name ); ?></strong>
                            <span><?php echo esc_html( $b->customer_email ); ?></span>
                            <?php if ( $b->customer_phone ) : ?>
                                <span><?php echo esc_html( $b->customer_phone ); ?></span>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td><?php echo esc_html( $b->service_name ); ?></td>
                    <td>
                        <div class="bm-datetime">
                            <strong><?php echo date( 'M j, Y', strtotime( $b->booking_date ) ); ?></strong>
                            <span><?php echo date( 'g:i A', strtotime( $b->booking_time ) ); ?></span>
                        </div>
                    </td>
                    <td>
                        <span class="bm-badge bm-badge--<?php echo esc_attr( $b->status ); ?>">
                            <?php echo esc_html( ucfirst( $b->status ) ); ?>
                        </span>
                    </td>
                    <td>
                        <div class="bm-actions">
                            <select class="bm-status-select" data-id="<?php echo (int) $b->id; ?>">
                                <option value="">Change status…</option>
                                <?php foreach ( ['pending','confirmed','completed','cancelled'] as $s ) : ?>
                                    <option value="<?php echo $s; ?>" <?php selected( $b->status, $s ); ?>><?php echo ucfirst( $s ); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <button class="bm-btn bm-btn--danger bm-delete-booking" data-id="<?php echo (int) $b->id; ?>">
                                <span class="dashicons dashicons-trash"></span>
                            </button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>

    <!-- Shortcode Helper -->
    <div class="bm-shortcode-info">
        <strong>Frontend Form Shortcode:</strong>
        <code>[booking_form]</code>
        — Paste this into any page to display the public booking form.
    </div>
</div>
