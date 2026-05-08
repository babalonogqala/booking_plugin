<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class BM_Public {

    public function __construct() {
        add_shortcode( 'booking_form', [ $this, 'render_form' ] );
        add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_assets' ] );
        add_action( 'wp_ajax_bm_submit_booking',        [ $this, 'handle_submission' ] );
        add_action( 'wp_ajax_nopriv_bm_submit_booking', [ $this, 'handle_submission' ] );
        add_action( 'wp_ajax_bm_get_slots',             [ $this, 'get_slots' ] );
        add_action( 'wp_ajax_nopriv_bm_get_slots',      [ $this, 'get_slots' ] );
    }

    public function enqueue_assets() {
        if ( ! is_singular() ) return;
        global $post;
        if ( ! has_shortcode( $post->post_content, 'booking_form' ) ) return;

        wp_enqueue_style(  'bm-public', BM_PLUGIN_URL . 'public/css/public.css', [], BM_VERSION );
        wp_enqueue_script( 'bm-public', BM_PLUGIN_URL . 'public/js/public.js', [ 'jquery' ], BM_VERSION, true );
        wp_localize_script( 'bm-public', 'BM', [
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'nonce'    => wp_create_nonce( 'bm_public_nonce' ),
        ] );
    }

    public function render_form() {
        $services = BM_Service::get_all( true );
        ob_start();
        require BM_PLUGIN_DIR . 'templates/booking-form.php';
        return ob_get_clean();
    }

    public function handle_submission() {
        check_ajax_referer( 'bm_public_nonce', 'nonce' );

        $service_id = (int) ( $_POST['service_id'] ?? 0 );
        $name       = sanitize_text_field( $_POST['customer_name'] ?? '' );
        $email      = sanitize_email( $_POST['customer_email'] ?? '' );
        $date       = sanitize_text_field( $_POST['booking_date'] ?? '' );
        $time       = sanitize_text_field( $_POST['booking_time'] ?? '' );

        if ( ! $service_id || ! $name || ! $email || ! $date || ! $time ) {
            wp_send_json_error( 'Please fill in all required fields.' );
        }

        if ( ! is_email( $email ) ) {
            wp_send_json_error( 'Please enter a valid email address.' );
        }

        if ( ! BM_Booking::is_slot_available( $service_id, $date, $time ) ) {
            wp_send_json_error( 'Sorry, that time slot is no longer available. Please choose another.' );
        }

        $booking_id = BM_Booking::create( $_POST );
        if ( ! $booking_id ) {
            wp_send_json_error( 'Something went wrong. Please try again.' );
        }

        BM_Email::send_confirmation( $booking_id );
        wp_send_json_success( [
            'message'    => 'Booking received! Check your email for confirmation.',
            'booking_id' => $booking_id,
        ] );
    }

    public function get_slots() {
        check_ajax_referer( 'bm_public_nonce', 'nonce' );
        $service_id = (int) ( $_POST['service_id'] ?? 0 );
        $date       = sanitize_text_field( $_POST['date'] ?? '' );

        if ( ! $service_id || ! $date ) {
            wp_send_json_error( 'Invalid request.' );
        }

        $start    = get_option( 'bm_business_hours_start', '08:00' );
        $end      = get_option( 'bm_business_hours_end', '18:00' );
        $interval = (int) get_option( 'bm_slot_interval', 30 );

        $slots    = [];
        $current  = strtotime( $date . ' ' . $start );
        $end_ts   = strtotime( $date . ' ' . $end );

        while ( $current < $end_ts ) {
            $time_val = date( 'H:i', $current );
            $slots[]  = [
                'value'     => $time_val,
                'label'     => date( 'g:i A', $current ),
                'available' => BM_Booking::is_slot_available( $service_id, $date, $time_val . ':00' ),
            ];
            $current += $interval * 60;
        }

        wp_send_json_success( $slots );
    }
}
