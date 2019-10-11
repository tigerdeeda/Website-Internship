<?php
/**
 * Notes class
 *
 * @author  Yithemes
 * @package YITH WooCommerce Auctions
 * @version 1.0.0
 */

if ( !defined( 'YITH_WCACT_VERSION' ) ) {
    exit( 'Direct access forbidden.' );
}

if ( !class_exists( 'YITH_WCACT_Auction_Ajax_Premium' ) ) {
    /**
     * YITH_WCACT_Auction_Ajax_Premium
     *
     * @since 1.0.0
     */
    class YITH_WCACT_Auction_Ajax_Premium extends YITH_WCACT_Auction_Ajax
    {

        /**
         * Constructor
         *
         * @since  1.0.0
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         */
        public function __construct()
        {
            add_action('wp_ajax_yith_wcact_reshedule_product', array($this, 'yith_wcact_reshedule_product'));
            add_action('wp_ajax_yith_wcact_update_my_account_auctions', array($this, 'yith_wcact_update_auction_list'));
            add_action('wp_ajax_yith_wcact_update_list_bids', array($this, 'update_list_bids'));
            add_action('wp_ajax_nopriv_yith_wcact_update_list_bids', array($this,'update_list_bids'));
            add_action('wp_ajax_yith_wcact_delete_customer_bid',array($this,'delete_customer_bid'));
            parent::__construct();
        }

        /**
         * Add a bid to the product
         *
         * @since  1.0.11
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         */
        public function yith_wcact_add_bid()
        {
            $userid = get_current_user_id();

            $user_can_make_bid = apply_filters('yith_wcact_user_can_make_bid', true, $userid);

            if (!$user_can_make_bid) {
                die();
            }
            if ($userid && isset($_POST['bid']) && isset($_POST['product'])) {
                $bid = $_POST['bid'];
                $product_id = $_POST['product'];
                $date = date("Y-m-d H:i:s");
                $product = wc_get_product($product_id);

                $overtime = $product->get_overtime();
                if ($overtime) {

                    $date_end = yit_get_prop($product, '_yith_auction_to', true);
                    $date_now = time();

                    $interval_seconds = $date_end - $date_now;
                    $interval_minutes = ceil($interval_seconds / MINUTE_IN_SECONDS);
                }
                $set_overtime = false;

                if ( $product && $product->is_type('auction') ) {
                    $bids = YITH_Auctions()->bids;
                    $current_price = $product->get_price();
                    $exist_auctions = $bids->get_max_bid($product_id);
                    $last_bid_user = $bids->get_last_bid_user($userid, $product_id);

                    if ($exist_auctions) {

                        if( $product->get_minimum_manual_bid_increment() ) {
                            $max_bid_manual = $product->get_minimum_manual_bid_increment() + $product->get_current_bid();
                            if ( $bid >= $max_bid_manual && !$last_bid_user ) {
                                if ($exist_auctions->bid < $bid && $exist_auctions->user_id != $userid) {
                                    WC()->mailer();
                                    do_action('yith_wcact_better_bid', $exist_auctions->user_id, $product);
                                }
                                $bids->add_bid($userid, $product_id, $bid, $date);
                                $set_overtime = true;
                                wc_add_notice( __('You have successfully bid', 'yith-auctions-for-woocommerce'),'success');


                            } elseif ( $bid > $last_bid_user && $bid >= $max_bid_manual ) {
                                if ($exist_auctions->bid < $bid && $exist_auctions->user_id != $userid) {
                                    WC()->mailer();
                                    do_action('yith_wcact_better_bid', $exist_auctions->user_id, $product);
                                }
                                $bids->add_bid($userid, $product_id, $bid, $date);
                                $set_overtime = true;
                                wc_add_notice( __('You have successfully bid', 'yith-auctions-for-woocommerce'),'success');

                            } else {
                                wc_add_notice(sprintf( __('Enter %s or more to be able to bid', 'yith-auctions-for-woocommerce'),
                                    wc_price($product->get_current_bid() + $product->get_minimum_manual_bid_increment())),'error');
                            }

                        } else {

                            if ($bid > $current_price && !$last_bid_user) {
                                if ($exist_auctions->bid < $bid && $exist_auctions->user_id != $userid) {
                                    WC()->mailer();
                                    do_action('yith_wcact_better_bid', $exist_auctions->user_id, $product);
                                }
                                $bids->add_bid($userid, $product_id, $bid, $date);
                                $set_overtime = true;
                                wc_add_notice( __('You have successfully bid', 'yith-auctions-for-woocommerce'),'success');

                            } elseif ($bid > $last_bid_user && $bid > $current_price) {
                                if ($exist_auctions->bid < $bid && $exist_auctions->user_id != $userid) {
                                    WC()->mailer();
                                    do_action('yith_wcact_better_bid', $exist_auctions->user_id, $product);
                                }
                                $bids->add_bid($userid, $product_id, $bid, $date);
                                $set_overtime = true;
                                wc_add_notice( __('You have successfully bid', 'yith-auctions-for-woocommerce'),'success');
                            } else {
                                wc_add_notice(sprintf( __('Enter %s or more to be able to bid', 'yith-auctions-for-woocommerce'),
                                    wc_price($product->get_current_bid() )),'error');
                            }
                        }

                    } else {
                        if ($bid >= $current_price) {
                            $bids->add_bid($userid, $product_id, $bid, $date);
                            $set_overtime = true;
                            wc_add_notice( __('You have successfully bid', 'yith-auctions-for-woocommerce'),'success');
                        }
                    }
                    $user_bid = array(
                        'user_id' => $userid,
                        'product_id' => $product_id,
                        'bid' => $bid,
                        'date' => $date,
                        'url' => get_permalink($product_id),
                    );

                    $actual_price = $product->get_current_bid();
                    yit_save_prop($product, '_price', $actual_price);
                }

                if ($set_overtime && $overtime) {
                    if ( $interval_minutes <= $product->check_for_overtime()) {


                        $new_date_finish = strtotime('+' . $overtime . 'minute', $date_end);
                        $product = wc_get_product($product_id);

                        //Remove cronjob for winner email
                        if (wp_next_scheduled('yith_wcact_send_emails_auction', array($product->get_id()))) {
                            wp_clear_scheduled_hook('yith_wcact_send_emails_auction', array($product->get_id()));
                        }

                        //Add new cronjob with the new end auction (end_auction + overtime)
                        wp_schedule_single_event($new_date_finish, 'yith_wcact_send_emails_auction_overtime', array($product->get_id()));

                        yit_save_prop($product, '_yith_auction_to', $new_date_finish);
                        yit_save_prop($product, '_yith_is_in_overtime', true);
                    }
                }
                wp_send_json($user_bid);
            }
            die();
        }

        /**
         * Reshedule auction product
         *
         * @since  1.0.14
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         */
        public function yith_wcact_reshedule_product()
        {
            if (isset($_POST['id'])) {
                $id = $_POST['id'];
                $product = wc_get_product($id['ID']);
                $product->set_stock_status('instock');
                $bids = YITH_Auctions()->bids;
                $correct = $bids->reshedule_auction($product->get_id());
                if ($product->is_closed_for_buy_now()) {
                    yit_save_prop($product, '_yith_auction_closed_buy_now', 0);
                }
                yit_delete_prop($product, '_yith_is_in_overtime', false);

                /*Product has a watchlist*/
                if( $product->get_watchlist() ) {
                    yit_delete_prop($product,'yith_wcact_auction_watchlist',false);
                }

                $array = array(
                    'product_id' => $id,
                    'url' => get_edit_post_link($id),
                );

                wp_send_json($array);
            }
            die();
        }

        /**
         * Update auction list
         *
         * @since  1.0.14
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         */
        public function yith_wcact_update_auction_list()
        {
            $instance = YITH_Auctions()->bids;
            $user_id = get_current_user_id();
            $auctions_by_user = $instance->get_auctions_by_user($user_id);

            foreach ($auctions_by_user as $valor) {
                $product = wc_get_product($valor->auction_id);
                if (!$product)
                    continue;

                $auction[] = array(
                    'product_id' => $product->get_id(),
                    'price' => wc_price($product->get_price()),
                    'product_name' => get_the_title($valor->auction_id),
                    'product_url' => get_the_permalink($valor->auction_id),
                    'image' => $product->get_image('thumbnail'),
                    'my_bid' => wc_price($valor->max_bid),
                    'status' => $this->yith_wcact_get_status($product, $valor, $user_id, $instance),
                );
            }
            wp_send_json($auction);
        }

        /**
         * Get status of an auctions
         *
         * @since  1.0.14
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         */
        function yith_wcact_get_status($product, $valor, $user_id, $instance)
        {

            if ($product->is_type('auction') && $product->is_closed()) {
                $max_bid = $instance->get_max_bid($valor->auction_id);

                if ($max_bid->user_id == $user_id && !$product->is_paid()) {
                    $url = add_query_arg(array('yith-wcact-pay-won-auction' => $product->get_id()), wc_get_checkout_url());
                    $status = $this->print_won_auctions($url);

                } else {
                    $status = $this->status_closed();
                }
            } else {
                $status = $this->status_started();
            }
            return $status;
        }

        /**
         * Print won auctions
         *
         * @since  1.0.14
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         */
        function print_won_auctions($url)
        {

            $won = __('You won this auction', 'yith-auctions-for-woocommerce');
            $pay_now = __('Pay now', 'yith-auctions-for-woocommerce');

            return '<span>' . $won . '</span><a href="' . $url . '" class="auction_add_to_cart_button button alt" id="yith-wcact-auction-won-auction">' . $pay_now . '</a>';
        }

        /**
         * status closed
         *
         * @since  1.0.14
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         */
        function status_closed()
        {
            $closed = __('Closed', 'yith-auctions-for-woocommerce');
            return '<span>' . $closed . '</span>';
        }

        /**
         * status started
         *
         * @since  1.0.14
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         */
        function status_started()
        {
            $started = __('Started', 'yith-auctions-for-woocommerce');
            return '<span>' . $started . '</span>';
        }

        /**
         * update list bid tab
         *
         * @since  1.1.0
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         */
        public function update_list_bids()
        {
            $product = wc_get_product($_POST['product']);
            $templates = array();
            $args = array(
                'product' => $product,
            );
            ob_start();
            wc_get_template('list-bids.php', $args, '', YITH_WCACT_TEMPLATE_PATH . 'frontend/');
            $templates['list_bids'] = ob_get_clean();
            $templates['current_bid'] = $product->get_price_html();
            ob_start();
            wc_get_template('max-bidder.php', $args, '', YITH_WCACT_TEMPLATE_PATH . 'frontend/');
            $templates['max_bid'] = ob_get_clean();
            ob_start();
            wc_get_template('reserve_price_and_overtime.php', $args, '', YITH_WCACT_TEMPLATE_PATH . 'frontend/');
            $templates['reserve_price_and_overtime'] = ob_get_clean();

            if ( $product->is_in_overtime() ) {
                ob_start();
                wc_get_template('auction-timeleft.php', $args, '', YITH_WCACT_TEMPLATE_PATH . 'frontend/');
                $templates['timeleft'] = ob_get_clean();
            }

            wp_send_json($templates);
        }

        /**
         * delete customer bid
         *
         * @since  1.1.0
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         */
        public function delete_customer_bid()
        {
            $product_id = $_POST['product_id'];
            $user_id =  $_POST['user_id'];
            $datetime = $_POST['date'];
            $instance = YITH_Auctions()->bids;
            $delete_bid = $instance->delete_customer_bid($product_id,$user_id,$datetime);
            die();
        }

    }
}