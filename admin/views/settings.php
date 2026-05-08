<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<div class="bm-wrap wrap">
    <h1 class="bm-title">
        <span class="dashicons dashicons-admin-settings"></span>
        Settings
    </h1>
    <div class="bm-card" style="max-width:600px;">
        <form method="post">
            <?php wp_nonce_field( 'bm_settings' ); ?>
            <div class="bm-field">
                <label>Business Hours Start</label>
                <input type="time" name="hours_start" value="<?php echo esc_attr( get_option('bm_business_hours_start','08:00') ); ?>">
            </div>
            <div class="bm-field">
                <label>Business Hours End</label>
                <input type="time" name="hours_end" value="<?php echo esc_attr( get_option('bm_business_hours_end','18:00') ); ?>">
            </div>
            <div class="bm-field">
                <label>Slot Interval (minutes)</label>
                <select name="slot_interval">
                    <?php foreach ( [15,30,60] as $min ) : ?>
                        <option value="<?php echo $min; ?>" <?php selected( get_option('bm_slot_interval',30), $min ); ?>><?php echo $min; ?> min</option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" name="bm_save_settings" class="bm-btn bm-btn--primary">Save Settings</button>
        </form>
    </div>
</div>
