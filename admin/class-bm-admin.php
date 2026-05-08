<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class BM_Admin {

    public function __construct() {
        add_action( 'admin_menu',            [ $this, 'register_menus' ] );
        add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_assets' ] );
        add_action( 'wp_ajax_bm_update_status',  [ $this, 'ajax_update_status' ] );
        add_action( 'wp_ajax_bm_delete_booking', [ $this, 'ajax_delete_booking' ] );
        add_action( 'wp_ajax_bm_save_service',   [ $this, 'ajax_save_service' ] );
        add_action( 'wp_ajax_bm_delete_service',  [ $this, 'ajax_delete_service' ] );
    }

    public function register_menus() {
        add_menu_page(
            'Booking Manager',
            'Bookings',
            'manage_options',
            'booking-manager',
            [ $this, 'page_bookings' ],
            'dashicons-calendar-alt',
            30
        );
        add_submenu_page( 'booking-manager', 'All Bookings', 'All Bookings', 'manage_options', 'booking-manager', [ $this, 'page_bookings' ] );
        add_submenu_page( 'booking-manager', 'Services',     'Services',     'manage_options', 'bm-services',     [ $this, 'page_services' ] );
        add_submenu_page( 'booking-manager', 'Settings',     'Settings',     'manage_options', 'bm-settings',     [ $this, 'page_settings' ] );
    }

    public function enqueue_assets( $hook ) {
        if ( strpos( $hook, 'booking-manager' ) === false && strpos( $hook, 'bm-' ) === false ) return;
        wp_enqueue_style(  'bm-admin', BM_PLUGIN_URL . 'admin/admin.css', [], BM_VERSION );
        wp_enqueue_script( 'bm-admin', BM_PLUGIN_URL . 'admin/admin.js', [ 'jquery' ], BM_VERSION, true );
        wp_localize_script( 'bm-admin', 'BM', [
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'nonce'    => wp_create_nonce( 'bm_nonce' ),
        ] );
    }

    // ── Pages ─────────────────────────────────────────────────────────────────

    public function page_bookings() {
        $counts   = BM_Booking::count_by_status();
        $filter   = sanitize_text_field( $_GET['status'] ?? '' );
        $bookings = BM_Booking::get_all( [ 'status' => $filter, 'limit' => 100 ] );
        $services = BM_Service::get_all( false );
        require BM_PLUGIN_DIR . 'admin/views/bookings.php';
    }

    public function page_services() {
        $services = BM_Service::get_all( false );
        require BM_PLUGIN_DIR . 'admin/views/services.php';
    }

    public function page_settings() {
        if ( isset( $_POST['bm_save_settings'] ) && check_admin_referer( 'bm_settings' ) ) {
            update_option( 'bm_business_hours_start', sanitize_text_field( $_POST['hours_start'] ) );
            update_option( 'bm_business_hours_end',   sanitize_text_field( $_POST['hours_end'] ) );
            update_option( 'bm_slot_interval',        (int) $_POST['slot_interval'] );
            echo '<div class="notice notice-success"><p>Settings saved.</p></div>';
        }
        require BM_PLUGIN_DIR . 'admin/views/settings.php';
    }

    // ── AJAX ──────────────────────────────────────────────────────────────────

    public function ajax_update_status() {
        check_ajax_referer( 'bm_nonce', 'nonce' );
        if ( ! current_user_can( 'manage_options' ) ) wp_send_json_error( 'Unauthorized' );
        $id     = (int) $_POST['id'];
        $status = sanitize_text_field( $_POST['status'] );
        $result = BM_Booking::update_status( $id, $status );
        if ( $result !== false ) {
            BM_Email::send_status_update( $id, $status );
            wp_send_json_success( [ 'message' => 'Status updated', 'status' => $status ] );
        }
        wp_send_json_error( 'Update failed' );
    }

    public function ajax_delete_booking() {
        check_ajax_referer( 'bm_nonce', 'nonce' );
        if ( ! current_user_can( 'manage_options' ) ) wp_send_json_error( 'Unauthorized' );
        $id = (int) $_POST['id'];
        wp_send_json_success( [ 'deleted' => BM_Booking::delete( $id ) ] );
    }

    public function ajax_save_service() {
        check_ajax_referer( 'bm_nonce', 'nonce' );
        if ( ! current_user_can( 'manage_options' ) ) wp_send_json_error( 'Unauthorized' );
        $id   = (int) ( $_POST['service_id'] ?? 0 );
        $data = $_POST;
        $result = $id ? BM_Service::update( $id, $data ) : BM_Service::create( $data );
        $result !== false ? wp_send_json_success() : wp_send_json_error( 'Save failed' );
    }

    public function ajax_delete_service() {
        check_ajax_referer( 'bm_nonce', 'nonce' );
        if ( ! current_user_can( 'manage_options' ) ) wp_send_json_error( 'Unauthorized' );
        $id = (int) $_POST['id'];
        wp_send_json_success( [ 'deleted' => BM_Service::delete( $id ) ] );
    }
}
