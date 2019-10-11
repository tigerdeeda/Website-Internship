<?php
/**
 * Notes class
 *
 * @author  Yithemes
 * @package YITH WooCommerce Auctions
 * @version 1.0.0
 */

if ( !defined( 'ABSPATH' ) ) {
    exit;
}

if ( !class_exists( 'YITH_WCACT_Cron' ) ) {
    /**
     * YITH_WCACT_Cron_emails
     *
     * @since 1.0.0
     */
    class YITH_WCACT_Cron{
        /**
         * Constructor
         *
         * @since  1.0.0
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         */
        public function __construct() {
            add_action( 'yith_wcact_register_cron_email', array( $this, 'cron_emails' ) );
            add_action( 'yith_wcact_send_emails', array( $this, 'yith_wcact_send_emails_bidders' ), 10, 1 );
            add_action( 'yith_wcact_register_cron_email_auction', array( $this, 'cron_emails_auctions' ));
            add_action( 'yith_wcact_send_emails_auction', array( $this, 'yith_wcact_send_emails' ),10,1);
            add_action('yith_wcact_send_emails_auction_overtime',array($this,'yith_wcact_send_emails'),10,1);


        }

        /**
         * Create single event
         * Create single event for send emails to user when the auction is about to end
         *
         * @since  1.0.0
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         */
        public function cron_emails( $product_id ) {
            if ( 'yes' == get_option( 'yith_wcact_settings_cron_auction_send_emails','yes') ) {
                $product = wc_get_product( $product_id );
                $time_end_auction = yit_get_prop( $product, '_yith_auction_to', true, 'edit' );
                $number           = get_option( 'yith_wcact_settings_cron_auction_number_days' );
                $unit             = get_option( 'yith_wcact_settings_cron_auction_type_numbers' );
                $time_send_email  = strtotime( ( sprintf( "-%d %s", $number, $unit ) ), (int)$time_end_auction );

                wp_schedule_single_event( $time_send_email, 'yith_wcact_send_emails', array( $product_id ) );

            }
        }

        /**
         * Sends email
         * Create single event for send emails to user when the auction is about to end
         *
         * @since  1.0.0
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         */
        public function yith_wcact_send_emails_bidders( $product_id ) {

            $query = YITH_Auctions()->bids;
            $users = $query->get_users( $product_id );
            foreach ( $users as $id => $user_id ) {
                WC()->mailer();
                do_action( 'yith_wcact_end_auction', (int)$user_id->user_id, $product_id );
            }

            if( 'yes' == get_option('yith_wcact_settings_tab_auction_allow_subscribe','no') ) {
                $product = wc_get_product($product_id);
                $users = $product->get_watchlist();
                if ( $users ) {
                    foreach ($users as $user) {
                        WC()->mailer();
                        do_action( 'yith_wcact_end_auction', $user, $product_id );
                    }
                }
            }

        }
        /**
         * Create single event when auction ends
         * Create single event for send emails to user when the auction is about to end
         *
         * @since  1.0.9
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         */
        public function cron_emails_auctions ($product_id) {
            $product = wc_get_product($product_id);
            $time = yit_get_prop($product, '_yith_auction_to',true);
            wp_schedule_single_event( $time, 'yith_wcact_send_emails_auction', array( $product_id ) );
        }

        /**
         * Sends email
         * Send emails when end auction and admin check this option = true
         *
         * @since  1.0.9
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         */
        public function yith_wcact_send_emails($product_id) {
            $product = wc_get_product($product_id);
            $instance = YITH_Auctions()->bids;
            $max_bid = $instance->get_max_bid($product_id);

            if ( $product->has_reserve_price() && $product->get_price() < $product->get_reserve_price() && $max_bid  ) { //Admin email

                WC()->mailer();

                if ( defined( 'YITH_WPV_PREMIUM' ) && YITH_WPV_PREMIUM ) {
                    $vendor = yith_get_vendor ($product, 'product');
                    if ( $vendor->is_valid() && $vendor->has_limited_access() && !user_can( $vendor->id, 'manage_options' ) ) {

                        do_action('yith_wcact_vendor_not_reached_reserve_price',$product,$vendor);

                    }else{
                        do_action('yith_wcact_not_reached_reserve_price', $product);
                    }
                } else {
                    do_action('yith_wcact_not_reached_reserve_price', $product);
                }


            } else {
                if ( $max_bid ) { //Then we send the email to the winner with the button for paying the order.
                    $user = get_user_by('id',$max_bid->user_id);
                    
                    WC()->mailer();
                    do_action('yith_wcact_auction_winner', $product, $user);
                    do_action('yith_wcact_email_winner_admin', $product, $user);
                    
                }else {//The auction is finished without any bids
                    WC()->mailer();

                    if ( defined( 'YITH_WPV_PREMIUM' ) && YITH_WPV_PREMIUM ) {
                        $vendor = yith_get_vendor($product, 'product');
                        if ($vendor->is_valid() && $vendor->has_limited_access() && !user_can($vendor->id, 'manage_options')) {

                            do_action('yith_wcact_vendor_finished_without_any_bids', $product, $vendor);

                        } else {
                            $time = $product->get_automatic_reschedule_time();
                            if ($time['time_quantity'] > 0 ){
                                $end_auction = yit_get_prop($product,'_yith_auction_to',true,'edit');
                                $new_end_auction  = strtotime( ( sprintf( "+%d %s", $time['time_quantity'], $time['time_unit'] ) ), $end_auction );
                                yit_save_prop($product,'_yith_auction_to',$new_end_auction,true);
                                $this->cron_emails($product_id);
                                $this->cron_emails_auctions($product_id);
                            } else {
                                do_action('yith_wcact_finished_without_any_bids', $product);
                            }
                        }
                    } else {
                        $time = $product->get_automatic_reschedule_time();
                        if ($time['time_quantity'] > 0 ){
                            $end_auction = yit_get_prop($product,'_yith_auction_to',true,'edit');
                            $new_end_auction  = strtotime( ( sprintf( "+%d %s", $time['time_quantity'], $time['time_unit'] ) ), $end_auction );
                            yit_save_prop($product,'_yith_auction_to',$new_end_auction,true);
                            $this->cron_emails($product_id);
                            $this->cron_emails_auctions($product_id);
                        } else {
                            do_action('yith_wcact_finished_without_any_bids', $product);
                        }
                    }
                }
            }
        }
    }

}

return new YITH_WCACT_Cron();
