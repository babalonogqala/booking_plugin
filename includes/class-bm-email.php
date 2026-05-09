<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class BM_Email {

    public static function send_confirmation( $booking_id ) {
        $booking = BM_Booking::get( $booking_id );
        if ( ! $booking ) return;

        $to      = $booking->customer_email;
        $subject = sprintf( '[%s] Booking Confirmation – #%d', get_bloginfo( 'name' ), $booking_id );

        $message  = "Hi {$booking->customer_name},\n\n";
        $message .= "Thank you! Your booking has been received.\n\n";
        $message .= "--- Booking Details ---\n";
        $message .= "Booking ID  : #{$booking_id}\n";
        $message .= "Service     : {$booking->service_name}\n";
        $message .= "Date        : " . date( 'l, F j Y', strtotime( $booking->booking_date ) ) . "\n";
        $message .= "Time        : " . date( 'g:i A', strtotime( $booking->booking_time ) ) . "\n";
        $message .= "Status      : Pending\n\n";
        $message .= "We'll confirm your appointment shortly.\n\n";
        $message .= "— " . get_bloginfo( 'name' );

        $headers = [ 'Content-Type: text/plain; charset=UTF-8' ];
        wp_mail( $to, $subject, $message, $headers );

        // Notify admin
        $admin_email = get_option( 'admin_email' );
        $admin_subject = "New Booking #{$booking_id} – {$booking->service_name}";
        $admin_message  = "A new booking has been submitted.\n\n";
        $admin_message .= "Customer : {$booking->customer_name} ({$booking->customer_email})\n";
        $admin_message .= "Service  : {$booking->service_name}\n";
        $admin_message .= "Date     : {$booking->booking_date} at {$booking->booking_time}\n\n";
        $admin_message .= "Manage: " . admin_url( 'admin.php?page=booking-manager&action=view&id=' . $booking_id );
        wp_mail( $admin_email, $admin_subject, $admin_message, $headers );
    }

    public static function send_status_update( $booking_id, $new_status ) {
        $booking = BM_Booking::get( $booking_id );
        if ( ! $booking ) return;

        $labels = [
            'confirmed'  => 'Confirmed ✅',
            'cancelled'  => 'Cancelled ❌',
            'completed'  => 'Completed 🎉',
        ];
        $label = $labels[ $new_status ] ?? ucfirst( $new_status );

        $subject  = sprintf( '[%s] Your booking #%d has been %s', get_bloginfo( 'name' ), $booking_id, $new_status );
        $message  = "Hi {$booking->customer_name},\n\n";
        $message .= "Your booking #{$booking_id} for {$booking->service_name} is now: {$label}\n\n";
        $message .= "Date : " . date( 'l, F j Y', strtotime( $booking->booking_date ) ) . "\n";
        $message .= "Time : " . date( 'g:i A', strtotime( $booking->booking_time ) ) . "\n\n";
        $message .= "— " . get_bloginfo( 'name' );

        wp_mail( $booking->customer_email, $subject, $message );
    }
}
//done