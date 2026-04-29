<?php
/**
 * Plugin Name: Booking Manager
 * Plugin URI:  https://github.com/babalonogqala
 * Description: A complete booking management system for services, appointments, and reservations.
 * Version:     1.0.0
 * Author:      Babalo Nogqala
 * Author URI:  https://github.com/babalonogqala
 * License:     GPL-2.0+
 * Text Domain: booking-manager
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'BM_VERSION', '1.0.0' );
define( 'BM_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'BM_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

// Autoload core files
require_once BM_PLUGIN_DIR . 'includes/class-bm-database.php';
require_once BM_PLUGIN_DIR . 'includes/class-bm-booking.php';
require_once BM_PLUGIN_DIR . 'includes/class-bm-service.php';
require_once BM_PLUGIN_DIR . 'includes/class-bm-email.php';
require_once BM_PLUGIN_DIR . 'admin/class-bm-admin.php';
require_once BM_PLUGIN_DIR . 'public/class-bm-public.php';

register_activation_hook( __FILE__, [ 'BM_Database', 'create_tables' ] );
register_deactivation_hook( __FILE__, [ 'BM_Database', 'drop_tables' ] );

function bm_init() {
    new BM_Admin();
    new BM_Public();
}
add_action( 'plugins_loaded', 'bm_init' );
