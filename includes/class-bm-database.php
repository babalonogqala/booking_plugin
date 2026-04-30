<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class BM_Database {

    public static function create_tables() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();

        // Services table
        $sql_services = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}bm_services (
            id          BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            name        VARCHAR(255)        NOT NULL,
            description TEXT,
            duration    INT                 NOT NULL DEFAULT 60,
            price       DECIMAL(10,2)       NOT NULL DEFAULT 0.00,
            capacity    INT                 NOT NULL DEFAULT 1,
            status      ENUM('active','inactive') NOT NULL DEFAULT 'active',
            created_at  DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) $charset_collate;";

        // Bookings table
        $sql_bookings = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}bm_bookings (
            id           BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            service_id   BIGINT(20) UNSIGNED NOT NULL,
            customer_name VARCHAR(255)       NOT NULL,
            customer_email VARCHAR(255)      NOT NULL,
            customer_phone VARCHAR(50),
            booking_date DATE               NOT NULL,
            booking_time TIME               NOT NULL,
            status       ENUM('pending','confirmed','cancelled','completed') NOT NULL DEFAULT 'pending',
            notes        TEXT,
            created_at   DATETIME           NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY service_id (service_id),
            KEY booking_date (booking_date),
            KEY status (status)
        ) $charset_collate;";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta( $sql_services );
        dbDelta( $sql_bookings );

        // Seed a demo service
        $count = $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}bm_services" );
        if ( (int) $count === 0 ) {
            $wpdb->insert( "{$wpdb->prefix}bm_services", [
                'name'        => 'Consultation',
                'description' => 'A 60-minute one-on-one consultation session.',
                'duration'    => 60,
                'price'       => 500.00,
                'capacity'    => 1,
                'status'      => 'active',
            ] );
        }
    }

    public static function drop_tables() {
        global $wpdb;
        $wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}bm_bookings" );
        $wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}bm_services" );
    }
}
