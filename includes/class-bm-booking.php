<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class BM_Booking {

    public static function get_all( $args = [] ) {
        global $wpdb;
        $defaults = [
            'status'     => '',
            'date'       => '',
            'service_id' => 0,
            'orderby'    => 'booking_date',
            'order'      => 'DESC',
            'limit'      => 50,
            'offset'     => 0,
        ];
        $args = wp_parse_args( $args, $defaults );

        $where = '1=1';
        if ( $args['status'] ) {
            $where .= $wpdb->prepare( ' AND b.status = %s', $args['status'] );
        }
        if ( $args['date'] ) {
            $where .= $wpdb->prepare( ' AND b.booking_date = %s', $args['date'] );
        }
        if ( $args['service_id'] ) {
            $where .= $wpdb->prepare( ' AND b.service_id = %d', $args['service_id'] );
        }

        $orderby = sanitize_sql_orderby( "{$args['orderby']} {$args['order']}" ) ?: 'booking_date DESC';
        $limit   = (int) $args['limit'];
        $offset  = (int) $args['offset'];

        return $wpdb->get_results(
            "SELECT b.*, s.name AS service_name, s.price AS service_price
             FROM {$wpdb->prefix}bm_bookings b
             LEFT JOIN {$wpdb->prefix}bm_services s ON b.service_id = s.id
             WHERE $where
             ORDER BY $orderby
             LIMIT $limit OFFSET $offset"
        );
    }

    public static function get( $id ) {
        global $wpdb;
        return $wpdb->get_row(
            $wpdb->prepare(
                "SELECT b.*, s.name AS service_name, s.price AS service_price, s.duration
                 FROM {$wpdb->prefix}bm_bookings b
                 LEFT JOIN {$wpdb->prefix}bm_services s ON b.service_id = s.id
                 WHERE b.id = %d",
                $id
            )
        );
    }

    public static function create( $data ) {
        global $wpdb;
        $insert = [
            'service_id'     => (int) $data['service_id'],
            'customer_name'  => sanitize_text_field( $data['customer_name'] ),
            'customer_email' => sanitize_email( $data['customer_email'] ),
            'customer_phone' => sanitize_text_field( $data['customer_phone'] ?? '' ),
            'booking_date'   => sanitize_text_field( $data['booking_date'] ),
            'booking_time'   => sanitize_text_field( $data['booking_time'] ),
            'status'         => 'pending',
            'notes'          => sanitize_textarea_field( $data['notes'] ?? '' ),
        ];
        $result = $wpdb->insert( "{$wpdb->prefix}bm_bookings", $insert );
        return $result ? $wpdb->insert_id : false;
    }

    public static function update_status( $id, $status ) {
        global $wpdb;
        $allowed = [ 'pending', 'confirmed', 'cancelled', 'completed' ];
        if ( ! in_array( $status, $allowed, true ) ) return false;
        return $wpdb->update(
            "{$wpdb->prefix}bm_bookings",
            [ 'status' => $status ],
            [ 'id' => (int) $id ],
            [ '%s' ],
            [ '%d' ]
        );
    }

    public static function delete( $id ) {
        global $wpdb;
        return $wpdb->delete( "{$wpdb->prefix}bm_bookings", [ 'id' => (int) $id ], [ '%d' ] );
    }

    public static function count_by_status() {
        global $wpdb;
        $rows = $wpdb->get_results(
            "SELECT status, COUNT(*) AS total FROM {$wpdb->prefix}bm_bookings GROUP BY status"
        );
        $counts = [ 'pending' => 0, 'confirmed' => 0, 'cancelled' => 0, 'completed' => 0, 'total' => 0 ];
        foreach ( $rows as $row ) {
            $counts[ $row->status ] = (int) $row->total;
            $counts['total'] += (int) $row->total;
        }
        return $counts;
    }

    public static function is_slot_available( $service_id, $date, $time ) {
        global $wpdb;
        $service  = BM_Service::get( $service_id );
        if ( ! $service ) return false;
        $booked = (int) $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(*) FROM {$wpdb->prefix}bm_bookings
                 WHERE service_id = %d AND booking_date = %s AND booking_time = %s
                 AND status NOT IN ('cancelled')",
                $service_id, $date, $time
            )
        );
        return $booked < (int) $service->capacity;
    }
}
