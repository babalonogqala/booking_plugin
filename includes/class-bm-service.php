<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class BM_Service {

    public static function get_all( $active_only = true ) {
        global $wpdb;
        $where = $active_only ? "WHERE status = 'active'" : '';
        return $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}bm_services $where ORDER BY name ASC" );
    }

    public static function get( $id ) {
        global $wpdb;
        return $wpdb->get_row( $wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}bm_services WHERE id = %d", $id
        ) );
    }

    public static function create( $data ) {
        global $wpdb;
        $result = $wpdb->insert( "{$wpdb->prefix}bm_services", [
            'name'        => sanitize_text_field( $data['name'] ),
            'description' => sanitize_textarea_field( $data['description'] ?? '' ),
            'duration'    => (int) ( $data['duration'] ?? 60 ),
            'price'       => (float) ( $data['price'] ?? 0 ),
            'capacity'    => (int) ( $data['capacity'] ?? 1 ),
            'status'      => 'active',
        ] );
        return $result ? $wpdb->insert_id : false;
    }

    public static function update( $id, $data ) {
        global $wpdb;
        return $wpdb->update(
            "{$wpdb->prefix}bm_services",
            [
                'name'        => sanitize_text_field( $data['name'] ),
                'description' => sanitize_textarea_field( $data['description'] ?? '' ),
                'duration'    => (int) ( $data['duration'] ?? 60 ),
                'price'       => (float) ( $data['price'] ?? 0 ),
                'capacity'    => (int) ( $data['capacity'] ?? 1 ),
                'status'      => in_array( $data['status'] ?? '', ['active','inactive'], true ) ? $data['status'] : 'active',
            ],
            [ 'id' => (int) $id ],
            [ '%s', '%s', '%d', '%f', '%d', '%s' ],
            [ '%d' ]
        );
    }

    public static function delete( $id ) {
        global $wpdb;
        return $wpdb->delete( "{$wpdb->prefix}bm_services", [ 'id' => (int) $id ], [ '%d' ] );
    }
}
